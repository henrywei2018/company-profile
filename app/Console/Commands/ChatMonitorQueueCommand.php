<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ChatService;
use App\Models\ChatSession;
use App\Models\ChatOperator;
use App\Jobs\BroadcastQueueUpdateJob;
use App\Jobs\ProcessChatQueueJob;
use Illuminate\Support\Facades\Log;

class ChatMonitorQueueCommand extends Command
{
    protected $signature = 'chat:monitor-queue
                            {--interval=30 : Monitoring interval in seconds}
                            {--alert-threshold=10 : Queue length threshold for alerts}
                            {--auto-process : Automatically process queue when threshold reached}';

    protected $description = 'Monitor chat queue and trigger actions based on conditions';

    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        parent::__construct();
        $this->chatService = $chatService;
    }

    public function handle(): int
    {
        $interval = (int) $this->option('interval');
        $alertThreshold = (int) $this->option('alert-threshold');
        $autoProcess = $this->option('auto-process');

        $this->info("🔍 Starting chat queue monitoring...");
        $this->info("   • Interval: {$interval} seconds");
        $this->info("   • Alert threshold: {$alertThreshold} sessions");
        $this->info("   • Auto-process: " . ($autoProcess ? 'enabled' : 'disabled'));
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

        while (true) {
            try {
                $this->monitorQueue($alertThreshold, $autoProcess);
                sleep($interval);
            } catch (\Exception $e) {
                $this->error("❌ Monitor error: {$e->getMessage()}");
                Log::error('Chat queue monitor error', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                sleep($interval);
            }
        }

        return Command::SUCCESS;
    }

    private function monitorQueue(int $alertThreshold, bool $autoProcess): void
    {
        $metrics = $this->chatService->getRealTimeMetrics();
        $waitingCount = $metrics['waiting_sessions'] ?? 0;
        $operatorsOnline = $metrics['operators_online'] ?? 0;
        $avgWaitTime = $metrics['avg_wait_time'] ?? 0;

        $timestamp = now()->format('H:i:s');
        
        // Display current status
        $status = "[$timestamp] Queue: {$waitingCount} waiting | Operators: {$operatorsOnline} online | Avg wait: {$avgWaitTime}min";
        
        if ($waitingCount >= $alertThreshold) {
            $this->warn("⚠️  {$status} [ALERT THRESHOLD REACHED]");
            
            if ($autoProcess) {
                $this->info("🔄 Auto-processing queue...");
                ProcessChatQueueJob::dispatchSync();
                $assigned = $this->chatService->autoAssignWaitingSessions();
                $this->info("   ✅ {$assigned} sessions assigned");
            }
            
        } elseif ($waitingCount > 0) {
            $this->line("🟡 {$status}");
        } else {
            $this->info("🟢 {$status}");
        }

        // Check for stale sessions (waiting too long)
        $staleSessions = ChatSession::where('status', 'waiting')
            ->where('created_at', '<=', now()->subMinutes(25))
            ->count();

        if ($staleSessions > 0) {
            $this->warn("   ⏰ {$staleSessions} sessions waiting over 25 minutes");
        }

        // Check operator workload distribution
        $overloadedOperators = ChatOperator::whereHas('activeSessions', function($query) {
            $query->havingRaw('COUNT(*) >= 5');
        })->count();

        if ($overloadedOperators > 0) {
            $this->warn("   🏋️  {$overloadedOperators} operators at capacity");
        }

        // Broadcast queue updates
        BroadcastQueueUpdateJob::dispatchSync();

        // Log metrics for historical tracking
        if ($waitingCount > 0 || $operatorsOnline === 0) {
            Log::info('Chat queue monitoring', [
                'waiting_sessions' => $waitingCount,
                'operators_online' => $operatorsOnline,
                'avg_wait_time' => $avgWaitTime,
                'stale_sessions' => $staleSessions,
                'overloaded_operators' => $overloadedOperators
            ]);
        }
    }
}