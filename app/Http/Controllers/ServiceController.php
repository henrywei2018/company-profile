<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ServiceController extends Controller
{
    /**
     * Display the services page with filtering, search, and pagination.
     */
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
    public function show(Service $service)
    {
        // Only show active services
        if (!$service->is_active) {
            abort(404);
        }

        $service->load(['category']);

        // Get related services
        $relatedServices = $this->getRelatedServices($service, 4);

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
            ->where('id', '!=', $service->id)
            ->latest('created_at')
            ->limit(5)
            ->get();

        // SEO Data
        $seoData = [
            'title' => $service->title . ' - CV Usaha Prima Lestari',
            'description' => $service->short_description ?: strip_tags($service->description),
            'keywords' => $service->category ? $service->category->name . ', ' . $service->title : $service->title,
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => route('home')],
                ['name' => 'Services', 'url' => route('services.index')],
                ['name' => $service->title, 'url' => route('services.show', $service->slug), 'active' => true]
            ]
        ];

        // Add category to breadcrumbs if exists
        if ($service->category) {
            array_splice($seoData['breadcrumbs'], -1, 0, [
                ['name' => $service->category->name, 'url' => route('services.index', ['category' => $service->category->slug])]
            ]);
        }

        return view('pages.service.show', compact(
            'service',
            'relatedServices',
            'categories',
            'recentServices',
            'seoData'
        ));
    }

    /**
     * Get related services based on category.
     */
    private function getRelatedServices(Service $service, int $limit = 4)
    {
        if (!$service->category_id) {
            return Service::query()
                ->active()
                ->with(['category'])
                ->where('id', '!=', $service->id)
                ->ordered()
                ->limit($limit)
                ->get();
        }

        return Service::query()
            ->active()
            ->with(['category'])
            ->where('id', '!=', $service->id)
            ->where('category_id', $service->category_id)
            ->ordered()
            ->limit($limit)
            ->get();
    }
}