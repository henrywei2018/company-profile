<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PortfolioController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display the portfolio index page with filtering
     */
    public function index(Request $request)
    {
        // Set page meta for SEO
        $this->setPageMeta(
            'Our Portfolio - ' . $this->siteConfig['site_title'],
            'Explore our completed construction and engineering projects. See the quality and craftsmanship we deliver.',
            'construction portfolio, completed projects, construction gallery, engineering projects',
            asset($this->siteConfig['site_logo'])
        );

        // Get filter parameters
        $category = $request->get('category');
        $service = $request->get('service');
        $year = $request->get('year');
        $search = $request->get('search');
        $sortBy = $request->get('sort', 'latest');
        $perPage = $request->get('per_page', 12);

        // Build projects query
        $projectsQuery = Project::where('is_active', true)
            ->where('status', 'completed')
            ->with(['category', 'service', 'client', 'images' => function($query) {
                $query->orderBy('is_featured', 'desc')->orderBy('sort_order', 'asc');
            }]);

        // Apply search filter
        if ($search) {
            $projectsQuery->where(function($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Apply category filter
        if ($category && $category !== 'all') {
            $projectsQuery->whereHas('category', function($query) use ($category) {
                $query->where('slug', $category);
            });
        }

        // Apply service filter
        if ($service && $service !== 'all') {
            $projectsQuery->whereHas('service', function($query) use ($service) {
                $query->where('slug', $service);
            });
        }

        // Apply year filter
        if ($year && $year !== 'all') {
            $projectsQuery->where('year', $year);
        }

        // Apply sorting
        switch ($sortBy) {
            case 'title':
                $projectsQuery->orderBy('title', 'asc');
                break;
            case 'oldest':
                $projectsQuery->orderBy('created_at', 'asc');
                break;
            case 'featured':
                $projectsQuery->orderBy('featured', 'desc')
                             ->orderBy('created_at', 'desc');
                break;
            case 'latest':
            default:
                $projectsQuery->orderBy('created_at', 'desc');
                break;
        }

        // Get paginated projects
        $projects = $projectsQuery->paginate($perPage)->withQueryString();

        // Get featured projects for hero section
        $featuredProjects = Cache::remember('featured_projects_portfolio', 1800, function () {
            return Project::where('is_active', true)
                ->where('status', 'completed')
                ->where('featured', true)
                ->with(['category', 'service', 'images' => function($query) {
                    $query->orderBy('is_featured', 'desc')->orderBy('sort_order', 'asc');
                }])
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
        });

        // Get categories for filter
        $categories = Cache::remember('project_categories_filter', 1800, function () {
            return ProjectCategory::where('is_active', true)
                ->withCount(['activeProjects'])
                ->orderBy('name', 'asc')
                ->get();
        });

        // Get services for filter
        $services = Cache::remember('services_for_projects', 1800, function () {
            return Service::where('is_active', true)
                ->whereHas('projects', function($query) {
                    $query->where('is_active', true)->where('status', 'completed');
                })
                ->orderBy('title', 'asc')
                ->get();
        });

        // Get available years
        $years = Cache::remember('project_years', 1800, function () {
            return Project::where('is_active', true)
                ->where('status', 'completed')
                ->whereNotNull('year')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year');
        });

        // Get portfolio statistics
        $stats = [
            'total_projects' => Project::where('status', 'completed')->count(),
            'active_categories' => ProjectCategory::where('is_active', true)->count(),
            'satisfied_clients' => Project::where('status', 'completed')->distinct('client_id')->count('client_id'),
            'years_experience' => now()->year - 2010, // Adjust base year as needed
        ];

        return view('pages.portfolio.index', compact(
            'projects',
            'featuredProjects',
            'categories',
            'services',
            'years',
            'stats',
            'search',
            'category',
            'service',
            'year',
            'sortBy'
        ));
    }

    /**
     * Display a specific project
     */
    public function show($slug)
    {
        // Find project by slug
        $project = Project::where('slug', $slug)
            ->where('is_active', true)
            ->where('status', 'completed')
            ->with(['category', 'service', 'client', 'images' => function($query) {
                $query->orderBy('is_featured', 'desc')->orderBy('sort_order', 'asc');
            }, 'milestones' => function($query) {
                $query->orderBy('sort_order', 'asc')->orderBy('due_date', 'asc');
            }])
            ->firstOrFail();

        // Set page meta for SEO
        $this->setPageMeta(
            $project->title . ' - Portfolio - ' . $this->siteConfig['site_title'],
            $project->short_description ?: 'View details of our ' . $project->title . ' project.',
            $project->title . ', construction project, portfolio',
            $project->featured_image_url ?: asset($this->siteConfig['site_logo'])
        );

        // Get related projects
        $relatedProjects = Project::where('is_active', true)
            ->where('status', 'completed')
            ->where('id', '!=', $project->id)
            ->where(function($query) use ($project) {
                if ($project->category_id) {
                    $query->where('category_id', $project->category_id);
                } elseif ($project->service_id) {
                    $query->where('service_id', $project->service_id);
                } else {
                    $query->where('featured', true);
                }
            })
            ->with(['category', 'service', 'images' => function($query) {
                $query->orderBy('is_featured', 'desc')->orderBy('sort_order', 'asc');
            }])
            ->orderBy('featured', 'desc')
            ->limit(3)
            ->get();

        return view('pages.portfolio.show', compact(
            'project',
            'relatedProjects'
        ));
    }
}