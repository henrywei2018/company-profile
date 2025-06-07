<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FilePondService
{
    protected string $tempPath = 'temp/filepond/';
    protected string $maxFileSize = '10MB';
    protected int $maxFiles = 20;
    protected array $allowedMimeTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain',
        'text/csv',
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/svg+xml',
        'image/webp',
        'application/zip',
        'application/x-rar-compressed',
        'application/x-7z-compressed',
        'application/dwg',
        'application/dxf',
        'application/json',
        'application/xml',
    ];

    /**
     * Process uploaded file to temporary storage
     */
    public function processUpload(UploadedFile $file, string $projectId): string
    {
        // Validate file
        $this->validateFile($file);

        // Generate unique temporary path
        $tempFileName = $this->generateTempFileName($file);
        $fullTempPath = $this->tempPath . $projectId . '/' . $tempFileName;

        // Store file temporarily
        $file->storeAs('public/' . dirname($fullTempPath), basename($fullTempPath));

        // Create server ID
        return $this->createServerId([
            'temp_path' => $fullTempPath,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'uploaded_at' => now()->timestamp,
            'project_id' => $projectId
        ]);
    }

    /**
     * Revert (delete) temporary file
     */
    public function revertUpload(string $serverId): bool
    {
        try {
            $fileData = $this->decodeServerId($serverId);
            
            if (isset($fileData['temp_path']) && Storage::disk('public')->exists($fileData['temp_path'])) {
                Storage::disk('public')->delete($fileData['temp_path']);
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            \Log::error('FilePond revert failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Move temporary file to permanent storage
     */
    public function moveToPermantent(string $serverId, string $permanentPath): ?array
    {
        try {
            $fileData = $this->decodeServerId($serverId);
            
            if (!isset($fileData['temp_path']) || !Storage::disk('public')->exists($fileData['temp_path'])) {
                throw new \Exception('Temporary file not found');
            }

            // Validate age (files older than 24 hours are rejected)
            if (isset($fileData['uploaded_at']) && 
                (time() - $fileData['uploaded_at']) > 86400) {
                throw new \Exception('Temporary file too old');
            }

            // Move file
            if (Storage::disk('public')->move($fileData['temp_path'], $permanentPath)) {
                return [
                    'original_name' => $fileData['original_name'],
                    'mime_type' => $fileData['mime_type'],
                    'size' => $fileData['size'],
                    'permanent_path' => $permanentPath
                ];
            }

            throw new \Exception('Failed to move file');

        } catch (\Exception $e) {
            \Log::error('FilePond move to permanent failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get file info from server ID
     */
    public function getFileInfo(string $serverId): ?array
    {
        try {
            return $this->decodeServerId($serverId);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Clean up old temporary files
     */
    public function cleanupOldFiles(): int
    {
        $deleted = 0;
        $cutoff = Carbon::now()->subHours(24);

        try {
            $tempDirectories = Storage::disk('public')->directories($this->tempPath);
            
            foreach ($tempDirectories as $directory) {
                $files = Storage::disk('public')->files($directory);
                
                foreach ($files as $file) {
                    $lastModified = Carbon::createFromTimestamp(
                        Storage::disk('public')->lastModified($file)
                    );
                    
                    if ($lastModified->lt($cutoff)) {
                        Storage::disk('public')->delete($file);
                        $deleted++;
                    }
                }

                // Remove empty directories
                if (empty(Storage::disk('public')->files($directory))) {
                    Storage::disk('public')->deleteDirectory($directory);
                }
            }
        } catch (\Exception $e) {
            \Log::error('FilePond cleanup failed: ' . $e->getMessage());
        }

        return $deleted;
    }

    /**
     * Get storage statistics
     */
    public function getStorageStats(): array
    {
        $totalSize = 0;
        $fileCount = 0;

        try {
            $tempDirectories = Storage::disk('public')->directories($this->tempPath);
            
            foreach ($tempDirectories as $directory) {
                $files = Storage::disk('public')->files($directory);
                $fileCount += count($files);
                
                foreach ($files as $file) {
                    $totalSize += Storage::disk('public')->size($file);
                }
            }
        } catch (\Exception $e) {
            \Log::error('FilePond stats failed: ' . $e->getMessage());
        }

        return [
            'temp_files_count' => $fileCount,
            'temp_files_size' => $totalSize,
            'temp_files_size_formatted' => $this->formatFileSize($totalSize)
        ];
    }

    /**
     * Validate uploaded file
     */
    protected function validateFile(UploadedFile $file): void
    {
        // Check file size
        $maxSizeBytes = $this->parseFileSize($this->maxFileSize);
        if ($file->getSize() > $maxSizeBytes) {
            throw new \Exception('File size exceeds maximum allowed size');
        }

        // Check mime type
        if (!in_array($file->getMimeType(), $this->allowedMimeTypes)) {
            throw new \Exception('File type not allowed: ' . $file->getMimeType());
        }

        // Check file name
        if (!$this->isValidFileName($file->getClientOriginalName())) {
            throw new \Exception('Invalid file name');
        }
    }

    /**
     * Generate unique temporary file name
     */
    protected function generateTempFileName(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $hash = hash('sha256', uniqid() . $file->getClientOriginalName() . time());
        return substr($hash, 0, 16) . '_' . time() . ($extension ? '.' . $extension : '');
    }

    /**
     * Create encoded server ID
     */
    protected function createServerId(array $data): string
    {
        return base64_encode(json_encode($data));
    }

    /**
     * Decode server ID
     */
    protected function decodeServerId(string $serverId): array
    {
        $decoded = base64_decode($serverId);
        $data = json_decode($decoded, true);
        
        if (!$data || !is_array($data)) {
            throw new \Exception('Invalid server ID');
        }
        
        return $data;
    }

    /**
     * Validate file name
     */
    protected function isValidFileName(string $fileName): bool
    {
        // Check length
        if (strlen($fileName) > 255) {
            return false;
        }

        // Check for dangerous characters
        $dangerousChars = ['..', '\\', '/', ':', '*', '?', '"', '<', '>', '|'];
        foreach ($dangerousChars as $char) {
            if (strpos($fileName, $char) !== false) {
                return false;
            }
        }

        // Check for hidden files
        if (str_starts_with($fileName, '.')) {
            return false;
        }

        return true;
    }

    /**
     * Parse file size string to bytes
     */
    protected function parseFileSize(string $size): int
    {
        $size = trim($size);
        $unit = strtoupper(substr($size, -2));
        $value = (int) substr($size, 0, -2);

        switch ($unit) {
            case 'KB':
                return $value * 1024;
            case 'MB':
                return $value * 1024 * 1024;
            case 'GB':
                return $value * 1024 * 1024 * 1024;
            default:
                return (int) $size;
        }
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

    /**
     * Set configuration
     */
    public function setConfig(array $config): self
    {
        if (isset($config['max_file_size'])) {
            $this->maxFileSize = $config['max_file_size'];
        }

        if (isset($config['max_files'])) {
            $this->maxFiles = $config['max_files'];
        }

        if (isset($config['allowed_mime_types'])) {
            $this->allowedMimeTypes = $config['allowed_mime_types'];
        }

        if (isset($config['temp_path'])) {
            $this->tempPath = $config['temp_path'];
        }

        return $this;
    }

    /**
     * Get configuration
     */
    public function getConfig(): array
    {
        return [
            'max_file_size' => $this->maxFileSize,
            'max_files' => $this->maxFiles,
            'allowed_mime_types' => $this->allowedMimeTypes,
            'temp_path' => $this->tempPath
        ];
    }
}