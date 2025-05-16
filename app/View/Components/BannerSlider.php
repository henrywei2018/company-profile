<?php

namespace App\View\Components;

use App\Models\BannerCategory;
use Illuminate\View\Component;

class BannerSlider extends Component
{
    public $banners;
    public $category;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($categorySlug = 'home-slider')
    {
        $this->category = BannerCategory::where('slug', $categorySlug)
            ->where('is_active', true)
            ->first();
            
        $this->banners = $this->category ? $this->category->activeBanners() : collect();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.banner-slider');
    }
}