<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use App\Traits\SeoableTrait;
use Carbon\Carbon;

class Post extends Model
{
    use HasFactory, SeoableTrait;

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

    protected $appends = [
        'featured_image_url',
        'thumbnail_url',
        'status_badge',
        'reading_time',
        'is_published'
    ];

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';
    const STATUS_ARCHIVED = 'archived';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_ARCHIVED => 'Archived',
        ];
    }

    /**
     * Relationships
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function categories()
    {
        return $this->belongsToMany(PostCategory::class, 'post_post_category');
    }

    /**
     * Scopes
     */
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED)
                    ->where('published_at', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->whereHas('categories', function ($q) use ($categoryId) {
            $q->where('post_categories.id', $categoryId);
        });
    }

    public function scopeSearch($query, $search)
    {
        if (!empty($search)) {
            return $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    public function scopeRecentlyPublished($query, $days = 30)
    {
        return $query->published()
                    ->where('published_at', '>=', now()->subDays($days));
    }

    /**
     * Accessors & Mutators
     */
    public function getFeaturedImageUrlAttribute(): ?string
    {
        if (!$this->featured_image) {
            return null;
        }

        if (Storage::disk('public')->exists($this->featured_image)) {
            return asset('storage/' . $this->featured_image);
        }

        return null;
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->featured_image) {
            return null;
        }

        // Try to get thumbnail version
        $thumbnailPath = 'posts/thumbnails/' . basename($this->featured_image);
        
        if (Storage::disk('public')->exists($thumbnailPath)) {
            return asset('storage/' . $thumbnailPath);
        }

        // Fallback to featured image
        return $this->featured_image_url;
    }

    public function getStatusBadgeAttribute(): array
    {
        $badges = [
            self::STATUS_DRAFT => ['color' => 'warning', 'text' => 'Draft'],
            self::STATUS_PUBLISHED => ['color' => 'success', 'text' => 'Published'],
            self::STATUS_ARCHIVED => ['color' => 'danger', 'text' => 'Archived'],
        ];

        return $badges[$this->status] ?? ['color' => 'secondary', 'text' => 'Unknown'];
    }

    public function getReadingTimeAttribute(): int
    {
        $wordCount = str_word_count(strip_tags($this->content));
        return max(1, ceil($wordCount / 200)); // Average reading speed: 200 words per minute
    }

    public function getIsPublishedAttribute(): bool
    {
        return $this->status === self::STATUS_PUBLISHED && 
               $this->published_at && 
               $this->published_at->isPast();
    }

    public function getExcerptAttribute($value): string
    {
        if (!empty($value)) {
            return $value;
        }

        // Auto-generate excerpt from content if not provided
        $content = strip_tags($this->content);
        return \Illuminate\Support\Str::limit($content, 160);
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug from title if not provided
        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = \Illuminate\Support\Str::slug($post->title);
            }
        });

        static::updating(function ($post) {
            if ($post->isDirty('title') && empty($post->slug)) {
                $post->slug = \Illuminate\Support\Str::slug($post->title);
            }
        });

        // Auto-set published_at when status changes to published
        static::saving(function ($post) {
            if ($post->status === self::STATUS_PUBLISHED && !$post->published_at) {
                $post->published_at = now();
            }
        });

        // Clean up images on delete
        static::deleting(function ($post) {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
                
                // Also delete thumbnail
                $thumbnailPath = 'posts/thumbnails/' . basename($post->featured_image);
                if (Storage::disk('public')->exists($thumbnailPath)) {
                    Storage::disk('public')->delete($thumbnailPath);
                }
            }
        });
    }

    /**
     * Helper methods
     */
    public function toggleFeatured(): bool
    {
        $this->featured = !$this->featured;
        return $this->save();
    }

    public function publish(): bool
    {
        $this->status = self::STATUS_PUBLISHED;
        if (!$this->published_at) {
            $this->published_at = now();
        }
        return $this->save();
    }

    public function unpublish(): bool
    {
        $this->status = self::STATUS_DRAFT;
        return $this->save();
    }

    public function archive(): bool
    {
        $this->status = self::STATUS_ARCHIVED;
        return $this->save();
    }

    public function canEdit(User $user = null): bool
    {
        $user = $user ?? auth()->user();
        
        if (!$user) {
            return false;
        }

        // Admin can edit any post
        if ($user->hasRole(['admin', 'super-admin'])) {
            return true;
        }

        // Author can edit their own post
        return $this->user_id === $user->id;
    }

    public function canDelete(User $user = null): bool
    {
        $user = $user ?? auth()->user();
        
        if (!$user) {
            return false;
        }

        // Only admin can delete posts
        return $user->hasRole(['admin', 'super-admin']);
    }

    /**
     * Get posts for sitemap
     */
    public static function forSitemap()
    {
        return static::published()
                    ->select(['slug', 'updated_at'])
                    ->orderByDesc('updated_at')
                    ->get();
    }

    /**
     * Get related posts
     */
    public function getRelatedPosts($limit = 3)
    {
        $categoryIds = $this->categories->pluck('id');
        
        return static::published()
                    ->where('id', '!=', $this->id)
                    ->whereHas('categories', function ($query) use ($categoryIds) {
                        $query->whereIn('post_categories.id', $categoryIds);
                    })
                    ->inRandomOrder()
                    ->limit($limit)
                    ->get();
    }

    /**
     * Get posts by year/month for archives
     */
    public static function getArchives()
    {
        return static::published()
                    ->selectRaw('YEAR(published_at) as year, MONTH(published_at) as month, COUNT(*) as count')
                    ->groupByRaw('YEAR(published_at), MONTH(published_at)')
                    ->orderByRaw('YEAR(published_at) DESC, MONTH(published_at) DESC')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'year' => $item->year,
                            'month' => $item->month,
                            'month_name' => Carbon::create($item->year, $item->month)->format('F'),
                            'count' => $item->count,
                            'url' => route('blog.archive', ['year' => $item->year, 'month' => $item->month])
                        ];
                    });
    }

    /**
     * Search posts
     */
    public static function searchPosts($query, $filters = [])
    {
        $posts = static::with(['author', 'categories']);

        // Search in title, excerpt, content
        if (!empty($query)) {
            $posts->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('excerpt', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            });
        }

        // Filter by category
        if (!empty($filters['category'])) {
            $posts->byCategory($filters['category']);
        }

        // Filter by status
        if (!empty($filters['status'])) {
            $posts->byStatus($filters['status']);
        } else {
            // Default to published for public search
            $posts->published();
        }

        // Filter by date range
        if (!empty($filters['from_date'])) {
            $posts->where('published_at', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $posts->where('published_at', '<=', $filters['to_date']);
        }

        return $posts->latest('published_at');
    }
}