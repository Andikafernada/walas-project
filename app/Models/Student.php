<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'class_id',
        'nisn',
        'nis',
        'name',
        'gender',
        'birth_date',
        'birth_place',
        'religion',
        'address',
        'father_name',
        'mother_name',
        'parent_phone',
        'parent_whatsapp',
        'emergency_contact',
        'photo',
        'poin',
        'is_active',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'poin' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relations
    public function classModel(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function user(): BelongsTo
    {
        return $this->classModel->user();
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function violations(): HasMany
    {
        return $this->hasMany(Violation::class);
    }

    public function journals(): HasMany
    {
        return $this->hasMany(Journal::class);
    }

    public function organizationStructures(): HasMany
    {
        return $this->hasMany(OrganizationStructure::class);
    }

    // Accessors
    public function getParentContactAttribute(): ?string
    {
        return $this->parent_whatsapp ?? $this->parent_phone;
    }

    public function getFormattedPoinAttribute(): string
    {
        $color = $this->poin >= 80 ? 'green' : ($this->poin >= 60 ? 'yellow' : 'red');
        return "<span class='text-{$color}'>{$this->poin}</span>";
    }
}
