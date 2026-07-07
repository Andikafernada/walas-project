<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    // Role constants
    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_ADMIN = 'admin';
    const ROLE_WALAS = 'walas';

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'organization_id',
        'role',
        'phone',
        'school_name',
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
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

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

    public function getIsSuperAdminAttribute(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function getIsAdminAttribute(): bool
    {
        return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN]);
    }

    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            self::ROLE_SUPER_ADMIN => 'Super Admin',
            self::ROLE_ADMIN => 'Admin Sekolah',
            self::ROLE_WALAS => 'Wali Kelas',
            default => $this->role,
        };
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return $this->avatar;
        }

        if ($this->google_id) {
            return "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&background=2563eb&color=fff&size=128";
        }

        return "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&background=2563eb&color=fff&size=128";
    }

    // Scopes
    public function scopeSuperAdmins($query)
    {
        return $query->where('role', self::ROLE_SUPER_ADMIN);
    }

    public function scopeAdmins($query)
    {
        return $query->whereIn('role', [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN]);
    }

    public function scopeWalas($query)
    {
        return $query->where('role', self::ROLE_WALAS);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }
}
