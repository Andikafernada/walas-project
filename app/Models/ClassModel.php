<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ClassModel extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'user_id',
        'name',
        'alias',
        'jurusan',
        'tingkat',
        'school_year_start',
        'school_year_end',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'school_year_start' => 'integer',
        'school_year_end' => 'integer',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function attendanceSessions(): HasMany
    {
        return $this->hasMany(AttendanceSession::class);
    }

    public function violations(): HasMany
    {
        return $this->hasMany(Violation::class);
    }

    public function cashBooks(): HasMany
    {
        return $this->hasMany(CashBook::class);
    }

    public function organizationStructures(): HasMany
    {
        return $this->hasMany(OrganizationStructure::class);
    }

    public function seatingCharts(): HasMany
    {
        return $this->hasMany(SeatingChart::class);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return "{$this->name} - {$this->jurusan}";
    }

    public function getStudentCountAttribute(): int
    {
        return $this->students()->where('is_active', true)->count();
    }
}
