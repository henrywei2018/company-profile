<?php
// File: app/Http/Controllers/PortfolioController.php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;

class PortfolioController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request)
    {
        // Set page meta
        $this->setPageMeta(
            'Portfolio - ' . $this->siteConfig['site_title'],
            'Browse our completed projects and portfolio showcasing our expertise and quality work.',
            'portfolio, projects, completed work, showcase'
        );

        // Set breadcrumb
        $this->setBreadcrumb([
            ['name' => 'Portfolio', 'url' => route('portfolio.index')]
        ]);

        // Ambil semua kategori dengan project count
        $categories = ProjectCategory::withCount(['activeProjects'])->get();

        // Query project dengan filter
        $query = Project::query()
            ->where('is_active', true)
            ->where('status', 'completed')
            ->with(['category', 'images']);

        if ($request->filled('category')) {
            $category = ProjectCategory::where('slug', $request->category)->first();
            if ($category) {
                $query->where('project_category_id', $category->id);
            }
        }

        $projects = $query->orderByDesc('featured')
                         ->latest('end_date')
                         ->paginate(9);

        return view('pages.portfolio.index', compact(
            'projects',
            'categories'
        ));
    }

    public function show(Project $project)
    {
        // Hanya tampilkan project yang aktif & completed
        if (!$project->is_active || $project->status !== 'completed') {
            abort(404);
        }

        // Set page meta
        $this->setPageMeta(
            $project->title . ' - Portfolio',
            $project->description,
            'project, portfolio, ' . $project->title
        );

        // Set breadcrumb
        $this->setBreadcrumb([
            ['name' => 'Portfolio', 'url' => route('portfolio.index')],
            ['name' => $project->title, 'url' => route('portfolio.show', $project->slug)]
        ]);

        // Load relasi
        $project->load([
            'category',
            'client',
            'images' => function ($query) {
                $query->orderBy('sort_order')->orderBy('created_at');
            },
            'testimonials' => function ($query) {
                $query->where('is_active', true)->where('featured', true);
            }
        ]);

        // Ambil project terkait
        $relatedProjects = Project::where('is_active', true)
            ->where('status', 'completed')
            ->where('id', '!=', $project->id)
            ->when($project->project_category_id, function ($query) use ($project) {
                $query->where('project_category_id', $project->project_category_id);
            })
            ->with(['category', 'images'])
            ->orderByDesc('featured')
            ->limit(3)
            ->get();

        return view('pages.portfolio.show', compact(
            'project',
            'relatedProjects'
        ));
    }
}