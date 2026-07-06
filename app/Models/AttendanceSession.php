<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AttendanceSession extends Model
{
    protected $fillable = [
        'class_id',
        'user_id',
        'date',
        'token',
        'pin',
        'method',
        'status',
        'expires_at',
        'submitted_at',
        'submitted_by',
        'submitted_by_name',
    ];

    protected $casts = [
        'date' => 'date',
        'expires_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    // Generate secure token
    public static function generateToken(): string
    {
        return Str::random(64);
    }

    // Generate daily PIN
    public static function generatePin(): string
    {
        return str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    }

    // Relations
    public function classModel(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'submitted_by');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    // Accessors
    public function getMagicLinkAttribute(): string
    {
        $baseUrl = config('app.url');
        return "{$baseUrl}/absensi/{$this->token}";
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at->isPast();
    }

    public function getRemainingMinutesAttribute(): int
    {
        return max(0, now()->diffInMinutes($this->expires_at, false));
    }
}
