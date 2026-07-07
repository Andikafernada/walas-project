<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ClassModel;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClassPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can see their own classes
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ClassModel $class): bool
    {
        return $user->id === $class->user_id;
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
    public function update(User $user, ClassModel $class): bool
    {
        return $user->id === $class->user_id && $user->is_active;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ClassModel $class): bool
    {
        return $user->id === $class->user_id && $user->is_active;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ClassModel $class): bool
    {
        return $user->id === $class->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ClassModel $class): bool
    {
        return $user->id === $class->user_id;
    }
}
