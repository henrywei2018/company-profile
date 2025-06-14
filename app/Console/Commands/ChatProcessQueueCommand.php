<?php

// File: app/Console/Commands/ChatProcessQueueCommand.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ChatService;
use App\Models\ChatSession;
use App\Models\ChatOperator;
use Illuminate\Support\Facades\Log;

class ChatProcessQueueCommand extends Command
{
    protected $signature = 'chat:process-queue
                            {--auto-assign : Automatically assign sessions to available operators}
                            {--timeout-check : Check for session timeouts}
                            {--priority-boost : Boost priority for long-waiting sessions}';

    protected $description = 'Process chat queue - assign sessions, handle timeouts, boost priorities';

    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        parent::__construct();
        $this->chatService = $chatService;
    }

    public function handle(): int
    {
        $this->info('🔄 Processing chat queue...');

        $processed = 0;
        $assigned = 0;
        $timeouts = 0;
        $boosted = 0;

        try {
            // 1. Auto-assign sessions if requested
            if ($this->option('auto-assign')) {
                $assigned = $this->processAutoAssignment();
                $processed += $assigned;
            }

            // 2. Handle session timeouts if requested
            if ($this->option('timeout-check')) {
                $timeouts = $this->processTimeouts();
                $processed += $timeouts;
            }

            // 3. Boost priority for long-waiting sessions if requested
            if ($this->option('priority-boost')) {
                $boosted = $this->processPriorityBoost();
                $processed += $boosted;
            }

            // 4. If no specific options, run all
            if (!$this->option('auto-assign') && !$this->option('timeout-check') && !$this->option('priority-boost')) {
                $assigned = $this->processAutoAssignment();
                $timeouts = $this->processTimeouts();
                $boosted = $this->processPriorityBoost();
                $processed = $assigned + $timeouts + $boosted;
            }

            // 5. Display summary
            $this->displaySummary($assigned, $timeouts, $boosted);

            Log::info('Chat queue processed successfully', [
                'assigned' => $assigned,
                'timeouts' => $timeouts,
                'priority_boosted' => $boosted,
                'total_processed' => $processed
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Error processing queue: {$e->getMessage()}");
            Log::error('Chat queue processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }

    private function processAutoAssignment(): int
    {
        $this->info('🎯 Processing auto-assignments...');

        $waitingSessions = ChatSession::where('status', 'waiting')
            ->byPriority('desc')
            ->orderBy('created_at')
            ->get();

        $availableOperators = ChatOperator::where('is_online', true)
            ->where('is_available', true)
            ->count();

        $this->info("   • Waiting sessions: {$waitingSessions->count()}");
        $this->info("   • Available operators: {$availableOperators}");

        $assigned = 0;

        foreach ($waitingSessions as $session) {
            if ($this->chatService->autoAssignSession($session)) {
                $assigned++;
                $this->line("   ✅ Assigned session {$session->session_id} to operator");
            }
        }

        return $assigned;
    }

    private function processTimeouts(): int
    {
        $this->info('⏰ Checking session timeouts...');

        $timeoutThreshold = now()->subMinutes(30);
        $timeoutSessions = ChatSession::where('status', 'waiting')
            ->where('created_at', '<=', $timeoutThreshold)
            ->get();

        $this->info("   • Sessions to timeout: {$timeoutSessions->count()}");

        $timeouts = 0;

        foreach ($timeoutSessions as $session) {
            $this->chatService->closeSession($session, 'Queue timeout after 30 minutes');
            $timeouts++;
            $this->line("   ⏳ Timeout session {$session->session_id}");
        }

        return $timeouts;
    }

    private function processPriorityBoost(): int
    {
        $this->info('🚀 Processing priority boosts...');

        $boostThreshold = now()->subMinutes(10);
        $urgentThreshold = now()->subMinutes(20);

        // Boost to high priority after 10 minutes
        $highPrioritySessions = ChatSession::where('status', 'waiting')
            ->where('priority', 'normal')
            ->where('created_at', '<=', $boostThreshold)
            ->get();

        // Boost to urgent priority after 20 minutes
        $urgentPrioritySessions = ChatSession::where('status', 'waiting')
            ->whereIn('priority', ['normal', 'high'])
            ->where('created_at', '<=', $urgentThreshold)
            ->get();

        $boosted = 0;

        foreach ($highPrioritySessions as $session) {
            $session->update(['priority' => 'high']);
            $boosted++;
            $this->line("   📈 Boosted session {$session->session_id} to HIGH priority");
        }

        foreach ($urgentPrioritySessions as $session) {
            $session->update(['priority' => 'urgent']);
            $boosted++;
            $this->line("   🔥 Boosted session {$session->session_id} to URGENT priority");
        }

        return $boosted;
    }

    private function displaySummary(int $assigned, int $timeouts, int $boosted): void
    {
        $this->info('');
        $this->info('📊 Queue Processing Summary:');
        $this->table(
            ['Action', 'Count'],
            [
                ['Sessions Assigned', $assigned],
                ['Sessions Timeout', $timeouts],
                ['Priority Boosted', $boosted],
            ]
        );

        // Current queue status
        $currentWaiting = ChatSession::where('status', 'waiting')->count();
        $currentActive = ChatSession::where('status', 'active')->count();
        $operatorsOnline = ChatOperator::where('is_online', true)->count();

        $this->info('');
        $this->info('📈 Current Status:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Waiting in Queue', $currentWaiting],
                ['Active Sessions', $currentActive],
                ['Operators Online', $operatorsOnline],
            ]
        );
    }
}