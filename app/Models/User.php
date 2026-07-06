<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'school_name',
        'avatar',
        'role',
        'tier',
        'subscription_expires_at',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'subscription_expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    // Relations
    public function classes(): HasMany
    {
        return $this->hasMany(\App\Models\ClassModel::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function journals(): HasMany
    {
        return $this->hasMany(Journal::class);
    }

    public function violations(): HasMany
    {
        return $this->hasMany(Violation::class);
    }

    public function cashBooks(): HasMany
    {
        return $this->hasMany(CashBook::class);
    }

    public function attendanceSessions(): HasMany
    {
        return $this->hasMany(AttendanceSession::class);
    }

    public function waQueues(): HasMany
    {
        return $this->hasMany(WaQueue::class);
    }

    public function scheduleJobs(): HasMany
    {
        return $this->hasMany(ScheduleJob::class);
    }

    public function apiTokens(): HasMany
    {
        return $this->hasMany(ApiToken::class);
    }

    // Accessors
    public function getIsProAttribute(): bool
    {
        return $this->tier === 'pro' || $this->tier === 'enterprise';
    }

    public function getIsSubscriptionActiveAttribute(): bool
    {
        if (in_array($this->tier, ['pro', 'enterprise'])) {
            return true;
        }
        return $this->subscription_expires_at && $this->subscription_expires_at->isFuture();
    }
}
