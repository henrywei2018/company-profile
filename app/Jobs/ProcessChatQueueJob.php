<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\ChatService;
use App\Models\ChatSession;
use Illuminate\Support\Facades\Log;

class ProcessChatQueueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;

    public function handle(ChatService $chatService): void
    {
        try {
            Log::info('🔄 Starting chat queue processing job');

            // 1. Auto-assign waiting sessions
            $assigned = $chatService->autoAssignWaitingSessions();
            
            // 2. Check for operator timeouts
            $this->checkOperatorTimeouts();
            
            // 3. Handle session timeouts
            $this->handleSessionTimeouts($chatService);
            
            // 4. Update queue metrics
            $this->updateQueueMetrics($chatService);

            Log::info('✅ Chat queue processing completed', [
                'sessions_assigned' => $assigned,
                'queue_length' => ChatSession::where('status', 'waiting')->count()
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Chat queue processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function checkOperatorTimeouts(): void
    {
        $inactiveThreshold = now()->subMinutes(10);
        
        \App\Models\ChatOperator::where('is_online', true)
            ->where('last_seen_at', '<=', $inactiveThreshold)
            ->update(['is_online' => false, 'is_available' => false]);
    }

    private function handleSessionTimeouts(ChatService $chatService): void
    {
        $timeoutThreshold = now()->subMinutes(30);
        
        $timeoutSessions = ChatSession::where('status', 'waiting')
            ->where('created_at', '<=', $timeoutThreshold)
            ->get();

        foreach ($timeoutSessions as $session) {
            $chatService->closeSession($session, 'Queue timeout after 30 minutes');
        }
    }

    private function updateQueueMetrics(ChatService $chatService): void
    {
        $metrics = $chatService->getRealTimeMetrics();
        
        // Store metrics for analytics (you can store in cache or database)
        cache()->put('chat_queue_metrics', $metrics, now()->addMinutes(60));
        
        // Optional: Store historical metrics in database
        \DB::table('chat_queue_metrics')->insert([
            'timestamp' => now(),
            'waiting_sessions' => $metrics['waiting_sessions'] ?? 0,
            'active_sessions' => $metrics['active_sessions'] ?? 0,
            'operators_online' => $metrics['operators_online'] ?? 0,
            'avg_wait_time' => $metrics['avg_wait_time'] ?? 0,
            'created_at' => now(),
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('❌ Chat queue processing job failed', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}