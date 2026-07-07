<?php

use App\Http\Middleware\CheckSubscription;
use App\Http\Middleware\EnsureTenantOwnership;
use App\Http\Middleware\LogActivity;
use App\Http\Middleware\ValidateApiToken;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register middleware aliases
        $middleware->alias([
            'tenant' => EnsureTenantOwnership::class,
            'subscription' => CheckSubscription::class,
            'api.token' => ValidateApiToken::class,
            'activity' => LogActivity::class,
        ]);

        // Add to web middleware group
        $middleware->web(append: [
            LogActivity::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
