<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\UserActivityService;
use App\Services\ClientAccessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    protected UserActivityService $activityService;
    protected ClientAccessService $clientAccessService;

    public function __construct(
        UserActivityService $activityService,
        ClientAccessService $clientAccessService
    ) {
        $this->activityService = $activityService;
        $this->clientAccessService = $clientAccessService;
    }

    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Attempt authentication
        $request->authenticate();

        // Regenerate session to prevent session fixation
        $request->session()->regenerate();

        // Get the authenticated user
        $user = Auth::user();

        // Perform post-authentication checks and actions
        $checkResult = $this->performPostAuthenticationChecks($user, $request);
        
        if ($checkResult !== true) {
            return $checkResult; // Return redirect response if checks failed
        }

        // Log successful authentication
        $this->logSuccessfulAuthentication($user, $request);

        // Determine redirect URL based on user role and intended URL
        $redirectUrl = $this->determineRedirectUrl($user, $request);

        // Clear any client cache if this is a client
        if ($user->hasRole('client')) {
            $this->clientAccessService->clearClientCache($user);
        }

        return redirect()->intended($redirectUrl);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Log logout activity before destroying session
        if ($user) {
            $this->activityService->logActivity($user, 'logout', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Log::info('User logged out', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
            ]);
        }

        // Perform logout
        Auth::guard('web')->logout();

        // Invalidate session and regenerate CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Perform post-authentication security checks.
     */
    protected function performPostAuthenticationChecks($user, Request $request)
    {
        // Check if user account is active
        if (!$user->is_active) {
            Auth::logout();
            
            Log::warning('Inactive user attempted login', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
            ]);
            
            return redirect()->route('login')
                ->with('error', 'Your account has been deactivated. Please contact the administrator.');
        }

        // Check if account is locked
        if (property_exists($user, 'locked_at') && $user->locked_at && $user->locked_at->isFuture()) {
            Auth::logout();
            
            Log::warning('Locked user attempted login', [
                'user_id' => $user->id,
                'email' => $user->email,
                'locked_until' => $user->locked_at->toDateTimeString(),
                'ip' => $request->ip(),
            ]);
            
            return redirect()->route('login')
                ->with('error', 'Your account is temporarily locked. Please try again later or contact support.');
        }

        // Update user login statistics
        $this->updateUserLoginStats($user, $request);

        return true; // All checks passed
    }

    /**
     * Update user login statistics.
     */
    protected function updateUserLoginStats($user, Request $request): void
    {
        $updateData = [
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ];

        // Reset failed login attempts on successful login
        if (property_exists($user, 'failed_login_attempts') && $user->failed_login_attempts > 0) {
            $updateData['failed_login_attempts'] = 0;
            $updateData['locked_at'] = null;
        }

        // Increment login count
        if (property_exists($user, 'login_count')) {
            $updateData['login_count'] = ($user->login_count ?? 0) + 1;
        }

        $user->update($updateData);
    }

    /**
     * Log successful authentication.
     */
    protected function logSuccessfulAuthentication($user, Request $request): void
    {
        $this->activityService->logActivity($user, 'login', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => $request->session()->getId(),
        ]);

        Log::info('User authenticated successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
            'roles' => $user->roles->pluck('name')->toArray(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    /**
     * Determine redirect URL based on user role and context.
     */
    protected function determineRedirectUrl($user, Request $request): string
    {
        // Check if there's an intended URL from before login
        $intendedUrl = session('url.intended');
        
        // If intended URL exists and is safe, use it
        if ($intendedUrl && $this->isSafeRedirectUrl($intendedUrl, $user)) {
            return $intendedUrl;
        }

        // Role-based redirect hierarchy (highest priority first)
        $roleRedirects = [
            'super-admin' => 'admin.dashboard',
            'admin' => 'admin.dashboard',
            'manager' => 'admin.dashboard', 
            'editor' => 'admin.dashboard',
            'client' => 'client.dashboard',
        ];

        // Check user roles in priority order
        foreach ($roleRedirects as $role => $route) {
            if ($user->hasRole($role)) {
                return route($route);
            }
        }

        // Check for specific permissions if no role match
        if ($user->can('view dashboard') || $user->can('access admin')) {
            return route('admin.dashboard');
        }

        // Final fallback
        return route('client.dashboard');
    }

    /**
     * Check if redirect URL is safe for the user's role.
     */
    protected function isSafeRedirectUrl(string $url, $user): bool
    {
        // Parse the URL to get the path
        $path = parse_url($url, PHP_URL_PATH);
        
        if (!$path) {
            return false;
        }

        // Admin area access check
        if (str_starts_with($path, '/admin')) {
            return $user->hasAnyRole(['super-admin', 'admin', 'manager', 'editor']);
        }

        // Client area access check  
        if (str_starts_with($path, '/client')) {
            return $user->hasRole('client') || $user->hasAnyRole(['super-admin', 'admin']);
        }

        // Public URLs are generally safe
        $publicPaths = ['/dashboard', '/home', '/profile', '/settings'];
        foreach ($publicPaths as $publicPath) {
            if (str_starts_with($path, $publicPath)) {
                return true;
            }
        }

        // Block potentially unsafe redirects
        $unsafePaths = ['/login', '/register', '/logout', '/password'];
        foreach ($unsafePaths as $unsafePath) {
            if (str_starts_with($path, $unsafePath)) {
                return false;
            }
        }

        return true; // Allow other URLs by default
    }

    /**
     * Handle failed authentication attempt.
     */
    public function handleFailedAttempt(Request $request): void
    {
        $email = $request->input('email');
        
        if ($email) {
            $user = \App\Models\User::where('email', $email)->first();
            
            if ($user) {
                // Increment failed login attempts
                $failedAttempts = ($user->failed_login_attempts ?? 0) + 1;
                $updateData = ['failed_login_attempts' => $failedAttempts];
                
                // Lock account after 5 failed attempts
                if ($failedAttempts >= 5) {
                    $updateData['locked_at'] = now()->addMinutes(30);
                    
                    Log::warning('User account locked due to failed login attempts', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'failed_attempts' => $failedAttempts,
                        'locked_until' => now()->addMinutes(30)->toDateTimeString(),
                        'ip' => $request->ip(),
                    ]);
                }
                
                $user->update($updateData);
            }
        }

        // Log failed login attempt
        $this->activityService->logFailedLogin([
            'email' => $email ?? 'unknown',
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    /**
     * Check session status (AJAX endpoint).
     */
    public function checkSession(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'authenticated' => false,
                'redirect_url' => route('login')
            ], 401);
        }

        $user = Auth::user();
        
        // Check if user is still active
        if (!$user->is_active) {
            Auth::logout();
            
            return response()->json([
                'authenticated' => false,
                'message' => 'Account deactivated',
                'redirect_url' => route('login')
            ], 401);
        }

        // Check session timeout
        $lastActivity = session('last_activity_time', time());
        $sessionLifetime = config('session.lifetime', 120) * 60;
        $timeRemaining = $sessionLifetime - (time() - $lastActivity);
        
        return response()->json([
            'authenticated' => true,
            'user' => $user->only(['id', 'name', 'email']),
            'roles' => $user->roles->pluck('name'),
            'session_remaining' => max(0, $timeRemaining),
            'session_warning' => $timeRemaining <= 300, // 5 minutes warning
        ]);
    }

    /**
     * Extend user session (AJAX endpoint).
     */
    public function extendSession(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        session(['last_activity_time' => time()]);
        
        Log::info('User session extended', [
            'user_id' => Auth::id(),
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Session extended'
        ]);
    }

    /**
     * Get authentication status with user context.
     */
    public function status(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'authenticated' => false
            ]);
        }

        $user = Auth::user();
        
        return response()->json([
            'authenticated' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar_url' => $user->avatar_url ?? null,
                'is_active' => $user->is_active,
                'email_verified' => !is_null($user->email_verified_at),
                'last_login_at' => $user->last_login_at?->format('Y-m-d H:i:s'),
            ],
            'roles' => $user->roles->pluck('name'),
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'access' => [
                'can_access_admin' => $user->hasAnyRole(['super-admin', 'admin', 'manager', 'editor']),
                'can_access_client' => $user->hasRole('client') || $user->hasAnyRole(['super-admin', 'admin']),
                'primary_role' => $user->primary_role ?? null,
                'dashboard_url' => $this->determineRedirectUrl($user, $request),
            ],
            'session' => [
                'id' => $request->session()->getId(),
                'csrf_token' => csrf_token(),
            ]
        ]);
    }
}