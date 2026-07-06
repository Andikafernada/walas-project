<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\Student;
use App\Models\AttendanceSession;
use App\Models\Attendance;
use App\Models\WaQueue;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(ClassModel $class)
    {
        $this->authorize('view', $class);

        $sessions = $class->attendanceSessions()
            ->with('attendances.student')
            ->latest('date')
            ->paginate(10);

        return view('dashboard.attendance.index', compact('class', 'sessions'));
    }

    public function generateLink(Request $request, ClassModel $class)
    {
        $this->authorize('update', $class);

        $today = Carbon::today();
        $expiresAt = $today->copy()->setTime(15, 0); // 3 PM WIB

        // Check if already exists for today
        $existing = $class->attendanceSessions()
            ->whereDate('date', $today)
            ->where('status', '!=', 'expired')
            ->first();

        if ($existing) {
            return back()->with('info', 'Link absensi hari ini sudah ada! Link lama: ' . $existing->magic_link);
        }

        $session = $class->attendanceSessions()->create([
            'user_id' => auth()->id(),
            'date' => $today,
            'token' => AttendanceSession::generateToken(),
            'pin' => AttendanceSession::generatePin(),
            'status' => 'active',
            'expires_at' => $expiresAt,
        ]);

        // Queue WhatsApp notification to Seksi Kehadiran
        $seksiKehadiran = $class->organizationStructures()
            ->where('position', 'seksi_kehadiran')
            ->where('is_active', true)
            ->where('academic_year', now()->year . '-' . (now()->year + 1))
            ->first();

        if ($seksiKehadiran?->student?->parent_whatsapp) {
            $message = "🔗 *LINK ABSENSI KELAS {$class->name}*\n\n";
            $message .= "Silakan isi absensi harian melalui tautan berikut:\n\n";
            $message .= "{$session->magic_link}\n\n";
            $message .= "PIN: {$session->pin}\n\n";
            $message .= "⏰ Batas waktu: 15:00 WIB\n";
            $message .= "_Jangan sebarkan link ini_";

            WaQueue::create([
                'user_id' => auth()->id(),
                'student_id' => $seksiKehadiran->student_id,
                'phone' => $seksiKehadiran->student->parent_whatsapp,
                'recipient_name' => $seksiKehadiran->student->parent_name ?? 'Seksi Kehadiran',
                'message' => $message,
                'type' => WaQueue::TYPE_ATTENDANCE,
            ]);
        }

        return back()->with('success', 'Magic Link berhasil dibuat & dikirm via WA!');
    }

    public function show(AttendanceSession $session)
    {
        $this->authorize('view', $session->classModel);

        $session->load('attendances.student', 'submittedBy');

        return view('dashboard.attendance.show', compact('session'));
    }

    public function submitAttendance(Request $request, AttendanceSession $session)
    {
        if ($session->status !== 'active') {
            return response()->json(['error' => 'Sesi sudah tidak aktif'], 400);
        }

        if ($session->isExpired()) {
            return response()->json(['error' => 'Sesi sudah kedaluwarsa'], 400);
        }

        $validated = $request->validate([
            'pin' => 'required|string|size:4',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:hadir,terlambat,sakit,izin,alpa',
            'attendances.*.minutes_late' => 'nullable|integer|min:0',
            'attendances.*.notes' => 'nullable|string',
        ]);

        if ($request->pin !== $session->pin) {
            return response()->json(['error' => 'PIN salah'], 400);
        }

        foreach ($validated['attendances'] as $att) {
            Attendance::updateOrCreate(
                [
                    'attendance_session_id' => $session->id,
                    'student_id' => $att['student_id'],
                ],
                [
                    'user_id' => auth()->id() ?? $session->user_id,
                    'date' => $session->date,
                    'status' => $att['status'],
                    'minutes_late' => $att['minutes_late'] ?? null,
                    'notes' => $att['notes'] ?? null,
                ]
            );
        }

        $session->update([
            'status' => 'used',
            'submitted_at' => now(),
        ]);

        // Queue parent notifications
        $students = Student::whereIn('id', collect($validated['attendances'])->pluck('student_id'))->get();

        foreach ($students as $student) {
            $attendance = collect($validated['attendances'])->firstWhere('student_id', $student->id);

            $message = "📩 *LAPORAN ABSENSI*\n\n";
            $message .= "Yth. Bapak/Ibu {$student->father_name}\n";
            $message .= "Putra/i: {$student->name}\n";
            $message .= "Kelas: {$session->classModel->name}\n";
            $message .= "Tanggal: {$session->date->format('d/m/Y')}\n\n";
            $message .= "Status: *{$attendance['status']}*\n\n";
            $message .= "_Wali Kelas: " . auth()->user()->name . "_";

            if ($student->parent_whatsapp) {
                WaQueue::create([
                    'user_id' => $session->user_id,
                    'student_id' => $student->id,
                    'phone' => $student->parent_whatsapp,
                    'recipient_name' => $student->father_name ?? 'Orang Tua',
                    'message' => $message,
                    'type' => WaQueue::TYPE_ATTENDANCE,
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Absensi berhasil disimpan!']);
    }
}
