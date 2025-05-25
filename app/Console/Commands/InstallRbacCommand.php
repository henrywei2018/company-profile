<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class InstallRbacCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbac:install 
                            {--fresh : Fresh installation, will reset all roles and permissions}
                            {--seed : Run the seeder after installation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install and setup RBAC system with roles and permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Installing RBAC System...');
        
        // Check if migrations are needed
        $this->checkMigrations();
        
        // Clear cache
        $this->clearCache();
        
        if ($this->option('fresh')) {
            $this->freshInstall();
        }
        
        // Install basic permissions and roles
        $this->installPermissions();
        $this->installRoles();
        
        // Create default admin user if needed
        $this->createAdminUser();
        
        if ($this->option('seed')) {
            $this->call('db:seed', ['--class' => 'RolePermissionSeeder']);
        }
        
        $this->info('✅ RBAC System installed successfully!');
        $this->displayInfo();
    }
    
    /**
     * Check if required migrations exist.
     */
    protected function checkMigrations()
    {
        $this->info('🔍 Checking database schema...');
        
        $requiredTables = ['roles', 'permissions', 'model_has_roles', 'role_has_permissions'];
        $missingTables = [];
        
        foreach ($requiredTables as $table) {
            if (!Schema::hasTable($table)) {
                $missingTables[] = $table;
            }
        }
        
        if (!empty($missingTables)) {
            $this->error('❌ Missing required tables: ' . implode(', ', $missingTables));
            $this->error('Please run: php artisan migrate');
            return false;
        }
        
        // Check for enhanced fields
        $enhancements = [
            'roles' => ['description', 'color', 'is_system'],
            'permissions' => ['description', 'module', 'is_system'],
            'users' => ['last_login_at', 'login_count', 'failed_login_attempts', 'locked_at']
        ];
        
        foreach ($enhancements as $table => $columns) {
            if (Schema::hasTable($table)) {
                foreach ($columns as $column) {
                    if (!Schema::hasColumn($table, $column)) {
                        $this->warn("⚠️  Enhanced field {$table}.{$column} is missing. Please run migration.");
                    }
                }
            }
        }
        
        $this->info('✅ Database schema check completed.');
        return true;
    }
    
    /**
     * Clear all caches.
     */
    protected function clearCache()
    {
        $this->info('🧹 Clearing caches...');
        
        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Clear application cache
        $this->call('cache:clear');
        $this->call('config:clear');
        $this->call('route:clear');
        $this->call('view:clear');
        
        $this->info('✅ Caches cleared.');
    }
    
    /**
     * Fresh installation - reset everything.
     */
    protected function freshInstall()
    {
        if (!$this->confirm('⚠️  This will delete all existing roles and permissions. Continue?')) {
            $this->info('Installation cancelled.');
            return;
        }
        
        $this->info('🗑️  Resetting RBAC data...');
        
        // Remove all role and permission assignments
        \DB::table('model_has_roles')->delete();
        \DB::table('role_has_permissions')->delete();
        
        // Delete all custom roles and permissions
        Role::where('is_system', false)->delete();
        Permission::where('is_system', false)->delete();
        
        $this->info('✅ RBAC data reset completed.');
    }
    
    /**
     * Install basic permissions.
     */
    protected function installPermissions()
    {
        $this->info('📋 Installing permissions...');
        
        $modules = [
            'dashboard' => ['view'],
            'users' => ['view', 'create', 'edit', 'delete', 'activate', 'deactivate'],
            'roles' => ['view', 'create', 'edit', 'delete', 'assign'],
            'permissions' => ['view', 'create', 'edit', 'delete'],
            'projects' => ['view', 'create', 'edit', 'delete', 'approve', 'export'],
            'quotations' => ['view', 'create', 'edit', 'delete', 'approve', 'reject'],
            'messages' => ['view', 'create', 'edit', 'delete', 'reply'],
            'services' => ['view', 'create', 'edit', 'delete'],
            'blog' => ['view', 'create', 'edit', 'delete', 'publish'],
            'settings' => ['view', 'edit'],
        ];
        
        $created = 0;
        $skipped = 0;
        
        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $permissionName = $action . ' ' . $module;
                
                if (Permission::where('name', $permissionName)->exists()) {
                    $skipped++;
                    continue;
                }
                
                Permission::create([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                    'description' => "Permission to {$action} {$module}",
                    'module' => $module,
                    'is_system' => true,
                ]);
                
                $created++;
            }
        }
        
        $this->info("✅ Permissions installed: {$created} created, {$skipped} skipped.");
    }
    
    /**
     * Install basic roles.
     */
    protected function installRoles()
    {
        $this->info('👥 Installing roles...');
        
        $roles = [
            'super-admin' => [
                'description' => 'Super Administrator with full system access',
                'color' => 'red',
                'permissions' => 'all'
            ],
            'admin' => [
                'description' => 'Administrator with most system permissions',
                'color' => 'blue',
                'permissions' => Permission::whereNotIn('name', ['delete roles', 'delete permissions'])->pluck('name')->toArray()
            ],
            'manager' => [
                'description' => 'Project manager with project and quotation management',
                'color' => 'purple',
                'permissions' => [
                    'view dashboard',
                    'view projects', 'create projects', 'edit projects', 'approve projects',
                    'view quotations', 'create quotations', 'edit quotations', 'approve quotations',
                    'view messages', 'reply messages',
                ]
            ],
            'client' => [
                'description' => 'Client with limited access',
                'color' => 'yellow',
                'permissions' => [
                    'view dashboard',
                    'view projects',
                    'view quotations', 'create quotations',
                    'view messages', 'create messages',
                ]
            ]
        ];
        
        $created = 0;
        $updated = 0;
        
        foreach ($roles as $roleName => $roleData) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ], [
                'description' => $roleData['description'],
                'color' => $roleData['color'],
                'is_system' => true,
            ]);
            
            if ($role->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
            
            // Assign permissions
            if ($roleData['permissions'] === 'all') {
                $role->syncPermissions(Permission::all());
            } else {
                $permissions = Permission::whereIn('name', $roleData['permissions'])->get();
                $role->syncPermissions($permissions);
            }
        }
        
        $this->info("✅ Roles installed: {$created} created, {$updated} updated.");
    }
    
    /**
     * Create default admin user.
     */
    protected function createAdminUser()
    {
        $this->info('👤 Checking admin user...');
        
        $adminEmail = 'admin@cvusahaprimlestari.com';
        $adminUser = User::where('email', $adminEmail)->first();
        
        if (!$adminUser) {
            if ($this->confirm("Create default admin user ({$adminEmail})?")) {
                $adminUser = User::create([
                    'name' => 'Super Administrator',
                    'email' => $adminEmail,
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]);
                
                $adminUser->assignRole('super-admin');
                
                $this->info("✅ Admin user created: {$adminEmail} / password123");
            }
        } else {
            if (!$adminUser->hasRole('super-admin')) {
                $adminUser->assignRole('super-admin');
                $this->info("✅ Admin role assigned to existing user.");
            } else {
                $this->info("✅ Admin user already exists and has super-admin role.");
            }
        }
    }
    
    /**
     * Display installation information.
     */
    protected function displayInfo()
    {
        $this->newLine();
        $this->info('📊 RBAC System Status:');
        $this->table(['Item', 'Count'], [
            ['Roles', Role::count()],
            ['Permissions', Permission::count()],
            ['Users with Roles', User::has('roles')->count()],
            ['Active Users', User::where('is_active', true)->count()],
        ]);
        
        $this->newLine();
        $this->info('🔐 Default Login Credentials:');
        $this->line('Email: admin@cvusahaprimlestari.com');
        $this->line('Password: password123');
        
        $this->newLine();
        $this->info('🛠️  Next Steps:');
        $this->line('1. Change default admin password');
        $this->line('2. Create additional users and assign roles');
        $this->line('3. Customize permissions as needed');
        $this->line('4. Test the admin interface at /admin');
        
        $this->newLine();
        $this->info('📚 Useful Commands:');
        $this->line('php artisan rbac:status    - Check RBAC status');
        $this->line('php artisan permission:cache-reset - Clear permission cache');
        $this->line('php artisan rbac:install --fresh - Fresh reinstall');
    }
}