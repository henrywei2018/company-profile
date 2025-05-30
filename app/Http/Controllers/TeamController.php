<?php
// File: app/Http/Controllers/TeamController.php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    /**
     * Display the team page.
     */
    public function index()
    {
        // Get featured team members
        $featuredMembers = TeamMember::active()
            ->featured()
            ->ordered()
            ->get();
            
        // Get other team members
        $regularMembers = TeamMember::active()
            ->notFeatured()
            ->ordered()
            ->get();
            
        // Combine into departments/categories if needed
        $departments = TeamMember::active()
            ->select('department')
            ->distinct()
            ->pluck('department')
            ->filter();
            
        $teamByDepartment = [];
        
        if ($departments->count() > 0) {
            foreach ($departments as $department) {
                $teamByDepartment[$department] = TeamMember::active()
                    ->where('department', $department)
                    ->ordered()
                    ->get();
            }
        }
        
        return view('pages.team', compact(
            'featuredMembers', 
            'regularMembers', 
            'departments', 
            'teamByDepartment'
        ));
    }
    
    /**
     * Display the specified team member.
     */
    public function show($slug)
    {
        // Find the team member by slug
        $teamMember = TeamMember::where('slug', $slug)
            ->active()
            ->firstOrFail();
            
        // Get related team members (same department)
        $relatedMembers = TeamMember::active()
            ->where('id', '!=', $teamMember->id)
            ->when($teamMember->department, function ($query) use ($teamMember) {
                return $query->where('department', $teamMember->department);
            })
            ->take(3)
            ->get();
            
        return view('pages.team-member', compact('teamMember', 'relatedMembers'));
    }
}