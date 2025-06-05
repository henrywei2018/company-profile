<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\SeoableTrait;

class PostCategory extends Model
{
    use HasFactory, SeoableTrait;

    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    protected $appends = [
        'url',
        'posts_count',
        'published_posts_count'
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

        // Update slug when name changes (if slug wasn't manually set)
        static::updating(function ($category) {
            if ($category->isDirty('name') && !$category->isDirty('slug')) {
                $category->slug = static::generateUniqueSlug($category->name, $category->id);
            }
        });
    }

    // RELATIONSHIPS

    /**
     * Get the posts for the category (many-to-many).
     */
    public function posts()
    {
        return $this->belongsToMany(
            Post::class,
            'post_post_category',
            'post_category_id',
            'post_id'
        )->withTimestamps();
    }

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
    public function scopeWithPostsCount($query)
    {
        return $query->withCount('posts');
    }


    /**
     * Scope to order by popularity (published posts count).
     */
    public function scopePopular($query)
    {
        return $query->withCount([
                'posts as published_posts_count' => function ($q) {
                    $q->where('status', 'published')
                      ->where('published_at', '<=', now());
                }
            ])
            ->orderByDesc('published_posts_count');
    }

    /**
     * Scope categories that have posts.
     */
    public function scopeHasPosts($query)
    {
        return $query->has('posts');
    }

    /**
     * Scope categories that have published posts.
     */
    public function scopeHasPublishedPosts($query)
    {
        return $query->whereHas('posts', function($q) {
            $q->where('status', 'published')
              ->where('published_at', '<=', now());
        });
    }

    // ACCESSORS

    /**
     * Get the category URL.
     */
    public function getUrlAttribute(): string
    {
        return route('blog.category', $this->slug);
    }

    /**
     * Get posts count - optimized accessor.
     */
    public function getPostsCountAttribute(): int
    {
        // If already loaded via withCount, use that
        if (array_key_exists('posts_count', $this->attributes)) {
            return (int) $this->attributes['posts_count'];
        }
        
        // Otherwise query dynamically (cached for this request)
        if (!isset($this->cachedPostsCount)) {
            $this->cachedPostsCount = $this->posts()->count();
        }
        
        return $this->cachedPostsCount;
    }

    /**
     * Get published posts count - optimized accessor.
     */
    public function getPublishedPostsCountAttribute(): int
    {
        // If already loaded via withCount, use that
        if (array_key_exists('published_posts_count', $this->attributes)) {
            return (int) $this->attributes['published_posts_count'];
        }
        
        // Otherwise query dynamically (cached for this request)
        if (!isset($this->cachedPublishedPostsCount)) {
            $this->cachedPublishedPostsCount = $this->publishedPosts()->count();
        }
        
        return $this->cachedPublishedPostsCount;
    }

    /**
     * Get formatted description for SEO/display.
     */
    public function getFormattedDescriptionAttribute(): string
    {
        if (!empty($this->description)) {
            return $this->description;
        }
        
        return "Browse all posts in the {$this->name} category.";
    }

    /**
     * Get category summary.
     */
    public function getSummaryAttribute(): string
    {
        $postsCount = $this->posts_count;
        $publishedCount = $this->published_posts_count;
        
        if ($publishedCount === 0) {
            return "No published posts yet.";
        }
        
        if ($publishedCount === 1) {
            return "1 published post.";
        }
        
        return "{$publishedCount} published posts.";
    }

    // METHODS

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
     * Get recent posts in this category.
     */
    public function getRecentPosts(int $limit = 5)
    {
        return $this->publishedPosts()
                   ->with(['author', 'categories'])
                   ->limit($limit)
                   ->get();
    }

    /**
     * Get latest post in this category.
     */
    public function getLatestPost()
    {
        return $this->publishedPosts()->first();
    }

    /**
     * Get oldest post in this category.
     */
    public function getOldestPost()
    {
        return $this->publishedPosts()
                   ->orderBy('published_at')
                   ->first();
    }

    /**
     * Check if category has posts.
     */
    public function hasPosts(): bool
    {
        return $this->posts_count > 0;
    }

    /**
     * Check if category has published posts.
     */
    public function hasPublishedPosts(): bool
    {
        return $this->published_posts_count > 0;
    }

    /**
     * Check if category can be safely deleted.
     */
    public function canBeDeleted(): bool
    {
        return $this->posts_count === 0;
    }

    /**
     * Get deletion prevention reason.
     */
    public function getDeletionPreventionReason(): ?string
    {
        if ($this->posts_count > 0) {
            return "Category has {$this->posts_count} associated posts. Please move or delete the posts first.";
        }
        
        return null;
    }

    /**
     * Get category statistics.
     */
    public function getStatistics(): array
    {
        $totalPosts = $this->posts_count;
        $publishedPosts = $this->published_posts_count;
        $draftPosts = $this->posts()->where('status', 'draft')->count();
        $archivedPosts = $this->posts()->where('status', 'archived')->count();

        return [
            'total_posts' => $totalPosts,
            'published_posts' => $publishedPosts,
            'draft_posts' => $draftPosts,
            'archived_posts' => $archivedPosts,
            'featured_posts' => $this->posts()->where('featured', true)->count(),
            'latest_post' => $this->getLatestPost(),
            'oldest_post' => $this->getOldestPost(),
            'most_recent_activity' => $this->posts()->latest('updated_at')->first()?->updated_at,
            'creation_rate' => $this->getCreationRate(),
        ];
    }

    /**
     * Get post creation rate (posts per month average).
     */
    protected function getCreationRate(): float
    {
        if ($this->posts_count === 0) {
            return 0;
        }

        $oldestPost = $this->posts()->oldest('created_at')->first();
        if (!$oldestPost) {
            return 0;
        }

        $monthsDiff = $oldestPost->created_at->diffInMonths(now());
        return $monthsDiff > 0 ? round($this->posts_count / $monthsDiff, 2) : $this->posts_count;
    }

    /**
     * Get related categories based on shared posts.
     */
    public function getRelatedCategories(int $limit = 5)
    {
        $postIds = $this->posts()->pluck('posts.id');
        
        if ($postIds->isEmpty()) {
            return collect();
        }

        return static::whereHas('posts', function ($query) use ($postIds) {
                    $query->whereIn('posts.id', $postIds);
                })
                ->where('id', '!=', $this->id)
                ->withCount([
                    'posts as shared_posts_count' => function ($query) use ($postIds) {
                        $query->whereIn('posts.id', $postIds);
                    }
                ])
                ->orderByDesc('shared_posts_count')
                ->limit($limit)
                ->get();
    }

    /**
     * Get breadcrumb trail for category.
     */
    public function getBreadcrumbTrail(): array
    {
        return [
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Blog', 'url' => route('blog.index')],
            ['name' => 'Categories', 'url' => route('blog.categories')],
            ['name' => $this->name, 'url' => $this->url, 'active' => true],
        ];
    }

    /**
     * Get category meta data for SEO.
     */
    public function getMetaData(): array
    {
        $seo = $this->getSeoData();
        
        return [
            'title' => $seo->title ?: "{$this->name} - Blog Category",
            'description' => $seo->description ?: $this->formatted_description,
            'keywords' => $seo->keywords ?: $this->name,
            'canonical_url' => $this->url,
            'og_title' => $seo->title ?: $this->name,
            'og_description' => $seo->description ?: $this->formatted_description,
            'og_url' => $this->url,
            'og_type' => 'website',
        ];
    }

    /**
     * Export category data.
     */
    public function export(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'posts_count' => $this->posts_count,
            'published_posts_count' => $this->published_posts_count,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'url' => $this->url,
            'statistics' => $this->getStatistics(),
            'seo' => $this->seo ? [
                'title' => $this->seo->title,
                'description' => $this->seo->description,
                'keywords' => $this->seo->keywords,
            ] : null,
        ];
    }

    /**
     * Create category from import data.
     */
    public static function createFromImport(array $data): self
    {
        return static::create([
            'name' => $data['name'],
            'slug' => $data['slug'] ?? static::generateUniqueSlug($data['name']),
            'description' => $data['description'] ?? null,
        ]);
    }

    /**
     * Get posts by status.
     */
    public function getPostsByStatus(string $status)
    {
        return $this->posts()->where('status', $status)->get();
    }

    /**
     * Get featured posts in this category.
     */
    public function getFeaturedPosts(int $limit = 3)
    {
        return $this->publishedPosts()
                   ->where('featured', true)
                   ->limit($limit)
                   ->get();
    }

    /**
     * Get posts for sitemap.
     */
    public function getPostsForSitemap()
    {
        return $this->publishedPosts()
                   ->select(['id', 'slug', 'updated_at'])
                   ->get();
    }

    /**
     * Get posts for RSS feed.
     */
    public function getPostsForRss(int $limit = 20)
    {
        return $this->publishedPosts()
                   ->with('author')
                   ->limit($limit)
                   ->get();
    }

    /**
     * Get monthly post counts for analytics.
     */
    public function getMonthlyPostCounts(int $months = 12): array
    {
        $result = [];
        $startDate = now()->subMonths($months);

        for ($i = 0; $i < $months; $i++) {
            $date = $startDate->copy()->addMonths($i);
            $count = $this->posts()
                         ->whereYear('published_at', $date->year)
                         ->whereMonth('published_at', $date->month)
                         ->where('status', 'published')
                         ->count();
            
            $result[] = [
                'month' => $date->format('Y-m'),
                'month_name' => $date->format('M Y'),
                'count' => $count,
            ];
        }

        return $result;
    }
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%")
              ->orWhere('slug', 'like', "%{$term}%");
        });
    }

    /**
     * Search posts within this category.
     */
    public function searchPosts(string $query, int $limit = 10)
    {
        return $this->publishedPosts()
                   ->where(function ($q) use ($query) {
                       $q->where('title', 'like', "%{$query}%")
                         ->orWhere('excerpt', 'like', "%{$query}%")
                         ->orWhere('content', 'like', "%{$query}%");
                   })
                   ->limit($limit)
                   ->get();
    }

    /**
     * Cache posts count to avoid repeated queries.
     */
    protected $cachedPostsCount;
    protected $cachedPublishedPostsCount;
}