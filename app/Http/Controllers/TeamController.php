<?php
// File: app/Http/Controllers/TeamController.php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use Illuminate\Http\Request;

class TeamController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        // Set page meta
        $this->setPageMeta(
            'Team - ' . $this->siteConfig['site_title'],
            'Meet our professional team members and their expertise.',
            'team, staff, professionals, expertise'
        );

        // Set breadcrumb
        $this->setBreadcrumb([
            ['name' => 'Team', 'url' => route('team.index')]
        ]);

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
            
        // Combine into departments if needed
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
        
        return view('pages.team.index', compact(
            'featuredMembers', 
            'regularMembers', 
            'departments', 
            'teamByDepartment'
        ));
    }
    
    public function show($slug)
    {
        // Find team member by slug
        $teamMember = TeamMember::where('slug', $slug)
            ->active()
            ->firstOrFail();

        // Set page meta
        $this->setPageMeta(
            $teamMember->name . ' - Team Member',
            $teamMember->bio ?? 'Meet ' . $teamMember->name . ', ' . $teamMember->position,
            'team member, ' . $teamMember->name . ', ' . $teamMember->position
        );

        // Set breadcrumb
        $this->setBreadcrumb([
            ['name' => 'Team', 'url' => route('team.index')],
            ['name' => $teamMember->name, 'url' => route('team.show', $teamMember->slug)]
        ]);
            
        // Get related team members (same department)
        $relatedMembers = TeamMember::active()
            ->where('id', '!=', $teamMember->id)
            ->when($teamMember->department, function ($query) use ($teamMember) {
                return $query->where('department', $teamMember->department);
            })
            ->take(3)
            ->get();
            
        return view('pages.team.show', compact('teamMember', 'relatedMembers'));
    }
}