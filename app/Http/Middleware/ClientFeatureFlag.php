<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class ClientFeatureFlag
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        if (!$this->isFeatureEnabled($feature)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Feature Not Available',
                    'message' => 'This feature is currently not available.'
                ], 404);
            }
            
            return redirect()->route('client.dashboard')
                ->with('info', 'This feature is currently not available.');
        }

        return $next($request);
    }

    /**
     * Check if feature is enabled.
     */
    protected function isFeatureEnabled(string $feature): bool
    {
        // Check global feature flags
        $globalFlags = config('features', []);
        if (isset($globalFlags[$feature])) {
            return $globalFlags[$feature];
        }

        // Check user-specific feature flags
        if (auth()->check()) {
            $user = auth()->user();
            $userFlags = $user->feature_flags ?? [];
            if (isset($userFlags[$feature])) {
                return $userFlags[$feature];
            }
        }

        // Check database feature flags
        return \DB::table('feature_flags')
            ->where('name', $feature)
            ->where('is_enabled', true)
            ->exists();
    }
}