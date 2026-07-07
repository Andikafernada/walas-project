<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'name',
        'status', // disconnected, connecting, connected, error
        'qr_code',
        'qr_expires_at',
        'last_seen_at',
        'session_data',
        'error_message',
    ];

    protected $casts = [
        'session_data' => 'encrypted',
        'qr_expires_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    // Status constants
    const STATUS_DISCONNECTED = 'disconnected';
    const STATUS_CONNECTING = 'connecting';
    const STATUS_CONNECTED = 'connected';
    const STATUS_ERROR = 'error';

    /**
     * Get the user that owns this WhatsApp session.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if session is active.
     */
    public function isConnected(): bool
    {
        return $this->status === self::STATUS_CONNECTED;
    }

    /**
     * Check if QR code is expired.
     */
    public function isQrExpired(): bool
    {
        return $this->qr_expires_at && $this->qr_expires_at->isPast();
    }

    /**
     * Get QR code as base64 image.
     */
    public function getQrCodeImageAttribute()
    {
        if (!$this->qr_code) {
            return null;
        }

        // QR code format: data:image/png;base64,...
        if (str_starts_with($this->qr_code, 'data:')) {
            return $this->qr_code;
        }

        // If plain base64, add prefix
        return 'data:image/png;base64,' . $this->qr_code;
    }

    /**
     * Mark as connecting.
     */
    public function markAsConnecting(): void
    {
        $this->update([
            'status' => self::STATUS_CONNECTING,
            'error_message' => null,
        ]);
    }

    /**
     * Mark as connected.
     */
    public function markAsConnected(string $phone): void
    {
        $this->update([
            'status' => self::STATUS_CONNECTED,
            'phone' => $phone,
            'qr_code' => null,
            'qr_expires_at' => null,
            'error_message' => null,
            'last_seen_at' => now(),
        ]);
    }

    /**
     * Mark as error.
     */
    public function markAsError(string $message): void
    {
        $this->update([
            'status' => self::STATUS_ERROR,
            'error_message' => $message,
        ]);
    }

    /**
     * Mark as disconnected.
     */
    public function markAsDisconnected(): void
    {
        $this->update([
            'status' => self::STATUS_DISCONNECTED,
            'qr_code' => null,
            'qr_expires_at' => null,
        ]);
    }

    /**
     * Update QR code.
     */
    public function updateQrCode(string $qr): void
    {
        $this->update([
            'qr_code' => $qr,
            'qr_expires_at' => now()->addMinutes(5),
            'status' => self::STATUS_CONNECTING,
        ]);
    }

    /**
     * Scope: Active sessions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_CONNECTED);
    }

    /**
     * Scope: By user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
