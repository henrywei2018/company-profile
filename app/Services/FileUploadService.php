<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Interfaces\ImageInterface;

class FileUploadService
{
    /**
     * Upload and process image.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $path
     * @param string|null $oldFile
     * @param int|null $width
     * @param int|null $height
     * @param int $quality
     * @return string
     * @throws \Exception
     */
    public function uploadImage(
        UploadedFile $file, 
        string $path, 
        ?string $oldFile = null, 
        ?int $width = null, 
        ?int $height = null,
        int $quality = 85
    ): string {
        try {
            // Validate file
            $this->validateImageFile($file);

            // Delete old file if exists
            if ($oldFile && Storage::disk('public')->exists($oldFile)) {
                Storage::disk('public')->delete($oldFile);
            }

            // Ensure directory exists
            if (!Storage::disk('public')->exists($path)) {
                Storage::disk('public')->makeDirectory($path);
            }

            // Generate unique filename
            $filename = $this->generateUniqueFilename($file);
            $fullPath = $path . '/' . $filename;

            // Process and resize image if dimensions specified
            if ($width || $height) {
                $processedImage = $this->processImage($file, $width, $height, $quality);
                Storage::disk('public')->put($fullPath, $processedImage);
            } else {
                // Store original file without processing
                $file->storeAs($path, $filename, 'public');
            }

            Log::info('Image uploaded successfully', [
                'original_name' => $file->getClientOriginalName(),
                'saved_path' => $fullPath,
                'size' => $file->getSize()
            ]);

            return $fullPath;

        } catch (\Exception $e) {
            Log::error('Failed to upload image: ' . $e->getMessage(), [
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'path' => $path
            ]);
            throw $e;
        }
    }

    /**
     * Create thumbnail from uploaded file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $path
     * @param string|null $filename
     * @param int $width
     * @param int $height
     * @param int $quality
     * @return string
     * @throws \Exception
     */
    public function createThumbnail(
        UploadedFile $file, 
        string $path, 
        ?string $filename = null, 
        int $width = 300, 
        int $height = 300,
        int $quality = 85
    ): string {
        try {
            // Ensure directory exists
            if (!Storage::disk('public')->exists($path)) {
                Storage::disk('public')->makeDirectory($path);
            }

            // Generate filename if not provided
            if (!$filename) {
                $filename = $this->generateUniqueFilename($file);
            }

            $fullPath = $path . '/' . $filename;

            // Create thumbnail
            $thumbnail = $this->processImage($file, $width, $height, $quality);
            Storage::disk('public')->put($fullPath, $thumbnail);

            Log::info('Thumbnail created successfully', [
                'original_file' => $file->getClientOriginalName(),
                'thumbnail_path' => $fullPath,
                'dimensions' => "{$width}x{$height}"
            ]);

            return $fullPath;

        } catch (\Exception $e) {
            Log::error('Failed to create thumbnail: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Upload file (non-image).
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $path
     * @param string|null $oldFile
     * @return string
     * @throws \Exception
     */
    public function uploadFile(UploadedFile $file, string $path, ?string $oldFile = null): string
    {
        try {
            // Delete old file if exists
            if ($oldFile && Storage::disk('public')->exists($oldFile)) {
                Storage::disk('public')->delete($oldFile);
            }

            // Ensure directory exists
            if (!Storage::disk('public')->exists($path)) {
                Storage::disk('public')->makeDirectory($path);
            }

            // Generate unique filename
            $filename = $this->generateUniqueFilename($file);
            $fullPath = $path . '/' . $filename;

            // Store file
            $file->storeAs($path, $filename, 'public');

            Log::info('File uploaded successfully', [
                'original_name' => $file->getClientOriginalName(),
                'saved_path' => $fullPath,
                'size' => $file->getSize()
            ]);

            return $fullPath;

        } catch (\Exception $e) {
            Log::error('Failed to upload file: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete file from storage.
     *
     * @param string $filePath
     * @return bool
     */
    public function deleteFile(string $filePath): bool
    {
        try {
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
                
                Log::info('File deleted successfully', ['path' => $filePath]);
                return true;
            }
            
            return false;

        } catch (\Exception $e) {
            Log::error('Failed to delete file: ' . $e->getMessage(), ['path' => $filePath]);
            return false;
        }
    }

    /**
     * Delete multiple files from storage.
     *
     * @param array $filePaths
     * @return int Number of files deleted
     */
    public function deleteFiles(array $filePaths): int
    {
        $deletedCount = 0;
        
        foreach ($filePaths as $path) {
            if ($this->deleteFile($path)) {
                $deletedCount++;
            }
        }
        
        return $deletedCount;
    }

    /**
     * Get file URL.
     *
     * @param string $filePath
     * @return string|null
     */
    public function getFileUrl(string $filePath): ?string
    {
        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->url($filePath);
        }
        
        return null;
    }

    /**
     * Check if file exists.
     *
     * @param string $filePath
     * @return bool
     */
    public function fileExists(string $filePath): bool
    {
        return Storage::disk('public')->exists($filePath);
    }

    /**
     * Get file size in bytes.
     *
     * @param string $filePath
     * @return int|null
     */
    public function getFileSize(string $filePath): ?int
    {
        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->size($filePath);
        }
        
        return null;
    }

    /**
     * Process image with Intervention Image.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param int|null $width
     * @param int|null $height
     * @param int $quality
     * @return string
     * @throws \Exception
     */
    private function processImage(UploadedFile $file, ?int $width, ?int $height, int $quality = 85): string
    {
        try {
            // Create image instance
            $image = Image::read($file->getRealPath());

            // Resize image based on provided dimensions
            if ($width && $height) {
                // Fit to exact dimensions (may crop)
                $image->cover($width, $height);
            } elseif ($width) {
                // Resize by width, maintain aspect ratio
                $image->scale(width: $width);
            } elseif ($height) {
                // Resize by height, maintain aspect ratio
                $image->scale(height: $height);
            }

            // Convert to JPEG and set quality
            $processedImage = $image->toJpeg($quality);

            return $processedImage;

        } catch (\Exception $e) {
            Log::error('Failed to process image: ' . $e->getMessage());
            throw new \Exception('Image processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate unique filename.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return string
     */
    private function generateUniqueFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        
        // Sanitize original name
        $sanitizedName = preg_replace('/[^a-zA-Z0-9\-_]/', '', $originalName);
        $sanitizedName = substr($sanitizedName, 0, 20); // Limit length
        
        // Generate unique filename
        $timestamp = time();
        $randomString = bin2hex(random_bytes(8));
        
        return "{$timestamp}_{$randomString}_{$sanitizedName}.{$extension}";
    }

    /**
     * Validate uploaded image file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @throws \Exception
     */
    private function validateImageFile(UploadedFile $file): void
    {
        // Check if file is valid
        if (!$file->isValid()) {
            throw new \Exception('Invalid file upload');
        }

        // Check file size (10MB max)
        $maxSize = 10 * 1024 * 1024; // 10MB in bytes
        if ($file->getSize() > $maxSize) {
            throw new \Exception('File size exceeds maximum allowed size (10MB)');
        }

        // Check if it's actually an image
        $allowedMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/bmp'
        ];

        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
            throw new \Exception('File must be a valid image (JPEG, PNG, GIF, WebP, or BMP)');
        }

        // Additional security check - verify image can be read
        try {
            $imageInfo = getimagesize($file->getRealPath());
            if (!$imageInfo) {
                throw new \Exception('File is not a valid image');
            }
        } catch (\Exception $e) {
            throw new \Exception('File validation failed: ' . $e->getMessage());
        }
    }

    /**
     * Get image dimensions.
     *
     * @param string $filePath
     * @return array|null [width, height]
     */
    public function getImageDimensions(string $filePath): ?array
    {
        try {
            if (!Storage::disk('public')->exists($filePath)) {
                return null;
            }

            $fullPath = Storage::disk('public')->path($filePath);
            $imageInfo = getimagesize($fullPath);

            if ($imageInfo) {
                return [
                    'width' => $imageInfo[0],
                    'height' => $imageInfo[1]
                ];
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Failed to get image dimensions: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Optimize existing image.
     *
     * @param string $filePath
     * @param int $quality
     * @return bool
     */
    public function optimizeImage(string $filePath, int $quality = 85): bool
    {
        try {
            if (!Storage::disk('public')->exists($filePath)) {
                return false;
            }

            $fullPath = Storage::disk('public')->path($filePath);
            $image = Image::read($fullPath);
            
            // Re-save with compression
            $optimized = $image->toJpeg($quality);
            Storage::disk('public')->put($filePath, $optimized);

            Log::info('Image optimized successfully', [
                'path' => $filePath,
                'quality' => $quality
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to optimize image: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Convert image format.
     *
     * @param string $filePath
     * @param string $newFormat (jpeg, png, gif, webp)
     * @param int $quality
     * @return string|null New file path
     */
    public function convertImageFormat(string $filePath, string $newFormat, int $quality = 85): ?string
    {
        try {
            if (!Storage::disk('public')->exists($filePath)) {
                return null;
            }

            $fullPath = Storage::disk('public')->path($filePath);
            $image = Image::read($fullPath);

            // Generate new filename with new extension
            $pathInfo = pathinfo($filePath);
            $newFilePath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.' . $newFormat;

            // Convert based on format
            switch (strtolower($newFormat)) {
                case 'jpeg':
                case 'jpg':
                    $converted = $image->toJpeg($quality);
                    break;
                case 'png':
                    $converted = $image->toPng();
                    break;
                case 'gif':
                    $converted = $image->toGif();
                    break;
                case 'webp':
                    $converted = $image->toWebp($quality);
                    break;
                default:
                    throw new \Exception("Unsupported format: {$newFormat}");
            }

            Storage::disk('public')->put($newFilePath, $converted);

            Log::info('Image format converted successfully', [
                'original_path' => $filePath,
                'new_path' => $newFilePath,
                'format' => $newFormat
            ]);

            return $newFilePath;

        } catch (\Exception $e) {
            Log::error('Failed to convert image format: ' . $e->getMessage());
            return null;
        }
    }
}