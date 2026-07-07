<?php

namespace App\Jobs;

use App\Models\Violation;
use App\Models\Student;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendViolationWarningJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public Violation $violation
    ) {}

    public function handle(WhatsAppService $whatsAppService): void
    {
        $violation->load('student.classModel');
        $student = $violation->student;

        if (!$student->parent_whatsapp) {
            Log::info('No parent WhatsApp for violation notification', [
                'violation_id' => $this->violation->id,
            ]);
            return;
        }

        $message = $this->buildMessage($violation, $student);

        \App\Models\WaQueue::create([
            'user_id' => $violation->user_id,
            'student_id' => $student->id,
            'phone' => $student->parent_whatsapp,
            'recipient_name' => $student->father_name ?? 'Orang Tua',
            'message' => $message,
            'type' => \App\Models\WaQueue::TYPE_WARNING,
            'status' => \App\Models\WaQueue::STATUS_PENDING,
        ]);

        dispatch(new SendWhatsAppJob(\App\Models\WaQueue::latest()->first()));

        Log::info('Violation warning queued', [
            'violation_id' => $this->violation->id,
            'student_id' => $student->id,
        ]);
    }

    protected function buildMessage(Violation $violation, Student $student): string
    {
        $categoryLabels = \App\Models\Violation::CATEGORIES;
        $category = $categoryLabels[$violation->category] ?? $violation->category;

        $message = "⚠️ *PERINGATAN PELANGGARAN*\n\n";
        $message .= "Yth. Bapak/Ibu {$student->father_name},\n";
        $message .= "Putra/i: *{$student->name}*\n\n";
        $message .= "Terdapat pelanggaran yang tercatat:\n\n";
        $message .= "📋 Kategori: {$category}\n";
        $message .= "📝 Keterangan: {$violation->description}\n";
        $message .= "⚖️ Tingkat: {$violation->severity}\n";
        $message .= "📉 Poin: -{$violation->poin_reduced}\n";
        $message .= "📅 Tanggal: {$violation->date->format('d/m/Y')}\n\n";
        $message .= "Total Poin Sekarang: *{$violation->poin_after}*\n\n";
        $message .= "_Silakan hubungi wali kelas untuk informasi lebih lanjut._\n";
        $message .= "_WaliKelas Pro_";

        return $message;
    }
}
