<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\ChatSession;
use App\Services\ChatService;
use App\Facades\Notifications;
use Illuminate\Console\Command;

class ChatTestCommand extends Command
{
    protected $signature = 'chat:test
                            {type : Test type (session, notification, performance)}
                            {--user= : User ID for session test}
                            {--count=10 : Number of test items to create}';

    protected $description = 'Test chat system functionality';

    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        parent::__construct();
        $this->chatService = $chatService;
    }

    public function handle()
    {
        $type = $this->argument('type');

        switch ($type) {
            case 'session':
                return $this->testChatSession();
            case 'notification':
                return $this->testNotifications();
            case 'performance':
                return $this->testPerformance();
            default:
                $this->error("Unknown test type: {$type}");
                return Command::FAILURE;
        }
    }

    protected function testChatSession()
    {
        $userId = $this->option('user');
        
        if (!$userId) {
            $user = User::whereHas('roles', function ($q) {
                $q->where('name', 'client');
            })->first();
            
            if (!$user) {
                $this->error('No client users found. Please specify --user option.');
                return Command::FAILURE;
            }
        } else {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User not found: {$userId}");
                return Command::FAILURE;
            }
        }

        $this->info("🧪 Testing chat session for user: {$user->name}");

        try {
            // Start session
            $session = $this->chatService->startSession($user);
            $this->info("✅ Session created: {$session->session_id}");

            // Send test messages
            $testMessages = [
                'Hello, I need help with my project',
                'Can you provide me with more information?',
                'This is urgent, please help!',
                'Thank you for your assistance'
            ];

            foreach ($testMessages as $messageText) {
                $message = $this->chatService->sendMessage($session, $messageText, 'visitor');
                $this->info("💬 Message sent: {$messageText}");
                sleep(1); // Simulate real conversation
            }

            // Auto-assign operator if available
            $assigned = $this->chatService->autoAssignSession($session);
            if ($assigned) {
                $this->info("👤 Operator assigned successfully");
            } else {
                $this->info("⏳ No operators available for assignment");
            }

            // Send operator response if assigned
            if ($session->assigned_operator_id) {
                $operatorMessage = $this->chatService->sendMessage(
                    $session, 
                    'Hello! I\'m here to help you with your project. How can I assist you today?', 
                    'operator'
                );
                $this->info("🎧 Operator response sent");
            }

            // Close session
            $this->chatService->closeSession($session, 'Test completed');
            $this->info("🔒 Session closed");

            $this->newLine();
            $this->info("✅ Chat session test completed successfully!");
            $this->info("Session ID: {$session->session_id}");
            $this->info("Messages: {$session->messages()->count()}");
            $this->info("Duration: {$session->getDuration()} minutes");

        } catch (\Exception $e) {
            $this->error("❌ Test failed: {$e->getMessage()}");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    protected function testNotifications()
    {
        $this->info("🧪 Testing chat notifications...");

        try {
            // Test session started notification
            $testSession = ChatSession::factory()->create([
                'session_id' => 'test-' . uniqid(),
                'status' => 'waiting',
            ]);

            $this->info("📧 Testing session started notification...");
            Notifications::send('chat.session_started', $testSession);
            $this->info("✅ Session started notification sent");

            // Test message received notification
            $this->info("📧 Testing message received notification...");
            Notifications::send('chat.message_received', $testSession);
            $this->info("✅ Message received notification sent");

            // Test session closed notification
            $testSession->update(['status' => 'closed', 'ended_at' => now()]);
            $this->info("📧 Testing session closed notification...");
            Notifications::send('chat.session_closed', $testSession);
            $this->info("✅ Session closed notification sent");

            // Clean up test session
            $testSession->delete();

            $this->newLine();
            $this->info("✅ Notification tests completed successfully!");

        } catch (\Exception $e) {
            $this->error("❌ Notification test failed: {$e->getMessage()}");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    protected function testPerformance()
    {
        $count = $this->option('count');
        $this->info("🧪 Testing chat system performance with {$count} sessions...");

        $startTime = microtime(true);
        $successCount = 0;
        $errorCount = 0;

        try {
            // Create test users if needed
            $testUsers = User::factory()->count(min($count, 10))->create();

            $this->withProgressBar($count, function () use ($testUsers, &$successCount, &$errorCount) {
                try {
                    $user = $testUsers->random();
                    
                    // Start session
                    $session = $this->chatService->startSession($user);
                    
                    // Send random number of messages
                    $messageCount = rand(1, 5);
                    for ($i = 0; $i < $messageCount; $i++) {
                        $this->chatService->sendMessage(
                            $session, 
                            'Test message ' . ($i + 1), 
                            'visitor'
                        );
                    }
                    
                    // Close session
                    $this->chatService->closeSession($session, 'Performance test');
                    
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                }
            });

            $endTime = microtime(true);
            $duration = $endTime - $startTime;

            $this->newLine();
            $this->newLine();
            
            $this->info("📊 Performance Test Results:");
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Total Sessions', $count],
                    ['Successful', $successCount],
                    ['Failed', $errorCount],
                    ['Success Rate', round(($successCount / $count) * 100, 2) . '%'],
                    ['Total Time', round($duration, 2) . ' seconds'],
                    ['Avg Time per Session', round($duration / $count, 3) . ' seconds'],
                    ['Sessions per Second', round($count / $duration, 2)],
                ]
            );

            // Clean up test users
            User::whereIn('id', $testUsers->pluck('id'))->delete();

            if ($errorCount === 0) {
                $this->info("✅ Performance test completed successfully!");
            } else {
                $this->warn("⚠️  Performance test completed with {$errorCount} errors");
            }

        } catch (\Exception $e) {
            $this->error("❌ Performance test failed: {$e->getMessage()}");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}