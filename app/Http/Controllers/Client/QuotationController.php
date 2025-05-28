<?php
// File: app/Http/Controllers/Client/QuotationController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\Service;
use App\Services\ClientAccessService;
use App\Services\DashboardService;
use App\Services\QuotationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class QuotationController extends Controller
{
    protected ClientAccessService $clientAccessService;
    protected DashboardService $dashboardService;
    protected QuotationService $quotationService;

    public function __construct(
        ClientAccessService $clientAccessService,
        DashboardService $dashboardService,
        QuotationService $quotationService
    ) {
        $this->clientAccessService = $clientAccessService;
        $this->dashboardService = $dashboardService;
        $this->quotationService = $quotationService;
    }

    /**
     * Display a listing of the client's quotations.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Validate filters
        $filters = $request->validate([
            'status' => 'nullable|string|in:pending,reviewed,approved,rejected',
            'service' => 'nullable|exists:services,id',
            'search' => 'nullable|string|max:255',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'sort' => 'nullable|string|in:created_at,updated_at,status,project_type',
            'direction' => 'nullable|string|in:asc,desc',
        ]);
        
        // Get quotations using service
        $quotationsQuery = $this->clientAccessService->getClientQuotations($user, $filters);
        
        // Apply sorting
        $sortField = $filters['sort'] ?? 'created_at';
        $sortDirection = $filters['direction'] ?? 'desc';
        $quotationsQuery->orderBy($sortField, $sortDirection);
        
        // Paginate results
        $quotations = $quotationsQuery->with(['service', 'attachments'])
            ->paginate(15);
        
        // Get filter options
        $services = Service::active()->get();
        $statuses = [
            'pending' => 'Pending Review',
            'reviewed' => 'Under Review', 
            'approved' => 'Approved',
            'rejected' => 'Rejected'
        ];
        
        // Get statistics
        $statistics = $this->getQuotationStatistics($user);
        
        // Get recent activities
        $dashboardData = $this->dashboardService->getDashboardData($user);
        $recentActivities = collect($dashboardData['recent_activities'] ?? [])
            ->where('type', 'quotation')
            ->take(5)
            ->values();

        return view('client.quotations.index', compact(
            'quotations',
            'services',
            'statuses',
            'statistics',
            'recentActivities',
            'filters'
        ));
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
            'start_date' => 'nullable|date|after:today',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max
            'priority' => 'nullable|string|in:low,normal,high,urgent',
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
            'priority' => $validated['priority'] ?? 'normal',
            'source' => 'client_portal',
        ]);
        
        // Use service to create quotation with attachments
        $quotation = $this->quotationService->createQuotation(
            $quotationData,
            $request->file('attachments') ?? []
        );
        
        // Clear dashboard cache
        $this->dashboardService->clearCache($user);
        
        return redirect()->route('client.quotations.show', $quotation)
            ->with('success', 'Quotation request submitted successfully! We will review it within 24 hours.');
    }

    /**
     * Display the specified quotation.
     */
    public function show(Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated client
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            abort(403, 'Unauthorized access to this quotation.');
        }
        
        $quotation->load(['service', 'attachments', 'project']);
        
        // Get quotation alerts
        $quotationAlerts = $this->getQuotationAlerts($quotation);
        
        // Get related quotations
        $relatedQuotations = $this->clientAccessService->getClientQuotations(auth()->user())
            ->where('id', '!=', $quotation->id)
            ->where('service_id', $quotation->service_id)
            ->with('service')
            ->limit(3)
            ->get();
        
        return view('client.quotations.show', compact(
            'quotation',
            'quotationAlerts',
            'relatedQuotations'
        ));
    }

    /**
     * Get quotation alerts and notifications.
     */
    protected function getQuotationAlerts(Quotation $quotation): array
    {
        $alerts = [];
        
        // Check for approval needed
        if ($quotation->status === 'approved' && !$quotation->client_approved) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Action Required',
                'message' => 'Your quotation has been approved. Please review and confirm.',
                'action' => [
                    'text' => 'Review & Approve',
                    'url' => route('client.quotations.approve', $quotation),
                ],
            ];
        }
        
        // Check for expiring quotation
        if ($quotation->status === 'approved' && 
            $quotation->approved_at && 
            $quotation->approved_at->diffInDays(now()) > 25) {
            
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Quotation Expiring Soon',
                'message' => 'This quotation will expire in a few days. Please take action.',
                'action' => [
                    'text' => 'Contact Us',
                    'url' => route('client.messages.create', [
                        'subject' => 'Quotation Extension Request - ' . $quotation->project_type
                    ]),
                ],
            ];
        }
        
        return $alerts;
    }
    
    /**
     * Show form to provide additional information for a quotation.
     */
    public function showAdditionalInfoForm(Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated client
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            abort(403, 'Unauthorized access to this quotation.');
        }
        
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
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            abort(403, 'Unauthorized access to this quotation.');
        }
        
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
            'last_communication_at' => now(),
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
        
        // Clear dashboard cache
        $this->dashboardService->clearCache(auth()->user());
        
        return redirect()->route('client.quotations.show', $quotation)
            ->with('success', 'Additional information provided successfully!');
    }
    
    /**
     * Download attachment
     */
    public function downloadAttachment(Quotation $quotation, $attachmentId)
    {
        // Ensure the quotation belongs to the authenticated client
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            abort(403, 'Unauthorized access to this quotation.');
        }
        
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
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            abort(403, 'Unauthorized access to this quotation.');
        }
        
        // Check if quotation is approved
        if ($quotation->status !== 'approved') {
            return redirect()->route('client.quotations.show', $quotation)
                ->with('error', 'Only approved quotations can be accepted.');
        }
        
        // Use service to process approval
        $this->quotationService->clientApproval($quotation, true);
        
        // Clear dashboard cache
        $this->dashboardService->clearCache(auth()->user());
        
        return redirect()->route('client.quotations.show', $quotation)
            ->with('success', 'Quotation approved successfully! We will contact you shortly to proceed with the project.');
    }
    
    /**
     * Show decline confirmation form.
     */
    public function showDeclineForm(Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated client
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            abort(403, 'Unauthorized access to this quotation.');
        }
        
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
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            abort(403, 'Unauthorized access to this quotation.');
        }
        
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
        
        // Clear dashboard cache
        $this->dashboardService->clearCache(auth()->user());
        
        return redirect()->route('client.quotations.show', $quotation)
            ->with('success', 'Quotation declined. Thank you for considering our services.');
    }

    /**
     * Get quotation statistics for API.
     */
    public function getStatistics(): JsonResponse
    {
        $user = auth()->user();
        $statistics = $this->getQuotationStatistics($user);
        
        return response()->json([
            'success' => true,
            'data' => $statistics,
        ]);
    }

    /**
     * Get quotation statistics for the client.
     */
    protected function getQuotationStatistics($user): array
    {
        $dashboardData = $this->dashboardService->getDashboardData($user);
        return $dashboardData['statistics']['quotations'] ?? [];
    }
}