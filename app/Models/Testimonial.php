<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasActiveTrait;
use App\Traits\ImageableTrait;

class Testimonial extends Model
{
    use HasFactory, HasActiveTrait, ImageableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id',
        'client_name',
        'client_position',
        'client_company',
        'content',
        'image',
        'rating',
        'is_active',
        'featured',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'rating' => 'integer',
        'is_active' => 'boolean',
        'featured' => 'boolean',
    ];
    
    /**
     * Get the project that owns the testimonial.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    
    /**
     * Scope a query to only include featured testimonials.
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }
    
    /**
     * Scope a query to only exclude featured testimonials.
     */
    public function scopeNotFeatured($query)
    {
        return $query->where('featured', false);
    }
}