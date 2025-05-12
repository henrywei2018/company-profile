<?php
// File: app/Models/Service.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasSlugTrait;
use App\Traits\ImageableTrait;
use App\Traits\SeoableTrait;

class Service extends Model
{
    use HasFactory, HasSlugTrait, ImageableTrait, SeoableTrait;

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'description',
        'category_id',
        'icon',
        'featured',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}