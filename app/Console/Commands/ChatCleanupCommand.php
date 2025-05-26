<?php

namespace App\Console\Commands;

use App\Models\ChatSession;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ChatCleanupCommand extends Command
{
    protected $signature = 'chat:cleanup {--days=30 : Days to keep chat sessions}';
    protected $description = 'Clean up old chat sessions and messages';

    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);
        
        $this->info("Cleaning up chat sessions older than {$days} days...");
        
        // Delete old closed sessions
        $deletedSessions = ChatSession::where('status', 'closed')
            ->where('ended_at', '<', $cutoffDate)
            ->count();
            
        ChatSession::where('status', 'closed')
            ->where('ended_at', '<', $cutoffDate)
            ->delete();
        
        $this->info("Deleted {$deletedSessions} old chat sessions.");
        
        // Clean up orphaned sessions (no activity for 24+ hours)
        $orphanedSessions = ChatSession::whereIn('status', ['active', 'waiting'])
            ->where('last_activity_at', '<', Carbon::now()->subHours(24))
            ->count();
            
        ChatSession::whereIn('status', ['active', 'waiting'])
            ->where('last_activity_at', '<', Carbon::now()->subHours(24))
            ->update(['status' => 'closed', 'ended_at' => now()]);
        
        $this->info("Closed {$orphanedSessions} orphaned chat sessions.");
        
        return 0;
    }
}