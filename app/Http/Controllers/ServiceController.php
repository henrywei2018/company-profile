<?php
// File: app/Http/Controllers/ServiceController.php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of services.
     */
    public function index(Request $request)
    {
        // Get service categories
        $categories = ServiceCategory::active()->with('services')->get();
        
        // Get services, optionally filtered by category
        $services = Service::active()
            ->when($request->filled('category'), function ($query) use ($request) {
                return $query->whereHas('category', function ($q) use ($request) {
                    $q->where('slug', $request->category);
                });
            })
            ->paginate(9);
        
        return view('pages.services', compact('services', 'categories'));
    }
    
    /**
     * Display the specified service.
     */
    public function show($slug)
    {
        // Find the service by slug
        $service = Service::where('slug', $slug)->active()->firstOrFail();
        
        // Get related services
        $relatedServices = Service::active()
            ->where('id', '!=', $service->id)
            ->when($service->category_id, function ($query) use ($service) {
                return $query->where('category_id', $service->category_id);
            })
            ->take(3)
            ->get();
        
        return view('pages.service-single', compact('service', 'relatedServices'));
    }
}