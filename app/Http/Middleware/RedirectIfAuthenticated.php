<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * RedirectIfAuthenticated - Works with your RBAC system
 */
class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ?string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                
                // Check if account is active (using your existing RBAC)
                if (!$user->is_active) {
                    Auth::guard($guard)->logout();
                    return redirect()->route('login')
                        ->with('error', 'Your account has been deactivated.');
                }
                
                // Use your existing RBAC role hierarchy for redirects
                if ($user->hasAnyRole(['super-admin', 'admin', 'manager', 'editor'])) {
                    return redirect()->route('admin.dashboard');
                }
                
                if ($user->hasRole('client')) {
                    return redirect()->route('client.dashboard');
                }
                
                // Fallback for users without roles
                return redirect()->route('client.dashboard');
            }
        }

        return $next($request);
    }
}