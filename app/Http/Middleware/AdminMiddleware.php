<?php
// app/Http/Middleware/AdminMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Check if user has admin access (super-admin, admin, or editor roles)
        if (!$user->hasAnyRole(['super-admin', 'admin', 'editor'])) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        // Check if user has permission to view admin dashboard
        if (!$user->can('view admin-dashboard')) {
            abort(403, 'Access denied. Insufficient permissions.');
        }

        return $next($request);
    }
}