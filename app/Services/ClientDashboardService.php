<?php

namespace App\Services;

use App\Models\User;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Message;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ClientDashboardService
{
    protected ClientAccessService $clientAccessService;

    public function __construct(ClientAccessService $clientAccessService)
    {
        $this->clientAccessService = $clientAccessService;
    }

    /**
     * Get comprehensive dashboard data for client.
     */
    public function getDashboardData(User $user): array
    {
        $cacheKey = "client_dashboard_{$user->id}";
        
        return Cache::remember($cacheKey, 300, function () use ($user) {
            return [
                'statistics' => $this->clientAccessService->getClientDashboardStats($user),
                'recent_projects' => $this->getRecentProjects($user),
                'recent_quotations' => $this->getRecentQuotations($user),
                'recent_messages' => $this->getRecentMessages($user),
                'upcoming_deadlines' => $this->getUpcomingDeadlines($user),
                'quick_actions' => $this->getQuickActions($user),
            ];
        });
    }

    /**
     * Get recent activities for dashboard.
     */
    public function getRecentActivities(User $user): Collection
    {
        return $this->clientAccessService->getRecentActivity($user);
    }

    /**
     * Get notifications for client.
     */
    public function getNotifications(User $user): array
    {
        return [
            'unread_messages' => $this->getUnreadMessagesCount($user),
            'pending_approvals' => $this->getPendingApprovalsCount($user),
            'overdue_items' => $this->getOverdueItemsCount($user),
            'system_announcements' => $this->getSystemAnnouncements(),
        ];
    }

    /**
     * Get recent projects for dashboard.
     */
    protected function getRecentProjects(User $user): Collection
    {
        return $this->clientAccessService
            ->getClientProjects($user)
            ->limit(5)
            ->get();
    }

    /**
     * Get recent quotations for dashboard.
     */
    protected function getRecentQuotations(User $user): Collection
    {
        return $this->clientAccessService
            ->getClientQuotations($user)
            ->limit(5)
            ->get();
    }

    /**
     * Get recent messages for dashboard.
     */
    protected function getRecentMessages(User $user): Collection
    {
        return $this->clientAccessService
            ->getClientMessages($user)
            ->limit(5)
            ->get();
    }

    /**
     * Get upcoming deadlines.
     */
    protected function getUpcomingDeadlines(User $user): Collection
    {
        $baseCondition = $user->hasAnyRole(['super-admin', 'admin', 'manager']) 
            ? [] 
            : ['client_id' => $user->id];

        return Project::where($baseCondition)
            ->where('status', 'in_progress')
            ->where('end_date', '>', now())
            ->where('end_date', '<=', now()->addDays(7))
            ->orderBy('end_date')
            ->get()
            ->map(function ($project) {
                return [
                    'type' => 'project_deadline',
                    'title' => $project->title,
                    'date' => $project->end_date,
                    'url' => route('client.projects.show', $project->id),
                    'urgency' => $this->calculateUrgency($project->end_date),
                ];
            });
    }

    /**
     * Get quick actions available to client.
     */
    protected function getQuickActions(User $user): array
    {
        $actions = [];

        if ($user->can('create quotations')) {
            $actions[] = [
                'title' => 'Request Quote',
                'description' => 'Submit a new quotation request',
                'url' => route('client.quotations.create'),
                'icon' => 'document-add',
                'color' => 'blue',
            ];
        }

        if ($user->can('create messages')) {
            $actions[] = [
                'title' => 'Send Message',
                'description' => 'Contact our support team',
                'url' => route('client.messages.create'),
                'icon' => 'mail',
                'color' => 'green',
            ];
        }

        if ($user->can('create testimonials')) {
            $actions[] = [
                'title' => 'Leave Review',
                'description' => 'Share your experience with us',
                'url' => route('client.testimonials.create'),
                'icon' => 'star',
                'color' => 'yellow',
            ];
        }

        $actions[] = [
            'title' => 'Start Chat',
            'description' => 'Get instant support via chat',
            'url' => '#',
            'icon' => 'chat',
            'color' => 'purple',
            'action' => 'start-chat',
        ];

        return $actions;
    }

    /**
     * Get unread messages count.
     */
    protected function getUnreadMessagesCount(User $user): int
    {
        $baseCondition = $user->hasAnyRole(['super-admin', 'admin', 'manager']) 
            ? [] 
            : ['client_id' => $user->id];

        return Message::where($baseCondition)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get pending approvals count.
     */
    protected function getPendingApprovalsCount(User $user): int
    {
        $baseCondition = $user->hasAnyRole(['super-admin', 'admin', 'manager']) 
            ? [] 
            : ['client_id' => $user->id];

        return Quotation::where($baseCondition)
            ->where('status', 'approved')
            ->whereNull('client_approved')
            ->count();
    }

    /**
     * Get overdue items count.
     */
    protected function getOverdueItemsCount(User $user): int
    {
        $baseCondition = $user->hasAnyRole(['super-admin', 'admin', 'manager']) 
            ? [] 
            : ['client_id' => $user->id];

        return Project::where($baseCondition)
            ->where('status', 'in_progress')
            ->where('end_date', '<', now())
            ->count();
    }

    /**
     * Get system announcements.
     */
    protected function getSystemAnnouncements(): array
    {
        // This could be expanded to fetch from a database table
        return [
            [
                'id' => 1,
                'title' => 'System Maintenance Scheduled',
                'message' => 'Scheduled maintenance will occur this weekend.',
                'type' => 'info',
                'created_at' => now()->subDays(2),
                'is_read' => false,
            ],
        ];
    }

    /**
     * Calculate urgency level for deadlines.
     */
    protected function calculateUrgency(\Carbon\Carbon $deadline): string
    {
        $daysUntil = now()->diffInDays($deadline, false);
        
        return match(true) {
            $daysUntil <= 1 => 'critical',
            $daysUntil <= 3 => 'high',
            $daysUntil <= 7 => 'medium',
            default => 'low',
        };
    }
}