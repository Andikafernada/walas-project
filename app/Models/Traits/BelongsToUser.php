<?php

namespace App\Models\Traits;

trait BelongsToUser
{
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
