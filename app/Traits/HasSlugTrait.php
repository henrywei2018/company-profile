<?php
// File: app/Traits/HasSlugTrait.php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSlugTrait
{
    /**
     * Boot the trait.
     */
    protected static function bootHasSlugTrait()
    {
        static::creating(function ($model) {
            if (!$model->slug) {
                $model->slug = $model->generateSlug();
            }
        });

        static::updating(function ($model) {
            // Generate slug only if title was changed
            if ($model->isDirty('title') && !$model->isDirty('slug')) {
                $model->slug = $model->generateSlug();
            }
        });
    }

    /**
     * Generate a slug from the title.
     */
    protected function generateSlug(): string
    {
        $slug = Str::slug($this->title);
        
        // Check if the slug already exists
        $count = static::where('slug', $slug)
            ->where('id', '!=', $this->id ?? 0)
            ->count();
        
        // If we have a duplicate, append a number
        if ($count > 0) {
            $slug = $slug . '-' . ($count + 1);
        }
        
        return $slug;
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}