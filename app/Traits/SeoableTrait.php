<?php
// File: app/Traits/SeoableTrait.php

namespace App\Traits;

use App\Models\Seo;

trait SeoableTrait
{
    /**
     * Boot the trait.
     */
    protected static function bootSeoableTrait()
    {
        static::deleting(function ($model) {
            // Clean up associated SEO data when model is deleted
            $model->seo()->delete();
        });
    }

    /**
     * Get the SEO relationship.
     */
    public function seo()
    {
        return $this->morphOne(Seo::class, 'seoable');
    }

    /**
     * Get or create SEO data.
     */
    public function getSeoData()
    {
        if (!$this->seo) {
            $this->seo()->create();
            $this->load('seo');
        }
        
        return $this->seo;
    }

    /**
     * Update SEO data.
     */
    public function updateSeo(array $data)
    {
        // Get or create SEO record
        $seo = $this->getSeoData();
        
        // Update SEO data
        $seo->update($data);
        
        return $seo;
    }

    /**
     * Get SEO title.
     */
    public function getSeoTitleAttribute()
    {
        return $this->seo->title ?? $this->title ?? config('app.name');
    }

    /**
     * Get SEO description.
     */
    public function getSeoDescriptionAttribute()
    {
        if ($this->seo && $this->seo->description) {
            return $this->seo->description;
        }
        
        // Use excerpt if available
        if (isset($this->excerpt) && !empty($this->excerpt)) {
            return substr(strip_tags($this->excerpt), 0, 160);
        }
        
        // Use description if available
        if (isset($this->description) && !empty($this->description)) {
            return substr(strip_tags($this->description), 0, 160);
        }
        
        // Use short_description if available
        if (isset($this->short_description) && !empty($this->short_description)) {
            return substr(strip_tags($this->short_description), 0, 160);
        }
        
        // Fallback to content
        if (isset($this->content) && !empty($this->content)) {
            return substr(strip_tags($this->content), 0, 160);
        }
        
        return config('app.description', '');
    }

    /**
     * Get SEO keywords.
     */
    public function getSeoKeywordsAttribute()
    {
        return $this->seo->keywords ?? '';
    }

    /**
     * Get Open Graph image.
     */
    public function getOgImageAttribute()
    {
        if ($this->seo && $this->seo->og_image) {
            return asset('storage/' . $this->seo->og_image);
        }
        
        // Try to get image from model
        if (method_exists($this, 'getImageUrl')) {
            return $this->getImageUrl();
        }
        
        if (isset($this->featured_image) && !empty($this->featured_image)) {
            return asset('storage/' . $this->featured_image);
        }
        
        if (isset($this->image) && !empty($this->image)) {
            return asset('storage/' . $this->image);
        }
        
        return asset('images/og-default.jpg');
    }
}