<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;


/**
 * Middleware to check multiple permissions (user must have ALL permissions)
 */
class RequireAllPermissions
{
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Account deactivated'], 403);
            }
            
            return redirect()->route('login')->with('error', 'Your account has been deactivated.');
        }

        // Check if user has ALL required permissions
        foreach ($permissions as $permission) {
            if (!$user->can($permission)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'Forbidden',
                        'message' => "Missing permission: {$permission}"
                    ], 403);
                }
                
                abort(403, "You do not have the required permission: {$permission}");
            }
        }

        return $next($request);
    }
}
