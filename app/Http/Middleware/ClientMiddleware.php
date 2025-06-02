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