<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to ensure the authenticated user owns the requested resource.
 *
 * Usage in routes:
 * Route::get('/classes/{class}', [ClassController::class, 'show'])
 *     ->middleware('tenant');
 */
class EnsureTenantOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $resourceType = 'class'): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get the resource from route parameters
        $resource = $this->getResourceFromRequest($request, $resourceType);

        if ($resource && method_exists($resource, 'classModel')) {
            // For resources that belong to a class
            $class = $resource->classModel;

            if ($class->user_id !== $user->id) {
                abort(403, 'You do not have permission to access this resource.');
            }
        } elseif ($resource && property_exists($resource, 'user_id')) {
            // For resources with direct user_id
            if ($resource->user_id !== $user->id) {
                abort(403, 'You do not have permission to access this resource.');
            }
        }

        return $next($request);
    }

    /**
     * Get the resource from the request.
     */
    protected function getResourceFromRequest(Request $request, string $resourceType): ?Model
    {
        $parameterName = match($resourceType) {
            'class' => 'class',
            'student' => 'student',
            'attendance' => 'attendance',
            'attendance_session' => 'session',
            'violation' => 'violation',
            'cash_book' => 'cashBook',
            'schedule' => 'schedule',
            'journal' => 'journal',
            'structure' => 'structure',
            'seating_chart' => 'chart',
            default => $resourceType,
        };

        return $request->route($parameterName);
    }
}
