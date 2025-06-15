<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\QuotationAttachment;
use App\Models\Service;
use App\Services\ClientAccessService;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class QuotationController extends Controller
{
    protected $clientAccessService;
    protected $dashboardService;

    public function __construct(ClientAccessService $clientAccessService, DashboardService $dashboardService)
    {
        $this->clientAccessService = $clientAccessService;
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display a listing of quotations for the authenticated client
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->get('search'),
            'status' => $request->get('status'),
            'service' => $request->get('service'),
            'priority' => $request->get('priority'),
            'sort' => $request->get('sort', 'created_at'),
            'direction' => $request->get('direction', 'desc'),
        ];

        $query = Quotation::where('client_id', auth()->id())
            ->with(['service', 'attachments']);

        // Apply filters
        if ($filters['search']) {
            $query->where(function ($q) use ($filters) {
                $q->where('project_type', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('requirements', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('quotation_number', 'like', '%' . $filters['search'] . '%');
            });
        }

        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        if ($filters['service']) {
            $query->where('service_id', $filters['service']);
        }

        if ($filters['priority']) {
            $query->where('priority', $filters['priority']);
        }

        // Sorting
        $sortBy = in_array($filters['sort'], ['created_at', 'project_type', 'status', 'priority']) 
            ? $filters['sort'] 
            : 'created_at';
        $sortDirection = $filters['direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        $quotations = $query->paginate(15)->withQueryString();

        // Get filter options
        $services = Service::where('is_active', true)->orderBy('title')->get();

        return view('client.quotations.index', compact('quotations', 'services', 'filters'));
    }

    /**
     * Show the form for creating a new quotation
     */
    public function create()
    {
        $services = Service::where('is_active', true)->orderBy('title')->get();
        
        return view('client.quotations.create', compact('services'));
    }

    /**
     * Store a newly created quotation
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

            // Send notifications (if available)
            try {
                if (function_exists('settings') && settings('quotation_client_confirmation_enabled', true)) {
                    // Add notification logic here if available
                    Log::info('Quotation created by client', ['quotation_id' => $quotation->id]);
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
     * Display the specified quotation
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
     * Show the form for editing the specified quotation
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
     * Update the specified quotation
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
     * Get temporary files for current session
     */
    public function getTempFiles(Request $request)
    {
        $tempFiles = session()->get('temp_quotation_files', []);
        
        return response()->json([
            'success' => true,
            'files' => $tempFiles
        ]);
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

            // Create new quotation with duplicated data
            $newQuotation = $quotation->replicate();
            $newQuotation->quotation_number = $this->generateQuotationNumber();
            $newQuotation->status = 'pending';
            $newQuotation->reviewed_at = null;
            $newQuotation->approved_at = null;
            $newQuotation->client_approved = null;
            $newQuotation->client_approved_at = null;
            $newQuotation->save();

            // Duplicate attachments if any
            foreach ($quotation->attachments as $attachment) {
                $newAttachment = $attachment->replicate();
                $newAttachment->quotation_id = $newQuotation->id;
                
                // Copy the file to new location
                $oldPath = $attachment->file_path;
                $extension = pathinfo($attachment->file_name, PATHINFO_EXTENSION);
                $newFilename = time() . '_' . uniqid() . '.' . $extension;
                $newPath = 'quotation_attachments/' . $newQuotation->id . '/' . $newFilename;
                
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->copy($oldPath, $newPath);
                    $newAttachment->file_path = $newPath;
                }
                
                $newAttachment->save();
            }

            DB::commit();

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

        if (!in_array($quotation->status, ['pending', 'reviewed'])) {
            return redirect()->back()
                ->with('error', 'Only pending or reviewed quotations can be cancelled.');
        }

        try {
            $quotation->update([
                'status' => 'rejected',
                'client_decline_reason' => $request->input('reason', 'Cancelled by client'),
                'client_approved' => false,
                'client_approved_at' => now()
            ]);

            return redirect()->route('client.quotations.index')
                ->with('success', 'Quotation cancelled successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to cancel quotation: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to cancel quotation. Please try again.');
        }
    }

    /**
     * Get quotation activity/timeline
     */
    public function getActivity(Quotation $quotation)
    {
        if (!$this->clientAccessService->canAccessQuotation(auth()->user(), $quotation)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $activity = collect();

        // Add creation event
        $activity->push([
            'type' => 'created',
            'title' => 'Quotation Created',
            'description' => 'Quotation request was submitted',
            'timestamp' => $quotation->created_at,
            'icon' => 'plus'
        ]);

        // Add reviewed event
        if ($quotation->reviewed_at) {
            $activity->push([
                'type' => 'reviewed',
                'title' => 'Under Review',
                'description' => 'Quotation is being reviewed by our team',
                'timestamp' => $quotation->reviewed_at,
                'icon' => 'eye'
            ]);
        }

        // Add approved event
        if ($quotation->approved_at) {
            $activity->push([
                'type' => 'approved',
                'title' => 'Approved',
                'description' => 'Quotation has been approved',
                'timestamp' => $quotation->approved_at,
                'icon' => 'check'
            ]);
        }

        // Add client response events
        if ($quotation->client_approved_at) {
            $activity->push([
                'type' => $quotation->client_approved ? 'accepted' : 'declined',
                'title' => $quotation->client_approved ? 'Accepted' : 'Declined',
                'description' => $quotation->client_approved 
                    ? 'You accepted this quotation' 
                    : 'You declined this quotation' . ($quotation->client_decline_reason ? ': ' . $quotation->client_decline_reason : ''),
                'timestamp' => $quotation->client_approved_at,
                'icon' => $quotation->client_approved ? 'thumb-up' : 'thumb-down'
            ]);
        }

        // Sort by timestamp
        $activity = $activity->sortBy('timestamp')->values();

        return response()->json($activity);
    }

    /**
     * Print quotation
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
     * Generate unique quotation number
     */
    private function generateQuotationNumber()
    {
        $prefix = 'QUO';
        $year = now()->year;
        $month = now()->format('m');
        
        // Get the last quotation number for this month
        $lastQuotation = Quotation::where('quotation_number', 'like', $prefix . $year . $month . '%')
            ->orderBy('quotation_number', 'desc')
            ->first();
        
        if ($lastQuotation) {
            $lastNumber = (int) substr($lastQuotation->quotation_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Transfer temporary files to quotation
     */
    private function transferTempFiles(Quotation $quotation)
    {
        $tempFiles = session()->get('temp_quotation_files', []);
        $transferredFiles = [];

        foreach ($tempFiles as $tempFile) {
            try {
                if (Storage::disk('public')->exists($tempFile['file_path'])) {
                    // Create new filename and path
                    $extension = pathinfo($tempFile['file_name'], PATHINFO_EXTENSION);
                    $newFilename = time() . '_' . uniqid() . '.' . $extension;
                    $newPath = 'quotation_attachments/' . $quotation->id . '/' . $newFilename;
                    
                    // Move file to permanent location
                    Storage::disk('public')->move($tempFile['file_path'], $newPath);
                    
                    // Create attachment record
                    $attachment = QuotationAttachment::create([
                        'quotation_id' => $quotation->id,
                        'file_path' => $newPath,
                        'file_name' => $tempFile['file_name'],
                        'file_type' => $tempFile['file_type'],
                        'file_size' => $tempFile['file_size'],
                    ]);
                    
                    $transferredFiles[] = $attachment;
                }
            } catch (\Exception $e) {
                Log::error('Failed to transfer temp file: ' . $e->getMessage(), [
                    'temp_file' => $tempFile
                ]);
            }
        }

        // Clear temp files from session
        session()->forget('temp_quotation_files');

        return $transferredFiles;
    }

    /**
     * Format bytes into human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}