<?php
// File: app/Services/ClientAccessService.php

namespace App\Services;

use App\Models\User;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Message;
use Illuminate\Database\Eloquent\Builder;

class ClientAccessService
{
    /**
     * Get projects accessible to the client.
     */
    public function getClientProjects(User $user, array $filters = []): Builder
    {
        $query = Project::query();
        
        // Apply client access control
        if (!$user->hasAnyRole(['super-admin', 'admin', 'manager'])) {
            $query->where('client_id', $user->id);
        }
        
        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (!empty($filters['category_id'])) {
            $query->where('project_category_id', $filters['category_id']);
        }
        
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('location', 'like', '%' . $filters['search'] . '%');
            });
        }
        
        if (!empty($filters['year'])) {
            $query->whereYear('created_at', $filters['year']);
        }
        
        return $query->orderBy('updated_at', 'desc');
    }
    
    /**
     * Get quotations accessible to the client.
     */
    public function getClientQuotations(User $user, array $filters = []): Builder
    {
        $query = Quotation::query();
        
        // Apply client access control
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
                $q->where('project_type', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('requirements', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('location', 'like', '%' . $filters['search'] . '%');
            });
        }
        
        return $query->orderBy('created_at', 'desc');
    }
    
    /**
     * Get messages accessible to the client.
     */
    public function getClientMessages(User $user, array $filters = []): Builder
    {
        $query = Message::query();
        
        // Apply client access control - messages table uses user_id
        if (!$user->hasAnyRole(['super-admin', 'admin', 'manager'])) {
            $query->where('user_id', $user->id);
        }
        
        // Apply filters
        if (!empty($filters['read'])) {
            $isRead = $filters['read'] === 'read';
            $query->where('is_read', $isRead);
        }
        
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('subject', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('message', 'like', '%' . $filters['search'] . '%');
            });
        }
        
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        
        return $query->orderBy('created_at', 'desc');
    }
    
    /**
     * Check if user can access a specific project.
     */
    public function canAccessProject(User $user, Project $project): bool
    {
        // Admin users can access all projects
        if ($user->hasAnyRole(['super-admin', 'admin', 'manager'])) {
            return true;
        }
        
        // Client can only access their own projects
        return $project->client_id === $user->id;
    }
    
    /**
     * Check if user can access a specific quotation.
     */
    public function canAccessQuotation(User $user, Quotation $quotation): bool
    {
        // Admin users can access all quotations
        if ($user->hasAnyRole(['super-admin', 'admin', 'manager'])) {
            return true;
        }
        
        // Client can only access their own quotations
        return $quotation->client_id === $user->id;
    }
    
    /**
     * Check if user can access a specific message.
     */
    public function canAccessMessage(User $user, Message $message): bool
    {
        // Admin users can access all messages
        if ($user->hasAnyRole(['super-admin', 'admin', 'manager'])) {
            return true;
        }
        
        // Client can only access their own messages
        return $message->user_id === $user->id;
    }
    
    /**
     * Get client statistics.
     */
    public function getClientStatistics(User $user): array
    {
        $stats = [];
        
        // Project statistics
        $projectQuery = $this->getClientProjects($user);
        $stats['projects'] = [
            'total' => (clone $projectQuery)->count(),
            'active' => (clone $projectQuery)->whereIn('status', ['in_progress', 'on_hold'])->count(),
            'completed' => (clone $projectQuery)->where('status', 'completed')->count(),
            'overdue' => (clone $projectQuery)
                ->where('status', 'in_progress')
                ->where('end_date', '<', now())
                ->whereNotNull('end_date')
                ->count(),
        ];
        
        // Quotation statistics
        $quotationQuery = $this->getClientQuotations($user);
        $stats['quotations'] = [
            'total' => (clone $quotationQuery)->count(),
            'pending' => (clone $quotationQuery)->where('status', 'pending')->count(),
            'approved' => (clone $quotationQuery)->where('status', 'approved')->count(),
            'awaiting_approval' => (clone $quotationQuery)
                ->where('status', 'approved')
                ->whereNull('client_approved')
                ->count(),
        ];
        
        // Message statistics
        $messageQuery = $this->getClientMessages($user);
        $stats['messages'] = [
            'total' => (clone $messageQuery)->count(),
            'unread' => (clone $messageQuery)->where('is_read', false)->count(),
            'replied' => (clone $messageQuery)->where('is_replied', true)->count(),
        ];
        
        return $stats;
    }
    public function clearClientCache(User $client): void
    {
        try {
            \Illuminate\Support\Facades\Cache::forget("client_statistics_{$client->id}");
            \Illuminate\Support\Facades\Cache::forget("client_projects_{$client->id}");
            \Illuminate\Support\Facades\Cache::forget("client_quotations_{$client->id}");
            \Illuminate\Support\Facades\Cache::forget("client_messages_{$client->id}");
            \Illuminate\Support\Facades\Cache::forget("client_notifications_{$client->id}");
            
            \Log::info('Client cache cleared', [
                'client_id' => $client->id
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to clear client cache', [
                'client_id' => $client->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Check if user has general client access.
     */
    public function hasClientAccess(User $user): bool
    {
        return $user->hasRole('client') && $user->is_active;
    }

    /**
     * Generic method to check if user can access a resource.
     */
    public function canAccessResource(User $user, string $resourceType, $resourceId): bool
    {
        // Admin users can access all resources
        if ($user->hasAnyRole(['super-admin', 'admin', 'manager'])) {
            return true;
        }

        switch ($resourceType) {
            case 'project':
                $project = is_object($resourceId) ? $resourceId : Project::find($resourceId);
                return $project ? $this->canAccessProject($user, $project) : false;
                
            case 'quotation':
                $quotation = is_object($resourceId) ? $resourceId : Quotation::find($resourceId);
                return $quotation ? $this->canAccessQuotation($user, $quotation) : false;
                
            case 'message':
                $message = is_object($resourceId) ? $resourceId : Message::find($resourceId);
                return $message ? $this->canAccessMessage($user, $message) : false;
                
            default:
                return false;
        }
    }

    /**
     * Get client navigation menu items.
     */
    public function getClientNavigationMenu(User $user): array
    {
        try {
            $projectsQuery = $this->getClientProjects($user);
            $quotationsQuery = $this->getClientQuotations($user);
            $messagesQuery = $this->getClientMessages($user);
            
            return [
                [
                    'title' => 'Dashboard',
                    'route' => 'client.dashboard',
                    'icon' => 'home',
                    'active' => request()->routeIs('client.dashboard'),
                ],
                [
                    'title' => 'Projects',
                    'route' => 'client.projects.index',
                    'icon' => 'folder',
                    'active' => request()->routeIs('client.projects.*'),
                    'badge' => $projectsQuery->count(),
                ],
                [
                    'title' => 'Quotations',
                    'route' => 'client.quotations.index',
                    'icon' => 'document-text',
                    'active' => request()->routeIs('client.quotations.*'),
                    'badge' => $quotationsQuery->count(),
                ],
                [
                    'title' => 'Messages',
                    'route' => 'client.messages.index',
                    'icon' => 'mail',
                    'active' => request()->routeIs('client.messages.*'),
                    'badge' => $messagesQuery->where('is_read', false)->count(),
                ],
                [
                    'title' => 'Profile',
                    'route' => 'client.profile.edit',
                    'icon' => 'user',
                    'active' => request()->routeIs('client.profile.*'),
                ],
            ];
        } catch (\Exception $e) {
            \Log::error('Error getting client navigation menu', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            // Return basic navigation without badges
            return [
                [
                    'title' => 'Dashboard',
                    'route' => 'client.dashboard',
                    'icon' => 'home',
                    'active' => request()->routeIs('client.dashboard'),
                ],
                [
                    'title' => 'Projects',
                    'route' => 'client.projects.index',
                    'icon' => 'folder',
                    'active' => request()->routeIs('client.projects.*'),
                ],
                [
                    'title' => 'Quotations',
                    'route' => 'client.quotations.index',
                    'icon' => 'document-text',
                    'active' => request()->routeIs('client.quotations.*'),
                ],
                [
                    'title' => 'Messages',
                    'route' => 'client.messages.index',
                    'icon' => 'mail',
                    'active' => request()->routeIs('client.messages.*'),
                ],
                [
                    'title' => 'Profile',
                    'route' => 'client.profile.edit',
                    'icon' => 'user',
                    'active' => request()->routeIs('client.profile.*'),
                ],
            ];
        }
    }

    /**
     * Get client permissions.
     */
    public function getClientPermissions(User $user): array
    {
        return [
            'can_view_projects' => $user->can('view projects'),
            'can_create_quotations' => $user->can('create quotations'),
            'can_send_messages' => $user->can('send messages'),
            'can_update_profile' => $user->can('update profile'),
            'can_download_files' => $user->can('download files'),
            'can_view_invoices' => $user->can('view invoices'),
        ];
    }
}