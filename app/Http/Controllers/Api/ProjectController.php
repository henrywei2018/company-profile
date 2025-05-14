<?php
// File: app/Http/Controllers/Api/ProjectController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProjectController extends Controller
{
    protected $projectRepository;

    /**
     * Create a new controller instance.
     *
     * @param ProjectRepositoryInterface $projectRepository
     */
    public function __construct(ProjectRepositoryInterface $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    /**
     * Display a listing of the projects.
     *
     * @param Request $request
     * @return ResourceCollection
     */
    public function index(Request $request)
    {
        $projects = Project::query()
            ->when($request->filled('category'), function ($query) use ($request) {
                return $query->where('category', $request->category);
            })
            ->when($request->filled('year'), function ($query) use ($request) {
                return $query->where('year', $request->year);
            })
            ->when($request->filled('featured'), function ($query) {
                return $query->where('featured', true);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                return $query->where(function ($q) use ($request) {
                    $q->where('title', 'like', "%{$request->search}%")
                      ->orWhere('description', 'like', "%{$request->search}%")
                      ->orWhere('client_name', 'like', "%{$request->search}%");
                });
            })
            ->with('images')
            ->latest()
            ->paginate($request->input('per_page', 12));
        
        return ProjectResource::collection($projects);
    }

    /**
     * Display the specified project.
     *
     * @param string $slug
     * @return ProjectResource
     */
    public function show($slug)
    {
        $project = $this->projectRepository->findBySlug($slug);
        
        if (!$project) {
            return response()->json([
                'message' => 'Project not found'
            ], 404);
        }
        
        return new ProjectResource($project);
    }
    
    /**
     * Get featured projects.
     *
     * @param Request $request
     * @return ResourceCollection
     */
    public function featured(Request $request)
    {
        $limit = $request->input('limit', 6);
        
        $projects = $this->projectRepository->getFeatured($limit);
        
        return ProjectResource::collection($projects);
    }
    
    /**
     * Get related projects.
     *
     * @param string $slug
     * @param Request $request
     * @return ResourceCollection
     */
    public function related($slug, Request $request)
    {
        $project = $this->projectRepository->findBySlug($slug);
        
        if (!$project) {
            return response()->json([
                'message' => 'Project not found'
            ], 404);
        }
        
        $limit = $request->input('limit', 3);
        
        $relatedProjects = $this->projectRepository->getRelated($project, $limit);
        
        return ProjectResource::collection($relatedProjects);
    }
    
    /**
     * Get project categories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function categories()
    {
        $categories = Project::select('category')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();
            
        return response()->json([
            'data' => $categories
        ]);
    }
    
    /**
     * Get project years.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function years()
    {
        $years = Project::select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->filter()
            ->values();
            
        return response()->json([
            'data' => $years
        ]);
    }
}