<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthenticationService
{
    protected AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    public function attemptLogin(array $credentials, bool $remember = false): bool
    {
        $key = $this->getRateLimitKey();
        
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->auditService->logSecurityEvent('login.rate_limited', [
                'email' => $credentials['email'] ?? 'unknown',
            ]);
            
            throw ValidationException::withMessages([
                'email' => ['Too many login attempts. Please try again later.'],
            ]);
        }

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($key);
            
            $user = Auth::user();
            $this->auditService->logLogin($user, request());
            
            return true;
        }

        RateLimiter::hit($key, 300); // 5 minutes
        
        $this->auditService->logSecurityEvent('login.failed', [
            'email' => $credentials['email'] ?? 'unknown',
        ]);

        return false;
    }

    public function logout(): void
    {
        $user = Auth::user();
        
        if ($user) {
            $this->auditService->logLogout($user);
        }
        
        Auth::logout();
    }

    public function register(array $userData): User
    {
        $userData['password'] = Hash::make($userData['password']);
        $userData['email_verification_token'] = Str::random(64);
        
        $user = User::create($userData);
        
        // Assign default role
        $user->assignRole('client');
        
        $this->auditService->logModelCreated($user);
        
        return $user;
    }

    public function changePassword(User $user, string $currentPassword, string $newPassword): bool
    {
        if (!Hash::check($currentPassword, $user->password)) {
            return false;
        }

        $user->update(['password' => Hash::make($newPassword)]);
        
        $this->auditService->logPasswordChange($user);
        
        return true;
    }

    public function resetPassword(User $user, string $password): bool
    {
        $user->update([
            'password' => Hash::make($password),
            'remember_token' => null,
        ]);
        
        $this->auditService->logPasswordChange($user);
        
        return true;
    }

    public function verifyEmail(User $user, string $token): bool
    {
        if ($user->email_verification_token !== $token) {
            return false;
        }

        $user->update([
            'email_verified_at' => now(),
            'email_verification_token' => null,
        ]);

        $this->auditService->logUserAction('user.email_verified', $user, [], $user);
        
        return true;
    }

    protected function getRateLimitKey(): string
    {
        return 'login.' . request()->ip();
    }

    public function checkPermission(User $user, string $permission): bool
    {
        return $user->can($permission);
    }

    public function requirePermission(string $permission): void
    {
        if (!auth()->check() || !auth()->user()->can($permission)) {
            abort(403, 'Insufficient permissions');
        }
    }
}