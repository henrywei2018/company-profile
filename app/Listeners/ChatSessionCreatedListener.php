<?php

namespace App\Listeners;

use App\Events\ChatSessionStarted;
use App\Jobs\ChatSessionTimeoutJob;
use App\Jobs\NotifyOperatorsJob;
use App\Jobs\ProcessChatQueueJob;
use Illuminate\Support\Facades\Log;

class ChatSessionCreatedListener
{
    public function handle(ChatSessionStarted $event): void
    {
        $session = $event->session;

        try {
            Log::info('Handling new chat session creation', [
                'session_id' => $session->session_id,
                'user_id' => $session->user_id
            ]);

            // 1. Schedule timeout job for 30 minutes
            ChatSessionTimeoutJob::dispatch($session)
                ->delay(now()->addMinutes(30));

            // 2. Notify available operators immediately
            NotifyOperatorsJob::dispatch($session, 'normal')
                ->delay(now()->addSeconds(5)); // Small delay to ensure session is fully created

            // 3. Trigger queue processing in case auto-assignment is possible
            ProcessChatQueueJob::dispatch()
                ->delay(now()->addSeconds(10));

            Log::info('Jobs scheduled for new chat session', [
                'session_id' => $session->session_id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to handle chat session creation', [
                'session_id' => $session->session_id,
                'error' => $e->getMessage()
            ]);
        }
    }
}