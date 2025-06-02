<?php

namespace App\Notifications;

use App\Models\ChatSession;

class ChatSessionWaitingNotification extends BaseNotification
{
    protected function configure(): void
    {
        $chatSession = $this->data;
        
        if ($chatSession instanceof ChatSession) {
            $waitingMinutes = now()->diffInMinutes($chatSession->created_at);
            
            $this->subject = "Chat Session Waiting - Action Required";
            $this->greeting = "Chat Alert!";
            
            $this->addLine("A chat session has been waiting for {$waitingMinutes} minutes:");
            $this->addLine("Visitor: " . $chatSession->getVisitorName());
            $this->addLine("Started: " . $chatSession->created_at->format('H:i'));
            
            $this->addLine("Please assign an operator to this chat session.");
            
            $this->setAction('Handle Chat', route('admin.chat.show', $chatSession));
            $this->salutation = 'Best regards,<br>' . config('app.name') . ' System';
        }
    }
}
