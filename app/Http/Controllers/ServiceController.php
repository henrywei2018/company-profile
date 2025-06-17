<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Project;
use App\Models\Testimonial;
use App\Models\CompanyProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ServiceController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        // BaseController sudah melakukan shareDataToViews() otomatis
        // Tidak perlu memanggil shareBaseData() lagi
    }

    /**
     * Display the services index page with filtering and search capabilities.
     */
    public function index(Request $request)
    {
        // Build query with filters
        $query = Service::query()
            ->active()
            ->with(['category', 'images']);

        // Apply category filter
        if ($request->filled('category')) {
            $category = ServiceCategory::where('slug', $request->category)->first();
            if ($category) {
                $query->where('service_category_id', $category->id);
            }
        }

        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('short_description', 'like', "%{$searchTerm}%");
            });
        }

        // Apply sorting
        $sortBy = $request->get('sort', 'featured');
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
            default: // featured
                $query->orderByDesc('featured')->latest('created_at');
        }

        // Paginate results
        $services = $query->paginate(9)->withQueryString();

        // Get categories for filter sidebar
        $categories = ServiceCategory::query()
            ->active()
            ->withCount(['activeServices'])
            ->orderBy('name')
            ->get();

        // Get featured services for sidebar or recommendations
        $featuredServices = Service::query()
            ->active()
            ->featured()
            ->with(['category', 'images'])
            ->latest('created_at')
            ->limit(3)
            ->get();

        // Get service statistics
        $serviceStats = [
            'total_services' => Service::active()->count(),
            'total_categories' => ServiceCategory::active()->count(),
            'completed_projects' => Project::where('status', 'completed')->count(),
            'satisfied_clients' => Testimonial::active()->featured()->count(),
        ];

        // SEO Data
        $seoData = [
            'title' => 'Layanan Kami - CV Usaha Prima Lestari',
            'description' => 'Jelajahi berbagai layanan professional yang kami tawarkan. Solusi terpercaya untuk kebutuhan bisnis dan konstruksi Anda.',
            'keywords' => 'layanan konstruksi, jasa profesional, kontraktor, konsultan',
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => route('home')],
                ['name' => 'Services', 'url' => route('services.index')]
            ]
        ];

        return view('pages.services.index', compact(
            'services',
            'categories',
            'featuredServices',
            'serviceStats',
            'seoData'
        ));
    }

    /**
     * Display the specified service detail page.
     */
    public function show(Service $services)
    {
        // Ensure service is active
        if (!$services->is_active) {
            abort(404);
        }

        // Load relationships
        $services->load([
            'category',
            'images' => function ($query) {
                $query->orderBy('sort_order')->orderBy('created_at');
            }
        ]);

        // Get related services (same category or featured)
        $relatedServices = Service::query()
            ->active()
            ->where('id', '!=', $services->id)
            ->when($services->service_category_id, function ($query) use ($services) {
                $query->where('service_category_id', $services->service_category_id);
            })
            ->with(['category', 'images'])
            ->orderByDesc('featured')
            ->limit(3)
            ->get();

        // Get projects using this service
        $relatedProjects = Project::query()
            ->active()
            ->where('status', 'completed')
            ->whereHas('services', function ($query) use ($services) {
                $query->where('services.id', $services->id);
            })
            ->with(['category', 'images', 'client'])
            ->orderByDesc('featured')
            ->limit(4)
            ->get();

        // Get testimonials related to this service
        $serviceTestimonials = Testimonial::query()
            ->active()
            ->featured()
            ->whereHas('project.services', function ($query) use ($services) {
                $query->where('services.id', $services->id);
            })
            ->with(['project', 'client'])
            ->limit(3)
            ->get();

        // Get service features/benefits (if stored in a separate table or JSON field)
        $serviceFeatures = $this->getServiceFeatures($services);

        // Get service FAQ (if available)
        $serviceFaq = $this->getServiceFaq($services);

        // Get next/previous service for navigation
        $navigation = [
            'previous' => Service::active()
                ->where('id', '<', $services->id)
                ->orderByDesc('id')
                ->first(['id', 'title', 'slug']),
            'next' => Service::active()
                ->where('id', '>', $services->id)
                ->orderBy('id')
                ->first(['id', 'title', 'slug'])
        ];

        // SEO Data
        $seoData = [
            'title' => $services->meta_title ?: $services->title . ' - CV Usaha Prima Lestari',
            'description' => $services->meta_description ?: $services->short_description,
            'keywords' => $services->meta_keywords,
            'image' => $services->featured_image,
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => route('home')],
                ['name' => 'Services', 'url' => route('services.index')],
                ['name' => $services->title, 'url' => route('services.show', $services->slug)]
            ]
        ];

        return view('pages.services.show', compact(
            'services',
            'relatedServices',
            'relatedProjects',
            'serviceTestimonials',
            'serviceFeatures',
            'serviceFaq',
            'navigation',
            'seoData'
        ));
    }

    /**
     * Get featured services for API or AJAX calls.
     */
    public function featured()
    {
        $services = Service::query()
            ->active()
            ->featured()
            ->with(['category', 'images'])
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $services
        ]);
    }

    /**
     * Get service categories for API or AJAX calls.
     */
    public function categories()
    {
        $categories = ServiceCategory::query()
            ->active()
            ->withCount(['activeServices'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    /**
     * Get related services for a specific service.
     */
    public function related(Service $service)
    {
        $relatedServices = Service::query()
            ->active()
            ->where('id', '!=', $service->id)
            ->when($service->service_category_id, function ($query) use ($service) {
                $query->where('service_category_id', $service->service_category_id);
            })
            ->with(['category', 'images'])
            ->orderByDesc('featured')
            ->limit(6)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $relatedServices
        ]);
    }

    /**
     * Get service features from database or configuration.
     */
    private function getServiceFeatures(Service $service)
    {
        // If you have a features field (JSON) in the services table
        if (isset($service->features) && is_array($service->features)) {
            return $service->features;
        }

        // Or if you have a separate service_features table
        // return $service->features()->active()->ordered()->get();

        // Default features structure if none available
        return [
            [
                'icon' => 'check-circle',
                'title' => 'Professional Quality',
                'description' => 'Kualitas professional dengan standar internasional'
            ],
            [
                'icon' => 'clock',
                'title' => 'Timely Delivery',
                'description' => 'Pengerjaan tepat waktu sesuai jadwal yang disepakati'
            ],
            [
                'icon' => 'shield-check',
                'title' => 'Quality Guarantee',
                'description' => 'Garansi kualitas dan kepuasan pelanggan'
            ],
        ];
    }

    /**
     * Get service FAQ from database or configuration.
     */
    private function getServiceFaq(Service $service)
    {
        // If you have a faq field (JSON) in the services table
        if (isset($service->faq) && is_array($service->faq)) {
            return $service->faq;
        }

        // Or if you have a separate service_faqs table
        // return $service->faqs()->active()->ordered()->get();

        // Default FAQ structure if none available
        return [
            [
                'question' => 'Berapa lama waktu pengerjaan?',
                'answer' => 'Waktu pengerjaan bervariasi tergantung kompleksitas proyek. Kami akan memberikan estimasi waktu yang akurat setelah evaluasi awal.'
            ],
            [
                'question' => 'Apakah ada garansi?',
                'answer' => 'Ya, kami memberikan garansi untuk semua pekerjaan sesuai dengan standar industri dan kesepakatan kontrak.'
            ],
            [
                'question' => 'Bagaimana cara memulai proyek?',
                'answer' => 'Hubungi kami untuk konsultasi awal gratis. Tim kami akan melakukan survey dan memberikan proposal yang sesuai dengan kebutuhan Anda.'
            ],
        ];
    }
}