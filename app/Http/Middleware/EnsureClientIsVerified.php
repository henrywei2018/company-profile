<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClientIsVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if ($user && $user->hasRole('client') && property_exists($user, 'is_verified') && !$user->is_verified) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Account verification required',
                    'message' => 'Your client account requires verification by an administrator.',
                    'verification_status' => 'pending'
                ], 403);
            }
            
            // Allow access to dashboard but show verification notice
            if (!$request->routeIs('client.dashboard')) {
                return redirect()->route('client.dashboard')
                    ->with('warning', 'Your account is pending verification by an administrator. Some features may be limited.');
            }
        }

        return $next($request);
    }
}