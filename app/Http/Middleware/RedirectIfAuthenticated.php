<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirect If Authenticated Middleware
 * 
 * This middleware redirects authenticated users away from guest-only routes
 * (like login, register) to their appropriate dashboard based on their role.
 */
class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  ...$guards
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ?string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                
                // Check if user account is active
                if (!$user->is_active) {
                    Auth::guard($guard)->logout();
                    
                    return redirect()->route('login')
                        ->with('error', 'Your account has been deactivated. Please contact support.');
                }
                
                // Redirect based on user role hierarchy
                $redirectUrl = $this->getRedirectUrlByRole($user);
                
                return redirect($redirectUrl);
            }
        }

        return $next($request);
    }

    /**
     * Get redirect URL based on user's role hierarchy.
     * 
     * @param  \App\Models\User  $user
     * @return string
     */
    private function getRedirectUrlByRole($user): string
    {
        // Role hierarchy (highest to lowest priority)
        $roleRedirects = [
            'super-admin' => 'admin.dashboard',
            'admin' => 'admin.dashboard', 
            'manager' => 'admin.dashboard',
            'editor' => 'admin.dashboard',
            'client' => 'client.dashboard',
        ];

        // Check roles in order of hierarchy
        foreach ($roleRedirects as $role => $route) {
            if ($user->hasRole($role)) {
                return route($route);
            }
        }

        // Check for specific permissions if no role match
        if ($user->can('view dashboard') || $user->can('access admin')) {
            return route('admin.dashboard');
        }

        // Default fallback for users without specific roles
        return route('client.dashboard');
    }
}