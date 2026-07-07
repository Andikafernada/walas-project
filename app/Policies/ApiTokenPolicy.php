<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ApiToken;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApiTokenPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ApiToken $token): bool
    {
        return $user->id === $token->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->is_active && $user->isPro;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ApiToken $token): bool
    {
        return $user->id === $token->user_id && $user->is_active;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ApiToken $token): bool
    {
        return $user->id === $token->user_id && $user->is_active;
    }
}
