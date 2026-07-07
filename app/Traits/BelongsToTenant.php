<?php

namespace App\Traits;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait to automatically apply tenant scoping to models.
 *
 * Usage:
 * class MyModel extends Model
 * {
 *     use BelongsToTenant;
 * }
 */
trait BelongsToTenant
{
    /**
     * Boot the trait.
     */
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope());
    }
}
