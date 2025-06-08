<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Upload a file to the specified directory.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string|null $filename
     * @return string The stored file path
     * @throws \Exception
     */
    public function uploadFile(UploadedFile $file, string $directory, ?string $filename = null): string
    {
        // Validate file
        if (!$file->isValid()) {
            throw new \Exception('Invalid file upload');
        }

        // Generate filename if not provided
        if (!$filename) {
            $filename = uniqid() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
        }

        // Ensure the filename is safe
        $filename = $this->sanitizeFilename($filename);

        // Create the full path
        $path = $directory . '/' . $filename;

        // Store the file
        $storedPath = $file->storeAs($directory, $filename, 'public');

        if (!$storedPath) {
            throw new \Exception('Failed to store file');
        }

        return $storedPath;
    }

    /**
     * Upload an image with optional resizing.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string|null $filename
     * @param int|null $maxWidth
     * @param int|null $maxHeight
     * @param int $quality
     * @return string The stored file path
     * @throws \Exception
     */
    public function uploadImage(
        UploadedFile $file, 
        string $directory, 
        ?string $filename = null, 
        ?int $maxWidth = null, 
        ?int $maxHeight = null, 
        int $quality = 85
    ): string {
        // Validate that it's an image
        if (!str_starts_with($file->getMimeType(), 'image/')) {
            throw new \Exception('File is not an image');
        }

        // Generate filename if not provided
        if (!$filename) {
            $filename = uniqid() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
        }

        // Ensure the filename is safe
        $filename = $this->sanitizeFilename($filename);

        // Create the full path
        $path = $directory . '/' . $filename;

        // If no resizing is needed, just store the file normally
        if (!$maxWidth && !$maxHeight) {
            return $this->uploadFile($file, $directory, $filename);
        }

        // Create directory if it doesn't exist
        $fullDirectory = storage_path('app/public/' . $directory);
        if (!is_dir($fullDirectory)) {
            mkdir($fullDirectory, 0755, true);
        }

        // Process the image with basic PHP (if Intervention Image is not available)
        try {
            // Try to use Intervention Image if available
            if (class_exists('\Intervention\Image\Facades\Image')) {
                $image = \Intervention\Image\Laravel\Facades\Image::make($file->getRealPath());
                
                if ($maxWidth || $maxHeight) {
                    $image->resize($maxWidth, $maxHeight, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }
                
                $image->save(storage_path('app/public/' . $path), $quality);
            } else {
                // Fallback to basic file upload without resizing
                $storedPath = $file->storeAs($directory, $filename, 'public');
                if (!$storedPath) {
                    throw new \Exception('Failed to store image');
                }
                return $storedPath;
            }
        } catch (\Exception $e) {
            // If image processing fails, try to store the original file
            $storedPath = $file->storeAs($directory, $filename, 'public');
            if (!$storedPath) {
                throw new \Exception('Failed to store image: ' . $e->getMessage());
            }
            return $storedPath;
        }

        return $path;
    }

    /**
     * Delete a file from storage.
     *
     * @param string $path
     * @param string $disk
     * @return bool
     */
    public function deleteFile(string $path, string $disk = 'public'): bool
    {
        try {
            if (Storage::disk($disk)->exists($path)) {
                return Storage::disk($disk)->delete($path);
            }
            return true; // File doesn't exist, consider it deleted
        } catch (\Exception $e) {
            \Log::error('Failed to delete file: ' . $e->getMessage(), ['path' => $path]);
            return false;
        }
    }

    /**
     * Get file information.
     *
     * @param string $path
     * @param string $disk
     * @return array|null
     */
    public function getFileInfo(string $path, string $disk = 'public'): ?array
    {
        try {
            if (!Storage::disk($disk)->exists($path)) {
                return null;
            }

            return [
                'path' => $path,
                'size' => Storage::disk($disk)->size($path),
                'last_modified' => Storage::disk($disk)->lastModified($path),
                'url' => Storage::disk($disk)->url($path),
                'mime_type' => Storage::disk($disk)->mimeType($path),
            ];
        } catch (\Exception $e) {
            \Log::error('Failed to get file info: ' . $e->getMessage(), ['path' => $path]);
            return null;
        }
    }

    /**
     * Move a file from one location to another.
     *
     * @param string $from
     * @param string $to
     * @param string $disk
     * @return bool
     */
    public function moveFile(string $from, string $to, string $disk = 'public'): bool
    {
        try {
            // Ensure destination directory exists
            $destinationDir = dirname($to);
            if (!Storage::disk($disk)->exists($destinationDir)) {
                Storage::disk($disk)->makeDirectory($destinationDir);
            }

            return Storage::disk($disk)->move($from, $to);
        } catch (\Exception $e) {
            \Log::error('Failed to move file: ' . $e->getMessage(), [
                'from' => $from,
                'to' => $to
            ]);
            return false;
        }
    }

    /**
     * Copy a file from one location to another.
     *
     * @param string $from
     * @param string $to
     * @param string $disk
     * @return bool
     */
    public function copyFile(string $from, string $to, string $disk = 'public'): bool
    {
        try {
            // Ensure destination directory exists
            $destinationDir = dirname($to);
            if (!Storage::disk($disk)->exists($destinationDir)) {
                Storage::disk($disk)->makeDirectory($destinationDir);
            }

            return Storage::disk($disk)->copy($from, $to);
        } catch (\Exception $e) {
            \Log::error('Failed to copy file: ' . $e->getMessage(), [
                'from' => $from,
                'to' => $to
            ]);
            return false;
        }
    }

    /**
     * Validate file upload.
     *
     * @param UploadedFile $file
     * @param array $rules
     * @return array
     */
    public function validateFile(UploadedFile $file, array $rules = []): array
    {
        $errors = [];

        // Default rules
        $maxSize = $rules['max_size'] ?? 10 * 1024 * 1024; // 10MB
        $allowedTypes = $rules['allowed_types'] ?? $this->getDefaultAllowedTypes();
        $maxFilenameLength = $rules['max_filename_length'] ?? 255;

        // Check file size
        if ($file->getSize() > $maxSize) {
            $errors[] = 'File size exceeds ' . $this->formatFileSize($maxSize) . ' limit';
        }

        // Check file type
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            $errors[] = 'File type not allowed: ' . $file->getMimeType();
        }

        // Check filename length
        if (strlen($file->getClientOriginalName()) > $maxFilenameLength) {
            $errors[] = 'Filename too long (max ' . $maxFilenameLength . ' characters)';
        }

        // Check for potentially dangerous files
        $dangerousExtensions = ['exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js', 'php'];
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (in_array($extension, $dangerousExtensions)) {
            $errors[] = 'File type potentially dangerous';
        }

        return $errors;
    }

    /**
     * Get default allowed file types.
     *
     * @return array
     */
    public function getDefaultAllowedTypes(): array
    {
        return [
            // Images
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/svg+xml',
            'image/webp',

            // Documents
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'text/csv',

            // Archives
            'application/zip',
            'application/x-rar-compressed',
            'application/x-7z-compressed',

            // Other
            'application/json',
            'application/xml',
            'text/xml',
        ];
    }

    /**
     * Sanitize filename for safe storage.
     *
     * @param string $filename
     * @return string
     */
    protected function sanitizeFilename(string $filename): string
    {
        // Remove or replace dangerous characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        
        // Remove multiple underscores
        $filename = preg_replace('/_+/', '_', $filename);
        
        // Remove leading/trailing underscores and dots
        $filename = trim($filename, '_.');
        
        // Ensure filename is not empty
        if (empty($filename)) {
            $filename = 'file_' . uniqid();
        }

        return $filename;
    }

    /**
     * Format file size in human readable format.
     *
     * @param int $size
     * @param int $precision
     * @return string
     */
    protected function formatFileSize(int $size, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, $precision) . ' ' . $units[$i];
    }

    /**
     * Generate a unique filename.
     *
     * @param string $originalName
     * @param string $directory
     * @param string $disk
     * @return string
     */
    public function generateUniqueFilename(string $originalName, string $directory, string $disk = 'public'): string
    {
        $pathInfo = pathinfo($originalName);
        $basename = Str::slug($pathInfo['filename'] ?? 'file');
        $extension = $pathInfo['extension'] ?? '';
        
        $filename = $basename . '.' . $extension;
        $path = $directory . '/' . $filename;
        $counter = 1;

        // Keep trying until we find a unique filename
        while (Storage::disk($disk)->exists($path)) {
            $filename = $basename . '_' . $counter . '.' . $extension;
            $path = $directory . '/' . $filename;
            $counter++;
        }

        return $filename;
    }

    /**
     * Clean up old files in a directory.
     *
     * @param string $directory
     * @param int $olderThanHours
     * @param string $disk
     * @return int Number of files deleted
     */
    public function cleanupOldFiles(string $directory, int $olderThanHours = 24, string $disk = 'public'): int
    {
        try {
            if (!Storage::disk($disk)->exists($directory)) {
                return 0;
            }

            $files = Storage::disk($disk)->files($directory);
            $deletedCount = 0;
            $cutoffTime = now()->subHours($olderThanHours)->timestamp;

            foreach ($files as $file) {
                $lastModified = Storage::disk($disk)->lastModified($file);
                
                if ($lastModified < $cutoffTime) {
                    if (Storage::disk($disk)->delete($file)) {
                        $deletedCount++;
                    }
                }
            }

            return $deletedCount;
        } catch (\Exception $e) {
            \Log::error('Failed to cleanup old files: ' . $e->getMessage(), [
                'directory' => $directory
            ]);
            return 0;
        }
    }
}