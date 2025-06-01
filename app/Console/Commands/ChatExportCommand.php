<?php

namespace App\Console\Commands;

use App\Models\ChatSession;
use App\Services\ChatService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ChatExportCommand extends Command
{
    protected $signature = 'chat:export
                            {format : Export format (csv, json)}
                            {--from= : Start date (Y-m-d)}
                            {--to= : End date (Y-m-d)}
                            {--status= : Session status filter}
                            {--output= : Output file path}';

    protected $description = 'Export chat sessions data';

    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        parent::__construct();
        $this->chatService = $chatService;
    }

    public function handle()
    {
        $format = $this->argument('format');
        $from = $this->option('from');
        $to = $this->option('to');
        $status = $this->option('status');
        $output = $this->option('output');

        if (!in_array($format, ['csv', 'json'])) {
            $this->error('Format must be either csv or json');
            return Command::FAILURE;
        }

        $this->info("📤 Exporting chat sessions in {$format} format...");

        try {
            // Build filters
            $filters = [];
            if ($from) $filters['date_from'] = $from;
            if ($to) $filters['date_to'] = $to;
            if ($status) $filters['status'] = $status;

            // Export based on format
            if ($format === 'csv') {
                $filepath = $this->chatService->exportSessionsToCSV($filters);
                $filename = basename($filepath);
            } else {
                $filename = 'chat_sessions_' . now()->format('Y-m-d_H-i-s') . '.json';
                $filepath = $this->exportToJson($filters, $filename);
            }

            // Move file if custom output specified
            if ($output) {
                $outputPath = $output;
                if (is_dir($output)) {
                    $outputPath = rtrim($output, '/') . '/' . $filename;
                }
                
                copy($filepath, $outputPath);
                unlink($filepath);
                $filepath = $outputPath;
            }

            $filesize = human_filesize(filesize($filepath));
            
            $this->newLine();
            $this->info("✅ Export completed successfully!");
            $this->info("📁 File: {$filepath}");
            $this->info("📊 Size: {$filesize}");

            // Show export summary
            $this->showExportSummary($filters);

        } catch (\Exception $e) {
            $this->error("❌ Export failed: {$e->getMessage()}");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    protected function exportToJson($filters, $filename)
    {
        $query = ChatSession::with(['user', 'operator', 'messages.sender']);
        
        // Apply filters
        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        $sessions = $query->orderBy('created_at', 'desc')->get();

        $exportData = [
            'metadata' => [
                'exported_at' => now()->toISOString(),
                'total_sessions' => $sessions->count(),
                'filters' => $filters,
            ],
            'sessions' => $sessions->map(function ($session) {
                return [
                    'session_id' => $session->session_id,
                    'visitor' => [
                        'name' => $session->getVisitorName(),
                        'email' => $session->getVisitorEmail(),
                        'user_id' => $session->user_id,
                    ],
                    'operator' => $session->operator ? [
                        'id' => $session->operator->id,
                        'name' => $session->operator->name,
                        'email' => $session->operator->email,
                    ] : null,
                    'session_data' => [
                        'status' => $session->status,
                        'priority' => $session->priority,
                        'started_at' => $session->started_at->toISOString(),
                        'ended_at' => $session->ended_at?->toISOString(),
                        'duration_minutes' => $session->getDuration(),
                        'summary' => $session->summary,
                    ],
                    'messages' => $session->messages->map(function ($message) {
                        return [
                            'id' => $message->id,
                            'sender_type' => $message->sender_type,
                            'sender_name' => $message->getSenderName(),
                            'message' => $message->message,
                            'message_type' => $message->message_type,
                            'created_at' => $message->created_at->toISOString(),
                            'metadata' => $message->metadata,
                        ];
                    }),
                ];
            }),
        ];

        $filepath = storage_path('app/exports/' . $filename);
        
        // Ensure directory exists
        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        file_put_contents($filepath, json_encode($exportData, JSON_PRETTY_PRINT));
        
        return $filepath;
    }

    protected function showExportSummary($filters)
    {
        $query = ChatSession::query();
        
        // Apply same filters
        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $sessions = $query->get();
        
        $summary = [
            'Total Sessions' => $sessions->count(),
            'With Messages' => $sessions->sum(function ($s) { return $s->messages()->count(); }),
            'Active Sessions' => $sessions->where('status', 'active')->count(),
            'Closed Sessions' => $sessions->where('status', 'closed')->count(),
            'Avg Duration' => round($sessions->where('status', 'closed')->avg(function ($s) { 
                return $s->getDuration(); 
            }) ?? 0, 1) . ' minutes',
        ];

        $this->newLine();
        $this->info("📈 Export Summary:");
        $this->table(
            ['Metric', 'Value'],
            array_map(function ($key, $value) {
                return [$key, $value];
            }, array_keys($summary), array_values($summary))
        );
    }
}