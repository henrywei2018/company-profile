<?php
// File: app/Http/Middleware/ChatOperatorMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ChatOperatorMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Authentication required'], 401);
            }
            return redirect()->route('login');
        }

        // Check if user has admin access for chat operations
        if (!auth()->user()->hasAdminAccess()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Insufficient permissions'], 403);
            }
            abort(403, 'Access denied. Admin privileges required for chat operations.');
        }

        return $next($request);
    }
}