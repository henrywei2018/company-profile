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
        // Image processing service provider
        \Intervention\Image\Laravel\ServiceProvider::class,
        
        // Repository pattern services
        App\Providers\RepositoryServiceProvider::class,
        
        // Client access management services
        App\Providers\ClientServiceProvider::class,
        
        // Enhanced authentication services
        App\Providers\AuthenticationServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        // Global middleware stack
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        // API middleware enhancements
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        // Middleware aliases for route-level application
        $middleware->alias([
            // Existing RBAC middleware
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'rbac' => \App\Http\Middleware\RoleBasedAccessControl::class,
            'require.all.permissions' => \App\Http\Middleware\RequireAllPermissions::class,
            'require.any.permission' => \App\Http\Middleware\RequireAnyPermission::class,
            'require.role' => \App\Http\Middleware\RequireRole::class,
            
            // Enhanced client middleware
            'client' => \App\Http\Middleware\ClientMiddleware::class,
            'client.resource' => \App\Http\Middleware\ClientResourceAccess::class,
            'client.verified' => \App\Http\Middleware\EnsureClientIsVerified::class,
            'client.active' => \App\Http\Middleware\EnsureUserIsActive::class,
            
            // Authentication flow middleware
            'redirect.authenticated' => \App\Http\Middleware\RedirectAuthenticatedUsers::class,
            'guest.redirect' => \App\Http\Middleware\RedirectAuthenticatedUsers::class,
            
            // API and rate limiting
            'api.client' => \App\Http\Middleware\ClientApiRateLimit::class,
            'throttle.client' => \App\Http\Middleware\ClientApiRateLimit::class,
            
            // Security middleware
            'secure.headers' => \App\Http\Middleware\SecurityHeaders::class,
            'activity.log' => \App\Http\Middleware\LogUserActivity::class,
        ]);

        // Middleware groups for common combinations
        $middleware->group('client-web', [
            'web',
            'auth',
            'verified',
            'client.active',
            'client',
            'activity.log',
        ]);

        $middleware->group('client-api', [
            'api',
            'auth:sanctum',
            'client.active',
            'client',
            'throttle.client:100,1',
        ]);

        $middleware->group('admin-secure', [
            'web',
            'auth',
            'verified',
            'client.active',
            'admin',
            'secure.headers',
            'activity.log',
        ]);

        // Rate limiting configurations
        $middleware->throttle([
            'api' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            'client-api' => \App\Http\Middleware\ClientApiRateLimit::class.':100,1',
            'admin-api' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':120,1',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Custom exception handling for client area
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Forbidden',
                    'message' => $e->getMessage(),
                    'code' => 403
                ], 403);
            }
            
            // Redirect unauthorized client access to appropriate page
            if ($request->is('client/*')) {
                return redirect()->route('client.dashboard')
                    ->with('error', 'You do not have permission to access that resource.');
            }
            
            if ($request->is('admin/*')) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'You do not have permission to access that resource.');
            }
            
            return redirect()->route('home')
                ->with('error', 'You do not have permission to access that resource.');
        });

        // Handle authentication exceptions
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Unauthenticated',
                    'message' => 'Please log in to access this resource.',
                    'redirect_url' => route('login')
                ], 401);
            }
            
            // Store intended URL for proper redirect after login
            if (!$request->is('login', 'register', 'password/*')) {
                session(['url.intended' => $request->url()]);
            }
            
            return redirect()->route('login')
                ->with('info', 'Please log in to access that resource.');
        });

        // Handle model not found exceptions for client resources
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
            
            // Default handling for other routes
            return null;
        });

        // Handle validation exceptions with enhanced client context
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Validation Failed',
                    'message' => 'The given data was invalid.',
                    'errors' => $e->errors(),
                    'context' => $request->is('client/*') ? 'client' : ($request->is('admin/*') ? 'admin' : 'public')
                ], 422);
            }
            
            return null; // Use default handling
        });

        // Rate limiting exception handling
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

        // Log all exceptions for monitoring
        $exceptions->reportable(function (Throwable $e) {
            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
            
            // Enhanced logging for client-related exceptions
            if (request()->is('client/*') && auth()->check()) {
                \Log::error('Client area exception', [
                    'user_id' => auth()->id(),
                    'email' => auth()->user()->email,
                    'url' => request()->url(),
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        });
    })
    ->create();