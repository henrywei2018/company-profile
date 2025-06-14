<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ChatSession;
use App\Models\ChatOperator;
use App\Facades\Notifications;
use Illuminate\Support\Facades\Log;

class NotifyOperatorsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $session;
    protected $urgency;
    public $timeout = 30;

    public function __construct(ChatSession $session, string $urgency = 'normal')
    {
        $this->session = $session;
        $this->urgency = $urgency;
    }

    public function handle(): void
    {
        try {
            // Get available operators
            $operators = ChatOperator::with('user')
                ->where('is_online', true)
                ->where('is_available', true)
                ->get();

            if ($operators->isEmpty()) {
                Log::warning('No operators available for notification', [
                    'session_id' => $this->session->session_id
                ]);
                return;
            }

            // Send notifications based on urgency
            foreach ($operators as $operator) {
                $this->notifyOperator($operator, $this->session, $this->urgency);
            }

            Log::info('Operators notified about new session', [
                'session_id' => $this->session->session_id,
                'operators_notified' => $operators->count(),
                'urgency' => $this->urgency
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to notify operators', [
                'session_id' => $this->session->session_id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function notifyOperator(ChatOperator $operator, ChatSession $session, string $urgency): void
    {
        $notificationType = match($urgency) {
            'urgent' => 'chat.session_urgent',
            'high' => 'chat.session_high_priority',
            default => 'chat.session_waiting'
        };

        Notifications::send($notificationType, $session, $operator->user);
    }
}