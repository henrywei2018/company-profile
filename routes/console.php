<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Schedule;
use App\Models\User;
use App\Models\ChatSession;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// RBAC Install Command
Artisan::command('rbac:install {--fresh} {--seed}', function () {
    $this->info('🚀 Installing RBAC System...');
    
    // Clear cache
    $this->info('🧹 Clearing caches...');
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    
    // Fresh install option
    if ($this->option('fresh')) {
        if ($this->confirm('⚠️  This will delete all existing roles and permissions. Continue?')) {
            $this->info('🗑️  Resetting RBAC data...');
            \DB::table('model_has_roles')->delete();
            \DB::table('role_has_permissions')->delete();
            Role::where('is_system', false)->delete();
            Permission::where('is_system', false)->delete();
        }
    }
    
    // Install permissions
    $this->info('📋 Installing permissions...');
    $modules = [
        'dashboard' => ['view'],
        'users' => ['view', 'create', 'edit', 'delete'],
        'roles' => ['view', 'create', 'edit', 'delete'],
        'permissions' => ['view', 'create', 'edit', 'delete'],
        'projects' => ['view', 'create', 'edit', 'delete'],
        'quotations' => ['view', 'create', 'edit', 'delete'],
        'messages' => ['view', 'create', 'edit', 'delete'],
        'services' => ['view', 'create', 'edit', 'delete'],
        'blog' => ['view', 'create', 'edit', 'delete'],
        'settings' => ['view', 'edit'],
    ];
    
    $created = 0;
    foreach ($modules as $module => $actions) {
        foreach ($actions as $action) {
            $permissionName = $action . ' ' . $module;
            if (!Permission::where('name', $permissionName)->exists()) {
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
    }
    $this->info("✅ Created {$created} permissions");
    
    // Install roles
    $this->info('👥 Installing roles...');
    $roles = [
        'super-admin' => ['description' => 'Super Administrator', 'color' => 'red'],
        'admin' => ['description' => 'Administrator', 'color' => 'blue'],
        'manager' => ['description' => 'Manager', 'color' => 'purple'],
        'client' => ['description' => 'Client', 'color' => 'yellow'],
    ];
    
    foreach ($roles as $roleName => $roleData) {
        $role = Role::firstOrCreate([
            'name' => $roleName,
            'guard_name' => 'web',
        ], array_merge($roleData, ['is_system' => true]));
        
        // Assign all permissions to super-admin
        if ($roleName === 'super-admin') {
            $role->syncPermissions(Permission::all());
        }
    }
    
    // Create admin user
    $adminEmail = 'admin@cvusahaprimlestari.com';
    $adminUser = User::firstOrCreate(['email' => $adminEmail], [
        'name' => 'Super Administrator',
        'email' => $adminEmail,
        'password' => Hash::make('password123'),
        'email_verified_at' => now(),
        'is_active' => true,
    ]);
    
    if (!$adminUser->hasRole('super-admin')) {
        $adminUser->assignRole('super-admin');
    }
    
    $this->info('✅ RBAC System installed successfully!');
    $this->info('🔐 Admin Login: admin@cvusahaprimlestari.com / password123');
    
    if ($this->option('seed')) {
        $this->info('🌱 Running additional seeder...');
        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder']);
    }
    
})->purpose('Install and setup RBAC system');

// RBAC Status Command
Artisan::command('rbac:status {--detailed}', function () {
    $this->info('🔐 RBAC System Status Report');
    $this->newLine();
    
    // Basic stats
    $this->info('📊 System Statistics:');
    $this->table(['Metric', 'Count'], [
        ['Roles', Role::count()],
        ['Permissions', Permission::count()],
        ['Users', User::count()],
        ['Active Users', User::where('is_active', true)->count()],
        ['Users with Roles', User::has('roles')->count()],
    ]);
    
    // Roles overview
    $this->info('👥 Roles Overview:');
    $roles = Role::withCount(['users', 'permissions'])->get();
    $roleData = [];
    foreach ($roles as $role) {
        $roleData[] = [
            'Name' => $role->name,
            'Users' => $role->users_count,
            'Permissions' => $role->permissions_count,
            'Type' => ($role->is_system ?? false) ? 'System' : 'Custom',
        ];
    }
    
    if (!empty($roleData)) {
        $this->table(['Name', 'Users', 'Permissions', 'Type'], $roleData);
    }
    
    if ($this->option('detailed')) {
        // Permission modules
        $this->info('📋 Permission Modules:');
        $modules = Permission::selectRaw('
            COALESCE(module, SUBSTRING_INDEX(SUBSTRING_INDEX(name, " ", 2), " ", -1)) as module_name,
            COUNT(*) as count
        ')
        ->groupBy('module_name')
        ->orderBy('module_name')
        ->get();
        
        $moduleData = [];
        foreach ($modules as $module) {
            $moduleData[] = ['Module' => $module->module_name, 'Count' => $module->count];
        }
        
        if (!empty($moduleData)) {
            $this->table(['Module', 'Count'], $moduleData);
        }
    }
    
    // Health check
    $this->info('🏥 Health Check:');
    $issues = [];
    
    $usersWithoutRoles = User::doesntHave('roles')->where('is_active', true)->count();
    if ($usersWithoutRoles > 0) {
        $issues[] = ['⚠️  Active users without roles', $usersWithoutRoles];
    }
    
    $superAdminCount = User::role('super-admin')->count();
    if ($superAdminCount === 0) {
        $issues[] = ['❌ No super admin users', 0];
    }
    
    if (empty($issues)) {
        $this->info('✅ System health looks good!');
    } else {
        $this->table(['Issue', 'Count'], $issues);
    }
    
})->purpose('Display RBAC system status');

Schedule::call(function () {
    $days = 30; // Keep chat sessions for 30 days
    $cutoffDate = Carbon::now()->subDays($days);
    
    // Delete old closed sessions
    $deletedSessions = ChatSession::where('status', 'closed')
        ->where('ended_at', '<', $cutoffDate)
        ->count();
        
    ChatSession::where('status', 'closed')
        ->where('ended_at', '<', $cutoffDate)
        ->delete();
    
    \Log::info("Chat cleanup: Deleted {$deletedSessions} old chat sessions.");
    
    // Clean up orphaned sessions (no activity for 24+ hours)
    $orphanedSessions = ChatSession::whereIn('status', ['active', 'waiting'])
        ->where('last_activity_at', '<', Carbon::now()->subHours(24))
        ->count();
        
    ChatSession::whereIn('status', ['active', 'waiting'])
        ->where('last_activity_at', '<', Carbon::now()->subHours(24))
        ->update(['status' => 'closed', 'ended_at' => now()]);
    
    \Log::info("Chat cleanup: Closed {$orphanedSessions} orphaned chat sessions.");
    
})->dailyAt('02:00')->name('chat-cleanup');

Schedule::call(function () {
    $stats = [
        'total_sessions_today' => ChatSession::whereDate('created_at', today())->count(),
        'active_sessions' => ChatSession::where('status', 'active')->count(),
        'waiting_sessions' => ChatSession::where('status', 'waiting')->count(),
        'avg_response_time' => 0, // You can calculate this
    ];
    
    // Send email report to admin
    $adminEmail = settings('admin_email', 'admin@usahaprimaestari.com');
    
    if ($adminEmail && $stats['total_sessions_today'] > 0) {
        \Mail::to($adminEmail)->send(new \App\Mail\DailyChatReport($stats));
    }
    
})->dailyAt('18:00')->name('daily-chat-report');

// Clean up old chat messages (keep only last 1000 messages per session)
Schedule::call(function () {
    $sessions = ChatSession::withCount('messages')
        ->having('messages_count', '>', 1000)
        ->get();
    
    foreach ($sessions as $session) {
        // Keep only the latest 1000 messages
        $messagesToDelete = $session->messages()
            ->orderBy('created_at', 'desc')
            ->skip(1000)
            ->pluck('id');
            
        if ($messagesToDelete->count() > 0) {
            \App\Models\ChatMessage::whereIn('id', $messagesToDelete)->delete();
            \Log::info("Cleaned up " . $messagesToDelete->count() . " old messages from session " . $session->session_id);
        }
    }
    
})->weekly()->name('chat-message-cleanup');

// Check for stale sessions and send reminders
Schedule::call(function () {
    $staleSessions = ChatSession::where('status', 'waiting')
        ->where('created_at', '<', Carbon::now()->subHours(2))
        ->get();
    
    foreach ($staleSessions as $session) {
        // Send reminder email to admin
        $adminEmail = settings('admin_email');
        if ($adminEmail) {
            \Mail::to($adminEmail)->send(new \App\Mail\StaleChatNotificationMail($session));
        }
    }
    
})->hourly()->name('stale-chat-check');