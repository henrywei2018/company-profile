<?php
// File: app/Http/Controllers/HomeController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Project;
use App\Models\Testimonial;
use App\Services\BannerService;

class HomeController extends BaseController
{
    protected $bannerService;

    public function __construct(BannerService $bannerService)
    {
        parent::__construct();
        $this->bannerService = $bannerService;
    }

    public function index()
    {
        // Set page meta untuk SEO
        $this->setPageMeta(
            $this->siteConfig['site_title'],
            $this->siteConfig['site_description'],
            $this->siteConfig['site_keywords'],
            asset($this->siteConfig['site_logo'])
        );

        // Ambil data spesifik untuk homepage
        $heroBanners = $this->getBannersByCategory('homepage-hero', 3);
        
        $featuredServices = Service::where('featured', true)
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->limit(6)
            ->get();
        
        $featuredProjects = Project::where('featured', true)
            ->where('is_active', true)
            ->where('status', 'completed')
            ->latest()
            ->limit(6)
            ->get();
        
        $testimonials = Testimonial::where('is_active', true)
            ->where('featured', true)
            ->latest()
            ->limit(3)
            ->get();

        // Statistik untuk homepage
        $stats = [
            'completed_projects' => Project::where('status', 'completed')->count(),
            'happy_clients' => Project::distinct('client_id')->count(),
            'years_experience' => now()->year - ($this->companyProfile->founded_year ?? 2010),
            'active_services' => $this->globalServices->count(),
        ];

        return view('pages.home', compact(
            'heroBanners',
            'featuredServices', 
            'featuredProjects',
            'testimonials',
            'stats'
        ));
    }
}
