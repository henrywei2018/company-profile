<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ChatSession;
use App\Models\ChatOperator;
use App\Services\ChatService;

class ChatQueueStatusCommand extends Command
{
    protected $signature = 'chat:queue-status
                            {--detailed : Show detailed session information}
                            {--export= : Export to file (csv|json)}';

    protected $description = 'Display current chat queue status and metrics';

    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        parent::__construct();
        $this->chatService = $chatService;
    }

    public function handle(): int
    {
        $this->info('📊 Chat Queue Status Report');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        // Get real-time metrics
        $metrics = $this->chatService->getRealTimeMetrics();
        
        // Queue overview
        $this->displayQueueOverview($metrics);

        // Operator status
        $this->displayOperatorStatus();

        // Waiting sessions
        $this->displayWaitingSessions();

        // Performance metrics
        $this->displayPerformanceMetrics();

        // Export if requested
        if ($this->option('export')) {
            $this->exportData();
        }

        return Command::SUCCESS;
    }

    private function displayQueueOverview(array $metrics): void
    {
        $this->info('');
        $this->info('🎯 Queue Overview:');
        
        $overview = [
            ['Active Sessions', $metrics['active_sessions'] ?? 0],
            ['Waiting in Queue', $metrics['waiting_sessions'] ?? 0],
            ['Operators Online', $metrics['operators_online'] ?? 0],
            ['Avg Response Time', ($metrics['avg_response_time'] ?? 0) . ' min'],
            ['Estimated Wait', $this->chatService->getEstimatedWaitTime() . ' min'],
        ];

        $this->table(['Metric', 'Value'], $overview);
    }

    private function displayOperatorStatus(): void
    {
        $this->info('');
        $this->info('👥 Operator Status:');

        $operators = ChatOperator::with('user')
            ->where('is_online', true)
            ->get()
            ->map(function ($operator) {
                $activeSessions = ChatSession::where('assigned_operator_id', $operator->user_id)
                    ->where('status', 'active')
                    ->count();

                return [
                    'name' => $operator->user->name,
                    'status' => $operator->is_available ? '🟢 Available' : '🟡 Busy',
                    'active_chats' => $activeSessions,
                    'max_chats' => $operator->max_concurrent_chats ?? 5,
                    'last_seen' => $operator->last_seen_at?->diffForHumans() ?? 'Unknown',
                ];
            });

        if ($operators->isNotEmpty()) {
            $this->table(
                ['Operator', 'Status', 'Active', 'Max', 'Last Seen'],
                $operators->toArray()
            );
        } else {
            $this->warn('   No operators currently online');
        }
    }

    private function displayWaitingSessions(): void
    {
        $this->info('');
        $this->info('⏳ Waiting Sessions:');

        $waitingSessions = ChatSession::where('status', 'waiting')
            ->with('user')
            ->byPriority('desc')
            ->orderBy('created_at')
            ->take(10)
            ->get()
            ->map(function ($session, $index) {
                return [
                    'position' => $index + 1,
                    'visitor' => $session->getVisitorName(),
                    'priority' => strtoupper($session->priority),
                    'waiting_time' => $session->created_at->diffForHumans(),
                    'session_id' => substr($session->session_id, 0, 8) . '...',
                ];
            });

        if ($waitingSessions->isNotEmpty()) {
            $this->table(
                ['#', 'Visitor', 'Priority', 'Waiting', 'Session ID'],
                $waitingSessions->toArray()
            );
        } else {
            $this->info('   ✅ No sessions waiting in queue');
        }
    }

    private function displayPerformanceMetrics(): void
    {
        $this->info('');
        $this->info('📈 Performance Metrics (Last 24h):');

        $yesterday = now()->subDay();

        $completedSessions = ChatSession::where('status', 'closed')
            ->where('ended_at', '>=', $yesterday)
            ->count();

        $avgDuration = ChatSession::where('status', 'closed')
            ->where('ended_at', '>=', $yesterday)
            ->whereNotNull('started_at')
            ->whereNotNull('ended_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, started_at, ended_at)) as avg_duration')
            ->value('avg_duration');

        $timeoutSessions = ChatSession::where('status', 'closed')
            ->where('close_reason', 'like', '%timeout%')
            ->where('ended_at', '>=', $yesterday)
            ->count();

        $metrics = [
            ['Completed Sessions', $completedSessions],
            ['Avg Session Duration', round($avgDuration ?? 0, 1) . ' min'],
            ['Timeout Rate', $completedSessions > 0 ? round(($timeoutSessions / $completedSessions) * 100, 1) . '%' : '0%'],
            ['Peak Queue Length', $this->getPeakQueueLength()],
        ];

        $this->table(['Metric', 'Value'], $metrics);
    }

    private function getPeakQueueLength(): int
    {
        // This would typically come from stored metrics
        // For now, return current waiting count as approximation
        return ChatSession::where('status', 'waiting')->count();
    }

    private function exportData(): void
    {
        $format = $this->option('export');
        $filename = 'chat_queue_status_' . now()->format('Y-m-d_H-i-s');

        $data = [
            'timestamp' => now()->toISOString(),
            'queue_overview' => $this->chatService->getRealTimeMetrics(),
            'waiting_sessions' => ChatSession::where('status', 'waiting')
                ->with('user')
                ->get()
                ->toArray(),
            'operators' => ChatOperator::with('user')
                ->where('is_online', true)
                ->get()
                ->toArray(),
        ];

        switch ($format) {
            case 'json':
                file_put_contents($filename . '.json', json_encode($data, JSON_PRETTY_PRINT));
                $this->info("📄 Exported to {$filename}.json");
                break;
            case 'csv':
                // Convert to CSV format - simplified version
                $csv = "Metric,Value\n";
                foreach ($data['queue_overview'] as $key => $value) {
                    $csv .= "{$key},{$value}\n";
                }
                file_put_contents($filename . '.csv', $csv);
                $this->info("📄 Exported to {$filename}.csv");
                break;
            default:
                $this->error("❌ Unsupported export format: {$format}");
        }
    }
}