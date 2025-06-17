<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Project;
use App\Models\Testimonial;
use App\Models\Banner;
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
        // Cek maintenance mode
        $maintenanceCheck = $this->checkMaintenanceMode();
        if ($maintenanceCheck) {
            return $maintenanceCheck;
        }

        // Set page meta untuk SEO
        $this->setPageMeta(
            $this->siteConfig['site_title'],
            $this->siteConfig['site_description'],
            $this->siteConfig['site_keywords'],
            asset($this->siteConfig['site_logo'])
        );

        // Set breadcrumb (home tidak perlu breadcrumb)
        $this->setBreadcrumb([]);

        // Set global JavaScript variables
        $this->addGlobalJsVars([
            'page' => 'home',
            'showChatWidget' => true,
            'servicesCount' => $this->globalServices->count(),
        ]);

        // Ambil data spesifik untuk homepage
        $heroBanners = $this->getBannersByCategory('homepage-hero', 3);
        
        $featuredServices = Service::where('featured', true)
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->limit(6)
            ->get();
        
        $featuredProjects = Project::where('featured', true)
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
            'years_experience' => now()->year - ($this->companyProfile->founded_year ?? 2020),
            'services_offered' => Service::where('is_active', true)->count(),
        ];

        // Berita/artikel terbaru untuk homepage
        $latestPosts = \App\Models\Post::where('status', 'published')
            ->latest('published_at')
            ->limit(3)
            ->get();

        // Data untuk SEO berdasarkan struktur existing
        $seoData = [
            'title' => $this->siteConfig['site_title'],
            'description' => $this->siteConfig['site_description'],
            'keywords' => $this->siteConfig['site_keywords'],
            'breadcrumbs' => [], // Home tidak perlu breadcrumb
        ];

        return view('pages.home', compact(
            'heroBanners',
            'featuredServices',
            'featuredProjects', 
            'testimonials',
            'stats',
            'latestPosts',
            'seoData'
        ));
    }
}