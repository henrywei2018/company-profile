<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Traits\SeoableTrait;

class Post extends Model
{
    use HasFactory,  SeoableTrait;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'user_id',
        'featured_image',
        'status',
        'published_at',
        'featured'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'featured' => 'boolean',
    ];

    protected $appends = [
        'featured_image_url',
        'reading_time',
        'excerpt_or_content'
    ];

    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug when creating
        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = static::generateUniqueSlug($post->title);
            }
        });

        // Clean up files when deleting
        static::deleting(function ($post) {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
                
                // Delete thumbnail too
                $thumbnailPath = 'posts/thumbnails/' . basename($post->featured_image);
                if (Storage::disk('public')->exists($thumbnailPath)) {
                    Storage::disk('public')->delete($thumbnailPath);
                }
            }
        });
    }

    // RELATIONSHIPS

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
        return $this->belongsToMany(
            PostCategory::class,
            'post_post_category',
            'post_id',
            'post_category_id'
        );
    }

    // SCOPES

    /**
     * Scope to get published posts.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where('published_at', '<=', now());
    }

    /**
     * Scope to get featured posts.
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->whereHas('categories', function ($q) use ($categoryId) {
            $q->where('post_categories.id', $categoryId);
        });
    }

    /**
     * Scope to search posts.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('excerpt', 'like', "%{$search}%")
                ->orWhere('content', 'like', "%{$search}%");
        });
    }

    // ACCESSORS

    /**
     * Get the featured image URL.
     */
    public function getFeaturedImageUrlAttribute(): ?string
    {
        if ($this->featured_image && Storage::disk('public')->exists($this->featured_image)) {
            return Storage::disk('public')->url($this->featured_image);
        }
        
        return null;
    }

    /**
     * Get the reading time in minutes.
     */
    public function getReadingTimeAttribute(): int
    {
        $wordCount = str_word_count(strip_tags($this->content));
        $wordsPerMinute = 200;
        return max(1, ceil($wordCount / $wordsPerMinute));
    }

    /**
     * Get excerpt or truncated content.
     */
    public function getExcerptOrContentAttribute(): string
    {
        if ($this->excerpt) {
            return $this->excerpt;
        }
        
        return Str::limit(strip_tags($this->content), 160);
    }

    /**
     * Get the post's URL.
     */
    public function getUrlAttribute(): string
    {
        return route('posts.show', $this->slug);
    }

    // METHODS

    /**
     * Publish the post.
     */
    public function publish(): void
    {
        $this->update([
            'status' => 'published',
            'published_at' => $this->published_at ?: now()
        ]);
    }

    /**
     * Unpublish the post.
     */
    public function unpublish(): void
    {
        $this->update([
            'status' => 'draft',
            'published_at' => null
        ]);
    }

    /**
     * Archive the post.
     */
    public function archive(): void
    {
        $this->update(['status' => 'archived']);
    }

    /**
     * Toggle featured status.
     */
    public function toggleFeatured(): void
    {
        $this->update(['featured' => !$this->featured]);
    }

    /**
     * Check if post is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published' && 
               $this->published_at && 
               $this->published_at <= now();
    }

    /**
     * Check if post is draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if post is archived.
     */
    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    /**
     * Get next published post.
     */
    public function getNextPost()
    {
        return static::published()
                    ->where('published_at', '>', $this->published_at)
                    ->orderBy('published_at')
                    ->first();
    }

    /**
     * Get previous published post.
     */
    public function getPreviousPost()
    {
        return static::published()
                    ->where('published_at', '<', $this->published_at)
                    ->orderByDesc('published_at')
                    ->first();
    }

    /**
     * Get related posts based on categories.
     */
    public function getRelatedPosts($limit = 3)
    {
        if ($this->categories->isEmpty()) {
            return collect();
        }

        return static::published()
                    ->where('id', '!=', $this->id)
                    ->whereHas('categories', function ($query) {
                        $query->whereIn('post_categories.id', $this->categories->pluck('id'));
                    })
                    ->limit($limit)
                    ->get();
    }

    /**
     * Generate unique slug.
     */
    public static function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
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
     * Get thumbnail URL.
     */
    public function getThumbnailUrl(): ?string
    {
        if (!$this->featured_image) {
            return null;
        }

        $thumbnailPath = 'posts/thumbnails/' . basename($this->featured_image);
        
        if (Storage::disk('public')->exists($thumbnailPath)) {
            return Storage::disk('public')->url($thumbnailPath);
        }
        
        return $this->featured_image_url;
    }

    /**
     * Get post status badge color.
     */
    public function getStatusBadgeColor(): string
    {
        return match ($this->status) {
            'published' => 'success',
            'draft' => 'warning',
            'archived' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get formatted published date.
     */
    public function getFormattedPublishedDate(): ?string
    {
        if (!$this->published_at) {
            return null;
        }
        
        return $this->published_at->format('M d, Y');
    }

    /**
     * Get post content without HTML tags.
     */
    public function getPlainContent(): string
    {
        return strip_tags($this->content);
    }

    /**
     * Get word count.
     */
    public function getWordCount(): int
    {
        return str_word_count($this->getPlainContent());
    }

    /**
     * Get character count.
     */
    public function getCharacterCount(): int
    {
        return strlen($this->getPlainContent());
    }

    /**
     * Check if post has featured image.
     */
    public function hasFeaturedImage(): bool
    {
        return !empty($this->featured_image) && 
               Storage::disk('public')->exists($this->featured_image);
    }

    /**
     * Get category names as comma-separated string.
     */
    public function getCategoryNames(): string
    {
        return $this->categories->pluck('name')->join(', ');
    }

    /**
     * Get category slugs as array.
     */
    public function getCategorySlugs(): array
    {
        return $this->categories->pluck('slug')->toArray();
    }

    /**
     * Duplicate post.
     */
    public function duplicate(array $overrides = []): self
    {
        $newPost = $this->replicate();
        
        // Default overrides
        $defaults = [
            'title' => $this->title . ' (Copy)',
            'slug' => static::generateUniqueSlug($this->title . ' Copy'),
            'status' => 'draft',
            'published_at' => null,
            'featured' => false,
            'user_id' => auth()->id() ?: $this->user_id,
        ];
        
        $newPost->fill(array_merge($defaults, $overrides));
        $newPost->save();
        
        // Duplicate categories
        $newPost->categories()->attach($this->categories->pluck('id'));
        
        // Duplicate SEO data if exists
        if ($this->seo) {
            $newPost->updateSeo([
                'title' => $this->seo->title,
                'description' => $this->seo->description,
                'keywords' => $this->seo->keywords,
            ]);
        }
        
        return $newPost;
    }
}