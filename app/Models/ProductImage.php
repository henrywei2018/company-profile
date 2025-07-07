<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasSortOrderTrait;

class ProductImage extends Model
{
    use HasFactory, HasSortOrderTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'image_path',
        'alt_text',
        'is_featured',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_featured' => 'boolean',
    ];

    /**
     * Get the product that owns the image.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope a query to only include featured images.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get the full image URL.
     */
    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }

    /**
     * Get the alt text or generate from product name.
     */
    public function getAltTextAttribute($value)
    {
        if ($value) {
            return $value;
        }
        
        return $this->product ? $this->product->name : 'Product Image';
    }

    /**
     * Set as featured image (and unset others for the same product).
     */
    public function setAsFeatured()
    {
        // First, unset all other images for this product
        static::where('product_id', $this->product_id)
              ->where('id', '!=', $this->id)
              ->update(['is_featured' => false]);
        
        // Then set this one as featured
        $this->update(['is_featured' => true]);
        
        return $this;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // When creating a new image, if it's the first image for the product, make it featured
        static::created(function ($image) {
            if ($image->product) {
                $imageCount = $image->product->images()->count();
                if ($imageCount === 1) {
                    $image->update(['is_featured' => true]);
                }
            }
        });
        
        // When deleting an image, if it was featured, make another image featured
        static::deleting(function ($image) {
            if ($image->is_featured && $image->product) {
                $nextImage = $image->product->images()
                                  ->where('id', '!=', $image->id)
                                  ->ordered()
                                  ->first();
                
                if ($nextImage) {
                    $nextImage->update(['is_featured' => true]);
                }
            }
        });
    }
}