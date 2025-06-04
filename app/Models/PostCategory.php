<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\SeoableTrait;

class PostCategory extends Model
{
    use HasFactory, SeoableTrait;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    protected $appends = [
        'posts_count',
        'published_posts_count'
    ];

    /**
     * Relationships
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_post_category');
    }

    public function publishedPosts()
    {
        return $this->posts()->published();
    }

    /**
     * Scopes
     */
    public function scopeWithPostsCount($query)
    {
        return $query->withCount(['posts', 'publishedPosts']);
    }

    public function scopeHasPosts($query)
    {
        return $query->whereHas('posts');
    }

    public function scopeSearch($query, $search)
    {
        if (!empty($search)) {
            return $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    /**
     * Accessors
     */
    public function getPostsCountAttribute(): int
    {
        return $this->posts()->count();
    }

    public function getPublishedPostsCountAttribute(): int
    {
        return $this->publishedPosts()->count();
    }

    public function getUrlAttribute(): string
    {
        return route('blog.category', $this->slug);
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug from name if not provided
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = \Illuminate\Support\Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = \Illuminate\Support\Str::slug($category->name);
            }
        });
    }

    /**
     * Helper methods
     */
    public function canDelete(): bool
    {
        return $this->posts_count === 0;
    }

    /**
     * Get categories for sitemap
     */
    public static function forSitemap()
    {
        return static::has('publishedPosts')
                    ->select(['slug', 'updated_at'])
                    ->orderBy('name')
                    ->get();
    }

    /**
     * Get popular categories
     */
    public static function popular($limit = 10)
    {
        return static::withCount('publishedPosts')
                    ->having('published_posts_count', '>', 0)
                    ->orderByDesc('published_posts_count')
                    ->limit($limit)
                    ->get();
    }
}