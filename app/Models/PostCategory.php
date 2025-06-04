<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PostCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug when creating
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = static::generateUniqueSlug($category->name);
            }
        });
    }

    // RELATIONSHIPS

    /**
     * Get the posts for the category.
     */
    public function posts()
    {
        return $this->belongsToMany(
            Post::class,
            'post_post_category',
            'post_category_id',
            'post_id'
        );
    }

    // SCOPES

    /**
     * Scope to include posts count.
     */
    public function scopeWithPostsCount($query)
    {
        return $query->withCount('posts');
    }

    /**
     * Scope to include published posts count.
     */
    public function scopeWithPublishedPostsCount($query)
    {
        return $query->withCount([
            'posts as published_posts_count' => function ($q) {
                $q->where('status', 'published')
                  ->where('published_at', '<=', now());
            }
        ]);
    }

    // ACCESSORS

    /**
     * Get the category URL.
     */
    public function getUrlAttribute(): string
    {
        return route('categories.show', $this->slug);
    }

    // METHODS

    /**
     * Get published posts for this category.
     */
    public function publishedPosts()
    {
        return $this->posts()
                   ->where('status', 'published')
                   ->where('published_at', '<=', now())
                   ->orderByDesc('published_at');
    }

    /**
     * Generate unique slug.
     */
    public static function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (true) {
            $query = static::where('slug', $slug);
            
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            
            if (!$query->exists()) {
                break;
            }
            
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Get posts count.
     */
    public function getPostsCount(): int
    {
        return $this->posts()->count();
    }

    /**
     * Get published posts count.
     */
    public function getPublishedPostsCount(): int
    {
        return $this->publishedPosts()->count();
    }
}