<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SeatingChart;
use Illuminate\Auth\Access\HandlesAuthorization;

class SeatingChartPolicy
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
    public function view(User $user, SeatingChart $chart): bool
    {
        return $user->id === $chart->classModel->user_id;
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
    public function update(User $user, SeatingChart $chart): bool
    {
        return $user->id === $chart->classModel->user_id && $user->is_active;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SeatingChart $chart): bool
    {
        return $user->id === $chart->classModel->user_id && $user->is_active;
    }

    /**
     * Determine whether the user can print seating chart.
     */
    public function print(User $user, SeatingChart $chart): bool
    {
        return $user->id === $chart->classModel->user_id;
    }
}
