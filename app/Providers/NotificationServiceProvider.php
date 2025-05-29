<?php
// File: app/Providers/NotificationServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\NotificationService;
use App\Observers\ProjectObserver;
use App\Observers\QuotationObserver;
use App\Observers\MessageObserver;
use App\Observers\TestimonialObserver;
use App\Observers\UserObserver;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Message;
use App\Models\Testimonial;
use App\Models\User;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(NotificationService::class, function ($app) {
            return new NotificationService();
        });

        // Register alias for easier access
        $this->app->alias(NotificationService::class, 'notifications');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register model observers to automatically send notifications
        Project::observe(ProjectObserver::class);
        Quotation::observe(QuotationObserver::class);
        Message::observe(MessageObserver::class);
        Testimonial::observe(TestimonialObserver::class);
        User::observe(UserObserver::class);
    }
}

// File: app/Observers/ProjectObserver.php

namespace App\Observers;

use App\Models\Project;
use App\Services\NotificationService;

class ProjectObserver
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the Project "created" event.
     */
    public function created(Project $project): void
    {
        $this->notificationService->send('project.created', $project);
    }

    /**
     * Handle the Project "updated" event.
     */
    public function updated(Project $project): void
    {
        // Check if status changed
        if ($project->isDirty('status')) {
            $this->notificationService->send('project.status_changed', $project);
        } else {
            $this->notificationService->send('project.updated', $project);
        }

        // Check if project is completed
        if ($project->status === 'completed' && $project->getOriginal('status') !== 'completed') {
            $this->notificationService->send('project.completed', $project);
        }
    }
}

// File: app/Observers/QuotationObserver.php

namespace App\Observers;

use App\Models\Quotation;
use App\Services\NotificationService;

class QuotationObserver
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the Quotation "created" event.
     */
    public function created(Quotation $quotation): void
    {
        $this->notificationService->send('quotation.created', $quotation);
    }

    /**
     * Handle the Quotation "updated" event.
     */
    public function updated(Quotation $quotation): void
    {
        // Check if status changed
        if ($quotation->isDirty('status')) {
            $this->notificationService->send('quotation.status_updated', $quotation);

            // Send specific notifications for certain status changes
            if ($quotation->status === 'approved') {
                $this->notificationService->send('quotation.approved', $quotation);
            }
        }

        // Check if client approval is needed
        if ($quotation->status === 'approved' && is_null($quotation->client_approved)) {
            $this->notificationService->send('quotation.client_response_needed', $quotation);
        }
    }
}

// File: app/Observers/MessageObserver.php

namespace App\Observers;

use App\Models\Message;
use App\Services\NotificationService;

class MessageObserver
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the Message "created" event.
     */
    public function created(Message $message): void
    {
        // Send general message notification
        $this->notificationService->send('message.created', $message);

        // Send urgent notification if marked as urgent
        if ($message->priority === 'urgent') {
            $this->notificationService->send('message.urgent', $message);
        }

        // If this is a reply, send reply notification
        if ($message->parent_id) {
            $this->notificationService->send('message.reply', $message);
        }
    }
}

// File: app/Observers/TestimonialObserver.php

namespace App\Observers;

use App\Models\Testimonial;
use App\Services\NotificationService;

class TestimonialObserver
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the Testimonial "created" event.
     */
    public function created(Testimonial $testimonial): void
    {
        $this->notificationService->send('testimonial.created', $testimonial);
    }

    /**
     * Handle the Testimonial "updated" event.
     */
    public function updated(Testimonial $testimonial): void
    {
        // Check if testimonial was approved
        if ($testimonial->is_active && !$testimonial->getOriginal('is_active')) {
            $this->notificationService->send('testimonial.approved', $testimonial);
        }

        // Check if testimonial was featured
        if ($testimonial->featured && !$testimonial->getOriginal('featured')) {
            $this->notificationService->send('testimonial.featured', $testimonial);
        }
    }
}

// File: app/Observers/UserObserver.php

namespace App\Observers;

use App\Models\User;
use App\Services\NotificationService;

class UserObserver
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Send welcome notification for new users
        $this->notificationService->send('user.welcome', $user);

        // Check if profile is incomplete
        if (!$this->hasCompleteProfile($user)) {
            $this->notificationService->send('user.profile_incomplete', $user);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Check if email was verified
        if ($user->email_verified_at && !$user->getOriginal('email_verified_at')) {
            $this->notificationService->send('user.email_verified', $user);
        }

        // Check if password was changed
        if ($user->isDirty('password')) {
            $this->notificationService->send('user.password_changed', $user);
        }
    }

    /**
     * Check if user has complete profile
     */
    protected function hasCompleteProfile(User $user): bool
    {
        return !empty($user->phone) && 
               !empty($user->company) && 
               !empty($user->address);
    }
}

// File: app/Helpers/NotificationHelper.php

namespace App\Helpers;

use App\Services\NotificationService;
use App\Models\User;
use Carbon\Carbon;

class NotificationHelper
{
    protected static NotificationService $service;

    /**
     * Initialize the helper
     */
    public static function init(): void
    {
        static::$service = app(NotificationService::class);
    }

    /**
     * Send notification
     */
    public static function send(string $type, $data = null, $recipients = null): bool
    {
        static::init();
        return static::$service->send($type, $data, $recipients);
    }

    /**
     * Send bulk notifications
     */
    public static function sendBulk(string $type, array $dataRecipientPairs): array
    {
        static::init();
        return static::$service->sendBulk($type, $dataRecipientPairs);
    }

    /**
     * Schedule notification
     */
    public static function schedule(string $type, $data, $recipients, Carbon $sendAt): bool
    {
        static::init();
        return static::$service->schedule($type, $data, $recipients, $sendAt);
    }

    /**
     * Notify all admins
     */
    public static function notifyAdmins(string $type, $data = null): bool
    {
        $admins = User::role(['super-admin', 'admin', 'manager'])->get();
        return static::send($type, $data, $admins);
    }

    /**
     * Notify all clients
     */
    public static function notifyClients(string $type, $data = null): bool
    {
        $clients = User::role('client')->where('is_active', true)->get();
        return static::send($type, $data, $clients);
    }

    /**
     * Notify project stakeholders
     */
    public static function notifyProjectStakeholders(string $type, $project): bool
    {
        $recipients = collect();
        
        // Add client
        if ($project->client) {
            $recipients->push($project->client);
        }
        
        // Add admins
        $recipients = $recipients->merge(
            User::role(['super-admin', 'admin', 'manager'])->get()
        );
        
        return static::send($type, $project, $recipients);
    }

    /**
     * Send system alert to super admins
     */
    public static function systemAlert(string $type, $data = null): bool
    {
        $superAdmins = User::role('super-admin')->get();
        return static::send($type, $data, $superAdmins);
    }

    /**
     * Send urgent notification
     */
    public static function urgent(string $type, $data = null, $recipients = null): bool
    {
        // If no recipients specified, send to all active admins
        if ($recipients === null) {
            $recipients = User::role(['super-admin', 'admin'])
                ->where('is_active', true)
                ->get();
        }
        
        return static::send($type, $data, $recipients);
    }

    /**
     * Test notification system
     */
    public static function test(User $user = null): array
    {
        static::init();
        
        if (!$user) {
            $user = User::role(['super-admin', 'admin'])->first();
        }
        
        return static::$service->test($user);
    }

    /**
     * Get notification statistics
     */
    public static function statistics(): array
    {
        static::init();
        return static::$service->getStatistics();
    }

    /**
     * Check if notification type exists
     */
    public static function hasType(string $type): bool
    {
        static::init();
        return static::$service->hasType($type);
    }

    /**
     * Get available notification types
     */
    public static function getAvailableTypes(): array
    {
        static::init();
        return static::$service->getAvailableTypes();
    }
}

// File: app/Console/Commands/NotificationCleanupCommand.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotificationCleanupCommand extends Command
{
    protected $signature = 'notifications:cleanup {--days=30 : Number of days to keep read notifications}';
    protected $description = 'Clean up old read notifications';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);

        // Delete old read notifications
        $deletedCount = DB::table('notifications')
            ->whereNotNull('read_at')
            ->where('read_at', '<', $cutoffDate)
            ->delete();

        $this->info("Deleted {$deletedCount} old read notifications.");

        // Clean up notification cache
        app(NotificationService::class)->clearCache();
        $this->info("Cleared notification cache.");

        return Command::SUCCESS;
    }
}

// File: app/Console/Commands/SendScheduledNotificationsCommand.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Certification;
use Carbon\Carbon;

class SendScheduledNotificationsCommand extends Command
{
    protected $signature = 'notifications:send-scheduled';
    protected $description = 'Send scheduled notifications (deadlines, reminders, etc.)';

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle(): int
    {
        $this->info('Checking for scheduled notifications...');

        $totalSent = 0;

        // Check project deadlines
        $totalSent += $this->checkProjectDeadlines();

        // Check quotation expiry
        $totalSent += $this->checkQuotationExpiry();

        // Check certificate expiry
        $totalSent += $this->checkCertificateExpiry();

        // Check incomplete profiles
        $totalSent += $this->checkIncompleteProfiles();

        $this->info("Total notifications sent: {$totalSent}");

        return Command::SUCCESS;
    }

    protected function checkProjectDeadlines(): int
    {
        $sent = 0;
        $alertDays = [1, 3, 7]; // Days before deadline to send alerts

        foreach ($alertDays as $days) {
            $targetDate = now()->addDays($days)->toDateString();
            
            $projects = Project::where('status', 'in_progress')
                ->whereDate('end_date', $targetDate)
                ->with('client')
                ->get();

            foreach ($projects as $project) {
                if ($this->notificationService->send('project.deadline_approaching', $project)) {
                    $sent++;
                }
            }
        }

        // Check overdue projects
        $overdueProjects = Project::where('status', 'in_progress')
            ->where('end_date', '<', now())
            ->whereNotNull('end_date')
            ->with('client')
            ->get();

        foreach ($overdueProjects as $project) {
            if ($this->notificationService->send('project.overdue', $project)) {
                $sent++;
            }
        }

        return $sent;
    }

    protected function checkQuotationExpiry(): int
    {
        $sent = 0;
        
        // Check quotations expiring in 5 days
        $expiringQuotations = Quotation::where('status', 'approved')
            ->whereDate('approved_at', '<=', now()->subDays(25))
            ->whereDate('approved_at', '>', now()->subDays(30))
            ->get();

        foreach ($expiringQuotations as $quotation) {
            if ($this->notificationService->send('quotation.expired', $quotation)) {
                $sent++;
            }
        }

        return $sent;
    }

    protected function checkCertificateExpiry(): int
    {
        $sent = 0;
        $alertDays = [1, 7, 30]; // Days before expiry to send alerts

        foreach ($alertDays as $days) {
            $targetDate = now()->addDays($days)->toDateString();
            
            $certificates = Certification::where('is_active', true)
                ->whereNotNull('expiry_date')
                ->whereDate('expiry_date', $targetDate)
                ->get();

            foreach ($certificates as $certificate) {
                if ($this->notificationService->send('system.certificate_expiring', $certificate)) {
                    $sent++;
                }
            }
        }

        return $sent;
    }

    protected function checkIncompleteProfiles(): int
    {
        // This would check for users with incomplete profiles and send reminders
        // Implementation depends on your specific requirements
        return 0;
    }
}