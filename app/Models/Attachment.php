<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'download_count',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'file_size' => 'integer',
        'download_count' => 'integer',
    ];
    
    /**
     * Get the parent attachable model.
     */
    public function attachable()
    {
        return $this->morphTo();
    }
    
    /**
     * Get formatted file size.
     */
    public function getFormattedFileSizeAttribute()
    {
        $size = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $size > 1024; $i++) {
            $size /= 1024;
        }
        
        return round($size, 2) . ' ' . $units[$i];
    }
    
    /**
     * Get file icon based on type.
     */
    public function getFileIconAttribute()
    {
        $type = $this->file_type;
        $icons = [
            'application/pdf' => 'pdf',
            'application/msword' => 'word',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'word',
            'application/vnd.ms-excel' => 'excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'excel',
            'application/vnd.ms-powerpoint' => 'powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'powerpoint',
            'image/jpeg' => 'image',
            'image/png' => 'image',
            'image/gif' => 'image',
            'image/svg+xml' => 'image',
            'text/plain' => 'text',
            'text/csv' => 'excel',
            'application/zip' => 'archive',
            'application/x-rar-compressed' => 'archive',
        ];
        
        return $icons[$type] ?? 'file';
    }
    
    /**
     * Get file extension.
     */
    public function getFileExtensionAttribute()
    {
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }
}