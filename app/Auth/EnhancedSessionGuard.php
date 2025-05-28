<?php

namespace App\Auth;

use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use App\Services\UserActivityService;
use App\Models\User;

/**
 * Enhanced Session Guard
 * 
 * Extends Laravel's SessionGuard with additional security features
 * for both admin and client authentication flows.
 */
class EnhancedSessionGuard extends SessionGuard
{
    /**
     * Attempt to authenticate a user using the given credentials.
     */
    public function attempt(array $credentials = [], $remember = false)
    {
        $this->fireAttemptEvent($credentials, $remember);

        // Add custom pre-authentication checks
        if (!$this->preAuthenticationChecks($credentials)) {
            $this->fireFailedEvent($this->provider->retrieveByCredentials($credentials), $credentials);
            return false;
        }

        $result = parent::attempt($credentials, $remember);

        // Add custom post-authentication logic
        if ($result && $this->user()) {
            $this->postAuthenticationActions($this->user());
        }

        return $result;
    }

    /**
     * Log a user into the application without sessions or cookies.
     */
    public function once(array $credentials = [])
    {
        $this->fireAttemptEvent($credentials, false);

        if (!$this->preAuthenticationChecks($credentials)) {
            return false;
        }

        $result = parent::once($credentials);

        if ($result && $this->user()) {
            $this->postAuthenticationActions($this->user(), false);
        }

        return $result;
    }

    /**
     * Perform pre-authentication checks.
     */
    protected function preAuthenticationChecks(array $credentials): bool
    {
        // Retrieve user by credentials first
        $user = $this->provider->retrieveByCredentials($credentials);
        
        if (!$user) {
            return true; // Let the normal flow handle non-existent users
        }

        // Check if account is active
        if ($this->hasProperty($user, 'is_active') && !$user->is_active) {
            \Log::warning('Inactive user attempted login', [
                'email' => $credentials['email'] ?? 'unknown',
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            return false;
        }

        // Check if account is locked
        if ($this->hasProperty($user, 'locked_at') && $user->locked_at && $user->locked_at->isFuture()) {
            \Log::warning('Locked user attempted login', [
                'email' => $credentials['email'] ?? 'unknown',
                'locked_until' => $user->locked_at->toDateTimeString(),
                'ip' => request()->ip(),
            ]);
            return false;
        }

        // Check failed login attempts
        $failedAttempts = $this->getFailedLoginAttempts($user);
        if ($failedAttempts >= 5) {
            $this->lockUserAccount($user);
            return false;
        }

        // Check if client is verified (if applicable)
        if (method_exists($user, 'hasRole') && $user->hasRole('client')) {
            if ($this->hasProperty($user, 'is_verified') && !$user->is_verified) {
                // Allow login but will be handled by middleware
                \Log::info('Unverified client login attempt', [
                    'email' => $credentials['email'] ?? 'unknown',
                    'ip' => request()->ip(),
                ]);
            }
        }

        return true;
    }

    /**
     * Perform post-authentication actions.
     */
    protected function postAuthenticationActions(Authenticatable $user, bool $persistent = true): void
    {
        // Reset failed login attempts
        $failedAttempts = $this->getFailedLoginAttempts($user);
        if ($failedAttempts > 0) {
            $this->updateUser($user, [
                'failed_login_attempts' => 0,
                'locked_at' => null,
            ]);
        }

        // Update login tracking
        $updateData = [
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ];

        // Increment login count if persistent login
        if ($persistent && $this->hasProperty($user, 'login_count')) {
            $loginCount = $this->getPropertyValue($user, 'login_count', 0);
            $updateData['login_count'] = $loginCount + 1;
        }

        $this->updateUser($user, $updateData);

        // Clear any cached permissions
        if (method_exists($user, 'forgetCachedPermissions')) {
            $user->forgetCachedPermissions();
        }

        // Log successful authentication
        if (app()->bound(UserActivityService::class)) {
            $activityService = app(UserActivityService::class);
            // Cast to User model if possible
            $userModel = $user instanceof User ? $user : null;
            if ($userModel) {
                $activityService->logActivity($userModel, 'login', [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'guard' => $this->name,
                    'persistent' => $persistent,
                ]);
            }
        }

        \Log::info('User authenticated successfully', [
            'user_id' => $this->getUserId($user),
            'email' => $this->getUserEmail($user),
            'ip' => request()->ip(),
            'guard' => $this->name,
        ]);
    }

    /**
     * Lock user account after too many failed attempts.
     */
    protected function lockUserAccount(Authenticatable $user): void
    {
        $failedAttempts = $this->getFailedLoginAttempts($user);
        
        $this->updateUser($user, [
            'locked_at' => now()->addMinutes(30), // Lock for 30 minutes
            'failed_login_attempts' => $failedAttempts + 1,
        ]);

        \Log::warning('User account locked due to failed login attempts', [
            'user_id' => $this->getUserId($user),
            'email' => $this->getUserEmail($user),
            'failed_attempts' => $failedAttempts,
            'locked_until' => now()->addMinutes(30)->toDateTimeString(),
            'ip' => request()->ip(),
        ]);
    }

    /**
     * Handle failed authentication attempt.
     */
    protected function incrementFailedAttempts(array $credentials): void
    {
        $user = $this->provider->retrieveByCredentials($credentials);
        
        if ($user && $this->hasProperty($user, 'failed_login_attempts')) {
            $failedAttempts = $this->getFailedLoginAttempts($user) + 1;
            
            $updateData = ['failed_login_attempts' => $failedAttempts];
            
            // Lock account if too many failed attempts
            if ($failedAttempts >= 5) {
                $updateData['locked_at'] = now()->addMinutes(30);
            }
            
            $this->updateUser($user, $updateData);

            \Log::warning('Failed login attempt recorded', [
                'user_id' => $this->getUserId($user),
                'email' => $this->getUserEmail($user),
                'failed_attempts' => $failedAttempts,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        // Log failed attempt for non-existent users
        if (!$user) {
            if (app()->bound(UserActivityService::class)) {
                $activityService = app(UserActivityService::class);
                $activityService->logFailedLogin([
                    'email' => $credentials['email'] ?? 'unknown',
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'guard' => $this->name,
                ]);
            }
        }
    }

    /**
     * Fire the attempt event with custom data.
     * Fixed method signature to match parent class.
     */
    protected function fireAttemptEvent(array $credentials, $remember = false): void
    {
        if (isset($this->events)) {
            $this->events->dispatch(new \Illuminate\Auth\Events\Attempting(
                $this->name, $credentials, $remember
            ));
        }
    }

    /**
     * Fire the failed event with user data.
     * Fixed method signature to match parent class.
     */
    protected function fireFailedEvent($user, array $credentials): void
    {
        if (isset($this->events)) {
            $this->events->dispatch(new \Illuminate\Auth\Events\Failed(
                $this->name, $user, $credentials
            ));
        }

        // Increment failed attempts
        $this->incrementFailedAttempts($credentials);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(): void
    {
        $user = $this->user();

        if ($user) {
            // Log logout activity
            if (app()->bound(UserActivityService::class)) {
                $activityService = app(UserActivityService::class);
                // Cast to User model if possible
                $userModel = $user instanceof User ? $user : null;
                if ($userModel) {
                    $activityService->logActivity($userModel, 'logout', [
                        'ip' => request()->ip(),
                        'guard' => $this->name,
                    ]);
                }
            }

            \Log::info('User logged out', [
                'user_id' => $this->getUserId($user),
                'email' => $this->getUserEmail($user),
                'ip' => request()->ip(),
            ]);
        }

        parent::logout();
    }

    /**
     * Validate a user's credentials.
     */
    public function validate(array $credentials = []): bool
    {
        // Pre-validation checks
        if (!$this->preAuthenticationChecks($credentials)) {
            return false;
        }

        return parent::validate($credentials);
    }

    /**
     * Get user's last activity timestamp.
     */
    public function getLastActivity(): ?\Carbon\Carbon
    {
        $user = $this->user();
        
        if ($user && $this->hasProperty($user, 'last_login_at')) {
            return $user->last_login_at;
        }

        return null;
    }

    /**
     * Check if user session is about to expire.
     */
    public function isSessionNearExpiry(int $warningMinutes = 5): bool
    {
        $lastActivity = session('last_activity_time', time());
        $sessionLifetime = config('session.lifetime', 120) * 60; // Convert to seconds
        $timeRemaining = $sessionLifetime - (time() - $lastActivity);
        
        return $timeRemaining <= ($warningMinutes * 60);
    }

    /**
     * Extend user session.
     */
    public function extendSession(): void
    {
        session(['last_activity_time' => time()]);
        
        if ($user = $this->user()) {
            \Log::info('User session extended', [
                'user_id' => $this->getUserId($user),
                'ip' => request()->ip(),
            ]);
        }
    }

    /**
     * Helper method to safely check if a property exists on the user model.
     */
    protected function hasProperty(Authenticatable $user, string $property): bool
    {
        // First check if it's a standard property
        if (property_exists($user, $property)) {
            return true;
        }
        
        // Then check if we can access it via getAttribute method (for Eloquent models)
        if (method_exists($user, 'getAttribute')) {
            try {
                $value = $user->getAttribute($property);
                return !is_null($value) || $user->hasAttribute($property);
            } catch (\Exception $e) {
                return false;
            }
        }
        
        return false;
    }

    /**
     * Helper method to safely get user ID.
     */
    protected function getUserId(Authenticatable $user)
    {
        if (method_exists($user, 'getKey')) {
            return $user->getKey();
        }
        
        if (method_exists($user, 'getAttribute')) {
            return $user->getAttribute('id') ?? 'unknown';
        }
        
        return property_exists($user, 'id') ? $user->id : 'unknown';
    }

    /**
     * Helper method to safely get user email.
     */
    protected function getUserEmail(Authenticatable $user): string
    {
        if (method_exists($user, 'getAttribute')) {
            return $user->getAttribute('email') ?? 'unknown';
        }
        
        return property_exists($user, 'email') ? $user->email : 'unknown';
    }

    /**
     * Helper method to safely get property value.
     */
    protected function getPropertyValue(Authenticatable $user, string $property, $default = null)
    {
        if (method_exists($user, 'getAttribute')) {
            return $user->getAttribute($property) ?? $default;
        }
        
        return property_exists($user, $property) ? $user->$property : $default;
    }

    /**
     * Helper method to safely get failed login attempts.
     */
    protected function getFailedLoginAttempts(Authenticatable $user): int
    {
        return (int) $this->getPropertyValue($user, 'failed_login_attempts', 0);
    }

    /**
     * Helper method to safely update user properties.
     */
    protected function updateUser(Authenticatable $user, array $data): void
    {
        if (method_exists($user, 'update')) {
            try {
                $user->update($data);
            } catch (\Exception $e) {
                \Log::error('Failed to update user during authentication', [
                    'user_id' => $this->getUserId($user),
                    'error' => $e->getMessage(),
                    'data' => $data,
                ]);
            }
        }
    }
}