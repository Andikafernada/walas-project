<?php

namespace App\Jobs;

use App\Models\WaQueue;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWhatsAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of retry attempts.
     */
    public int $tries = 3;

    /**
     * Backoff intervals in seconds.
     */
    public array $backoff = [30, 60, 120];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public WaQueue $queue
    ) {}

    /**
     * Execute the job.
     */
    public function handle(WhatsAppService $whatsAppService): void
    {
        $this->queue->update(['status' => WaQueue::STATUS_PROCESSING]);

        try {
            // Format phone number
            $phone = $this->formatPhoneNumber($this->queue->phone);

            // Send via configured gateway
            $gateway = config('services.whatsapp.driver', 'n8n');

            if ($gateway === 'fonnte') {
                $this->sendViaFonnte($phone);
            } else {
                $this->sendViaN8n($phone);
            }

            // Mark as sent
            $this->queue->markAsSent('Sent successfully');

            Log::info('WhatsApp message sent', [
                'queue_id' => $this->queue->id,
                'phone' => substr($phone, 0, 6) . '****',
            ]);
        } catch (\Exception $e) {
            Log::error('WhatsApp send failed', [
                'queue_id' => $this->queue->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $this->queue->markAsFailed($exception->getMessage());

        Log::error('WhatsApp job permanently failed', [
            'queue_id' => $this->queue->id,
            'attempts' => $this->attempts(),
            'error' => $exception->getMessage(),
        ]);
    }

    /**
     * Send via Fonnte API.
     */
    protected function sendViaFonnte(string $phone): void
    {
        $apiKey = config('services.whatsapp.fonnte.api_key');
        $url = config('services.whatsapp.fonnte.url', 'https://mu.fonnte.com/api/send');

        if (!$apiKey) {
            throw new \Exception('Fonnte API key not configured');
        }

        $response = Http::timeout(30)
            ->withHeaders(['Authorization' => $apiKey])
            ->post($url, [
                'target' => $phone,
                'message' => $this->queue->message,
            ]);

        if (!$response->successful()) {
            throw new \Exception('Fonnte API error: ' . $response->body());
        }
    }

    /**
     * Send via N8N webhook.
     */
    protected function sendViaN8n(string $phone): void
    {
        $webhookUrl = config('services.whatsapp.n8n.webhook_url');

        if (!$webhookUrl) {
            throw new \Exception('N8N webhook URL not configured');
        }

        $response = Http::timeout(30)
            ->post($webhookUrl . '/whatsapp/send', [
                'phone' => $phone,
                'message' => $this->queue->message,
                'token' => config('services.whatsapp.n8n.secret_token'),
            ]);

        if (!$response->successful()) {
            throw new \Exception('N8N webhook error: ' . $response->body());
        }
    }

    /**
     * Format phone number.
     */
    protected function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            return '62' . substr($phone, 1);
        }

        return $phone;
    }
}
