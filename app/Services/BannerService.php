<?php

namespace App\Services;

use App\Models\Banner;
use App\Models\BannerCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BannerService
{
    private const CACHE_DURATION = 60;

    public function getBannersByCategory(string $categorySlug): Collection
    {
        $cacheKey = "banners.category.{$categorySlug}";
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($categorySlug) {
            $category = BannerCategory::where('slug', $categorySlug)
                ->where('is_active', true)
                ->first();
                
            if (!$category) {
                // Return empty Eloquent Collection using Banner model
                return Banner::whereRaw('1 = 0')->get();
            }
            
            // Call activeBanners method which returns Eloquent Collection
            return $category->activeBanners();
        });
    }
    public function getRandomBannersForAds(int $limit = 3, array $excludeCategories = []): Collection
    {
        $cacheKey = "banners.random.ads.{$limit}." . md5(implode(',', $excludeCategories));
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($limit, $excludeCategories) {
            $query = Banner::active()
                ->with('category')
                ->where('button_link', '!=', null); // Only banners with links for ads
                
            if (!empty($excludeCategories)) {
                $query->whereDoesntHave('category', function ($q) use ($excludeCategories) {
                    $q->whereIn('slug', $excludeCategories);
                });
            }
            
            return $query->inRandomOrder()
                ->limit($limit)
                ->get();
        });
    }
    public function getDashboardAds(int $limit = 2): Collection
    {
        return $this->getRandomBannersForAds($limit, ['homepage-hero']);
    }
    public function getActiveCategoriesWithCounts(): Collection
    {
        $cacheKey = "banner.categories.with.counts";
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            return BannerCategory::where('is_active', true)
                ->withCount(['banners as active_banners_count' => function ($query) {
                    $query->active();
                }])
                ->orderBy('display_order')
                ->get();
        });
    }
    public function trackBannerInteraction(int $bannerId, string $action = 'click'): bool
    {
        try {
            Log::info("Banner {$action}", [
                'banner_id' => $bannerId,
                'action' => $action,
                'timestamp' => now(),
                'user_agent' => request()->userAgent(),
                'ip' => request()->ip()
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to track banner interaction", [
                'banner_id' => $bannerId,
                'action' => $action,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    public function clearCache(string $categorySlug = null): void
    {
        if ($categorySlug) {
            Cache::forget("banners.category.{$categorySlug}");
        } else {
            // Clear all banner-related cache
            $patterns = [
                'banners.category.*',
                'banners.random.*', 
                'banners.featured.*',
                'banner.categories.*'
            ];
            
            // In production, implement proper cache tagging
            Cache::flush();
        }
    }
    public function getBannerStats(): array
    {
        $cacheKey = "banner.stats";
        
        return Cache::remember($cacheKey, 30, function () {
            return [
                'total_banners' => Banner::count(),
                'active_banners' => Banner::active()->count(),
                'total_categories' => BannerCategory::count(),
                'active_categories' => BannerCategory::where('is_active', true)->count(),
                'banners_with_schedule' => Banner::whereNotNull('start_date')->orWhereNotNull('end_date')->count(),
                'expired_banners' => Banner::where('end_date', '<', now())->count(),
            ];
        });
    }
    public function isBannerScheduleActive(Banner $banner): bool
    {
        $now = now();
        
        // Check start date
        if ($banner->start_date && $banner->start_date > $now) {
            return false;
        }
        
        // Check end date
        if ($banner->end_date && $banner->end_date < $now) {
            return false;
        }
        
        return true;
    }
    public function getUpcomingBanners(): Collection
    {
        return Banner::where('is_active', true)
            ->where('start_date', '>', now())
            ->orderBy('start_date')
            ->with('category')
            ->get();
    }
    public function getExpiredBanners(): Collection
    {
        return Banner::where('end_date', '<', now())
            ->orderByDesc('end_date')
            ->with('category')
            ->get();
    }
    public function getActiveBanners(): Collection
    {
        return Banner::active()
            ->with('category')
            ->orderBy('display_order')
            ->get();
    }
    public function getFeaturedBanners(int $limit = 5): Collection
    {
        return Banner::active()
            ->with('category')
            ->orderBy('display_order')
            ->limit($limit)
            ->get();
    }
    public function getBannersForAdmin(array $filters = [], int $perPage = 10)
    {
        $query = Banner::with(['category'])
            ->orderBy('banner_category_id')
            ->orderBy('display_order');

        // Apply search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('subtitle', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply category filter
        if (!empty($filters['category'])) {
            $query->where('banner_category_id', $filters['category']);
        }

        // Apply status filter
        if (!empty($filters['status'])) {
            $now = now();
            switch ($filters['status']) {
                case 'active':
                    $query->where('is_active', true)
                          ->where(function ($q) use ($now) {
                              $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
                          })
                          ->where(function ($q) use ($now) {
                              $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
                          });
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'scheduled':
                    $query->where('is_active', true)
                          ->where('start_date', '>', $now);
                    break;
                case 'expired':
                    $query->where('end_date', '<', $now);
                    break;
            }
        }

        return $query->paginate($perPage);
    }
    public function toggleStatus(Banner $banner): bool
    {
        $banner->is_active = !$banner->is_active;
        return $banner->save();
    }

    public function duplicate(Banner $banner): Banner
    {
        $newBanner = $banner->replicate();
        $newBanner->title = $banner->title . ' (Copy)';
        $newBanner->is_active = false;
        $newBanner->display_order = $banner->display_order + 1;
        $newBanner->save();

        return $newBanner;
    }

    public function getStatistics(): array
    {
        $now = now();
        
        return [
            'total_banners' => Banner::count(),
            'active_banners' => Banner::where('is_active', true)->count(),
            'inactive_banners' => Banner::where('is_active', false)->count(),
            'live_banners' => Banner::active()->count(),
            'scheduled_banners' => Banner::where('is_active', true)
                ->where('start_date', '>', $now)
                ->count(),
            'expired_banners' => Banner::where('end_date', '<', $now)->count(),
            'categories_count' => BannerCategory::count(),
            'active_categories_count' => BannerCategory::where('is_active', true)->count(),
        ];
    }

    public function reorderBanners(array $bannerIds, int $categoryId): bool
    {
        $order = 1;
        foreach ($bannerIds as $bannerId) {
            Banner::where('id', $bannerId)
                  ->where('banner_category_id', $categoryId)
                  ->update(['display_order' => $order]);
            $order++;
        }

        return true;
    }
    public function bulkUpdateStatus(array $bannerIds, bool $isActive): int
    {
        return Banner::whereIn('id', $bannerIds)
                     ->update(['is_active' => $isActive]);
    }

    public function bulkDelete(array $bannerIds): int
    {
        // First, delete the image files
        $banners = Banner::whereIn('id', $bannerIds)->get();
        foreach ($banners as $banner) {
            $this->deleteImages($banner);
        }

        return Banner::whereIn('id', $bannerIds)->delete();
    }
    public function deleteImages(Banner $banner): void
    {
        if ($banner->image) {
            \Storage::delete('public/' . $banner->image);
        }
        
        if ($banner->mobile_image) {
            \Storage::delete('public/' . $banner->mobile_image);
        }
    }

    public function removeImage(Banner $banner, string $imageType): bool
    {
        if ($imageType === 'desktop' && $banner->image) {
            \Storage::delete('public/' . $banner->image);
            $banner->image = null;
        } elseif ($imageType === 'mobile' && $banner->mobile_image) {
            \Storage::delete('public/' . $banner->mobile_image);
            $banner->mobile_image = null;
        }

        return $banner->save();
    }
}