<?php

namespace App\View\Components;

use App\Services\BannerService;
use Illuminate\View\Component;

class BannerSlider extends Component
{
    public $banners;
    public $category;
    public $displayType;
    public $showArrows;
    public $showDots;
    public $autoPlay;
    public $height;
    public $limit;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $categorySlug = 'homepage-hero',
        string $displayType = 'slider', // slider, ads, grid, single
        bool $showArrows = true,
        bool $showDots = false,
        bool $autoPlay = true,
        string $height = 'h-50 md:h-[calc(100vh-106px)]',
        int $limit = null
    ) {
        $bannerService = app(BannerService::class);
        
        $this->displayType = $displayType;
        $this->showArrows = $showArrows;
        $this->showDots = $showDots;
        $this->autoPlay = $autoPlay;
        $this->height = $height;
        $this->limit = $limit;

        // Get banners based on display type
        switch ($displayType) {
            case 'ads':
            case 'dashboard-ads':
                $this->banners = $bannerService->getDashboardAds($limit ?? 2);
                $this->category = null;
                break;
            case 'random':
                $this->banners = $bannerService->getRandomBannersForAds($limit ?? 3);
                $this->category = null;
                break;
            case 'featured':
                $this->banners = $bannerService->getFeaturedBanners($limit ?? 5);
                $this->category = null;
                break;
            default:
                $this->banners = $bannerService->getBannersByCategory($categorySlug);
                $this->category = $categorySlug;
                if ($limit) {
                    $this->banners = $this->banners->take($limit);
                }
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.banner-slider');
    }
}