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
        $schedule->call(fn () => app(\App\Observers\ProjectObserver::class)->checkOverdueProjects())
            ->daily()->at('09:00')->name('check-overdue-projects');

        $schedule->call(fn () => app(\App\Observers\QuotationObserver::class)->checkExpiredQuotations())
            ->daily()->at('10:00')->name('check-expired-quotations');

        $schedule->call(fn () => app(\App\Observers\QuotationObserver::class)->checkPendingClientResponses())
            ->daily()->at('14:00')->name('check-pending-responses');

        $schedule->call(fn () => app(\App\Observers\CertificationObserver::class)->checkExpiringCertifications())
            ->daily()->at('08:00')->name('check-expiring-certifications');

        $schedule->call(fn () => app(\App\Observers\CertificationObserver::class)->checkExpiredCertifications())
            ->daily()->at('08:30')->name('check-expired-certifications');

        $schedule->call(fn () => app(\App\Observers\ChatSessionObserver::class)->checkAbandonedSessions())
            ->hourly()->name('check-abandoned-chats');

        $schedule->call(fn () => app(\App\Observers\ChatSessionObserver::class)->checkInactiveSessions())
            ->everyThirtyMinutes()->name('check-inactive-chats');

        $schedule->call(fn () => app(\App\Observers\UserObserver::class)->checkIncompleteProfiles())
            ->weekly()->mondays()->at('10:00')->name('check-incomplete-profiles');

        $schedule->call(fn () => app(\App\Observers\TestimonialObserver::class)->checkTestimonialFollowups())
            ->weekly()->fridays()->at('15:00')->name('testimonial-followups');

        $schedule->call(fn () => app(\App\Observers\CertificationObserver::class)->generateMonthlyCertificationReport())
            ->monthlyOn(1, '09:00')->name('monthly-certification-report');

        $schedule->call(fn () => app(\App\Observers\TestimonialObserver::class)->sendMonthlyTestimonialSummary())
            ->monthlyOn(1, '10:00')->name('monthly-testimonial-summary');


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
        $schedule->call(function () {
            app(\App\Services\SettingsService::class)->clearCache();
        })->daily()->at('01:00')->name('clear-settings-cache');
        // FilePond cleanup
        $schedule->command('filepond:cleanup')
        ->hourly()
        ->description('Clean up old FilePond temporary files');
        
        $schedule->command('filepond:cleanup --hours=6')
            ->daily()
            ->at('02:00')
            ->description('Daily aggressive cleanup');
            
        // Storage monitoring
        $schedule->call(function () {
            $stats = app(\App\Services\FilePondService::class)->getStorageStats();
            
            if ($stats['temp_files_size'] > 100 * 1024 * 1024) {
                \Log::warning('FilePond temporary storage usage is high', $stats);
            }
             })->weekly();   

        $schedule->call(function () {
            $stats = app(\App\Services\FilePondService::class)->getStorageStats();
            \Log::info('FilePond storage stats', $stats);
        })->daily()->at('03:00')->name('log-filepond-storage-stats');
    })
    ->withProviders([
        App\Providers\RepositoryServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\NotificationServiceProvider::class,
        App\Providers\SeoServiceProvider::class,
        App\Providers\NavigationServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\SeoMiddleware::class,

        ]);
        
        $middleware->alias([
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'role' => \App\Http\Middleware\RequireRole::class,
            'permission.all' => \App\Http\Middleware\RequireAllPermissions::class,
            'permission.any' => \App\Http\Middleware\RequireAnyPermission::class,
            'rbac' => \App\Http\Middleware\RoleBasedAccessControl::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'client' => \App\Http\Middleware\ClientMiddleware::class,
            'validate.filepond' => \App\Http\Middleware\ValidateFilePondUpload::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
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
            return response()->view('errors.403', [], 403);
        });

    })
    ->create();