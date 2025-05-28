<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


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