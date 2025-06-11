<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'banner_category_id',
        'title',
        'subtitle',
        'description',
        'image',
        'mobile_image',
        'button_text',
        'button_link',
        'link_type',
        'open_in_new_tab',
        'is_active',
        'display_order',
        'start_date',
        'end_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'open_in_new_tab' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Get the category that owns the banner.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BannerCategory::class, 'banner_category_id');
    }

    /**
     * Get the desktop image URL attribute.
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return Storage::disk('public')->url($this->image);
        }
        
        return asset('images/default-banner.jpg');
    }

    /**
     * Get the mobile image URL attribute.
     */
    public function getMobileImageUrlAttribute()
    {
        if ($this->mobile_image) {
            return Storage::disk('public')->url($this->mobile_image);
        }
        
        return $this->getImageUrlAttribute();
    }

    /**
     * Get the responsive image URL based on device type
     */
    public function getResponsiveImageUrl($isMobile = false)
    {
        if ($isMobile && $this->mobile_image) {
            return $this->getMobileImageUrlAttribute();
        }
        
        return $this->getImageUrlAttribute();
    }

    /**
     * Get image dimensions
     */
    public function getImageDimensions($imageType = 'desktop')
    {
        $imagePath = $imageType === 'mobile' ? $this->mobile_image : $this->image;
        
        if (!$imagePath || !Storage::disk('public')->exists($imagePath)) {
            return null;
        }

        try {
            $fullPath = Storage::disk('public')->path($imagePath);
            $imageSize = getimagesize($fullPath);
            
            return [
                'width' => $imageSize[0] ?? null,
                'height' => $imageSize[1] ?? null,
                'type' => $imageSize['mime'] ?? null,
            ];
        } catch (\Exception $e) {
            \Log::warning('Could not get image dimensions: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get formatted file size for images
     */
    public function getImageFileSize($imageType = 'desktop')
    {
        $imagePath = $imageType === 'mobile' ? $this->mobile_image : $this->image;
        
        if (!$imagePath || !Storage::disk('public')->exists($imagePath)) {
            return '0 B';
        }

        try {
            $bytes = Storage::disk('public')->size($imagePath);
            return $this->formatFileSize($bytes);
        } catch (\Exception $e) {
            return '0 B';
        }
    }

    /**
     * Check if banner has any images
     */
    public function hasImages()
    {
        return !empty($this->image) || !empty($this->mobile_image);
    }

    /**
     * Check if banner has desktop image
     */
    public function hasDesktopImage()
    {
        return !empty($this->image) && Storage::disk('public')->exists($this->image);
    }

    /**
     * Check if banner has mobile image
     */
    public function hasMobileImage()
    {
        return !empty($this->mobile_image) && Storage::disk('public')->exists($this->mobile_image);
    }

    /**
     * Get all banner images formatted for Universal File Uploader
     */
    public function getImagesForUploader()
    {
        $images = [];
        
        if ($this->hasDesktopImage()) {
            $dimensions = $this->getImageDimensions('desktop');
            $images[] = [
                'id' => 'desktop_' . $this->id,
                'name' => 'Desktop Image',
                'file_name' => basename($this->image),
                'file_path' => $this->image,
                'file_type' => $this->getImageMimeType($this->image),
                'file_size' => $this->getImageFileSizeBytes('desktop'),
                'category' => 'desktop',
                'type' => 'desktop',
                'url' => $this->getImageUrlAttribute(),
                'download_url' => $this->getImageUrlAttribute(),
                'size' => $this->getImageFileSize('desktop'),
                'dimensions' => $dimensions,
                'created_at' => $this->updated_at->format('M j, Y H:i'),
                'description' => 'Desktop banner image (' . ($dimensions['width'] ?? '?') . 'x' . ($dimensions['height'] ?? '?') . ')',
            ];
        }
        
        if ($this->hasMobileImage()) {
            $dimensions = $this->getImageDimensions('mobile');
            $images[] = [
                'id' => 'mobile_' . $this->id,
                'name' => 'Mobile Image',
                'file_name' => basename($this->mobile_image),
                'file_path' => $this->mobile_image,
                'file_type' => $this->getImageMimeType($this->mobile_image),
                'file_size' => $this->getImageFileSizeBytes('mobile'),
                'category' => 'mobile',
                'type' => 'mobile',
                'url' => $this->getMobileImageUrlAttribute(),
                'download_url' => $this->getMobileImageUrlAttribute(),
                'size' => $this->getImageFileSize('mobile'),
                'dimensions' => $dimensions,
                'created_at' => $this->updated_at->format('M j, Y H:i'),
                'description' => 'Mobile banner image (' . ($dimensions['width'] ?? '?') . 'x' . ($dimensions['height'] ?? '?') . ')',
            ];
        }
        
        return $images;
    }

    /**
     * Get image MIME type
     */
    protected function getImageMimeType($imagePath)
    {
        if (!$imagePath || !Storage::disk('public')->exists($imagePath)) {
            return 'image/jpeg';
        }

        try {
            $fullPath = Storage::disk('public')->path($imagePath);
            $imageInfo = getimagesize($fullPath);
            return $imageInfo['mime'] ?? 'image/jpeg';
        } catch (\Exception $e) {
            // Fallback based on file extension
            $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
            return match($extension) {
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                default => 'image/jpeg'
            };
        }
    }

    /**
     * Get image file size in bytes
     */
    protected function getImageFileSizeBytes($imageType = 'desktop')
    {
        $imagePath = $imageType === 'mobile' ? $this->mobile_image : $this->image;
        
        if (!$imagePath || !Storage::disk('public')->exists($imagePath)) {
            return 0;
        }

        try {
            return Storage::disk('public')->size($imagePath);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Format file size in human readable format
     */
    protected function formatFileSize($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Get optimized image URLs with responsive support
     */
    public function getOptimizedImageUrls()
    {
        return [
            'desktop' => [
                'original' => $this->getImageUrlAttribute(),
                'large' => $this->getImageUrlAttribute(), // Could add image variants here
                'medium' => $this->getImageUrlAttribute(),
                'small' => $this->getMobileImageUrlAttribute(),
            ],
            'mobile' => [
                'original' => $this->getMobileImageUrlAttribute(),
                'large' => $this->getMobileImageUrlAttribute(),
                'medium' => $this->getMobileImageUrlAttribute(),
                'small' => $this->getMobileImageUrlAttribute(),
            ]
        ];
    }

    /**
     * Get banner data for JSON responses
     */
    public function toUploadResponse()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'description' => $this->description,
            'category' => $this->category->name,
            'images' => $this->getImagesForUploader(),
            'status' => $this->status,
            'is_active' => $this->is_active,
            'has_desktop_image' => $this->hasDesktopImage(),
            'has_mobile_image' => $this->hasMobileImage(),
            'desktop_image_url' => $this->hasDesktopImage() ? $this->getImageUrlAttribute() : null,
            'mobile_image_url' => $this->hasMobileImage() ? $this->getMobileImageUrlAttribute() : null,
        ];
    }

    /**
     * Scope a query to only include active banners.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    /**
     * Scope to filter by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('banner_category_id', $categoryId);
    }

    /**
     * Scope to order by display order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('created_at');
    }

    /**
     * Check if banner is currently scheduled to be active
     */
    public function isScheduledActive()
    {
        $now = now();
        
        // Check if not started yet
        if ($this->start_date && $this->start_date > $now) {
            return false;
        }
        
        // Check if expired
        if ($this->end_date && $this->end_date < $now) {
            return false;
        }
        
        return $this->is_active;
    }

    /**
     * Get banner status with schedule consideration
     */
    public function getStatusAttribute()
    {
        if (!$this->is_active) {
            return 'inactive';
        }
        
        $now = now();
        
        if ($this->start_date && $this->start_date > $now) {
            return 'scheduled';
        }
        
        if ($this->end_date && $this->end_date < $now) {
            return 'expired';
        }
        
        return 'active';
    }

    /**
     * Get formatted status for display
     */
    public function getFormattedStatusAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    /**
     * Process URL based on type and base URL
     */
    public function getProcessedButtonLinkAttribute()
    {
        if (empty($this->button_link)) {
            return null;
        }

        return $this->processUrl($this->button_link, $this->link_type);
    }

    /**
     * Process URL based on type
     */
    public function processUrl($url, $linkType = null)
    {
        if (empty($url)) {
            return null;
        }

        // If no link type specified, try to detect
        if (!$linkType) {
            $linkType = $this->detectLinkType($url);
        }

        switch ($linkType) {
            case 'external':
                return $this->ensureProtocol($url);
            case 'internal':
                return $this->makeInternalUrl($url);
            case 'route':
                return $this->makeRouteUrl($url);
            case 'email':
                return 'mailto:' . $url;
            case 'phone':
                return 'tel:' . $url;
            case 'anchor':
                return $url;
            default:
                return $this->autoProcessUrl($url);
        }
    }

    /**
     * Detect link type based on URL pattern
     */
    protected function detectLinkType($url)
    {
        // Email detection
        if (filter_var($url, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }

        // Phone detection
        if (preg_match('/^\+?[\d\s\-\(\)]+$/', $url)) {
            return 'phone';
        }

        // Anchor detection
        if (str_starts_with($url, '#')) {
            return 'anchor';
        }

        // External URL detection
        if (preg_match('/^https?:\/\//', $url)) {
            $parsed = parse_url($url);
            $currentDomain = parse_url(config('app.url'), PHP_URL_HOST);
            
            if (isset($parsed['host']) && $parsed['host'] !== $currentDomain) {
                return 'external';
            }
            return 'internal';
        }

        // Route detection
        if (preg_match('/^[a-zA-Z][a-zA-Z0-9._-]*$/', $url) && \Route::has($url)) {
            return 'route';
        }

        return 'internal';
    }

    /**
     * Ensure URL has protocol
     */
    protected function ensureProtocol($url)
    {
        if (!preg_match('/^https?:\/\//', $url)) {
            return 'https://' . $url;
        }
        return $url;
    }

    /**
     * Make internal URL with base URL
     */
    protected function makeInternalUrl($url)
    {
        if (preg_match('/^https?:\/\//', $url)) {
            return $url;
        }

        $url = ltrim($url, '/');
        return url($url);
    }

    /**
     * Make route URL
     */
    protected function makeRouteUrl($routeName)
    {
        try {
            if (str_contains($routeName, ':')) {
                [$route, $params] = explode(':', $routeName, 2);
                $paramArray = explode(',', $params);
                return route($route, $paramArray);
            }

            return route($routeName);
        } catch (\Exception $e) {
            \Log::warning("Invalid route name in banner: {$routeName}", [
                'banner_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return '#';
        }
    }

    /**
     * Auto-process URL with intelligent detection
     */
    protected function autoProcessUrl($url)
    {
        // Already has protocol
        if (preg_match('/^https?:\/\//', $url)) {
            return $url;
        }

        // Email
        if (filter_var($url, FILTER_VALIDATE_EMAIL)) {
            return 'mailto:' . $url;
        }

        // Phone
        if (preg_match('/^\+?[\d\s\-\(\)]+$/', $url)) {
            return 'tel:' . $url;
        }

        // Anchor
        if (str_starts_with($url, '#')) {
            return $url;
        }

        // Try as route first
        if (\Route::has($url)) {
            return route($url);
        }

        // Default to internal URL
        return $this->makeInternalUrl($url);
    }

    /**
     * Check if link should open in new tab
     */
    public function shouldOpenInNewTab()
    {
        // Always open external links in new tab
        if ($this->detectLinkType($this->button_link) === 'external') {
            return true;
        }

        return $this->open_in_new_tab;
    }

    /**
     * Get the complete link attributes for HTML
     */
    public function getLinkAttributesAttribute()
    {
        $attributes = [
            'href' => $this->processed_button_link
        ];

        if ($this->shouldOpenInNewTab()) {
            $attributes['target'] = '_blank';
            $attributes['rel'] = 'noopener noreferrer';
        }

        return $attributes;
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();
        
        // Auto-set display order if not provided
        static::creating(function ($banner) {
            if (empty($banner->display_order)) {
                $maxOrder = static::where('banner_category_id', $banner->banner_category_id)
                    ->max('display_order');
                $banner->display_order = ($maxOrder ?? 0) + 1;
            }
        });
        
        // Clean up images when banner is deleted
        static::deleting(function ($banner) {
            if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                Storage::disk('public')->delete($banner->image);
            }
            
            if ($banner->mobile_image && Storage::disk('public')->exists($banner->mobile_image)) {
                Storage::disk('public')->delete($banner->mobile_image);
            }
        });
    }
}