<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions grouped by module
        $permissions = [
            'dashboard' => ['view'],
            'users' => ['view', 'create', 'edit', 'delete', 'activate', 'deactivate'],
            'roles' => ['view', 'create', 'edit', 'delete', 'assign'],
            'permissions' => ['view', 'create', 'edit', 'delete'],
            'projects' => ['view', 'create', 'edit', 'delete', 'approve', 'export'],
            'project-categories' => ['view', 'create', 'edit', 'delete'],
            'quotations' => ['view', 'create', 'edit', 'delete', 'approve', 'reject', 'export'],
            'messages' => ['view', 'create', 'edit', 'delete', 'reply'],
            'services' => ['view', 'create', 'edit', 'delete'],
            'service-categories' => ['view', 'create', 'edit', 'delete'],
            'team' => ['view', 'create', 'edit', 'delete'],
            'team-departments' => ['view', 'create', 'edit', 'delete'],
            'testimonials' => ['view', 'create', 'edit', 'delete'],
            'certifications' => ['view', 'create', 'edit', 'delete'],
            'blog' => ['view', 'create', 'edit', 'delete', 'publish'],
            'blog-categories' => ['view', 'create', 'edit', 'delete'],
            'company-profile' => ['view', 'edit'],
            'settings' => ['view', 'edit'],
            'reports' => ['view', 'export'],
        ];

        // Create permissions
        foreach ($permissions as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => $action . ' ' . $module,
                    'guard_name' => 'web',
                ], [
                    'description' => "Permission to {$action} {$module}",
                    'module' => $module,
                    'is_system' => true,
                ]);
            }
        }

        // Define roles with their permissions
        $roles = [
            'super-admin' => [
                'description' => 'Super Administrator with full system access',
                'color' => 'red',
                'permissions' => 'all', // All permissions
                'is_system' => true,
            ],
            'admin' => [
                'description' => 'Administrator with most system permissions',
                'color' => 'blue',
                'is_system' => true,
                'permissions' => [
                    'view dashboard',
                    // Users management
                    'view users', 'create users', 'edit users', 'activate users', 'deactivate users',
                    // Projects
                    'view projects', 'create projects', 'edit projects', 'delete projects', 'approve projects', 'export projects',
                    'view project-categories', 'create project-categories', 'edit project-categories', 'delete project-categories',
                    // Quotations
                    'view quotations', 'create quotations', 'edit quotations', 'delete quotations', 'approve quotations', 'reject quotations', 'export quotations',
                    // Messages
                    'view messages', 'create messages', 'edit messages', 'delete messages', 'reply messages',
                    // Services
                    'view services', 'create services', 'edit services', 'delete services',
                    'view service-categories', 'create service-categories', 'edit service-categories', 'delete service-categories',
                    // Team
                    'view team', 'create team', 'edit team', 'delete team',
                    'view team-departments', 'create team-departments', 'edit team-departments', 'delete team-departments',
                    // Content
                    'view testimonials', 'create testimonials', 'edit testimonials', 'delete testimonials',
                    'view certifications', 'create certifications', 'edit certifications', 'delete certifications',
                    'view blog', 'create blog', 'edit blog', 'delete blog', 'publish blog',
                    'view blog-categories', 'create blog-categories', 'edit blog-categories', 'delete blog-categories',
                    // Settings
                    'view company-profile', 'edit company-profile',
                    'view settings', 'edit settings',
                    'view reports', 'export reports',
                ]
            ],
            'editor' => [
                'description' => 'Content editor with limited administrative access',
                'color' => 'green',
                'is_system' => true,
                'permissions' => [
                    'view dashboard',
                    // Projects (view and edit only)
                    'view projects', 'edit projects',
                    'view project-categories',
                    // Content management
                    'view blog', 'create blog', 'edit blog', 'publish blog',
                    'view blog-categories', 'create blog-categories', 'edit blog-categories',
                    'view testimonials', 'create testimonials', 'edit testimonials',
                    'view team', 'edit team',
                    'view services', 'edit services',
                    // Messages (view and reply only)
                    'view messages', 'reply messages',
                    // Company profile
                    'view company-profile', 'edit company-profile',
                ]
            ],
            'manager' => [
                'description' => 'Project manager with project and quotation management',
                'color' => 'purple',
                'is_system' => true,
                'permissions' => [
                    'view dashboard',
                    // Projects
                    'view projects', 'create projects', 'edit projects', 'approve projects',
                    'view project-categories',
                    // Quotations
                    'view quotations', 'create quotations', 'edit quotations', 'approve quotations', 'reject quotations',
                    // Messages
                    'view messages', 'create messages', 'reply messages',
                    // Team (view only)
                    'view team',
                    // Reports
                    'view reports', 'export reports',
                ]
            ],
            'client' => [
                'description' => 'Client with limited access to own projects and quotations',
                'color' => 'yellow',
                'is_system' => true,
                'permissions' => [
                    'view dashboard',
                    // Own projects only (handled by policies)
                    'view projects',
                    // Own quotations only (handled by policies)
                    'view quotations', 'create quotations',
                    // Messages
                    'view messages', 'create messages', 'reply messages',
                ]
            ],
        ];

        // Create roles and assign permissions
        foreach ($roles as $roleName => $roleData) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ], [
                'description' => $roleData['description'],
                'color' => $roleData['color'] ?? null,
                'is_system' => $roleData['is_system'] ?? false,
            ]);

            // Assign permissions
            if ($roleData['permissions'] === 'all') {
                $role->syncPermissions(Permission::all());
            } else {
                $permissionObjects = Permission::whereIn('name', $roleData['permissions'])->get();
                $role->syncPermissions($permissionObjects);
            }
        }

        // Create default super admin user if it doesn't exist
        $superAdminUser = User::firstOrCreate([
            'email' => 'admin@cvusahaprimlestari.com'
        ], [
            'name' => 'Super Administrator',
            'email' => 'admin@cvusahaprimlestari.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        // Assign super-admin role
        if (!$superAdminUser->hasRole('super-admin')) {
            $superAdminUser->assignRole('super-admin');
        }

        // Create default admin user if it doesn't exist
        $adminUser = User::firstOrCreate([
            'email' => 'manager@cvusahaprimlestari.com'
        ], [
            'name' => 'System Administrator',
            'email' => 'manager@cvusahaprimlestari.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        // Assign admin role
        if (!$adminUser->hasRole('admin')) {
            $adminUser->assignRole('admin');
        }

        $this->command->info('Roles and permissions seeded successfully!');
        $this->command->info('Default users created:');
        $this->command->info('Super Admin: admin@cvusahaprimlestari.com / password123');
        $this->command->info('Admin: manager@cvusahaprimlestari.com / password123');
    }
}