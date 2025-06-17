<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Project;
use App\Models\Testimonial;
use App\Models\CompanyProfile;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ServiceController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        // BaseController automatically handles shareDataToViews()
    }

    /**
     * Display the services index page with filtering and search capabilities.
     */
    public function index(Request $request)
    {
        // Set page meta for SEO
        $this->setPageMeta(
            'Our Services - ' . $this->siteConfig['site_title'],
            'Discover our comprehensive range of professional construction and engineering services. Quality solutions for all your project needs.',
            'construction services, engineering, building, renovation, consultation',
            asset($this->siteConfig['site_logo'])
        );

        // Get search and filter parameters
        $search = $request->get('search');
        $category = $request->get('category');
        $sortBy = $request->get('sort', 'featured'); // featured, title, price
        $perPage = $request->get('per_page', 12);

        // Build services query
        $servicesQuery = Service::where('is_active', true)
            ->with(['category']);

        // Apply search filter
        if ($search) {
            $servicesQuery->where(function($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('short_description', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhereHas('category', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
            });
        }

        // Apply category filter
        if ($category && $category !== 'all') {
            $servicesQuery->whereHas('category', function($query) use ($category) {
                $query->where('slug', $category);
            });
        }

        // Apply sorting
        switch ($sortBy) {
            case 'title':
                $servicesQuery->orderBy('title', 'asc');
                break;
            case 'price':
                $servicesQuery->orderBy('base_price', 'asc');
                break;
            case 'newest':
                $servicesQuery->latest();
                break;
            case 'featured':
            default:
                $servicesQuery->orderBy('featured', 'desc')
                             ->orderBy('sort_order', 'asc')
                             ->orderBy('title', 'asc');
                break;
        }

        // Get paginated services
        $services = $servicesQuery->paginate($perPage)->withQueryString();

        // Get featured services for hero section
        $featuredServices = Cache::remember('featured_services_page', 1800, function () {
            return Service::where('is_active', true)
                ->where('featured', true)
                ->orderBy('sort_order', 'asc')
                ->limit(3)
                ->get();
        });

        // Get categories for filter dropdown
        $categories = Cache::remember('service_categories_filter', 1800, function () {
            return ServiceCategory::where('is_active', true)
                ->withCount(['activeServices'])
                ->orderBy('name', 'asc')
                ->get();
        });

        // Get service statistics
        $stats = [
            'total_services' => Service::where('is_active', true)->count(),
            'completed_projects' => Project::where('status', 'completed')->count(),
            'satisfied_clients' => Project::distinct('client_id')->count(),
            'team_experts' => TeamMember::where('is_active', true)->count(),
        ];

        // Get recent testimonials
        $testimonials = Cache::remember('services_testimonials', 1800, function () {
            return Testimonial::where('is_active', true)
                ->where('featured', true)
                ->latest()
                ->limit(6)
                ->get();
        });

        // Get related projects for showcase
        $recentProjects = Cache::remember('recent_projects_showcase', 1800, function () {
            return Project::where('is_active', true)
                ->where('status', 'completed')
                ->where('featured', true)
                ->with(['category', 'client'])
                ->latest()
                ->limit(6)
                ->get();
        });

        return view('pages.services.index', compact(
            'services',
            'featuredServices',
            'categories',
            'stats',
            'testimonials',
            'recentProjects',
            'search',
            'category',
            'sortBy'
        ));
    }

    /**
     * Display a specific service detail page.
     */
    public function show($slug)
    {
        // Find service by slug
        $service = Service::where('slug', $slug)
            ->where('is_active', true)
            ->with(['category'])
            ->firstOrFail();

        // Get projects that use this service
        $serviceProjects = Project::where('is_active', true)
            ->where('status', 'completed')
            ->where('service_id', $service->id)
            ->with(['category', 'client'])
            ->latest()
            ->limit(6)
            ->get();

        // Get related services
        $relatedServices = Service::where('is_active', true)
            ->where('id', '!=', $service->id)
            ->where(function($query) use ($service) {
                if ($service->category_id) {
                    $query->where('category_id', $service->category_id);
                } else {
                    $query->where('featured', true);
                }
            })
            ->orderBy('featured', 'desc')
            ->limit(3)
            ->get();

        // Get service features
        $features = $this->getServiceFeatures($service);
        
        // Get service FAQ
        $faqs = $this->getServiceFaq($service);
        
        // Get service process steps
        $processSteps = $this->getServiceProcess($service);

        return view('pages.services.show', compact(
            'service',
            'serviceProjects', 
            'relatedServices',
            'features',
            'faqs',
            'processSteps'
        ));
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

        // Default features structure if none available
        return [
            [
                'icon' => 'check-circle',
                'title' => 'Professional Quality',
                'description' => 'High-quality workmanship with international standards and attention to detail.'
            ],
            [
                'icon' => 'clock',
                'title' => 'Timely Delivery',
                'description' => 'On-time project completion according to agreed schedules and milestones.'
            ],
            [
                'icon' => 'shield-check',
                'title' => 'Quality Guarantee',
                'description' => 'Comprehensive warranty and customer satisfaction guarantee on all work.'
            ],
            [
                'icon' => 'users',
                'title' => 'Expert Team',
                'description' => 'Skilled professionals with years of experience in the industry.'
            ],
            [
                'icon' => 'tools',
                'title' => 'Modern Equipment',
                'description' => 'Latest technology and equipment for efficient and precise execution.'
            ],
            [
                'icon' => 'dollar-sign',
                'title' => 'Competitive Pricing',
                'description' => 'Fair and transparent pricing with no hidden costs or surprises.'
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

        // Default FAQ structure if none available
        return [
            [
                'question' => 'How long does the project take to complete?',
                'answer' => 'Project duration varies depending on complexity and scope. We provide accurate time estimates after initial evaluation and planning.'
            ],
            [
                'question' => 'Do you provide warranty for your work?',
                'answer' => 'Yes, we provide comprehensive warranty for all our services according to industry standards and contract agreements.'
            ],
            [
                'question' => 'How do I start a project with you?',
                'answer' => 'Contact us for a free initial consultation. Our team will conduct a site survey and provide a detailed proposal tailored to your needs.'
            ],
            [
                'question' => 'Do you handle permits and legal requirements?',
                'answer' => 'Yes, we assist with all necessary permits, licenses, and legal requirements to ensure your project complies with local regulations.'
            ],
            [
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept various payment methods including bank transfers, checks, and installment plans for larger projects.'
            ],
        ];
    }

    /**
     * Get service process steps.
     */
    private function getServiceProcess(Service $service)
    {
        // If you have a process field (JSON) in the services table
        if (isset($service->process) && is_array($service->process)) {
            return $service->process;
        }

        // Default process structure
        return [
            [
                'step' => 1,
                'title' => 'Initial Consultation',
                'description' => 'Free consultation to understand your needs and project requirements.',
                'icon' => 'chat-alt-2'
            ],
            [
                'step' => 2,
                'title' => 'Site Survey & Analysis',
                'description' => 'Detailed site inspection and technical analysis for accurate planning.',
                'icon' => 'search'
            ],
            [
                'step' => 3,
                'title' => 'Design & Proposal',
                'description' => 'Custom design solutions with detailed proposal and cost estimation.',
                'icon' => 'pencil-alt'
            ],
            [
                'step' => 4,
                'title' => 'Project Execution',
                'description' => 'Professional implementation with regular progress updates and quality control.',
                'icon' => 'cog'
            ],
            [
                'step' => 5,
                'title' => 'Quality Inspection',
                'description' => 'Thorough quality inspection and testing before project handover.',
                'icon' => 'badge-check'
            ],
            [
                'step' => 6,
                'title' => 'Project Handover',
                'description' => 'Final handover with documentation, warranty, and maintenance guidelines.',
                'icon' => 'hand'
            ],
        ];
    }
}