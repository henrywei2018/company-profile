<?php
// File: app/Providers/NotificationServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\NotificationService;
use App\Services\ClientNotificationService;
use App\Services\EmailNotificationService;
use App\Observers\ProjectObserver;
use App\Observers\QuotationObserver;
use App\Observers\MessageObserver;
use App\Observers\TestimonialObserver;
use App\Observers\UserObserver;
use App\Observers\ChatSessionObserver;
use App\Observers\CertificationObserver;
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

        // Register notification channels if using custom channels
        $this->registerNotificationChannels();

        // Load custom notification templates
        $this->loadNotificationTemplates();
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
            Project::observe(ProjectObserver::class);
            Quotation::observe(QuotationObserver::class);
            Message::observe(MessageObserver::class);
            Testimonial::observe(TestimonialObserver::class);
            User::observe(UserObserver::class);
            
            // Register additional observers if models exist
            if (class_exists(ChatSession::class)) {
                ChatSession::observe(ChatSessionObserver::class);
            }
            
            if (class_exists(Certification::class)) {
                Certification::observe(CertificationObserver::class);
            }
        }
    }

    /**
     * Register custom notification channels (only email-based)
     */
    protected function registerNotificationChannels(): void
    {
        // Register Slack channel if configured
        if (config('services.slack.notifications.bot_user_oauth_token')) {
            $this->app->singleton('notification.slack', function ($app) {
                return new \App\Channels\SlackNotificationChannel();
            });
        }

        // Register Discord channel if configured
        if (config('services.discord.webhook_url')) {
            $this->app->singleton('notification.discord', function ($app) {
                return new \App\Channels\DiscordNotificationChannel();
            });
        }

        // Register Teams channel if configured
        if (config('services.teams.webhook_url')) {
            $this->app->singleton('notification.teams', function ($app) {
                return new \App\Channels\TeamsNotificationChannel();
            });
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
            'notification.slack',
            'notification.discord',
            'notification.teams',
        ];
    }
}
