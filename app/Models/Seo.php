<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seo extends Model
{
    use HasFactory;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'seo';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'keywords',
        'seoable_id',
        'seoable_type',
        'og_image',
    ];
    
    /**
     * Get the parent seoable model.
     */
    public function seoable()
    {
        return $this->morphTo();
    }
    
    /**
     * Get the meta title.
     */
    public function getMetaTitleAttribute()
    {
        if ($this->title) {
            return $this->title;
        }
        
        if ($this->seoable && method_exists($this->seoable, 'getTitle')) {
            return $this->seoable->getTitle();
        }
        
        if ($this->seoable && isset($this->seoable->title)) {
            return $this->seoable->title;
        }
        
        return config('app.name');
    }
    
    /**
     * Get the meta description.
     */
    public function getMetaDescriptionAttribute()
    {
        if ($this->description) {
            return $this->description;
        }
        
        if ($this->seoable && method_exists($this->seoable, 'getDescription')) {
            return $this->seoable->getDescription();
        }
        
        if ($this->seoable) {
            // Try to get description from different possible attributes
            foreach (['short_description', 'excerpt', 'description', 'content'] as $field) {
                if (isset($this->seoable->$field)) {
                    $text = strip_tags($this->seoable->$field);
                    return strlen($text) > 160 ? substr($text, 0, 157) . '...' : $text;
                }
            }
        }
        
        return config('app.description', '');
    }
    
    /**
     * Get the OG image URL.
     */
    public function getOgImageUrlAttribute()
    {
        if ($this->og_image) {
            return asset('storage/' . $this->og_image);
        }
        
        if ($this->seoable && method_exists($this->seoable, 'getImageUrl')) {
            return $this->seoable->getImageUrl();
        }
        
        if ($this->seoable) {
            // Try to get image from different possible attributes
            foreach (['featured_image', 'image', 'thumbnail'] as $field) {
                if (isset($this->seoable->$field)) {
                    return asset('storage/' . $this->seoable->$field);
                }
            }
        }
        
        return asset('images/og-default.jpg');
    }
}