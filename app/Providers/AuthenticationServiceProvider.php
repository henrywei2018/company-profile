<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use App\Services\ClientAccessService;
use App\Services\UserActivityService;

/**
 * Authentication Service Provider
 * 
 * This provider handles authentication-related services, events,
 * and customizations for both admin and client authentication flows.
 */
class AuthenticationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register user activity service
        $this->app->singleton(UserActivityService::class, function ($app) {
            return new UserActivityService();
        });

        // Register authentication guards if needed
        $this->registerCustomGuards();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register authentication event listeners
        $this->registerAuthEventListeners();
        
        // Configure authentication providers
        $this->configureAuthProviders();
        
        // Set up custom authentication logic
        $this->setupCustomAuthLogic();
    }

    /**
     * Register custom authentication guards.
     */
    protected function registerCustomGuards(): void
    {
        // Add any custom guards here if needed
        // For example, API guards with different configurations
    }

    /**
     * Register authentication event listeners.
     */
    protected function registerAuthEventListeners(): void
    {
        // Listen for successful login events
        $this->app['events']->listen(Login::class, function (Login $event) {
            $user = $event->user;
            
            // Log user activity
            $activityService = app(UserActivityService::class);
            $activityService->logActivity($user, 'login', [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'guard' => $event->guard,
            ]);

            // Update last login timestamp
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
            ]);

            // Clear any client cache if this is a client
            if ($user->hasRole('client')) {
                $clientService = app(ClientAccessService::class);
                $clientService->clearClientCache($user);
            }

            // Send login notification if enabled
            if (config('auth.notifications.login', false)) {
                $user->notify(new \App\Notifications\LoginNotification());
            }
        });

        // Listen for logout events
        $this->app['events']->listen(Logout::class, function (Logout $event) {
            if ($event->user) {
                $activityService = app(UserActivityService::class);
                $activityService->logActivity($event->user, 'logout', [
                    'ip' => request()->ip(),
                    'guard' => $event->guard,
                ]);
            }
        });

        // Listen for user registration events
        $this->app['events']->listen(Registered::class, function (Registered $event) {
            $user = $event->user;
            
            $activityService = app(UserActivityService::class);
            $activityService->logActivity($user, 'registered', [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Send welcome notification for clients
            if ($user->hasRole('client')) {
                $notificationService = app(\App\Services\ClientNotificationService::class);
                $notificationService->sendWelcomeNotification($user);
            }
        });

        // Listen for authentication failure events
        $this->app['events']->listen(\Illuminate\Auth\Events\Failed::class, function ($event) {
            $activityService = app(UserActivityService::class);
            $activityService->logFailedLogin([
                'email' => $event->credentials['email'] ?? 'unknown',
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'guard' => $event->guard,
            ]);
        });

        // Listen for password reset events
        $this->app['events']->listen(\Illuminate\Auth\Events\PasswordReset::class, function ($event) {
            $activityService = app(UserActivityService::class);
            $activityService->logActivity($event->user, 'password_reset', [
                'ip' => request()->ip(),
            ]);
        });
    }

    /**
     * Configure authentication providers.
     */
    protected function configureAuthProviders(): void
    {
        // Extend the user provider if needed
        Auth::provider('enhanced_eloquent', function ($app, array $config) {
            return new \App\Auth\EnhancedEloquentUserProvider($app['hash'], $config['model']);
        });
    }

    /**
     * Set up custom authentication logic.
     */
    protected function setupCustomAuthLogic(): void
    {
        // Add custom authentication checks
        Auth::extend('enhanced_session', function ($app, $name, array $config) {
            $provider = Auth::createUserProvider($config['provider'] ?? null);
            $guard = new \App\Auth\EnhancedSessionGuard($name, $provider, $app['session.store']);
            
            // Add custom logic to the guard
            $guard->setCookieJar($app['cookie']);
            $guard->setDispatcher($app['events']);
            $guard->setRequest($app->refresh('request', $guard, 'setRequest'));
            
            return $guard;
        });
    }
}

/**
 * User Activity Service
 * File: app/Services/UserActivityService.php
 */
namespace App\Services;

use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Support\Facades\Log;

class UserActivityService
{
    /**
     * Log user activity.
     */
    public function logActivity(User $user, string $action, array $context = []): void
    {
        try {
            UserActivity::create([
                'user_id' => $user->id,
                'action' => $action,
                'ip_address' => $context['ip'] ?? request()->ip(),
                'user_agent' => $context['user_agent'] ?? request()->userAgent(),
                'context' => $context,
                'performed_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log user activity', [
                'user_id' => $user->id,
                'action' => $action,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Log failed login attempt.
     */
    public function logFailedLogin(array $data): void
    {
        try {
            UserActivity::create([
                'user_id' => null,
                'action' => 'failed_login',
                'ip_address' => $data['ip'],
                'user_agent' => $data['user_agent'] ?? null,
                'context' => [
                    'email' => $data['email'],
                    'guard' => $data['guard'] ?? 'web',
                ],
                'performed_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log failed login attempt', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
        }
    }

    /**
     * Get user's recent activities.
     */
    public function getRecentActivities(User $user, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return UserActivity::where('user_id', $user->id)
            ->orderBy('performed_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Clean up old activity records.
     */
    public function cleanupOldActivities(int $daysToKeep = 90): int
    {
        return UserActivity::where('performed_at', '<', now()->subDays($daysToKeep))
            ->delete();
    }
}

/**
 * Enhanced Session Guard
 * File: app/Auth/EnhancedSessionGuard.php
 */
namespace App\Auth;

use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\Authenticatable;

class EnhancedSessionGuard extends SessionGuard
{
    /**
     * Attempt to authenticate a user using the given credentials.
     */
    public function attempt(array $credentials = [], $remember = false)
    {
        // Add custom pre-authentication checks
        if (!$this->preAuthenticationChecks($credentials)) {
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
     * Perform pre-authentication checks.
     */
    protected function preAuthenticationChecks(array $credentials): bool
    {
        // Check if user exists and is active
        $user = $this->provider->retrieveByCredentials($credentials);
        
        if (!$user) {
            return true; // Let the normal flow handle non-existent users
        }

        // Check if account is active
        if (isset($user->is_active) && !$user->is_active) {
            return false;
        }

        // Check if client is verified (if applicable)
        if ($user->hasRole('client') && isset($user->is_verified) && !$user->is_verified) {
            // Allow login but will be handled by middleware
            return true;
        }

        return true;
    }

    /**
     * Perform post-authentication actions.
     */
    protected function postAuthenticationActions(Authenticatable $user): void
    {
        // Update login tracking
        if (method_exists($user, 'update')) {
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
            ]);
        }

        // Clear any cached permissions
        if (method_exists($user, 'forgetCachedPermissions')) {
            $user->forgetCachedPermissions();
        }
    }
}

/**
 * Enhanced Eloquent User Provider
 * File: app/Auth/EnhancedEloquentUserProvider.php
 */
namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class EnhancedEloquentUserProvider extends EloquentUserProvider
{
    /**
     * Validate a user against the given credentials.
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        // Perform standard validation
        $valid = parent::validateCredentials($user, $credentials);

        if (!$valid) {
            return false;
        }

        // Additional validation checks
        return $this->additionalValidationChecks($user);
    }

    /**
     * Perform additional validation checks.
     */
    protected function additionalValidationChecks(Authenticatable $user): bool
    {
        // Check if account is active
        if (isset($user->is_active) && !$user->is_active) {
            return false;
        }

        // Check if account is locked
        if (isset($user->is_locked) && $user->is_locked) {
            return false;
        }

        // Check if account has expired
        if (isset($user->expires_at) && $user->expires_at && $user->expires_at->isPast()) {
            return false;
        }

        return true;
    }
}