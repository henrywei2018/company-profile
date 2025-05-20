<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterableTrait;
use App\Traits\HasSlugTrait;
use App\Traits\SeoableTrait;

class Project extends Model
{
    use HasFactory, FilterableTrait, HasSlugTrait, SeoableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'description',
        'category',
        'client_id',
        'location',
        'client_name',
        'year',
        'status',
        'value',
        'featured',
        'start_date',
        'end_date',
        'challenge',
        'solution',
        'result',
        'services_used',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'featured' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'services_used' => 'array',
    ];
    
    /**
     * The filterable attributes for the model.
     *
     * @var array
     */
    protected $filterable = [
        'category',
        'year',
        'status',
        'search',
        'featured',
    ];
    
    /**
     * The searchable attributes for the model.
     *
     * @var array
     */
    protected $searchable = [
        'title',
        'description',
        'location',
        'client_name',
    ];
    
    /**
     * Get the images for the project.
     */
    public function images()
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order');
    }

    public function category()
    {
        return $this->belongsTo(ProjectCategory::class);
    }
    
    /**
     * Get the client associated with the project.
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
    
    /**
     * Get the testimonial associated with the project.
     */
    public function testimonial()
    {
        return $this->hasOne(Testimonial::class);
    }
    
    /**
     * Get the project files.
     */
    public function files()
    {
        return $this->hasMany(ProjectFile::class);
    }
    
    /**
     * Get the project milestones.
     */
    public function milestones()
    {
        return $this->hasMany(ProjectMilestone::class);
    }
    
    /**
     * Get the project messages.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
    
    /**
     * Scope a query to only include featured projects.
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }
    
    /**
     * Scope a query to only include completed projects.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
    
    /**
     * Get the first image or a default one.
     */
    public function getFeaturedImageAttribute()
    {
        $featuredImage = $this->images()->where('is_featured', true)->first();
        
        if (!$featuredImage) {
            $featuredImage = $this->images()->first();
        }
        
        return $featuredImage ? $featuredImage->image_path : 'images/default-project.jpg';
    }
    
    /**
     * Get featured image URL.
     */
    public function getFeaturedImageUrlAttribute()
    {
        $featuredImage = $this->getFeaturedImageAttribute();
        
        if ($featuredImage && strpos($featuredImage, 'images/default') === false) {
            return asset('storage/' . $featuredImage);
        }
        
        return asset($featuredImage);
    }
}