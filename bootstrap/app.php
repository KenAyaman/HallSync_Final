<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureActiveAccount;
use App\Http\Middleware\EnsurePasswordChanged;
use App\Http\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Trust Render's (or any) reverse proxy so Laravel correctly reads the
        // X-Forwarded-Proto header and knows the original request was HTTPS,
        // even though the proxy forwards to this app over plain HTTP internally.
        // Without this, url()/route() generate http:// links even on a secure
        // site, which triggers "this form is not secure" warnings in browsers.
        $middleware->trustProxies(at: '*');

        $middleware->appendToGroup('web', [
            EnsureActiveAccount::class,
        ]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'password.changed' => EnsurePasswordChanged::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();