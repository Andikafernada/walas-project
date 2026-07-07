<?php

namespace App\Services;

use App\Models\WaQueue;
use App\Models\AttendanceSession;
use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send a single WhatsApp message.
     */
    public function send(string $phone, string $message, array $options = []): bool
    {
        try {
            // Format phone number
            $phone = $this->formatPhoneNumber($phone);

            // Create queue entry
            $queue = WaQueue::create([
                'user_id' => $options['user_id'] ?? auth()->id(),
                'student_id' => $options['student_id'] ?? null,
                'phone' => $phone,
                'recipient_name' => $options['recipient_name'] ?? 'Unknown',
                'message' => $message,
                'type' => $options['type'] ?? WaQueue::TYPE_ANNOUNCEMENT,
                'status' => WaQueue::STATUS_PENDING,
            ]);

            // Send via n8n webhook
            $webhookUrl = config('services.n8n.webhook_url');
            $webhookToken = config('services.n8n.secret_token');

            if ($webhookUrl) {
                $response = Http::timeout(30)
                    ->post($webhookUrl . '/webhook/whatsapp-send', [
                        'phone' => $phone,
                        'message' => $message,
                        'token' => $webhookToken,
                    ]);

                if ($response->successful()) {
                    $queue->markAsSent($response->body());
                    return true;
                }

                $queue->markAsFailed($response->body());
                return false;
            }

            // Fallback: Direct gateway (Fonnte)
            return $this->sendViaFonnte($phone, $message, $queue);
        } catch (\Exception $e) {
            Log::error('WhatsApp send failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send bulk messages.
     */
    public function sendBulk(array $recipients, string $message, array $options = []): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'queued' => 0,
        ];

        foreach ($recipients as $recipient) {
            $success = $this->send(
                $recipient['phone'],
                $this->replacePlaceholders($message, $recipient['data'] ?? []),
                array_merge($options, [
                    'student_id' => $recipient['student_id'] ?? null,
                    'recipient_name' => $recipient['name'] ?? 'Unknown',
                ])
            );

            if ($success) {
                $results['success']++;
            } else {
                $results['queued']++;
            }
        }

        return $results;
    }

    /**
     * Send magic link attendance to Seksi Kehadiran.
     */
    public function sendAttendanceLink(AttendanceSession $session): bool
    {
        $class = $session->classModel;
        $seksiKehadiran = $class->organizationStructures()
            ->where('position', 'seksi_kehadiran')
            ->where('is_active', true)
            ->first();

        if (!$seksiKehadiran?->student?->parent_whatsapp) {
            Log::warning('No Seksi Kehadiran found for class: ' . $class->id);
            return false;
        }

        $message = $this->buildAttendanceLinkMessage($session, $class);

        return $this->send($seksiKehadiran->student->parent_whatsapp, $message, [
            'user_id' => $session->user_id,
            'student_id' => $seksiKehadiran->student_id,
            'recipient_name' => $seksiKehadiran->student->father_name ?? 'Seksi Kehadiran',
            'type' => WaQueue::TYPE_ATTENDANCE,
        ]);
    }

    /**
     * Send attendance notification to parents.
     */
    public function sendAttendanceNotification(Attendance $attendance): bool
    {
        $student = $attendance->student;

        if (!$student->parent_whatsapp) {
            return false;
        }

        $message = $this->buildAttendanceNotificationMessage($attendance, $student);

        return $this->send($student->parent_whatsapp, $message, [
            'user_id' => $attendance->user_id,
            'student_id' => $student->id,
            'recipient_name' => $student->father_name ?? 'Orang Tua',
            'type' => WaQueue::TYPE_ATTENDANCE,
        ]);
    }

    /**
     * Send violation warning to parent.
     */
    public function sendViolationWarning($violation): bool
    {
        $student = $violation->student;

        if (!$student->parent_whatsapp) {
            return false;
        }

        $message = $this->buildViolationWarningMessage($violation, $student);

        return $this->send($student->parent_whatsapp, $message, [
            'user_id' => $violation->user_id,
            'student_id' => $student->id,
            'recipient_name' => $student->father_name ?? 'Orang Tua',
            'type' => WaQueue::TYPE_WARNING,
        ]);
    }

    /**
     * Format phone number to Indonesia format.
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove all non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Handle various formats
        if (str_starts_with($phone, '0')) {
            // 08xx -> 628xx
            $phone = '62' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '62')) {
            // Add 62 if not present
            $phone = '62' . $phone;
        }

        // Ensure no + sign
        return ltrim($phone, '+');
    }

    /**
     * Send via Fonnte API directly.
     */
    protected function sendViaFonnte(string $phone, string $message, WaQueue $queue): bool
    {
        $apiKey = config('services.fonnte.api_key');
        $url = config('services.fonnte.url', 'https://mu.fonnte.com/api/send');

        if (!$apiKey) {
            // Just queue it
            Log::info('WhatsApp queued (no gateway configured): ' . $queue->id);
            return true;
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => $apiKey,
                ])
                ->post($url, [
                    'target' => $phone,
                    'message' => $message,
                ]);

            if ($response->successful()) {
                $queue->markAsSent($response->body());
                return true;
            }

            $queue->markAsFailed($response->body());
            return false;
        } catch (\Exception $e) {
            $queue->markAsFailed($e->getMessage());
            return false;
        }
    }

    /**
     * Build attendance link message.
     */
    protected function buildAttendanceLinkMessage(AttendanceSession $session, $class): string
    {
        $message = "🔗 *LINK ABSENSI KELAS {$class->name}*\n\n";
        $message .= "Silakan isi absensi harian melalui tautan berikut:\n\n";
        $message .= "{$session->magic_link}\n\n";
        $message .= "PIN: *{$session->pin}*\n\n";
        $message .= "⏰ Batas waktu: {$session->expires_at->format('H:i')} WIB\n";
        $message .= "_Jangan sebarkan link ini ke siapa pun!_";

        return $message;
    }

    /**
     * Build attendance notification message.
     */
    protected function buildAttendanceNotificationMessage(Attendance $attendance, Student $student): string
    {
        $statusLabels = [
            'hadir' => '✅ Hadir',
            'terlambat' => '⏰ Terlambat',
            'sakit' => '🏥 Sakit',
            'izin' => '📝 Izin',
            'alpa' => '❌ Alfa',
        ];

        $message = "📩 *LAPORAN ABSENSI*\n\n";
        $message .= "Yth. Bapak/Ibu {$student->father_name}\n";
        $message .= "Putra/i: *{$student->name}*\n";
        $message .= "Kelas: {$attendance->student->classModel->name}\n";
        $message .= "Tanggal: {$attendance->date->format('d/m/Y')}\n\n";
        $message .= "Status: *{$statusLabels[$attendance->status]}*\n\n";
        $message .= "_WaliKelas Pro_";

        return $message;
    }

    /**
     * Build violation warning message.
     */
    protected function buildViolationWarningMessage($violation, Student $student): string
    {
        $message = "⚠️ *PERINGATAN PELANGGARAN*\n\n";
        $message .= "Yth. Bapak/Ibu {$student->father_name}\n";
        $message .= "Putra/i: *{$student->name}*\n\n";
        $message .= "Terdapat pelanggaran yang tercatat:\n\n";
        $message .= "📋 Kategori: " . (\App\Models\Violation::CATEGORIES[$violation->category] ?? $violation->category) . "\n";
        $message .= "📝 Keterangan: {$violation->description}\n";
        $message .= "⚖️ Tingkat: {$violation->severity}\n";
        $message .= "📉 Poin: -{$violation->poin_reduced}\n";
        $message .= "📅 Tanggal: {$violation->date->format('d/m/Y')}\n\n";
        $message .= "Total Poin Sekarang: *{$violation->poin_after}*\n\n";
        $message .= "_Silakan hubungi wali kelas untuk informasi lebih lanjut._\n";
        $message .= "_WaliKelas Pro_";

        return $message;
    }

    /**
     * Replace placeholders in message.
     */
    protected function replacePlaceholders(string $message, array $data): string
    {
        foreach ($data as $key => $value) {
            $message = str_replace('{{' . $key . '}}', $value, $message);
        }
        return $message;
    }
}
