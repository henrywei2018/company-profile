<?php
// File: app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Service;
use App\Models\Message;
use App\Models\Quotation;
use App\Models\User;
use App\Models\CompanyProfile;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Calculate period for change statistics
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        
        // Get counts for dashboard widgets
        $totalProjects = Project::count();
        $projectsLastMonth = Project::where('created_at', '<', $currentMonth)
            ->where('created_at', '>=', $lastMonth)
            ->count();
        $projectsThisMonth = Project::where('created_at', '>=', $currentMonth)->count();
        $projectsChange = $projectsLastMonth > 0 
            ? round(($projectsThisMonth - $projectsLastMonth) / $projectsLastMonth * 100) 
            : 0;
        
        // Clients statistics
        $activeClients = User::role('client')->count();
        $clientsLastMonth = User::role('client')
            ->where('created_at', '<', $currentMonth)
            ->where('created_at', '>=', $lastMonth)
            ->count();
        $clientsThisMonth = User::role('client')
            ->where('created_at', '>=', $currentMonth)
            ->count();
        $clientsChange = $clientsLastMonth > 0 
            ? round(($clientsThisMonth - $clientsLastMonth) / $clientsLastMonth * 100) 
            : 0;
        
        // Messages and quotations
        $unreadMessages = Message::unread()->count();
        $pendingQuotations = Quotation::pending()->count();
        
        // Get recent activities
        $recentMessages = Message::latest()->take(5)->get();
        $recentQuotations = Quotation::latest()->take(5)->get();
        $recentProjects = Project::with('client')->latest()->take(5)->get();
        
        // Get company profile
        $companyProfile = CompanyProfile::getInstance();
        
        // Enable charts for dashboard
        $enableCharts = true;
        
        return view('admin.dashboard', compact(
            'totalProjects',
            'activeClients',
            'unreadMessages',
            'pendingQuotations',
            'projectsChange',
            'clientsChange',
            'recentMessages',
            'recentQuotations',
            'recentProjects',
            'companyProfile',
            'enableCharts'
        ));
    }
}