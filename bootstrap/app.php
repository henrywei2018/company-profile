<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('notifications:send-scheduled')
                ->everyFiveMinutes()
                ->withoutOverlapping();

        $schedule->command('notifications:cleanup')
                ->daily()
                ->at('02:00');
    })
    ->withProviders([
        App\Providers\RepositoryServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        // Simple middleware aliases - only what we need
        $middleware->alias([
            // Core Laravel middleware
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            
            // Simple RBAC middleware
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'client' => \App\Http\Middleware\ClientMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Simple exception handling
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }
            return redirect()->route('login');
        });

        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Forbidden'], 403);
            }
            
            if (auth()->check()) {
                $user = auth()->user();
                if ($request->is('admin/*') && !$user->hasAnyRole(['super-admin', 'admin', 'manager', 'editor'])) {
                    return redirect()->route('client.dashboard')->with('error', 'Access denied.');
                }
                if ($request->is('client/*') && !$user->hasRole('client') && !$user->hasAnyRole(['super-admin', 'admin'])) {
                    return redirect()->route('admin.dashboard')->with('error', 'Access denied.');
                }
            }
            
            return redirect()->back()->with('error', 'Access denied.');
        });
    })
    ->create();