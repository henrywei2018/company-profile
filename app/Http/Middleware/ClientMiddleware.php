<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


/**
 * Client Middleware for client area access control
 * 
 * This middleware ensures that only authenticated users with client role 
 * or admin privileges can access client area routes.
 */
class ClientMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'Authentication required to access client area.'
                ], 401);
            }
            
            // Store intended URL for redirect after login
            session(['url.intended' => $request->url()]);
            
            return redirect()->route('login')
                ->with('info', 'Please log in to access the client area.');
        }

        $user = Auth::user();

        // Check if user account is active
        if (!$user->is_active) {
            Auth::logout();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Account deactivated',
                    'message' => 'Your account has been deactivated. Please contact the administrator.'
                ], 403);
            }
            
            return redirect()->route('login')
                ->with('error', 'Your account has been deactivated. Please contact the administrator.');
        }

        // Check email verification if required
        if (!$user->hasVerifiedEmail() && config('auth.verification.required', false)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Email verification required',
                    'message' => 'Please verify your email address to access the client area.'
                ], 403);
            }
            
            return redirect()->route('verification.notice')
                ->with('warning', 'Please verify your email address to access the client area.');
        }

        // Check if user has client role or admin privileges
        if (!$this->hasClientAccess($user)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Forbidden',
                    'message' => 'You do not have permission to access the client area.'
                ], 403);
            }
            
            // Log the unauthorized access attempt
            \Log::warning('Unauthorized client area access attempt', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
                'url' => $request->url(),
                'user_agent' => $request->userAgent(),
            ]);
            
            abort(403, 'You do not have permission to access the client area.');
        }

        // Set client context for the request
        $request->attributes->set('client_user', $user);
        $request->attributes->set('is_admin_viewing', $this->isAdminViewingClientArea($user));

        return $next($request);
    }

    /**
     * Check if user has client access privileges.
     * 
     * @param  \App\Models\User  $user
     * @return bool
     */
    private function hasClientAccess($user): bool
    {
        // Super admin and admin can access client area for support purposes
        if ($user->hasAnyRole(['super-admin', 'admin'])) {
            return true;
        }

        // Manager and editor roles can access client area if they have the permission
        if ($user->hasAnyRole(['manager', 'editor'])) {
            return $user->can('access client area') || $user->can('view client dashboard');
        }

        // Standard client role check
        if ($user->hasRole('client')) {
            return true;
        }

        // Check for specific client permissions (for custom roles)
        if ($user->can('access client area') || $user->can('view client dashboard')) {
            return true;
        }

        return false;
    }

    /**
     * Check if an admin user is viewing the client area.
     * 
     * @param  \App\Models\User  $user
     * @return bool
     */
    private function isAdminViewingClientArea($user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'manager', 'editor']);
    }
}

/**
 * Enhanced RedirectIfAuthenticated Middleware
 * 
 * This middleware redirects already authenticated users to their appropriate dashboard
 * based on their role when they try to access guest-only routes like login/register.
 */
class RedirectAuthenticatedUsers
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  $guard
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ?string $guard = null): Response
    {
        $guards = $guard ? [$guard] : array_keys(config('auth.guards'));

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                
                // Check if user account is active
                if (!$user->is_active) {
                    Auth::guard($guard)->logout();
                    continue;
                }
                
                // Redirect based on role hierarchy
                $redirectUrl = $this->getRedirectUrlByRole($user);
                
                return redirect($redirectUrl);
            }
        }

        return $next($request);
    }

    /**
     * Get redirect URL based on user's role.
     * 
     * @param  \App\Models\User  $user
     * @return string
     */
    private function getRedirectUrlByRole($user): string
    {
        // Check roles in order of hierarchy (highest to lowest)
        if ($user->hasRole('super-admin')) {
            return route('admin.dashboard');
        }

        if ($user->hasRole('admin')) {
            return route('admin.dashboard');
        }

        if ($user->hasRole('manager')) {
            return route('admin.dashboard');
        }

        if ($user->hasRole('editor')) {
            return route('admin.dashboard');
        }

        if ($user->hasRole('client')) {
            return route('client.dashboard');
        }

        // Default fallback - check if user has any admin permissions
        if ($user->can('view dashboard') || $user->can('access admin')) {
            return route('admin.dashboard');
        }

        // If no specific role, treat as client
        return route('client.dashboard');
    }
}

/**
 * Client Resource Access Middleware
 * 
 * This middleware ensures clients can only access their own resources
 * (projects, quotations, messages, etc.)
 */
class ClientResourceAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $resourceType
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $resourceType): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Admin users can access all resources
        if ($user->hasAnyRole(['super-admin', 'admin', 'manager'])) {
            return $next($request);
        }

        // Get the resource ID from route parameters
        $resourceId = $this->getResourceIdFromRequest($request, $resourceType);
        
        if ($resourceId && !$this->canAccessResource($user, $resourceType, $resourceId)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Forbidden',
                    'message' => 'You can only access your own resources.'
                ], 403);
            }
            
            abort(403, 'You can only access your own resources.');
        }

        return $next($request);
    }

    /**
     * Get resource ID from request parameters.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $resourceType
     * @return mixed
     */
    private function getResourceIdFromRequest(Request $request, string $resourceType)
    {
        return $request->route($resourceType) ?? $request->route('id');
    }

    /**
     * Check if user can access the specific resource.
     * 
     * @param  \App\Models\User  $user
     * @param  string  $resourceType
     * @param  mixed  $resourceId
     * @return bool
     */
    private function canAccessResource($user, string $resourceType, $resourceId): bool
    {
        switch ($resourceType) {
            case 'project':
                return \App\Models\Project::where('id', $resourceId)
                    ->where('client_id', $user->id)
                    ->exists();
                    
            case 'quotation':
                return \App\Models\Quotation::where('id', $resourceId)
                    ->where('client_id', $user->id)
                    ->exists();
                    
            case 'message':
                return \App\Models\Message::where('id', $resourceId)
                    ->where('client_id', $user->id)
                    ->exists();
                    
            case 'testimonial':
                return \App\Models\Testimonial::where('id', $resourceId)
                    ->where('client_id', $user->id)
                    ->exists();
                    
            default:
                return false;
        }
    }
}

/**
 * Client API Rate Limiting Middleware
 * 
 * This middleware provides enhanced rate limiting for client API endpoints
 */
class ClientApiRateLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  int  $maxAttempts
     * @param  int  $decayMinutes
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, int $maxAttempts = 60, int $decayMinutes = 1): Response
    {
        $key = $this->resolveRequestSignature($request);
        
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $retryAfter = \Illuminate\Support\Facades\RateLimiter::availableIn($key);
            
            return response()->json([
                'error' => 'Too Many Requests',
                'message' => 'Rate limit exceeded. Please try again later.',
                'retry_after' => $retryAfter
            ], 429);
        }

        \Illuminate\Support\Facades\RateLimiter::hit($key, $decayMinutes * 60);

        $response = $next($request);

        // Add rate limit headers
        $response->headers->add([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => max(0, $maxAttempts - \Illuminate\Support\Facades\RateLimiter::attempts($key)),
        ]);

        return $response;
    }

    /**
     * Resolve request signature for rate limiting.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function resolveRequestSignature(Request $request): string
    {
        if (Auth::check()) {
            return 'client_api:' . Auth::id();
        }

        return 'client_api:' . $request->ip();
    }
}