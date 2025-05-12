<?php
// File: app/Http/Controllers/HomeController.php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\Post;
use App\Models\CompanyProfile;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the homepage.
     */
    public function index()
    {
        // Get featured projects
        $featuredProjects = Project::featured()->latest()->take(6)->get();
        
        // Get services
        $services = Service::active()->featured()->take(6)->get();
        
        // Get testimonials
        $testimonials = Testimonial::active()->featured()->take(4)->get();
        
        // Get recent blog posts
        $latestPosts = Post::published()->recent(3)->get();
        
        // Get company profile
        $companyProfile = CompanyProfile::getInstance();
        
        return view('pages.home', compact(
            'featuredProjects',
            'services',
            'testimonials',
            'latestPosts',
            'companyProfile'
        ));
    }
}