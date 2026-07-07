<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Student;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentPolicy
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
    public function view(User $user, Student $student): bool
    {
        return $user->id === $student->classModel->user_id;
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
    public function update(User $user, Student $student): bool
    {
        return $user->id === $student->classModel->user_id && $user->is_active;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Student $student): bool
    {
        return $user->id === $student->classModel->user_id && $user->is_active;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Student $student): bool
    {
        return $user->id === $student->classModel->user_id;
    }

    /**
     * Determine whether the user can force delete the model.
     */
    public function forceDelete(User $user, Student $student): bool
    {
        return $user->id === $student->classModel->user_id;
    }

    /**
     * Determine whether the user can manage violations for the student.
     */
    public function manageViolation(User $user, Student $student): bool
    {
        return $user->id === $student->classModel->user_id && $user->is_active;
    }

    /**
     * Determine whether the user can view student report.
     */
    public function viewReport(User $user, Student $student): bool
    {
        return $user->id === $student->classModel->user_id;
    }
}
