<?php
// File: app/Models/Testimonial.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ImageableTrait;

class Testimonial extends Model
{
    use HasFactory, ImageableTrait;

    protected $fillable = [
        'project_id',
        'client_name',
        'client_position',
        'client_company',
        'content',
        'rating',
        'is_active',
        'featured',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_active' => 'boolean',
        'featured' => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }
}