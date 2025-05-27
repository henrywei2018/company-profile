<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !auth()->user()->is_active) {
            auth()->logout();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Account deactivated',
                    'message' => 'Your account has been deactivated. Please contact support.'
                ], 403);
            }
            
            return redirect()->route('login')
                ->with('error', 'Your account has been deactivated. Please contact support.');
        }

        return $next($request);
    }
}