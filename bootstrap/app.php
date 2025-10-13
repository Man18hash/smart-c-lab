<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Configuration\Exceptions;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',          // ← enables /api routes
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Route middleware aliases (usable in routes via ->middleware('alias'))
        $middleware->alias([
            'role'    => \App\Http\Middleware\RoleMiddleware::class,
            'api.key' => \App\Http\Middleware\ApiKeyMiddleware::class, // ← your API key checker
        ]);

        // (Optional) Examples if you want to customize stacks:
        // $middleware->group('api', ['throttle:api', \Illuminate\Routing\Middleware\SubstituteBindings::class]);
        // $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);
        // $middleware->prepend(\App\Http\Middleware\TrustProxies::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Customize exception handling if needed
    })
    ->create();
