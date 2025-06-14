<?php

namespace App\Providers;


use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Console\Scheduling\Schedule;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        // Chat Events
        \App\Events\ChatSessionStarted::class => [
            \App\Listeners\ChatSessionCreatedListener::class,
        ],
        
        \App\Events\ChatMessageSent::class => [
            \App\Listeners\ChatMessageSentListener::class,
        ],
        
        \App\Events\ChatOperatorStatusChanged::class => [
            \App\Listeners\ChatOperatorStatusListener::class,
        ],
    ];

    protected function schedule(Schedule $schedule)
    {
        
    }

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}