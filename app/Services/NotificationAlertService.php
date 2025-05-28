<?php
// File: app/Services/NotificationAlertService.php

namespace App\Services;

use App\Models\User;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Message;
use App\Models\ChatSession;
use App\Models\Certification;
use App\Models\SystemAlert;
use App\Models\UserNotification;
use App\Notifications\ProjectDeadlineAlert;
use App\Notifications\QuotationStatusAlert;
use App\Notifications\UrgentMessageAlert;
use App\Notifications\SystemMaintenanceAlert;
use App\Notifications\CertificationExpiryAlert;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotificationAlertService
{
    protected DashboardService $dashboardService;
    protected ClientAccessService $clientAccessService;

    public function __construct(
        DashboardService $dashboardService,
        ClientAccessService $clientAccessService
    ) {
        $this->dashboardService = $dashboardService;
        $this->clientAccessService = $clientAccessService;
    }

    /**
     * Check and send all notifications and alerts.
     */
    public function checkAndSendAll(): array
    {
        $results = [
            'project_deadlines' => $this->checkProjectDeadlines(),
            'quotation_alerts' => $this->checkQuotationAlerts(),
            'message_alerts' => $this->checkMessageAlerts(),
            'system_alerts' => $this->checkSystemAlerts(),
            'certification_alerts' => $this->checkCertificationAlerts(),
            'client_alerts' => $this->checkClientSpecificAlerts(),
            'chat_alerts' => $this->checkChatAlerts(),
        ];

        // Log summary
        $totalSent = array_sum(array_column($results, 'sent'));
        Log::info("Notification check completed. Total notifications sent: {$totalSent}");

        return [
            'success' => true,
            'total_sent' => $totalSent,
            'details' => $results,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Check project deadline alerts.
     */
    public function checkProjectDeadlines(): array
    {
        $sent = 0;
        $alerts = [];

        // Check for projects approaching deadlines (7, 3, 1 days)
        $alertDays = [7, 3, 1];

        foreach ($alertDays as $days) {
            $targetDate = now()->addDays($days)->toDateString();
            
            $projects = Project::where('status', 'in_progress')
                ->whereDate('end_date', $targetDate)
                ->with(['client', 'category'])
                ->get();

            foreach ($projects as $project) {
                if ($this->shouldSendProjectAlert($project, $days)) {
                    // Send to admin
                    $this->sendToAdmins(new ProjectDeadlineAlert($project, $days));
                    
                    // Send to client if exists
                    if ($project->client) {
                        $project->client->notify(new ProjectDeadlineAlert($project, $days));
                    }

                    $alerts[] = [
                        'project_id' => $project->id,
                        'project_title' => $project->title,
                        'client' => $project->client?->name,
                        'days_until_deadline' => $days,
                        'deadline_date' => $project->end_date->toDateString(),
                    ];

                    $this->recordAlertSent('project_deadline', $project->id, $days);
                    $sent++;
                }
            }
        }

        // Check for overdue projects
        $overdueProjects = Project::where('status', 'in_progress')
            ->where('end_date', '<', now())
            ->whereNotNull('end_date')
            ->with(['client', 'category'])
            ->get();

        foreach ($overdueProjects as $project) {
            if ($this->shouldSendOverdueAlert($project)) {
                $daysOverdue = now()->diffInDays($project->end_date);
                
                // Send to admin
                $this->sendToAdmins(new ProjectDeadlineAlert($project, -$daysOverdue, true));
                
                // Send to client
                if ($project->client) {
                    $project->client->notify(new ProjectDeadlineAlert($project, -$daysOverdue, true));
                }

                $alerts[] = [
                    'project_id' => $project->id,
                    'project_title' => $project->title,
                    'client' => $project->client?->name,
                    'days_overdue' => $daysOverdue,
                    'type' => 'overdue',
                ];

                $this->recordAlertSent('project_overdue', $project->id, $daysOverdue);
                $sent++;
            }
        }

        return [
            'sent' => $sent,
            'alerts' => $alerts,
            'checked_projects' => Project::where('status', 'in_progress')->count(),
        ];
    }

    /**
     * Check quotation-related alerts.
     */
    public function checkQuotationAlerts(): array
    {
        $sent = 0;
        $alerts = [];

        // Check for old pending quotations (>3 days without review)
        $oldPendingQuotations = Quotation::where('status', 'pending')
            ->where('created_at', '<', now()->subDays(3))
            ->with(['service', 'client'])
            ->get();

        foreach ($oldPendingQuotations as $quotation) {
            if ($this->shouldSendQuotationAlert($quotation)) {
                $this->sendToAdmins(new QuotationStatusAlert($quotation, 'pending_too_long'));
                
                $alerts[] = [
                    'quotation_id' => $quotation->id,
                    'client' => $quotation->name,
                    'service' => $quotation->service?->title,
                    'days_pending' => now()->diffInDays($quotation->created_at),
                    'type' => 'pending_too_long',
                ];

                $this->recordAlertSent('quotation_pending', $quotation->id);
                $sent++;
            }
        }

        // Check for approved quotations awaiting client response (>7 days)
        $awaitingApprovalQuotations = Quotation::where('status', 'approved')
            ->whereNull('client_approved')
            ->where('approved_at', '<', now()->subDays(7))
            ->with(['service', 'client'])
            ->get();

        foreach ($awaitingApprovalQuotations as $quotation) {
            if ($this->shouldSendClientApprovalAlert($quotation)) {
                // Send reminder to client
                if ($quotation->client) {
                    $quotation->client->notify(new QuotationStatusAlert($quotation, 'approval_reminder'));
                }

                // Notify admin
                $this->sendToAdmins(new QuotationStatusAlert($quotation, 'client_approval_needed'));

                $alerts[] = [
                    'quotation_id' => $quotation->id,
                    'client' => $quotation->name,
                    'service' => $quotation->service?->title,
                    'days_waiting' => now()->diffInDays($quotation->approved_at),
                    'type' => 'awaiting_client_approval',
                ];

                $this->recordAlertSent('quotation_approval', $quotation->id);
                $sent++;
            }
        }

        // Check for high-priority quotations
        $urgentQuotations = Quotation::where('priority', 'urgent')
            ->where('status', 'pending')
            ->with(['service', 'client'])
            ->get();

        foreach ($urgentQuotations as $quotation) {
            if ($this->shouldSendUrgentQuotationAlert($quotation)) {
                $this->sendToAdmins(new QuotationStatusAlert($quotation, 'urgent_priority'));

                $alerts[] = [
                    'quotation_id' => $quotation->id,
                    'client' => $quotation->name,
                    'service' => $quotation->service?->title,
                    'priority' => $quotation->priority,
                    'type' => 'urgent_priority',
                ];

                $this->recordAlertSent('quotation_urgent', $quotation->id);
                $sent++;
            }
        }

        return [
            'sent' => $sent,
            'alerts' => $alerts,
            'checked_quotations' => Quotation::whereIn('status', ['pending', 'approved'])->count(),
        ];
    }

    /**
     * Check message-related alerts.
     */
    public function checkMessageAlerts(): array
    {
        $sent = 0;
        $alerts = [];

        // Check for urgent unread messages
        $urgentMessages = Message::where('priority', 'urgent')
            ->where('is_read', false)
            ->where('created_at', '>', now()->subHours(24))
            ->get();

        foreach ($urgentMessages as $message) {
            if ($this->shouldSendUrgentMessageAlert($message)) {
                $this->sendToAdmins(new UrgentMessageAlert($message));

                $alerts[] = [
                    'message_id' => $message->id,
                    'sender' => $message->name,
                    'subject' => $message->subject,
                    'type' => 'urgent_unread',
                    'hours_old' => now()->diffInHours($message->created_at),
                ];

                $this->recordAlertSent('message_urgent', $message->id);
                $sent++;
            }
        }

        // Check for old unreplied messages (>24 hours)
        $oldUnrepliedMessages = Message::where('is_replied', false)
            ->where('type', '!=', 'admin_to_client')
            ->where('created_at', '<', now()->subHours(24))
            ->where('created_at', '>', now()->subDays(7))
            ->get();

        foreach ($oldUnrepliedMessages as $message) {
            if ($this->shouldSendUnrepliedMessageAlert($message)) {
                $this->sendToAdmins(new UrgentMessageAlert($message, 'unreplied'));

                $alerts[] = [
                    'message_id' => $message->id,
                    'sender' => $message->name,
                    'subject' => $message->subject,
                    'type' => 'unreplied_old',
                    'hours_old' => now()->diffInHours($message->created_at),
                ];

                $this->recordAlertSent('message_unreplied', $message->id);
                $sent++;
            }
        }

        return [
            'sent' => $sent,
            'alerts' => $alerts,
            'checked_messages' => Message::where('created_at', '>', now()->subDays(7))->count(),
        ];
    }

    /**
     * Check certification expiry alerts.
     */
    public function checkCertificationAlerts(): array
    {
        $sent = 0;
        $alerts = [];

        // Check for certifications expiring in 30, 7, and 1 days
        $alertDays = [30, 7, 1];

        foreach ($alertDays as $days) {
            $expiringCertifications = Certification::where('is_active', true)
                ->whereNotNull('expiry_date')
                ->whereDate('expiry_date', now()->addDays($days)->toDateString())
                ->get();

            foreach ($expiringCertifications as $certification) {
                if ($this->shouldSendCertificationAlert($certification, $days)) {
                    $this->sendToAdmins(new CertificationExpiryAlert($certification, $days));

                    $alerts[] = [
                        'certification_id' => $certification->id,
                        'name' => $certification->name,
                        'issuer' => $certification->issuer,
                        'expiry_date' => $certification->expiry_date,
                        'days_until_expiry' => $days,
                        'severity' => $days <= 7 ? 'critical' : 'warning',
                    ];

                    $this->recordAlertSent('certification_expiry', $certification->id, $days);
                    $sent++;
                }
            }
        }

        // Check for already expired certifications
        $expiredCertifications = Certification::where('is_active', true)
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now())
            ->get();

        foreach ($expiredCertifications as $certification) {
            if ($this->shouldSendExpiredCertificationAlert($certification)) {
                $daysExpired = now()->diffInDays($certification->expiry_date);
                
                $this->sendToAdmins(new CertificationExpiryAlert($certification, -$daysExpired, true));

                $alerts[] = [
                    'certification_id' => $certification->id,
                    'name' => $certification->name,
                    'issuer' => $certification->issuer,
                    'expiry_date' => $certification->expiry_date,
                    'days_expired' => $daysExpired,
                    'severity' => 'critical',
                    'type' => 'expired',
                ];

                $this->recordAlertSent('certification_expired', $certification->id, $daysExpired);
                $sent++;
            }
        }

        return [
            'sent' => $sent,
            'alerts' => $alerts,
            'checked_certifications' => Certification::where('is_active', true)->count(),
        ];
    }

    /**
     * Check client-specific alerts.
     */
    public function checkClientSpecificAlerts(): array
    {
        $sent = 0;
        $alerts = [];

        $clients = User::role('client')->where('is_active', true)->get();

        foreach ($clients as $client) {
            $clientAlerts = $this->generateClientAlerts($client);
            
            if (!empty($clientAlerts)) {
                foreach ($clientAlerts as $alert) {
                    if ($this->shouldSendClientAlert($client, $alert['type'])) {
                        // Send notification to client
                        $this->sendClientAlert($client, $alert);

                        $alerts[] = [
                            'client_id' => $client->id,
                            'client_name' => $client->name,
                            'alert_type' => $alert['type'],
                            'message' => $alert['message'],
                            'severity' => $alert['severity'],
                        ];

                        $this->recordAlertSent('client_' . $alert['type'], $client->id);
                        $sent++;
                    }
                }
            }
        }

        return [
            'sent' => $sent,
            'alerts' => $alerts,
            'checked_clients' => $clients->count(),
        ];
    }

    /**
     * Check chat-related alerts.
     */
    public function checkChatAlerts(): array
    {
        $sent = 0;
        $alerts = [];

        // Check for waiting chat sessions (>15 minutes)
        $waitingChats = ChatSession::where('status', 'waiting')
            ->where('created_at', '<', now()->subMinutes(15))
            ->with('user')
            ->get();

        foreach ($waitingChats as $chat) {
            if ($this->shouldSendChatAlert($chat)) {
                $this->sendToAdmins(new \App\Notifications\ChatWaitingAlert($chat));

                $alerts[] = [
                    'session_id' => $chat->session_id,
                    'client' => $chat->getVisitorName(),
                    'waiting_minutes' => now()->diffInMinutes($chat->created_at),
                    'type' => 'waiting_too_long',
                ];

                $this->recordAlertSent('chat_waiting', $chat->id);
                $sent++;
            }
        }

        // Check for active chats without recent activity (>30 minutes)
        $inactiveChats = ChatSession::where('status', 'active')
            ->where('last_activity_at', '<', now()->subMinutes(30))
            ->with('user')
            ->get();

        foreach ($inactiveChats as $chat) {
            if ($this->shouldSendInactiveChatAlert($chat)) {
                $this->sendToAdmins(new \App\Notifications\ChatInactiveAlert($chat));

                $alerts[] = [
                    'session_id' => $chat->session_id,
                    'client' => $chat->getVisitorName(),
                    'inactive_minutes' => now()->diffInMinutes($chat->last_activity_at),
                    'type' => 'inactive_too_long',
                ];

                $this->recordAlertSent('chat_inactive', $chat->id);
                $sent++;
            }
        }

        return [
            'sent' => $sent,
            'alerts' => $alerts,
            'checked_sessions' => ChatSession::whereIn('status', ['waiting', 'active'])->count(),
        ];
    }

    /**
     * Generate client-specific alerts.
     */
    protected function generateClientAlerts(User $client): array
    {
        $alerts = [];

        // Check for overdue project payments (mock implementation)
        $overduePayments = $this->clientAccessService->getClientProjects($client)
            ->where('status', 'completed')
            ->where('actual_completion_date', '<', now()->subDays(30))
            ->whereNull('payment_received_at')
            ->count();

        if ($overduePayments > 0) {
            $alerts[] = [
                'type' => 'overdue_payment',
                'message' => "You have {$overduePayments} project(s) with overdue payments.",
                'severity' => 'high',
                'data' => ['count' => $overduePayments],
            ];
        }

        // Check for incomplete profile
        if (!$this->hasCompleteProfile($client)) {
            $alerts[] = [
                'type' => 'incomplete_profile',
                'message' => 'Please complete your profile for better service.',
                'severity' => 'low',
                'data' => [],
            ];
        }

        // Check for pending project approvals
        $pendingApprovals = $this->clientAccessService->getClientQuotations($client)
            ->where('status', 'approved')
            ->whereNull('client_approved')
            ->count();

        if ($pendingApprovals > 0) {
            $alerts[] = [
                'type' => 'pending_approvals',
                'message' => "You have {$pendingApprovals} quotation(s) awaiting your approval.",
                'severity' => 'medium',
                'data' => ['count' => $pendingApprovals],
            ];
        }

        // Check for project deadline approaching
        $upcomingDeadlines = $this->clientAccessService->getClientProjects($client)
            ->where('status', 'in_progress')
            ->where('end_date', '>', now())
            ->where('end_date', '<=', now()->addDays(7))
            ->count();

        if ($upcomingDeadlines > 0) {
            $alerts[] = [
                'type' => 'upcoming_deadline',
                'message' => "You have {$upcomingDeadlines} project(s) with deadlines approaching within 7 days.",
                'severity' => 'medium',
                'data' => ['count' => $upcomingDeadlines],
            ];
        }

        return $alerts;
    }

    /**
     * Send notification to all admin users.
     */
    protected function sendToAdmins($notification): void
    {
        $admins = User::role(['super-admin', 'admin', 'manager'])->get();
        Notification::send($admins, $notification);
    }

    /**
     * Send notification to super admin users only.
     */
    protected function sendToSuperAdmins($notification): void
    {
        $superAdmins = User::role('super-admin')->get();
        Notification::send($superAdmins, $notification);
    }

    /**
     * Send client-specific alert.
     */
    protected function sendClientAlert(User $client, array $alert): void
    {
        // Create a generic client notification
        $client->notify(new \App\Notifications\ClientAlert($alert));
    }

    /**
     * Record that an alert has been sent to prevent duplicates.
     */
    protected function recordAlertSent(string $type, ?int $entityId = null, ?int $value = null): void
    {
        $key = $this->getAlertCacheKey($type, $entityId, $value);
        Cache::put($key, now(), now()->addHours(24));
    }

    /**
     * Check if alert should be sent (prevent duplicates).
     */
    protected function shouldSendAlert(string $type, ?int $entityId = null, ?int $value = null): bool
    {
        $key = $this->getAlertCacheKey($type, $entityId, $value);
        return !Cache::has($key);
    }

    /**
     * Generate cache key for alert tracking.
     */
    protected function getAlertCacheKey(string $type, ?int $entityId = null, ?int $value = null): string
    {
        return "alert_sent:{$type}:" . ($entityId ?? 'global') . ':' . ($value ?? 'default');
    }

    // Specific alert checking methods

    protected function shouldSendProjectAlert(Project $project, int $days): bool
    {
        return $this->shouldSendAlert('project_deadline', $project->id, $days);
    }

    protected function shouldSendOverdueAlert(Project $project): bool
    {
        $daysOverdue = now()->diffInDays($project->end_date);
        return $this->shouldSendAlert('project_overdue', $project->id, $daysOverdue);
    }

    protected function shouldSendQuotationAlert(Quotation $quotation): bool
    {
        return $this->shouldSendAlert('quotation_pending', $quotation->id);
    }

    protected function shouldSendClientApprovalAlert(Quotation $quotation): bool
    {
        return $this->shouldSendAlert('quotation_approval', $quotation->id);
    }

    protected function shouldSendUrgentQuotationAlert(Quotation $quotation): bool
    {
        return $this->shouldSendAlert('quotation_urgent', $quotation->id);
    }

    protected function shouldSendUrgentMessageAlert(Message $message): bool
    {
        return $this->shouldSendAlert('message_urgent', $message->id);
    }

    protected function shouldSendUnrepliedMessageAlert(Message $message): bool
    {
        return $this->shouldSendAlert('message_unreplied', $message->id);
    }

    protected function shouldSendSystemAlert(string $type): bool
    {
        return $this->shouldSendAlert('system_' . $type);
    }

    protected function shouldSendCertificationAlert(Certification $certification, int $days): bool
    {
        return $this->shouldSendAlert('certification_expiry', $certification->id, $days);
    }

    protected function shouldSendExpiredCertificationAlert(Certification $certification): bool
    {
        return $this->shouldSendAlert('certification_expired', $certification->id);
    }

    protected function shouldSendClientAlert(User $client, string $type): bool
    {
        return $this->shouldSendAlert('client_' . $type, $client->id);
    }

    protected function shouldSendChatAlert(ChatSession $chat): bool
    {
        return $this->shouldSendAlert('chat_waiting', $chat->id);
    }

    protected function shouldSendInactiveChatAlert(ChatSession $chat): bool
    {
        return $this->shouldSendAlert('chat_inactive', $chat->id);
    }

    // System health check methods

    protected function getDiskUsage(): float
    {
        // Mock implementation - replace with actual disk usage check
        return rand(60, 90);
    }

    protected function getDatabaseSize(): float
    {
        try {
            $size = DB::select("
                SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) as size_mb
                FROM information_schema.tables
                WHERE table_schema = DATABASE()
            ")[0]->size_mb ?? 0;
            
            return (float) $size;
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getFailedJobsCount(): int
    {
        try {
            return DB::table('failed_jobs')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getInactiveAdminCount(): int
    {
        return User::role(['admin', 'manager'])
            ->where('is_active', false)
            ->count();
    }

    protected function hasCompleteProfile(User $user): bool
    {
        return !empty($user->phone) && 
               !empty($user->address) && 
               !empty($user->company);
    }

    /**
     * Get notification summary for user.
     */
    public function getNotificationSummary(User $user): array
    {
        if ($user->hasAnyRole(['super-admin', 'admin', 'manager'])) {
            return $this->getAdminNotificationSummary($user);
        } else {
            return $this->getClientNotificationSummary($user);
        }
    }

    /**
     * Get admin notification summary.
     */
    protected function getAdminNotificationSummary(User $user): array
    {
        return [
            'total_alerts' => $this->getTotalActiveAlerts(),
            'critical_alerts' => $this->getCriticalAlerts(),
            'pending_items' => [
                'messages' => Message::where('is_read', false)->count(),
                'quotations' => Quotation::where('status', 'pending')->count(),
                'projects' => Project::where('status', 'planning')->count(),
                'chats' => ChatSession::where('status', 'waiting')->count(),
            ],
            'system_health' => [
                'disk_usage' => $this->getDiskUsage(),
                'database_size' => $this->getDatabaseSize(),
                'failed_jobs' => $this->getFailedJobsCount(),
                'overall_status' => $this->getOverallSystemStatus(),
            ],
            'recent_notifications' => $this->getRecentNotifications($user, 10),
        ];
    }

    /**
     * Get client notification summary.
     */
    protected function getClientNotificationSummary(User $user): array
    {
        return [
            'unread_messages' => $this->clientAccessService->getClientMessages($user)
                ->where('is_read', false)
                ->count(),
            'pending_approvals' => $this->clientAccessService->getClientQuotations($user)
                ->where('status', 'approved')
                ->whereNull('client_approved')
                ->count(),
            'upcoming_deadlines' => $this->clientAccessService->getClientProjects($user)
                ->where('status', 'in_progress')
                ->where('end_date', '>', now())
                ->where('end_date', '<=', now()->addDays(7))
                ->count(),
            'overdue_projects' => $this->clientAccessService->getClientProjects($user)
                ->where('status', 'in_progress')
                ->where('end_date', '<', now())
                ->whereNotNull('end_date')
                ->count(),
            'recent_notifications' => $this->getRecentNotifications($user, 5),
            'profile_completion' => $this->getProfileCompletionStatus($user),
        ];
    }

    /**
     * Get total active alerts count.
     */
    protected function getTotalActiveAlerts(): int
    {
        return Message::where('is_read', false)->count() +
               Quotation::where('status', 'pending')->count() +
               Project::where('status', 'in_progress')
                   ->where('end_date', '<', now())
                   ->whereNotNull('end_date')
                   ->count() +
               ChatSession::where('status', 'waiting')->count();
    }

    /**
     * Get critical alerts count.
     */
    protected function getCriticalAlerts(): int
    {
        return Message::where('priority', 'urgent')
                   ->where('is_read', false)
                   ->count() +
               Quotation::where('priority', 'urgent')
                   ->where('status', 'pending')
                   ->count() +
               Project::where('status', 'in_progress')
                   ->where('end_date', '<', now()->subDays(7))
                   ->whereNotNull('end_date')
                   ->count();
    }

    /**
     * Get overall system status.
     */
    protected function getOverallSystemStatus(): string
    {
        $diskUsage = $this->getDiskUsage();
        $failedJobs = $this->getFailedJobsCount();
        
        if ($diskUsage > 90 || $failedJobs > 50) {
            return 'critical';
        } elseif ($diskUsage > 80 || $failedJobs > 20) {
            return 'warning';
        } else {
            return 'healthy';
        }
    }

    /**
     * Get recent notifications for user.
     */
    protected function getRecentNotifications(User $user, int $limit = 10): array
    {
        // This would typically fetch from a notifications table
        // For now, return mock data structure
        return [
            [
                'id' => 1,
                'type' => 'project_deadline',
                'title' => 'Project deadline approaching',
                'message' => 'Project "Office Renovation" deadline is in 3 days',
                'created_at' => now()->subHours(2),
                'read_at' => null,
                'priority' => 'medium',
            ],
            [
                'id' => 2,
                'type' => 'new_quotation',
                'title' => 'New quotation request',
                'message' => 'New quotation request from John Doe',
                'created_at' => now()->subHours(5),
                'read_at' => now()->subHours(3),
                'priority' => 'normal',
            ],
        ];
    }

    /**
     * Get profile completion status.
     */
    protected function getProfileCompletionStatus(User $user): array
    {
        $fields = [
            'phone' => !empty($user->phone),
            'address' => !empty($user->address),
            'company' => !empty($user->company),
            'avatar' => !empty($user->avatar),
        ];

        $completed = count(array_filter($fields));
        $total = count($fields);

        return [
            'percentage' => round(($completed / $total) * 100),
            'completed_fields' => $completed,
            'total_fields' => $total,
            'missing_fields' => array_keys(array_filter($fields, fn($v) => !$v)),
        ];
    }

    /**
     * Clear notification cache.
     */
    public function clearNotificationCache(): void
    {
        $keys = Cache::get('notification_cache_keys', []);
        foreach ($keys as $key) {
            Cache::forget($key);
        }
        Cache::forget('notification_cache_keys');
    }

    /**
     * Test notification system.
     */
    public function testNotifications(User $user): array
    {
        $results = [];

        try {
            // Test admin notifications
            if ($user->hasAnyRole(['super-admin', 'admin'])) {
                $user->notify(new \App\Notifications\TestNotification('admin'));
                $results['admin_notification'] = 'sent';
            }

            // Test client notifications
            if ($user->hasRole('client')) {
                $user->notify(new \App\Notifications\TestNotification('client'));
                $results['client_notification'] = 'sent';
            }

            $results['success'] = true;
            $results['message'] = 'Test notifications sent successfully';

        } catch (\Exception $e) {
            $results['success'] = false;
            $results['message'] = 'Failed to send test notifications: ' . $e->getMessage();
        }

        return $results;
    }
}