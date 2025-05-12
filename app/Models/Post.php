<?php
// File: app/Models/Post.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasSlugTrait;
use App\Traits\ImageableTrait;
use App\Traits\SeoableTrait;

class Post extends Model
{
    use HasFactory, HasSlugTrait, ImageableTrait, SeoableTrait;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'user_id',
        'featured_image',
        'status',
        'published_at',
        'featured',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'featured' => 'boolean',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function categories()
    {
        return $this->belongsToMany(PostCategory::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where('published_at', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeRecent($query, $limit = 5)
    {
        return $query->orderBy('published_at', 'desc')->limit($limit);
    }

    public function getReadTimeAttribute()
    {
        $wordCount = str_word_count(strip_tags($this->content));
        $readTime = ceil($wordCount / 200); // Assuming 200 words per minute
        return $readTime > 0 ? $readTime : 1;
    }
}