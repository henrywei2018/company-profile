<?php
// File: app/Console/Commands/ChatCleanupCommand.php

namespace App\Console\Commands;

use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Services\ChatService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ChatCleanupCommand extends Command
{
    protected $signature = 'chat:cleanup
                            {--days=30 : Number of days to keep chat data}
                            {--force : Force cleanup without confirmation}';

    protected $description = 'Clean up old chat sessions and messages';

    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        parent::__construct();
        $this->chatService = $chatService;
    }

    public function handle()
    {
        $days = $this->option('days');
        $force = $this->option('force');

        $this->info("🧹 Starting chat cleanup for data older than {$days} days...");

        // Get counts before cleanup
        $totalSessions = ChatSession::count();
        $totalMessages = ChatMessage::count();

        if (!$force) {
            $cutoffDate = now()->subDays($days);
            $sessionsToDelete = ChatSession::where('created_at', '<', $cutoffDate)->count();
            
            if (!$this->confirm("This will delete {$sessionsToDelete} chat sessions and their messages. Continue?")) {
                $this->info('Cleanup cancelled.');
                return Command::SUCCESS;
            }
        }

        try {
            // Archive old sessions first
            $archivedCount = $this->chatService->archiveOldSessions($days);
            $this->info("📦 Archived {$archivedCount} old sessions");

            // Clean up stale sessions
            $staleCount = $this->chatService->cleanupStaleSessions();
            $this->info("🔄 Cleaned up {$staleCount} stale sessions");

            // Delete very old data
            $cutoffDate = now()->subDays($days * 2); // Keep archived data for double the time
            $deletedSessions = ChatSession::where('created_at', '<', $cutoffDate)
                ->where('status', 'archived')
                ->count();

            ChatSession::where('created_at', '<', $cutoffDate)
                ->where('status', 'archived')
                ->delete();

            $this->info("🗑️  Deleted {$deletedSessions} very old sessions");

            // Final counts
            $finalSessions = ChatSession::count();
            $finalMessages = ChatMessage::count();

            $this->newLine();
            $this->info('📊 Cleanup Summary:');
            $this->table(
                ['Metric', 'Before', 'After', 'Difference'],
                [
                    ['Sessions', $totalSessions, $finalSessions, $totalSessions - $finalSessions],
                    ['Messages', $totalMessages, $finalMessages, $totalMessages - $finalMessages],
                ]
            );

            Log::info('Chat cleanup completed', [
                'sessions_before' => $totalSessions,
                'sessions_after' => $finalSessions,
                'messages_before' => $totalMessages,
                'messages_after' => $finalMessages,
                'days_threshold' => $days,
            ]);

            $this->info('✅ Chat cleanup completed successfully!');

        } catch (\Exception $e) {
            $this->error('❌ Cleanup failed: ' . $e->getMessage());
            Log::error('Chat cleanup failed: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}