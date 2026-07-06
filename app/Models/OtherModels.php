<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationStructure extends Model{
    protected $table = 'organization_structures';

    protected $fillable = [
        'class_id',
        'student_id',
        'position',
        'academic_year',
        'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    const POSITIONS = [
        'ketua_kelas' => 'Ketua Kelas',
        'wakil_ketua' => 'Wakil Ketua',
        'sekretaris' => 'Sekretaris',
        'bendahara' => 'Bendahara',
        'seksi_kehadiran' => 'Seksi Kehadiran',
        'seksi_barang' => 'Seksi Barang Hilang/Rusak',
        'seksi_kebersihan' => 'Seksi Kebersihan',
        'seksi_keamanan' => 'Seksi Keamanan',
        'seksi_olahraga' => 'Seksi Olahraga',
        'seksi_kesenian' => 'Seksi Kesenian',
    ];

    public function classModel(): BelongsTo{
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function student(): BelongsTo{
        return $this->belongsTo(Student::class);
    }
}

class SeatingChart extends Model{
    protected $table = 'seating_charts';

    protected $fillable = [
        'class_id',
        'name',
        'layout',
        'effective_date',
        'expired_date',
        'is_active',
    ];

    protected $casts = [
        'layout' => 'array',
        'effective_date' => 'date',
        'expired_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function classModel(): BelongsTo{
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function getLayoutGridAttribute(): array{
        $layout = $this->layout ?? ['rows' => 5, 'cols' => 6];
        return $layout;
    }
}

class ScheduleJob extends Model{
    protected $table = 'schedule_jobs';

    protected $fillable = [
        'user_id',
        'type',
        'class_ids',
        'schedule_time',
        'day_of_week',
        'is_active',
        'last_run',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_run' => 'datetime',
    ];

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function getClassIdsArrayAttribute(): array{
        return array_filter(explode(',', $this->class_ids ?? ''));
    }
}

class ApiToken extends Model{
    protected $fillable = [
        'user_id',
        'name',
        'token',
        'abilities',
        'expires_at',
        'last_used_at',
    ];

    protected $casts = [
        'abilities' => 'array',
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    protected $hidden = ['token'];

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool{
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function can(string $ability): bool{
        return in_array('*', $this->abilities ?? []) || in_array($ability, $this->abilities ?? []);
    }
}

class ActivityLog extends Model{
    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
}