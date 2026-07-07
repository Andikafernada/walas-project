<?php

namespace App\Jobs;

use App\Models\AttendanceSession;
use App\Models\ClassModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateDailyAttendanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function handle(): void
    {
        $today = now()->toDateString();
        $created = 0;

        // Get all active users with classes
        $users = \App\Models\User::where('is_active', true)->get();

        foreach ($users as $user) {
            $classes = $user->classes;

            foreach ($classes as $class) {
                // Check if session already exists for today
                $exists = AttendanceSession::where('class_id', $class->id)
                    ->whereDate('date', $today)
                    ->exists();

                if ($exists) {
                    continue;
                }

                // Create new session
                $session = AttendanceSession::create([
                    'class_id' => $class->id,
                    'user_id' => $user->id,
                    'date' => $today,
                    'token' => \Illuminate\Support\Str::random(64),
                    'pin' => str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT),
                    'method' => 'magic_link',
                    'status' => 'active',
                    'expires_at' => now()->setTime(
                        config('walas.attendance.default_expires_at', '15:00'),
                        0
                    ),
                ]);

                // Queue WhatsApp notification
                \App\Models\WaQueue::create([
                    'user_id' => $user->id,
                    'phone' => $this->getSeksiKehadiranPhone($class),
                    'recipient_name' => 'Seksi Kehadiran',
                    'message' => $this->buildAttendanceLinkMessage($session, $class),
                    'type' => \App\Models\WaQueue::TYPE_ATTENDANCE,
                    'status' => \App\Models\WaQueue::STATUS_PENDING,
                ]);

                dispatch(new SendWhatsAppJob(\App\Models\WaQueue::latest()->first()));

                $created++;
            }
        }

        Log::info('Daily attendance sessions generated', [
            'created' => $created,
        ]);
    }

    protected function getSeksiKehadiranPhone(ClassModel $class): ?string
    {
        $structure = $class->organizationStructures()
            ->where('position', 'seksi_kehadiran')
            ->where('is_active', true)
            ->first();

        return $structure?->student?->parent_whatsapp;
    }

    protected function buildAttendanceLinkMessage(AttendanceSession $session, ClassModel $class): string
    {
        $message = "🔗 *LINK ABSENSI KELAS {$class->name}*\n\n";
        $message .= "Silakan isi absensi harian melalui tautan berikut:\n\n";
        $message .= "{$session->magic_link}\n\n";
        $message .= "PIN: *{$session->pin}*\n\n";
        $message .= "⏰ Batas waktu: {$session->expires_at->format('H:i')} WIB\n";
        $message .= "_Jangan sebarkan link ini!_";

        return $message;
    }
}
