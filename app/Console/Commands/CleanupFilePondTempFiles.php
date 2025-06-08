<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupFilePondTempFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filepond:cleanup {--hours=24 : Delete files older than this many hours}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old FilePond temporary files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = (int) $this->option('hours');
        $tempPath = config('filepond.path', 'temp/filepond');
        $disk = config('filepond.disk', 'local');
        
        $this->info("Cleaning up FilePond temporary files older than {$hours} hours...");
        
        $deletedCount = 0;
        $cutoff = Carbon::now()->subHours($hours);
        
        try {
            if (!Storage::disk($disk)->exists($tempPath)) {
                $this->info("Temporary directory does not exist: {$tempPath}");
                return 0;
            }
            
            $files = Storage::disk($disk)->files($tempPath);
            
            if (empty($files)) {
                $this->info("No temporary files found.");
                return 0;
            }
            
            foreach ($files as $file) {
                $lastModified = Carbon::createFromTimestamp(
                    Storage::disk($disk)->lastModified($file)
                );
                
                if ($lastModified->lt($cutoff)) {
                    if (Storage::disk($disk)->delete($file)) {
                        $deletedCount++;
                        $this->line("Deleted: {$file}");
                    }
                }
            }
            
            // Try to remove empty directories
            $directories = Storage::disk($disk)->directories($tempPath);
            foreach ($directories as $directory) {
                if (empty(Storage::disk($disk)->files($directory))) {
                    Storage::disk($disk)->deleteDirectory($directory);
                    $this->line("Removed empty directory: {$directory}");
                }
            }
            
            $this->info("Cleanup completed. Deleted {$deletedCount} temporary files.");
            
        } catch (\Exception $e) {
            $this->error("Cleanup failed: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}