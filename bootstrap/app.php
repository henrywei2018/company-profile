<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule) {
        // Project notifications
            $schedule->call([\App\Observers\ProjectObserver::class, 'checkOverdueProjects'])
                ->daily()->at('09:00')->name('check-overdue-projects');

            // Quotation notifications
            $schedule->call([\App\Observers\QuotationObserver::class, 'checkExpiredQuotations'])
                ->daily()->at('10:00')->name('check-expired-quotations');

            $schedule->call([\App\Observers\QuotationObserver::class, 'checkPendingClientResponses'])
                ->daily()->at('14:00')->name('check-pending-responses');

            // Certification notifications
            $schedule->call([\App\Observers\CertificationObserver::class, 'checkExpiringCertifications'])
                ->daily()->at('08:00')->name('check-expiring-certifications');

            $schedule->call([\App\Observers\CertificationObserver::class, 'checkExpiredCertifications'])
                ->daily()->at('08:30')->name('check-expired-certifications');

            // Chat session cleanup
            $schedule->call([\App\Observers\ChatSessionObserver::class, 'checkAbandonedSessions'])
                ->hourly()->name('check-abandoned-chats');

            $schedule->call([\App\Observers\ChatSessionObserver::class, 'checkInactiveSessions'])
                ->everyThirtyMinutes()->name('check-inactive-chats');

            // User notifications
            $schedule->call([\App\Observers\UserObserver::class, 'checkIncompleteProfiles'])
                ->weekly()->mondays()->at('10:00')->name('check-incomplete-profiles');

            // Testimonial follow-ups
            $schedule->call([\App\Observers\TestimonialObserver::class, 'checkTestimonialFollowups'])
                ->weekly()->fridays()->at('15:00')->name('testimonial-followups');

            // Monthly reports
            $schedule->call([\App\Observers\CertificationObserver::class, 'generateMonthlyCertificationReport'])
                ->monthlyOn(1, '09:00')->name('monthly-certification-report');

            $schedule->call([\App\Observers\TestimonialObserver::class, 'sendMonthlyTestimonialSummary'])
                ->monthlyOn(1, '10:00')->name('monthly-testimonial-summary');

            // Daily chat report
            $schedule->call([\App\Observers\ChatSessionObserver::class, 'generateDailyChatReport'])
                ->daily()->at('23:30')->name('daily-chat-report');

            // Notification cleanup
            $schedule->call(function () {
                \DB::table('notifications')
                    ->whereNotNull('read_at')
                    ->where('created_at', '<', now()->subDays(30))
                    ->delete();

                \DB::table('notifications')
                    ->whereNull('read_at')
                    ->where('created_at', '<', now()->subDays(90))
                    ->delete();
            })->daily()->at('02:00')->name('cleanup-old-notifications');

    })
    ->withProviders([
        App\Providers\RepositoryServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\NotificationServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        // Simple middleware aliases - only what we need
        $middleware->alias([
            // Core Laravel middleware
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,

            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'client' => \App\Http\Middleware\ClientMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Simple exception handling
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }
            return redirect()->route('login');
        });

        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Forbidden'], 403);
            }

            if (auth()->check()) {
                $user = auth()->user();
                if ($request->is('admin/*') && !$user->hasAnyRole(['super-admin', 'admin', 'manager', 'editor'])) {
                    return redirect()->route('client.dashboard')->with('error', 'Access denied.');
                }
                if ($request->is('client/*') && !$user->hasRole('client') && !$user->hasAnyRole(['super-admin', 'admin'])) {
                    return redirect()->route('admin.dashboard')->with('error', 'Access denied.');
                }
            }

            return redirect()->back()->with('error', 'Access denied.');
        });
    })
    ->create();