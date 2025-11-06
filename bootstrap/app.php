<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // ✅ Enable API routes
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        /**
         * -----------------------------------------------------
         * Global & Group Middleware Configuration (Laravel 12)
         * -----------------------------------------------------
         */

        // ✅ Register your API middleware stack
        $middleware->api(prepend: [
            \App\Http\Middleware\ValidateSSOToken::class, // custom Passport/SSO middleware
        ]);

        // ✅ You can also add global middleware here if needed:
        // $middleware->append([
        //     \Illuminate\Http\Middleware\TrustProxies::class,
        //     \Illuminate\Http\Middleware\HandleCors::class,
        // ]);

            // 👇 Register CheckScopes as a named middleware
        $middleware->alias([
            'scopes' => \App\Http\Middleware\CheckScopes::class,
            'gateway.proxy' => \App\Http\Middleware\GatewayProxy::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // You can customize exception rendering/logging here if needed
    })
    ->create();

