<?php

namespace App\Notifications;

use App\Models\ChatSession;

class ChatSessionStartedNotification extends BaseNotification
{
    protected function configure(): void
    {
        $chatSession = $this->data;
        
        if ($chatSession instanceof ChatSession) {
            $this->subject = "New Chat Session Started";
            $this->greeting = "New Chat!";
            
            $this->addLine("A new chat session has been started:");
            $this->addLine("Visitor: " . $chatSession->getVisitorName());
            
            if ($chatSession->getVisitorEmail()) {
                $this->addLine("Email: " . $chatSession->getVisitorEmail());
            }
            
            $this->addLine("Session ID: {$chatSession->session_id}");
            
            $this->setAction('Join Chat', route('admin.chat.show', $chatSession));
            $this->salutation = 'Best regards,<br>' . config('app.name') . ' System';
        }
    }
}