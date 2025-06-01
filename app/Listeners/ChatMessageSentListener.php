<?php

namespace App\Listeners;

use App\Events\ChatMessageSent;
use App\Facades\Notifications;
use App\Models\ChatOperator;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ChatMessageSentListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(ChatMessageSent $event): void
    {
        $message = $event->message;
        $session = $event->session;

        try {
            // Update session activity
            $session->update(['last_activity_at' => now()]);

            // Handle different message types
            $this->handleMessageType($message, $session);

            // Check for auto-responses
            $this->checkAutoResponses($message, $session);

            // Notify relevant parties
            $this->notifyRelevantParties($message, $session);

            // Log message
            Log::info('Chat message sent', [
                'message_id' => $message->id,
                'session_id' => $session->session_id,
                'sender_type' => $message->sender_type,
            ]);

        } catch (\Exception $e) {
            Log::error('Error handling chat message sent event: ' . $e->getMessage(), [
                'message_id' => $message->id,
                'session_id' => $session->session_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle different message types
     */
    protected function handleMessageType($message, $session): void
    {
        switch ($message->sender_type) {
            case 'visitor':
                $this->handleVisitorMessage($message, $session);
                break;
            case 'operator':
                $this->handleOperatorMessage($message, $session);
                break;
            case 'bot':
                $this->handleBotMessage($message, $session);
                break;
        }
    }

    /**
     * Handle visitor messages
     */
    protected function handleVisitorMessage($message, $session): void
    {
        // If session is waiting and no operator assigned, try to assign
        if ($session->status === 'waiting' && !$session->assigned_operator_id) {
            $this->tryAssignOperator($session);
        }

        // Check if message needs urgent attention
        if ($this->isUrgentMessage($message)) {
            $session->update(['priority' => 'urgent']);
        }
    }

    /**
     * Handle operator messages
     */
    protected function handleOperatorMessage($message, $session): void
    {
        // Mark session as active
        if ($session->status !== 'active') {
            $session->update(['status' => 'active']);
        }

        // Assign operator if not already assigned
        if (!$session->assigned_operator_id) {
            $session->update(['assigned_operator_id' => $message->sender_id]);
        }
    }

    /**
     * Handle bot messages
     */
    protected function handleBotMessage($message, $session): void
    {
        // Log bot interaction for analytics
        Log::info('Bot message sent', [
            'session_id' => $session->session_id,
            'message_type' => $message->message_type,
            'template_id' => $message->metadata['template_id'] ?? null,
        ]);
    }

    /**
     * Try to assign operator to session
     */
    protected function tryAssignOperator($session): void
    {
        $operator = ChatOperator::where('is_online', true)
            ->where('is_available', true)
            ->whereHas('user', function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->whereIn('name', ['super-admin', 'admin', 'manager']);
                });
            })
            ->where('current_chats_count', '<', \DB::raw('max_concurrent_chats'))
            ->orderBy('current_chats_count')
            ->first();

        if ($operator) {
            $session->update([
                'assigned_operator_id' => $operator->user_id,
                'status' => 'active',
            ]);

            $operator->incrementChatCount();

            // Notify the assigned operator
            Notifications::send('chat.session_assigned', $session, $operator->user);
        }
    }

    /**
     * Check if message is urgent
     */
    protected function isUrgentMessage($message): bool
    {
        $urgentKeywords = [
            'urgent', 'emergency', 'asap', 'immediately', 'help',
            'problem', 'issue', 'broken', 'error', 'critical'
        ];

        $messageText = strtolower($message->message);

        foreach ($urgentKeywords as $keyword) {
            if (str_contains($messageText, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check for auto-responses
     */
    protected function checkAutoResponses($message, $session): void
    {
        if ($message->sender_type !== 'visitor') {
            return;
        }

        if (!settings('chat_auto_response_enabled', true)) {
            return;
        }

        // Check for template triggers
        $templates = \App\Models\ChatTemplate::where('type', 'auto_response')
            ->where('is_active', true)
            ->whereNotNull('trigger')
            ->get();

        foreach ($templates as $template) {
            if ($template->matchesTrigger($message->message)) {
                // Send auto-response
                $session->messages()->create([
                    'sender_type' => 'bot',
                    'message' => $template->message,
                    'message_type' => 'template',
                    'metadata' => ['template_id' => $template->id, 'auto_response' => true],
                ]);

                $template->incrementUsage();
                break; // Only send one auto-response
            }
        }
    }

    /**
     * Notify relevant parties about the message
     */
    protected function notifyRelevantParties($message, $session): void
    {
        if ($message->sender_type === 'visitor') {
            // Notify assigned operator
            if ($session->assigned_operator_id) {
                $operator = User::find($session->assigned_operator_id);
                if ($operator) {
                    Notifications::send('chat.message_received', $session, $operator, ['database']);
                }
            } else {
                // Notify all available operators
                $operators = User::whereHas('roles', function ($q) {
                    $q->whereIn('name', ['super-admin', 'admin', 'manager']);
                })
                ->whereHas('chatOperator', function ($q) {
                    $q->where('is_online', true)->where('is_available', true);
                })
                ->get();

                foreach ($operators as $operator) {
                    Notifications::send('chat.message_received', $session, $operator, ['database']);
                }
            }
        } elseif ($message->sender_type === 'operator') {
            // Notify client if they have an account
            if ($session->user) {
                Notifications::send('chat.operator_reply', $session, $session->user, ['database']);
            }
        }
    }
}