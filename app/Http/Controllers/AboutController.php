<?php
// File: app/Http/Controllers/AboutController.php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use App\Models\TeamMemberDepartment;
use Illuminate\Http\Request;

class AboutController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        // Set page meta
        $this->setPageMeta(
            'About Us - ' . $this->siteConfig['site_title'],
            $this->companyProfile->about ?? 'Learn more about our company.',
            'about us, company profile, team, vision, mission'
        );

        // Set breadcrumb
        $this->setBreadcrumb([
            ['name' => 'About', 'url' => route('about.index')]
        ]);
        
        // Get featured team members
        $featuredTeamMembers = TeamMember::with('department')
            ->active()
            ->featured()
            ->ordered()
            ->limit(8)
            ->get();
        
        // Get departments dengan count
        $departments = TeamMemberDepartment::withCount(['activeTeamMembers'])
            ->active()
            ->ordered()
            ->get();
        
        // Company statistics
        $statistics = [
            'completed_projects' => \App\Models\Project::where('status', 'completed')->count(),
            'active_clients' => \App\Models\Project::distinct('client_id')->count(),
            'team_members' => TeamMember::active()->count(),
            'years_experience' => now()->year - ($this->companyProfile->founded_year ?? 2010),
        ];

        return view('pages.about.index', compact(
            'featuredTeamMembers',
            'departments',
            'statistics'
        ));
    }

    public function team()
    {
        // Set page meta
        $this->setPageMeta(
            'Our Team - ' . $this->siteConfig['site_title'],
            'Meet our professional team members and their expertise.',
            'team, staff, professionals, expertise'
        );

        // Set breadcrumb
        $this->setBreadcrumb([
            ['name' => 'About', 'url' => route('about.index')],
            ['name' => 'Team', 'url' => route('about.team')]
        ]);

        // Get team members by department
        $departments = TeamMemberDepartment::with(['activeTeamMembers' => function ($query) {
            $query->ordered();
        }])
        ->active()
        ->ordered()
        ->get();

        return view('pages.about.team', compact('departments'));
    }
}