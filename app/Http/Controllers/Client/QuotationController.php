<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\Service;
use App\Models\QuotationAttachment;
use App\Services\ClientAccessService;
use App\Services\DashboardService;
use App\Services\QuotationService;
use App\Facades\Notifications;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

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
     * Display a listing of the client's quotations with advanced filtering
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Validate filters with comprehensive options
        $filters = $request->validate([
            'status' => 'nullable|string|in:pending,reviewed,approved,rejected',
            'service' => 'nullable|exists:services,id',
            'search' => 'nullable|string|max:255',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'sort' => 'nullable|string|in:created_at,updated_at,status,project_type,priority,start_date',
            'direction' => 'nullable|string|in:asc,desc',
            'priority' => 'nullable|string|in:low,normal,high,urgent',
            'project_created' => 'nullable|boolean',
            'client_approved' => 'nullable|in:0,1',
            'per_page' => 'nullable|integer|min:5|max:50',
        ]);
        
        // Get quotations using service with enhanced filtering
        $quotationsQuery = $this->clientAccessService->getClientQuotations($user, $filters);
        
        // Apply additional filters
        if (!empty($filters['priority'])) {
            $quotationsQuery->where('priority', $filters['priority']);
        }
        
        if (isset($filters['project_created'])) {
            $quotationsQuery->where('project_created', $filters['project_created']);
        }
        
        if (isset($filters['client_approved'])) {
            if ($filters['client_approved'] === '1') {
                $quotationsQuery->where('client_approved', true);
            } elseif ($filters['client_approved'] === '0') {
                $quotationsQuery->where('client_approved', false);
            }
        }
        
        // Apply sorting
        $sortField = $filters['sort'] ?? 'created_at';
        $sortDirection = $filters['direction'] ?? 'desc';
        $quotationsQuery->orderBy($sortField, $sortDirection);
        
        // Paginate results
        $perPage = $filters['per_page'] ?? 15;
        $quotations = $quotationsQuery->with(['service', 'attachments', 'project'])
            ->paginate($perPage)
            ->withQueryString();
        
        // Get filter options
        $services = Service::active()->orderBy('title')->get();
        $statuses = Quotation::getStatuses();
        $priorities = Quotation::getPriorities();
        
        // Get comprehensive statistics
        $statistics = $this->getDetailedQuotationStatistics($user);
        
        // Get recent activities related to quotations
        $dashboardData = $this->dashboardService->getDashboardData($user);
        $recentActivities = collect($dashboardData['recent_activities'] ?? [])
            ->where('type', 'quotation')
            ->take(5)
            ->values();
        
        // Get alerts and notifications
        $alerts = $this->getQuotationAlerts($user);
        
        return view('client.quotations.index', compact(
            'quotations',
            'services',
            'statuses',
            'priorities',
            'statistics',
            'recentActivities',
            'alerts',
            'filters'
        ));
    }

    /**
     * Show the form for creating a new quotation
     */
    public function create()
    {
        $services = Service::active()->orderBy('title')->get();
        $user = auth()->user();
        
        // Pre-populate form with user data
        $userDefaults = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'company' => $user->company,
        ];
        
        return view('client.quotations.create', compact('services', 'userDefaults'));
    }

    /**
     * Store a newly created quotation with comprehensive validation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'nullable|exists:services,id',
            'project_type' => 'required|string|max:255',
            'requirements' => 'required|string|min:50',
            'location' => 'nullable|string|max:255',
            'budget_range' => 'nullable|string|max:100',
            'start_date' => 'nullable|date|after:today',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png,gif,zip,rar',
            'priority' => 'nullable|string|in:low,normal,high,urgent',
            'additional_info' => 'nullable|string|max:1000',
            'preferred_contact_method' => 'nullable|string|in:email,phone,whatsapp',
            'preferred_contact_time' => 'nullable|string|in:morning,afternoon,evening',
        ]);
        
        $user = auth()->user();
        
        try {
            DB::beginTransaction();
            
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
                'quotation_number' => $this->generateQuotationNumber(),
            ]);
            
            // Use service to create quotation with attachments
            $quotation = $this->quotationService->createQuotation(
                $quotationData,
                $request->file('attachments') ?? []
            );
            
            // Send notification to admin
            try {
                Notifications::send('quotation.created_by_client', $quotation);
            } catch (\Exception $e) {
                Log::warning('Failed to send quotation notification: ' . $e->getMessage());
            }
            
            DB::commit();
            
            // Clear dashboard cache
            $this->dashboardService->clearCache($user);
            
            return redirect()->route('client.quotations.show', $quotation)
                ->with('success', 'Quotation request submitted successfully! We will review it within 24 hours.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create client quotation: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to submit quotation request. Please try again.');
        }
    }

    /**
     * Display the specified quotation with comprehensive details
     */
    public function show(Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated client
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            abort(403, 'Unauthorized access to this quotation.');
        }
        
        $quotation->load(['service', 'attachments', 'project']);
        
        // Get quotation timeline
        $timeline = $this->getQuotationTimeline($quotation);
        
        // Get quotation alerts
        $quotationAlerts = $this->getQuotationSpecificAlerts($quotation);
        
        // Get related quotations
        $relatedQuotations = $this->clientAccessService->getClientQuotations(auth()->user())
            ->where('id', '!=', $quotation->id)
            ->where(function ($query) use ($quotation) {
                $query->where('service_id', $quotation->service_id)
                      ->orWhere('project_type', 'like', '%' . $quotation->project_type . '%');
            })
            ->with('service')
            ->limit(3)
            ->get();
        
        // Get available actions
        $availableActions = $this->getAvailableActions($quotation);
        
        // Calculate quotation metrics
        $metrics = $this->getQuotationMetrics($quotation);
        
        return view('client.quotations.show', compact(
            'quotation',
            'timeline',
            'quotationAlerts',
            'relatedQuotations',
            'availableActions',
            'metrics'
        ));
    }

    /**
     * Show form to edit quotation (only if status allows)
     */
    public function edit(Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated client
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            abort(403, 'Unauthorized access to this quotation.');
        }
        
        // Check if quotation can be edited
        if (!in_array($quotation->status, ['pending', 'reviewed'])) {
            return redirect()->route('client.quotations.show', $quotation)
                ->with('error', 'This quotation cannot be edited at this time.');
        }
        
        $services = Service::active()->orderBy('title')->get();
        
        return view('client.quotations.edit', compact('quotation', 'services'));
    }

    /**
     * Update quotation (limited fields for client)
     */
    public function update(Request $request, Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated client
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            abort(403, 'Unauthorized access to this quotation.');
        }
        
        // Check if quotation can be updated
        if (!in_array($quotation->status, ['pending', 'reviewed'])) {
            return redirect()->route('client.quotations.show', $quotation)
                ->with('error', 'This quotation cannot be updated at this time.');
        }
        
        $validated = $request->validate([
            'requirements' => 'required|string|min:50',
            'location' => 'nullable|string|max:255',
            'budget_range' => 'nullable|string|max:100',
            'start_date' => 'nullable|date|after:today',
            'additional_info' => 'nullable|string|max:1000',
            'preferred_contact_method' => 'nullable|string|in:email,phone,whatsapp',
            'preferred_contact_time' => 'nullable|string|in:morning,afternoon,evening',
        ]);
        
        try {
            DB::beginTransaction();
            
            $quotation->update(array_merge($validated, [
                'status' => 'pending', // Reset to pending for review
                'last_communication_at' => now(),
            ]));
            
            // Send notification to admin about update
            try {
                Notifications::send('quotation.updated_by_client', $quotation);
            } catch (\Exception $e) {
                Log::warning('Failed to send quotation update notification: ' . $e->getMessage());
            }
            
            DB::commit();
            
            // Clear dashboard cache
            $this->dashboardService->clearCache(auth()->user());
            
            return redirect()->route('client.quotations.show', $quotation)
                ->with('success', 'Quotation updated successfully! We will review the changes within 24 hours.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update client quotation: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update quotation. Please try again.');
        }
    }

    /**
     * Delete attachment from quotation
     */
    public function deleteAttachment(Quotation $quotation, QuotationAttachment $attachment)
    {
        // Ensure the quotation belongs to the authenticated client
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            abort(403, 'Unauthorized access to this quotation.');
        }
        
        // Check if quotation allows modifications
        if (!in_array($quotation->status, ['pending', 'reviewed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Attachments cannot be removed at this time.'
            ], 403);
        }
        
        try {
            // Delete file from storage
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }
            
            // Delete attachment record
            $attachment->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Attachment removed successfully.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to delete quotation attachment: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove attachment.'
            ], 500);
        }
    }

    /**
     * Download attachment
     */
    public function downloadAttachment(Quotation $quotation, QuotationAttachment $attachment)
    {
        // Ensure the quotation belongs to the authenticated client
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            abort(403, 'Unauthorized access to this quotation.');
        }
        
        if (!$attachment->exists()) {
            abort(404, 'File not found.');
        }
        
        return Storage::disk('public')->download(
            $attachment->file_path,
            $attachment->file_name
        );
    }

    /**
     * Approve a quotation
     */
    public function approve(Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated client
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            abort(403, 'Unauthorized access to this quotation.');
        }
        
        // Check if quotation can be approved
        if ($quotation->status !== 'approved') {
            return redirect()->route('client.quotations.show', $quotation)
                ->with('error', 'Only approved quotations can be accepted.');
        }
        
        if ($quotation->client_approved) {
            return redirect()->route('client.quotations.show', $quotation)
                ->with('info', 'This quotation has already been approved.');
        }
        
        try {
            // Use service to process approval
            $this->quotationService->clientApproval($quotation, true);
            
            // Clear dashboard cache
            $this->dashboardService->clearCache(auth()->user());
            
            return redirect()->route('client.quotations.show', $quotation)
                ->with('success', 'Quotation approved successfully! We will contact you shortly to proceed with the project.');
                
        } catch (\Exception $e) {
            Log::error('Failed to approve quotation: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to approve quotation. Please try again.');
        }
    }

    /**
     * Show decline confirmation form
     */
    public function showDeclineForm(Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated client
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            abort(403, 'Unauthorized access to this quotation.');
        }
        
        // Check if quotation can be declined
        if ($quotation->status !== 'approved') {
            return redirect()->route('client.quotations.show', $quotation)
                ->with('error', 'Only approved quotations can be declined.');
        }
        
        if ($quotation->client_approved === false) {
            return redirect()->route('client.quotations.show', $quotation)
                ->with('info', 'This quotation has already been declined.');
        }
        
        return view('client.quotations.decline', compact('quotation'));
    }

    /**
     * Decline a quotation
     */
    public function decline(Request $request, Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated client
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            abort(403, 'Unauthorized access to this quotation.');
        }
        
        // Check if quotation can be declined
        if ($quotation->status !== 'approved') {
            return redirect()->route('client.quotations.show', $quotation)
                ->with('error', 'Only approved quotations can be declined.');
        }
        
        $validated = $request->validate([
            'decline_reason' => 'required|string|max:500',
        ]);
        
        try {
            // Use service to process decline
            $this->quotationService->clientApproval($quotation, false, $validated['decline_reason']);
            
            // Clear dashboard cache
            $this->dashboardService->clearCache(auth()->user());
            
            return redirect()->route('client.quotations.show', $quotation)
                ->with('success', 'Quotation declined. Thank you for considering our services.');
                
        } catch (\Exception $e) {
            Log::error('Failed to decline quotation: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to decline quotation. Please try again.');
        }
    }

    /**
     * Show form to provide additional information
     */
    public function showAdditionalInfoForm(Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated client
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            abort(403, 'Unauthorized access to this quotation.');
        }
        
        // Check if additional info can be provided
        if (!in_array($quotation->status, ['pending', 'reviewed'])) {
            return redirect()->route('client.quotations.show', $quotation)
                ->with('error', 'Additional information can only be provided for pending or reviewed quotations.');
        }
        
        return view('client.quotations.additional-info', compact('quotation'));
    }

    /**
     * Update quotation with additional information
     */
    public function updateAdditionalInfo(Request $request, Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated client
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            abort(403, 'Unauthorized access to this quotation.');
        }
        
        // Check if additional info can be provided
        if (!in_array($quotation->status, ['pending', 'reviewed'])) {
            return redirect()->route('client.quotations.show', $quotation)
                ->with('error', 'Additional information can only be provided for pending or reviewed quotations.');
        }
        
        $validated = $request->validate([
            'additional_info' => 'required|string|max:2000',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png,gif,zip,rar',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Update quotation
            $quotation->update([
                'additional_info' => $validated['additional_info'],
                'status' => 'pending', // Reset to pending for review
                'last_communication_at' => now(),
            ]);
            
            // Handle new attachments
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
            
            // Send notification to admin
            try {
                Notifications::send('quotation.additional_info_provided', $quotation);
            } catch (\Exception $e) {
                Log::warning('Failed to send additional info notification: ' . $e->getMessage());
            }
            
            DB::commit();
            
            // Clear dashboard cache
            $this->dashboardService->clearCache(auth()->user());
            
            return redirect()->route('client.quotations.show', $quotation)
                ->with('success', 'Additional information provided successfully! We will review it within 24 hours.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update additional info: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to provide additional information. Please try again.');
        }
    }

    /**
     * Get quotation statistics for API
     */
    public function getStatistics(): JsonResponse
    {
        $user = auth()->user();
        $statistics = $this->getDetailedQuotationStatistics($user);
        
        return response()->json([
            'success' => true,
            'data' => $statistics,
        ]);
    }

    /**
     * Export quotations to PDF/Excel
     */
    public function export(Request $request)
    {
        $validated = $request->validate([
            'format' => 'required|in:pdf,excel',
            'status' => 'nullable|string|in:pending,reviewed,approved,rejected',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);
        
        $user = auth()->user();
        $quotations = $this->clientAccessService->getClientQuotations($user, $validated)->get();
        
        // Implementation would depend on your export service
        // return $this->exportService->exportQuotations($quotations, $validated['format']);
        
        return redirect()->back()->with('info', 'Export functionality coming soon.');
    }

    /**
     * Get detailed quotation statistics for the client
     */
    protected function getDetailedQuotationStatistics($user): array
    {
        $baseQuery = $this->clientAccessService->getClientQuotations($user);
        
        return [
            'total' => $baseQuery->count(),
            'pending' => $baseQuery->clone()->where('status', 'pending')->count(),
            'reviewed' => $baseQuery->clone()->where('status', 'reviewed')->count(),
            'approved' => $baseQuery->clone()->where('status', 'approved')->count(),
            'rejected' => $baseQuery->clone()->where('status', 'rejected')->count(),
            'client_approved' => $baseQuery->clone()->where('client_approved', true)->count(),
            'projects_created' => $baseQuery->clone()->where('project_created', true)->count(),
            'this_month' => $baseQuery->clone()->whereMonth('created_at', now()->month)->count(),
            'this_year' => $baseQuery->clone()->whereYear('created_at', now()->year)->count(),
            'avg_response_time' => $this->calculateAverageResponseTime($user),
        ];
    }

    /**
     * Get quotation alerts for the client
     */
    protected function getQuotationAlerts($user): array
    {
        $alerts = [];
        
        // Check for quotations needing action
        $needingAction = $this->clientAccessService->getClientQuotations($user)
            ->where('status', 'approved')
            ->where('client_approved', null)
            ->count();
        
        if ($needingAction > 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Action Required',
                'message' => "You have {$needingAction} approved quotation(s) waiting for your response.",
                'action' => [
                    'text' => 'View Quotations',
                    'url' => route('client.quotations.index', ['status' => 'approved']),
                ],
            ];
        }
        
        // Check for expiring quotations
        $expiring = $this->clientAccessService->getClientQuotations($user)
            ->where('status', 'approved')
            ->where('approved_at', '<', now()->subDays(25))
            ->where('client_approved', null)
            ->count();
        
        if ($expiring > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Quotations Expiring Soon',
                'message' => "You have {$expiring} quotation(s) that will expire soon.",
                'action' => [
                    'text' => 'Review Now',
                    'url' => route('client.quotations.index', ['status' => 'approved']),
                ],
            ];
        }
        
        return $alerts;
    }

    /**
     * Get quotation-specific alerts
     */
    protected function getQuotationSpecificAlerts(Quotation $quotation): array
    {
        $alerts = [];
        
        // Check for approval needed
        if ($quotation->status === 'approved' && $quotation->client_approved === null) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Action Required',
                'message' => 'This quotation has been approved. Please review and confirm.',
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
     * Get quotation timeline
     */
    protected function getQuotationTimeline(Quotation $quotation): array
    {
        $timeline = [];
        
        // Created
        $timeline[] = [
            'date' => $quotation->created_at,
            'title' => 'Quotation Submitted',
            'description' => 'Your quotation request was submitted successfully.',
            'type' => 'success',
            'icon' => 'plus-circle',
        ];
        
        // Reviewed
        if ($quotation->reviewed_at) {
            $timeline[] = [
                'date' => $quotation->reviewed_at,
                'title' => 'Under Review',
                'description' => 'Our team is reviewing your quotation request.',
                'type' => 'info',
                'icon' => 'eye',
            ];
        }
        
        // Approved/Rejected
        if ($quotation->approved_at) {
            $timeline[] = [
                'date' => $quotation->approved_at,
                'title' => $quotation->status === 'approved' ? 'Quotation Approved' : 'Quotation Status Updated',
                'description' => $quotation->status === 'approved' 
                    ? 'Your quotation has been approved. Please review and confirm.'
                    : 'The quotation status has been updated.',
                'type' => $quotation->status === 'approved' ? 'success' : 'warning',
                'icon' => $quotation->status === 'approved' ? 'check-circle' : 'exclamation-circle',
            ];
        }
        
        // Client approved
        if ($quotation->client_approved_at) {
            $timeline[] = [
                'date' => $quotation->client_approved_at,
                'title' => $quotation->client_approved ? 'Quotation Accepted' : 'Quotation Declined',
                'description' => $quotation->client_approved 
                    ? 'You have accepted this quotation. We will contact you soon.'
                    : 'You have declined this quotation.',
                'type' => $quotation->client_approved ? 'success' : 'danger',
                'icon' => $quotation->client_approved ? 'thumbs-up' : 'thumbs-down',
            ];
        }
        
        // Project created
        if ($quotation->project_created_at) {
            $timeline[] = [
                'date' => $quotation->project_created_at,
                'title' => 'Project Created',
                'description' => 'A project has been created based on this quotation.',
                'type' => 'success',
                'icon' => 'briefcase',
            ];
        }
        
        return collect($timeline)->sortBy('date')->values()->toArray();
    }

    /**
     * Get available actions for quotation
     */
    protected function getAvailableActions(Quotation $quotation): array
    {
        $actions = [];
        
        // Edit action
        if (in_array($quotation->status, ['pending', 'reviewed'])) {
            $actions['edit'] = [
                'url' => route('client.quotations.edit', $quotation),
                'text' => 'Edit Quotation',
                'type' => 'secondary',
                'icon' => 'edit',
            ];
        }
        
        // Additional info action
        if (in_array($quotation->status, ['pending', 'reviewed'])) {
            $actions['additional_info'] = [
                'url' => route('client.quotations.additional-info', $quotation),
                'text' => 'Add Information',
                'type' => 'info',
                'icon' => 'plus',
            ];
        }
        
        // Approve action
        if ($quotation->status === 'approved' && $quotation->client_approved === null) {
            $actions['approve'] = [
                'url' => route('client.quotations.approve', $quotation),
                'text' => 'Accept Quotation',
                'type' => 'success',
                'icon' => 'check',
                'confirm' => 'Are you sure you want to accept this quotation?',
            ];
            
            $actions['decline'] = [
                'url' => route('client.quotations.decline', $quotation),
                'text' => 'Decline Quotation',
                'type' => 'danger',
                'icon' => 'x',
            ];
        }
        
        // View project action
        if ($quotation->project_created && $quotation->project) {
            $actions['view_project'] = [
                'url' => route('client.projects.show', $quotation->project),
                'text' => 'View Project',
                'type' => 'primary',
                'icon' => 'external-link',
            ];
        }
        
        // Contact action
        $actions['contact'] = [
            'url' => route('client.messages.create', [
                'subject' => 'Regarding Quotation #' . $quotation->id . ' - ' . $quotation->project_type
            ]),
            'text' => 'Contact Us',
            'type' => 'secondary',
            'icon' => 'mail',
        ];
        
        return $actions;
    }

    /**
     * Get quotation metrics
     */
    protected function getQuotationMetrics(Quotation $quotation): array
    {
        return [
            'days_since_created' => $quotation->created_at->diffInDays(now()),
            'days_since_reviewed' => $quotation->reviewed_at ? $quotation->reviewed_at->diffInDays(now()) : null,
            'days_since_approved' => $quotation->approved_at ? $quotation->approved_at->diffInDays(now()) : null,
            'response_time' => $quotation->reviewed_at ? $quotation->created_at->diffInHours($quotation->reviewed_at) : null,
            'approval_time' => $quotation->approved_at && $quotation->reviewed_at ? 
                $quotation->reviewed_at->diffInHours($quotation->approved_at) : null,
            'is_urgent' => in_array($quotation->priority, ['high', 'urgent']),
            'is_overdue' => $quotation->status === 'pending' && $quotation->created_at->diffInDays(now()) > 3,
            'expires_soon' => $quotation->status === 'approved' && $quotation->approved_at && 
                $quotation->approved_at->diffInDays(now()) > 25,
        ];
    }

    /**
     * Calculate average response time for user's quotations
     */
    protected function calculateAverageResponseTime($user): ?float
    {
        $quotations = $this->clientAccessService->getClientQuotations($user)
            ->whereNotNull('reviewed_at')
            ->get();
        
        if ($quotations->isEmpty()) {
            return null;
        }
        
        $totalHours = $quotations->sum(function ($quotation) {
            return $quotation->created_at->diffInHours($quotation->reviewed_at);
        });
        
        return round($totalHours / $quotations->count(), 1);
    }

    /**
     * Generate unique quotation number
     */
    protected function generateQuotationNumber(): string
    {
        $prefix = 'QT';
        $year = now()->year;
        $month = now()->format('m');
        
        // Get next sequence number for this month
        $lastQuotation = Quotation::where('quotation_number', 'like', "{$prefix}-{$year}{$month}%")
            ->orderBy('quotation_number', 'desc')
            ->first();
        
        if ($lastQuotation) {
            $lastNumber = (int) substr($lastQuotation->quotation_number, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        return $prefix . '-' . $year . $month . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Duplicate a quotation (create new one based on existing)
     */
    public function duplicate(Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated client
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            abort(403, 'Unauthorized access to this quotation.');
        }
        
        try {
            DB::beginTransaction();
            
            // Create new quotation with similar data
            $newQuotationData = $quotation->toArray();
            
            // Remove fields that shouldn't be duplicated
            unset($newQuotationData['id']);
            unset($newQuotationData['quotation_number']);
            unset($newQuotationData['created_at']);
            unset($newQuotationData['updated_at']);
            unset($newQuotationData['status']);
            unset($newQuotationData['reviewed_at']);
            unset($newQuotationData['approved_at']);
            unset($newQuotationData['client_approved']);
            unset($newQuotationData['client_approved_at']);
            unset($newQuotationData['client_decline_reason']);
            unset($newQuotationData['project_created']);
            unset($newQuotationData['project_created_at']);
            unset($newQuotationData['last_communication_at']);
            
            // Set new values
            $newQuotationData['quotation_number'] = $this->generateQuotationNumber();
            $newQuotationData['status'] = 'pending';
            $newQuotationData['project_type'] = 'Copy of: ' . $newQuotationData['project_type'];
            
            $newQuotation = Quotation::create($newQuotationData);
            
            // Copy attachments if they exist
            foreach ($quotation->attachments as $attachment) {
                if ($attachment->exists()) {
                    $originalPath = $attachment->file_path;
                    $newPath = 'quotation_attachments/' . $newQuotation->id . '/' . $attachment->file_name;
                    
                    // Ensure directory exists
                    $directory = dirname($newPath);
                    if (!Storage::disk('public')->exists($directory)) {
                        Storage::disk('public')->makeDirectory($directory);
                    }
                    
                    // Copy the file
                    Storage::disk('public')->copy($originalPath, $newPath);
                    
                    // Create new attachment record
                    $newQuotation->attachments()->create([
                        'file_path' => $newPath,
                        'file_name' => $attachment->file_name,
                        'file_size' => $attachment->file_size,
                        'file_type' => $attachment->file_type,
                    ]);
                }
            }
            
            DB::commit();
            
            // Clear dashboard cache
            $this->dashboardService->clearCache(auth()->user());
            
            return redirect()->route('client.quotations.show', $newQuotation)
                ->with('success', 'Quotation duplicated successfully! You can now modify it as needed.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to duplicate quotation: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to duplicate quotation. Please try again.');
        }
    }

    /**
     * Cancel a quotation (only pending ones)
     */
    public function cancel(Request $request, Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated client
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            abort(403, 'Unauthorized access to this quotation.');
        }
        
        // Check if quotation can be cancelled
        if ($quotation->status !== 'pending') {
            return redirect()->route('client.quotations.show', $quotation)
                ->with('error', 'Only pending quotations can be cancelled.');
        }
        
        $validated = $request->validate([
            'cancel_reason' => 'nullable|string|max:500',
        ]);
        
        try {
            $quotation->update([
                'status' => 'rejected',
                'client_decline_reason' => $validated['cancel_reason'] ?? 'Cancelled by client',
                'client_approved' => false,
                'client_approved_at' => now(),
            ]);
            
            // Send notification to admin
            try {
                Notifications::send('quotation.cancelled_by_client', $quotation);
            } catch (\Exception $e) {
                Log::warning('Failed to send cancellation notification: ' . $e->getMessage());
            }
            
            // Clear dashboard cache
            $this->dashboardService->clearCache(auth()->user());
            
            return redirect()->route('client.quotations.index')
                ->with('success', 'Quotation cancelled successfully.');
                
        } catch (\Exception $e) {
            Log::error('Failed to cancel quotation: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to cancel quotation. Please try again.');
        }
    }

    /**
     * Add attachment to existing quotation
     */
    public function addAttachment(Request $request, Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated client
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }
        
        // Check if attachments can be added
        if (!in_array($quotation->status, ['pending', 'reviewed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Attachments cannot be added at this time.'
            ], 403);
        }
        
        $validated = $request->validate([
            'attachment' => 'required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png,gif,zip,rar',
        ]);
        
        try {
            $file = $request->file('attachment');
            $path = $file->store('quotation_attachments/' . $quotation->id, 'public');
            
            $attachment = $quotation->attachments()->create([
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_type' => $file->getMimeType(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Attachment added successfully.',
                'attachment' => [
                    'id' => $attachment->id,
                    'name' => $attachment->file_name,
                    'size' => $attachment->formatted_file_size,
                    'url' => $attachment->url,
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to add attachment: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to add attachment.'
            ], 500);
        }
    }

    /**
     * Get quotation activity feed
     */
    public function getActivity(Quotation $quotation): JsonResponse
    {
        // Ensure the quotation belongs to the authenticated client
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }
        
        $activities = [];
        
        // Get from dashboard service or create activity feed
        $dashboardData = $this->dashboardService->getDashboardData(auth()->user());
        $allActivities = collect($dashboardData['recent_activities'] ?? []);
        
        $quotationActivities = $allActivities
            ->where('quotation_id', $quotation->id)
            ->take(20)
            ->values();
        
        return response()->json([
            'success' => true,
            'data' => $quotationActivities
        ]);
    }

    /**
     * Print quotation details
     */
    public function print(Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated client
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            abort(403, 'Unauthorized access to this quotation.');
        }
        
        $quotation->load(['service', 'attachments']);
        
        return view('client.quotations.print', compact('quotation'));
    }
}