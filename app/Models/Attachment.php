<?php
// File: app/Models/Attachment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'attachable_id',
        'attachable_type',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'original_name',
        'disk',
        'metadata',
        'uploaded_by',
        'category',
        'description',
        'is_public',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'metadata' => 'array',
        'is_public' => 'boolean',
        'is_active' => 'boolean',
        'file_size' => 'integer',
    ];

    /**
     * Get the parent attachable model (quotation, message, etc.).
     */
    public function attachable()
    {
        return $this->morphTo();
    }

    /**
     * Get the user who uploaded this attachment.
     */
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Scope for active attachments.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for public attachments.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for specific category.
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get the full URL for the attachment.
     */
    public function getUrlAttribute()
    {
        if ($this->disk === 'public') {
            return Storage::disk('public')->url($this->file_path);
        }
        
        return Storage::disk($this->disk)->url($this->file_path);
    }

    /**
     * Get the full file path.
     */
    public function getFullPathAttribute()
    {
        return Storage::disk($this->disk)->path($this->file_path);
    }

    /**
     * Check if the file exists.
     */
    public function exists()
    {
        return Storage::disk($this->disk)->exists($this->file_path);
    }

    /**
     * Get formatted file size.
     */
    public function getFormattedFileSizeAttribute()
    {
        return $this->formatBytes($this->file_size);
    }

    /**
     * Get file extension.
     */
    public function getFileExtensionAttribute()
    {
        return strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));
    }

    /**
     * Get file icon class based on file type.
     */
    public function getFileIconAttribute()
    {
        $extension = $this->file_extension;
        
        return match($extension) {
            'pdf' => 'text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900/30',
            'doc', 'docx' => 'text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/30',
            'xls', 'xlsx' => 'text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900/30',
            'jpg', 'jpeg', 'png', 'gif', 'webp' => 'text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-900/30',
            'zip', 'rar', '7z' => 'text-orange-600 dark:text-orange-400 bg-orange-100 dark:bg-orange-900/30',
            default => 'text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700'
        };
    }

    /**
     * Check if file is an image.
     */
    public function isImage()
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        return in_array($this->file_extension, $imageExtensions);
    }

    /**
     * Check if file is a document.
     */
    public function isDocument()
    {
        $documentExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];
        return in_array($this->file_extension, $documentExtensions);
    }

    /**
     * Check if file is an archive.
     */
    public function isArchive()
    {
        $archiveExtensions = ['zip', 'rar', '7z', 'tar', 'gz'];
        return in_array($this->file_extension, $archiveExtensions);
    }

    /**
     * Delete the file from storage when model is deleted.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($attachment) {
            // Delete the actual file from storage
            if ($attachment->exists()) {
                Storage::disk($attachment->disk)->delete($attachment->file_path);
            }
        });
    }

    /**
     * Format bytes into human readable format.
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Create attachment from uploaded file.
     */
    public static function createFromUploadedFile($file, $attachable, $category = null, $description = null)
    {
        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        
        // Determine storage path based on attachable type
        $folder = match(class_basename($attachable)) {
            'Quotation' => 'quotation_attachments',
            'Message' => 'message_attachments',
            'Project' => 'project_attachments',
            default => 'attachments'
        };
        
        $path = $folder . '/' . $attachable->id;
        
        // Store the file
        $filePath = $file->storeAs($path, $filename, 'public');
        
        // Create attachment record
        return self::create([
            'attachable_id' => $attachable->id,
            'attachable_type' => get_class($attachable),
            'file_name' => $filename,
            'file_path' => $filePath,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'original_name' => $file->getClientOriginalName(),
            'disk' => 'public',
            'uploaded_by' => auth()->id(),
            'category' => $category,
            'description' => $description,
            'is_public' => false,
            'is_active' => true,
        ]);
    }
}