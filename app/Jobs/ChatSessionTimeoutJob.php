<?php

namespace App\Jobs;

use App\Models\ChatSession;
use App\Services\ChatService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ChatSessionTimeoutJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    public function __construct()
    {
        $this->onQueue('chat');
    }

    public function handle(ChatService $chatService): void
    {
        try {
            Log::info('Checking for inactive chat sessions...');
            
            $closedSessions = $chatService->closeInactiveSessions();
            
            if (!empty($closedSessions)) {
                Log::info('Closed inactive chat sessions', [
                    'closed_count' => count($closedSessions),
                    'session_ids' => $closedSessions
                ]);
            }

            // Schedule next timeout check
            $this->scheduleNext();

        } catch (\Exception $e) {
            Log::error('Chat timeout check failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            
            throw $e;
        }
    }

    private function scheduleNext(): void
    {
        ChatSessionTimeoutJob::dispatch()
            ->delay(now()->addMinutes(5))
            ->onQueue('chat');
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Chat timeout job failed permanently', [
            'exception' => $exception->getMessage()
        ]);
    }
}