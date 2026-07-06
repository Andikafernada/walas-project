<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaQueue extends Model
{
    protected $fillable = [
        'user_id',
        'student_id',
        'phone',
        'recipient_name',
        'message',
        'type',
        'status',
        'response',
        'attempts',
        'error',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    const TYPE_ATTENDANCE = 'attendance';
    const TYPE_ANNOUNCEMENT = 'announcement';
    const TYPE_WARNING = 'warning';
    const TYPE_REPORT = 'report';

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function markAsSent(string $response): void
    {
        $this->update([
            'status' => self::STATUS_SENT,
            'response' => $response,
            'sent_at' => now(),
        ]);
    }

    public function markAsFailed(string $error): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error' => $error,
            'attempts' => $this->attempts + 1,
        ]);
    }
}
