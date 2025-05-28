<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
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
        // Authenticate user
        $request->authenticate();

        // Regenerate session
        $request->session()->regenerate();

        // Get authenticated user
        $user = Auth::user();

        // Security checks
        if (!$this->performSecurityChecks($user, $request)) {
            return redirect()->route('login')
                ->with('error', 'Account access denied.');
        }

        // Update login stats
        $this->updateLoginStats($user, $request);

        // Log successful login
        $this->logSuccessfulLogin($user, $request);

        // Determine redirect URL
        $redirectUrl = $this->getRedirectUrl($user, $request);

        return redirect()->intended($redirectUrl);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Log logout
        if ($user) {
            Log::info('User logged out', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
            ]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Successfully logged out.');
    }

    /**
     * Perform security checks on authenticated user.
     */
    protected function performSecurityChecks($user, Request $request): bool
    {
        // Check if account is active
        if (!$user->is_active) {
            Auth::logout();
            Log::warning('Inactive user login attempt', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
            ]);
            return false;
        }

        // Check if account is locked
        if (property_exists($user, 'locked_at') && $user->locked_at && $user->locked_at->isFuture()) {
            Auth::logout();
            Log::warning('Locked user login attempt', [
                'user_id' => $user->id,
                'email' => $user->email,
                'locked_until' => $user->locked_at,
                'ip' => $request->ip(),
            ]);
            return false;
        }

        return true;
    }

    /**
     * Update user login statistics.
     */
    protected function updateLoginStats($user, Request $request): void
    {
        $updateData = [
            'last_login_at' => now(),
        ];

        // Reset failed attempts on successful login
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
    protected function logSuccessfulLogin($user, Request $request): void
    {
        Log::info('User authenticated successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
            'roles' => $user->roles->pluck('name')->toArray(),
            'ip' => $request->ip(),
        ]);
    }

    /**
     * Get redirect URL based on user role.
     */
    protected function getRedirectUrl($user, Request $request): string
    {
        // Check intended URL first
        $intendedUrl = session('url.intended');
        if ($intendedUrl && $this->isValidRedirectUrl($intendedUrl, $user)) {
            return $intendedUrl;
        }

        // Role-based redirect
        if ($user->hasAnyRole(['super-admin', 'admin', 'manager', 'editor'])) {
            return route('admin.dashboard');
        }

        if ($user->hasRole('client')) {
            return route('client.dashboard');
        }

        // Fallback based on permissions
        if ($user->can('view dashboard') || $user->can('access admin')) {
            return route('admin.dashboard');
        }

        return route('client.dashboard');
    }

    /**
     * Check if redirect URL is valid for user.
     */
    protected function isValidRedirectUrl(string $url, $user): bool
    {
        $path = parse_url($url, PHP_URL_PATH);
        
        if (!$path) return false;

        // Admin area check
        if (str_starts_with($path, '/admin')) {
            return $user->hasAnyRole(['super-admin', 'admin', 'manager', 'editor']);
        }

        // Client area check
        if (str_starts_with($path, '/client')) {
            return $user->hasRole('client') || $user->hasAnyRole(['super-admin', 'admin']);
        }

        // Block auth routes
        $blockedPaths = ['/login', '/register', '/logout'];
        foreach ($blockedPaths as $blockedPath) {
            if (str_starts_with($path, $blockedPath)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get authentication status (AJAX endpoint).
     */
    public function status(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['authenticated' => false]);
        }

        $user = Auth::user();

        return response()->json([
            'authenticated' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_active' => $user->is_active,
                'email_verified' => !is_null($user->email_verified_at),
            ],
            'roles' => $user->roles->pluck('name'),
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'access' => [
                'can_access_admin' => $user->hasAnyRole(['super-admin', 'admin', 'manager', 'editor']),
                'can_access_client' => $user->hasRole('client') || $user->hasAnyRole(['super-admin', 'admin']),
                'dashboard_url' => $this->getRedirectUrl($user, $request),
            ],
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

        return response()->json(['success' => true, 'message' => 'Session extended']);
    }
}