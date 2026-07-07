<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'address',
        'city',
        'province',
        'phone',
        'email',
        'logo',
        'website',
        'status',
        'notes',
    ];

    protected $casts = [
        'status' => 'string',
        'type' => 'string',
    ];

    /**
     * Get the users (walas) for this organization
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the classes for this organization
     */
    public function classes(): HasMany
    {
        return $this->hasMany(\App\Models\ClassModel::class);
    }

    /**
     * Get active users count
     */
    public function getActiveUsersCountAttribute(): int
    {
        return $this->users()->where('is_active', true)->count();
    }

    /**
     * Get total students count
     */
    public function getTotalStudentsCountAttribute(): int
    {
        return $this->classes()->withCount('students')->get()->sum('students_count');
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'sd' => 'Sekolah Dasar',
            'smp' => 'Sekolah Menengah Pertama',
            'sma' => 'Sekolah Menengah Atas',
            'smk' => 'Sekolah Menengah Kejuruan',
            'others' => 'Lainnya',
            default => $this->type,
        };
    }

    /**
     * Generate unique slug
     */
    public static function generateSlug(string $name): string
    {
        $slug = \Illuminate\Support\Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
