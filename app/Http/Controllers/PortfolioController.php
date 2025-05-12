<?php
// File: app/Http/Controllers/PortfolioController.php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    /**
     * Display a listing of projects.
     */
    public function index(Request $request)
    {
        // Get all distinct categories
        $categories = Project::select('category')
            ->distinct()
            ->pluck('category')
            ->filter();
            
        // Get all distinct years
        $years = Project::select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->filter();
        
        // Apply filters and pagination
        $projects = Project::with('images')
            ->filter($request->only(['category', 'year']))
            ->latest()
            ->paginate(9);
        
        return view('pages.portfolio', compact('projects', 'categories', 'years'));
    }
    
    /**
     * Display the specified project.
     */
    public function show($slug)
    {
        // Find the project by slug
        $project = Project::where('slug', $slug)
            ->with(['images', 'testimonial'])
            ->firstOrFail();
        
        // Get related projects
        $relatedProjects = Project::where('id', '!=', $project->id)
            ->where('category', $project->category)
            ->take(3)
            ->get();
        
        return view('pages.project-single', compact('project', 'relatedProjects'));
    }
}