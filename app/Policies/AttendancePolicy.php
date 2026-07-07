<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttendancePolicy
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
    public function view(User $user, Attendance $attendance): bool
    {
        return $user->id === $attendance->user_id;
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
    public function update(User $user, Attendance $attendance): bool
    {
        return $user->id === $attendance->user_id && $user->is_active;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Attendance $attendance): bool
    {
        return $user->id === $attendance->user_id && $user->is_active;
    }

    /**
     * Determine whether the user can export attendance.
     */
    public function export(User $user): bool
    {
        return $user->is_active;
    }

    /**
     * Determine whether the user can send notification for attendance.
     */
    public function sendNotification(User $user, Attendance $attendance): bool
    {
        return $user->id === $attendance->user_id && $user->is_active;
    }
}
