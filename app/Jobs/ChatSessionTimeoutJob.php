<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ChatSession;
use App\Services\ChatService;
use App\Facades\Notifications;
use Illuminate\Support\Facades\Log;

class ChatSessionTimeoutJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $session;
    public $timeout = 60;
    public $tries = 2;

    public function __construct(ChatSession $session)
    {
        $this->session = $session;
    }

    public function handle(ChatService $chatService): void
    {
        try {
            // Reload session to get latest state
            $session = ChatSession::find($this->session->id);
            
            if (!$session || $session->status !== 'waiting') {
                Log::info('Session no longer waiting, skipping timeout', [
                    'session_id' => $this->session->session_id
                ]);
                return;
            }

            $waitingMinutes = now()->diffInMinutes($session->created_at);

            if ($waitingMinutes >= 30) {
                Log::info('Processing session timeout', [
                    'session_id' => $session->session_id,
                    'waiting_minutes' => $waitingMinutes
                ]);

                // Send timeout warning first (at 25 minutes)
                if ($waitingMinutes >= 25 && $waitingMinutes < 30) {
                    $this->sendTimeoutWarning($session);
                    return;
                }

                // Close session due to timeout
                $chatService->closeSession($session, 'Queue timeout after 30 minutes');
                
                // Notify user if authenticated
                if ($session->user) {
                    Notifications::send('chat.session_timeout', $session, $session->user);
                }

                Log::info('Session closed due to timeout', [
                    'session_id' => $session->session_id,
                    'waiting_minutes' => $waitingMinutes
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to process session timeout', [
                'session_id' => $this->session->session_id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function sendTimeoutWarning(ChatSession $session): void
    {
        $chatService = app(ChatService::class);
        
        $chatService->sendMessage(
            $session,
            "⚠️ You've been waiting for 25 minutes. We'll automatically close this chat in 5 minutes if no operator becomes available. You can start a new chat anytime.",
            'system'
        );
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Chat session timeout job failed', [
            'session_id' => $this->session->session_id,
            'error' => $exception->getMessage()
        ]);
    }
}