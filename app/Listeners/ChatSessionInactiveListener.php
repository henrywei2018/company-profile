<?php

namespace App\Listeners;

use App\Models\ChatSession;
use App\Facades\Notifications;
use Illuminate\Support\Facades\Log;

class ChatSessionInactiveListener
{
    /**
     * Handle sessions that have been inactive
     */
    public function handle(): void
    {
        $inactiveThreshold = now()->subMinutes(30);
        $warningThreshold = now()->subMinutes(20);

        try {
            // Find sessions that need warnings
            $sessionsNeedingWarning = ChatSession::whereIn('status', ['active', 'waiting'])
                ->where('last_activity_at', '<', $warningThreshold)
                ->where('last_activity_at', '>', $inactiveThreshold)
                ->whereNull('inactivity_warning_sent_at')
                ->get();

            foreach ($sessionsNeedingWarning as $session) {
                $this->sendInactivityWarning($session);
            }

            // Find sessions that should be closed due to inactivity
            $inactiveSessions = ChatSession::whereIn('status', ['active', 'waiting'])
                ->where('last_activity_at', '<', $inactiveThreshold)
                ->get();

            foreach ($inactiveSessions as $session) {
                $this->closeInactiveSession($session);
            }

        } catch (\Exception $e) {
            Log::error('Error handling inactive chat sessions: ' . $e->getMessage());
        }
    }

    /**
     * Send inactivity warning
     */
    protected function sendInactivityWarning($session): void
    {
        $warningMessage = 'This chat has been inactive for a while. Are you still there?';
        
        $session->messages()->create([
            'sender_type' => 'system',
            'message' => $warningMessage,
            'message_type' => 'system',
        ]);

        $session->update(['inactivity_warning_sent_at' => now()]);

        Log::info('Inactivity warning sent', ['session_id' => $session->session_id]);
    }

    /**
     * Close inactive session
     */
    protected function closeInactiveSession($session): void
    {
        $session->update([
            'status' => 'closed',
            'ended_at' => now(),
            'close_reason' => 'Closed due to inactivity',
        ]);

        // Add system message
        $session->messages()->create([
            'sender_type' => 'system',
            'message' => 'This chat has been closed due to inactivity.',
            'message_type' => 'system',
        ]);

        // Notify about closure
        Notifications::send('chat.session_closed', $session);

        // Decrement operator's chat count if assigned
        if ($session->assigned_operator_id) {
            $operator = \App\Models\ChatOperator::where('user_id', $session->assigned_operator_id)->first();
            if ($operator) {
                $operator->decrementChatCount();
            }
        }

        Log::info('Chat session closed due to inactivity', ['session_id' => $session->session_id]);
    }
}