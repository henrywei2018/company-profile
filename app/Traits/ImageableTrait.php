<?php
// File: app/Traits/ImageableTrait.php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

trait ImageableTrait
{
    /**
     * Upload an image and store it.
     * 
     * @param UploadedFile|string $image
     * @param string $directory
     * @param int|null $width
     * @param int|null $height
     * @return string
     */
    public function uploadImage(UploadedFile|string $image, string $directory = 'images', ?int $width = 1200, ?int $height = null): string
    {
        // If image is already a path string, return it
        if (is_string($image) && !is_file($image)) {
            return $image;
        }
        
        // Generate a unique filename
        $filename = $this->generateImageFilename($image);
        
        // Get the full storage path
        $path = $directory . '/' . $filename;
        
        // Process and save the image
        $img = Image::make($image);
        
        // Resize the image
        if ($width && $height) {
            $img->fit($width, $height);
        } elseif ($width) {
            $img->resize($width, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }
        
        // Save the image to storage
        $img->stream();
        Storage::disk('public')->put($path, $img);
        
        return $path;
    }
    
    /**
     * Upload a thumbnail version of an image.
     * 
     * @param UploadedFile|string $image
     * @param string $directory
     * @param int $width
     * @param int $height
     * @return string|null
     */
    public function uploadThumbnail(UploadedFile|string $image, string $directory = 'thumbnails', int $width = 300, int $height = 300): ?string
    {
        // If image is already a path string, use it as source
        if (is_string($image) && !is_file($image)) {
            // Load from storage
            if (Storage::disk('public')->exists($image)) {
                $image = Storage::disk('public')->get($image);
            } else {
                return null;
            }
        }
        
        // Generate a unique filename
        $filename = $this->generateImageFilename($image);
        
        // Get the full storage path
        $path = $directory . '/' . $filename;
        
        // Process and save the thumbnail
        $img = Image::make($image);
        
        // Resize and crop to fit the dimensions
        $img->fit($width, $height);
        
        // Save the thumbnail to storage
        $img->stream();
        Storage::disk('public')->put($path, $img);
        
        return $path;
    }
    
    /**
     * Delete an image from storage.
     * 
     * @param string|null $path
     * @return bool
     */
    public function deleteImage(?string $path): bool
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
            return true;
        }
        
        return false;
    }
    
    /**
     * Get image URL.
     * 
     * @param string|null $path
     * @return string
     */
    public function getImageUrl(?string $path = null): string
    {
        $path = $path ?? $this->getImagePath();
        
        if (!$path) {
            return asset('images/default.jpg');
        }
        
        // Fix for Laravel 12: Use asset() with Storage path
        return asset('storage/' . $path);
    }
    
    /**
     * Get thumbnail URL.
     * 
     * @param string|null $path
     * @return string
     */
    public function getThumbnailUrl(?string $path = null): string
    {
        $path = $path ?? $this->getThumbnailPath();
        
        if (!$path) {
            return asset('images/default-thumb.jpg');
        }
        
        // Fix for Laravel 12: Use asset() with Storage path
        return asset('storage/' . $path);
    }
    
    /**
     * Generate a unique filename for an image.
     * 
     * @param UploadedFile|string $image
     * @return string
     */
    protected function generateImageFilename(UploadedFile|string $image): string
    {
        $extension = is_string($image) ? pathinfo($image, PATHINFO_EXTENSION) : 
            $image->getClientOriginalExtension();
            
        if (empty($extension)) {
            $extension = 'jpg';
        }
        
        return Str::slug(class_basename($this)) . '-' . 
               $this->id . '-' . 
               Str::random(10) . '.' . 
               $extension;
    }
    
    /**
     * Get the image path from the model.
     * 
     * @return string|null
     */
    protected function getImagePath(): ?string
    {
        if (isset($this->attributes['image'])) {
            return $this->attributes['image'];
        }
        
        if (isset($this->attributes['featured_image'])) {
            return $this->attributes['featured_image'];
        }
        
        if (isset($this->attributes['image_path'])) {
            return $this->attributes['image_path'];
        }
        
        return null;
    }
    
    /**
     * Get the thumbnail path from the model.
     * 
     * @return string|null
     */
    protected function getThumbnailPath(): ?string
    {
        if (isset($this->attributes['thumbnail'])) {
            return $this->attributes['thumbnail'];
        }
        
        $imagePath = $this->getImagePath();
        
        if ($imagePath) {
            $path = pathinfo($imagePath);
            return 'thumbnails/' . $path['filename'] . '.' . ($path['extension'] ?? 'jpg');
        }
        
        return null;
    }
}