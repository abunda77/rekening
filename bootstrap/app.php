<?php

use App\Http\Middleware\LogActivity;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        // Record every web and API request for the Activity log.
        $middleware->web(append: [LogActivity::class]);
        $middleware->api(append: [LogActivity::class]);

        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('agent*')) {
                return route('agent.login');
            }

            return route('login');
        });

        $middleware->redirectUsersTo(function ($request) {
            if (request()->is('agent*') || Auth::guard('agent')->check()) {
                return route('agent.dashboard');
            }

            return route('dashboard');
        });

        // Spatie Laravel Permission middleware
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
