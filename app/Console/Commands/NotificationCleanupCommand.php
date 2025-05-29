<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService;
use Carbon\Carbon;

class NotificationCleanupCommand extends Command
{
    protected $signature = 'notifications:cleanup 
                          {--days=30 : Number of days to keep read notifications}
                          {--unread-days=90 : Number of days to keep unread notifications}';
    
    protected $description = 'Clean up old notifications to keep database optimized';

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle(): int
    {
        $this->info('🧹 Starting notification cleanup...');

        $readDays = (int) $this->option('days');
        $unreadDays = (int) $this->option('unread-days');

        $readCutoff = Carbon::now()->subDays($readDays);
        $unreadCutoff = Carbon::now()->subDays($unreadDays);

        // Delete old read notifications
        $readDeleted = DB::table('notifications')
            ->whereNotNull('read_at')
            ->where('read_at', '<', $readCutoff)
            ->delete();

        $this->info("✅ Deleted {$readDeleted} old read notifications (older than {$readDays} days)");

        // Delete very old unread notifications
        $unreadDeleted = DB::table('notifications')
            ->whereNull('read_at')
            ->where('created_at', '<', $unreadCutoff)
            ->delete();

        $this->info("✅ Deleted {$unreadDeleted} old unread notifications (older than {$unreadDays} days)");

        // Clear notification cache
        $this->notificationService->clearCache();
        $this->info("✅ Cleared notification cache");

        // Optimize notification table
        if (config('database.default') === 'mysql') {
            DB::statement('OPTIMIZE TABLE notifications');
            $this->info("✅ Optimized notifications table");
        }

        $totalDeleted = $readDeleted + $unreadDeleted;
        $this->info("🎉 Cleanup completed! Total notifications deleted: {$totalDeleted}");

        return Command::SUCCESS;
    }
}