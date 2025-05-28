<?php
// File: app/Http/Controllers/Client/DashboardController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Message;
use App\Models\Quotation;
use App\Models\User;
use App\Services\ClientAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected ClientAccessService $clientAccessService;

    public function __construct(ClientAccessService $clientAccessService)
    {
        $this->clientAccessService = $clientAccessService;
    }

    /**
     * Display the client dashboard.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get dashboard statistics (properly synced with schema)
        $statistics = $this->getDashboardStatistics($user);
        
        // Get recent activities 
        $recentActivities = $this->getRecentActivities($user);
        
        // Get upcoming deadlines
        $upcomingDeadlines = $this->getUpcomingDeadlines($user);
        
        // Get notifications
        $notifications = $this->getNotifications($user);
        
        // Get quick actions available to user
        $quickActions = $this->getQuickActions($user);

        return view('client.dashboard', compact(
            'statistics',
            'recentActivities', 
            'upcomingDeadlines',
            'notifications',
            'quickActions'
        ));
    }

    /**
     * Get dashboard statistics properly synced with database schema.
     */
    protected function getDashboardStatistics(User $user): array
    {
        $cacheKey = "client_dashboard_stats_{$user->id}";
        
        return Cache::remember($cacheKey, 300, function () use ($user) {
            // Base conditions for client access
            $isAdmin = $user->hasAnyRole(['super-admin', 'admin', 'manager']);
            
            // Projects statistics (using client_id as per schema)
            $projectsQuery = Project::query();
            if (!$isAdmin) {
                $projectsQuery->where('client_id', $user->id);
            }
            
            // Quotations statistics (using client_id as per schema)
            $quotationsQuery = Quotation::query();
            if (!$isAdmin) {
                $quotationsQuery->where('client_id', $user->id);
            }
            
            // Messages statistics (using user_id as per schema - messages table uses user_id)
            $messagesQuery = Message::query();
            if (!$isAdmin) {
                // Messages table uses user_id, not client_id
                $messagesQuery->where('user_id', $user->id);
            }

            return [
                'projects' => [
                    'total' => $projectsQuery->count(),
                    'active' => (clone $projectsQuery)->whereIn('status', ['in_progress', 'on_hold'])->count(),
                    'completed' => (clone $projectsQuery)->where('status', 'completed')->count(),
                    'pending' => (clone $projectsQuery)->where('status', 'planning')->count(),
                    'overdue' => (clone $projectsQuery)
                        ->where('status', 'in_progress')
                        ->where('end_date', '<', now())
                        ->whereNotNull('end_date')
                        ->count(),
                ],
                'quotations' => [
                    'total' => $quotationsQuery->count(),
                    'pending' => (clone $quotationsQuery)->where('status', 'pending')->count(),
                    'reviewed' => (clone $quotationsQuery)->where('status', 'reviewed')->count(),
                    'approved' => (clone $quotationsQuery)->where('status', 'approved')->count(),
                    'awaiting_approval' => (clone $quotationsQuery)
                        ->where('status', 'approved')
                        ->whereNull('client_approved')
                        ->count(),
                ],
                'messages' => [
                    'total' => $messagesQuery->count(),
                    'unread' => (clone $messagesQuery)->where('is_read', false)->count(),
                    'replied' => (clone $messagesQuery)->where('is_replied', true)->count(),
                    'this_week' => (clone $messagesQuery)
                        ->whereBetween('created_at', [
                            Carbon::now()->startOfWeek(),
                            Carbon::now()->endOfWeek()
                        ])
                        ->count(),
                ],
                'summary' => [
                    'active_projects_value' => (clone $projectsQuery)
                        ->whereIn('status', ['in_progress', 'on_hold'])
                        ->whereNotNull('value')
                        ->count(), // Could be sum if value is numeric
                    'completion_rate' => $this->calculateCompletionRate($projectsQuery),
                    'response_rate' => $this->calculateResponseRate($messagesQuery),
                ]
            ];
        });
    }

    /**
     * Get recent activities properly synced with schema.
     */
    protected function getRecentActivities(User $user): array
    {
        $activities = [];
        $isAdmin = $user->hasAnyRole(['super-admin', 'admin', 'manager']);

        // Recent project updates (using client_id)
        $recentProjects = Project::query()
            ->when(!$isAdmin, fn($q) => $q->where('client_id', $user->id))
            ->with(['category', 'images' => fn($q) => $q->where('is_featured', true)])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentProjects as $project) {
            $activities[] = [
                'type' => 'project',
                'title' => 'Project: ' . $project->title,
                'description' => 'Status: ' . ucfirst(str_replace('_', ' ', $project->status)),
                'date' => $project->updated_at,
                'url' => route('client.projects.show', $project->id),
                'icon' => 'folder',
                'color' => $this->getProjectStatusColor($project->status),
                'meta' => [
                    'status' => $project->status,
                    'category' => $project->category?->name,
                    'location' => $project->location,
                ]
            ];
        }

        // Recent quotation updates (using client_id)
        $recentQuotations = Quotation::query()
            ->when(!$isAdmin, fn($q) => $q->where('client_id', $user->id))
            ->with('service')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentQuotations as $quotation) {
            $activities[] = [
                'type' => 'quotation',
                'title' => 'Quotation: ' . ($quotation->project_type ?? 'Quote #' . $quotation->id),
                'description' => 'Status: ' . ucfirst($quotation->status),
                'date' => $quotation->updated_at,
                'url' => route('client.quotations.show', $quotation->id),
                'icon' => 'document-text',
                'color' => $this->getQuotationStatusColor($quotation->status),
                'meta' => [
                    'status' => $quotation->status,
                    'service' => $quotation->service?->title,
                    'priority' => $quotation->priority,
                ]
            ];
        }

        // Recent messages (using user_id as per messages table schema)
        $recentMessages = Message::query()
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $user->id))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentMessages as $message) {
            $activities[] = [
                'type' => 'message',
                'title' => 'Message: ' . ($message->subject ?? 'No Subject'),
                'description' => $message->is_read ? 'Read' : 'Unread',
                'date' => $message->created_at,
                'url' => route('client.messages.show', $message->id),
                'icon' => 'mail',
                'color' => $message->is_read ? 'gray' : 'blue',
                'meta' => [
                    'is_read' => $message->is_read,
                    'type' => $message->type,
                    'replied' => $message->is_replied,
                ]
            ];
        }

        // Sort all activities by date and return latest 10
        return collect($activities)
            ->sortByDesc('date')
            ->take(10)
            ->values()
            ->toArray();
    }

    /**
     * Get upcoming deadlines.
     */
    protected function getUpcomingDeadlines(User $user): array
    {
        $isAdmin = $user->hasAnyRole(['super-admin', 'admin', 'manager']);
        
        return Project::query()
            ->when(!$isAdmin, fn($q) => $q->where('client_id', $user->id))
            ->where('status', 'in_progress')
            ->whereNotNull('end_date')
            ->where('end_date', '>', now())
            ->where('end_date', '<=', now()->addDays(30))
            ->orderBy('end_date')
            ->get()
            ->map(function ($project) {
                $daysUntil = now()->diffInDays($project->end_date, false);
                
                return [
                    'type' => 'project_deadline',
                    'title' => $project->title,
                    'date' => $project->end_date,
                    'days_until' => $daysUntil,
                    'url' => route('client.projects.show', $project->id),
                    'urgency' => $this->calculateUrgency($daysUntil),
                    'location' => $project->location,
                ];
            })
            ->toArray();
    }

    /**
     * Get notifications for client.
     */
    protected function getNotifications(User $user): array
    {
        $isAdmin = $user->hasAnyRole(['super-admin', 'admin', 'manager']);
        
        return [
            'unread_messages' => Message::query()
                ->when(!$isAdmin, fn($q) => $q->where('user_id', $user->id))
                ->where('is_read', false)
                ->count(),
            'pending_approvals' => Quotation::query()
                ->when(!$isAdmin, fn($q) => $q->where('client_id', $user->id))
                ->where('status', 'approved')
                ->whereNull('client_approved')
                ->count(),
            'overdue_projects' => Project::query()
                ->when(!$isAdmin, fn($q) => $q->where('client_id', $user->id))
                ->where('status', 'in_progress')
                ->where('end_date', '<', now())
                ->whereNotNull('end_date')
                ->count(),
            'expiring_quotations' => Quotation::query()
                ->when(!$isAdmin, fn($q) => $q->where('client_id', $user->id))
                ->where('status', 'approved')
                ->whereNotNull('approved_at')
                ->where('approved_at', '<', now()->subDays(25)) // Assume 30-day validity
                ->count(),
        ];
    }

    /**
     * Get quick actions available to user.
     */
    protected function getQuickActions(User $user): array
    {
        $actions = [];

        if ($user->can('create quotations') || $user->hasRole('client')) {
            $actions[] = [
                'title' => 'Request Quote',
                'description' => 'Submit a new quotation request',
                'url' => route('client.quotations.create'),
                'icon' => 'document-add',
                'color' => 'blue',
            ];
        }

        if ($user->can('create messages') || $user->hasRole('client')) {
            $actions[] = [
                'title' => 'Send Message',
                'description' => 'Contact our support team',
                'url' => route('client.messages.create'),
                'icon' => 'mail',
                'color' => 'green',
            ];
        }

        if ($user->can('create testimonials') || $user->hasRole('client')) {
            $actions[] = [
                'title' => 'Leave Review',
                'description' => 'Share your experience',
                'url' => route('client.testimonials.create'),
                'icon' => 'star',
                'color' => 'yellow',
            ];
        }

        $actions[] = [
            'title' => 'View Portfolio',
            'description' => 'Browse our completed projects',
            'url' => route('portfolio.index'),
            'icon' => 'photograph',
            'color' => 'purple',
        ];

        return $actions;
    }

    /**
     * Calculate project completion rate.
     */
    protected function calculateCompletionRate($projectsQuery): float
    {
        $total = (clone $projectsQuery)->count();
        if ($total === 0) return 0;
        
        $completed = (clone $projectsQuery)->where('status', 'completed')->count();
        return round(($completed / $total) * 100, 1);
    }

    /**
     * Calculate message response rate.
     */
    protected function calculateResponseRate($messagesQuery): float
    {
        $total = (clone $messagesQuery)->count();
        if ($total === 0) return 0;
        
        $replied = (clone $messagesQuery)->where('is_replied', true)->count();
        return round(($replied / $total) * 100, 1);
    }

    /**
     * Get color for project status.
     */
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

    /**
     * Get color for quotation status.
     */
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

    /**
     * Calculate urgency based on days until deadline.
     */
    protected function calculateUrgency(int $daysUntil): string
    {
        return match(true) {
            $daysUntil <= 1 => 'critical',
            $daysUntil <= 3 => 'high',
            $daysUntil <= 7 => 'medium',
            default => 'low',
        };
    }
}