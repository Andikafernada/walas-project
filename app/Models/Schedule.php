<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    protected $table = 'schedules';

    protected $fillable = [
        'class_id',
        'subject',
        'teacher_name',
        'day',
        'start_time',
        'end_time',
        'week_number',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    const DAYS = [
        'senin' => 'Senin',
        'selasa' => 'Selasa',
        'rabu' => 'Rabu',
        'kamis' => 'Kamis',
        'jumat' => 'Jumat',
        'sabtu' => 'Sabtu',
        'minggu' => 'Minggu',
    ];

    public function classModel(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function getDurationAttribute(): int
    {
        $start = strtotime($this->start_time);
        $end = strtotime($this->end_time);
        return round(($end - $start) / 60);
    }
}
