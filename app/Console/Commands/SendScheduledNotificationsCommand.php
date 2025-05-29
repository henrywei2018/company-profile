<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Certification;
use App\Models\User;
use Carbon\Carbon;

class SendScheduledNotificationsCommand extends Command
{
    protected $signature = 'notifications:send-scheduled 
                          {--dry-run : Show what would be sent without actually sending}';
    
    protected $description = 'Send scheduled notifications (deadlines, reminders, alerts)';

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No notifications will be sent');
        }
        
        $this->info('📅 Checking for scheduled notifications...');

        $totalSent = 0;

        // Check project deadlines
        $totalSent += $this->checkProjectDeadlines($dryRun);

        // Check quotation expiry
        $totalSent += $this->checkQuotationExpiry($dryRun);

        // Check certificate expiry
        $totalSent += $this->checkCertificateExpiry($dryRun);

        // Check incomplete profiles
        $totalSent += $this->checkIncompleteProfiles($dryRun);

        $action = $dryRun ? 'would be sent' : 'sent';
        $this->info("✅ Total notifications {$action}: {$totalSent}");

        return Command::SUCCESS;
    }

    protected function checkProjectDeadlines(bool $dryRun): int
    {
        $sent = 0;
        $alertDays = config('notifications.scheduling.project_deadline_alerts', [1, 3, 7]);

        $this->info('🔍 Checking project deadlines...');

        foreach ($alertDays as $days) {
            $targetDate = now()->addDays($days)->toDateString();
            
            $projects = Project::where('status', 'in_progress')
                ->whereDate('end_date', $targetDate)
                ->with('client')
                ->get();

            foreach ($projects as $project) {
                if ($dryRun) {
                    $this->line("  📋 Would send deadline alert for: {$project->title} (due in {$days} days)");
                } else {
                    if ($this->notificationService->send('project.deadline_approaching', $project)) {
                        $this->line("  📋 Sent deadline alert for: {$project->title}");
                    }
                }
                $sent++;
            }
        }

        // Check overdue projects
        $overdueProjects = Project::where('status', 'in_progress')
            ->where('end_date', '<', now())
            ->whereNotNull('end_date')
            ->with('client')
            ->get();

        foreach ($overdueProjects as $project) {
            $daysOverdue = now()->diffInDays($project->end_date);
            if ($dryRun) {
                $this->line("  ⚠️  Would send overdue alert for: {$project->title} ({$daysOverdue} days overdue)");
            } else {
                if ($this->notificationService->send('project.overdue', $project)) {
                    $this->line("  ⚠️  Sent overdue alert for: {$project->title}");
                }
            }
            $sent++;
        }

        return $sent;
    }

    protected function checkQuotationExpiry(bool $dryRun): int
    {
        $sent = 0;
        
        $this->info('🔍 Checking quotation expiry...');
        
        // Check quotations expiring in 5 days
        $expiringQuotations = Quotation::where('status', 'approved')
            ->whereDate('approved_at', '<=', now()->subDays(25))
            ->whereDate('approved_at', '>', now()->subDays(30))
            ->whereNull('client_approved')
            ->get();

        foreach ($expiringQuotations as $quotation) {
            if ($dryRun) {
                $this->line("  📄 Would send expiry reminder for quotation: {$quotation->project_type}");
            } else {
                if ($this->notificationService->send('quotation.client_response_needed', $quotation)) {
                    $this->line("  📄 Sent expiry reminder for quotation: {$quotation->project_type}");
                }
            }
            $sent++;
        }

        return $sent;
    }

    protected function checkCertificateExpiry(bool $dryRun): int
    {
        $sent = 0;
        
        if (!class_exists(Certification::class)) {
            return $sent;
        }

        $this->info('🔍 Checking certificate expiry...');
        
        $alertDays = config('notifications.scheduling.certificate_expiry_alerts', [30, 7, 1]);

        foreach ($alertDays as $days) {
            $targetDate = now()->addDays($days)->toDateString();
            
            $certificates = Certification::where('is_active', true)
                ->whereNotNull('expiry_date')
                ->whereDate('expiry_date', $targetDate)
                ->get();

            foreach ($certificates as $certificate) {
                if ($dryRun) {
                    $this->line("  🏆 Would send expiry alert for certificate: {$certificate->name} (expires in {$days} days)");
                } else {
                    if ($this->notificationService->send('system.certificate_expiring', $certificate)) {
                        $this->line("  🏆 Sent expiry alert for certificate: {$certificate->name}");
                    }
                }
                $sent++;
            }
        }

        return $sent;
    }

    protected function checkIncompleteProfiles(bool $dryRun): int
    {
        $sent = 0;
        $reminderDays = config('notifications.scheduling.profile_completion_reminder', 7);
        
        $this->info('🔍 Checking incomplete profiles...');
        
        $cutoffDate = now()->subDays($reminderDays);
        
        $incompleteUsers = User::where('created_at', '<=', $cutoffDate)
            ->where(function($query) {
                $query->whereNull('phone')
                      ->orWhereNull('company')
                      ->orWhereNull('address');
            })
            ->whereNull('profile_reminder_sent_at')
            ->limit(50) // Process in batches
            ->get();

        foreach ($incompleteUsers as $user) {
            if ($dryRun) {
                $this->line("  👤 Would send profile completion reminder to: {$user->name}");
            } else {
                if ($this->notificationService->send('user.profile_incomplete', $user)) {
                    $user->update(['profile_reminder_sent_at' => now()]);
                    $this->line("  👤 Sent profile completion reminder to: {$user->name}");
                }
            }
            $sent++;
        }

        return $sent;
    }
}