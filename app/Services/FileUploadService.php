<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

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
     * @return string
     */
    public function uploadImage(UploadedFile $file, $path, $oldFile = null, $width = null, $height = null)
    {
        // Delete old file if exists
        if ($oldFile && Storage::disk('public')->exists($oldFile)) {
            Storage::disk('public')->delete($oldFile);
        }
        
        // Generate unique name
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $fullPath = $path . '/' . $filename;
        
        // Process and resize image if needed
        if ($width || $height) {
            $img = Image::make($file->getRealPath());
            
            if ($width && $height) {
                $img->fit($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
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
            
            // Save processed image
            $img->stream();
            Storage::disk('public')->put($fullPath, $img);
        } else {
            // Store original file without processing
            Storage::disk('public')->putFileAs($path, $file, $filename);
        }
        
        return $fullPath;
    }
}