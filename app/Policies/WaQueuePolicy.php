<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WaQueue;
use Illuminate\Auth\Access\HandlesAuthorization;

class WaQueuePolicy
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
    public function view(User $user, WaQueue $queue): bool
    {
        return $user->id === $queue->user_id;
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
    public function update(User $user, WaQueue $queue): bool
    {
        return $user->id === $queue->user_id && $user->is_active;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WaQueue $queue): bool
    {
        return $user->id === $queue->user_id && $user->is_active;
    }

    /**
     * Determine whether the user can retry failed message.
     */
    public function retry(User $user, WaQueue $queue): bool
    {
        return $user->id === $queue->user_id && $queue->status === 'failed' && $user->is_active;
    }

    /**
     * Determine whether the user can send bulk messages.
     */
    public function sendBulk(User $user): bool
    {
        return $user->is_active;
    }
}
