<?php
// File: app/Providers/NotificationServiceProvider.php - UPDATED

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\NotificationService;
use App\Services\ClientNotificationService;
use App\Services\EmailNotificationService;

// Import all observers
use App\Observers\ProjectObserver;
use App\Observers\QuotationObserver;
use App\Observers\MessageObserver;
use App\Observers\TestimonialObserver;
use App\Observers\UserObserver;
use App\Observers\ChatSessionObserver;
use App\Observers\CertificationObserver;

// Import all models
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Message;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\ChatSession;
use App\Models\Certification;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the main NotificationService as singleton
        $this->app->singleton(NotificationService::class, function ($app) {
            return new NotificationService();
        });

        // Register alias for facade access
        $this->app->alias(NotificationService::class, 'notifications');

        // Register additional notification services
        $this->registerAdditionalServices();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load notification configuration
        $this->loadConfiguration();

        // Register model observers for automatic notifications
        $this->registerModelObservers();

        // Load custom notification templates
        $this->loadNotificationTemplates();

        // Register scheduled notification checks
        $this->registerScheduledChecks();
    }

    /**
     * Register additional notification-related services
     */
    protected function registerAdditionalServices(): void
    {
        // Register Email Service for notifications
        $this->app->singleton(EmailNotificationService::class, function ($app) {
            return new EmailNotificationService();
        });

        // Register Client Notification Service
        $this->app->singleton(ClientNotificationService::class, function ($app) {
            return new ClientNotificationService($app->make(NotificationService::class));
        });

        // Register alias for email service
        $this->app->alias(EmailNotificationService::class, 'notification.email');
    }

    /**
     * Load notification configuration
     */
    protected function loadConfiguration(): void
    {
        // Merge notification config if not already published
        $this->mergeConfigFrom(
            __DIR__.'/../../config/notifications.php', 'notifications'
        );

        // Publish config file for customization
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/notifications.php' => config_path('notifications.php'),
            ], 'notification-config');
        }
    }

    /**
     * Register model observers to automatically send notifications
     */
    protected function registerModelObservers(): void
    {
        // Only register observers if auto notifications are enabled
        if (config('notifications.auto_notifications', true)) {
            
            // Core business model observers
            Project::observe(ProjectObserver::class);
            Quotation::observe(QuotationObserver::class);
            Message::observe(MessageObserver::class);
            User::observe(UserObserver::class);
            Testimonial::observe(TestimonialObserver::class);
            ChatSession::observe(ChatSessionObserver::class);
            Certification::observe(CertificationObserver::class);
        }
    }

    /**
     * Load custom notification templates
     */
    protected function loadNotificationTemplates(): void
    {
        // Load email templates
        $this->loadViewsFrom(__DIR__.'/../../resources/views/notifications', 'notifications');
        
        // Publish notification views for customization
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../resources/views/notifications' => resource_path('views/vendor/notifications'),
            ], 'notification-views');
        }
    }

    /**
     * Register scheduled notification checks
     */
    protected function registerScheduledChecks(): void
    {
        if ($this->app->runningInConsole()) {
            
        }
    }

    /**
     * Get services provided by this provider
     */
    public function provides(): array
    {
        return [
            NotificationService::class,
            ClientNotificationService::class,
            EmailNotificationService::class,
            'notifications',
            'notification.email',
        ];
    }
}