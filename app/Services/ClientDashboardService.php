<?php
// File: app/Services/ClientDashboardService.php

namespace App\Services;

use App\Models\User;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Message;
use App\Models\Testimonial;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ClientDashboardService
{
    protected ClientAccessService $clientAccessService;
    protected ClientNotificationService $notificationService;

    public function __construct(
        ClientAccessService $clientAccessService,
        ClientNotificationService $notificationService
    ) {
        $this->clientAccessService = $clientAccessService;
        $this->notificationService = $notificationService;
    }

    /**
     * Get comprehensive dashboard data for client.
     */
    public function getDashboardData(User $user): array
    {
        $cacheKey = "client_dashboard_data_{$user->id}";
        
        return Cache::remember($cacheKey, 300, function () use ($user) {
            return [
                'statistics' => $this->getStatistics($user),
                'recent_activities' => $this->getRecentActivities($user),
                'upcoming_deadlines' => $this->getUpcomingDeadlines($user),
                'notifications' => $this->getNotifications($user),
                'quick_actions' => $this->getQuickActions($user),
                'performance_summary' => $this->getPerformanceSummary($user),
                'alerts' => $this->getAlerts($user),
            ];
        });
    }

    /**
     * Get dashboard statistics using ClientAccessService.
     */
    public function getStatistics(User $user): array
    {
        $cacheKey = "client_statistics_{$user->id}";
        
        return Cache::remember($cacheKey, 300, function () use ($user) {
            // Use ClientAccessService for consistent data access
            $projectsQuery = $this->clientAccessService->getClientProjects($user);
            $quotationsQuery = $this->clientAccessService->getClientQuotations($user);
            $messagesQuery = $this->clientAccessService->getClientMessages($user);

            return [
                'projects' => [
                    'total' => (clone $projectsQuery)->count(),
                    'active' => (clone $projectsQuery)->whereIn('status', ['in_progress', 'on_hold'])->count(),
                    'completed' => (clone $projectsQuery)->where('status', 'completed')->count(),
                    'planning' => (clone $projectsQuery)->where('status', 'planning')->count(),
                    'overdue' => (clone $projectsQuery)
                        ->where('status', 'in_progress')
                        ->where('end_date', '<', now())
                        ->whereNotNull('end_date')
                        ->count(),
                    'this_year' => (clone $projectsQuery)->whereYear('created_at', now()->year)->count(),
                ],
                'quotations' => [
                    'total' => (clone $quotationsQuery)->count(),
                    'pending' => (clone $quotationsQuery)->where('status', 'pending')->count(),
                    'reviewed' => (clone $quotationsQuery)->where('status', 'reviewed')->count(),
                    'approved' => (clone $quotationsQuery)->where('status', 'approved')->count(),
                    'awaiting_approval' => (clone $quotationsQuery)
                        ->where('status', 'approved')
                        ->whereNull('client_approved')
                        ->count(),
                    'this_month' => (clone $quotationsQuery)->whereMonth('created_at', now()->month)->count(),
                ],
                'messages' => [
                    'total' => (clone $messagesQuery)->count(),
                    'unread' => (clone $messagesQuery)->where('is_read', false)->count(),
                    'replied' => (clone $messagesQuery)->where('is_replied', true)->count(),
                    'this_week' => (clone $messagesQuery)->whereBetween('created_at', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ])->count(),
                    'urgent' => (clone $messagesQuery)
                        ->where('priority', 'urgent')
                        ->where('is_read', false)
                        ->count(),
                ],
                'summary' => [
                    'completion_rate' => $this->calculateCompletionRate($projectsQuery),
                    'response_rate' => $this->calculateResponseRate($messagesQuery),
                    'satisfaction_rate' => $this->calculateSatisfactionRate($user),
                    'project_value' => $this->calculateProjectValue($projectsQuery),
                ]
            ];
        });
    }

    /**
     * Get recent activities aggregated from multiple sources.
     */
    public function getRecentActivities(User $user): array
    {
        $cacheKey = "client_activities_{$user->id}";
        
        return Cache::remember($cacheKey, 180, function () use ($user) {
            $activities = collect();

            // Recent project updates
            $recentProjects = $this->clientAccessService->getClientProjects($user)
                ->with(['category', 'images' => fn($q) => $q->where('is_featured', true)])
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get();

            foreach ($recentProjects as $project) {
                $activities->push([
                    'id' => 'project_' . $project->id,
                    'type' => 'project',
                    'title' => 'Project Updated: ' . $project->title,
                    'description' => 'Status changed to: ' . ucfirst(str_replace('_', ' ', $project->status)),
                    'date' => $project->updated_at,
                    'url' => route('client.projects.show', $project->id),
                    'icon' => 'folder',
                    'color' => $this->getProjectStatusColor($project->status),
                    'priority' => $this->getActivityPriority($project->status),
                    'meta' => [
                        'status' => $project->status,
                        'category' => $project->category?->name,
                        'location' => $project->location,
                        'progress' => $this->calculateProjectProgress($project),
                    ]
                ]);
            }

            // Recent quotation updates
            $recentQuotations = $this->clientAccessService->getClientQuotations($user)
                ->with('service')
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get();

            foreach ($recentQuotations as $quotation) {
                $activities->push([
                    'id' => 'quotation_' . $quotation->id,
                    'type' => 'quotation',
                    'title' => 'Quotation Updated: ' . ($quotation->project_type ?? 'Quote #' . $quotation->id),
                    'description' => 'Status: ' . ucfirst($quotation->status),
                    'date' => $quotation->updated_at,
                    'url' => route('client.quotations.show', $quotation->id),
                    'icon' => 'document-text',
                    'color' => $this->getQuotationStatusColor($quotation->status),
                    'priority' => $this->getQuotationPriority($quotation),
                    'meta' => [
                        'status' => $quotation->status,
                        'service' => $quotation->service?->title,
                        'priority' => $quotation->priority,
                        'value' => $quotation->estimated_cost,
                    ]
                ]);
            }

            // Recent messages
            $recentMessages = $this->clientAccessService->getClientMessages($user)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            foreach ($recentMessages as $message) {
                $activities->push([
                    'id' => 'message_' . $message->id,
                    'type' => 'message',
                    'title' => 'Message: ' . ($message->subject ?? 'No Subject'),
                    'description' => $message->is_read ? 'Read' : 'New message received',
                    'date' => $message->created_at,
                    'url' => route('client.messages.show', $message->id),
                    'icon' => 'mail',
                    'color' => $message->is_read ? 'gray' : 'blue',
                    'priority' => $message->is_read ? 'low' : 'medium',
                    'meta' => [
                        'is_read' => $message->is_read,
                        'type' => $message->type,
                        'replied' => $message->is_replied,
                        'has_attachments' => $message->attachments()->count() > 0,
                    ]
                ]);
            }

            // Sort by date and priority, return latest 10
            return $activities
                ->sortByDesc(function ($activity) {
                    return $activity['date']->timestamp + $this->getPriorityWeight($activity['priority']);
                })
                ->take(10)
                ->values()
                ->toArray();
        });
    }

    /**
     * Get upcoming deadlines with enhanced details.
     */
    public function getUpcomingDeadlines(User $user): array
    {
        return $this->clientAccessService->getClientProjects($user)
            ->where('status', 'in_progress')
            ->whereNotNull('end_date')
            ->where('end_date', '>', now())
            ->where('end_date', '<=', now()->addDays(30))
            ->with(['category', 'images'])
            ->orderBy('end_date')
            ->get()
            ->map(function ($project) {
                $daysUntil = now()->diffInDays($project->end_date, false);
                
                return [
                    'id' => $project->id,
                    'type' => 'project_deadline',
                    'title' => $project->title,
                    'date' => $project->end_date,
                    'days_until' => $daysUntil,
                    'url' => route('client.projects.show', $project->id),
                    'urgency' => $this->calculateUrgency($daysUntil),
                    'location' => $project->location,
                    'category' => $project->category?->name,
                    'progress' => $this->calculateProjectProgress($project),
                    'estimated_completion' => $this->estimateCompletion($project),
                ];
            })
            ->toArray();
    }

    /**
     * Get notifications using ClientNotificationService.
     */
    public function getNotifications(User $user): array
    {
        return $this->notificationService->getClientNotifications($user);
    }

    /**
     * Get quick actions available to user.
     */
    public function getQuickActions(User $user): array
    {
        $actions = [];

        // Request Quote
        if ($user->can('create quotations') || $user->hasRole('client')) {
            $actions[] = [
                'title' => 'Request Quote',
                'description' => 'Submit a new quotation request',
                'url' => route('client.quotations.create'),
                'icon' => 'document-add',
                'color' => 'blue',
                'enabled' => true,
                'badge' => null,
            ];
        }

        // Send Message
        if ($user->can('create messages') || $user->hasRole('client')) {
            $unreadCount = $this->clientAccessService->getClientMessages($user)
                ->where('is_read', false)
                ->count();
                
            $actions[] = [
                'title' => 'Send Message',
                'description' => 'Contact our support team',
                'url' => route('client.messages.create'),
                'icon' => 'mail',
                'color' => 'green',
                'enabled' => true,
                'badge' => $unreadCount > 0 ? $unreadCount : null,
            ];
        }

        // Leave Review
        if ($user->can('create testimonials') || $user->hasRole('client')) {
            $completedProjects = $this->clientAccessService->getClientProjects($user)
                ->where('status', 'completed')
                ->whereDoesntHave('testimonial')
                ->count();
                
            $actions[] = [
                'title' => 'Leave Review',
                'description' => 'Share your experience',
                'url' => route('client.testimonials.create'),
                'icon' => 'star',
                'color' => 'yellow',
                'enabled' => $completedProjects > 0,
                'badge' => $completedProjects > 0 ? $completedProjects : null,
            ];
        }

        // View Portfolio
        $actions[] = [
            'title' => 'View Portfolio',
            'description' => 'Browse our completed projects',
            'url' => route('portfolio.index'),
            'icon' => 'photograph',
            'color' => 'purple',
            'enabled' => true,
            'badge' => null,
        ];

        return $actions;
    }

    /**
     * Get performance summary with KPIs.
     */
    public function getPerformanceSummary(User $user): array
    {
        $projectsQuery = $this->clientAccessService->getClientProjects($user);
        $quotationsQuery = $this->clientAccessService->getClientQuotations($user);
        $messagesQuery = $this->clientAccessService->getClientMessages($user);

        return [
            'completion_rate' => $this->calculateCompletionRate($projectsQuery),
            'response_rate' => $this->calculateResponseRate($messagesQuery),
            'satisfaction_rate' => $this->calculateSatisfactionRate($user),
            'project_value' => $this->calculateProjectValue($projectsQuery),
            'on_time_delivery' => $this->calculateOnTimeDelivery($projectsQuery),
            'communication_score' => $this->calculateCommunicationScore($messagesQuery),
            'trends' => [
                'projects' => $this->getProjectTrends($user),
                'quotations' => $this->getQuotationTrends($user),
                'messages' => $this->getMessageTrends($user),
            ]
        ];
    }

    /**
     * Get alerts and important notifications.
     */
    public function getAlerts(User $user): array
    {
        $alerts = [];

        // Overdue projects
        $overdueProjects = $this->clientAccessService->getClientProjects($user)
            ->where('status', 'in_progress')
            ->where('end_date', '<', now())
            ->whereNotNull('end_date')
            ->count();

        if ($overdueProjects > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Overdue Projects',
                'message' => "You have {$overdueProjects} project(s) that are overdue.",
                'action' => [
                    'text' => 'View Projects',
                    'url' => route('client.projects.index', ['status' => 'overdue']),
                ],
                'priority' => 'high',
            ];
        }

        // Pending approvals
        $pendingApprovals = $this->clientAccessService->getClientQuotations($user)
            ->where('status', 'approved')
            ->whereNull('client_approved')
            ->count();

        if ($pendingApprovals > 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Pending Approvals',
                'message' => "You have {$pendingApprovals} quotation(s) awaiting your approval.",
                'action' => [
                    'text' => 'Review Quotations',
                    'url' => route('client.quotations.index', ['status' => 'approved']),
                ],
                'priority' => 'medium',
            ];
        }

        // Unread urgent messages
        $urgentMessages = $this->clientAccessService->getClientMessages($user)
            ->where('is_read', false)
            ->where('priority', 'urgent')
            ->count();

        if ($urgentMessages > 0) {
            $alerts[] = [
                'type' => 'error',
                'title' => 'Urgent Messages',
                'message' => "You have {$urgentMessages} urgent unread message(s).",
                'action' => [
                    'text' => 'Read Messages',
                    'url' => route('client.messages.index', ['read' => 'unread']),
                ],
                'priority' => 'critical',
            ];
        }

        return $alerts;
    }

    // Helper Methods

    protected function calculateCompletionRate($projectsQuery): float
    {
        $total = (clone $projectsQuery)->count();
        if ($total === 0) return 0;
        
        $completed = (clone $projectsQuery)->where('status', 'completed')->count();
        return round(($completed / $total) * 100, 1);
    }

    protected function calculateResponseRate($messagesQuery): float
    {
        $total = (clone $messagesQuery)->count();
        if ($total === 0) return 0;
        
        $replied = (clone $messagesQuery)->where('is_replied', true)->count();
        return round(($replied / $total) * 100, 1);
    }

    protected function calculateSatisfactionRate(User $user): float
    {
        $testimonials = Testimonial::whereHas('project', function($query) use ($user) {
            $query->where('client_id', $user->id);
        })->get();

        if ($testimonials->isEmpty()) return 0;

        $averageRating = $testimonials->avg('rating');
        return round(($averageRating / 5) * 100, 1);
    }

    protected function calculateProjectValue($projectsQuery): float
    {
        return (clone $projectsQuery)
            ->whereNotNull('value')
            ->sum('value') ?? 0;
    }

    protected function calculateOnTimeDelivery($projectsQuery): float
    {
        $completedProjects = (clone $projectsQuery)
            ->where('status', 'completed')
            ->whereNotNull('end_date')
            ->get();

        if ($completedProjects->isEmpty()) return 0;

        $onTimeCount = $completedProjects->filter(function($project) {
            return $project->completed_at <= $project->end_date;
        })->count();

        return round(($onTimeCount / $completedProjects->count()) * 100, 1);
    }

    protected function calculateCommunicationScore($messagesQuery): float
    {
        $totalMessages = (clone $messagesQuery)->count();
        if ($totalMessages === 0) return 100;

        $timelyResponses = (clone $messagesQuery)
            ->where('is_replied', true)
            ->whereRaw('replied_at <= DATE_ADD(created_at, INTERVAL 24 HOUR)')
            ->count();

        return round(($timelyResponses / $totalMessages) * 100, 1);
    }

    protected function getProjectStatusColor(string $status): string
    {
        return match($status) {
            'planning' => 'yellow',
            'in_progress' => 'blue',
            'on_hold' => 'orange',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    protected function getQuotationStatusColor(string $status): string
    {
        return match($status) {
            'pending' => 'yellow',
            'reviewed' => 'blue',
            'approved' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }

    protected function calculateUrgency(int $daysUntil): string
    {
        return match(true) {
            $daysUntil <= 1 => 'critical',
            $daysUntil <= 3 => 'high',
            $daysUntil <= 7 => 'medium',
            default => 'low',
        };
    }

    protected function getActivityPriority(string $status): string
    {
        return match($status) {
            'completed' => 'high',
            'in_progress' => 'medium',
            'on_hold' => 'high',
            'cancelled' => 'low',
            default => 'medium',
        };
    }

    protected function getQuotationPriority(Quotation $quotation): string
    {
        if ($quotation->status === 'approved' && !$quotation->client_approved) {
            return 'high';
        }
        return $quotation->priority ?? 'medium';
    }

    protected function getPriorityWeight(string $priority): int
    {
        return match($priority) {
            'critical' => 1000,
            'high' => 500,
            'medium' => 100,
            'low' => 10,
            default => 50,
        };
    }

    protected function calculateProjectProgress(Project $project): int
    {
        // Simplified progress calculation
        return match($project->status) {
            'planning' => 10,
            'in_progress' => 50,
            'on_hold' => 50,
            'completed' => 100,
            'cancelled' => 0,
            default => 0,
        };
    }

    protected function estimateCompletion(Project $project): ?Carbon
    {
        if ($project->end_date) {
            return $project->end_date;
        }

        // Estimate based on project type and current progress
        return now()->addDays(30); // Default estimation
    }

    protected function getProjectTrends(User $user): array
    {
        // Implementation for project trends over time
        return [];
    }

    protected function getQuotationTrends(User $user): array
    {
        // Implementation for quotation trends over time
        return [];
    }

    protected function getMessageTrends(User $user): array
    {
        // Implementation for message trends over time
        return [];
    }

    /**
     * Clear all cached data for a user.
     */
    public function clearCache(User $user): void
    {
        $keys = [
            "client_dashboard_data_{$user->id}",
            "client_statistics_{$user->id}",
            "client_activities_{$user->id}",
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
}