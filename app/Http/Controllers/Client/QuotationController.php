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
use Illuminate\Support\Facades\Response;
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
        
        // Validate filters
        $filters = $request->validate([
            'status' => 'nullable|string|in:pending,reviewed,approved,rejected',
            'service' => 'nullable|exists:services,id',
            'search' => 'nullable|string|max:255',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'sort' => 'nullable|string|in:created_at,updated_at,status,project_type,priority,start_date',
            'direction' => 'nullable|string|in:asc,desc',
            'priority' => 'nullable|string|in:low,normal,high,urgent',
        ]);

        $query = $user->quotations()->with(['service', 'attachments']);

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['service'])) {
            $query->where('service_id', $filters['service']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('project_type', 'like', "%{$search}%")
                  ->orWhere('requirements', 'like', "%{$search}%")
                  ->orWhere('quotation_number', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        // Apply sorting
        $sortBy = $filters['sort'] ?? 'created_at';
        $sortDirection = $filters['direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        $quotations = $query->paginate(15)->withQueryString();

        // Get filter options
        $services = Service::where('is_active', true)->orderBy('title')->get();

        return view('client.quotations.index', compact('quotations', 'services', 'filters'));
    }

    /**
     * Show the form for creating a new quotation.
     */
    public function create()
    {
        $services = Service::where('is_active', true)->orderBy('title')->get();
        
        return view('client.quotations.create', compact('services'));
    }

    /**
     * Store a newly created quotation - handles both web and AJAX requests
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'service_id' => 'nullable|exists:services,id',
            'project_type' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'requirements' => 'required|string',
            'budget_range' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'g-recaptcha-response' => 'sometimes|required', // For reCAPTCHA if enabled
        ]);

        try {
            DB::beginTransaction();

            // Set additional fields
            $validated['client_id'] = auth()->id();
            $validated['status'] = 'pending';
            $validated['priority'] = 'normal';
            $validated['source'] = 'client_portal';
            $validated['quotation_number'] = $this->generateQuotationNumber();

            // Create the quotation
            $quotation = Quotation::create($validated);

            // Transfer any temporary files to the quotation
            $transferredFiles = $this->transferTempFiles($quotation);

            DB::commit();

            // Send notifications
            try {
                if (function_exists('settings') && settings('quotation_client_confirmation_enabled', true)) {
                    Notifications::send('quotation.confirmation', $quotation, auth()->user());
                }
                
                if (function_exists('settings') && settings('notify_admin_new_quotation', true)) {
                    Notifications::send('quotation.created', $quotation);
                }
            } catch (\Exception $e) {
                Log::error('Failed to send quotation notifications: ' . $e->getMessage());
            }

            // Clear dashboard cache
            $this->dashboardService->clearCache(auth()->user());

            // Handle AJAX/JSON requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Your quotation request has been submitted successfully! We will contact you soon.',
                    'data' => [
                        'id' => $quotation->id,
                        'quotation_number' => $quotation->quotation_number,
                        'transferred_files' => $transferredFiles,
                        'redirect' => route('client.quotations.show', $quotation)
                    ]
                ]);
            }

            // Handle regular web requests
            return redirect()->route('client.quotations.show', $quotation)
                ->with('success', 'Your quotation request has been submitted successfully! We will contact you soon.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create quotation: ' . $e->getMessage());
            
            // Handle AJAX/JSON error responses
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to submit quotation. Please try again.',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            // Handle regular web error responses
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to submit quotation. Please try again.');
        }
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

        $quotation->load(['service', 'attachments']);

        return view('client.quotations.show', compact('quotation'));
    }

    /**
     * Show the form for editing the specified quotation.
     */
    public function edit(Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated client
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            abort(403, 'Unauthorized access to this quotation.');
        }

        // Only allow editing of pending quotations
        if ($quotation->status !== 'pending') {
            return redirect()->route('client.quotations.show', $quotation)
                ->with('error', 'Only pending quotations can be edited.');
        }

        $services = Service::where('is_active', true)->orderBy('title')->get();
        $quotation->load(['service', 'attachments']);

        return view('client.quotations.edit', compact('quotation', 'services'));
    }

    /**
     * Update the specified quotation.
     */
    public function update(Request $request, Quotation $quotation)
    {
        // Ensure the quotation belongs to the authenticated client
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            abort(403, 'Unauthorized access to this quotation.');
        }

        // Only allow updating of pending quotations
        if ($quotation->status !== 'pending') {
            return redirect()->route('client.quotations.show', $quotation)
                ->with('error', 'Only pending quotations can be updated.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'service_id' => 'nullable|exists:services,id',
            'project_type' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'requirements' => 'required|string',
            'budget_range' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
        ]);

        try {
            $quotation->update($validated);

            // Clear dashboard cache
            $this->dashboardService->clearCache(auth()->user());

            return redirect()->route('client.quotations.show', $quotation)
                ->with('success', 'Quotation updated successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to update quotation: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update quotation. Please try again.');
        }
    }

    /**
     * Universal uploader endpoint for quotation attachments
     */
    public function uploadAttachment(Request $request)
    {
        Log::info('Upload attachment request received', [
            'has_files' => $request->hasFile('files'),
            'files_count' => $request->hasFile('files') ? count($request->file('files')) : 0,
            'quotation_id' => $request->input('quotation_id'),
            'temp_session' => $request->input('temp_session', session()->getId()),
            'request_data' => $request->except(['files'])
        ]);

        $request->validate([
            'files' => 'required|array|max:5',
            'files.*' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif,zip,rar,txt,csv',
            'quotation_id' => 'nullable|exists:quotations,id',
            'temp_session' => 'nullable|string',
        ]);

        try {
            $uploadedFiles = [];
            $quotationId = $request->input('quotation_id');
            $tempSession = $request->input('temp_session', session()->getId());

            Log::info('Processing file uploads', [
                'quotation_id' => $quotationId,
                'temp_session' => $tempSession,
                'session_id' => session()->getId()
            ]);

            foreach ($request->file('files') as $file) {
                Log::info('Processing individual file', [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ]);

                if ($quotationId) {
                    // Upload to existing quotation
                    $quotation = Quotation::findOrFail($quotationId);
                    
                    if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Unauthorized access to quotation.'
                        ], 403);
                    }

                    $attachment = QuotationAttachment::createFromUploadedFile($file, $quotation);
                    
                    $uploadedFiles[] = [
                        'id' => $attachment->id,
                        'name' => $attachment->file_name,
                        'size' => $attachment->formatted_file_size,
                        'type' => $attachment->file_type,
                        'url' => $attachment->url,
                        'download_url' => route('client.quotations.download-attachment', [$quotation, $attachment]),
                        'uploaded_at' => $attachment->created_at->format('M j, Y H:i')
                    ];
                } else {
                    // Store temporarily
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = 'temp_quotation_attachments/' . $tempSession;
                    $filePath = $file->storeAs($path, $filename, 'public');

                    Log::info('File stored temporarily', [
                        'file_path' => $filePath,
                        'filename' => $filename,
                        'temp_session' => $tempSession
                    ]);

                    $tempFiles = session()->get('temp_quotation_files', []);
                    $tempFileData = [
                        'temp_id' => uniqid(),
                        'file_path' => $filePath,
                        'file_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'file_type' => $file->getMimeType(),
                        'uploaded_at' => now()->toISOString()
                    ];
                    
                    $tempFiles[] = $tempFileData;
                    session()->put('temp_quotation_files', $tempFiles);

                    Log::info('Updated session with temp file data', [
                        'temp_files_count' => count($tempFiles),
                        'temp_file_data' => $tempFileData
                    ]);

                    $uploadedFiles[] = [
                        'temp_id' => $tempFileData['temp_id'],
                        'name' => $tempFileData['file_name'],
                        'size' => $this->formatBytes($tempFileData['file_size']),
                        'type' => $tempFileData['file_type'],
                        'is_temp' => true,
                        'uploaded_at' => now()->format('M j, Y H:i')
                    ];
                }
            }

            Log::info('File upload completed', [
                'uploaded_files_count' => count($uploadedFiles),
                'uploaded_files' => $uploadedFiles
            ]);

            return response()->json([
                'success' => true,
                'message' => count($uploadedFiles) === 1 
                    ? 'File uploaded successfully!' 
                    : count($uploadedFiles) . ' files uploaded successfully!',
                'files' => $uploadedFiles
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to upload quotation attachment: ' . $e->getMessage(), [
                'error' => $e->getTraceAsString(),
                'request_data' => $request->except(['files'])
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete quotation attachment
     */
    public function deleteAttachment(Request $request, Quotation $quotation, QuotationAttachment $attachment)
    {
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }

        if ($attachment->quotation_id !== $quotation->id) {
            return response()->json([
                'success' => false,
                'message' => 'Attachment does not belong to this quotation.'
            ], 403);
        }

        try {
            $attachment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Attachment deleted successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete attachment: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete attachment.'
            ], 500);
        }
    }

    /**
     * Download quotation attachment
     */
    public function downloadAttachment(Quotation $quotation, QuotationAttachment $attachment)
    {
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            abort(403, 'Unauthorized access.');
        }

        if ($attachment->quotation_id !== $quotation->id) {
            abort(403, 'Attachment does not belong to this quotation.');
        }

        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'File not found.');
        }

        // Increment download count
        $attachment->increment('download_count');

        return Response::download(
            Storage::disk('public')->path($attachment->file_path),
            $attachment->file_name
        );
    }

    /**
     * Delete temporary file before quotation is created
     */
    public function deleteTempFile(Request $request)
    {
        $request->validate([
            'temp_id' => 'required|string'
        ]);

        $tempId = $request->input('temp_id');
        $tempFiles = session()->get('temp_quotation_files', []);
        
        foreach ($tempFiles as $index => $tempFile) {
            if ($tempFile['temp_id'] === $tempId) {
                // Delete file from storage
                if (Storage::disk('public')->exists($tempFile['file_path'])) {
                    Storage::disk('public')->delete($tempFile['file_path']);
                }
                
                // Remove from session
                unset($tempFiles[$index]);
                session()->put('temp_quotation_files', array_values($tempFiles));
                
                return response()->json([
                    'success' => true,
                    'message' => 'File deleted successfully.'
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'File not found.'
        ], 404);
    }

    /**
     * Get existing temporary files
     */
    public function getTempFiles()
    {
        $tempFiles = session()->get('temp_quotation_files', []);
        
        $formattedFiles = collect($tempFiles)->map(function ($file) {
            return [
                'temp_id' => $file['temp_id'],
                'name' => $file['file_name'],
                'size' => $this->formatBytes($file['file_size']),
                'type' => $file['file_type'],
                'is_temp' => true,
                'uploaded_at' => \Carbon\Carbon::parse($file['uploaded_at'])->format('M j, Y H:i')
            ];
        });

        return response()->json([
            'success' => true,
            'files' => $formattedFiles
        ]);
    }

    /**
     * Transfer temporary files to quotation (called after quotation creation)
     */
    public function transferTempFiles(Quotation $quotation)
    {
        $tempFiles = session()->get('temp_quotation_files', []);
        $transferredCount = 0;

        Log::info('Starting temp file transfer', [
            'quotation_id' => $quotation->id,
            'temp_files_count' => count($tempFiles),
            'temp_files' => $tempFiles
        ]);

        if (empty($tempFiles)) {
            Log::info('No temp files found in session');
            return 0;
        }

        foreach ($tempFiles as $tempFile) {
            try {
                $tempPath = $tempFile['file_path'];
                $newPath = 'quotation_attachments/' . $quotation->id . '/' . basename($tempPath);

                Log::info('Processing temp file', [
                    'temp_path' => $tempPath,
                    'new_path' => $newPath,
                    'file_exists' => Storage::disk('public')->exists($tempPath)
                ]);

                // Ensure directory exists
                $directory = dirname($newPath);
                if (!Storage::disk('public')->exists($directory)) {
                    Storage::disk('public')->makeDirectory($directory);
                    Log::info('Created directory: ' . $directory);
                }

                // Move file to quotation directory
                if (Storage::disk('public')->exists($tempPath)) {
                    Storage::disk('public')->move($tempPath, $newPath);

                    // Create attachment record
                    $attachment = QuotationAttachment::create([
                        'quotation_id' => $quotation->id,
                        'file_path' => $newPath,
                        'file_name' => $tempFile['file_name'],
                        'file_size' => $tempFile['file_size'],
                        'file_type' => $tempFile['file_type'],
                    ]);

                    Log::info('File transferred successfully', [
                        'attachment_id' => $attachment->id,
                        'file_name' => $attachment->file_name
                    ]);

                    $transferredCount++;
                } else {
                    Log::warning('Temp file not found in storage', ['temp_path' => $tempPath]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to transfer temp file: ' . $e->getMessage(), [
                    'temp_file' => $tempFile,
                    'quotation_id' => $quotation->id,
                    'error' => $e->getTraceAsString()
                ]);
            }
        }

        // Clear temp files from session
        session()->forget('temp_quotation_files');
        Log::info('Cleared temp files from session');

        Log::info('Temp file transfer completed', [
            'quotation_id' => $quotation->id,
            'transferred_count' => $transferredCount
        ]);

        return $transferredCount;
    }

    /**
     * Duplicate a quotation
     */
    public function duplicate(Quotation $quotation)
    {
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            abort(403, 'Unauthorized access to this quotation.');
        }
        
        try {
            DB::beginTransaction();
            
            $newQuotationData = $quotation->toArray();
            
            // Remove fields that shouldn't be duplicated
            unset($newQuotationData['id'], $newQuotationData['quotation_number'], 
                  $newQuotationData['created_at'], $newQuotationData['updated_at'],
                  $newQuotationData['status'], $newQuotationData['reviewed_at'],
                  $newQuotationData['approved_at']);
            
            // Set new values
            $newQuotationData['quotation_number'] = $this->generateQuotationNumber();
            $newQuotationData['status'] = 'pending';
            $newQuotationData['project_type'] = 'Copy of: ' . $newQuotationData['project_type'];
            
            $newQuotation = Quotation::create($newQuotationData);
            
            // Copy attachments
            foreach ($quotation->attachments as $attachment) {
                if (Storage::disk('public')->exists($attachment->file_path)) {
                    $originalPath = $attachment->file_path;
                    $newPath = 'quotation_attachments/' . $newQuotation->id . '/' . basename($originalPath);
                    
                    $directory = dirname($newPath);
                    if (!Storage::disk('public')->exists($directory)) {
                        Storage::disk('public')->makeDirectory($directory);
                    }
                    
                    Storage::disk('public')->copy($originalPath, $newPath);
                    
                    $newQuotation->attachments()->create([
                        'file_path' => $newPath,
                        'file_name' => $attachment->file_name,
                        'file_size' => $attachment->file_size,
                        'file_type' => $attachment->file_type,
                    ]);
                }
            }
            
            DB::commit();
            
            $this->dashboardService->clearCache(auth()->user());
            
            return redirect()->route('client.quotations.show', $newQuotation)
                ->with('success', 'Quotation duplicated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to duplicate quotation: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to duplicate quotation. Please try again.');
        }
    }

    /**
     * Cancel a quotation
     */
    public function cancel(Request $request, Quotation $quotation)
    {
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            abort(403, 'Unauthorized access to this quotation.');
        }
        
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
                'client_approved_at' => null,
            ]);
            
            $this->dashboardService->clearCache(auth()->user());
            
            return redirect()->route('client.quotations.show', $quotation)
                ->with('success', 'Quotation cancelled successfully.');
                
        } catch (\Exception $e) {
            Log::error('Failed to cancel quotation: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to cancel quotation. Please try again.');
        }
    }

    /**
     * Get quotation activity feed
     */
    public function getActivity(Quotation $quotation): JsonResponse
    {
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }
        
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
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            abort(403, 'Unauthorized access to this quotation.');
        }
        
        $quotation->load(['service', 'attachments']);
        
        return view('client.quotations.print', compact('quotation'));
    }

    /**
     * Generate quotation number
     */
    private function generateQuotationNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        
        $lastQuotation = Quotation::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $nextNumber = $lastQuotation ? (int) substr($lastQuotation->quotation_number, -4) + 1 : 1;
        
        return 'QT-' . $year . $month . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}