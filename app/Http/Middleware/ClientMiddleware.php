<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must login to access this area.');
        }
        
        // Check if user has client role
        if (!Auth::user()->hasRole('client')) {
            abort(403, 'Unauthorized action. You do not have the necessary permissions.');
        }
        
        // Check if user is active
        if (!Auth::user()->is_active) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account has been deactivated. Please contact the administrator.');
        }
        
        // Check if email is verified (if email verification is required)
        if (config('auth.verify_email') && !Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }
        
        return $next($request);
    }
}