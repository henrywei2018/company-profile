<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientResourceAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $resourceType
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $resourceType): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Admin users can access all resources
        if ($user->hasAnyRole(['super-admin', 'admin', 'manager'])) {
            return $next($request);
        }

        // Get the resource ID from route parameters
        $resourceId = $this->getResourceIdFromRequest($request, $resourceType);
        
        if ($resourceId && !$this->canAccessResource($user, $resourceType, $resourceId)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Forbidden',
                    'message' => 'You can only access your own resources.'
                ], 403);
            }
            
            abort(403, 'You can only access your own resources.');
        }

        return $next($request);
    }

    /**
     * Get resource ID from request parameters.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $resourceType
     * @return mixed
     */
    private function getResourceIdFromRequest(Request $request, string $resourceType)
    {
        return $request->route($resourceType) ?? $request->route('id');
    }

    /**
     * Check if user can access the specific resource.
     * 
     * @param  \App\Models\User  $user
     * @param  string  $resourceType
     * @param  mixed  $resourceId
     * @return bool
     */
    private function canAccessResource($user, string $resourceType, $resourceId): bool
    {
        switch ($resourceType) {
            case 'project':
                return \App\Models\Project::where('id', $resourceId)
                    ->where('client_id', $user->id)
                    ->exists();
                    
            case 'quotation':
                return \App\Models\Quotation::where('id', $resourceId)
                    ->where('client_id', $user->id)
                    ->exists();
                    
            case 'message':
                return \App\Models\Message::where('id', $resourceId)
                    ->where('client_id', $user->id)
                    ->exists();
                    
            case 'testimonial':
                return \App\Models\Testimonial::where('id', $resourceId)
                    ->where('client_id', $user->id)
                    ->exists();
                    
            default:
                return false;
        }
    }
}