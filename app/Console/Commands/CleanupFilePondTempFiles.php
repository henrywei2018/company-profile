<?php
// app/Console/Commands/CleanupFilePondTempFiles.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupFilePondTempFiles extends Command
{
    protected $signature = 'filepond:cleanup';
    protected $description = 'Clean up old FilePond temporary files';

    public function handle()
    {
        $disk = config('filepond.disk', 'local');
        $path = config('filepond.path', 'filepond/tmp');
        $maxAge = config('filepond.max_file_age', 60); // minutes

        $deletedCount = 0;

        try {
            $files = Storage::disk($disk)->allFiles($path);
            
            foreach ($files as $file) {
                $lastModified = Storage::disk($disk)->lastModified($file);
                $fileAge = Carbon::createFromTimestamp($lastModified);
                
                if ($fileAge->diffInMinutes(now()) > $maxAge) {
                    Storage::disk($disk)->delete($file);
                    $deletedCount++;
                }
            }
            
            $this->info("Cleaned up {$deletedCount} temporary files older than {$maxAge} minutes.");
            
        } catch (\Exception $e) {
            $this->error('Failed to cleanup temporary files: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}