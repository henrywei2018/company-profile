<?php

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
    ->withProviders([
        \Intervention\Image\Laravel\ServiceProvider::class,
        App\Providers\RepositoryServiceProvider::class,
        App\Providers\ClientServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        // MINIMAL FIX: Add missing core Laravel middleware aliases for your RBAC system
        $middleware->alias([
            // Core Laravel middleware (THESE WERE MISSING - causing your errors!)
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            
            // Your existing RBAC middleware (keep exactly as is)
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'client' => \App\Http\Middleware\ClientMiddleware::class,
            'rbac' => \App\Http\Middleware\RoleBasedAccessControl::class,
            'client.resource' => \App\Http\Middleware\ClientResourceAccess::class,
            'client.verified' => \App\Http\Middleware\EnsureClientIsVerified::class,
            'client.active' => \App\Http\Middleware\EnsureUserIsActive::class,
            'client.session' => \App\Http\Middleware\ClientSessionManagement::class,
            'activity.log' => \App\Http\Middleware\LogUserActivity::class,
            'secure.headers' => \App\Http\Middleware\SecurityHeaders::class,
        ]);

        // Keep your existing middleware groups if you have them
        $middleware->group('admin-auth', [
            'web',
            'auth',
            'verified', 
            'client.active',
            'admin',
            'secure.headers',
            'activity.log',
        ]);

        $middleware->group('client-auth', [
            'web',
            'auth',
            'verified',
            'client.active', 
            'client',
            'client.session',
            'activity.log',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Keep your existing exception handling for RBAC
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Unauthenticated',
                    'message' => 'Please log in to access this resource.'
                ], 401);
            }

            return redirect()->route('login');
        });

        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Forbidden', 
                    'message' => $e->getMessage()
                ], 403);
            }

            // RBAC-aware redirects
            if (auth()->check()) {
                $user = auth()->user();
                if ($request->is('admin/*') && !$user->hasAnyRole(['super-admin', 'admin', 'manager', 'editor'])) {
                    return redirect()->route('client.dashboard')
                        ->with('error', 'You do not have admin access.');
                }
                if ($request->is('client/*') && !$user->hasRole('client') && !$user->hasAnyRole(['super-admin', 'admin'])) {
                    return redirect()->route('admin.dashboard')
                        ->with('error', 'You do not have client access.');
                }
            }

            return redirect()->back()->with('error', 'Access denied.');
        });
    })
    ->create();