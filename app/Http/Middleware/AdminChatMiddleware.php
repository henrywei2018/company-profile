<?php
// app/Http/Middleware/AdminChatMiddleware.php - Middleware khusus untuk admin chat

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminChatMiddleware
{
    /**
     * Handle an incoming request untuk admin chat dengan dual auth support
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Coba web session auth dulu
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            
            if ($this->hasAdminAccess($user)) {
                return $next($request);
            }
        }

        // Jika web auth gagal, coba sanctum
        if (Auth::guard('sanctum')->check()) {
            $user = Auth::guard('sanctum')->user();
            
            if ($this->hasAdminAccess($user)) {
                return $next($request);
            }
        }

        // Jika semua gagal, return unauthorized
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Unauthenticated',
                'message' => 'Admin access required for chat management.'
            ], 401);
        }

        return redirect()->route('login')->with('error', 'Admin access required.');
    }

    private function hasAdminAccess($user): bool
    {
        if (!$user || !$user->is_active) {
            return false;
        }

        return $user->hasAnyRole(['super-admin', 'admin', 'manager', 'editor']) 
            || $user->can('manage chat');
    }
}