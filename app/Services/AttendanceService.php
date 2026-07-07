<?php

namespace App\Services;

use App\Models\AttendanceSession;
use App\Models\Attendance;
use App\Models\ClassModel;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AttendanceService
{
    protected WhatsAppService $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Create a new attendance session with magic link.
     */
    public function createSession(ClassModel $class, ?Carbon $date = null, ?Carbon $expiresAt = null): AttendanceSession
    {
        $date = $date ?? Carbon::today();
        $expiresAt = $expiresAt ?? Carbon::today()->setTime(15, 0); // Default 3 PM

        // Check for existing active session today
        $existing = $class->attendanceSessions()
            ->whereDate('date', $date)
            ->whereIn('status', ['active', 'pending'])
            ->first();

        if ($existing) {
            return $existing;
        }

        // Generate unique token
        $token = $this->generateUniqueToken();

        // Create session
        $session = $class->attendanceSessions()->create([
            'user_id' => auth()->id(),
            'date' => $date,
            'token' => $token,
            'pin' => $this->generatePin(),
            'method' => 'magic_link',
            'status' => 'active',
            'expires_at' => $expiresAt,
        ]);

        // Send WhatsApp notification to Seksi Kehadiran
        $this->whatsAppService->sendAttendanceLink($session);

        return $session;
    }

    /**
     * Submit attendance data.
     */
    public function submitAttendance(AttendanceSession $session, array $attendances, ?int $submittedBy = null): array
    {
        $results = [
            'created' => 0,
            'updated' => 0,
            'errors' => [],
        ];

        foreach ($attendances as $data) {
            try {
                $attendance = Attendance::updateOrCreate(
                    [
                        'attendance_session_id' => $session->id,
                        'student_id' => $data['student_id'],
                    ],
                    [
                        'user_id' => $session->user_id,
                        'class_id' => $session->class_id,
                        'date' => $session->date,
                        'status' => $data['status'],
                        'minutes_late' => $data['minutes_late'] ?? null,
                        'notes' => $data['notes'] ?? null,
                    ]
                );

                if ($attendance->wasRecentlyCreated) {
                    $results['created']++;
                } else {
                    $results['updated']++;
                }

                // Send notification to parent
                $this->whatsAppService->sendAttendanceNotification($attendance);
            } catch (\Exception $e) {
                $results['errors'][] = [
                    'student_id' => $data['student_id'],
                    'error' => $e->getMessage(),
                ];
            }
        }

        // Update session status
        $session->update([
            'status' => 'used',
            'submitted_at' => now(),
            'submitted_by' => $submittedBy,
            'submitted_by_name' => Student::find($submittedBy)?->name,
        ]);

        return $results;
    }

    /**
     * Verify PIN for attendance submission.
     */
    public function verifyPin(AttendanceSession $session, string $pin): bool
    {
        if ($session->status !== 'active') {
            return false;
        }

        if ($session->isExpired) {
            return false;
        }

        return $session->pin === $pin;
    }

    /**
     * Get attendance summary for a date range.
     */
    public function getSummary(ClassModel $class, Carbon $startDate, Carbon $endDate): array
    {
        $attendances = $class->attendances()
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $total = $attendances->count();
        $hadir = $attendances->where('status', 'hadir')->count();
        $terlambat = $attendances->where('status', 'terlambat')->count();
        $sakit = $attendances->where('status', 'sakit')->count();
        $izin = $attendances->where('status', 'izin')->count();
        $alpa = $attendances->where('status', 'alpa')->count();

        return [
            'total' => $total,
            'hadir' => $hadir,
            'terlambat' => $terlambat,
            'sakit' => $sakit,
            'izin' => $izin,
            'alpa' => $alpa,
            'attendance_rate' => $total > 0 ? round((($hadir + $terlambat) / $total) * 100, 2) : 0,
            'sick_rate' => $total > 0 ? round(($sakit / $total) * 100, 2) : 0,
            'absence_rate' => $total > 0 ? round(($alpa / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Get student attendance history.
     */
    public function getStudentHistory(Student $student, ?int $limit = 30): array
    {
        return $student->attendances()
            ->orderBy('date', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Check if attendance is done for today.
     */
    public function isAttendanceDoneToday(ClassModel $class): bool
    {
        return $class->attendanceSessions()
            ->whereDate('date', Carbon::today())
            ->where('status', 'used')
            ->exists();
    }

    /**
     * Get today's attendance session.
     */
    public function getTodaySession(ClassModel $class): ?AttendanceSession
    {
        return $class->attendanceSessions()
            ->whereDate('date', Carbon::today())
            ->first();
    }

    /**
     * Mark expired sessions.
     */
    public function markExpiredSessions(): int
    {
        return AttendanceSession::where('status', 'active')
            ->where('expires_at', '<', now())
            ->update(['status' => 'expired']);
    }

    /**
     * Generate unique token.
     */
    protected function generateUniqueToken(): string
    {
        do {
            $token = Str::random(64);
        } while (AttendanceSession::where('token', $token)->exists());

        return $token;
    }

    /**
     * Generate 4-digit PIN.
     */
    protected function generatePin(): string
    {
        return str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    }
}
