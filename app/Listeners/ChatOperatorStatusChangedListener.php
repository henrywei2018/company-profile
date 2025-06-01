<?php

namespace App\Listeners;

use App\Events\ChatOperatorStatusChanged;
use App\Facades\Notifications;
use App\Models\ChatSession;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ChatOperatorStatusChangedListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(ChatOperatorStatusChanged $event): void
    {
        $operator = $event->operator;
        $isOnline = $event->isOnline;

        try {
            if (!$isOnline) {
                $this->handleOperatorGoingOffline($operator);
            } else {
                $this->handleOperatorComingOnline($operator);
            }

            // Log status change
            Log::info('Chat operator status changed', [
                'operator_id' => $operator->user_id,
                'operator_name' => $operator->user->name,
                'is_online' => $isOnline,
            ]);

        } catch (\Exception $e) {
            Log::error('Error handling operator status change: ' . $e->getMessage(), [
                'operator_id' => $operator->user_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle operator going offline
     */
    protected function handleOperatorGoingOffline($operator): void
    {
        // Get active sessions assigned to this operator
        $activeSessions = ChatSession::where('assigned_operator_id', $operator->user_id)
            ->whereIn('status', ['active', 'waiting'])
            ->get();

        foreach ($activeSessions as $session) {
            // Try to reassign to another operator
            $newOperator = \App\Models\ChatOperator::where('is_online', true)
                ->where('is_available', true)
                ->where('user_id', '!=', $operator->user_id)
                ->whereHas('user', function ($query) {
                    $query->whereHas('roles', function ($q) {
                        $q->whereIn('name', ['super-admin', 'admin', 'manager']);
                    });
                })
                ->where('current_chats_count', '<', \DB::raw('max_concurrent_chats'))
                ->orderBy('current_chats_count')
                ->first();

            if ($newOperator) {
                $session->update(['assigned_operator_id' => $newOperator->user_id]);
                
                $newOperator->incrementChatCount();
                $operator->decrementChatCount();

                // Add system message
                $session->messages()->create([
                    'sender_type' => 'system',
                    'message' => "Chat transferred to {$newOperator->user->name}",
                    'message_type' => 'system',
                ]);

                // Notify new operator
                Notifications::send('chat.session_transferred', $session, $newOperator->user);
            } else {
                // No available operators, mark as waiting
                $session->update([
                    'assigned_operator_id' => null,
                    'status' => 'waiting',
                ]);

                // Add system message
                $session->messages()->create([
                    'sender_type' => 'system',
                    'message' => 'Operator went offline. You are now in queue for the next available operator.',
                    'message_type' => 'system',
                ]);
            }
        }

        // Reset operator's chat count
        $operator->update(['current_chats_count' => 0]);
    }

    /**
     * Handle operator coming online
     */
    protected function handleOperatorComingOnline($operator): void
    {
        // Check for waiting sessions that can be assigned
        $waitingSessions = ChatSession::where('status', 'waiting')
            ->whereNull('assigned_operator_id')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at')
            ->limit($operator->max_concurrent_chats)
            ->get();

        foreach ($waitingSessions as $session) {
            if ($operator->current_chats_count >= $operator->max_concurrent_chats) {
                break;
            }

            $session->update([
                'assigned_operator_id' => $operator->user_id,
                'status' => 'active',
            ]);

            $operator->incrementChatCount();

            // Add system message
            $session->messages()->create([
                'sender_type' => 'system',
                'message' => "Connected with {$operator->user->name}",
                'message_type' => 'system',
            ]);

            // Notify operator
            Notifications::send('chat.session_assigned', $session, $operator->user);
        }
    }
}