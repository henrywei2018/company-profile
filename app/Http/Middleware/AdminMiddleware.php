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
        if (!$user->hasAnyRole(['super-admin', 'admin', 'manager', 'editor'])) {
            // bisa redirect atau abort di sini
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Forbidden',
                    'message' => 'Admin access required.'
                ], 403);
            }
            if ($user->hasRole('client')) {
                return redirect()->route('client.dashboard')->with('warning', 'You do not have admin access.');
            }
            abort(403, 'Admin access required.');
        }


        return $next($request);
    }
}