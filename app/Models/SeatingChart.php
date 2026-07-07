<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeatingChart extends Model
{
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

    public function classModel(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function getLayoutGridAttribute(): array
    {
        $layout = $this->layout ?? ['rows' => 5, 'cols' => 6];
        return $layout;
    }
}
