<?php
// File: app/Http/Controllers/Admin/QuotationController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    /**
     * Display a listing of the quotations.
     */
    public function index(Request $request)
    {
        $quotations = Quotation::with(['service', 'client'])
            ->when($request->filled('status'), function ($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->when($request->filled('service'), function ($query) use ($request) {
                return $query->where('service_id', $request->service);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                return $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%")
                      ->orWhere('email', 'like', "%{$request->search}%")
                      ->orWhere('company', 'like', "%{$request->search}%");
                });
            })
            ->latest()
            ->paginate(15);
        
        $services = Service::all();
        $statuses = ['pending', 'reviewed', 'approved', 'rejected'];
        
        return view('admin.quotations.index', compact('quotations', 'services', 'statuses'));
    }

    /**
     * Display the specified quotation.
     */
    public function show(Quotation $quotation)
    {
        $quotation->load(['service', 'client']);
        
        return view('admin.quotations.show', compact('quotation'));
    }

    /**
     * Show the form for editing the specified quotation.
     */
    public function edit(Quotation $quotation)
    {
        $quotation->load(['service', 'client']);
        $services = Service::all();
        $clients = User::role('client')->get();
        
        return view('admin.quotations.edit', compact('quotation', 'services', 'clients'));
    }

    /**
     * Update the specified quotation.
     */
    public function update(Request $request, Quotation $quotation)
    {
        $validated = $request->validate([
            'service_id' => 'nullable|exists:services,id',
            'client_id' => 'nullable|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'project_type' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'requirements' => 'nullable|string',
            'budget_range' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'status' => 'required|in:pending,reviewed,approved,rejected',
            'admin_notes' => 'nullable|string',
        ]);
        
        // Update quotation
        $quotation->update($validated);
        
        return redirect()->route('admin.quotations.index')
            ->with('success', 'Quotation updated successfully!');
    }

    /**
     * Remove the specified quotation.
     */
    public function destroy(Quotation $quotation)
    {
        $quotation->delete();
        
        return redirect()->route('admin.quotations.index')
            ->with('success', 'Quotation deleted successfully!');
    }
    
    /**
     * Update quotation status.
     */
    public function updateStatus(Request $request, Quotation $quotation)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewed,approved,rejected',
            'admin_notes' => 'nullable|string',
        ]);
        
        $quotation->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ]);
        
        // If status is approved, we might want to create a project or send notification
        if ($request->status === 'approved') {
            // Implement logic for approved quotations
            // This could include creating a project, sending confirmation emails, etc.
        }
        
        return redirect()->back()
            ->with('success', 'Quotation status updated to ' . ucfirst($request->status));
    }
}