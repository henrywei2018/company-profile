<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ServiceController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->shareBaseData();
    }

    public function index(Request $request)
    {
        // Build query with filters - only active services
        $query = Service::query()
            ->active()
            ->with(['category'])
            ->ordered();

        // Apply filters
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $searchTerm = $request->search;
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('short_description', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('featured') && $request->featured == '1') {
            $query->featured();
        }

        // Apply sorting
        $sortBy = $request->get('sort', 'default');
        switch ($sortBy) {
            case 'name_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('title', 'desc');
                break;
            case 'newest':
                $query->latest('created_at');
                break;
            case 'oldest':
                $query->oldest('created_at');
                break;
            default:
                $query->ordered(); // Use sort_order from model
        }

        $services = $query->paginate(12)->withQueryString();

        // Sidebar data
        $categories = ServiceCategory::query()
            ->active()
            ->whereHas('services', function ($q) {
                $q->active();
            })
            ->withCount(['services' => function ($q) {
                $q->active();
            }])
            ->ordered()
            ->get();

        $featuredServices = Service::query()
            ->active()
            ->featured()
            ->with(['category'])
            ->ordered()
            ->limit(4)
            ->get();

        $recentServices = Service::query()
            ->active()
            ->with(['category'])
            ->latest('created_at')
            ->limit(6)
            ->get();

        // SEO Data
        $seoData = [
            'title' => 'Our Services - CV Usaha Prima Lestari',
            'description' => 'Explore our comprehensive range of construction services, general supplies, and professional solutions for all your building needs.',
            'keywords' => 'services, construction, general supplies, building materials, CV Usaha Prima Lestari',
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => route('home')],
                ['name' => 'Services', 'url' => route('services.index'), 'active' => true]
            ]
        ];

        // Customize SEO based on filters
        if ($request->filled('search')) {
            $seoData['title'] = "Search Results for '{$request->search}' - Services";
            $seoData['description'] = "Search results for '{$request->search}' in our services.";
        }

        if ($request->filled('category')) {
            $category = $categories->where('slug', $request->category)->first();
            if ($category) {
                $seoData['title'] = "{$category->name} - Service Category";
                $seoData['description'] = $category->description ?: "Browse all services in the {$category->name} category.";
                $seoData['breadcrumbs'][] = ['name' => $category->name, 'url' => route('services.index', ['category' => $category->slug]), 'active' => true];
            }
        }

        return view('pages.services.index', compact(
            'services',
            'categories',
            'featuredServices',
            'recentServices',
            'seoData'
        ));
    }

    /**
     * Display single service detail.
     */
    public function show(Service $services)
    {

        $services->load(['category']);

        // Get related services
        $relatedServices = $this->getRelatedServices($services, 4);

        // Sidebar data
        $categories = ServiceCategory::query()
            ->active()
            ->whereHas('services', function ($q) {
                $q->active();
            })
            ->withCount(['services' => function ($q) {
                $q->active();
            }])
            ->ordered()
            ->get();

        $recentServices = Service::query()
            ->active()
            ->with(['category'])
            ->where('id', '!=', $services->id)
            ->latest('created_at')
            ->limit(5)
            ->get();

        // SEO Data
        $seoData = [
            'title' => $services->title . ' - CV Usaha Prima Lestari',
            'description' => $services->short_description ?: strip_tags($services->description),
            'keywords' => $services->category ? $services->category->name . ', ' . $services->title : $services->title,
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => route('home')],
                ['name' => 'Services', 'url' => route('services.index')],
                ['name' => $services->title, 'url' => route('services.show', $services->slug), 'active' => true]
            ]
        ];

        // Add category to breadcrumbs if exists
        if ($services->category) {
            array_splice($seoData['breadcrumbs'], -1, 0, [
                ['name' => $services->category->name, 'url' => route('services.index', ['category' => $services->category->slug])]
            ]);
        }

        return view('pages.services.show', compact(
            'services',
            'relatedServices',
            'categories',
            'recentServices',
            'seoData'
        ));
    }

    /**
     * Get related services based on category.
     */
    private function getRelatedServices(Service $services, int $limit = 4)
    {
        if (!$services->category_id) {
            return Service::query()
                ->active()
                ->with(['category'])
                ->where('id', '!=', $services->id)
                ->ordered()
                ->limit($limit)
                ->get();
        }

        return Service::query()
            ->active()
            ->with(['category'])
            ->where('id', '!=', $services->id)
            ->where('category_id', $services->category_id)
            ->ordered()
            ->limit($limit)
            ->get();
    }
}