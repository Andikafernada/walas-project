<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AttendanceSession;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttendanceSessionPolicy
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
    public function view(User $user, AttendanceSession $session): bool
    {
        return $user->id === $session->user_id;
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
    public function update(User $user, AttendanceSession $session): bool
    {
        return $user->id === $session->user_id && $user->is_active;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AttendanceSession $session): bool
    {
        return $user->id === $session->user_id && $user->is_active;
    }

    /**
     * Determine whether the user can generate magic link.
     */
    public function generateLink(User $user): bool
    {
        return $user->is_active;
    }

    /**
     * Determine whether the user can submit attendance.
     * Note: This is called via public magic link, so we check via token
     */
    public function submitAttendance(User $user, AttendanceSession $session): bool
    {
        // Magic link submission - allow if session is active and not expired
        return $session->status === 'active' && !$session->isExpired;
    }

    /**
     * Determine whether the user can send reminder.
     */
    public function sendReminder(User $user, AttendanceSession $session): bool
    {
        return $user->id === $session->user_id && $session->status === 'active' && !$session->isExpired;
    }
}
