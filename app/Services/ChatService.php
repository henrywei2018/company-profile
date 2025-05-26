<?php
// app/Services/ChatService.php

namespace App\Services;

use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Models\ChatOperator;
use App\Models\ChatTemplate;
use App\Models\User;
use App\Notifications\NewChatMessageNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class ChatService
{
    /**
     * Start a new chat session
     */
    public function startSession(array $visitorInfo = null, ?User $user = null): ChatSession
    {
        $session = ChatSession::create([
            'user_id' => $user?->id,
            'visitor_info' => $visitorInfo,
            'status' => 'active',
            'priority' => 'normal',
            'source' => 'website',
            'started_at' => now(),
            'last_activity_at' => now(),
        ]);

        // Send greeting message
        $this->sendGreeting($session);

        return $session;
    }

    /**
     * Send a message in a chat session
     */
    public function sendMessage(
        ChatSession $session, 
        string $message, 
        string $senderType = 'visitor',
        ?User $sender = null
    ): ChatMessage {
        $chatMessage = ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender_type' => $senderType,
            'sender_id' => $sender?->id,
            'message' => $message,
            'message_type' => 'text',
        ]);

        // Update session activity
        $session->updateActivity();

        // Handle auto-responses for visitor messages
        if ($senderType === 'visitor') {
            $this->handleVisitorMessage($session, $message);
        }

        return $chatMessage;
    }

    /**
     * Handle visitor messages with auto-responses
     */
    protected function handleVisitorMessage(ChatSession $session, string $message): void
    {
        // Check if any operators are available
        $availableOperator = $this->getAvailableOperator();

        if (!$availableOperator) {
            // No operators available - handle with bot
            $this->handleOfflineMessage($session, $message);
        } else {
            // Operator available - check if should auto-respond or assign
            $autoResponse = $this->getAutoResponse($message);
            
            if ($autoResponse) {
                $this->sendBotMessage($session, $autoResponse);
            }

            // Notify available operators about new message
            $this->notifyOperators($session, $message);
        }
    }

    /**
     * Handle messages when no operators are online
     */
    protected function handleOfflineMessage(ChatSession $session, string $message): void
    {
        // Update session status to waiting
        $session->update(['status' => 'waiting']);

        // Get appropriate offline response
        $response = $this->getOfflineResponse($message);
        
        // Send bot response
        $this->sendBotMessage($session, $response);

        // Collect visitor info if not already collected
        if (!$session->visitor_info || !isset($session->visitor_info['email'])) {
            $this->requestVisitorInfo($session);
        }

        // Notify administrators about offline message
        $this->notifyAdminsOfflineMessage($session, $message);
    }

    /**
     * Send bot message
     */
    public function sendBotMessage(ChatSession $session, string $message): ChatMessage
    {
        return ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender_type' => 'bot',
            'message' => $message,
            'message_type' => 'text',
        ]);
    }

    /**
     * Send greeting message
     */
    protected function sendGreeting(ChatSession $session): void
    {
        $greeting = $this->getGreeting();
        $this->sendBotMessage($session, $greeting);
    }

    /**
     * Get greeting message
     */
    protected function getGreeting(): string
    {
        $template = ChatTemplate::active()
            ->where('type', 'greeting')
            ->first();

        if ($template) {
            $template->incrementUsage();
            return $template->message;
        }

        return "Hello! 👋 Welcome to CV Usaha Prima Lestari. How can I help you today?\n\nI can assist you with:\n• 🏗️ Construction Services\n• 💰 Request a Quote\n• 📞 Contact Information\n• 📋 Project Portfolio\n\nJust type your question!";
    }

    /**
     * Get auto-response based on message content
     */
    protected function getAutoResponse(string $message): ?string
    {
        $message = strtolower($message);

        // Check for template matches
        $template = ChatTemplate::active()
            ->where('type', 'auto_response')
            ->get()
            ->first(function ($template) use ($message) {
                return $template->matchesTrigger($message);
            });

        if ($template) {
            $template->incrementUsage();
            return $template->message;
        }

        // Simple keyword-based responses
        $responses = [
            'quote' => "I'd be happy to help you with a quotation! Let me connect you with our sales team. In the meantime, you can also fill out our quick quote form at " . route('quotation.form'),
            'price' => "For pricing information, I'll connect you with our sales team who can provide accurate quotes based on your specific needs.",
            'service' => "We offer comprehensive construction services including building construction, infrastructure development, and general supplier services. What specific service are you interested in?",
            'contact' => "You can reach us at:\n📞 " . settings('contact_phone', '+62 XXX XXXX XXXX') . "\n📧 " . settings('contact_email', 'info@usahaprimaestari.com') . "\n📍 " . settings('contact_address', 'Jakarta, Indonesia'),
            'hello' => "Hello! How can I assist you today?",
            'help' => "I'm here to help! You can ask me about our services, request a quote, or get our contact information. What would you like to know?",
        ];

        foreach ($responses as $keyword => $response) {
            if (str_contains($message, $keyword)) {
                return $response;
            }
        }

        return null;
    }

    /**
     * Get offline response
     */
    protected function getOfflineResponse(string $message): string
    {
        $template = ChatTemplate::active()
            ->where('type', 'offline')
            ->first();

        if ($template) {
            $template->incrementUsage();
            return $template->message;
        }

        return "Thank you for contacting us! 🙏\n\nOur team is currently offline, but I'm here to help. I've received your message and our team will respond within 2 hours during business hours.\n\nFor urgent matters, you can:\n📞 Call us at " . settings('contact_phone', '+62 XXX XXXX XXXX') . "\n📧 Email us at " . settings('admin_email', 'admin@usahaprimaestari.com');
    }

    /**
     * Request visitor information
     */
    protected function requestVisitorInfo(ChatSession $session): void
    {
        $message = "To ensure we can follow up with you, could you please provide:\n• Your name\n• Email address\n• Phone number (optional)\n\nThis helps us give you the best service possible!";
        
        $this->sendBotMessage($session, $message);
    }

    /**
     * Get available operator
     */
    protected function getAvailableOperator(): ?ChatOperator
    {
        return ChatOperator::online()
            ->available()
            ->whereRaw('current_chats_count < max_concurrent_chats')
            ->orderBy('current_chats_count')
            ->first();
    }

    /**
     * Assign chat to operator
     */
    public function assignToOperator(ChatSession $session, ChatOperator $operator): void
    {
        $session->assignOperator($operator->user);
        $operator->incrementChatCount();

        // Send system message
        $this->sendSystemMessage($session, "Chat assigned to " . $operator->user->name);
    }

    /**
     * Send system message
     */
    public function sendSystemMessage(ChatSession $session, string $message): ChatMessage
    {
        return ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender_type' => 'system',
            'message' => $message,
            'message_type' => 'system',
        ]);
    }

    /**
     * Close chat session
     */
    public function closeSession(ChatSession $session, ?string $summary = null): void
    {
        $session->update([
            'status' => 'closed',
            'ended_at' => now(),
            'summary' => $summary,
        ]);

        // Update operator chat count
        if ($session->assigned_operator_id) {
            $operator = ChatOperator::where('user_id', $session->assigned_operator_id)->first();
            $operator?->decrementChatCount();
        }

        // Send closing message
        $this->sendSystemMessage($session, "Chat session ended");
    }

    /**
     * Get chat session by ID
     */
    public function getSession(string $sessionId): ?ChatSession
    {
        return ChatSession::where('session_id', $sessionId)->first();
    }

    /**
     * Get chat messages for session
     */
    public function getMessages(ChatSession $session, int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return $session->messages()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }

    /**
     * Update visitor information
     */
    public function updateVisitorInfo(ChatSession $session, array $info): void
    {
        $currentInfo = $session->visitor_info ?? [];
        $updatedInfo = array_merge($currentInfo, $info);
        
        $session->update(['visitor_info' => $updatedInfo]);
    }

    /**
     * Notify operators about new message
     */
    protected function notifyOperators(ChatSession $session, string $message): void
    {
        $operators = ChatOperator::online()->available()->get();
        
        foreach ($operators as $operator) {
            Notification::send(
                $operator->user, 
                new NewChatMessageNotification($session, $message)
            );
        }
    }

    /**
     * Notify administrators about offline message
     */
    protected function notifyAdminsOfflineMessage(ChatSession $session, string $message): void
    {
        $adminEmails = [
            settings('admin_email', 'admin@usahaprimaestari.com'),
            settings('support_email', 'support@usahaprimaestari.com')
        ];

        foreach (array_filter($adminEmails) as $email) {
            // Send email notification
            \Mail::to($email)->send(new \App\Mail\OfflineChatMessage($session, $message));
        }
    }

    /**
     * Get chat statistics
     */
    public function getStatistics(): array
    {
        return [
            'total_sessions' => ChatSession::count(),
            'active_sessions' => ChatSession::where('status', 'active')->count(),
            'waiting_sessions' => ChatSession::where('status', 'waiting')->count(),
            'online_operators' => ChatOperator::online()->count(),
            'available_operators' => ChatOperator::online()->available()->count(),
            'avg_response_time' => $this->getAverageResponseTime(),
            'today_sessions' => ChatSession::whereDate('created_at', today())->count(),
        ];
    }

    /**
     * Calculate average response time
     */
    protected function getAverageResponseTime(): float
    {
        // Simple calculation - can be improved
        $sessions = ChatSession::where('status', 'closed')
            ->whereNotNull('ended_at')
            ->take(100)
            ->get();

        if ($sessions->isEmpty()) {
            return 0;
        }

        $totalMinutes = $sessions->sum(function ($session) {
            return $session->getDuration() ?? 0;
        });

        return round($totalMinutes / $sessions->count(), 2);
    }
}