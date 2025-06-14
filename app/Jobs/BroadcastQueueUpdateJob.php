<?php

// File: app/Jobs/BroadcastQueueUpdateJob.php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ChatSession;
use App\Services\ChatService;
use Illuminate\Support\Facades\Log;

class BroadcastQueueUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 30;
    public $tries = 3;

    public function handle(ChatService $chatService): void
    {
        try {
            $metrics = $chatService->getRealTimeMetrics();
            
            // Broadcast queue status to public channel
            $queueData = [
                'waiting_sessions' => $metrics['waiting_sessions'] ?? 0,
                'operators_online' => $metrics['operators_online'] ?? 0,
                'estimated_wait_minutes' => $chatService->getEstimatedWaitTime(),
                'timestamp' => now()->toISOString()
            ];

            // Broadcast to public chat status channel
            if (class_exists('\App\Events\ChatQueueUpdated')) {
                broadcast(new \App\Events\ChatQueueUpdated($queueData))->toOthers();
            }

            // Update individual session positions
            $this->updateSessionPositions($chatService);

            Log::info('Queue status broadcast completed', [
                'waiting_sessions' => $queueData['waiting_sessions'],
                'operators_online' => $queueData['operators_online']
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to broadcast queue update', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function updateSessionPositions(ChatService $chatService): void
    {
        $waitingSessions = ChatSession::where('status', 'waiting')
            ->byPriority('desc')
            ->orderBy('created_at')
            ->get();

        foreach ($waitingSessions as $index => $session) {
            $position = $index + 1;
            $estimatedWait = $chatService->getEstimatedWaitTime();
            
            // Broadcast position update to specific session channel
            if (class_exists('\App\Events\ChatSessionPositionUpdated')) {
                try {
                    broadcast(new \App\Events\ChatSessionPositionUpdated($session, $position, $estimatedWait))
                        ->toOthers();
                } catch (\Exception $e) {
                    Log::warning('Failed to broadcast position update', [
                        'session_id' => $session->session_id,
                        'position' => $position,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Queue update broadcast job failed', [
            'error' => $exception->getMessage()
        ]);
    }
}