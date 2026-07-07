<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * Global scope that filters queries to only show records
 * belonging to the currently authenticated user.
 */
class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Only apply scope if the model has user_id column
            if (in_array('user_id', $model->getFillable())) {
                $builder->where($model->getTable() . '.user_id', $user->id);
            }
        }
    }
}
