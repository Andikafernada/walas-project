<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to validate API tokens for external integrations.
 * Used for CBT/ExamBrowser API access.
 */
class ValidateApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $ability = 'read'): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'API token required'
            ], 401);
        }

        $apiToken = \App\Models\ApiToken::where('token', hash('sha256', $token))
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$apiToken) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Invalid or expired API token'
            ], 401);
        }

        // Check ability
        if (!$apiToken->can($ability) && !$apiToken->can('*')) {
            return response()->json([
                'error' => 'Forbidden',
                'message' => "Token does not have '{$ability}' permission"
            ], 403);
        }

        // Update last used
        $apiToken->update(['last_used_at' => now()]);

        // Set the user context
        $request->setUserResolver(fn() => $apiToken->user);

        return $next($request);
    }
}
