<?php

namespace App\View\Components;

use App\Services\BannerService;
use Illuminate\View\Component;
use Illuminate\Support\Collection;

class BannerSlider extends Component
{
    public Collection $banners;
    public string $category;
    public int $limit;
    public string $height;
    public bool $showNavigation;
    public bool $showPagination;
    public bool $autoplay;
    public int $autoplayDelay;
    public string $effect;
    public string $containerClass;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $category = 'homepage-hero',
        int $limit = 5,
        string $height = 'h-[600px] lg:h-[700px]',
        bool $showNavigation = true,
        bool $showPagination = true,
        bool $autoplay = true,
        int $autoplayDelay = 5000,
        string $effect = 'fade', // fade, slide, creative
        string $containerClass = ''
    ) {
        $this->category = $category;
        $this->limit = $limit;
        $this->height = $height;
        $this->showNavigation = $showNavigation;
        $this->showPagination = $showPagination;
        $this->autoplay = $autoplay;
        $this->autoplayDelay = $autoplayDelay;
        $this->effect = $effect;
        $this->containerClass = $containerClass;

        // Get banners from service
        $bannerService = app(BannerService::class);
        $this->banners = $bannerService->getBannersByCategory($category, $limit);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.banner-slider');
    }

    /**
     * Get unique swiper ID for this instance
     */
    public function getSwiperId(): string
    {
        return 'banner-slider-' . md5($this->category . $this->limit . microtime());
    }

    /**
     * Get swiper configuration
     */
    public function getSwiperConfig(): array
    {
        $config = [
            'loop' => $this->banners->count() > 1,
            'autoplay' => $this->autoplay ? [
                'delay' => $this->autoplayDelay,
                'disableOnInteraction' => false,
            ] : false,
        ];

        // Effect configuration
        switch ($this->effect) {
            case 'fade':
                $config['effect'] = 'fade';
                $config['fadeEffect'] = ['crossFade' => true];
                break;
            case 'creative':
                $config['effect'] = 'creative';
                $config['creativeEffect'] = [
                    'prev' => ['shadow' => true, 'translate' => [0, 0, -400]],
                    'next' => ['translate' => ['100%', 0, 0]]
                ];
                break;
            default:
                // Default slide effect
                break;
        }

        return $config;
    }

    /**
     * Check if banner has content
     */
    public function hasContent(): bool
    {
        return $this->banners->isNotEmpty();
    }

    /**
     * Get banner background styles
     */
    public function getBannerStyles($banner): string
    {
        if (!$banner->image) {
            return 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);';
        }

        $imageUrl = \Storage::url($banner->image);
        return "background-image: url('{$imageUrl}'); background-size: cover; background-position: center;";
    }
}