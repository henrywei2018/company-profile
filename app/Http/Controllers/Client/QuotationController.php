<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\Service;
use App\Models\QuotationAttachment;
use App\Services\ClientAccessService;
use App\Services\DashboardService;
use App\Services\QuotationService;
use App\Services\FileUploadService;
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
    protected FileUploadService $fileUploadService;

    public function __construct(
        ClientAccessService $clientAccessService,
        DashboardService $dashboardService,
        QuotationService $quotationService,
        FileUploadService $fileUploadService
    ) {
        $this->clientAccessService = $clientAccessService;
        $this->dashboardService = $dashboardService;
        $this->quotationService = $quotationService;
        $this->fileUploadService = $fileUploadService;
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
        $budgetRanges = [
            'under_5k' => 'Under $5,000',
            '5k_10k' => '$5,000 - $10,000',
            '10k_25k' => '$10,000 - $25,000',
            '25k_50k' => '$25,000 - $50,000',
            '50k_100k' => '$50,000 - $100,000',
            'over_100k' => 'Over $100,000',
            'tbd' => 'To Be Determined'
        ];
        
        return view('client.quotations.create', compact('services', 'userDefaults', 'budgetRanges'));
    }

    /**
     * Store a newly created quotation with comprehensive validation
     */
    public function store(Request $request)
    {
        // Enhanced validation that handles both traditional and universal file uploads
        $validated = $request->validate([
            'service_id' => 'nullable|exists:services,id',
            'project_type' => 'required|string|max:255',
            'requirements' => 'required|string|min:10|max:2000',
            'location' => 'nullable|string|max:255',
            'budget_range' => 'nullable|string|max:100',
            'start_date' => 'nullable|date|after:today',
            'priority' => 'nullable|string|in:low,medium,high,urgent',
            'additional_notes' => 'nullable|string|max:1000',
            'preferred_contact_method' => 'nullable|in:email,phone,both',
            'action' => 'nullable|in:save_as_draft,submit',
            
            // Traditional file uploads (your existing method)
            'attachments.*' => 'nullable|file|max:10240',
            
            // Universal file uploader temp files (new method)
            'temp_files' => 'nullable|array',
            'temp_files.*.temp_id' => 'required|string',
            'temp_files.*.category' => 'nullable|string|in:document,image,requirement,specification,other',
            'temp_files.*.description' => 'nullable|string|max:255'
        ]);
        
        $user = auth()->user();
        
        DB::beginTransaction();
        
        try {
            // Prepare quotation data
            $quotationData = array_merge($validated, [
                'client_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'company' => $user->company,
                'status' => $request->input('action') === 'save_as_draft' ? 'draft' : 'pending',
                'priority' => $validated['priority'] ?? 'medium',
                'source' => 'client_portal',
                'submitted_at' => $request->input('action') === 'save_as_draft' ? null : now()
            ]);
            
            // Remove temp_files from quotation data before passing to service
            unset($quotationData['temp_files']);
            
            // Handle traditional file uploads (your existing method)
            $traditionalFiles = $request->file('attachments') ?? [];
            
            // Use service to create quotation with traditional attachments
            $quotation = $this->quotationService->createQuotation($quotationData, $traditionalFiles);
            
            // Handle universal file uploader temp files (new method)
            if ($request->has('temp_files') && !empty($request->input('temp_files'))) {
                $attachmentCount = $this->processTempFiles($quotation, $request->input('temp_files'));
                
                if ($attachmentCount > 0) {
                    Log::info("Processed {$attachmentCount} temp file attachments for quotation {$quotation->id}");
                }
            }
            
            DB::commit();
            
            // Clear dashboard cache
            $this->dashboardService->clearCache($user);
            
            $message = $quotation->status === 'draft' 
                ? 'Quotation saved as draft successfully! You can continue editing or submit it later.'
                : 'Quotation request submitted successfully! We will review it within 24 hours.';
            
            // Redirect based on action
            if ($request->input('action') === 'save_as_draft') {
                return redirect()->route('client.quotations.edit', $quotation)
                    ->with('success', $message);
            }
            
            return redirect()->route('client.quotations.show', $quotation)
                ->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create quotation: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create quotation. Please try again.');
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
        
        // Only allow editing of draft or pending quotations
        if (!in_array($quotation->status, ['draft', 'pending'])) {
            return redirect()->route('client.quotations.show', $quotation)
                ->with('error', 'This quotation cannot be edited in its current status.');
        }

        $services = Service::active()->get();
        $quotation->load('attachments');

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
        
        // Only allow updating of draft or pending quotations
        if (!in_array($quotation->status, ['draft', 'pending'])) {
            return redirect()->route('client.quotations.show', $quotation)
                ->with('error', 'This quotation cannot be updated in its current status.');
        }

        $validated = $request->validate([
            'service_id' => 'nullable|exists:services,id',
            'project_type' => 'required|string|max:255',
            'requirements' => 'required|string|min:10|max:2000',
            'location' => 'nullable|string|max:255',
            'budget_range' => 'nullable|string|max:100',
            'start_date' => 'nullable|date|after:today',
            'priority' => 'nullable|string|in:low,medium,high,urgent',
            'additional_notes' => 'nullable|string|max:1000',
            'preferred_contact_method' => 'nullable|in:email,phone,both',
            'action' => 'nullable|in:save_as_draft,submit',
            'temp_files' => 'nullable|array',
            'temp_files.*.temp_id' => 'required|string',
            'temp_files.*.category' => 'nullable|string|in:document,image,requirement,specification,other',
            'temp_files.*.description' => 'nullable|string|max:255'
        ]);

        DB::beginTransaction();

        try {
            // Update quotation data
            $updateData = $validated;
            
            // Handle status change
            if ($request->input('action') === 'submit' && $quotation->status === 'draft') {
                $updateData['status'] = 'pending';
                $updateData['submitted_at'] = now();
            }

            $quotation->update($updateData);

            // Handle new file attachments
            if ($request->has('temp_files')) {
                $attachmentCount = $this->processTempFiles($quotation, $request->input('temp_files'));
                
                if ($attachmentCount > 0) {
                    Log::info("Added {$attachmentCount} new attachments to quotation {$quotation->id}");
                }
            }

            DB::commit();

            // Clear dashboard cache
            $this->dashboardService->clearCache(auth()->user());

            $message = $quotation->status === 'draft' 
                ? 'Quotation draft updated successfully!'
                : 'Quotation updated and submitted successfully!';

            return redirect()->route('client.quotations.show', $quotation)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update quotation: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update quotation. Please try again.');
        }
    }

    public function tempUpload(Request $request): JsonResponse
{
    // Check if file was actually uploaded
    if (!$request->hasFile('file')) {
        return response()->json([
            'success' => false,
            'message' => 'No file was uploaded'
        ], 422);
    }

    // Validate the upload
    $request->validate([
        'file' => 'required|file|max:10240', // 10MB max
        'category' => 'nullable|string|in:document,image,requirement,specification,other'
    ]);

    try {
        $file = $request->file('file');
        $category = $request->input('category', 'document');
        
        // Validate file type based on category
        $allowedTypes = $this->getAllowedFileTypes($category);
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            return response()->json([
                'success' => false,
                'message' => 'File type "' . $file->getMimeType() . '" not allowed for category "' . $category . '"'
            ], 422);
        }
        
        // Generate unique temp ID
        $tempId = 'temp_quotation_' . uniqid() . '_' . time();
        
        // Store temporarily with organized structure
        $tempPath = "temp/quotations/" . auth()->id() . "/" . date('Y-m-d');
        $fileName = $tempId . '_' . $file->getClientOriginalName();
        $path = $file->storeAs($tempPath, $fileName, 'public');
        
        // Store temp file info in session
        session()->put("temp_quotation_files.{$tempId}", [
            'original_name' => $file->getClientOriginalName(),
            'temp_path' => $path,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'category' => $category,
            'uploaded_at' => now()->toISOString()
        ]);

        return response()->json([
            'success' => true,
            'temp_id' => $tempId,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'file_size_formatted' => $this->formatFileSize($file->getSize()),
            'category' => $category,
            'url' => Storage::url($path),
            'message' => 'File uploaded successfully'
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        Log::error('Temp file upload failed: ' . $e->getMessage(), [
            'user_id' => auth()->id(),
            'file_name' => $request->file('file') ? $request->file('file')->getClientOriginalName() : 'unknown'
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Upload failed: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * ADD THIS NEW METHOD - Delete temporary file
     */
    public function tempDelete(Request $request): JsonResponse
    {
        $request->validate([
            'temp_id' => 'required|string'
        ]);

        try {
            $tempId = $request->input('temp_id');
            $tempData = session()->get("temp_quotation_files.{$tempId}");

            if ($tempData && isset($tempData['temp_path'])) {
                Storage::disk('public')->delete($tempData['temp_path']);
                session()->forget("temp_quotation_files.{$tempId}");
                
                return response()->json([
                    'success' => true,
                    'message' => 'File deleted successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Temp file deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ADD THIS NEW METHOD - Get current temporary files
     */
    public function getTempFiles(Request $request): JsonResponse
{
    $sessionKey = 'quotation_temp_files_' . session()->getId();
    $tempFiles = session()->get($sessionKey, []);
    $files = [];
    foreach ($tempFiles as $tempId => $data) {
        $files[] = [
            'id'         => $tempId,
            'temp_id'    => $tempId,
            'name'       => $data['original_name'] ?? 'Attachment',
            'file_name'  => $data['original_name'] ?? '',
            'category'   => $data['category'] ?? 'document',
            'type'       => $data['category'] ?? 'document',
            'url'        => Storage::disk('public')->url($data['temp_path']),
            'size'       => $this->formatFileSize($data['file_size'] ?? 0),
            'temp_path'  => $data['temp_path'],
            'is_temp'    => true,
            'created_at' => \Carbon\Carbon::parse($data['uploaded_at'])->format('M j, Y H:i')
        ];
    }
    return response()->json(['files' => $files]);
}


    /**
     * ADD THIS NEW METHOD - Upload attachment to existing quotation (for edit mode)
     */
    public function uploadTempFiles(Request $request)
{
    // Ambil semua file dari request, baik multiple maupun single
    $files = [];
    if ($request->hasFile('files')) {
        $files = $request->file('files');
        // Jika cuma single, bungkus jadi array
        if (!is_array($files)) {
            $files = [$files];
        }
    }

    if (empty($files)) {
        return response()->json([
            'success' => false,
            'message' => 'No file was uploaded',
        ], 422);
    }

    // Tidak perlu validasi array di sini, cukup validasi file per item
    foreach ($files as $file) {
        if (!$file->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid file uploaded',
            ], 422);
        }
    }

    $category = $request->input('category', 'document');
    $sessionKey = 'quotation_temp_files_' . session()->getId();
    $sessionData = session()->get($sessionKey, []);
    $uploadedFiles = [];

    foreach ($files as $file) {
        $tempId = 'temp_' . uniqid() . '_' . time();
        $tempFilename = $tempId . '.' . $file->getClientOriginalExtension();
        $tempPath = $file->storeAs('temp/quotations', $tempFilename, 'public');

        $tempData = [
            'temp_id'      => $tempId,
            'temp_path'    => $tempPath,
            'original_name'=> $file->getClientOriginalName(),
            'category'     => $category,
            'file_size'    => $file->getSize(),
            'mime_type'    => $file->getMimeType(),
            'uploaded_at'  => now()->toISOString(),
            'session_id'   => session()->getId()
        ];

        $sessionData[$tempId] = $tempData;

        $uploadedFiles[] = [
            'id'         => $tempId,
            'temp_id'    => $tempId,
            'name'       => $file->getClientOriginalName(),
            'file_name'  => $file->getClientOriginalName(),
            'category'   => $category,
            'type'       => $category,
            'url'        => Storage::disk('public')->url($tempPath),
            'size'       => $this->formatFileSize($file->getSize()),
            'temp_path'  => $tempPath,
            'is_temp'    => true,
            'created_at' => now()->format('M j, Y H:i')
        ];
    }

    session()->put($sessionKey, $sessionData);

    return response()->json(['files' => $uploadedFiles]);
}



    /**
     * ADD THIS NEW METHOD - Delete attachment from quotation
     */
    public function deleteAttachment(Request $request, Quotation $quotation): JsonResponse
    {
        // Ensure the quotation belongs to the authenticated client
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'attachment_id' => 'required|exists:quotation_attachments,id'
        ]);
 
        try {
            $attachment = QuotationAttachment::where('quotation_id', $quotation->id)
                ->findOrFail($request->input('attachment_id'));

            // Delete file from storage
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            // Delete database record
            $attachment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Attachment deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Attachment deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ADD THIS NEW METHOD - Download attachment
     */
    public function downloadAttachment(Quotation $quotation, QuotationAttachment $attachment)
    {
        // Ensure the quotation belongs to the authenticated client
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            abort(403, 'Unauthorized access to this quotation.');
        }

        // Ensure attachment belongs to quotation
        if ($attachment->quotation_id !== $quotation->id) {
            abort(404, 'Attachment not found.');
        }

        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'File not found.');
        }

        // Increment download count if tracking
        if (method_exists($attachment, 'incrementDownloads')) {
            $attachment->incrementDownloads();
        }

        return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
    }

    /**
     * ADD THIS PRIVATE METHOD - Process temporary files and convert to permanent attachments
     */
    private function processTempFiles(Quotation $quotation, array $tempFiles): int
{
    $processedCount = 0;
    
    foreach ($tempFiles as $fileData) {
        $tempId = $fileData['temp_id'];
        $tempData = session()->get("temp_quotation_files.{$tempId}");
        
        if (!$tempData) {
            Log::warning("Temp file data not found for ID: {$tempId}");
            continue;
        }
        
        try {
            // Move from temp to permanent storage using your existing structure
            $permanentPath = "quotation_attachments/{$quotation->id}";
            $fileName = time() . '_' . $processedCount . '_' . $tempData['original_name'];
            $finalPath = $permanentPath . '/' . $fileName;
            
            // Ensure directory exists
            Storage::disk('public')->makeDirectory($permanentPath);
            
            // Move file
            if (Storage::disk('public')->exists($tempData['temp_path'])) {
                Storage::disk('public')->move($tempData['temp_path'], $finalPath);
                
                // Create attachment record using your existing QuotationAttachment model structure
                $attachmentData = [
                    'quotation_id' => $quotation->id,
                    'file_name' => $tempData['original_name'],
                    'file_path' => $finalPath,
                    'file_size' => $tempData['size'],
                    'file_type' => $tempData['mime_type'],
                ];                
                
                QuotationAttachment::create($attachmentData);
                $processedCount++;
            }
            
            // Clean up session
            session()->forget("temp_quotation_files.{$tempId}");
            
        } catch (\Exception $e) {
            Log::error("Failed to process temp file {$tempId}: " . $e->getMessage(), [
                'quotation_id' => $quotation->id,
                'temp_id' => $tempId,
                'user_id' => auth()->id()
            ]);
        }
    }
    
    return $processedCount;
}

    /**
     * ADD THIS PRIVATE METHOD - Get allowed file types for a category
     */
    private function getAllowedFileTypes(string $category): array
{
    $types = [
        'document' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'text/csv',
            'application/rtf'
        ],
        'image' => [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml',
            'image/bmp'
        ],
        'requirement' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'text/csv'
        ],
        'specification' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation'
        ],
        'other' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/zip',
            'application/x-zip-compressed',
            'text/plain',
            'image/jpeg',
            'image/png'
        ]
    ];

    return $types[$category] ?? $types['other'];
}

    /**
     * ADD THIS PRIVATE METHOD - Format file size for display
     */
    private function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.1f %s", $bytes / pow(1024, $factor), $units[$factor]);
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