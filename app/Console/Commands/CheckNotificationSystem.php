<?php
// File: app/Console/Commands/CheckNotificationSystem.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;
use App\Models\User;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Message;
use App\Facades\Notifications;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckNotificationSystem extends Command
{
    protected $signature = 'notifications:check 
                          {--test : Run test notifications}
                          {--clean : Clean test data after check}';

    protected $description = 'Check notification system health and optionally test notifications';

    public function handle()
    {
        $this->info('🔍 Checking Notification System Health...');
        $this->newLine();

        // 1. Check Database Tables
        $this->checkDatabaseTables();
        
        // 2. Check Service Registration
        $this->checkServiceRegistration();
        
        // 3. Check Notification Classes
        $this->checkNotificationClasses();
        
        // 4. Check Model Observers
        $this->checkModelObservers();
        
        // 5. Check Configuration
        $this->checkConfiguration();

        if ($this->option('test')) {
            $this->newLine();
            $this->info('🧪 Running Test Notifications...');
            $this->runTestNotifications();
        }

        if ($this->option('clean')) {
            $this->cleanTestData();
        }

        $this->newLine();
        $this->info('✅ Notification system check completed!');
    }

    protected function checkDatabaseTables()
    {
        $this->info('📋 Checking Database Tables:');
        
        $tables = ['notifications', 'users', 'projects', 'quotations', 'messages'];
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $count = DB::table($table)->count();
                $this->line("  ✅ {$table} table exists ({$count} records)");
            } else {
                $this->error("  ❌ {$table} table missing!");
            }
        }
    }

    protected function checkServiceRegistration()
    {
        $this->info('🔧 Checking Service Registration:');
        
        try {
            $notificationService = app(NotificationService::class);
            $this->line('  ✅ NotificationService registered');
            
            $stats = $notificationService->getStatistics();
            $this->line("  📊 Registered notification types: {$stats['total_types']}");
            $this->line("  📊 Enabled channels: " . implode(', ', $stats['enabled_channels']));
            
        } catch (\Exception $e) {
            $this->error("  ❌ NotificationService registration failed: " . $e->getMessage());
        }

        try {
            $facadeTest = Notifications::getAvailableTypes();
            $this->line('  ✅ Notifications Facade working');
            $this->line("  📝 Available types: " . count($facadeTest));
        } catch (\Exception $e) {
            $this->error("  ❌ Notifications Facade failed: " . $e->getMessage());
        }
    }

    protected function checkNotificationClasses()
    {
        $this->info('📦 Checking Notification Classes:');
        
        $notificationClasses = [
            'App\Notifications\GenericNotification',
            'App\Notifications\WelcomeNotification', 
            'App\Notifications\EmailVerifiedNotification',
            'App\Notifications\ProjectCreatedNotification',
            'App\Notifications\QuotationCreatedNotification',
            'App\Notifications\MessageCreatedNotification',
            'App\Notifications\ChatSessionStartedNotification',
            'App\Notifications\TestimonialCreatedNotification'
        ];

        foreach ($notificationClasses as $class) {
            if (class_exists($class)) {
                $this->line("  ✅ {$class}");
            } else {
                $this->warn("  ⚠️  {$class} (missing but optional)");
            }
        }
    }

    protected function checkModelObservers()
    {
        $this->info('👁️  Checking Model Observers:');
        
        $models = [
            'App\Models\User',
            'App\Models\Project', 
            'App\Models\Quotation',
            'App\Models\Message'
        ];

        foreach ($models as $model) {
            if (class_exists($model)) {
                $this->line("  ✅ {$model} exists");
                
                // Check if observer is registered (simplified check)
                $observers = app('events')->getListeners("eloquent.created: {$model}");
                if (!empty($observers)) {
                    $this->line("  📝 Observer registered for {$model}");
                } else {
                    $this->warn("  ⚠️  No observer found for {$model}");
                }
            } else {
                $this->warn("  ⚠️  {$model} missing");
            }
        }
    }

    protected function checkConfiguration()
    {
        $this->info('⚙️ Checking Configuration:');
        
        // Check config files
        $configs = [
            'notifications.auto_notifications',
            'notifications.queue',
            'notifications.channels'
        ];

        foreach ($configs as $config) {
            $value = config($config);
            if ($value !== null) {
                $this->line("  ✅ {$config}: " . (is_bool($value) ? ($value ? 'true' : 'false') : json_encode($value)));
            } else {
                $this->warn("  ⚠️  {$config} not set");
            }
        }

        // Check queue configuration
        $queueConnection = config('queue.default');
        $this->line("  📤 Queue connection: {$queueConnection}");
    }

    protected function runTestNotifications()
    {
        $this->newLine();
        
        // Create test user if needed
        $testUser = $this->createTestUser();
        
        // Test 1: Welcome Notification
        $this->testWelcomeNotification($testUser);
        
        // Test 2: Generic Notification
        $this->testGenericNotification($testUser);
        
        // Test 3: Project Notification (if Project model exists)
        if (class_exists(Project::class)) {
            $this->testProjectNotification($testUser);
        }
        
        // Test 4: Message Notification (if Message model exists)
        if (class_exists(Message::class)) {
            $this->testMessageNotification();
        }
        
        // Test 5: Check database results
        $this->checkNotificationResults($testUser);
    }

    protected function createTestUser()
    {
        $this->line('👤 Creating test user...');
        
        $testUser = User::firstOrCreate(
            ['email' => 'test-notifications@example.com'],
            [
                'name' => 'Test Notification User',
                'password' => bcrypt('password'),
                'email_verified_at' => now()
            ]
        );

        $this->line("  ✅ Test user created/found: {$testUser->name} (ID: {$testUser->id})");
        return $testUser;
    }

    protected function testWelcomeNotification($user)
    {
        $this->line('📧 Testing Welcome Notification...');
        
        try {
            $result = Notifications::send('user.welcome', $user, $user);
            
            if ($result) {
                $this->line('  ✅ Welcome notification sent successfully');
            } else {
                $this->error('  ❌ Welcome notification failed');
            }
        } catch (\Exception $e) {
            $this->error("  ❌ Welcome notification error: " . $e->getMessage());
        }
    }

    protected function testGenericNotification($user)
    {
        $this->line('🔔 Testing Generic Notification...');
        
        try {
            $result = Notifications::send('system.test', [
                'message' => 'This is a test notification',
                'timestamp' => now()
            ], $user);
            
            if ($result) {
                $this->line('  ✅ Generic notification sent successfully');
            } else {
                $this->error('  ❌ Generic notification failed');
            }
        } catch (\Exception $e) {
            $this->error("  ❌ Generic notification error: " . $e->getMessage());
        }
    }

    protected function testProjectNotification($user)
    {
        $this->line('📁 Testing Project Notification...');
        
        try {
            // Create a test project
            $project = new Project([
                'title' => 'Test Notification Project',
                'description' => 'Project created for notification testing',
                'status' => 'planning',
                'client_id' => $user->id
            ]);
            
            // Don't save to avoid triggering observer, just test notification
            $result = Notifications::send('project.created', $project);
            
            if ($result) {
                $this->line('  ✅ Project notification sent successfully');
            } else {
                $this->error('  ❌ Project notification failed');
            }
        } catch (\Exception $e) {
            $this->error("  ❌ Project notification error: " . $e->getMessage());
        }
    }

    protected function testMessageNotification()
    {
        $this->line('💬 Testing Message Notification...');
        
        try {
            // Create a test message
            $message = new Message([
                'name' => 'Test Client',
                'email' => 'client@example.com',
                'subject' => 'Test Notification Message',
                'message' => 'This is a test message for notification testing',
                'type' => 'client_to_admin'
            ]);
            
            // Don't save to avoid triggering observer, just test notification
            $result = Notifications::send('message.created', $message);
            
            if ($result) {
                $this->line('  ✅ Message notification sent successfully');
            } else {
                $this->error('  ❌ Message notification failed');
            }
        } catch (\Exception $e) {
            $this->error("  ❌ Message notification error: " . $e->getMessage());
        }
    }

    protected function checkNotificationResults($user)
    {
        $this->newLine();
        $this->line('📊 Checking Notification Results:');
        
        // Count total notifications
        $totalNotifications = DB::table('notifications')->count();
        $this->line("  📝 Total notifications in database: {$totalNotifications}");
        
        // Count user notifications
        $userNotifications = $user->notifications()->count();
        $this->line("  👤 Test user notifications: {$userNotifications}");
        
        // Count unread notifications
        $unreadNotifications = $user->unreadNotifications()->count();
        $this->line("  📬 Unread notifications: {$unreadNotifications}");
        
        // Show recent notifications
        if ($userNotifications > 0) {
            $this->line('  📋 Recent notifications:');
            $recent = $user->notifications()->latest()->take(3)->get();
            
            foreach ($recent as $notification) {
                $type = $notification->data['type'] ?? 'unknown';
                $title = $notification->data['title'] ?? 'No title';
                $created = $notification->created_at->diffForHumans();
                $this->line("    • {$type}: {$title} ({$created})");
            }
        }
    }

    protected function cleanTestData()
    {
        $this->newLine();
        $this->line('🧹 Cleaning test data...');
        
        // Delete test user notifications
        $testUser = User::where('email', 'test-notifications@example.com')->first();
        if ($testUser) {
            $deletedCount = $testUser->notifications()->delete();
            $this->line("  🗑️  Deleted {$deletedCount} test notifications");
            
            if ($this->confirm('Delete test user as well?', false)) {
                $testUser->delete();
                $this->line('  🗑️  Deleted test user');
            }
        }
    }
}