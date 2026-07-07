<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppSession;
use App\Models\Schedule;
use App\Models\AttendanceSession;
use App\Models\OrganizationStructure;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WhatsAppBotController extends Controller
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Show WhatsApp connection page.
     */
    public function index()
    {
        $user = auth()->user();

        // Get or create session for this user
        $session = WhatsAppSession::forUser($user->id)->first();

        return view('dashboard.whatsapp-bot', compact('session'));
    }

    /**
     * Generate QR code for connection.
     */
    public function generateQr(Request $request)
    {
        $user = auth()->user();

        // Get or create session
        $session = WhatsAppSession::firstOrCreate(
            ['user_id' => $user->id],
            ['status' => WhatsAppSession::STATUS_DISCONNECTED]
        );

        // If already connected, return error
        if ($session->isConnected()) {
            return response()->json([
                'success' => false,
                'message' => 'WhatsApp sudah terhubung!'
            ]);
        }

        // Mark as connecting
        $session->markAsConnecting();

        // TODO: Integrate with WhatsApp Web API (Baileys/WA-JS)
        // For now, simulate QR code generation
        // In production, this would connect to a WhatsApp service

        // Simulate QR code (in real implementation, this would come from WhatsApp Web API)
        $qrCode = $this->generateSimulatedQr();
        $session->updateQrCode($qrCode);

        return response()->json([
            'success' => true,
            'qr_code' => $qrCode,
            'expires_at' => $session->qr_expires_at->toIso8601String()
        ]);
    }

    /**
     * Check connection status.
     */
    public function status()
    {
        $user = auth()->user();
        $session = WhatsAppSession::forUser($user->id)->first();

        if (!$session) {
            return response()->json([
                'status' => 'no_session',
                'connected' => false,
            ]);
        }

        return response()->json([
            'status' => $session->status,
            'connected' => $session->isConnected(),
            'phone' => $session->phone,
            'qr_code' => $session->qr_code,
            'qr_expired' => $session->isQrExpired(),
            'last_seen' => $session->last_seen_at?->diffForHumans(),
        ]);
    }

    /**
     * Disconnect WhatsApp.
     */
    public function disconnect()
    {
        $user = auth()->user();
        $session = WhatsAppSession::forUser($user->id)->first();

        if ($session) {
            // TODO: Send logout command to WhatsApp service
            $session->markAsDisconnected();
        }

        return redirect()->back()->with('success', 'WhatsApp berhasil disconnect!');
    }

    /**
     * Send test message.
     */
    public function sendTest(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'message' => 'required|max:1000',
        ]);

        $user = auth()->user();
        $session = WhatsAppSession::forUser($user->id)->first();

        if (!$session || !$session->isConnected()) {
            return back()->with('error', 'WhatsApp belum terhubung!');
        }

        $success = $this->whatsAppService->sendPersonal(
            $session->phone,
            $request->message,
            $user->id
        );

        if ($success) {
            return back()->with('success', 'Pesan test berhasil dikirim!');
        }

        return back()->with('error', 'Gagal mengirim pesan!');
    }

    /**
     * Get schedules for today (for automation preview).
     */
    public function getSchedules()
    {
        $user = auth()->user();
        $today = strtolower(Carbon::now()->locale('id')->dayName);

        $schedules = Schedule::whereHas('classModel', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->where('day', $today)
        ->where('is_active', true)
        ->with('classModel')
        ->get();

        return response()->json($schedules->map(function ($schedule) {
            return [
                'id' => $schedule->id,
                'class' => $schedule->classModel->name,
                'subject' => $schedule->subject,
                'time' => $schedule->start_time . ' - ' . $schedule->end_time,
            ];
        }));
    }

    /**
     * Simulate QR generation (placeholder for real implementation).
     */
    private function generateSimulatedQr(): string
    {
        // This is a placeholder. In production, integrate with:
        // - Baileys (WhatsApp Web API)
        // - WA-JS
        // - Your custom WhatsApp service

        // For demo, return a fake QR data
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAYAAACp8Z5+AAAAHklEQVQI12NkYPh/n0EBBD4LFD4YGBj+hwABBkEAFTkAAGnJgHNwAAAABJRU5ErkJggg==';
    }
}
