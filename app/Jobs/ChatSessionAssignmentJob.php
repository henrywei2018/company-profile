<?php

namespace App\Jobs;

use App\Models\ChatSession;
use App\Services\ChatService;
use App\Facades\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ChatSessionAssignmentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ChatSession $session;
    public $tries = 3;
    public $timeout = 60;

    public function __construct(ChatSession $session)
    {
        $this->session = $session;
        $this->onQueue('chat');
    }

    public function handle(ChatService $chatService): void
    {
        try {
            // Attempt auto-assignment
            if ($chatService->attemptAutoAssignment($this->session)) {
                Log::info('Chat session auto-assigned', [
                    'session_id' => $this->session->session_id,
                    'operator_id' => $this->session->assigned_operator_id
                ]);

                // Notify operator about new assignment
                Notifications::send('chat.session_assigned', $this->session);
            } else {
                Log::info('No available operators for session', [
                    'session_id' => $this->session->session_id
                ]);

                // Retry assignment after delay
                $this->retryAssignment();
            }

        } catch (\Exception $e) {
            Log::error('Chat assignment failed: ' . $e->getMessage(), [
                'session_id' => $this->session->session_id,
                'exception' => $e
            ]);
            
            throw $e;
        }
    }

    private function retryAssignment(): void
    {
        ChatSessionAssignmentJob::dispatch($this->session)
            ->delay(now()->addMinutes(2))
            ->onQueue('chat');
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Chat assignment job failed permanently', [
            'session_id' => $this->session->session_id,
            'exception' => $exception->getMessage()
        ]);
    }
}