<?php

namespace App\Services;

use App\Facades\Notifications;
use App\Models\ChatOperator;
use App\Models\ChatSession;
use App\Models\User;

class ChatService
{
    public function startSession(User $user): ChatSession
    {
        return ChatSession::create([
            'user_id' => $user->id,
            'started_at' => now(),
        ]);
    }

    public function endSession(ChatSession $session): void
    {
        $session->update(['ended_at' => now()]);
    }

    public function assignOperator(ChatSession $session): void
    {
        $operator = ChatOperator::available()->first();

        if ($operator) {
            $session->update(['operator_id' => $operator->id]);
        }
    }

    public function sendMessage(ChatSession $session, User $sender, string $message): void
    {
        $session->messages()->create([
            'sender_id' => $sender->id,
            'message' => $message,
        ]);

        $this->notifyOperators($session, $message);
    }

    protected function notifyOperators(ChatSession $session, string $message): void
    {
        Notifications::send('chat.message_received', $session);
    }
}
