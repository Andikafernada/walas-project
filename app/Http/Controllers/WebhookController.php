<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebhookController extends Controller
{
    /**
     * Handle incoming WhatsApp messages.
     */
    public function whatsappIncoming(Request $request)
    {
        $from = $request->input('from');
        $body = strtoupper(trim($request->input('body', '')));
        $messageId = $request->input('id');

        // Find user by phone
        $user = \App\Models\User::where('phone', $from)
            ->orWhere('phone', 'like', '%' . ltrim($from, '62') . '%')
            ->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Handle commands
        $response = match($body) {
            'MENU' => $this->getMenu($user),
            'ABSENSI' => $this->getAttendanceStatus($user),
            'STATUS' => $this->getStatus($user),
            'HELP' => $this->getHelp(),
            default => "Perintah tidak dikenali. Ketik *MENU* untuk melihat perintah yang tersedia.",
        };

        // Send response via WhatsApp gateway
        $this->sendWhatsApp($from, $response);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Handle WhatsApp send status callback.
     */
    public function whatsappStatus(Request $request)
    {
        $messageId = $request->input('id');
        $status = $request->input('status');

        // Update wa_queue status
        // ...

        return response()->json(['status' => 'ok']);
    }

    /**
     * N8N attendance webhook.
     */
    public function n8nAttendance(Request $request)
    {
        $secret = $request->header('X-Walas-Secret');

        if ($secret !== config('services.n8n.secret_token')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Process attendance from N8N
        // ...

        return response()->json(['status' => 'ok']);
    }

    /**
     * N8N notification webhook.
     */
    public function n8nNotification(Request $request)
    {
        $secret = $request->header('X-Walas-Secret');

        if ($secret !== config('services.n8n.secret_token')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Process notification from N8N
        // ...

        return response()->json(['status' => 'ok']);
    }

    /**
     * Get menu response.
     */
    protected function getMenu($user): string
    {
        $menu = "*MENU WALI KELAS PRO*\n\n";
        $menu .= "1. *MENU* - Lihat menu ini\n";
        $menu .= "2. *ABSENSI* - Status absensi hari ini\n";
        $menu .= "3. *STATUS* - Status akun Anda\n";
        $menu .= "4. *HELP* - Bantuan\n\n";
        $menu .= "_Hubungi WaliKelas Pro untuk informasi lebih lanjut_";

        return $menu;
    }

    /**
     * Get attendance status.
     */
    protected function getAttendanceStatus($user): string
    {
        $today = \Carbon\Carbon::today();
        $hasAttendance = $user->attendanceSessions()
            ->whereDate('date', $today)
            ->where('status', 'used')
            ->exists();

        if ($hasAttendance) {
            return "✅ *Absensi Hari Ini*\n\nAbsensi kelas Anda hari ini ({$today->format('d/m/Y')}) sudah dicatat. Terima kasih!";
        }

        $pending = $user->attendanceSessions()
            ->whereDate('date', $today)
            ->where('status', 'active')
            ->exists();

        if ($pending) {
            return "📝 *Absensi Hari Ini*\n\nAbsensi hari ini ({$today->format('d/m/Y')}) menunggu untuk diisi. Silakan buka link yang sudah dikirim.";
        }

        return "ℹ️ *Absensi Hari Ini*\n\nBelum ada sesi absensi untuk hari ini ({$today->format('d/m/Y')}).";
    }

    /**
     * Get user status.
     */
    protected function getStatus($user): string
    {
        $status = "*STATUS AKUN*\n\n";
        $status .= "Nama: {$user->name}\n";
        $status .= "Paket: " . strtoupper($user->tier) . "\n";
        $status .= "Kelas: " . $user->classes()->count() . "\n";

        if ($user->subscription_expires_at) {
            $status .= "Berlaku: {$user->subscription_expires_at->format('d/m/Y')}\n";
        }

        return $status;
    }

    /**
     * Get help response.
     */
    protected function getHelp(): string
    {
        $help = "*BANTUAN*\n\n";
        $help .= "Hubungi admin untuk bantuan:\n";
        $help .= "📧 support@walaskelas.pro\n";
        $help .= "📱 WhatsApp: 08xxxxxxxxxx\n\n";
        $help .= "_WaliKelas Pro - Solusi Administrasi Wali Kelas_";

        return $help;
    }

    /**
     * Send WhatsApp message.
     */
    protected function sendWhatsApp(string $phone, string $message): void
    {
        // Implementation via N8N webhook or direct gateway
        // ...
    }
}
