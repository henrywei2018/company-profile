<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\FileHelper;
use Illuminate\Support\Facades\Storage;


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
    if (!$this->file_size || !is_numeric($this->file_size)) {
        return 'Unknown size';
    }
    
    try {
        $result = FileHelper::formatFileSize($this->file_size);
        
        // Ensure we return a string
        if (is_array($result)) {
            \Log::warning('FileHelper::formatFileSize returned array', [
                'file_id' => $this->id,
                'file_size' => $this->file_size,
                'result' => $result
            ]);
            return 'Unknown size';
        }
        
        return is_string($result) ? $result : (string) $result;
        
    } catch (\Exception $e) {
        \Log::error('Error formatting file size', [
            'file_id' => $this->id,
            'file_size' => $this->file_size,
            'error' => $e->getMessage()
        ]);
        return 'Unknown size';
    }
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
    public function isPreviewable(): bool
{
    $previewableTypes = [
        'application/pdf',
        'text/plain',
        'text/csv',
        'application/json',
        'text/html',
        'application/xml',
        'text/xml',
    ];

    return $this->isImage() || in_array($this->file_type, $previewableTypes);
}

/**
 * Get file extension from filename.
 */
public function getFileExtensionAttribute(): string
{
    return strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));
}

/**
 * Get filename without extension.
 */
public function getFileBasenameAttribute(): string
{
    return pathinfo($this->file_name, PATHINFO_FILENAME);
}

/**
 * Check if file is recently uploaded (within last 7 days).
 */
public function isRecentlyUploaded(): bool
{
    return $this->created_at->isAfter(now()->subDays(7));
}

/**
 * Get file status based on various factors.
 */
public function getFileStatusAttribute(): string
{
    if (!Storage::disk('public')->exists($this->file_path)) {
        return 'missing';
    }
    
    if ($this->isRecentlyUploaded()) {
        return 'new';
    }
    
    if ($this->download_count > 10) {
        return 'popular';
    }
    
    return 'normal';
}

/**
 * Get relative time when file was uploaded.
 */
public function getUploadedTimeAttribute(): string
{
    return $this->created_at->diffForHumans();
}

/**
 * Check if file can be safely deleted.
 */
public function canBeDeleted(): bool
{
    // Add any business logic here
    // For example, prevent deletion of files with high download counts
    // or files that are referenced elsewhere
    
    return true; // Default: all files can be deleted
}

/**
 * Get file URL for public access (if file is public).
 */
public function getPublicUrlAttribute(): ?string
{
    if (!$this->is_public) {
        return null;
    }
    
    return Storage::url($this->file_path);
}

/**
 * Get secure download URL (always goes through controller).
 */
public function getSecureDownloadUrlAttribute(): string
{
    return route('admin.projects.files.download', [
        'project' => $this->project_id,
        'file' => $this->id
    ]);
}

/**
 * Scope for filtering by file extension.
 */
public function scopeByExtension($query, string $extension)
{
    return $query->where('file_name', 'like', "%{$extension}");
}

/**
 * Scope for filtering by upload date range.
 */
public function scopeUploadedBetween($query, $startDate, $endDate)
{
    return $query->whereBetween('created_at', [$startDate, $endDate]);
}

/**
 * Scope for filtering by file size range.
 */
public function scopeBySizeRange($query, int $minSize, int $maxSize)
{
    return $query->whereBetween('file_size', [$minSize, $maxSize]);
}

/**
 * Scope for popular files (high download count).
 */
public function scopePopular($query, int $minDownloads = 5)
{
    return $query->where('download_count', '>=', $minDownloads);
}

/**
 * Scope for recently uploaded files.
 */
public function scopeRecent($query, int $days = 7)
{
    return $query->where('created_at', '>=', now()->subDays($days));
}

/**
 * Scope for large files.
 */
public function scopeLarge($query, int $sizeInMB = 10)
{
    $sizeInBytes = $sizeInMB * 1024 * 1024;
    return $query->where('file_size', '>=', $sizeInBytes);
}

/**
 * Get file info array for API responses.
 */
public function toApiArray(): array
{
    return [
        'id' => $this->id,
        'name' => $this->file_name,
        'size' => $this->file_size,
        'formatted_size' => $this->formatted_file_size,
        'type' => $this->file_type,
        'type_name' => $this->file_type_name,
        'category' => $this->category ?: 'general',
        'is_image' => $this->isImage(),
        'is_previewable' => $this->isPreviewable(),
        'download_count' => $this->download_count,
        'uploaded_at' => $this->created_at->toISOString(),
        'uploaded_time' => $this->uploaded_time,
        'urls' => [
            'download' => $this->secure_download_url,
            'preview' => $this->isPreviewable() ? route('admin.projects.files.preview', [
                'project' => $this->project_id,
                'file' => $this->id
            ]) : null,
            'thumbnail' => route('admin.projects.files.thumbnail', [
                'project' => $this->project_id,
                'file' => $this->id
            ]),
        ]
    ];
}

/**
 * Get safe filename for downloads (removes potentially dangerous characters).
 */
public function getSafeFilenameAttribute(): string
{
    return preg_replace('/[^a-zA-Z0-9._-]/', '_', $this->file_name);
}

/**
 * Check if file exists on disk.
 */
public function existsOnDisk(): bool
{
    return Storage::disk('public')->exists($this->file_path);
}

/**
 * Get file's last modified time from disk.
 */
public function getLastModifiedAttribute(): ?\Carbon\Carbon
{
    if (!$this->existsOnDisk()) {
        return null;
    }
    
    try {
        $timestamp = Storage::disk('public')->lastModified($this->file_path);
        return $timestamp ? \Carbon\Carbon::createFromTimestamp($timestamp) : null;
    } catch (\Exception $e) {
        return null;
    }
}

/**
 * Get file's actual size from disk (in case it differs from stored size).
 */
public function getActualFileSizeAttribute(): ?int
{
    if (!$this->existsOnDisk()) {
        return null;
    }
    
    try {
        return Storage::disk('public')->size($this->file_path);
    } catch (\Exception $e) {
        return null;
    }
}

/**
 * Verify file integrity (check if stored size matches actual size).
 */
public function verifyIntegrity(): bool
{
    $actualSize = $this->actual_file_size;
    
    if ($actualSize === null) {
        return false; // File doesn't exist
    }
    
    return $actualSize === $this->file_size;
}

/**
 * Get file hash for duplicate detection.
 */
public function getFileHashAttribute(): ?string
{
    if (!$this->existsOnDisk()) {
        return null;
    }
    
    try {
        $filePath = Storage::disk('public')->path($this->file_path);
        return hash_file('md5', $filePath);
    } catch (\Exception $e) {
        return null;
    }
}

/**
 * Check if this file is a duplicate of another file in the same project.
 */
public function isDuplicate(): bool
{
    $fileHash = $this->file_hash;
    
    if (!$fileHash) {
        return false;
    }
    
    return static::where('project_id', $this->project_id)
        ->where('id', '!=', $this->id)
        ->get()
        ->contains(function ($file) use ($fileHash) {
            return $file->file_hash === $fileHash;
        });
}

/**
 * Get similar files (same project, similar name or type).
 */
public function getSimilarFiles()
{
    $basename = $this->file_basename;
    $extension = $this->file_extension;
    
    return static::where('project_id', $this->project_id)
        ->where('id', '!=', $this->id)
        ->where(function ($query) use ($basename, $extension) {
            $query->where('file_name', 'like', "%{$basename}%")
                  ->orWhere('file_name', 'like', "%.{$extension}");
        })
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
}

/**
 * Generate a unique filename if current name conflicts.
 */
public function generateUniqueFilename(): string
{
    $basename = pathinfo($this->file_name, PATHINFO_FILENAME);
    $extension = pathinfo($this->file_name, PATHINFO_EXTENSION);
    
    $counter = 1;
    $newFilename = $this->file_name;
    
    while (static::where('project_id', $this->project_id)
                 ->where('id', '!=', $this->id)
                 ->where('file_name', $newFilename)
                 ->exists()) {
        $newFilename = $basename . '_' . $counter . ($extension ? '.' . $extension : '');
        $counter++;
    }
    
    return $newFilename;
}

/**
 * Move file to a different category.
 */
public function moveToCategory(string $newCategory): bool
{
    try {
        $this->update(['category' => $newCategory]);
        return true;
    } catch (\Exception $e) {
        \Log::error('Failed to move file to category: ' . $e->getMessage(), [
            'file_id' => $this->id,
            'old_category' => $this->category,
            'new_category' => $newCategory,
        ]);
        return false;
    }
}

/**
 * Archive file (move to archive category).
 */
public function archive(): bool
{
    return $this->moveToCategory('archived');
}

/**
 * Mark file as favorite/important.
 */
public function markAsFavorite(): bool
{
    try {
        $this->update(['is_favorite' => true]);
        return true;
    } catch (\Exception $e) {
        return false;
    }
}

/**
 * Remove favorite status.
 */
public function unmarkAsFavorite(): bool
{
    try {
        $this->update(['is_favorite' => false]);
        return true;
    } catch (\Exception $e) {
        return false;
    }
}


/**
 * Get file access permissions for current user.
 */
public function getAccessPermissions(): array
{
    $user = auth()->user();
    
    if (!$user) {
        return ['read' => false, 'write' => false, 'delete' => false];
    }
    
    // Basic permission check - you can extend this based on your needs
    $canRead = $user->can('view', $this->project);
    $canWrite = $user->can('update', $this->project);
    $canDelete = $user->can('update', $this->project) && $this->canBeDeleted();
    
    return [
        'read' => $canRead,
        'write' => $canWrite,
        'delete' => $canDelete,
        'download' => $canRead,
        'preview' => $canRead && $this->isPreviewable(),
    ];
}

/**
 * Log file access for audit trail.
 */
public function logAccess(string $action, ?int $userId = null): void
{
    $userId = $userId ?: auth()->id();
    
    \Log::info("File {$action}", [
        'file_id' => $this->id,
        'file_name' => $this->file_name,
        'project_id' => $this->project_id,
        'user_id' => $userId,
        'action' => $action,
        'timestamp' => now()->toISOString(),
    ]);
}

/**
 * Boot method to add model event listeners.
 */
protected static function boot()
{
    parent::boot();
    
    // Log file creation
    static::created(function ($file) {
        $file->logAccess('created');
    });
    
    // Log file deletion
    static::deleting(function ($file) {
        $file->logAccess('deleted');
    });
    
    // Verify file integrity before saving
    static::saving(function ($file) {
        if ($file->isDirty('file_path') && $file->existsOnDisk()) {
            $actualSize = $file->actual_file_size;
            if ($actualSize && $actualSize !== $file->file_size) {
                \Log::warning('File size mismatch detected', [
                    'file_id' => $file->id,
                    'stored_size' => $file->file_size,
                    'actual_size' => $actualSize,
                ]);
            }
        }
    });
}
}