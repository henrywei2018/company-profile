<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceCategory;

class ServicesController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display a listing of services
     */
    public function index(Request $request)
    {
        // Set page meta
        $this->setPageMeta(
            title: 'Layanan Kami - ' . $this->siteConfig['site_name'],
            description: 'Temukan berbagai layanan berkualitas yang kami tawarkan untuk memenuhi kebutuhan bisnis Anda.',
            keywords: 'layanan, jasa, ' . $this->siteConfig['site_keywords']
        );

        // Set breadcrumb
        $this->setBreadcrumb([
            ['name' => 'Layanan', 'url' => null]
        ]);

        // Get services with filtering
        $query = Service::where('is_active', true)->with('category');

        // Filter by category
        if ($request->filled('category')) {
            $category = ServiceCategory::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $services = $query->orderBy('sort_order', 'asc')
                         ->orderBy('created_at', 'desc')
                         ->paginate(12);

        // Add global JS vars for page interactivity
        $this->addGlobalJsVars([
            'page' => 'services',
            'currentCategory' => $request->category,
            'searchQuery' => $request->search,
            'enableFiltering' => true,
        ]);

        return view('pages.services.index', compact('services'));
    }

    /**
     * Display services by category
     */
    public function category($categorySlug)
    {
        $category = ServiceCategory::where('slug', $categorySlug)
                                  ->where('is_active', true)
                                  ->firstOrFail();

        // Set page meta
        $this->setPageMeta(
            title: $category->name . ' - Layanan ' . $this->siteConfig['site_name'],
            description: $category->description ?: "Layanan {$category->name} terbaik dari {$this->siteConfig['site_name']}",
            keywords: $category->name . ', layanan, ' . $this->siteConfig['site_keywords']
        );

        // Set breadcrumb
        $this->setBreadcrumb([
            ['name' => 'Layanan', 'url' => route('services.index')],
            ['name' => $category->name, 'url' => null]
        ]);

        $services = Service::where('category_id', $category->id)
                          ->where('is_active', true)
                          ->orderBy('sort_order', 'asc')
                          ->orderBy('created_at', 'desc')
                          ->paginate(12);

        $this->addGlobalJsVars([
            'page' => 'services-category',
            'categoryId' => $category->id,
            'categorySlug' => $category->slug,
        ]);

        return view('pages.services.category', compact('services', 'category'));
    }

    /**
     * Display the specified service
     */
    public function show($slug)
    {
        $service = Service::where('slug', $slug)
                         ->where('is_active', true)
                         ->with(['category', 'projects' => function($query) {
                             $query->where('is_active', true)
                                   ->where('is_featured', true)
                                   ->limit(6);
                         }])
                         ->firstOrFail();

        // Set page meta
        $this->setPageMeta(
            title: $service->title . ' - ' . $this->siteConfig['site_name'],
            description: $service->excerpt ?: strip_tags($service->description),
            keywords: $service->title . ', ' . ($service->category?->name ?? '') . ', ' . $this->siteConfig['site_keywords'],
            image: $service->featured_image ? asset($service->featured_image) : null
        );

        // Set breadcrumb
        $breadcrumbItems = [
            ['name' => 'Layanan', 'url' => route('services.index')]
        ];
        
        if ($service->category) {
            $breadcrumbItems[] = [
                'name' => $service->category->name, 
                'url' => route('services.category', $service->category->slug)
            ];
        }
        
        $breadcrumbItems[] = ['name' => $service->title, 'url' => null];
        
        $this->setBreadcrumb($breadcrumbItems);

        // Get related services
        $relatedServices = Service::where('is_active', true)
                                 ->where('id', '!=', $service->id)
                                 ->when($service->category_id, function($query) use ($service) {
                                     $query->where('category_id', $service->category_id);
                                 })
                                 ->limit(4)
                                 ->get();

        $this->addGlobalJsVars([
            'page' => 'service-detail',
            'serviceId' => $service->id,
            'serviceSlug' => $service->slug,
            'enableQuotationForm' => true,
            'quotationUrl' => route('quotation.create', ['service' => $service->slug]),
        ]);

        return view('pages.services.show', compact('service', 'relatedServices'));
    }

    /**
     * Get services for AJAX requests
     */
    public function ajax(Request $request)
    {
        $query = Service::where('is_active', true)->with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $services = $query->orderBy('sort_order', 'asc')
                         ->limit($request->limit ?? 12)
                         ->get();

        return response()->json([
            'status' => 'success',
            'data' => $services,
            'count' => $services->count()
        ]);
    }
}