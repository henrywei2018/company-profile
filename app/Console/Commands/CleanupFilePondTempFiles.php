<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FilePondService;

class CleanupFilePondTempFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filepond:cleanup 
                           {--hours=24 : Files older than this many hours will be deleted}
                           {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old FilePond temporary files';

    protected FilePondService $filePondService;

    /**
     * Create a new command instance.
     */
    public function __construct(FilePondService $filePondService)
    {
        parent::__construct();
        $this->filePondService = $filePondService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $hours = (int) $this->option('hours');

        $this->info("FilePond temporary files cleanup started...");
        
        if ($dryRun) {
            $this->warn("DRY RUN MODE - No files will actually be deleted");
        }

        // Get current stats before cleanup
        $statsBefore = $this->filePondService->getStorageStats();
        $this->info("Before cleanup:");
        $this->line("  Files: {$statsBefore['temp_files_count']}");
        $this->line("  Size: {$statsBefore['temp_files_size_formatted']}");

        if (!$dryRun) {
            // Perform actual cleanup
            $deletedCount = $this->filePondService->cleanupOldFiles();
            
            // Get stats after cleanup
            $statsAfter = $this->filePondService->getStorageStats();
            
            $this->info("\nCleanup completed!");
            $this->line("  Files deleted: {$deletedCount}");
            $this->line("  Files remaining: {$statsAfter['temp_files_count']}");
            $this->line("  Size remaining: {$statsAfter['temp_files_size_formatted']}");
            
            if ($deletedCount > 0) {
                $sizeSaved = $statsBefore['temp_files_size'] - $statsAfter['temp_files_size'];
                $this->info("  Space freed: " . $this->formatFileSize($sizeSaved));
            }
        } else {
            // Simulate cleanup to show what would be deleted
            $this->simulateCleanup($hours);
        }

        return Command::SUCCESS;
    }

    /**
     * Simulate cleanup to show what would be deleted
     */
    protected function simulateCleanup(int $hours): void
    {
        // This would require extending FilePondService to support dry-run mode
        $this->info("\nDry run simulation not implemented yet.");
        $this->line("Use --no-dry-run to perform actual cleanup.");
    }

    /**
     * Format file size for display
     */
    protected function formatFileSize(int $bytes): string
    {
        if ($bytes == 0) return '0 Bytes';
        
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes) / log($k));
        
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }
}