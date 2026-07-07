<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to log user activity for audit trail.
 */
class LogActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $action = null): Response
    {
        return $next($request);
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     */
    public function terminate(Request $request, Response $response): void
    {
        $user = $request->user();

        if (!$user) {
            return;
        }

        // Only log write operations
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return;
        }

        // Determine the action
        $action = $this->determineAction($request);

        if (!$action) {
            return;
        }

        // Get the model being affected
        $model = $this->getModelFromRequest($request);

        // Log the activity
        \App\Models\ActivityLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'old_values' => $this->getOldValues($request, $model),
            'new_values' => $this->getNewValues($request),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    /**
     * Determine the action from request method.
     */
    protected function determineAction(Request $request): ?string
    {
        return match($request->method()) {
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => null,
        };
    }

    /**
     * Get the model from request route.
     */
    protected function getModelFromRequest(Request $request): ?\Illuminate\Database\Eloquent\Model
    {
        $route = $request->route();

        if (!$route) {
            return null;
        }

        $parameters = $route->parameters();

        foreach ($parameters as $parameter) {
            if ($parameter instanceof \Illuminate\Database\Eloquent\Model) {
                return $parameter;
            }
        }

        return null;
    }

    /**
     * Get old values for update/delete operations.
     */
    protected function getOldValues(Request $request, $model): ?array
    {
        if (!$model || !in_array($request->method(), ['PUT', 'PATCH', 'DELETE'])) {
            return null;
        }

        $hidden = $model->getHidden();
        $fillable = $model->getFillable();

        $values = [];
        foreach ($fillable as $field) {
            if (!in_array($field, $hidden)) {
                $values[$field] = $model->{$field};
            }
        }

        return $values ?: null;
    }

    /**
     * Get new values from request.
     */
    protected function getNewValues(Request $request): ?array
    {
        if ($request->method() === 'DELETE') {
            return null;
        }

        return $request->except(['password', 'password_confirmation', '_token', '_method']);
    }
}
