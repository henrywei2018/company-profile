<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Project;
use App\Models\Testimonial;
use App\Models\CompanyProfile;
use App\Models\Banner;
use App\Services\BannerService;

class HomeController extends Controller
{
    protected $bannerService;

    public function __construct(BannerService $bannerService)
    {
        $this->bannerService = $bannerService;
    }
    public function index()
    {
        $heroBanners = $this->bannerService->getBannersByCategory('homepage-hero', 3);
        $services = Service::latest()->limit(6)->get();
        $featuredProjects = Project::latest()->limit(6)->get();
        $testimonials = Testimonial::latest()->limit(1)->get(); // 1 saja yang tampil
        $companyProfile = CompanyProfile::first(); // Ambil data profil perusahaan, jika ada

        // Lempar ke view
        return view('pages.home', compact(
            'services',
            'heroBanners',
            'featuredProjects',
            'testimonials',
            'companyProfile'
        ));
    }
}
