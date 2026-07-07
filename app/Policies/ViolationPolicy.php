<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Violation;
use Illuminate\Auth\Access\HandlesAuthorization;

class ViolationPolicy
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
    public function view(User $user, Violation $violation): bool
    {
        return $user->id === $violation->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->is_active;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Violation $violation): bool
    {
        return $user->id === $violation->user_id && $user->is_active;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Violation $violation): bool
    {
        return $user->id === $violation->user_id && $user->is_active;
    }

    /**
     * Determine whether the user can approve a violation.
     */
    public function approve(User $user, Violation $violation): bool
    {
        return $user->id === $violation->user_id && $user->is_active;
    }

    /**
     * Determine whether the user can reject a violation.
     */
    public function reject(User $user, Violation $violation): bool
    {
        return $user->id === $violation->user_id && $user->is_active;
    }

    /**
     * Determine whether the user can send warning notification.
     */
    public function sendWarning(User $user, Violation $violation): bool
    {
        return $user->id === $violation->user_id && $user->is_active;
    }
}
