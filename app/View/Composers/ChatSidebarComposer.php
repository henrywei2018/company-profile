<?php
// Create: app/View/Composers/ChatSidebarComposer.php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\ChatSession;
use App\Models\ChatOperator;

class ChatSidebarComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        try {
            // Get chat statistics
            $activeChatSessions = ChatSession::where('status', 'active')->count();
            $waitingChatSessions = ChatSession::where('status', 'waiting')->count();
            $totalActiveSessions = $activeChatSessions + $waitingChatSessions;
            
            // Get current user's operator status
            $currentOperator = null;
            $isOperatorOnline = false;
            
            if (auth()->check()) {
                $currentOperator = ChatOperator::where('user_id', auth()->id())->first();
                $isOperatorOnline = $currentOperator ? $currentOperator->is_online : false;
            }
            
            $view->with([
                'activeChatSessions' => $activeChatSessions,
                'waitingChatSessions' => $waitingChatSessions,
                'totalActiveSessions' => $totalActiveSessions,
                'isOperatorOnline' => $isOperatorOnline,
                'currentOperator' => $currentOperator,
            ]);
            
        } catch (\Exception $e) {
            // Fallback values if database queries fail
            $view->with([
                'activeChatSessions' => 0,
                'waitingChatSessions' => 0,
                'totalActiveSessions' => 0,
                'isOperatorOnline' => false,
                'currentOperator' => null,
            ]);
        }
    }
}
