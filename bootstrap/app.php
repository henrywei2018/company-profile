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
        App\Providers\AuthenticationServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        // Fix: Add missing core Laravel middleware aliases
        $middleware->alias([
            // Core Laravel Auth Middleware (MISSING - This was the issue!)
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'auth.session' => \Illuminate\Auth\Middleware\AuthenticateSession::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            
            // Custom Admin & RBAC Middleware
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'rbac' => \App\Http\Middleware\RoleBasedAccessControl::class,
            'require.all.permissions' => \App\Http\Middleware\RequireAllPermissions::class,
            'require.any.permission' => \App\Http\Middleware\RequireAnyPermission::class,
            'require.role' => \App\Http\Middleware\RequireRole::class,
            
            // Client Area Middleware
            'client' => \App\Http\Middleware\ClientMiddleware::class,
            'client.resource' => \App\Http\Middleware\ClientResourceAccess::class,
            'client.verified' => \App\Http\Middleware\EnsureClientIsVerified::class,
            'client.active' => \App\Http\Middleware\EnsureUserIsActive::class,
            'client.session' => \App\Http\Middleware\ClientSessionManagement::class,
            'client.csrf' => \App\Http\Middleware\ClientCsrfProtection::class,
            'client.locale' => \App\Http\Middleware\ClientLocale::class,
            'client.maintenance' => \App\Http\Middleware\ClientMaintenanceMode::class,
            'client.feature' => \App\Http\Middleware\ClientFeatureFlag::class,
            'client.validate' => \App\Http\Middleware\ValidateClientRequest::class,
            
            // Authentication Flow Middleware
            'redirect.authenticated' => \App\Http\Middleware\RedirectAuthenticatedUsers::class,
            'guest.redirect' => \App\Http\Middleware\RedirectAuthenticatedUsers::class,
            
            // API & Security Middleware
            'api.client' => \App\Http\Middleware\ClientApiRateLimit::class,
            'throttle.client' => \App\Http\Middleware\ClientApiRateLimit::class,
            'secure.headers' => \App\Http\Middleware\SecurityHeaders::class,
            'activity.log' => \App\Http\Middleware\LogUserActivity::class,
        ]);

        // Middleware Groups for different authentication flows
        $middleware->group('web-auth', [
            'web',
            'auth',
            'verified',
            'client.active',
        ]);

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

        $middleware->group('client-api-auth', [
            'api',
            'auth:sanctum',
            'client.active',
            'client',
            'throttle.client:100,1',
        ]);

        $middleware->group('guest-only', [
            'web',
            'guest',
            'throttle:6,1',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Authentication Exception Handling
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Unauthenticated',
                    'message' => 'Please log in to access this resource.',
                    'redirect_url' => route('login')
                ], 401);
            }

            // Store intended URL for post-login redirect
            if (!$request->is('login', 'register', 'password/*')) {
                session(['url.intended' => $request->url()]);
            }

            return redirect()->route('login')
                ->with('info', 'Please log in to access that resource.');
        });

        // Authorization Exception Handling
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Forbidden',
                    'message' => $e->getMessage(),
                    'code' => 403
                ], 403);
            }

            // Role-based redirects for authorization errors
            if (auth()->check()) {
                $user = auth()->user();
                
                if ($request->is('client/*') && !$user->hasRole('client')) {
                    return redirect()->route('admin.dashboard')
                        ->with('error', 'You do not have client area access.');
                }
                
                if ($request->is('admin/*') && !$user->hasAnyRole(['super-admin', 'admin', 'manager', 'editor'])) {
                    return redirect()->route('client.dashboard')
                        ->with('error', 'You do not have admin area access.');
                }
            }

            return redirect()->back()
                ->with('error', $e->getMessage() ?: 'You do not have permission to access that resource.');
        });

        // Model Not Found Exception (for client resource access)
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            if ($request->is('client/*')) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'Not Found',
                        'message' => 'The requested resource was not found or you do not have access to it.'
                    ], 404);
                }

                return redirect()->route('client.dashboard')
                    ->with('error', 'The requested resource was not found or you do not have access to it.');
            }

            if ($request->is('admin/*')) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'The requested resource was not found.');
            }

            return null; // Let Laravel handle other cases
        });

        // Validation Exception Handling
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Validation Failed',
                    'message' => 'The given data was invalid.',
                    'errors' => $e->errors(),
                    'context' => $request->is('client/*') ? 'client' : ($request->is('admin/*') ? 'admin' : 'public')
                ], 422);
            }

            return null; // Let Laravel handle form validation redirects
        });

        // Rate Limiting Exception
        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Too Many Requests',
                    'message' => 'Rate limit exceeded. Please try again later.',
                    'retry_after' => $e->getHeaders()['Retry-After'] ?? 60
                ], 429);
            }

            return redirect()->back()
                ->with('error', 'Too many requests. Please wait a moment before trying again.');
        });

        // Global Exception Reporting
        $exceptions->reportable(function (Throwable $e) {
            // Log client area exceptions with user context
            if (request()->is('client/*') && auth()->check()) {
                \Log::error('Client area exception', [
                    'user_id' => auth()->id(),
                    'email' => auth()->user()->email ?? 'unknown',
                    'url' => request()->url(),
                    'method' => request()->method(),
                    'exception' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
            }

            // Log admin area exceptions with elevated context
            if (request()->is('admin/*') && auth()->check()) {
                \Log::error('Admin area exception', [
                    'user_id' => auth()->id(),
                    'email' => auth()->user()->email ?? 'unknown',
                    'roles' => auth()->user()->roles->pluck('name')->toArray() ?? [],
                    'url' => request()->url(),
                    'method' => request()->method(),
                    'exception' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
            }

            // Send to external error tracking (Sentry, etc.)
            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        });
    })
    ->create();