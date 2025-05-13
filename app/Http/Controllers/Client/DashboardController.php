<?php
// File: app/Http/Controllers/Client/DashboardController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Message;
use App\Models\Quotation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the client dashboard.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get counts for dashboard widgets
        $projectsCount = Project::where('client_id', $user->id)->count();
        $activeProjectsCount = Project::where('client_id', $user->id)
            ->where('status', 'active')
            ->count();
        $quotationsCount = Quotation::where('client_id', $user->id)->count();
        $pendingQuotationsCount = Quotation::where('client_id', $user->id)
            ->where('status', 'pending')
            ->count();
        $messagesCount = Message::where('client_id', $user->id)->count();
        $unreadMessagesCount = Message::where('client_id', $user->id)
            ->where('is_read_by_client', false)
            ->count();
        
        // Get recent activities
        $recentProjects = Project::where('client_id', $user->id)
            ->latest()
            ->take(3)
            ->get();
            
        $recentQuotations = Quotation::where('client_id', $user->id)
            ->latest()
            ->take(3)
            ->get();
            
        $recentMessages = Message::where('client_id', $user->id)
            ->latest()
            ->take(5)
            ->get();
        
        return view('client.dashboard', compact(
            'projectsCount',
            'activeProjectsCount',
            'quotationsCount',
            'pendingQuotationsCount',
            'messagesCount',
            'unreadMessagesCount',
            'recentProjects',
            'recentQuotations',
            'recentMessages'
        ));
    }
}