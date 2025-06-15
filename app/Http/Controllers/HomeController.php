<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Project;
use App\Models\Testimonial;
use App\Models\CompanyProfile; // Jika ada
use App\Models\Banner; // Jika ada

class HomeController extends Controller
{
    public function index()
    {
        // Ambil data yang diperlukan, contoh limit sesuai kebutuhan home page
        $services = Service::latest()->limit(6)->get();
        $featuredProjects = Project::latest()->limit(6)->get();
        $testimonials = Testimonial::latest()->limit(1)->get(); // 1 saja yang tampil
        $companyProfile = CompanyProfile::first(); // Ambil data profil perusahaan, jika ada

        // Lempar ke view
        return view('pages.home', compact(
            'services',
            'featuredProjects',
            'testimonials',
            'companyProfile'
        ));
    }
}
