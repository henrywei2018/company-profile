<?php
// File: app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Service;
use App\Models\Message;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Get counts for dashboard widgets
        $projectsCount = Project::count();
        $servicesCount = Service::count();
        $messagesCount = Message::count();
        $unreadMessagesCount = Message::unread()->count();
        $quotationsCount = Quotation::count();
        $pendingQuotationsCount = Quotation::pending()->count();
        $clientsCount = User::role('client')->count();
        
        // Get recent activities
        $recentMessages = Message::latest()->take(5)->get();
        $recentQuotations = Quotation::latest()->take(5)->get();
        
        return view('layouts.admin', compact(
            'projectsCount',
            'servicesCount',
            'messagesCount',
            'unreadMessagesCount',
            'quotationsCount',
            'pendingQuotationsCount',
            'clientsCount',
            'recentMessages',
            'recentQuotations'
        ));
    }
}