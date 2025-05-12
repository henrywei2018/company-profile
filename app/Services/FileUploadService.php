<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;

class FileUploadService
{
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
        $path = $file->storeAs($directory, $filename, 'public');
        
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
    int $quality = 80
    ): string {
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
        Storage::disk('public')->put($path, $img);
        
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
    public function createThumbnail($image, string $directory, ?string $filename = null, int $width = 300, int $height = 300): string
    {
        // If image is a file path, load it
        if (is_string($image)) {
            if (Storage::disk('public')->exists($image)) {
                $img = Image::make(Storage::disk('public')->get($image));
            } else {
                throw new \Exception("Source image not found: {$image}");
            }
        } else {
            $img = Image::make($image);
        }
        
        // Generate a filename if not provided
        $filename = $filename ?? $this->generateFilename($image);
        
        // Get the full storage path
        $path = $directory . '/' . $filename;
        
        // Resize and crop to fit the dimensions
        $img->fit($width, $height);
        
        // Save the thumbnail
        Storage::disk('public')->put($path, $img->encode());
        
        return $path;
    }
    
    /**
     * Delete a file from storage
     *
     * @param string $path
     * @return bool
     */
    public function deleteFile(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
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
}