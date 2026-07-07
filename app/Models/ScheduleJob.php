<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleJob extends Model
{
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getClassIdsArrayAttribute(): array
    {
        return array_filter(explode(',', $this->class_ids ?? ''));
    }
}
