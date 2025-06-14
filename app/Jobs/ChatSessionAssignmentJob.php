<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ChatSession;
use App\Models\User;
use App\Services\ChatService;
use App\Facades\Notifications;
use Illuminate\Support\Facades\Log;

class ChatSessionAssignmentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $session;
    protected $operator;
    public $timeout = 60;

    public function __construct(ChatSession $session, User $operator)
    {
        $this->session = $session;
        $this->operator = $operator;
    }

    public function handle(ChatService $chatService): void
    {
        try {
            // Reload models to ensure fresh data
            $session = ChatSession::find($this->session->id);
            $operator = User::find($this->operator->id);

            if (!$session || !$operator) {
                Log::warning('Session or operator not found during assignment', [
                    'session_id' => $this->session->id,
                    'operator_id' => $this->operator->id
                ]);
                return;
            }

            // Check if session is still waiting
            if ($session->status !== 'waiting') {
                Log::info('Session no longer waiting during assignment', [
                    'session_id' => $session->session_id,
                    'current_status' => $session->status
                ]);
                return;
            }

            // Verify operator is still available
            $operatorModel = $chatService->getOperator($operator);
            if (!$operatorModel || !$operatorModel->is_online || !$operatorModel->is_available) {
                Log::info('Operator no longer available during assignment', [
                    'operator_id' => $operator->id,
                    'is_online' => $operatorModel?->is_online ?? false,
                    'is_available' => $operatorModel?->is_available ?? false
                ]);
                
                // Try to find another operator
                if ($chatService->autoAssignSession($session)) {
                    Log::info('Session reassigned to different operator', [
                        'session_id' => $session->session_id
                    ]);
                }
                return;
            }

            // Perform assignment
            $session->update([
                'assigned_operator_id' => $operator->id,
                'status' => 'active',
                'assigned_at' => now()
            ]);

            // Update operator chat count
            $operatorModel->incrementChatCount();

            // Add system message about assignment
            $chatService->addSystemMessage(
                $session,
                "Chat assigned to {$operator->name}. How can I help you today?"
            );

            // Send notifications
            try {
                // Notify operator about new assignment
                Notifications::send('chat.session_assigned', $session, $operator);
                
                // Notify client that operator joined
                if ($session->user) {
                    Notifications::send('chat.operator_joined', $session, $session->user);
                }
            } catch (\Exception $notificationError) {
                Log::warning('Failed to send assignment notifications', [
                    'session_id' => $session->session_id,
                    'error' => $notificationError->getMessage()
                ]);
            }

            // Broadcast assignment update
            try {
                if (class_exists('\App\Events\ChatSessionAssigned')) {
                    broadcast(new \App\Events\ChatSessionAssigned($session, $operator))->toOthers();
                }
            } catch (\Exception $broadcastError) {
                Log::warning('Failed to broadcast assignment', [
                    'session_id' => $session->session_id,
                    'error' => $broadcastError->getMessage()
                ]);
            }

            Log::info('Chat session assigned successfully', [
                'session_id' => $session->session_id,
                'operator_id' => $operator->id,
                'operator_name' => $operator->name,
                'wait_time_minutes' => now()->diffInMinutes($session->created_at)
            ]);

        } catch (\Exception $e) {
            Log::error('Chat session assignment failed', [
                'session_id' => $this->session->session_id,
                'operator_id' => $this->operator->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Chat session assignment job failed', [
            'session_id' => $this->session->session_id,
            'operator_id' => $this->operator->id,
            'error' => $exception->getMessage()
        ]);

        // Try to auto-assign to someone else
        try {
            $chatService = app(ChatService::class);
            $session = ChatSession::find($this->session->id);
            
            if ($session && $session->status === 'waiting') {
                $chatService->autoAssignSession($session);
            }
        } catch (\Exception $retryError) {
            Log::error('Failed to retry assignment after job failure', [
                'session_id' => $this->session->session_id,
                'retry_error' => $retryError->getMessage()
            ]);
        }
    }
}