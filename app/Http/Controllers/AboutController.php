<?php
// File: app/Http/Controllers/AboutController.php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use App\Models\TeamMember;
use App\Models\Certification;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    /**
     * Display the about us page.
     */
    public function index()
    {
        // Get company profile
        $companyProfile = CompanyProfile::getInstance();
        
        // Get team members
        $teamMembers = TeamMember::active()->ordered()->get();
        
        // Get certifications
        $certifications = Certification::active()->valid()->ordered()->get();
        
        return view('pages.about', compact(
            'companyProfile',
            'teamMembers',
            'certifications'
        ));
    }
    
    /**
     * Display the team page.
     */
    public function team()
    {
        // Get team members
        $teamMembers = TeamMember::active()->ordered()->get();
        
        // Get company profile
        $companyProfile = CompanyProfile::getInstance();
        
        return view('pages.team', compact(
            'teamMembers',
            'companyProfile'
        ));
    }
}