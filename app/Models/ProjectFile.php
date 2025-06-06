<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\FileHelper;

class ProjectFile extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'description',
        'is_public',
        'download_count',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'file_size' => 'integer',
        'is_public' => 'boolean',
        'download_count' => 'integer',
    ];
    
    /**
     * Get the project that owns the file.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    
    /**
     * Get formatted file size using FileHelper.
     */
    public function getFormattedFileSizeAttribute()
    {
        return FileHelper::formatFileSize($this->file_size);
    }

    /**
     * Get file icon based on type using FileHelper.
     */
    public function getFileIconAttribute()
    {
        return FileHelper::getFileIcon($this->file_type);
    }

    /**
     * Get file category using FileHelper.
     */
    public function getFileCategoryAttribute()
    {
        return FileHelper::getFileCategory($this->file_type);
    }

    /**
     * Get human readable file type name.
     */
    public function getFileTypeNameAttribute()
    {
        return FileHelper::getFileTypeName($this->file_type);
    }

    /**
     * Check if file is an image.
     */
    public function isImage(): bool
    {
        return str_starts_with($this->file_type, 'image/');
    }

    /**
     * Check if file is a document.
     */
    public function isDocument(): bool
    {
        $documentTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'text/csv',
        ];

        return in_array($this->file_type, $documentTypes);
    }

    /**
     * Check if file is previewable.
     */
    public function isPreviewable(): bool
    {
        $previewableTypes = [
            'application/pdf',
            'text/plain',
            'text/csv',
            'application/json',
            'text/html',
        ];

        return $this->isImage() || in_array($this->file_type, $previewableTypes);
    }

    /**
     * Get download URL.
     */
    public function getDownloadUrlAttribute()
    {
        return route('admin.projects.files.download', [$this->project, $this]);
    }

    /**
     * Get preview URL.
     */
    public function getPreviewUrlAttribute()
    {
        if ($this->isPreviewable()) {
            return route('admin.projects.files.preview', [$this->project, $this]);
        }
        
        return null;
    }

    /**
     * Get thumbnail URL.
     */
    public function getThumbnailUrlAttribute()
    {
        return route('admin.projects.files.thumbnail', [$this->project, $this]);
    }

    /**
     * Scope for public files.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for private files.
     */
    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    /**
     * Scope by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope by file type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('file_type', 'like', $type . '%');
    }

    /**
     * Increment download count.
     */
    public function incrementDownloads()
    {
        $this->increment('download_count');
    }

    /**
     * Get file age in days.
     */
    public function getAgeInDaysAttribute()
    {
        return $this->created_at->diffInDays(now());
    }

    /**
     * Check if file was uploaded recently (within last 7 days).
     */
    public function isRecent(): bool
    {
        return $this->age_in_days <= 7;
    }
}