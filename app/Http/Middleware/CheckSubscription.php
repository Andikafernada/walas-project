<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to check if user subscription is active.
 * Redirects to subscription page if expired.
 */
class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature = null): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check if subscription is active
        if (!$user->isSubscriptionActive) {
            // For API requests, return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Subscription Required',
                    'message' => 'Your subscription has expired. Please renew to continue using the service.',
                    'upgrade_url' => route('subscription.upgrade'),
                ], 402);
            }

            // For web requests, redirect to upgrade page
            return redirect()->route('subscription.upgrade')
                ->with('warning', 'Subscription Anda sudah expired. Mohon perpanjang untuk melanjutkan.');
        }

        // Check for specific feature access (for tier-based features)
        if ($feature && !$this->hasFeatureAccess($user, $feature)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Feature Not Available',
                    'message' => "This feature requires a higher subscription tier.",
                    'upgrade_url' => route('subscription.upgrade'),
                ], 403);
            }

            return redirect()->route('subscription.upgrade')
                ->with('info', "Fitur ini memerlukan paket premium.");
        }

        return $next($request);
    }

    /**
     * Check if user has access to specific feature.
     */
    protected function hasFeatureAccess($user, string $feature): bool
    {
        $featureTiers = [
            'api_access' => ['pro', 'enterprise'],
            'bulk_export' => ['pro', 'enterprise'],
            'advanced_analytics' => ['enterprise'],
            'multiple_classes' => ['pro', 'enterprise'],
            'custom_branding' => ['enterprise'],
        ];

        $requiredTiers = $featureTiers[$feature] ?? [];

        if (empty($requiredTiers)) {
            return true;
        }

        return in_array($user->tier, $requiredTiers);
    }
}
