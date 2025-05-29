<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Facades\Notifications;
use App\Models\User;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Message;

class NotificationTestCommand extends Command
{
    protected $signature = 'notifications:test {--user= : User ID to test with} {--type= : Specific notification type to test}';
    protected $description = 'Test the centralized notification system';

    public function handle(): int
    {
        $this->info('🧪 Testing Centralized Notification System');
        $this->newLine();

        // Get test user
        $userId = $this->option('user');
        $user = $userId ? User::find($userId) : User::role(['admin', 'super-admin'])->first();
        
        if (!$user) {
            $this->error('❌ No suitable test user found. Please specify --user=ID or ensure admin users exist.');
            return Command::FAILURE;
        }

        $this->info("👤 Testing with user: {$user->name} ({$user->email})");
        $this->newLine();

        // Test specific type or all types
        $testType = $this->option('type');
        
        if ($testType) {
            return $this->testSpecificType($testType, $user);
        } else {
            return $this->testAllTypes($user);
        }
    }

    protected function testSpecificType(string $type, User $user): int
    {
        $this->info("🎯 Testing notification type: {$type}");
        
        if (!Notifications::hasType($type)) {
            $this->error("❌ Notification type '{$type}' not found");
            $this->line('Available types:');
            foreach (Notifications::getAvailableTypes() as $availableType) {
                $this->line("  - {$availableType}");
            }
            return Command::FAILURE;
        }

        $testData = $this->getTestData($type);
        $result = Notifications::send($type, $testData, $user);

        if ($result) {
            $this->info("✅ Successfully sent '{$type}' notification");
        } else {
            $this->error("❌ Failed to send '{$type}' notification");
        }

        return Command::SUCCESS;
    }

    protected function testAllTypes(User $user): int
    {
        $this->info('🔄 Running comprehensive notification tests...');
        $this->newLine();

        $testResults = [];
        $testTypes = [
            'user.welcome',
            'user.profile_incomplete',
            'system.maintenance',
        ];

        // Add more specific tests if data exists
        if (Project::exists()) {
            $testTypes[] = 'project.created';
            $testTypes[] = 'project.deadline_approaching';
        }

        if (Quotation::exists()) {
            $testTypes[] = 'quotation.created';
            $testTypes[] = 'quotation.status_updated';
        }

        if (Message::exists()) {
            $testTypes[] = 'message.created';
        }

        foreach ($testTypes as $type) {
            $this->line("Testing: {$type}");
            
            try {
                $testData = $this->getTestData($type);
                $result = Notifications::send($type, $testData, $user);
                
                $testResults[$type] = $result;
                
                if ($result) {
                    $this->info("  ✅ Success");
                } else {
                    $this->error("  ❌ Failed");
                }
                
            } catch (\Exception $e) {
                $testResults[$type] = false;
                $this->error("  ❌ Error: " . $e->getMessage());
            }
        }

        // Summary
        $this->newLine();
        $this->info('📊 Test Results Summary:');
        $passed = count(array_filter($testResults));
        $total = count($testResults);
        
        $this->line("Passed: {$passed}/{$total}");
        
        if ($passed === $total) {
            $this->info('🎉 All tests passed!');
            return Command::SUCCESS;
        } else {
            $this->error('❌ Some tests failed. Check logs for details.');
            return Command::FAILURE;
        }
    }

    protected function getTestData(string $type): mixed
    {
        return match($type) {
            'project.created', 'project.updated', 'project.deadline_approaching' => 
                Project::first() ?? $this->createMockProject(),
            
            'quotation.created', 'quotation.status_updated', 'quotation.approved' => 
                Quotation::first() ?? $this->createMockQuotation(),
            
            'message.created', 'message.reply', 'message.urgent' => 
                Message::first() ?? $this->createMockMessage(),
            
            'user.welcome', 'user.profile_incomplete' => 
                User::first(),
            
            'system.maintenance' => [
                'start_time' => now()->addHours(2)->format('Y-m-d H:i:s'),
                'end_time' => now()->addHours(4)->format('Y-m-d H:i:s'),
                'description' => 'Scheduled system maintenance for testing'
            ],
            
            default => null,
        };
    }

    protected function createMockProject(): object
    {
        return (object) [
            'id' => 999,
            'title' => 'Test Project',
            'status' => 'in_progress',
            'client' => (object) ['name' => 'Test Client'],
            'start_date' => now(),
            'end_date' => now()->addDays(30),
        ];
    }

    protected function createMockQuotation(): object
    {
        return (object) [
            'id' => 999,
            'name' => 'Test Client',
            'email' => 'test@example.com',
            'project_type' => 'Test Project Type',
            'status' => 'pending',
            'service' => (object) ['title' => 'Test Service'],
            'budget_range' => '$10,000 - $20,000',
        ];
    }

    protected function createMockMessage(): object
    {
        return (object) [
            'id' => 999,
            'name' => 'Test Sender',
            'email' => 'sender@example.com',
            'subject' => 'Test Message Subject',
            'message' => 'This is a test message content.',
            'priority' => 'normal',
            'created_at' => now(),
        ];
    }
}