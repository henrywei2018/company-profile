<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterableTrait;
use App\Traits\HasSlugTrait;
use App\Traits\SeoableTrait;
use App\Traits\ImageableTrait;

class Post extends Model
{
    use HasFactory, FilterableTrait, HasSlugTrait, SeoableTrait, ImageableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
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
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'published_at' => 'datetime',
        'featured' => 'boolean',
    ];
    
    /**
     * The filterable attributes for the model.
     *
     * @var array
     */
    protected $filterable = [
        'status',
        'search',
        'featured',
    ];
    
    /**
     * The searchable attributes for the model.
     *
     * @var array
     */
    protected $searchable = [
        'title',
        'excerpt',
        'content',
    ];
    
    /**
     * Get the author of the post.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Get the categories for the post.
     */
    public function categories()
    {
        return $this->belongsToMany(PostCategory::class);
    }
    
    /**
     * Scope a query to only include published posts.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }
    
    /**
     * Scope a query to only include recent posts.
     */
    public function scopeRecent($query, $limit = 5)
    {
        return $query->latest('published_at')->limit($limit);
    }
}