<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Default disk for file storage
     *
     * @var string
     */
    protected $disk = 'public';
    
    /**
     * Default image quality for compression
     *
     * @var int
     */
    protected $quality = 85;
    
    /**
     * Set the storage disk
     *
     * @param string $disk
     * @return $this
     */
    public function setDisk(string $disk)
    {
        $this->disk = $disk;
        return $this;
    }
    
    /**
     * Set the image quality
     *
     * @param int $quality
     * @return $this
     */
    public function setQuality(int $quality)
    {
        $this->quality = $quality;
        return $this;
    }
    
    /**
     * Upload a file to storage
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string|null $filename
     * @return string
     */
    public function uploadFile(UploadedFile $file, string $directory, ?string $filename = null): string
    {
        $filename = $filename ?? $this->generateFilename($file);
        $path = $file->storeAs($directory, $filename, $this->disk);
        
        return $path;
    }
    
    /**
     * Upload and optimize an image
     *
     * @param UploadedFile $image
     * @param string $directory
     * @param string|null $filename
     * @param int|null $width
     * @param int|null $height
     * @param int $quality
     * @return string
     */
    public function uploadImage(
        UploadedFile $image, 
        string $directory, 
        ?string $filename = null, 
        ?int $width = null, 
        ?int $height = null, 
        ?int $quality = null
    ): string {
        // Set quality if not provided
        $quality = $quality ?? $this->quality;
        
        // Generate a unique filename
        $filename = $filename ?? $this->generateFilename($image);
        
        // Get the full storage path
        $path = $directory . '/' . $filename;
        
        // Process the image
        $img = Image::make($image);
        
        // Resize the image if dimensions provided
        if ($width && $height) {
            $img->fit($width, $height);
        } elseif ($width) {
            $img->resize($width, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        } elseif ($height) {
            $img->resize(null, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }
        
        // Optimize and save the image
        $img->encode(null, $quality);
        Storage::disk($this->disk)->put($path, $img);
        
        return $path;
    }
    
    /**
     * Create a thumbnail from an image
     *
     * @param UploadedFile|string $image
     * @param string $directory
     * @param string|null $filename
     * @param int $width
     * @param int $height
     * @return string
     */
    public function createThumbnail(
        $image, 
        string $directory, 
        ?string $filename = null, 
        int $width = 300, 
        int $height = 300
    ): string {
        // If image is a file path, load it
        if (is_string($image)) {
            if (Storage::disk($this->disk)->exists($image)) {
                $img = Image::make(Storage::disk($this->disk)->get($image));
            } else {
                throw new \Exception("Source image not found: {$image}");
            }
        } else {
            $img = Image::make($image);
        }
        
        // Generate a filename if not provided
        $filename = $filename ?? $this->generateThumbnailFilename($image);
        
        // Get the full storage path
        $path = $directory . '/' . $filename;
        
        // Resize and crop to fit the dimensions
        $img->fit($width, $height);
        
        // Save the thumbnail
        Storage::disk($this->disk)->put($path, $img->encode());
        
        return $path;
    }
    
    /**
     * Create multiple image sizes (responsive images)
     *
     * @param UploadedFile $image
     * @param string $directory
     * @param array $sizes
     * @return array
     */
    public function createResponsiveImages(UploadedFile $image, string $directory, array $sizes): array
    {
        $paths = [];
        $baseFilename = pathinfo($this->generateFilename($image), PATHINFO_FILENAME);
        $extension = $image->getClientOriginalExtension();
        
        foreach ($sizes as $key => $size) {
            $width = $size['width'] ?? null;
            $height = $size['height'] ?? null;
            
            if (!$width && !$height) {
                continue;
            }
            
            $filename = $baseFilename . '-' . $key . '.' . $extension;
            
            $paths[$key] = $this->uploadImage(
                $image, 
                $directory, 
                $filename, 
                $width, 
                $height
            );
        }
        
        return $paths;
    }
    
    /**
     * Delete a file from storage
     *
     * @param string $path
     * @return bool
     */
    public function deleteFile(string $path): bool
    {
        if (Storage::disk($this->disk)->exists($path)) {
            return Storage::disk($this->disk)->delete($path);
        }
        
        return false;
    }
    
    /**
     * Generate a unique filename
     *
     * @param UploadedFile|string $file
     * @return string
     */
    protected function generateFilename($file): string
    {
        if (is_string($file)) {
            $extension = pathinfo($file, PATHINFO_EXTENSION);
        } else {
            $extension = $file->getClientOriginalExtension();
        }
        
        if (empty($extension)) {
            $extension = 'jpg';
        }
        
        return Str::random(20) . '_' . time() . '.' . $extension;
    }
    
    /**
     * Generate a filename for a thumbnail
     *
     * @param UploadedFile|string $file
     * @return string
     */
    protected function generateThumbnailFilename($file): string
    {
        if (is_string($file)) {
            $pathInfo = pathinfo($file);
            return $pathInfo['filename'] . '_thumb.' . ($pathInfo['extension'] ?? 'jpg');
        } else {
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            
            $name = pathinfo($filename, PATHINFO_FILENAME);
            
            return $name . '_thumb.' . $extension;
        }
    }
}