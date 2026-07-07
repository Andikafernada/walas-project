<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppSession;
use App\Models\Schedule;
use App\Models\AttendanceSession;
use App\Models\OrganizationStructure;
use App\Models\WaQueue;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WhatsAppBotController extends Controller
{
    /**
     * Show WhatsApp connection page.
     */
    public function index()
    {
        $user = auth()->user();

        // Get session for this user (or null if not exists)
        $session = WhatsAppSession::where('user_id', $user->id)->first();

        // Get today's schedules
        $today = strtolower(Carbon::now()->locale('id')->dayName);
        $schedules = Schedule::whereHas('classModel', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->where('day', $today)
        ->where('is_active', true)
        ->with('classModel')
        ->get();

        return view('dashboard.whatsapp-bot', compact('session', 'schedules'));
    }

    /**
     * Generate QR code for WhatsApp connection.
     */
    public function generateQr(Request $request)
    {
        $user = auth()->user();

        // Get or create session
        $session = WhatsAppSession::firstOrCreate(
            ['user_id' => $user->id],
            ['status' => 'disconnected']
        );

        // If already connected, return error
        if ($session->isConnected()) {
            return response()->json([
                'success' => false,
                'message' => 'WhatsApp sudah terhubung!'
            ]);
        }

        // Mark as connecting
        $session->update(['status' => 'connecting']);

        // TODO: In production, integrate with WhatsApp Web API
        // For now, simulate QR generation
        // You would use: Baileys, WA-JS, or similar library

        // Simulate QR (placeholder - replace with real WhatsApp API)
        $qrData = $this->generateRealQrCode($user);
        $session->update([
            'qr_code' => $qrData,
            'qr_expires_at' => Carbon::now()->addMinutes(5),
        ]);

        return response()->json([
            'success' => true,
            'qr_code' => $qrData,
            'expires_at' => $session->qr_expires_at->toIso8601String()
        ]);
    }

    /**
     * Check connection status.
     */
    public function status()
    {
        $user = auth()->user();
        $session = WhatsAppSession::where('user_id', $user->id)->first();

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
     * Simulate successful connection (for demo).
     * In production, this would be called by WebSocket/event from WhatsApp API.
     */
    public function simulateConnect(Request $request)
    {
        $user = auth()->user();
        $session = WhatsAppSession::where('user_id', $user->id)->first();

        if ($session) {
            $session->markAsConnected($request->phone ?? '6281234567890');
        }

        return response()->json(['success' => true]);
    }

    /**
     * Disconnect WhatsApp.
     */
    public function disconnect()
    {
        $user = auth()->user();
        $session = WhatsAppSession::where('user_id', $user->id)->first();

        if ($session) {
            // TODO: Send logout to WhatsApp service
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
        $session = WhatsAppSession::where('user_id', $user->id)
            ->where('status', 'connected')
            ->first();

        if (!$session) {
            return back()->with('error', 'WhatsApp belum terhubung!');
        }

        // Queue the message
        WaQueue::create([
            'user_id' => $user->id,
            'phone' => $request->phone,
            'recipient_name' => 'Test Recipient',
            'message' => $request->message,
            'type' => 'test',
            'status' => 'pending',
        ]);

        return back()->with('success', 'Pesan test queued! Akan dikirim dalam beberapa detik.');
    }

    /**
     * Generate QR code placeholder.
     * REPLACE THIS with real WhatsApp Web API integration.
     */
    private function generateRealQrCode($user): string
    {
        // PLACEHOLDER: Generate a fake QR code
        // In production, integrate with:
        // 1. Baileys (Node.js) - WhatsApp Web API
        // 2. WA-JS library
        // 3. Your custom WhatsApp service

        // For demo, return a base64 placeholder
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAYAAAB2ghYbAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAdklEQVR4nO3PQQoAIBTF0F9Wo6ig4C64e5deNpkPNPE4tJfJTJKcJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJKmPXz+7AQAAAP//6F8J9wAAAABJRU5ErkJggg==';
    }
}
