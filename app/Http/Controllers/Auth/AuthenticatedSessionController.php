<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $request->authenticate();

        $request->session()->regenerate();

        // Get the authenticated user
        $user = Auth::user();

        // Check if user account is active
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Your account has been deactivated. Please contact the administrator.');
        }

        // Determine redirect URL based on user role
        $redirectUrl = $this->getRedirectUrlByRole($user);

        return redirect()->intended($redirectUrl);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Get redirect URL based on user's role.
     */
    private function getRedirectUrlByRole($user): string
    {
        // Check roles in order of hierarchy (highest to lowest)
        if ($user->hasRole('super-admin')) {
            return route('admin.dashboard');
        }

        if ($user->hasRole('admin')) {
            return route('admin.dashboard');
        }

        if ($user->hasRole('manager')) {
            return route('admin.dashboard');
        }

        if ($user->hasRole('editor')) {
            return route('admin.dashboard');
        }

        if ($user->hasRole('client')) {
            return route('client.dashboard');
        }

        // Default fallback - if user has no role or unrecognized role
        // Check if user has any admin permissions
        if ($user->can('view dashboard')) {
            return route('admin.dashboard');
        }

        // If no admin permissions, treat as client
        return route('client.dashboard');
    }
}