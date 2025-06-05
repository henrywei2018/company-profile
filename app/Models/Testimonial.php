<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'client_id',
        'name',
        'email',
        'company',
        'position',
        'content',
        'rating',
        'featured',
        'is_active',
        'approved_at',
        'photo',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'is_active' => 'boolean',
        'rating' => 'integer',
        'approved_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_at');
    }
}