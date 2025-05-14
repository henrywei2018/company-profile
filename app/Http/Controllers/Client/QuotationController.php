<?php
// File: app/Http/Controllers/Client/QuotationController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\Service;
use App\Services\QuotationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuotationController extends Controller
{
    protected $quotationService;

    /**
     * Create a new controller instance.
     *
     * @param QuotationService $quotationService
     */
    public function __construct(QuotationService $quotationService)
    {
        $this->quotationService = $quotationService;
    }

    /**
     * Display a listing of the client's quotations.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Use service to get filtered quotations
        $quotations = $this->quotationService->getClientQuotations(
            $user, 
            $request->only(['status', 'service'])
        );
        
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
        $validated = $request->validate([
            'service_id' => 'nullable|exists:services,id',
            'project_type' => 'required|string|max:255',
            'requirements' => 'required|string',
            'location' => 'nullable|string|max:255',
            'budget_range' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max
        ]);
        
        $user = auth()->user();
        
        // Prepare quotation data
        $quotationData = array_merge($validated, [
            'client_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'company' => $user->company,
            'status' => 'pending',
        ]);
        
        // Use service to create quotation with attachments
        $quotation = $this->quotationService->createQuotation(
            $quotationData,
            $request->file('attachments') ?? []
        );
        
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
        
        // Update quotation basic info
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
        
        return redirect()->route('client.quotations.show', $quotation)
            ->with('success', 'Additional information provided successfully!');
    }
    
    /**
     * Download attachment
     */
    public function downloadAttachment(Quotation $quotation, $attachmentId)
    {
        // Ensure the quotation belongs to the authenticated client
        $this->authorize('view', $quotation);
        
        $attachment = $quotation->attachments()->findOrFail($attachmentId);
        
        return Storage::disk('public')->download(
            $attachment->file_path,
            $attachment->file_name
        );
    }
    
    /**
     * Approve a quotation.
     */
    public function approve(Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated client
        $this->authorize('view', $quotation);
        
        // Check if quotation is approved
        if ($quotation->status !== 'approved') {
            return redirect()->route('client.quotations.show', $quotation)
                ->with('error', 'Only approved quotations can be accepted.');
        }
        
        // Use service to process approval
        $this->quotationService->clientApproval($quotation, true);
        
        return redirect()->route('client.quotations.show', $quotation)
            ->with('success', 'Quotation approved successfully! We will contact you shortly to proceed with the project.');
    }
    
    /**
     * Show decline confirmation form.
     */
    public function showDeclineForm(Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated client
        $this->authorize('view', $quotation);
        
        // Check if quotation is approved
        if ($quotation->status !== 'approved') {
            return redirect()->route('client.quotations.show', $quotation)
                ->with('error', 'Only approved quotations can be declined.');
        }
        
        return view('client.quotations.decline', compact('quotation'));
    }
    
    /**
     * Decline a quotation.
     */
    public function decline(Request $request, Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated client
        $this->authorize('view', $quotation);
        
        // Check if quotation is approved
        if ($quotation->status !== 'approved') {
            return redirect()->route('client.quotations.show', $quotation)
                ->with('error', 'Only approved quotations can be declined.');
        }
        
        $validated = $request->validate([
            'decline_reason' => 'required|string|max:500',
        ]);
        
        // Use service to process decline
        $this->quotationService->clientApproval($quotation, false, $validated['decline_reason']);
        
        return redirect()->route('client.quotations.show', $quotation)
            ->with('success', 'Quotation declined. Thank you for considering our services.');
    }
}