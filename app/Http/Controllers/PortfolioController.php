<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;

class PortfolioController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->shareBaseData();
    }
    // Menampilkan list project portfolio
    public function index(Request $request)
    {
        // Ambil semua kategori unik
        $categories = ProjectCategory::withCount('projects')->get();

        // Query project, filter by category jika ada
        $query = Project::query()
            ->where('is_active', true)
            ->where('status', 'completed')
            ->with(['category', 'images']);

        if ($request->filled('category')) {
            // Filter berdasar slug kategori, jika ada request category
            $category = ProjectCategory::where('slug', $request->category)->first();
            if ($category) {
                $query->where('project_category_id', $category->id);
            }
        }

        $projects = $query->latest('end_date')->paginate(9);

        return view('pages.portfolio.index', [
            'projects' => $projects,
            'categories' => $categories,
            'selectedCategory' => $request->category ?? null,
        ]);
    }

    // Menampilkan detail project
    public function show(Project $project)
    {
        // Hanya tampilkan project yang aktif & completed
        if (!$project->is_active || $project->status !== 'completed') {
            abort(404);
        }

        // Load relasi (category, client, images, testimonial aktif & featured)
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

        return view('pages.portfolio.show', [
            'project' => $project,
            'relatedProjects' => $relatedProjects,
        ]);
    }
}
