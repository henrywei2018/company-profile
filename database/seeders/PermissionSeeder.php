<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User management
            'view users', 'create users', 'edit users', 'delete users',
            
            // Company profile
            'edit company-profile',
            
            // Services
            'view services', 'create services', 'edit services', 'delete services',
            'view service-categories', 'create service-categories', 'edit service-categories', 'delete service-categories',
            
            // Projects
            'view projects', 'create projects', 'edit projects', 'delete projects',
            'view project-categories', 'create project-categories', 'edit project-categories', 'delete project-categories',
            
            // Team
            'view team-members', 'create team-members', 'edit team-members', 'delete team-members',
            'view team-departments', 'create team-departments', 'edit team-departments', 'delete team-departments',
            
            // Blog
            'view posts', 'create posts', 'edit posts', 'delete posts',
            'view post-categories', 'create post-categories', 'edit post-categories', 'delete post-categories',
            
            // Testimonials
            'view testimonials', 'create testimonials', 'edit testimonials', 'delete testimonials',
            
            // Certifications
            'view certifications', 'create certifications', 'edit certifications', 'delete certifications',
            
            // Banners
            'view banners', 'create banners', 'edit banners', 'delete banners',
            'view banner-categories', 'create banner-categories', 'edit banner-categories', 'delete banner-categories',
            
            // Quotations
            'view quotations', 'create quotations', 'edit quotations', 'delete quotations',
            'process quotations', 'approve quotations',
            
            // Messages
            'view messages', 'reply messages', 'delete messages',
            
            // Settings
            'view settings', 'edit settings',
            
            // Dashboard
            'view admin-dashboard',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles
        $superAdminRole = Role::create(['name' => 'super-admin']);
        $adminRole = Role::create(['name' => 'admin']);
        $editorRole = Role::create(['name' => 'editor']);
        $clientRole = Role::create(['name' => 'client']);

        // Assign permissions to roles
        $superAdminRole->givePermissionTo(Permission::all());
        
        $adminRole->givePermissionTo([
            'view admin-dashboard',
            'edit company-profile',
            'view services', 'create services', 'edit services', 'delete services',
            'view service-categories', 'create service-categories', 'edit service-categories', 'delete service-categories',
            'view projects', 'create projects', 'edit projects', 'delete projects',
            'view project-categories', 'create project-categories', 'edit project-categories', 'delete project-categories',
            'view team-members', 'create team-members', 'edit team-members', 'delete team-members',
            'view team-departments', 'create team-departments', 'edit team-departments', 'delete team-departments',
            'view posts', 'create posts', 'edit posts', 'delete posts',
            'view post-categories', 'create post-categories', 'edit post-categories', 'delete post-categories',
            'view testimonials', 'create testimonials', 'edit testimonials', 'delete testimonials',
            'view certifications', 'create certifications', 'edit certifications', 'delete certifications',
            'view banners', 'create banners', 'edit banners', 'delete banners',
            'view banner-categories', 'create banner-categories', 'edit banner-categories', 'delete banner-categories',
            'view quotations', 'create quotations', 'edit quotations', 'delete quotations',
            'process quotations', 'approve quotations',
            'view messages', 'reply messages', 'delete messages',
            'view users', 'create users', 'edit users',
            'view settings', 'edit settings',
        ]);
        
        $editorRole->givePermissionTo([
            'view services', 'edit services',
            'view projects', 'edit projects',
            'view team-members', 'edit team-members',
            'view posts', 'create posts', 'edit posts',
            'view testimonials', 'edit testimonials',
            'view messages', 'reply messages',
        ]);
    }
}