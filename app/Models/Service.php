<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterableTrait;
use App\Traits\HasActiveTrait;
use App\Traits\HasSlugTrait;
use App\Traits\HasSortOrderTrait;
use App\Traits\SeoableTrait;
use App\Traits\ImageableTrait;

class Service extends Model
{
    use HasFactory, FilterableTrait, HasActiveTrait, HasSlugTrait, HasSortOrderTrait, SeoableTrait, ImageableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'category_id',
        'short_description',
        'description',
        'icon',
        'image',
        'featured',
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
        'category_id',
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
        'title',
        'short_description',
        'description',
    ];
    
    /**
     * Get the category that owns the service.
     */
    public function category()
    {
        return $this->belongsTo(ServiceCategory::class);
    }
    
    /**
     * Get the quotations for the service.
     */
    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }
    
    /**
     * Scope a query to only include featured services.
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }
    
    /**
     * Get icon URL.
     */
    public function getIconUrlAttribute()
    {
        if ($this->icon) {
            return asset('storage/' . $this->icon);
        }
        
        return asset('images/default-service-icon.png');
    }
    
    /**
     * Get image URL.
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        
        return asset('images/default-service-image.jpg');
    }
}