<?php
// File: app/Listeners/ChatSessionStartedListener.php

namespace App\Listeners;

use App\Events\ChatSessionStarted;
use App\Facades\Notifications;
use App\Models\ChatOperator;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ChatSessionStartedListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(ChatSessionStarted $event): void
    {
        $session = $event->session;

        try {
            // Auto-assign to available operator if possible
            $this->tryAutoAssign($session);

            // Notify all available operators
            $this->notifyAvailableOperators($session);

            // Send auto-greeting if enabled
            $this->sendAutoGreeting($session);

            // Log session start
            Log::info('Chat session started', [
                'session_id' => $session->session_id,
                'user_id' => $session->user_id,
                'visitor_name' => $session->getVisitorName(),
            ]);

        } catch (\Exception $e) {
            Log::error('Error handling chat session started event: ' . $e->getMessage(), [
                'session_id' => $session->session_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Try to auto-assign session to available operator
     */
    protected function tryAutoAssign($session): void
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

            // Add system message
            $session->messages()->create([
                'sender_type' => 'system',
                'message' => 'Connected with ' . $operator->user->name,
                'message_type' => 'system',
            ]);

            // Notify the assigned operator
            Notifications::send('chat.session_assigned', $session, $operator->user);
        }
    }

    /**
     * Notify all available operators about new session
     */
    protected function notifyAvailableOperators($session): void
    {
        $operators = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['super-admin', 'admin', 'manager']);
        })
        ->whereHas('chatOperator', function ($q) {
            $q->where('is_online', true)->where('is_available', true);
        })
        ->get();

        foreach ($operators as $operator) {
            Notifications::send('chat.session_started', $session, $operator, ['database']);
        }
    }

    /**
     * Send auto-greeting message if enabled
     */
    protected function sendAutoGreeting($session): void
    {
        if (!settings('chat_auto_greeting_enabled', true)) {
            return;
        }

        $greetingTemplate = \App\Models\ChatTemplate::where('type', 'greeting')
            ->where('is_active', true)
            ->first();

        if ($greetingTemplate) {
            $session->messages()->create([
                'sender_type' => 'bot',
                'message' => $greetingTemplate->message,
                'message_type' => 'template',
                'metadata' => ['template_id' => $greetingTemplate->id],
            ]);

            $greetingTemplate->incrementUsage();
        } else {
            // Default greeting
            $defaultGreeting = settings('chat_greeting', 'Hello! How can we help you today?');
            
            $session->messages()->create([
                'sender_type' => 'bot',
                'message' => $defaultGreeting,
                'message_type' => 'text',
            ]);
        }
    }
}