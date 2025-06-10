<?php
// File: app/Services/UniversalFileUploadService.php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class UniversalFileUploadService
{
    protected array $defaultConfig = [
        'disk' => 'public',
        'max_file_size' => 10 * 1024 * 1024, // 10MB
        'max_files' => 10,
        'allowed_types' => [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain', 'text/csv',
            'application/zip', 'application/x-rar-compressed', 'application/x-7z-compressed'
        ],
        'image_resize' => [
            'enabled' => false,
            'max_width' => 1920,
            'max_height' => 1080,
            'quality' => 85
        ],
        'generate_thumbnails' => false,
        'thumbnail_size' => [150, 150]
    ];

    /**
     * Upload files with configuration
     */
    public function uploadFiles(
        Request $request,
        string $directory,
        array $config = [],
        ?callable $beforeSave = null,
        ?callable $afterSave = null
    ): array {
        $config = array_merge($this->defaultConfig, $config);
        
        $request->validate([
            'files' => 'required|array|min:1',
            'files.*' => "required|file|max:{$this->bytesToKb($config['max_file_size'])}",
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_public' => 'boolean',
        ]);

        $uploadedFiles = [];
        $files = $request->file('files', []);

        if (count($files) > $config['max_files']) {
            throw new \InvalidArgumentException("Maximum {$config['max_files']} files allowed");
        }

        foreach ($files as $file) {
            try {
                $fileData = $this->processFile($file, $directory, $config);
                
                // Execute before save callback
                if ($beforeSave) {
                    $fileData = $beforeSave($fileData, $file, $request) ?: $fileData;
                }

                $uploadedFiles[] = $fileData;

                // Execute after save callback
                if ($afterSave) {
                    $afterSave($fileData, $file, $request);
                }
            } catch (\Exception $e) {
                \Log::error('File upload failed: ' . $e->getMessage(), [
                    'file_name' => $file->getClientOriginalName(),
                    'directory' => $directory
                ]);
                
                // Continue with other files instead of failing completely
                continue;
            }
        }

        if (empty($uploadedFiles)) {
            throw new \Exception('No files were uploaded successfully');
        }

        return $uploadedFiles;
    }

    /**
     * Process individual file upload
     */
    protected function processFile(UploadedFile $file, string $directory, array $config): array
    {
        // Validate file
        $this->validateFile($file, $config);

        // Generate filename
        $filename = $this->generateFilename($file);
        $filePath = $directory . '/' . $filename;

        // Handle image processing
        if ($this->isImage($file) && $config['image_resize']['enabled']) {
            $this->processImage($file, $filePath, $config);
        } else {
            // Regular file upload
            $storedPath = $file->storeAs($directory, $filename, $config['disk']);
            if (!$storedPath) {
                throw new \Exception('Failed to store file');
            }
        }

        // Generate thumbnail if required
        $thumbnailPath = null;
        if ($config['generate_thumbnails'] && $this->isImage($file)) {
            $thumbnailPath = $this->generateThumbnail($filePath, $config);
        }

        return [
            'original_name' => $file->getClientOriginalName(),
            'filename' => $filename,
            'path' => $filePath,
            'thumbnail_path' => $thumbnailPath,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'extension' => $file->getClientOriginalExtension(),
            'disk' => $config['disk'],
            'url' => Storage::disk($config['disk'])->url($filePath),
            'thumbnail_url' => $thumbnailPath ? Storage::disk($config['disk'])->url($thumbnailPath) : null,
        ];
    }

    /**
     * Validate uploaded file
     */
    protected function validateFile(UploadedFile $file, array $config): void
    {
        // Size validation
        if ($file->getSize() > $config['max_file_size']) {
            throw new \InvalidArgumentException(
                "File '{$file->getClientOriginalName()}' exceeds maximum size of {$this->formatFileSize($config['max_file_size'])}"
            );
        }

        // Type validation
        if (!in_array($file->getMimeType(), $config['allowed_types'])) {
            throw new \InvalidArgumentException(
                "File type '{$file->getMimeType()}' is not allowed"
            );
        }

        // Filename validation
        if (strlen($file->getClientOriginalName()) > 255) {
            throw new \InvalidArgumentException('Filename is too long (max 255 characters)');
        }

        // Security validation
        $dangerousExtensions = ['exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js', 'php'];
        if (in_array(strtolower($file->getClientOriginalExtension()), $dangerousExtensions)) {
            throw new \InvalidArgumentException('File type is potentially dangerous');
        }
    }

    /**
     * Generate safe filename
     */
    protected function generateFilename(UploadedFile $file): string
    {
        $pathInfo = pathinfo($file->getClientOriginalName());
        $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';
        $basename = Str::slug($pathInfo['filename'] ?? 'file');
        
        return uniqid() . '_' . $basename . $extension;
    }

    /**
     * Process image upload with resizing
     */
    protected function processImage(UploadedFile $file, string $path, array $config): void
    {
        if (!class_exists('Intervention\Image\Laravel\Facades\Image')) {
            // Fallback to regular upload if Intervention Image is not available
            $file->storeAs(dirname($path), basename($path), $config['disk']);
            return;
        }

        $resizeConfig = $config['image_resize'];
        $image = Image::make($file->getRealPath());

        // Resize if needed
        if ($resizeConfig['max_width'] || $resizeConfig['max_height']) {
            $image->resize($resizeConfig['max_width'], $resizeConfig['max_height'], function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        // Save with quality
        $fullPath = Storage::disk($config['disk'])->path($path);
        
        // Ensure directory exists
        $directory = dirname($fullPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $image->save($fullPath, $resizeConfig['quality']);
    }

    /**
     * Generate thumbnail for image
     */
    protected function generateThumbnail(string $imagePath, array $config): ?string
    {
        if (!class_exists('Intervention\Image\Laravel\Facades\Image')) {
            return null;
        }

        try {
            $thumbnailPath = str_replace(
                basename($imagePath),
                'thumb_' . basename($imagePath),
                $imagePath
            );

            $fullImagePath = Storage::disk($config['disk'])->path($imagePath);
            $fullThumbnailPath = Storage::disk($config['disk'])->path($thumbnailPath);

            $image = Image::make($fullImagePath);
            $image->resize(
                $config['thumbnail_size'][0],
                $config['thumbnail_size'][1],
                function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                }
            );

            $image->save($fullThumbnailPath, 80);

            return $thumbnailPath;
        } catch (\Exception $e) {
            \Log::warning('Thumbnail generation failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete file and its thumbnail
     */
    public function deleteFile(string $filePath, string $disk = 'public', ?string $thumbnailPath = null): bool
    {
        try {
            $deleted = true;

            // Delete main file
            if (Storage::disk($disk)->exists($filePath)) {
                $deleted = Storage::disk($disk)->delete($filePath);
            }

            // Delete thumbnail if exists
            if ($thumbnailPath && Storage::disk($disk)->exists($thumbnailPath)) {
                Storage::disk($disk)->delete($thumbnailPath);
            }

            return $deleted;
        } catch (\Exception $e) {
            \Log::error('File deletion failed: ' . $e->getMessage(), [
                'file_path' => $filePath,
                'disk' => $disk
            ]);
            return false;
        }
    }

    /**
     * Generate file preview
     */
    public function generatePreview(string $filePath, string $mimeType, string $disk = 'public'): string
    {
        if (!Storage::disk($disk)->exists($filePath)) {
            return '<div class="text-center py-8 text-red-600">File not found</div>';
        }

        try {
            if (str_starts_with($mimeType, 'image/')) {
                return $this->generateImagePreview($filePath, $disk);
            }

            if ($mimeType === 'application/pdf') {
                return $this->generatePdfPreview($filePath, $disk);
            }

            if (str_starts_with($mimeType, 'text/') || in_array($mimeType, ['application/json', 'application/xml'])) {
                return $this->generateTextPreview($filePath, $disk);
            }

            return $this->generateGenericPreview($filePath, $mimeType, $disk);
        } catch (\Exception $e) {
            return '<div class="text-center py-8 text-red-600">Preview generation failed: ' . $e->getMessage() . '</div>';
        }
    }

    /**
     * Generate image preview
     */
    protected function generateImagePreview(string $filePath, string $disk): string
    {
        $imageUrl = Storage::disk($disk)->url($filePath);
        
        return "
            <div class='text-center'>
                <div class='relative inline-block'>
                    <img src='{$imageUrl}' 
                         alt='Preview' 
                         class='max-w-full max-h-96 mx-auto rounded-lg shadow-md'
                         onload=\"document.getElementById('image-loading').style.display='none'\"
                         onerror=\"document.getElementById('image-error').style.display='block'; this.style.display='none';\">
                    <div id='image-loading' class='absolute inset-0 flex items-center justify-center bg-gray-100 dark:bg-gray-800 rounded-lg'>
                        <div class='animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600'></div>
                    </div>
                </div>
                <div id='image-error' class='hidden p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800'>
                    <p class='text-red-600 dark:text-red-400'>Failed to load image</p>
                </div>
            </div>
        ";
    }

    /**
     * Generate PDF preview
     */
    protected function generatePdfPreview(string $filePath, string $disk): string
    {
        $pdfUrl = Storage::disk($disk)->url($filePath);

        return "
            <div class='w-full h-96'>
                <iframe src='{$pdfUrl}' class='w-full h-full border-0' type='application/pdf'>
                    <p>Your browser does not support PDF preview. 
                       <a href='{$pdfUrl}' target='_blank' class='text-blue-600 hover:underline'>Download the PDF</a>
                    </p>
                </iframe>
            </div>
        ";
    }

    /**
     * Generate text preview
     */
    protected function generateTextPreview(string $filePath, string $disk): string
    {
        $fullPath = Storage::disk($disk)->path($filePath);
        $content = file_get_contents($fullPath);

        // Limit content size for preview
        if (strlen($content) > 10000) {
            $content = substr($content, 0, 10000) . "\n\n... (truncated)";
        }

        $escapedContent = htmlspecialchars($content);

        return "
            <div class='text-left'>
                <div class='bg-gray-100 dark:bg-gray-900 rounded-lg p-4 max-h-96 overflow-auto'>
                    <pre class='text-sm font-mono whitespace-pre-wrap'>{$escapedContent}</pre>
                </div>
            </div>
        ";
    }

    /**
     * Generate generic file preview
     */
    protected function generateGenericPreview(string $filePath, string $mimeType, string $disk): string
    {
        $fileSize = Storage::disk($disk)->size($filePath);
        $fileName = basename($filePath);

        return "
            <div class='text-center'>
                <div class='mx-auto w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mb-4'>
                    <svg class='w-12 h-12 text-gray-400' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                        <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'/>
                    </svg>
                </div>
                <h3 class='text-lg font-medium text-gray-900 dark:text-white mb-2'>{$fileName}</h3>
                <div class='space-y-2 text-sm text-gray-600 dark:text-gray-400'>
                    <p><strong>Size:</strong> {$this->formatFileSize($fileSize)}</p>
                    <p><strong>Type:</strong> {$mimeType}</p>
                </div>
            </div>
        ";
    }

    /**
     * Get file metadata
     */
    public function getFileMetadata(string $filePath, string $disk = 'public'): ?array
    {
        if (!Storage::disk($disk)->exists($filePath)) {
            return null;
        }

        try {
            return [
                'path' => $filePath,
                'size' => Storage::disk($disk)->size($filePath),
                'last_modified' => Storage::disk($disk)->lastModified($filePath),
                'url' => Storage::disk($disk)->url($filePath),
                'mime_type' => Storage::disk($disk)->mimeType($filePath),
                'exists' => true
            ];
        } catch (\Exception $e) {
            \Log::error('Failed to get file metadata: ' . $e->getMessage(), [
                'file_path' => $filePath,
                'disk' => $disk
            ]);
            return null;
        }
    }

    /**
     * Bulk delete files
     */
    public function bulkDeleteFiles(array $filePaths, string $disk = 'public'): array
    {
        $results = [
            'deleted' => [],
            'failed' => []
        ];

        foreach ($filePaths as $filePath) {
            try {
                if ($this->deleteFile($filePath, $disk)) {
                    $results['deleted'][] = $filePath;
                } else {
                    $results['failed'][] = $filePath;
                }
            } catch (\Exception $e) {
                $results['failed'][] = $filePath;
                \Log::error('Bulk delete failed for file: ' . $e->getMessage(), [
                    'file_path' => $filePath
                ]);
            }
        }

        return $results;
    }

    /**
     * Clean up old files
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
            \Log::error('Cleanup failed: ' . $e->getMessage(), [
                'directory' => $directory
            ]);
            return 0;
        }
    }

    /**
     * Create zip archive from files
     */
    public function createZipArchive(array $filePaths, string $zipName, string $disk = 'public'): ?string
    {
        try {
            $zipPath = storage_path('app/temp/' . $zipName);
            
            // Ensure temp directory exists
            if (!is_dir(dirname($zipPath))) {
                mkdir(dirname($zipPath), 0755, true);
            }

            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE) !== TRUE) {
                throw new \Exception('Cannot create zip file');
            }

            $addedFiles = 0;
            foreach ($filePaths as $filePath) {
                if (Storage::disk($disk)->exists($filePath)) {
                    $fullPath = Storage::disk($disk)->path($filePath);
                    $zip->addFile($fullPath, basename($filePath));
                    $addedFiles++;
                }
            }

            $zip->close();

            return $addedFiles > 0 ? $zipPath : null;
        } catch (\Exception $e) {
            \Log::error('Zip creation failed: ' . $e->getMessage());
            return null;
        }
    }

    // Utility Methods

    /**
     * Check if file is an image
     */
    protected function isImage(UploadedFile $file): bool
    {
        return str_starts_with($file->getMimeType(), 'image/');
    }

    /**
     * Format file size
     */
    public function formatFileSize(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Convert bytes to kilobytes for validation
     */
    protected function bytesToKb(int $bytes): int
    {
        return (int) ceil($bytes / 1024);
    }

    /**
     * Get file type category
     */
    public function getFileCategory(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        if ($mimeType === 'application/pdf') {
            return 'document';
        }

        if (in_array($mimeType, [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'text/csv'
        ])) {
            return 'document';
        }

        if (in_array($mimeType, [
            'application/zip',
            'application/x-rar-compressed',
            'application/x-7z-compressed'
        ])) {
            return 'archive';
        }

        return 'other';
    }

    /**
     * Generate unique filename ensuring no conflicts
     */
    public function generateUniqueFilename(string $originalName, string $directory, string $disk = 'public'): string
    {
        $pathInfo = pathinfo($originalName);
        $basename = Str::slug($pathInfo['filename'] ?? 'file');
        $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';
        
        $filename = $basename . $extension;
        $path = $directory . '/' . $filename;
        $counter = 1;

        // Keep trying until we find a unique filename
        while (Storage::disk($disk)->exists($path)) {
            $filename = $basename . '_' . $counter . $extension;
            $path = $directory . '/' . $filename;
            $counter++;
        }

        return $filename;
    }
}