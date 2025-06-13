<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Traits\FilterableTrait;
use App\Traits\HasActiveTrait;
use App\Traits\HasSlugTrait;
use App\Traits\HasSortOrderTrait;
use App\Traits\SeoableTrait;
use App\Traits\ImageableTrait;

class TeamMember extends Model
{
    use HasFactory, FilterableTrait, HasActiveTrait, HasSlugTrait, HasSortOrderTrait, SeoableTrait, ImageableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'position',
        'department_id',
        'bio',
        'email',
        'phone',
        'photo', // Changed from 'image' to 'photo' for consistency
        'linkedin',
        'twitter', // Added these social media fields
        'facebook',
        'instagram',
        'featured', // Changed from 'is_featured' to 'featured' for consistency
        'is_active',
        'sort_order',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'featured' => 'boolean',
        'is_active' => 'boolean',
    ];
    
    /**
     * The filterable attributes for the model.
     *
     * @var array
     */
    protected $filterable = [
        'department_id',
        'featured',
        'is_active',
        'search',
    ];
    
    /**
     * The searchable attributes for the model.
     *
     * @var array
     */
    protected $searchable = [
        'name',
        'position',
        'department_id',
        'bio',
    ];
    
    /**
     * Scope a query to only include featured team members.
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /**
     * Scope a query to order team members by sort order and name.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    /**
     * Get the department that the team member belongs to.
     */
    public function department()
    {
        return $this->belongsTo(TeamMemberDepartment::class, 'department_id');
    }
    
    /**
     * Get photo URL accessor.
     */
    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return Storage::disk('public')->url($this->photo);
        }
        
        return asset('images/default-profile.png');
    }

    /**
     * Check if the team member has a photo.
     */
    public function hasPhoto()
    {
        return !empty($this->photo) && Storage::disk('public')->exists($this->photo);
    }

    /**
     * Get photo file size.
     */
    public function getPhotoFileSize()
    {
        if ($this->hasPhoto()) {
            $size = Storage::disk('public')->size($this->photo);
            return $this->formatFileSize($size);
        }
        
        return 'N/A';
    }

    /**
     * Get photo dimensions.
     */
    public function getPhotoDimensions()
    {
        if ($this->hasPhoto()) {
            $path = Storage::disk('public')->path($this->photo);
            
            if (file_exists($path)) {
                $imageInfo = getimagesize($path);
                if ($imageInfo) {
                    return [
                        'width' => $imageInfo[0],
                        'height' => $imageInfo[1]
                    ];
                }
            }
        }
        
        return null;
    }

    /**
     * Check if team member has social links.
     */
    public function hasSocialLinks()
    {
        return $this->facebook || $this->twitter || $this->linkedin || $this->instagram;
    }
    
    /**
     * Get active social links.
     */
    public function getSocialLinksAttribute()
    {
        $links = [];
        
        if ($this->facebook) {
            $links['facebook'] = $this->facebook;
        }
        
        if ($this->twitter) {
            $links['twitter'] = $this->twitter;
        }
        
        if ($this->linkedin) {
            $links['linkedin'] = $this->linkedin;
        }
        
        if ($this->instagram) {
            $links['instagram'] = $this->instagram;
        }
        
        return $links;
    }

    /**
     * Format file size in human readable format
     */
    protected function formatFileSize(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug when creating
        static::creating(function ($teamMember) {
            if (empty($teamMember->slug)) {
                $teamMember->slug = \Illuminate\Support\Str::slug($teamMember->name);
            }
        });

        // Auto-generate slug when updating name
        static::updating(function ($teamMember) {
            if ($teamMember->isDirty('name') && empty($teamMember->slug)) {
                $teamMember->slug = \Illuminate\Support\Str::slug($teamMember->name);
            }
        });

        // Delete photo when deleting team member
        static::deleting(function ($teamMember) {
            if ($teamMember->photo) {
                Storage::disk('public')->delete($teamMember->photo);
            }
        });
    }
}