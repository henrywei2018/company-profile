<?php
// File: app/Models/Project.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasSlugTrait;
use App\Traits\ImageableTrait;
use App\Traits\SeoableTrait;
use App\Traits\FilterableTrait;

class Project extends Model
{
    use HasFactory, HasSlugTrait, ImageableTrait, SeoableTrait, FilterableTrait;

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

    protected $casts = [
        'featured' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'services_used' => 'array',
    ];

    protected $filterable = [
        'category',
        'year',
        'status',
        'featured',
    ];

    protected $with = ['images'];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function testimonial()
    {
        return $this->hasOne(Testimonial::class);
    }

    public function images()
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order');
    }

    public function getFeaturedImageAttribute()
    {
        return $this->images()->where('is_featured', true)->first() 
            ?? $this->images()->first();
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}