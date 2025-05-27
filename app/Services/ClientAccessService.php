<?php

namespace App\Services;

use App\Models\User;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Message;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

/**
 * Client Access Management Service
 * 
 * This service handles client-specific access control and data filtering
 * to ensure clients can only access their own resources.
 */
class ClientAccessService
{
    /**
     * Check if user has client access privileges.
     * 
     * @param  User  $user
     * @return bool
     */
    public function hasClientAccess(User $user): bool
    {
        // Super admin and admin can access client area for support
        if ($user->hasAnyRole(['super-admin', 'admin'])) {
            return true;
        }

        // Manager and editor with specific permissions
        if ($user->hasAnyRole(['manager', 'editor'])) {
            return $user->can('access client area') || $user->can('view client dashboard');
        }

        // Standard client role
        if ($user->hasRole('client')) {
            return $user->is_active && ($user->is_verified ?? true);
        }

        // Custom permissions
        return $user->can('access client area') || $user->can('view client dashboard');
    }

    /**
     * Check if user can access specific resource.
     * 
     * @param  User  $user
     * @param  string  $resourceType
     * @param  mixed  $resourceId
     * @return bool
     */
    public function canAccessResource(User $user, string $resourceType, $resourceId): bool
    {
        // Admin users can access all resources
        if ($user->hasAnyRole(['super-admin', 'admin', 'manager'])) {
            return true;
        }

        return match($resourceType) {
            'project' => $this->canAccessProject($user, $resourceId),
            'quotation' => $this->canAccessQuotation($user, $resourceId),
            'message' => $this->canAccessMessage($user, $resourceId),
            'testimonial' => $this->canAccessTestimonial($user, $resourceId),
            default => false,
        };
    }

    /**
     * Get client's projects with proper filtering.
     * 
     * @param  User  $user
     * @param  array  $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getClientProjects(User $user, array $filters = [])
    {
        $query = Project::query();

        // Admin users can see all projects, clients only their own
        if (!$user->hasAnyRole(['super-admin', 'admin', 'manager'])) {
            $query->where('client_id', $user->id);
        }

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                  ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        return $query->with(['category', 'client', 'attachments'])
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Get client's quotations with proper filtering.
     * 
     * @param  User  $user
     * @param  array  $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getClientQuotations(User $user, array $filters = [])
    {
        $query = Quotation::query();

        // Admin users can see all quotations, clients only their own
        if (!$user->hasAnyRole(['super-admin', 'admin', 'manager'])) {
            $query->where('client_id', $user->id);
        }

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['service_id'])) {
            $query->where('service_id', $filters['service_id']);
        }

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                  ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        return $query->with(['service', 'client', 'attachments'])
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Get client's messages with proper filtering.
     * 
     * @param  User  $user
     * @param  array  $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getClientMessages(User $user, array $filters = [])
    {
        $query = Message::query();

        // Admin users can see all messages, clients only their own
        if (!$user->hasAnyRole(['super-admin', 'admin', 'manager'])) {
            $query->where('client_id', $user->id);
        }

        // Apply filters
        if (isset($filters['is_read'])) {
            $query->where('is_read', $filters['is_read']);
        }

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('subject', 'like', "%{$filters['search']}%")
                  ->orWhere('message', 'like', "%{$filters['search']}%");
            });
        }

        return $query->with(['client', 'replies'])
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Get client dashboard statistics.
     * 
     * @param  User  $user
     * @return array
     */
    public function getClientDashboardStats(User $user): array
    {
        $cacheKey = "client_stats_{$user->id}";
        
        return Cache::remember($cacheKey, 300, function () use ($user) {
            $baseQuery = $user->hasAnyRole(['super-admin', 'admin', 'manager']) 
                ? [] // Admin can see all
                : ['client_id' => $user->id]; // Client sees only their own

            return [
                'projects' => [
                    'total' => Project::where($baseQuery)->count(),
                    'active' => Project::where($baseQuery)->whereIn('status', ['in_progress', 'on_hold'])->count(),
                    'completed' => Project::where($baseQuery)->where('status', 'completed')->count(),
                    'pending' => Project::where($baseQuery)->where('status', 'pending')->count(),
                ],
                'quotations' => [
                    'total' => Quotation::where($baseQuery)->count(),
                    'pending' => Quotation::where($baseQuery)->where('status', 'pending')->count(),
                    'approved' => Quotation::where($baseQuery)->where('status', 'approved')->count(),
                    'under_review' => Quotation::where($baseQuery)->where('status', 'under_review')->count(),
                ],
                'messages' => [
                    'total' => Message::where($baseQuery)->count(),
                    'unread' => Message::where($baseQuery)->where('is_read', false)->count(),
                    'replied' => Message::where($baseQuery)->whereHas('replies')->count(),
                ],
                'recent_activity' => $this->getRecentActivity($user),
            ];
        });
    }

    /**
     * Get recent activity for client dashboard.
     * 
     * @param  User  $user
     * @return Collection
     */
    public function getRecentActivity(User $user): Collection
    {
        $activities = collect();
        $isAdmin = $user->hasAnyRole(['super-admin', 'admin', 'manager']);
        $baseCondition = $isAdmin ? [] : ['client_id' => $user->id];

        // Recent project updates
        $recentProjects = Project::where($baseCondition)
            ->select('id', 'title', 'status', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($project) {
                return [
                    'type' => 'project',
                    'title' => 'Project Updated: ' . $project->title,
                    'description' => 'Status: ' . ucfirst(str_replace('_', ' ', $project->status)),
                    'date' => $project->updated_at,
                    'url' => route('client.projects.show', $project->id),
                    'icon' => 'folder',
                    'color' => $this->getProjectStatusColor($project->status),
                ];
            });

        // Recent quotation updates
        $recentQuotations = Quotation::where($baseCondition)
            ->select('id', 'title', 'status', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($quotation) {
                return [
                    'type' => 'quotation',
                    'title' => 'Quotation Updated: ' . ($quotation->title ?? 'Quotation #' . $quotation->id),
                    'description' => 'Status: ' . ucfirst(str_replace('_', ' ', $quotation->status)),
                    'date' => $quotation->updated_at,
                    'url' => route('client.quotations.show', $quotation->id),
                    'icon' => 'document-text',
                    'color' => $this->getQuotationStatusColor($quotation->status),
                ];
            });

        // Recent messages
        $recentMessages = Message::where($baseCondition)
            ->select('id', 'subject', 'is_read', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($message) {
                return [
                    'type' => 'message',
                    'title' => 'Message: ' . ($message->subject ?? 'No Subject'),
                    'description' => $message->is_read ? 'Read' : 'Unread',
                    'date' => $message->updated_at,
                    'url' => route('client.messages.show', $message->id),
                    'icon' => 'mail',
                    'color' => $message->is_read ? 'gray' : 'blue',
                ];
            });

        return $activities->concat($recentProjects)
                         ->concat($recentQuotations)
                         ->concat($recentMessages)
                         ->sortByDesc('date')
                         ->take(10)
                         ->values();
    }

    /**
     * Check if user can access specific project.
     * 
     * @param  User  $user
     * @param  mixed  $projectId
     * @return bool
     */
    private function canAccessProject(User $user, $projectId): bool
    {
        return Project::where('id', $projectId)
            ->where('client_id', $user->id)
            ->exists();
    }

    /**
     * Check if user can access specific quotation.
     * 
     * @param  User  $user
     * @param  mixed  $quotationId
     * @return bool
     */
    private function canAccessQuotation(User $user, $quotationId): bool
    {
        return Quotation::where('id', $quotationId)
            ->where('client_id', $user->id)
            ->exists();
    }

    /**
     * Check if user can access specific message.
     * 
     * @param  User  $user
     * @param  mixed  $messageId
     * @return bool
     */
    private function canAccessMessage(User $user, $messageId): bool
    {
        return Message::where('id', $messageId)
            ->where('client_id', $user->id)
            ->exists();
    }

    /**
     * Check if user can access specific testimonial.
     * 
     * @param  User  $user
     * @param  mixed  $testimonialId
     * @return bool
     */
    private function canAccessTestimonial(User $user, $testimonialId): bool
    {
        return Testimonial::where('id', $testimonialId)
            ->where('client_id', $user->id)
            ->exists();
    }

    /**
     * Get color class for project status.
     * 
     * @param  string  $status
     * @return string
     */
    private function getProjectStatusColor(string $status): string
    {
        return match($status) {
            'pending' => 'yellow',
            'in_progress' => 'blue',
            'on_hold' => 'orange',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get color class for quotation status.
     * 
     * @param  string  $status
     * @return string
     */
    private function getQuotationStatusColor(string $status): string
    {
        return match($status) {
            'pending' => 'yellow',
            'under_review' => 'blue',
            'approved' => 'green',
            'rejected' => 'red',
            'expired' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Clear client-specific caches.
     * 
     * @param  User  $user
     * @return void
     */
    public function clearClientCache(User $user): void
    {
        Cache::forget("client_stats_{$user->id}");
        Cache::forget("client_projects_{$user->id}");
        Cache::forget("client_quotations_{$user->id}");
        Cache::forget("client_messages_{$user->id}");
    }

    /**
     * Log client access attempt.
     * 
     * @param  User  $user
     * @param  string  $resource
     * @param  string  $action
     * @param  bool  $success
     * @param  array  $context
     * @return void
     */
    public function logClientAccess(User $user, string $resource, string $action, bool $success, array $context = []): void
    {
        $logData = [
            'user_id' => $user->id,
            'email' => $user->email,
            'resource' => $resource,
            'action' => $action,
            'success' => $success,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
            'context' => $context,
        ];

        if ($success) {
            \Log::info("Client access granted", $logData);
        } else {
            \Log::warning("Client access denied", $logData);
        }
    }

    /**
     * Check if client needs verification.
     * 
     * @param  User  $user
     * @return bool
     */
    public function needsVerification(User $user): bool
    {
        return $user->hasRole('client') && 
               property_exists($user, 'is_verified') && 
               !$user->is_verified;
    }

    /**
     * Get client navigation menu items based on permissions.
     * 
     * @param  User  $user
     * @return array
     */
    public function getClientNavigationMenu(User $user): array
    {
        $menu = [
            [
                'name' => 'Dashboard',
                'route' => 'client.dashboard',
                'icon' => 'home',
                'active' => request()->routeIs('client.dashboard'),
            ],
        ];

        // Projects menu
        if ($user->can('view projects') || $user->hasRole('client')) {
            $menu[] = [
                'name' => 'Projects',
                'route' => 'client.projects.index',
                'icon' => 'folder',
                'active' => request()->routeIs('client.projects.*'),
                'badge' => $this->getActiveProjectsCount($user),
            ];
        }

        // Quotations menu
        if ($user->can('view quotations') || $user->hasRole('client')) {
            $menu[] = [
                'name' => 'Quotations',
                'route' => 'client.quotations.index',
                'icon' => 'document-text',
                'active' => request()->routeIs('client.quotations.*'),
                'badge' => $this->getPendingQuotationsCount($user),
                'submenu' => [
                    [
                        'name' => 'All Quotations',
                        'route' => 'client.quotations.index',
                    ],
                    [
                        'name' => 'Request Quote',
                        'route' => 'client.quotations.create',
                    ],
                ],
            ];
        }

        // Messages menu
        if ($user->can('view messages') || $user->hasRole('client')) {
            $menu[] = [
                'name' => 'Messages',
                'route' => 'client.messages.index',
                'icon' => 'mail',
                'active' => request()->routeIs('client.messages.*'),
                'badge' => $this->getUnreadMessagesCount($user),
                'submenu' => [
                    [
                        'name' => 'All Messages',
                        'route' => 'client.messages.index',
                    ],
                    [
                        'name' => 'Send Message',
                        'route' => 'client.messages.create',
                    ],
                ],
            ];
        }

        // Testimonials menu
        if ($user->can('create testimonials') || $user->hasRole('client')) {
            $menu[] = [
                'name' => 'Testimonials',
                'route' => 'client.testimonials.index',
                'icon' => 'star',
                'active' => request()->routeIs('client.testimonials.*'),
            ];
        }

        return $menu;
    }

    /**
     * Get active projects count for badge.
     * 
     * @param  User  $user
     * @return int
     */
    private function getActiveProjectsCount(User $user): int
    {
        $query = Project::whereIn('status', ['in_progress', 'on_hold']);
        
        if (!$user->hasAnyRole(['super-admin', 'admin', 'manager'])) {
            $query->where('client_id', $user->id);
        }
        
        return $query->count();
    }

    /**
     * Get pending quotations count for badge.
     * 
     * @param  User  $user
     * @return int
     */
    private function getPendingQuotationsCount(User $user): int
    {
        $query = Quotation::where('status', 'pending');
        
        if (!$user->hasAnyRole(['super-admin', 'admin', 'manager'])) {
            $query->where('client_id', $user->id);
        }
        
        return $query->count();
    }

    /**
     * Get unread messages count for badge.
     * 
     * @param  User  $user
     * @return int
     */
    private function getUnreadMessagesCount(User $user): int
    {
        $query = Message::where('is_read', false);
        
        if (!$user->hasAnyRole(['super-admin', 'admin', 'manager'])) {
            $query->where('client_id', $user->id);
        }
        
        return $query->count();
    }

    /**
     * Get client permissions summary.
     * 
     * @param  User  $user
     * @return array
     */
    public function getClientPermissions(User $user): array
    {
        return [
            'can_view_projects' => $user->can('view projects') || $user->hasRole('client'),
            'can_create_quotations' => $user->can('create quotations') || $user->hasRole('client'),
            'can_view_quotations' => $user->can('view quotations') || $user->hasRole('client'),
            'can_approve_quotations' => $user->can('approve quotations') || $user->hasRole('client'),
            'can_view_messages' => $user->can('view messages') || $user->hasRole('client'),
            'can_create_messages' => $user->can('create messages') || $user->hasRole('client'),
            'can_reply_messages' => $user->can('reply messages') || $user->hasRole('client'),
            'can_create_testimonials' => $user->can('create testimonials') || $user->hasRole('client'),
            'can_access_chat' => true, // All authenticated users can access chat
            'is_admin_viewing' => $user->hasAnyRole(['super-admin', 'admin', 'manager', 'editor']),
        ];
    }

    /**
     * Validate client resource access and throw appropriate exceptions.
     * 
     * @param  User  $user
     * @param  string  $resourceType
     * @param  mixed  $resourceId
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function validateClientResourceAccess(User $user, string $resourceType, $resourceId): void
    {
        if (!$this->canAccessResource($user, $resourceType, $resourceId)) {
            $this->logClientAccess($user, $resourceType, 'access_denied', false, [
                'resource_id' => $resourceId,
                'reason' => 'insufficient_permissions'
            ]);
            
            abort(403, "You don't have permission to access this {$resourceType}.");
        }
        
        $this->logClientAccess($user, $resourceType, 'access_granted', true, [
            'resource_id' => $resourceId
        ]);
    }
}