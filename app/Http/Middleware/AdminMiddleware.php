<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Enhanced Admin Middleware with proper RBAC
 */
class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user account is active
        if (!$user->is_active) {
            Auth::logout();
            
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Account deactivated'], 403);
            }
            
            return redirect()->route('login')->with('error', 'Your account has been deactivated.');
        }

        // Check if user has admin roles or dashboard access permission
        if (!$user->hasAnyRole(['super-admin', 'admin', 'manager', 'editor']) && !$user->can('view dashboard')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Forbidden',
                    'message' => 'Admin access required.'
                ], 403);
            }
            
            // Redirect client users to their area
            if ($user->hasRole('client')) {
                return redirect()->route('client.dashboard')->with('warning', 'You do not have admin access.');
            }
            
            abort(403, 'Admin access required.');
        }

        return $next($request);
    }
}

/**
 * Client Middleware for client area access
 */
class ClientMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user account is active
        if (!$user->is_active) {
            Auth::logout();
            
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Account deactivated'], 403);
            }
            
            return redirect()->route('login')->with('error', 'Your account has been deactivated.');
        }

        // Check if user has client role or is an admin (admins can access client area)
        if (!$user->hasRole('client') && !$user->hasAnyRole(['super-admin', 'admin', 'manager', 'editor'])) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Forbidden',
                    'message' => 'Client access required.'
                ], 403);
            }
            
            abort(403, 'You do not have access to the client area.');
        }

        return $next($request);
    }
}

/**
 * Redirect Authenticated Users Middleware
 * Redirects already authenticated users to appropriate dashboard
 */
class RedirectAuthenticatedUsers
{
    public function handle(Request $request, Closure $next, ?string $guard = null): Response
    {
        $guards = $guard ? [$guard] : array_keys(config('auth.guards'));

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                
                // Redirect based on role
                if ($user->hasAnyRole(['super-admin', 'admin', 'manager', 'editor'])) {
                    return redirect()->route('admin.dashboard');
                } elseif ($user->hasRole('client')) {
                    return redirect()->route('client.dashboard');
                } else {
                    // Default redirect for users without specific roles
                    return redirect()->route('client.dashboard');
                }
            }
        }

        return $next($request);
    }
}