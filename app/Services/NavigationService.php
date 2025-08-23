<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class NavigationService
{
    /**
     * Get admin navigation links
     */
    public function getAdminNavigation(): array
    {
        $user = Auth::user();
        
        return [
            // Dashboard
            [
                'title' => 'Dashboard',
                'route' => 'admin.dashboard',
                'icon' => 'dashboard',
                'active' => request()->routeIs('admin.dashboard'),
                'permission' => null,
            ],

            // Company Profile  
            [
                'title' => 'Company Profile',
                'icon' => 'building',
                'active' => request()->routeIs(['admin.company.*', 'admin.certifications.*']),
                'permission' => 'view company profile',
                'children' => [
                    [
                        'title' => 'Company Info',
                        'route' => 'admin.company.index',
                        'active' => request()->routeIs('admin.company.index'),
                        'permission' => 'view company profile',
                    ],
                    [
                        'title' => 'Certifications',
                        'route' => 'admin.certifications.index',
                        'active' => request()->routeIs('admin.certifications.*'),
                        'permission' => 'view certifications',
                    ],
                ],
            ],

            // Blog/Posts
            [
                'title' => 'Blog',
                'icon' => 'blog',
                'active' => request()->routeIs(['admin.posts.*', 'admin.post-categories.*']),
                'permission' => 'view posts',
                'badge' => $this->getDraftPostsCount(),
                'children' => [
                    [
                        'title' => 'All Posts',
                        'route' => 'admin.posts.index',
                        'active' => request()->routeIs('admin.posts.index'),
                        'permission' => 'view posts',
                    ],
                    [
                        'title' => 'Add New Post',
                        'route' => 'admin.posts.create',
                        'active' => request()->routeIs('admin.posts.create'),
                        'permission' => 'create posts',
                    ],
                    [
                        'title' => 'Categories',
                        'route' => 'admin.post-categories.index',
                        'active' => request()->routeIs('admin.post-categories.*'),
                        'permission' => 'view post categories',
                    ],
                ],
            ],

            // Banners
            [
                'title' => 'Banners',
                'icon' => 'banners',
                'active' => request()->routeIs(['admin.banners.*', 'admin.banner-categories.*']),
                'permission' => 'view banners',
                'children' => [
                    [
                        'title' => 'All Banners',
                        'route' => 'admin.banners.index',
                        'active' => request()->routeIs('admin.banners.index'),
                        'permission' => 'view banners',
                    ],
                    [
                        'title' => 'Add New Banner',
                        'route' => 'admin.banners.create',
                        'active' => request()->routeIs('admin.banners.create'),
                        'permission' => 'create banners',
                    ],
                    [
                        'title' => 'Banner Categories',
                        'route' => 'admin.banner-categories.index',
                        'active' => request()->routeIs('admin.banner-categories.*'),
                        'permission' => 'view banner categories',
                    ],
                ],
            ],

            // Services
            [
                'title' => 'Services',
                'icon' => 'services',
                'active' => request()->routeIs(['admin.services.*', 'admin.service-categories.*']),
                'permission' => 'view services',
                'children' => [
                    [
                        'title' => 'Services List',
                        'route' => 'admin.services.index',
                        'active' => request()->routeIs('admin.services.index'),
                        'permission' => 'view services',
                    ],
                    [
                        'title' => 'Add New Service',
                        'route' => 'admin.services.create',
                        'active' => request()->routeIs('admin.services.create'),
                        'permission' => 'create services',
                    ],
                    [
                        'title' => 'Service Categories',
                        'route' => 'admin.service-categories.index',
                        'active' => request()->routeIs('admin.service-categories.*'),
                        'permission' => 'view service categories',
                    ],
                ],
            ],
            // Products
            [
                'title' => 'Products',
                'icon' => 'products',
                'active' => request()->routeIs(['admin.products.*', 'admin.product-categories.*']),
                'permission' => 'view products',
                'children' => [
                    [
                        'title' => 'Products List',
                        'route' => 'admin.products.index',
                        'active' => request()->routeIs('admin.products.index'),
                        'permission' => 'view products',
                    ],
                    [
                        'title' => 'Add New Product',
                        'route' => 'admin.products.create',
                        'active' => request()->routeIs('admin.products.create'),
                        'permission' => 'create products',
                    ],
                    [
                        'title' => 'Product Categories',
                        'route' => 'admin.product-categories.index',
                        'active' => request()->routeIs('admin.product-categories.*'),
                        'permission' => 'view product categories',
                    ],
                ],
            ],

            // Orders
            [
                'title' => 'Orders',
                'icon' => 'orders',
                'active' => request()->routeIs('admin.orders.*'),
                'permission' => 'view orders',
                'badge' => $this->getPendingOrdersCount(),
                'children' => [
                    [
                        'title' => 'All Orders',
                        'route' => 'admin.orders.index',
                        'active' => request()->routeIs('admin.orders.index'),
                        'permission' => 'view orders',
                        'badge' => $this->getPendingOrdersCount(),
                    ],
                    [
                        'title' => 'Payment Management',
                        'route' => 'admin.orders.payments.index',
                        'active' => request()->routeIs('admin.orders.payments.*'),
                        'permission' => 'view orders',
                        'badge' => $this->getPendingPaymentsCount(),
                    ],
                ],
            ],

            // Projects
            [
                'title' => 'Projects',
                'icon' => 'projects',
                'active' => request()->routeIs(['admin.projects.*', 'admin.project-categories.*']),
                'permission' => 'view projects',
                'children' => [
                    [
                        'title' => 'Projects List',
                        'route' => 'admin.projects.index',
                        'active' => request()->routeIs('admin.projects.index'),
                        'permission' => 'view projects',
                    ],
                    [
                        'title' => 'Project Categories',
                        'route' => 'admin.project-categories.index',
                        'active' => request()->routeIs('admin.project-categories.*'),
                        'permission' => 'view project categories',
                    ],
                    [
                        'title' => 'Add New Project',
                        'route' => 'admin.projects.create',
                        'active' => request()->routeIs('admin.projects.create'),
                        'permission' => 'create projects',
                    ],
                ],
            ],

            // Team
            [
                'title' => 'Team',
                'icon' => 'team',
                'active' => request()->routeIs(['admin.team.*', 'admin.team-member-departments.*']),
                'permission' => 'view team',
                'children' => [
                    [
                        'title' => 'Team Members',
                        'route' => 'admin.team.index',
                        'active' => request()->routeIs('admin.team.index'),
                        'permission' => 'view team',
                    ],
                    [
                        'title' => 'Add Team Member',
                        'route' => 'admin.team.create',
                        'active' => request()->routeIs('admin.team.create'),
                        'permission' => 'create team',
                    ],
                    [
                        'title' => 'Departments',
                        'route' => 'admin.team-member-departments.index',
                        'active' => request()->routeIs('admin.team-member-departments.*'),
                        'permission' => 'view departments',
                    ],
                ],
            ],

            // Testimonials
            [
                'title' => 'Testimonials',
                'route' => 'admin.testimonials.index',
                'icon' => 'testimonials',
                'active' => request()->routeIs('admin.testimonials.*'),
                'permission' => 'view testimonials',
            ],

            // Messages
            [
                'title' => 'Messages',
                'icon' => 'messages',
                'active' => request()->routeIs('admin.messages.*'),
                'permission' => 'view messages',
                'badge' => $this->getUnreadMessagesCount(),
                'children' => [
                    [
                        'title' => 'All Messages',
                        'route' => 'admin.messages.index',
                        'active' => request()->routeIs('admin.messages.index'),
                        'permission' => 'view messages',
                        'badge' => $this->getUnreadMessagesCount(),
                    ],
                    [
                        'title' => 'Send Message',
                        'route' => 'admin.messages.create',
                        'active' => request()->routeIs('admin.messages.create'),
                        'permission' => 'create messages',
                    ],
                ],
            ],

            // Live Chat
            [
                'title' => 'Live Chat',
                'icon' => 'chat',
                'active' => request()->routeIs('admin.chat.*'),
                'permission' => 'view chat',
                'badge' => $this->getWaitingChatsCount(),
                'children' => [
                    [
                        'title' => 'Chat Sessions',
                        'route' => 'admin.chat.index',
                        'active' => request()->routeIs('admin.chat.index'),
                        'permission' => 'view chat',
                        'badge' => $this->getWaitingChatsCount(),
                    ],
                    [
                        'title' => 'Chat Settings',
                        'route' => 'admin.chat.settings',
                        'active' => request()->routeIs('admin.chat.settings'),
                        'permission' => 'manage chat settings',
                    ],
                ],
            ],

            // Quotations
            [
                'title' => 'Quotations',
                'route' => 'admin.quotations.index',
                'icon' => 'quotations',
                'active' => request()->routeIs('admin.quotations.*'),
                'permission' => 'view quotations',
                'badge' => $this->getPendingQuotationsCount(),
            ],

            // User Management
            [
                'title' => 'User Management',
                'icon' => 'users',
                'active' => request()->routeIs(['admin.users.*', 'admin.roles.*', 'admin.permissions.*']),
                'permission' => 'view users',
                'children' => [
                    [
                        'title' => 'All Users',
                        'route' => 'admin.users.index',
                        'active' => request()->routeIs('admin.users.index'),
                        'permission' => 'view users',
                    ],
                    [
                        'title' => 'Roles & Permissions',
                        'route' => 'admin.roles.index',
                        'active' => request()->routeIs('admin.roles.*'),
                        'permission' => 'view roles',
                    ],
                    [
                        'title' => 'Add User',
                        'route' => 'admin.users.create',
                        'active' => request()->routeIs('admin.users.create'),
                        'permission' => 'create users',
                    ],
                ],
            ],

            // Settings
            [
                'title' => 'Settings',
                'icon' => 'settings',
                'active' => request()->routeIs(['admin.settings.*', 'admin.payment-methods.*']),
                'permission' => 'view settings',
                'children' => [
                    [
                        'title' => 'General Settings',
                        'route' => 'admin.settings.index',
                        'active' => request()->routeIs('admin.settings.index'),
                        'permission' => 'view settings',
                    ],
                    [
                        'title' => 'Payment Methods',
                        'route' => 'admin.payment-methods.index',
                        'active' => request()->routeIs('admin.payment-methods.*'),
                        'permission' => 'view settings',
                    ],
                    [
                        'title' => 'Email Settings',
                        'route' => 'admin.settings.email',
                        'active' => request()->routeIs('admin.settings.email'),
                        'permission' => 'manage email settings',
                    ],
                    [
                        'title' => 'SEO Settings',
                        'route' => 'admin.settings.seo',
                        'active' => request()->routeIs('admin.settings.seo'),
                        'permission' => 'manage seo settings',
                    ],
                ],
            ],
        ];
    }

    /**
     * Get client navigation links
     */
    public function getClientNavigation(): array
    {
        $user = Auth::user();
        
        return [
            [
                'title' => 'Dashboard',
                'route' => 'client.dashboard',
                'icon' => 'dashboard',
                'active' => request()->routeIs('client.dashboard'),
            ],
            [
                'title' => 'My Projects',
                'route' => 'client.projects.index',
                'icon' => 'projects',
                'active' => request()->routeIs('client.projects.*'),
                'badge' => $this->getActiveProjectsCount(),
            ],
            [
                'title' => 'Quotations',
                'route' => 'client.quotations.index',
                'icon' => 'quotations',
                'active' => request()->routeIs('client.quotations.*'),
                'badge' => $this->getPendingQuotationsCount(),
            ],
            [
                'title' => 'Messages',
                'route' => 'client.messages.index',
                'icon' => 'messages',
                'active' => request()->routeIs('client.messages.*'),
                'badge' => $this->getUnreadMessagesCount(),
            ],
            [
                'title' => 'Profile',
                'route' => 'client.profile.index',
                'icon' => 'users',
                'active' => request()->routeIs('client.profile.*'),
            ],
        ];
    }

    /**
     * Filter navigation items based on user permissions
     */
    public function filterByPermissions(array $navigation): array
    {
        $user = Auth::user();
        
        // Bypass permission checks for admin users - show all navigation
        if ($user && ($user->hasRole(['admin', 'super-admin']) || $user->isAdmin())) {
            return $navigation;
        }
        
        return collect($navigation)->filter(function ($item) use ($user) {
            // Check if user has permission for this item
            if (isset($item['permission']) && !$user->can($item['permission'])) {
                return false;
            }
            
            // Filter children if they exist
            if (isset($item['children'])) {
                $item['children'] = $this->filterByPermissions($item['children']);
                // Remove parent if no children remain
                return count($item['children']) > 0;
            }
            
            return true;
        })->values()->toArray();
    }

    /**
     * Get admin navigation with permissions applied
     */
    public function getFilteredAdminNavigation(): array
    {
        return $this->filterByPermissions($this->getAdminNavigation());
    }

    /**
     * Get client navigation (no permission filtering needed for basic client nav)
     */
    public function getFilteredClientNavigation(): array
    {
        return $this->getClientNavigation();
    }

    /**
     * Get quick actions for admin header dropdown
     */
    public function getAdminQuickActions(): array
    {
        return [
            [
                'title' => 'Create Project',
                'route' => 'admin.projects.create',
                'icon' => 'plus',
                'permission' => 'create projects',
            ],
            [
                'title' => 'Create Quotation',
                'route' => 'admin.quotations.create',
                'icon' => 'document',
                'permission' => 'create quotations',
            ],
            [
                'title' => 'Add User',
                'route' => 'admin.users.create',
                'icon' => 'user-plus',
                'permission' => 'create users',
            ],
            'divider',
            [
                'title' => 'System Health',
                'route' => 'admin.dashboard.system-health',
                'icon' => 'activity',
                'permission' => 'view system health',
            ],
        ];
    }

    /**
     * Get client quick actions
     */
    public function getClientQuickActions(): array
    {
        return [
            [
                'title' => 'Request Quote',
                'route' => 'client.quotations.create',
                'icon' => 'document',
            ],
            [
                'title' => 'New Message',
                'route' => 'client.messages.create',
                'icon' => 'message',
            ],
            [
                'title' => 'View Services',
                'route' => 'services.index',
                'icon' => 'services',
            ],
        ];
    }

    /**
     * Get breadcrumbs for current route
     */
    public function getBreadcrumbs(): array
    {
        $routeName = Route::currentRouteName();
        $breadcrumbs = [];

        // Define comprehensive breadcrumb mappings
        $breadcrumbMappings = [
            // Dashboard
            'admin.dashboard' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard']
            ],
            
            // Users Management
            'admin.users.index' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'User Management', 'route' => 'admin.users.index']
            ],
            'admin.users.create' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'User Management', 'route' => 'admin.users.index'],
                ['title' => 'Create User']
            ],
            'admin.users.edit' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'User Management', 'route' => 'admin.users.index'],
                ['title' => 'Edit User']
            ],
            'admin.users.show' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'User Management', 'route' => 'admin.users.index'],
                ['title' => 'User Details']
            ],
            
            // Roles & Permissions
            'admin.roles.index' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'User Management', 'route' => 'admin.users.index'],
                ['title' => 'Roles & Permissions']
            ],
            'admin.roles.create' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'User Management', 'route' => 'admin.users.index'],
                ['title' => 'Roles & Permissions', 'route' => 'admin.roles.index'],
                ['title' => 'Create Role']
            ],
            
            // Projects
            'admin.projects.index' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Projects', 'route' => 'admin.projects.index']
            ],
            'admin.projects.create' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Projects', 'route' => 'admin.projects.index'],
                ['title' => 'Create Project']
            ],
            'admin.projects.show' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Projects', 'route' => 'admin.projects.index'],
                ['title' => 'Project Details']
            ],
            'admin.projects.edit' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Projects', 'route' => 'admin.projects.index'],
                ['title' => 'Edit Project']
            ],
            
            // Project Categories
            'admin.project-categories.index' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Projects', 'route' => 'admin.projects.index'],
                ['title' => 'Project Categories']
            ],
            
            // Services
            'admin.services.index' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Services', 'route' => 'admin.services.index']
            ],
            'admin.services.create' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Services', 'route' => 'admin.services.index'],
                ['title' => 'Create Service']
            ],
            'admin.services.edit' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Services', 'route' => 'admin.services.index'],
                ['title' => 'Edit Service']
            ],
            
            // Blog/Posts
            'admin.posts.index' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Blog', 'route' => 'admin.posts.index']
            ],
            'admin.posts.create' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Blog', 'route' => 'admin.posts.index'],
                ['title' => 'Create Post']
            ],
            'admin.posts.edit' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Blog', 'route' => 'admin.posts.index'],
                ['title' => 'Edit Post']
            ],
            
            // Messages
            'admin.messages.index' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Messages', 'route' => 'admin.messages.index']
            ],
            'admin.messages.create' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Messages', 'route' => 'admin.messages.index'],
                ['title' => 'Send Message']
            ],
            
            // Chat
            'admin.chat.index' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Live Chat', 'route' => 'admin.chat.index']
            ],
            'admin.chat.settings' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Live Chat', 'route' => 'admin.chat.index'],
                ['title' => 'Chat Settings']
            ],
            
            // Orders
            'admin.orders.index' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Orders', 'route' => 'admin.orders.index']
            ],
            'admin.orders.show' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Orders', 'route' => 'admin.orders.index'],
                ['title' => 'Order Details']
            ],
            'admin.orders.payment' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Orders', 'route' => 'admin.orders.index'],
                ['title' => 'Payment Review']
            ],
            'admin.orders.negotiation' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Orders', 'route' => 'admin.orders.index'],
                ['title' => 'Price Negotiation']
            ],
            'admin.orders.payments.index' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Orders', 'route' => 'admin.orders.index'],
                ['title' => 'Payment Management']
            ],

            // Quotations
            'admin.quotations.index' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Quotations', 'route' => 'admin.quotations.index']
            ],
            'admin.quotations.show' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Quotations', 'route' => 'admin.quotations.index'],
                ['title' => 'Quotation Details']
            ],
            
            // Team
            'admin.team.index' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Team', 'route' => 'admin.team.index']
            ],
            'admin.team.create' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Team', 'route' => 'admin.team.index'],
                ['title' => 'Add Team Member']
            ],
            
            // Testimonials
            'admin.testimonials.index' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Testimonials', 'route' => 'admin.testimonials.index']
            ],
            
            // Settings
            'admin.settings.index' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Settings', 'route' => 'admin.settings.index']
            ],
            'admin.settings.email' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Settings', 'route' => 'admin.settings.index'],
                ['title' => 'Email Settings']
            ],
            'admin.settings.seo' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Settings', 'route' => 'admin.settings.index'],
                ['title' => 'SEO Settings']
            ],
            
            // Payment Methods
            'admin.payment-methods.index' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Settings', 'route' => 'admin.settings.index'],
                ['title' => 'Payment Methods']
            ],
            'admin.payment-methods.create' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Settings', 'route' => 'admin.settings.index'],
                ['title' => 'Payment Methods', 'route' => 'admin.payment-methods.index'],
                ['title' => 'Add Payment Method']
            ],
            'admin.payment-methods.edit' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Settings', 'route' => 'admin.settings.index'],
                ['title' => 'Payment Methods', 'route' => 'admin.payment-methods.index'],
                ['title' => 'Edit Payment Method']
            ],
            'admin.payment-methods.show' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Settings', 'route' => 'admin.settings.index'],
                ['title' => 'Payment Methods', 'route' => 'admin.payment-methods.index'],
                ['title' => 'Payment Method Details']
            ],
            
            // Company Profile
            'admin.company.index' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Company Profile', 'route' => 'admin.company.index']
            ],
            'admin.certifications.index' => [
                ['title' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['title' => 'Company Profile', 'route' => 'admin.company.index'],
                ['title' => 'Certifications']
            ],
            
            // Client Routes
            'client.dashboard' => [
                ['title' => 'Dashboard', 'route' => 'client.dashboard']
            ],
            'client.projects.index' => [
                ['title' => 'Dashboard', 'route' => 'client.dashboard'],
                ['title' => 'My Projects', 'route' => 'client.projects.index']
            ],
            'client.quotations.index' => [
                ['title' => 'Dashboard', 'route' => 'client.dashboard'],
                ['title' => 'Quotations', 'route' => 'client.quotations.index']
            ],
            'client.messages.index' => [
                ['title' => 'Dashboard', 'route' => 'client.dashboard'],
                ['title' => 'Messages', 'route' => 'client.messages.index']
            ],
            'client.profile.index' => [
                ['title' => 'Dashboard', 'route' => 'client.dashboard'],
                ['title' => 'Profile', 'route' => 'client.profile.index']
            ],
        ];

        return $breadcrumbMappings[$routeName] ?? [
            ['title' => 'Dashboard', 'route' => Auth::user()->isClient() ? 'client.dashboard' : 'admin.dashboard']
        ];
    }

    /**
     * Badge count methods
     */
    
    public function getDraftPostsCount(): int
    {
        try {
            return \App\Models\Post::where('status', 'draft')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getUnreadMessagesCount(): int
    {
        try {
            return \App\Models\Message::where('is_read', false)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getWaitingChatsCount(): int
    {
        try {
            return \App\Models\ChatSession::where('status', 'waiting')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getPendingQuotationsCount(): int
    {
        try {
            return \App\Models\Quotation::where('status', 'pending')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getActiveProjectsCount(): int
    {
        try {
            $user = Auth::user();
            if ($user && $user->isClient()) {
                return $user->projects()->where('status', 'active')->count();
            }
            return \App\Models\Project::where('status', 'active')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getPendingOrdersCount(): int
    {
        try {
            return \App\Models\ProductOrder::whereIn('status', ['pending', 'confirmed'])
                ->orWhere('payment_status', 'proof_uploaded')
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getPendingPaymentsCount(): int
    {
        try {
            return \App\Models\ProductOrder::where('payment_status', 'proof_uploaded')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }
}