<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSession;
use App\Models\Attendance;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\WaQueue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PublicAttendanceController extends Controller
{
    /**
     * Show the attendance form via Magic Link.
     */
    public function show(AttendanceSession $session)
    {
        // Check if session is valid
        if ($session->status === 'expired') {
            return view('attendance.expired', [
                'message' => 'Link absensi sudah kedaluwarsa.'
            ]);
        }

        if ($session->status === 'used') {
            return view('attendance.used', [
                'message' => 'Absensi sudah submitted.'
            ]);
        }

        if ($session->isExpired) {
            $session->update(['status' => 'expired']);
            return view('attendance.expired', [
                'message' => 'Link absensi sudah kedaluwarsa.'
            ]);
        }

        // Get students for this class
        $class = $session->classModel;
        $students = $class->students()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'nisn']);

        return view('attendance.form', [
            'session' => $session,
            'class' => $class,
            'students' => $students,
        ]);
    }

    /**
     * Submit attendance via Magic Link.
     */
    public function submit(Request $request, AttendanceSession $session)
    {
        // Validate session
        if ($session->status !== 'active' || $session->isExpired) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi absensi tidak valid atau sudah expired.'
            ], 400);
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'pin' => 'required|string|size:4',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:hadir,terlambat,sakit,izin,alpa',
            'attendances.*.minutes_late' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Verify PIN
        if ($request->pin !== $session->pin) {
            return response()->json([
                'success' => false,
                'message' => 'PIN salah. Pastikan memasukkan 4 digit PIN dengan benar.'
            ], 400);
        }

        // Save attendances
        $validated = $validator->validated();
        $user = $session->user;

        foreach ($validated['attendances'] as $att) {
            Attendance::updateOrCreate(
                [
                    'attendance_session_id' => $session->id,
                    'student_id' => $att['student_id'],
                ],
                [
                    'user_id' => $user->id,
                    'class_id' => $session->class_id,
                    'date' => $session->date,
                    'status' => $att['status'],
                    'minutes_late' => $att['minutes_late'] ?? null,
                ]
            );
        }

        // Update session status
        $session->update([
            'status' => 'used',
            'submitted_at' => now(),
        ]);

        // Queue parent notifications (async)
        $this->sendParentNotifications($session, $validated['attendances']);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil disimpan! Terima kasih.'
        ]);
    }

    /**
     * Send WhatsApp notifications to parents.
     */
    protected function sendParentNotifications(AttendanceSession $session, array $attendances): void
    {
        $studentIds = collect($attendances)->pluck('student_id');
        $students = Student::whereIn('id', $studentIds)
            ->whereNotNull('parent_whatsapp')
            ->get();

        foreach ($students as $student) {
            $attendance = collect($attendances)->firstWhere('student_id', $student->id);

            $statusLabel = match($attendance['status']) {
                'hadir' => '✅ Hadir',
                'terlambat' => '⏰ Terlambat',
                'sakit' => '🏥 Sakit',
                'izin' => '📝 Izin',
                'alpa' => '❌ Alfa',
                default => $attendance['status'],
            };

            $message = "📩 *LAPORAN ABSENSI*\n\n";
            $message .= "Yth. Bapak/Ibu {$student->father_name}\n";
            $message .= "Putra/i: *{$student->name}*\n";
            $message .= "Kelas: {$session->classModel->name}\n";
            $message .= "Tanggal: {$session->date->format('d/m/Y')}\n\n";
            $message .= "Status: {$statusLabel}\n\n";
            $message .= "_WaliKelas Pro_";

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
}
