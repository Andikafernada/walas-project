<?php

namespace App\Jobs;

use App\Models\AttendanceSession;
use App\Models\Attendance;
use App\Models\Student;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendAttendanceNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of retry attempts.
     */
    public int $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public AttendanceSession $session
    ) {}

    /**
     * Execute the job.
     */
    public function handle(WhatsAppService $whatsAppService): void
    {
        $session = $this->session->load('attendances.student', 'classModel');

        $sent = 0;
        $failed = 0;

        foreach ($session->attendances as $attendance) {
            $student = $attendance->student;

            if (!$student->parent_whatsapp) {
                continue;
            }

            try {
                $message = $this->buildNotificationMessage($attendance, $student);

                \App\Models\WaQueue::create([
                    'user_id' => $session->user_id,
                    'student_id' => $student->id,
                    'phone' => $student->parent_whatsapp,
                    'recipient_name' => $student->father_name ?? 'Orang Tua',
                    'message' => $message,
                    'type' => \App\Models\WaQueue::TYPE_ATTENDANCE,
                    'status' => \App\Models\WaQueue::STATUS_PENDING,
                ]);

                // Dispatch individual send job
                dispatch(new SendWhatsAppJob(\App\Models\WaQueue::latest()->first()));

                $sent++;
            } catch (\Exception $e) {
                $failed++;
                Log::error('Failed to queue notification', [
                    'student_id' => $student->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Attendance notifications queued', [
            'session_id' => $session->id,
            'sent' => $sent,
            'failed' => $failed,
        ]);
    }

    /**
     * Build notification message.
     */
    protected function buildNotificationMessage(Attendance $attendance, Student $student): string
    {
        $statusLabels = [
            'hadir' => '✅ Hadir',
            'terlambat' => '⏰ Terlambat',
            'sakit' => '🏥 Sakit',
            'izin' => '📝 Izin',
            'alpa' => '❌ Alfa',
        ];

        $status = $statusLabels[$attendance->status] ?? $attendance->status;

        $message = "📩 *LAPORAN ABSENSI*\n\n";
        $message .= "Yth. Bapak/Ibu {$student->father_name}\n";
        $message .= "Putra/i: *{$student->name}*\n";
        $message .= "Kelas: {$attendance->student->classModel->name}\n";
        $message .= "Tanggal: {$attendance->date->format('d/m/Y')}\n\n";
        $message .= "Status: {$status}\n\n";
        $message .= "_WaliKelas Pro_";

        return $message;
    }
}
