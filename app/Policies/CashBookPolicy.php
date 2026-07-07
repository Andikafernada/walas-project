<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CashBook;
use Illuminate\Auth\Access\HandlesAuthorization;

class CashBookPolicy
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
    public function view(User $user, CashBook $cashBook): bool
    {
        return $user->id === $cashBook->user_id;
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
    public function update(User $user, CashBook $cashBook): bool
    {
        return $user->id === $cashBook->user_id && $user->is_active;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CashBook $cashBook): bool
    {
        return $user->id === $cashBook->user_id && $user->is_active;
    }

    /**
     * Determine whether the user can export report.
     */
    public function export(User $user): bool
    {
        return $user->is_active;
    }

    /**
     * Determine whether the user can print receipt.
     */
    public function printReceipt(User $user, CashBook $cashBook): bool
    {
        return $user->id === $cashBook->user_id;
    }
}
