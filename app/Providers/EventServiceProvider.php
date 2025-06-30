<?php

namespace App\Providers;

use App\Events\QuotationStatusChanged;
use App\Events\MessageReceived;
use App\Events\ProjectStatusChanged;
use App\Events\ChatSessionStarted;
use App\Events\ChatMessageSent;
use App\Events\ChatOperatorStatusChanged;
use App\Listeners\SendQuotationStatusNotification;
use App\Listeners\SendMessageNotification;
use App\Listeners\SendProjectStatusNotification;
use App\Listeners\ChatSessionStartedListener;
use App\Listeners\ChatMessageSentListener;
use App\Listeners\ChatOperatorStatusChangedListener;
use Illuminate\Auth\Events\Registered;
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
        QuotationStatusChanged::class => [
            SendQuotationStatusNotification::class,
        ],
        MessageReceived::class => [
            SendMessageNotification::class,
        ],
        ProjectStatusChanged::class => [
            SendProjectStatusNotification::class,
        ],
        ChatSessionStarted::class => [
            ChatSessionStartedListener::class,
        ],

        ChatMessageSent::class => [
            ChatMessageSentListener::class,
        ],

        ChatOperatorStatusChanged::class => [
            ChatOperatorStatusChangedListener::class,
        ],
    ];

    protected function schedule(Schedule $schedule)
    {
        // Check for inactive sessions every 5 minutes
        $schedule->call([new \App\Listeners\ChatSessionInactiveListener(), 'handle'])
            ->everyFiveMinutes()
            ->name('check-inactive-chat-sessions');

        // Auto-assign waiting sessions every minute
        $schedule->call(function () {
            app(\App\Services\ChatService::class)->autoAssignWaitingSessions();
        })
            ->everyMinute()
            ->name('auto-assign-waiting-sessions');

        // Clean up stale sessions daily
        $schedule->call(function () {
            app(\App\Services\ChatService::class)->cleanupStaleSessions();
        })
            ->daily()
            ->name('cleanup-stale-chat-sessions');
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