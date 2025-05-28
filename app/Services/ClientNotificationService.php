<?php


namespace App\Services;

use App\Models\User;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Message;
use App\Notifications\ClientProjectUpdated;
use App\Notifications\ClientQuotationUpdated;
use App\Notifications\ClientMessageReceived;
use App\Notifications\ClientDeadlineAlert;
use App\Notifications\ClientWelcome;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ClientNotificationService
{
    protected ClientAccessService $clientAccessService;

    public function __construct(ClientAccessService $clientAccessService)
    {
        $this->clientAccessService = $clientAccessService;
    }

    /**
     * Get all notifications for a client.
     */
    public function getClientNotifications(User $user): array
    {
        $cacheKey = "client_notifications_{$user->id}";
        
        return Cache::remember($cacheKey, 300, function () use ($user) {
            return [
                'unread_messages' => $this->getUnreadMessagesCount($user),
                'pending_approvals' => $this->getPendingApprovalsCount($user),
                'overdue_projects' => $this->getOverdueProjectsCount($user),
                'upcoming_deadlines' => $this->getUpcomingDeadlinesCount($user),
                'system_alerts' => $this->getSystemAlerts($user),
                'priority_items' => $this->getPriorityItems($user),
                'recent_updates' => $this->getRecentUpdates($user),
            ];
        });
    }

    /**
     * Send project update notification to client.
     */
    public function notifyProjectUpdate(User $client, Project $project, string $updateType, array $changes = []): void
    {
        // Check if client should receive this notification
        if (!$this->shouldNotifyClient($client, 'project_updates')) {
            return;
        }

        // Send notification
        $client->notify(new ClientProjectUpdated($project, $updateType, $changes));

        // Log notification
        $this->logNotification($client, 'project_update', [
            'project_id' => $project->id,
            'update_type' => $updateType,
            'changes' => $changes,
        ]);

        // Clear cache
        $this->clearNotificationCache($client);
    }

    /**
     * Send quotation update notification to client.
     */
    public function notifyQuotationUpdate(User $client, Quotation $quotation, string $updateType, array $changes = []): void
    {
        // Check if client should receive this notification
        if (!$this->shouldNotifyClient($client, 'quotation_updates')) {
            return;
        }

        // Send notification
        $client->notify(new ClientQuotationUpdated($quotation, $updateType, $changes));

        // Log notification
        $this->logNotification($client, 'quotation_update', [
            'quotation_id' => $quotation->id,
            'update_type' => $updateType,
            'changes' => $changes,
        ]);

        // Clear cache
        $this->clearNotificationCache($client);
    }

    /**
     * Send new message notification to client.
     */
    public function notifyNewMessage(User $client, Message $message): void
    {
        // Check if client should receive this notification
        if (!$this->shouldNotifyClient($client, 'new_messages')) {
            return;
        }

        // Send notification
        $client->notify(new ClientMessageReceived($message));

        // Log notification
        $this->logNotification($client, 'new_message', [
            'message_id' => $message->id,
            'subject' => $message->subject,
            'type' => $message->type,
        ]);

        // Clear cache
        $this->clearNotificationCache($client);
    }

    /**
     * Send deadline alert notification.
     */
    public function notifyDeadlineAlert(User $client, Project $project, int $daysUntilDeadline): void
    {
        // Check if client should receive this notification
        if (!$this->shouldNotifyClient($client, 'deadline_alerts')) {
            return;
        }

        // Send notification
        $client->notify(new ClientDeadlineAlert($project, $daysUntilDeadline));

        // Log notification
        $this->logNotification($client, 'deadline_alert', [
            'project_id' => $project->id,
            'days_until_deadline' => $daysUntilDeadline,
        ]);
    }

    /**
     * Send welcome notification to new client.
     */
    public function sendWelcomeNotification(User $client): void
    {
        $client->notify(new ClientWelcome());
        
        $this->logNotification($client, 'welcome', []);
    }

    /**
     * Send bulk notifications to multiple clients.
     */
    public function sendBulkNotification(array $clientIds, $notification): void
    {
        $clients = User::whereIn('id', $clientIds)
            ->where('is_active', true)
            ->get();
            
        Notification::send($clients, $notification);

        // Clear cache for all notified clients
        foreach ($clients as $client) {
            $this->clearNotificationCache($client);
        }
    }

    /**
     * Check and send automatic deadline alerts.
     */
    public function checkAndSendDeadlineAlerts(): int
    {
        $alertsSent = 0;
        $alertDays = [1, 3, 7]; // Send alerts 1, 3, and 7 days before deadline

        foreach ($alertDays as $days) {
            $targetDate = now()->addDays($days)->toDateString();
            
            $projects = Project::where('status', 'in_progress')
                ->whereDate('end_date', $targetDate)
                ->with('client')
                ->get();

            foreach ($projects as $project) {
                if ($project->client && !$this->hasRecentDeadlineAlert($project->client, $project, $days)) {
                    $this->notifyDeadlineAlert($project->client, $project, $days);
                    $alertsSent++;
                }
            }
        }

        return $alertsSent;
    }

    /**
     * Get unread messages count.
     */
    protected function getUnreadMessagesCount(User $user): int
    {
        return $this->clientAccessService->getClientMessages($user)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get pending approvals count.
     */
    protected function getPendingApprovalsCount(User $user): int
    {
        return $this->clientAccessService->getClientQuotations($user)
            ->where('status', 'approved')
            ->whereNull('client_approved')
            ->count();
    }

    /**
     * Get overdue projects count.
     */
    protected function getOverdueProjectsCount(User $user): int
    {
        return $this->clientAccessService->getClientProjects($user)
            ->where('status', 'in_progress')
            ->where('end_date', '<', now())
            ->whereNotNull('end_date')
            ->count();
    }

    /**
     * Get upcoming deadlines count (next 7 days).
     */
    protected function getUpcomingDeadlinesCount(User $user): int
    {
        return $this->clientAccessService->getClientProjects($user)
            ->where('status', 'in_progress')
            ->whereBetween('end_date', [now(), now()->addDays(7)])
            ->count();
    }

    /**
     * Get system alerts.
     */
    protected function getSystemAlerts(User $user): array
    {
        $alerts = [];

        // Check for incomplete profile
        if (!$this->hasCompleteProfile($user)) {
            $alerts[] = [
                'type' => 'profile_incomplete',
                'title' => 'Complete Your Profile',
                'message' => 'Please complete your profile to ensure smooth communication and service delivery.',
                'action' => route('client.profile.edit'),
            ];
        }
    }
}

