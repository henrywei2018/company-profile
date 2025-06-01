<?php

namespace App\Services;

use App\Models\User;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Message;
use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class ClientAccessService
{
    /**
     * Get projects for a client with optional filters
     */
    public function getClientProjects(User $client, array $filters = []): Builder
    {
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = Project::where('client_id', $client->id);

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['category'])) {
            $query->where('project_category_id', $filters['category']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                  ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['year'])) {
            $query->whereYear('created_at', $filters['year']);
        }

        return $query;
    }

    /**
     * Get quotations for a client with optional filters
     */
    public function getClientQuotations(User $client, array $filters = []): Builder
    {
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = Quotation::where('client_id', $client->id);

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['service'])) {
            $query->where('service_id', $filters['service']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('project_type', 'like', "%{$filters['search']}%")
                  ->orWhere('requirements', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query;
    }

    /**
     * Get messages for a client with optional filters
     */
    public function getClientMessages(User $client, array $filters = []): Builder
    {
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = Message::where('user_id', $client->id);

        // Apply filters
        if (!empty($filters['read'])) {
            if ($filters['read'] === 'read') {
                $query->where('is_read', true);
            } elseif ($filters['read'] === 'unread') {
                $query->where('is_read', false);
            }
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('subject', 'like', "%{$filters['search']}%")
                  ->orWhere('message', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query;
    }

    /**
     * Check if client can access a specific project
     */
    public function canAccessProject(User $client, Project $project): bool
    {
        return $project->client_id === $client->id;
    }

    /**
     * Check if client can access a specific quotation
     */
    public function canAccessQuotation(User $client, Quotation $quotation): bool
    {
        return $quotation->client_id === $client->id;
    }

    /**
     * Check if client can access a specific message
     */
    public function canAccessMessage(User $client, Message $message): bool
    {
        return $message->user_id === $client->id;
    }

    /**
     * Get client statistics
     */
    public function getClientStatistics(User $client): array
    {
        $cacheKey = "client_stats_{$client->id}";

        return Cache::remember($cacheKey, 300, function () use ($client) { // 5 minutes cache
            return [
                'projects' => [
                    'total' => $this->getClientProjects($client)->count(),
                    'active' => $this->getClientProjects($client)->whereIn('status', ['in_progress', 'on_hold'])->count(),
                    'completed' => $this->getClientProjects($client)->where('status', 'completed')->count(),
                    'overdue' => $this->getClientProjects($client)
                        ->where('status', 'in_progress')
                        ->where('end_date', '<', now())
                        ->whereNotNull('end_date')
                        ->count(),
                ],
                'quotations' => [
                    'total' => $this->getClientQuotations($client)->count(),
                    'pending' => $this->getClientQuotations($client)->where('status', 'pending')->count(),
                    'approved' => $this->getClientQuotations($client)->where('status', 'approved')->count(),
                    'rejected' => $this->getClientQuotations($client)->where('status', 'rejected')->count(),
                ],
                'messages' => [
                    'total' => $this->getClientMessages($client)->count(),
                    'unread' => $this->getClientMessages($client)->where('is_read', false)->count(),
                    'this_week' => $this->getClientMessages($client)
                        ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                        ->count(),
                ],
                'testimonials' => [
                    'total' => Testimonial::whereHas('project', function ($query) use ($client) {
                        $query->where('client_id', $client->id);
                    })->count(),
                    'published' => Testimonial::whereHas('project', function ($query) use ($client) {
                        $query->where('client_id', $client->id);
                    })->where('is_active', true)->count(),
                ],
            ];
        });
    }

    /**
     * Clear client cache
     */
    public function clearClientCache(User $client): void
    {
        Cache::forget("client_stats_{$client->id}");
        Cache::forget("dashboard_data_{$client->id}_client");
    }

    /**
     * Get client permissions (basic implementation)
     */
    public function getClientPermissions(User $client): array
    {
        return [
            'can_create_quotation' => true,
            'can_send_message' => true,
            'can_view_projects' => true,
            'can_leave_testimonial' => $this->canLeaveTestimonial($client),
            'can_update_profile' => true,
        ];
    }

    /**
     * Check if client can leave testimonial
     */
    protected function canLeaveTestimonial(User $client): bool
    {
        // Check if client has completed projects without testimonials
        return $this->getClientProjects($client)
            ->where('status', 'completed')
            ->whereDoesntHave('testimonial')
            ->exists();
    }

    /**
     * Get client activity summary
     */
    public function getClientActivity(User $client, int $days = 30): array
    {
        $startDate = now()->subDays($days);

        return [
            'projects_updated' => $this->getClientProjects($client)
                ->where('updated_at', '>=', $startDate)
                ->count(),
            'quotations_created' => $this->getClientQuotations($client)
                ->where('created_at', '>=', $startDate)
                ->count(),
            'messages_sent' => $this->getClientMessages($client)
                ->where('created_at', '>=', $startDate)
                ->count(),
            'projects_completed' => $this->getClientProjects($client)
                ->where('status', 'completed')
                ->where('actual_completion_date', '>=', $startDate)
                ->count(),
        ];
    }

    /**
     * Get upcoming deadlines for client
     */
    public function getUpcomingDeadlines(User $client, int $days = 30): array
    {
        return $this->getClientProjects($client)
            ->where('status', 'in_progress')
            ->where('end_date', '>', now())
            ->where('end_date', '<=', now()->addDays($days))
            ->orderBy('end_date')
            ->get()
            ->map(function ($project) {
                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'end_date' => $project->end_date,
                    'days_until' => now()->diffInDays($project->end_date, false),
                    'urgency' => $this->getUrgencyLevel($project->end_date),
                    'url' => route('client.projects.show', $project),
                ];
            })
            ->toArray();
    }

    /**
     * Get urgency level based on deadline
     */
    protected function getUrgencyLevel($deadline): string
    {
        $daysUntil = now()->diffInDays($deadline, false);

        return match (true) {
            $daysUntil <= 1 => 'critical',
            $daysUntil <= 3 => 'high',
            $daysUntil <= 7 => 'medium',
            default => 'low'
        };
    }

    /**
     * Get recent activities for dashboard
     */
    public function getRecentActivities(User $client, int $limit = 10): array
    {
        $activities = collect();

        // Recent project updates
        $projectActivities = $this->getClientProjects($client)
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($project) {
                return [
                    'type' => 'project',
                    'icon' => 'folder',
                    'color' => $this->getProjectStatusColor($project->status),
                    'title' => $project->title,
                    'description' => "Status: " . ucfirst(str_replace('_', ' ', $project->status)),
                    'date' => $project->updated_at,
                    'url' => route('client.projects.show', $project),
                ];
            });

        // Recent quotation updates
        $quotationActivities = $this->getClientQuotations($client)
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($quotation) {
                return [
                    'type' => 'quotation',
                    'icon' => 'document-text',
                    'color' => $this->getQuotationStatusColor($quotation->status),
                    'title' => $quotation->project_type,
                    'description' => "Status: " . ucfirst(str_replace('_', ' ', $quotation->status)),
                    'date' => $quotation->updated_at,
                    'url' => route('client.quotations.show', $quotation),
                ];
            });

        // Recent messages
        $messageActivities = $this->getClientMessages($client)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($message) {
                return [
                    'type' => 'message',
                    'icon' => 'mail',
                    'color' => $message->is_read ? 'gray' : 'blue',
                    'title' => $message->subject,
                    'description' => $message->is_replied ? 'Replied' : 'New message',
                    'date' => $message->created_at,
                    'url' => route('client.messages.show', $message),
                ];
            });

        return $activities
            ->concat($projectActivities)
            ->concat($quotationActivities)
            ->concat($messageActivities)
            ->sortByDesc('date')
            ->take($limit)
            ->values()
            ->toArray();
    }

    /**
     * Get project status color
     */
    protected function getProjectStatusColor(string $status): string
    {
        return match ($status) {
            'completed' => 'green',
            'in_progress' => 'blue',
            'on_hold' => 'yellow',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get quotation status color
     */
    protected function getQuotationStatusColor(string $status): string
    {
        return match ($status) {
            'approved' => 'green',
            'pending' => 'yellow',
            'reviewed' => 'blue',
            'rejected' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get notification summary for client
     */
    public function getNotificationSummary(User $client): array
    {
        return [
            'unread_count' => $client->unreadNotifications()->count(),
            'unread_messages' => $this->getClientMessages($client)->where('is_read', false)->count(),
            'pending_approvals' => $this->getClientQuotations($client)
                ->where('status', 'approved')
                ->whereNull('client_approved')
                ->count(),
            'overdue_projects' => $this->getClientProjects($client)
                ->where('status', 'in_progress')
                ->where('end_date', '<', now())
                ->whereNotNull('end_date')
                ->count(),
        ];
    }
}