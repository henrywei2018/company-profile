<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class RbacStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbac:status {--detailed : Show detailed information}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display RBAC system status and statistics';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔐 RBAC System Status Report');
        $this->newLine();

        // Check database schema
        $this->checkSchema();
        
        // Show basic statistics
        $this->showStatistics();
        
        // Show role information
        $this->showRoles();
        
        if ($this->option('detailed')) {
            $this->showDetailedInfo();
        }
        
        // Show system health
        $this->showSystemHealth();
    }
    
    /**
     * Check database schema.
     */
    protected function checkSchema()
    {
        $this->info('📋 Database Schema Status:');
        
        $requiredTables = [
            'roles' => 'Roles table',
            'permissions' => 'Permissions table',
            'model_has_roles' => 'User-Role assignments',
            'model_has_permissions' => 'Direct user permissions',
            'role_has_permissions' => 'Role-Permission assignments',
        ];
        
        $tableStatus = [];
        foreach ($requiredTables as $table => $description) {
            $exists = Schema::hasTable($table);
            $tableStatus[] = [
                'Table' => $table,
                'Status' => $exists ? '✅ Exists' : '❌ Missing',
                'Description' => $description
            ];
        }
        
        $this->table(['Table', 'Status', 'Description'], $tableStatus);
        
        // Check enhanced fields
        $this->checkEnhancedFields();
        $this->newLine();
    }
    
    /**
     * Check enhanced fields.
     */
    protected function checkEnhancedFields()
    {
        $enhancements = [
            'roles' => ['description', 'color', 'is_system', 'created_at'],
            'permissions' => ['description', 'module', 'is_system', 'created_at'],
            'users' => ['last_login_at', 'login_count', 'failed_login_attempts', 'locked_at']
        ];
        
        $this->info('🔧 Enhanced Fields Status:');
        
        foreach ($enhancements as $table => $fields) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            
            $this->line("  {$table}:");
            foreach ($fields as $field) {
                $exists = Schema::hasColumn($table, $field);
                $status = $exists ? '✅' : '❌';
                $this->line("    {$status} {$field}");
            }
        }
    }
    
    /**
     * Show basic statistics.
     */
    protected function showStatistics()
    {
        $this->info('📊 System Statistics:');
        
        $stats = [
            ['Metric', 'Count', 'Details'],
            ['Total Roles', Role::count(), Role::where('is_system', true)->count() . ' system, ' . Role::where('is_system', false)->count() . ' custom'],
            ['Total Permissions', Permission::count(), Permission::where('is_system', true)->count() . ' system, ' . Permission::where('is_system', false)->count() . ' custom'],
            ['Total Users', User::count(), User::where('is_active', true)->count() . ' active, ' . User::where('is_active', false)->count() . ' inactive'],
            ['Users with Roles', User::has('roles')->count(), ''],
            ['Users without Roles', User::doesntHave('roles')->count(), ''],
        ];
        
        $this->table($stats[0], array_slice($stats, 1));
        $this->newLine();
    }
    
    /**
     * Show role information.
     */
    protected function showRoles()
    {
        $this->info('👥 Roles Overview:');
        
        $roles = Role::withCount(['users', 'permissions'])->orderBy('name')->get();
        
        $roleData = [];
        foreach ($roles as $role) {
            $roleData[] = [
                'Name' => $role->name,
                'Users' => $role->users_count,
                'Permissions' => $role->permissions_count,
                'Type' => $role->is_system ? 'System' : 'Custom',
                'Color' => $role->color ?? 'N/A',
            ];
        }
        
        if (!empty($roleData)) {
            $this->table(['Name', 'Users', 'Permissions', 'Type', 'Color'], $roleData);
        } else {
            $this->warn('No roles found in the system.');
        }
        
        $this->newLine();
    }
    
    /**
     * Show detailed information.
     */
    protected function showDetailedInfo()
    {
        $this->info('🔍 Detailed Information:');
        
        // Permission modules
        $this->showPermissionModules();
        
        // User role distribution
        $this->showUserRoleDistribution();
        
        // Recent activity
        $this->showRecentActivity();
        
        $this->newLine();
    }
    
    /**
     * Show permission modules.
     */
    protected function showPermissionModules()
    {
        $this->line('📋 Permission Modules:');
        
        $modules = Permission::selectRaw('
            COALESCE(module, SUBSTRING_INDEX(SUBSTRING_INDEX(name, " ", 2), " ", -1)) as module_name,
            COUNT(*) as count
        ')
        ->groupBy('module_name')
        ->orderBy('module_name')
        ->get();
        
        $moduleData = [];
        foreach ($modules as $module) {
            $moduleData[] = [
                'Module' => $module->module_name,
                'Permissions' => $module->count
            ];
        }
        
        if (!empty($moduleData)) {
            $this->table(['Module', 'Permissions'], $moduleData);
        }
    }
    
    /**
     * Show user role distribution.
     */
    protected function showUserRoleDistribution()
    {
        $this->line('👤 User Role Distribution:');
        
        $distribution = \DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->join('users', 'model_has_roles.model_id', '=', 'users.id')
            ->selectRaw('roles.name as role_name, COUNT(*) as user_count')
            ->where('users.is_active', true)
            ->groupBy('roles.name')
            ->orderBy('user_count', 'desc')
            ->get();
        
        $distributionData = [];
        foreach ($distribution as $item) {
            $distributionData[] = [
                'Role' => $item->role_name,
                'Active Users' => $item->user_count
            ];
        }
        
        if (!empty($distributionData)) {
            $this->table(['Role', 'Active Users'], $distributionData);
        }
    }
    
    /**
     * Show recent activity (if audit logs exist).
     */
    protected function showRecentActivity()
    {
        $this->line('🕒 Recent User Activity:');
        
        $recentUsers = User::where('last_login_at', '>', now()->subDays(7))
            ->with('roles')
            ->orderBy('last_login_at', 'desc')
            ->limit(10)
            ->get();
        
        if ($recentUsers->count() > 0) {
            $activityData = [];
            foreach ($recentUsers as $user) {
                $activityData[] = [
                    'User' => $user->name,
                    'Email' => $user->email,
                    'Roles' => $user->roles->pluck('name')->join(', '),
                    'Last Login' => $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never'
                ];
            }
            
            $this->table(['User', 'Email', 'Roles', 'Last Login'], $activityData);
        } else {
            $this->line('No recent login activity found.');
        }
    }
    
    /**
     * Show system health.
     */
    protected function showSystemHealth()
    {
        $this->info('🏥 System Health Check:');
        
        $healthIssues = [];
        
        // Check for users without roles
        $usersWithoutRoles = User::doesntHave('roles')->where('is_active', true)->count();
        if ($usersWithoutRoles > 0) {
            $healthIssues[] = ['⚠️  Users without roles', $usersWithoutRoles, 'Users should have at least one role'];
        }
        
        // Check for inactive users with roles
        $inactiveUsersWithRoles = User::has('roles')->where('is_active', false)->count();
        if ($inactiveUsersWithRoles > 0) {
            $healthIssues[] = ['⚠️  Inactive users with roles', $inactiveUsersWithRoles, 'Consider removing roles from inactive users'];
        }
        
        // Check for empty roles
        $emptyRoles = Role::doesntHave('users')->where('is_system', false)->count();
        if ($emptyRoles > 0) {
            $healthIssues[] = ['ℹ️  Empty custom roles', $emptyRoles, 'Custom roles with no users assigned'];
        }
        
        // Check for unused permissions
        $unusedPermissions = Permission::doesntHave('roles')->count();
        if ($unusedPermissions > 0) {
            $healthIssues[] = ['ℹ️  Unused permissions', $unusedPermissions, 'Permissions not assigned to any role'];
        }
        
        // Check for super admin users
        $superAdminCount = User::role('super-admin')->count();
        if ($superAdminCount === 0) {
            $healthIssues[] = ['❌ No super admin users', 0, 'System should have at least one super admin'];
        } elseif ($superAdminCount === 1) {
            $healthIssues[] = ['⚠️  Only one super admin', 1, 'Consider having backup super admin users'];
        }
        
        if (empty($healthIssues)) {
            $this->info('✅ System health looks good!');
        } else {
            $this->table(['Issue', 'Count', 'Recommendation'], $healthIssues);
        }
    }
}