<?php
// File: app/Http/Controllers/Client/QuotationController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\Service;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    /**
     * Display a listing of the client's quotations.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $quotations = Quotation::where('client_id', $user->id)
            ->when($request->filled('status'), function ($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->when($request->filled('service'), function ($query) use ($request) {
                return $query->where('service_id', $request->service);
            })
            ->latest()
            ->paginate(10);
        
        $services = Service::active()->get();
        $statuses = ['pending', 'reviewed', 'approved', 'rejected'];
        
        return view('client.quotations.index', compact('quotations', 'services', 'statuses'));
    }

    /**
     * Show the form for creating a new quotation.
     */
    public function create()
    {
        $services = Service::active()->get();
        
        return view('client.quotations.create', compact('services'));
    }

    /**
     * Store a newly created quotation.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'service_id' => 'nullable|exists:services,id',
            'project_type' => 'required|string|max:255',
            'requirements' => 'required|string',
            'location' => 'nullable|string|max:255',
            'budget_range' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max
        ]);
        
        // Create quotation
        $quotation = Quotation::create([
            'client_id' => $user->id,
            'service_id' => $validated['service_id'],
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'company' => $user->company,
            'project_type' => $validated['project_type'],
            'requirements' => $validated['requirements'],
            'location' => $validated['location'],
            'budget_range' => $validated['budget_range'],
            'start_date' => $validated['start_date'],
            'status' => 'pending',
        ]);
        
        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('quotation_attachments/' . $quotation->id, 'public');
                
                $quotation->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getMimeType(),
                ]);
            }
        }
        
        // Send notification to admin (to be implemented)
        // Notification::route('mail', config('mail.admin'))
        //    ->notify(new QuotationSubmittedNotification($quotation));
        
        return redirect()->route('client.quotations.index')
            ->with('success', 'Quotation request submitted successfully!');
    }

    /**
     * Display the specified quotation.
     */
    public function show(Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated client
        $this->authorize('view', $quotation);
        
        $quotation->load(['service', 'attachments']);
        
        return view('client.quotations.show', compact('quotation'));
    }
    
    /**
     * Show form to provide additional information for a quotation.
     */
    public function showAdditionalInfoForm(Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated client
        $this->authorize('update', $quotation);
        
        // Check if quotation is pending or reviewed
        if (!in_array($quotation->status, ['pending', 'reviewed'])) {
            return redirect()->route('client.quotations.show', $quotation)
                ->with('error', 'Additional information can only be provided for pending or reviewed quotations.');
        }
        
        return view('client.quotations.additional-info', compact('quotation'));
    }
    
    /**
     * Update a quotation with additional information.
     */
    public function updateAdditionalInfo(Request $request, Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated client
        $this->authorize('update', $quotation);
        
        // Check if quotation is pending or reviewed
        if (!in_array($quotation->status, ['pending', 'reviewed'])) {
            return redirect()->route('client.quotations.show', $quotation)
                ->with('error', 'Additional information can only be provided for pending or reviewed quotations.');
        }
        
        $validated = $request->validate([
            'additional_info' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max
        ]);
        
        // Update quotation
        $quotation->update([
            'additional_info' => $validated['additional_info'],
            'status' => 'pending', // Reset to pending for review
        ]);
        
        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('quotation_attachments/' . $quotation->id, 'public');
                
                $quotation->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getMimeType(),
                ]);
            }
        }
        
        // Send notification to admin (to be implemented)
        // Notification::route('mail', config('mail.admin'))
        //    ->notify(new QuotationUpdatedNotification($quotation));
        
        return redirect()->route('client.quotations.show', $quotation)
            ->with('success', 'Additional information provided successfully!');
    }
    
    /**
     * Approve a quotation.
     */
    public function approve(Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated client
        $this->authorize('update', $quotation);
        
        // Check if quotation is approved
        if ($quotation->status !== 'approved') {
            return redirect()->route('client.quotations.show', $quotation)
                ->with('error', 'Only approved quotations can be accepted.');
        }
        
        // Update quotation
        $quotation->update([
            'client_approved' => true,
            'client_approved_at' => now(),
        ]);
        
        // Send notification to admin (to be implemented)
        // Notification::route('mail', config('mail.admin'))
        //    ->notify(new QuotationApprovedNotification($quotation));
        
        return redirect()->route('client.quotations.show', $quotation)
            ->with('success', 'Quotation approved successfully! We will contact you shortly to proceed with the project.');
    }
    
    /**
     * Decline a quotation.
     */
    public function decline(Request $request, Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated client
        $this->authorize('update', $quotation);
        
        // Check if quotation is approved
        if ($quotation->status !== 'approved') {
            return redirect()->route('client.quotations.show', $quotation)
                ->with('error', 'Only approved quotations can be declined.');
        }
        
        $validated = $request->validate([
            'decline_reason' => 'required|string|max:500',
        ]);
        
        // Update quotation
        $quotation->update([
            'client_approved' => false,
            'client_decline_reason' => $validated['decline_reason'],
            'client_approved_at' => now(),
        ]);
        
        // Send notification to admin (to be implemented)
        // Notification::route('mail', config('mail.admin'))
        //    ->notify(new QuotationDeclinedNotification($quotation));
        
        return redirect()->route('client.quotations.show', $quotation)
            ->with('success', 'Quotation declined. Thank you for considering our services.');
    }
}