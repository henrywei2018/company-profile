<?php

namespace App\Console\Commands;

use App\Models\ChatSession;
use App\Models\ChatOperator;
use App\Models\ChatMessage;
use App\Services\ChatService;
use Illuminate\Console\Command;

class ChatStatsCommand extends Command
{
    protected $signature = 'chat:stats
                            {--period=30d : Time period (7d, 30d, 90d)}
                            {--detailed : Show detailed statistics}';

    protected $description = 'Display chat system statistics';

    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        parent::__construct();
        $this->chatService = $chatService;
    }

    public function handle()
    {
        $period = $this->option('period');
        $detailed = $this->option('detailed');

        $this->info("📊 Chat System Statistics ({$period})");
        $this->newLine();

        // Basic statistics
        $stats = $this->chatService->getStatistics();
        
        $this->info('🎯 Current Status:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Active Sessions', $stats['active_sessions']],
                ['Waiting Sessions', $stats['waiting_sessions']],
                ['Online Operators', $stats['online_operators']],
                ['Available Operators', $stats['available_operators']],
                ['Sessions Today', $stats['sessions_today']],
                ['Messages Today', $stats['messages_today']],
            ]
        );

        // Performance metrics
        $performance = $this->chatService->getPerformanceMetrics();
        
        $this->newLine();
        $this->info('⚡ Performance Metrics:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Sessions', $performance['total_sessions']],
                ['Resolution Rate', $performance['resolution_rate'] . '%'],
                ['Avg First Response', $performance['avg_first_response_time'] . ' min'],
                ['Avg Session Duration', $performance['avg_session_duration'] . ' min'],
                ['Customer Satisfaction', $performance['customer_satisfaction'] . '/5'],
            ]
        );

        if ($detailed) {
            $this->showDetailedStats($period);
        }

        return \Symfony\Component\Console\Command\Command::SUCCESS;
    }

    protected function showDetailedStats($period)
    {
        // Operator workload
        $workload = $this->chatService->getOperatorWorkload();
        
        if (!empty($workload)) {
            $this->newLine();
            $this->info('👥 Operator Workload:');
            $this->table(
                ['Operator', 'Active Sessions', 'Max Sessions', 'Workload %', 'Available'],
                array_map(function ($op) {
                    return [
                        $op['operator_name'],
                        $op['active_sessions'],
                        $op['max_sessions'],
                        round($op['workload_percentage'], 1) . '%',
                        $op['availability'] ? '✅' : '❌',
                    ];
                }, $workload)
            );
        }

        // Session analytics
        $analytics = $this->chatService->getSessionAnalytics($period);
        
        $this->newLine();
        $this->info("📈 Session Analytics ({$period}):");
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Sessions', $analytics['total_sessions']],
                ['Peak Hour', array_key_first($analytics['peak_hours']) . ':00 (' . array_values($analytics['peak_hours'])[0] . ' sessions)'],
                ['Busiest Day', array_key_first($analytics['popular_days']) . ' (' . array_values($analytics['popular_days'])[0] . ' sessions)'],
            ]
        );

        // Show daily breakdown for last 7 days
        $dailyStats = array_slice($analytics['daily_stats'], -7, 7, true);
        
        if (!empty($dailyStats)) {
            $this->newLine();
            $this->info('📅 Daily Breakdown (Last 7 days):');
            $this->table(
                ['Date', 'Sessions', 'Messages', 'Avg Duration (min)'],
                array_map(function ($date, $stats) {
                    return [
                        $date,
                        $stats['sessions_count'],
                        $stats['messages_count'],
                        round($stats['avg_duration'], 1),
                    ];
                }, array_keys($dailyStats), array_values($dailyStats))
            );
        }
    }
}