<?php
// =======================
// app/Jobs/ProcessChatQueueJob.php
// =======================

namespace App\Jobs;

use App\Services\ChatService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessChatQueueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;

    public function __construct()
    {
        $this->onQueue('chat');
    }

    public function handle(ChatService $chatService): void
    {
        try {
            Log::info('Processing chat queue...');
            
            $processedSessions = $chatService->processQueue();
            
            if (!empty($processedSessions)) {
                Log::info('Chat queue processed', [
                    'assigned_sessions' => count($processedSessions),
                    'session_ids' => $processedSessions
                ]);
            }

            // Schedule next queue processing
            $this->scheduleNext();

        } catch (\Exception $e) {
            Log::error('Chat queue processing failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            
            throw $e;
        }
    }

    private function scheduleNext(): void
    {
        ProcessChatQueueJob::dispatch()
            ->delay(now()->addSeconds(30))
            ->onQueue('chat');
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Chat queue job failed permanently', [
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}