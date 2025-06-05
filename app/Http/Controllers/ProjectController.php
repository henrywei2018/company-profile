<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects.
     */
    public function index(Request $request)
    {
        $query = Project::query()
            ->with(['category', 'images', 'client'])
            ->where('is_active', true);

        // Apply filters
        if ($request->filled('category')) {
            $query->where('project_category_id', $request->category);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $projects = $query->orderByDesc('featured')
                         ->orderByDesc('created_at')
                         ->paginate(12);

        $categories = ProjectCategory::whereHas('projects', function ($query) {
            $query->where('is_active', true);
        })->get();

        return view('projects.index', compact('projects', 'categories'));
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project)
    {
        // Check if project is active
        if (!$project->is_active) {
            abort(404);
        }

        $project->load([
            'category',
            'client',
            'images' => function ($query) {
                $query->orderBy('sort_order')->orderBy('created_at');
            },
            'testimonials' => function ($query) {
                $query->where('is_active', true);
            }
        ]);

        // Get related projects
        $relatedProjects = Project::where('is_active', true)
            ->where('id', '!=', $project->id)
            ->when($project->project_category_id, function ($query) use ($project) {
                $query->where('project_category_id', $project->project_category_id);
            })
            ->with(['category', 'images'])
            ->limit(3)
            ->get();

        return view('projects.show', compact('project', 'relatedProjects'));
    }
}