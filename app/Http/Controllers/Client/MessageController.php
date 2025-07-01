<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Services\ClientAccessService;
use App\Services\MessageService;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    protected ClientAccessService $clientAccessService;
    protected MessageService $messageService;
    protected DashboardService $dashboardService;

    public function __construct(
        ClientAccessService $clientAccessService,
        MessageService $messageService,
        DashboardService $dashboardService
    ) {
        $this->clientAccessService = $clientAccessService;
        $this->messageService = $messageService;
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display a listing of the client's messages.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Validate filters
        $filters = $request->validate([
            'search' => 'nullable|string|max:255',
            'read' => 'nullable|string|in:read,unread',
            'type' => 'nullable|string|in:general,support,project_inquiry,complaint,feedback,client_reply',
            'priority' => 'nullable|string|in:low,normal,high,urgent',
            'project_id' => 'nullable|exists:projects,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'sort' => 'nullable|string|in:created_at,updated_at,subject,is_read',
            'direction' => 'nullable|string|in:asc,desc',
        ]);

        // Get messages using service
        $messages = $this->messageService->getClientMessages($user, $filters, 15);

        // Get message statistics for dashboard
        $statistics = $this->messageService->getMessageStatistics($user);

        // Get filter options
        $filterOptions = $this->clientAccessService->getMessageFilters($user);

        // Get recent activities
        $recentActivity = $this->messageService->getRecentActivity($user, 5);

        return view('client.messages.index', compact(
            'messages',
            'statistics',
            'filterOptions',
            'recentActivity',
            'filters'
        ));
    }

    /**
     * Show the form for creating a new message.
     */
    public function create(Request $request)
    {
        $user = auth()->user();

        // Get user's projects for context selection
        $projects = $this->clientAccessService->getClientProjects($user)
            ->orderBy('title')
            ->get(['id', 'title', 'status']);

        // Get available message types
        $messageTypes = $this->getClientMessageTypes();

        // Pre-fill data from query parameters
        $prefillData = $request->only(['subject', 'project_id', 'type', 'priority']);

        return view('client.messages.create', compact(
            'projects',
            'messageTypes',
            'prefillData'
        ));
    }

    /**
     * Store a newly created message - COMPLETE WITH ALL FIELDS
     */
    public function store(Request $request)
{
    $user = auth()->user();
    

    try {
        // Simplified validation - remove the 'type' field validation
        $validated = $request->validate([
            'priority' => 'nullable|in:normal,urgent',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
            'temp_files' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id', // if you have project selection
        ]);
        
        Log::info('Validation passed', ['validated_data' => $validated]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation failed', [
            'user_id' => $user->id,
            'errors' => $e->errors(),
            'request_data' => $request->except(['temp_files', '_token'])
        ]);
        throw $e;
    }

    try {
        DB::beginTransaction();
        
        Log::info('Database transaction started');

        // Create the message - always set type to 'client_to_admin'
        $messageData = [
            'type' => 'client_to_admin',
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'company' => $user->company,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'priority' => $validated['priority'] ?? 'normal',
            'user_id' => $user->id,
            'project_id' => $validated['project_id'] ?? null, // if you have project selection
            'is_read' => false,
            'is_replied' => false,
        ];
        
        Log::info('Creating message with data', $messageData);
        
        $message = Message::create($messageData);
        
        Log::info('Message created successfully', [
            'message_id' => $message->id,
            'message_type' => $message->type
        ]);

        // Handle temp files if any
        $attachmentCount = 0;
        if ($request->filled('temp_files')) {
            Log::info('Processing temp files', [
                'temp_files_raw' => $request->input('temp_files')
            ]);
            
            $tempFilePaths = json_decode($request->input('temp_files'), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON decode error for temp_files', [
                    'json_error' => json_last_error_msg(),
                    'temp_files_raw' => $request->input('temp_files')
                ]);
            } elseif (is_array($tempFilePaths)) {
                Log::info('Found temp files to process', [
                    'file_count' => count($tempFilePaths),
                    'file_paths' => $tempFilePaths
                ]);
                
                foreach ($tempFilePaths as $tempPath) {
                    try {
                        if ($this->moveTempFileToMessage($tempPath, $message)) {
                            $attachmentCount++;
                            Log::info('Attachment processed successfully', [
                                'temp_path' => $tempPath,
                                'message_id' => $message->id
                            ]);
                        } else {
                            Log::warning('Failed to process attachment', [
                                'temp_path' => $tempPath,
                                'message_id' => $message->id
                            ]);
                        }
                    } catch (\Exception $attachmentError) {
                        Log::error('Attachment processing error', [
                            'temp_path' => $tempPath,
                            'message_id' => $message->id,
                            'error' => $attachmentError->getMessage()
                        ]);
                    }
                }
            }
        }

        // Clear dashboard cache
        try {
            $this->dashboardService->clearCache($user);
            Log::info('Dashboard cache cleared successfully');
        } catch (\Exception $cacheError) {
            Log::warning('Failed to clear dashboard cache', [
                'error' => $cacheError->getMessage()
            ]);
        }

        DB::commit();
        
        Log::info('Message submission completed successfully', [
            'message_id' => $message->id,
            'attachment_count' => $attachmentCount,
            'user_id' => $user->id
        ]);

        $successMessage = 'Message sent successfully!';
        if ($attachmentCount > 0) {
            $successMessage .= " ({$attachmentCount} attachment" . ($attachmentCount > 1 ? 's' : '') . " included)";
        }

        return redirect()->route('client.messages.index')
            ->with('success', $successMessage);

    } catch (\Exception $e) {
        DB::rollBack();
        
        Log::error('Message submission failed', [
            'user_id' => $user->id,
            'error_message' => $e->getMessage(),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine(),
            'stack_trace' => $e->getTraceAsString(),
            'request_data' => $request->except(['temp_files', '_token'])
        ]);

        return redirect()->back()
            ->withErrors(['error' => 'Failed to send message. Please try again.'])
            ->withInput();
    }
}

    /**
     * Display the specified message with auto mark-as-read
     */
    public function show(Message $message)
    {
        $user = auth()->user();
        
        // Ensure message belongs to authenticated client
        if (!$this->clientAccessService->canAccessMessage($user, $message)) {
            abort(403, 'Unauthorized access to this message.');
        }
        
        // Load all necessary relationships
        $message->load([
            'attachments',
            'project',
            'user',
            'parent',
            'replies' => fn($q) => $q->with(['attachments', 'user'])->orderBy('created_at'),
        ]);

        // Get the root message for the conversation
        $rootMessage = $message->parent_id ? $message->parent : $message;
        
        // Get the complete conversation thread (root + all replies)
        $thread = $this->messageService->getMessageThread($message);
        
        // AUTO MARK AS READ - Mark entire thread that should be read by client
        $this->autoMarkMessagesAsRead($thread, $user);
        
        // Get related messages from same project (excluding current thread)
        $relatedMessages = collect();
        if ($message->project_id) {
            $threadMessageIds = $thread->pluck('id')->toArray();
            $relatedMessages = $this->clientAccessService->getProjectMessages($user, $message->project_id)
                ->whereNotIn('id', $threadMessageIds)
                ->with(['attachments'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        // Check permissions
        $canReply = $this->clientAccessService->canReplyToMessage($user, $message);
        $canEscalate = $this->clientAccessService->canEscalateMessage($user, $message);

        // Clear dashboard cache after potential read status changes
        $this->dashboardService->clearCache($user);

        return view('client.messages.show', compact(
            'message',
            'rootMessage',
            'thread',
            'relatedMessages',
            'canReply',
            'canEscalate'
        ));
    }

    /**
     * Reply to a message - COMPLETE WITH ALL FIELDS
     */
    public function reply(Request $request, Message $message)
    {
        $user = auth()->user();

        // Security checks
        if (!$this->clientAccessService->canAccessMessage($user, $message)) {
            abort(403, 'Unauthorized access to this message.');
        }

        if (!$this->clientAccessService->canReplyToMessage($user, $message)) {
            return redirect()->back()
                ->with('error', 'You cannot reply to this message.');
        }

        $validated = $request->validate([
            'message' => 'required|string|max:5000|min:10',
            'temp_files' => 'nullable|string', // JSON string of temp file paths
        ]);

        try {
            DB::beginTransaction();

            // Get the root message for threading
            $rootMessage = $message->parent_id ? $message->parent : $message;

            // Create reply message with ALL fields
            $reply = Message::create([
                'type' => 'client_reply',
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'company' => $user->company,
                'subject' => 'Re: ' . $rootMessage->subject,
                'message' => $validated['message'],
                'priority' => $rootMessage->priority,
                'user_id' => $user->id,
                'parent_id' => $rootMessage->id,
                'is_read' => false,
                'is_replied' => false,
            ]);

            // Handle temp files if any
            $attachmentCount = 0;
            if ($request->filled('temp_files')) {
                $tempFilePaths = json_decode($request->input('temp_files'), true);

                if (is_array($tempFilePaths)) {
                    foreach ($tempFilePaths as $tempPath) {
                        if ($this->moveTempFileToMessage($tempPath, $reply)) {
                            $attachmentCount++;
                        }
                    }
                }
            }

            // Clean up temp directory after successful processing
            $this->cleanupSessionTempFiles();

            // Mark original message as replied if it's from admin - ENHANCED
            if ($message->type === 'admin_to_client') {
                $message->update([
                    'is_replied' => true, 
                    'replied_at' => now(),
                    'replied_by' => $user->id
                ]);
            }

            // Clear dashboard cache
            $this->dashboardService->clearCache($user);

            DB::commit();

            $successMessage = 'Reply sent successfully!';
            if ($attachmentCount > 0) {
                $successMessage .= " ({$attachmentCount} attachment" . ($attachmentCount > 1 ? 's' : '') . " included)";
            }

            return redirect()->route('client.messages.show', $rootMessage)
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to send reply: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to send reply. Please try again.')
                ->withInput();
        }
    }

    /**
     * Bulk action for multiple messages - OPTIMIZED FOR SERVICES
     */
    public function bulkAction(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'action' => 'required|string|in:mark_read,mark_unread,delete',
            'message_ids' => 'required|array|min:1|max:100', // Add limits for safety
            'message_ids.*' => 'integer|exists:messages,id',
        ]);

        // Security check: Get only messages that belong to the client
        $allowedMessageIds = $this->clientAccessService->getClientMessages($user)
            ->whereIn('id', $validated['message_ids'])
            ->pluck('id')
            ->toArray();

        if (empty($allowedMessageIds)) {
            return $this->bulkActionResponse('error', 'No valid messages selected.', 0);
        }

        $count = 0;
        $actionName = str_replace('_', ' ', $validated['action']);

        try {
            DB::beginTransaction();

            switch ($validated['action']) {
                case 'mark_read':
                    $count = $this->messageService->bulkMarkAsRead($allowedMessageIds, $user);
                    break;

                case 'mark_unread':
                    $count = $this->messageService->bulkMarkAsUnread($allowedMessageIds, $user);
                    break;

                case 'delete':
                    $count = $this->handleBulkDelete($allowedMessageIds, $user);
                    break;
            }

            // Clear dashboard cache after successful operation
            $this->dashboardService->clearCache($user);

            DB::commit();

            // Log bulk action for audit
            Log::info('Bulk action performed', [
                'user_id' => $user->id,
                'action' => $validated['action'],
                'message_count' => $count,
                'message_ids' => $allowedMessageIds
            ]);

            return $this->bulkActionResponse('success', "{$count} messages {$actionName}.", $count);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Bulk action failed', [
                'user_id' => $user->id,
                'action' => $validated['action'],
                'message_ids' => $allowedMessageIds,
                'error' => $e->getMessage()
            ]);

            return $this->bulkActionResponse('error', 'Failed to perform bulk action. Please try again.', 0);
        }
    }

    /**
     * Toggle message read status - ENHANCED
     */
    public function toggleRead(Message $message)
    {
        $user = auth()->user();

        // Ensure message belongs to authenticated client
        if (!$this->clientAccessService->canAccessMessage($user, $message)) {
            abort(403, 'Unauthorized access to this message.');
        }

        try {
            if ($message->is_read) {
                $this->messageService->markAsUnread($message);
                $status = 'unread';
            } else {
                $this->messageService->markAsRead($message, $user);
                $status = 'read';
            }

            // Clear dashboard cache
            $this->dashboardService->clearCache($user);

            // Check if this is an AJAX request
            if (request()->ajax() || request()->wantsJson()) {
                // Get updated statistics for AJAX requests
                $updatedStats = $this->messageService->getMessageStatistics($user);

                return response()->json([
                    'success' => true,
                    'message' => "Message marked as {$status}",
                    'is_read' => $message->is_read,
                    'status' => $status,
                    'statistics' => $updatedStats
                ]);
            }

            return redirect()->back()
                ->with('success', "Message marked as {$status}.");

        } catch (\Exception $e) {
            Log::error('Failed to toggle message read status: ' . $e->getMessage());

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update message status'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to update message status.');
        }
    }

    /**
     * Mark message as urgent (escalate priority)
     */
    public function markUrgent(Message $message)
    {
        $user = auth()->user();

        if (!$this->clientAccessService->canEscalateMessage($user, $message)) {
            return redirect()->back()
                ->with('error', 'You cannot escalate this message priority.');
        }

        $message->update(['priority' => 'urgent']);

        // Clear dashboard cache
        $this->dashboardService->clearCache($user);

        return redirect()->back()
            ->with('success', 'Message marked as urgent. We will prioritize your request.');
    }

    /**
     * Get project-specific messages
     */
    public function projectMessages(Request $request, $projectId)
    {
        $user = auth()->user();

        // Verify project access
        $project = \App\Models\Project::findOrFail($projectId);
        if (!$this->clientAccessService->canAccessProject($user, $project)) {
            abort(403, 'Unauthorized access to this project.');
        }

        $filters = $request->validate([
            'search' => 'nullable|string|max:255',
            'type' => 'nullable|string',
            'read' => 'nullable|string|in:read,unread',
            'sort' => 'nullable|string|in:created_at,updated_at,subject',
            'direction' => 'nullable|string|in:asc,desc',
        ]);

        // Get project messages
        $query = $this->clientAccessService->getProjectMessages($user, $projectId);

        // Apply filters
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('subject', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('message', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['read'])) {
            $isRead = $filters['read'] === 'read';
            $query->where('is_read', $isRead);
        }

        // Sort
        $sortField = $filters['sort'] ?? 'created_at';
        $sortDirection = $filters['direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        $messages = $query->with(['attachments', 'parent', 'replies'])
            ->paginate(15);

        return view('client.messages.project', compact(
            'messages',
            'project',
            'filters'
        ));
    }

    /**
     * Download an attachment
     */
    public function downloadAttachment(Message $message, MessageAttachment $attachment)
    {
        $user = auth()->user();

        // Security checks
        if (!$this->clientAccessService->canAccessMessage($user, $message)) {
            abort(403, 'Unauthorized access to this message.');
        }

        if ($attachment->message_id !== $message->id) {
            abort(403, 'Invalid attachment for this message.');
        }

        // Check if file exists
        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download(
            $attachment->file_path,
            $attachment->file_name
        );
    }

    // ===== TEMP FILE UPLOAD METHODS (Universal Uploader) =====

    /**
     * Upload temporary attachment for message - FIXED FOR UNIVERSAL UPLOADER
     */
    public function uploadTempAttachment(Request $request)
    {
        try {
            // Validate based on what Universal Uploader sends
            $request->validate([
                'files' => 'required|array|min:1|max:5',
                'files.*' => 'required|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,txt,csv,zip,rar',
                'category' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:500',
                'is_public' => 'nullable|boolean',
            ]);

            $uploadedFiles = [];
            $directory = 'temp/message-attachments/' . session()->getId();
            
            // Ensure directory exists
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory, 0755, true);
            }

            foreach ($request->file('files') as $file) {
                if ($file->isValid()) {
                    // Generate unique filename
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $filename = uniqid() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;
                    $filePath = $directory . '/' . $filename;

                    // Store the file
                    $storedPath = $file->storeAs($directory, $filename, 'public');
                    
                    if ($storedPath) {
                        $uploadedFiles[] = [
                            'id' => uniqid(),
                            'temp_id' => uniqid(),
                            'is_temp' => true,
                            'original_name' => $originalName,
                            'filename' => $filename,
                            'file_name' => $originalName,
                            'name' => $originalName,
                            'path' => $filePath,
                            'file_path' => $filePath,
                            'size' => $file->getSize(),
                            'file_size' => $file->getSize(),
                            'mime_type' => $file->getMimeType(),
                            'file_type' => $file->getMimeType(),
                            'type' => $file->getMimeType(),
                            'extension' => $extension,
                            'disk' => 'public',
                            'url' => Storage::disk('public')->url($filePath),
                            'file_url' => Storage::disk('public')->url($filePath),
                            'uploaded_at' => now()->toISOString(),
                            'category' => $request->input('category', 'message_attachment'),
                        ];
                    }
                }
            }

            if (empty($uploadedFiles)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No files were uploaded successfully'
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => count($uploadedFiles) . ' file(s) uploaded successfully',
                'files' => $uploadedFiles,
                'data' => $uploadedFiles
            ]);

        } catch (\Exception $e) {
            Log::error('Temp attachment upload failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete temporary attachment - FIXED FOR UNIVERSAL UPLOADER
     */
    public function deleteTempAttachment(Request $request)
    {
        try {
            $request->validate([
                'temp_id' => 'sometimes|string',
                'id' => 'sometimes|string',
                'file_path' => 'sometimes|string',
                'path' => 'sometimes|string',
            ]);

            $filePath = $request->input('file_path') ?? $request->input('path');

            if (!$filePath) {
                $tempId = $request->input('temp_id') ?? $request->input('id');
                if ($tempId) {
                    $directory = 'temp/message-attachments/' . session()->getId();
                    $files = Storage::disk('public')->files($directory);
                    
                    foreach ($files as $file) {
                        if (str_contains($file, $tempId) || str_contains(basename($file), $tempId)) {
                            $filePath = $file;
                            break;
                        }
                    }
                }
            }

            if (!$filePath) {
                return response()->json([
                    'success' => false,
                    'message' => 'File path not provided or found'
                ], 400);
            }

            // Security check
            $allowedPath = 'temp/message-attachments/' . session()->getId();
            if (!str_starts_with($filePath, $allowedPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized file access'
                ], 403);
            }

            if (Storage::disk('public')->exists($filePath)) {
                $deleted = Storage::disk('public')->delete($filePath);
                
                if ($deleted) {
                    return response()->json([
                        'success' => true,
                        'message' => 'File deleted successfully'
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'File not found or could not be deleted'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Temp attachment deletion failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get temporary files for current session
     */
    public function getTempFiles()
    {
        try {
            $directory = 'temp/message-attachments/' . session()->getId();
            
            if (!Storage::disk('public')->exists($directory)) {
                return response()->json([
                    'success' => true,
                    'files' => [],
                    'data' => []
                ]);
            }

            $files = Storage::disk('public')->files($directory);
            $fileData = [];
            
            foreach ($files as $file) {
                if (Storage::disk('public')->exists($file)) {
                    $originalName = basename($file);
                    if (preg_match('/^[a-f0-9]+_(.+)$/', $originalName, $matches)) {
                        $displayName = $matches[1];
                    } else {
                        $displayName = $originalName;
                    }

                    $fileData[] = [
                        'id' => pathinfo($file, PATHINFO_FILENAME),
                        'temp_id' => pathinfo($file, PATHINFO_FILENAME),
                        'is_temp' => true,
                        'path' => $file,
                        'file_path' => $file,
                        'name' => $displayName,
                        'file_name' => $displayName,
                        'original_name' => $displayName,
                        'size' => Storage::disk('public')->size($file),
                        'file_size' => Storage::disk('public')->size($file),
                        'mime_type' => Storage::disk('public')->mimeType($file),
                        'file_type' => Storage::disk('public')->mimeType($file),
                        'type' => Storage::disk('public')->mimeType($file),
                        'url' => Storage::disk('public')->url($file),
                        'file_url' => Storage::disk('public')->url($file),
                        'uploaded_at' => Storage::disk('public')->lastModified($file),
                        'category' => 'message_attachment',
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'files' => $fileData,
                'data' => $fileData
            ]);

        } catch (\Exception $e) {
            Log::error('Get temp files failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get files: ' . $e->getMessage(),
                'files' => [],
                'data' => []
            ], 500);
        }
    }

    /**
     * Cleanup temporary files for session
     */
    public function cleanupTempFiles()
    {
        try {
            $directory = 'temp/message-attachments/' . session()->getId();
            
            if (!Storage::disk('public')->exists($directory)) {
                return response()->json([
                    'success' => true,
                    'message' => 'No temporary files to clean up',
                    'deleted_count' => 0
                ]);
            }

            $files = Storage::disk('public')->files($directory);
            $deletedCount = 0;
            $cutoffTime = now()->subHours(24)->timestamp;

            foreach ($files as $file) {
                try {
                    $lastModified = Storage::disk('public')->lastModified($file);
                    
                    if ($lastModified < $cutoffTime) {
                        if (Storage::disk('public')->delete($file)) {
                            $deletedCount++;
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to delete temp file: ' . $file);
                    continue;
                }
            }

            // Try to remove empty directory
            try {
                $remainingFiles = Storage::disk('public')->files($directory);
                if (empty($remainingFiles)) {
                    Storage::disk('public')->deleteDirectory($directory);
                }
            } catch (\Exception $e) {
                // Directory deletion is not critical
            }

            return response()->json([
                'success' => true,
                'message' => "Cleaned up {$deletedCount} temporary files",
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Temp cleanup failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Cleanup failed: ' . $e->getMessage(),
                'deleted_count' => 0
            ], 500);
        }
    }

    // ===== API METHODS =====

    /**
     * Get message statistics for AJAX
     */
    public function getStatistics()
    {
        $user = auth()->user();

        try {
            $statistics = $this->messageService->getMessageStatistics($user);

            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get message statistics: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics'
            ], 500);
        }
    }

    /**
     * Mark all messages as read - USING SERVICE
     */
    public function markAllAsRead()
    {
        $user = auth()->user();

        try {
            $messageIds = $this->clientAccessService->getClientMessages($user)
                ->where('is_read', false)
                ->pluck('id')
                ->toArray();

            $count = $this->messageService->bulkMarkAsRead($messageIds, $user);

            // Clear dashboard cache
            $this->dashboardService->clearCache($user);

            // Return updated statistics
            $updatedStats = $this->messageService->getMessageStatistics($user);

            return response()->json([
                'success' => true,
                'message' => "{$count} messages marked as read",
                'count' => $count,
                'statistics' => $updatedStats
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to mark all messages as read: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark messages as read'
            ], 500);
        }
    }

    /**
     * API Toggle read status for AJAX
     */
    public function apiToggleRead(Message $message)
    {
        $user = auth()->user();

        try {
            // Security check
            if (!$this->clientAccessService->canAccessMessage($user, $message)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            if ($message->is_read) {
                $this->messageService->markAsUnread($message);
                $status = 'unread';
                $isRead = false;
            } else {
                $this->messageService->markAsRead($message, $user);
                $status = 'read';
                $isRead = true;
            }

            // Clear dashboard cache
            $this->dashboardService->clearCache($user);

            // Get updated statistics
            $updatedStats = $this->messageService->getMessageStatistics($user);

            return response()->json([
                'success' => true,
                'message' => "Message marked as {$status}",
                'is_read' => $isRead,
                'status' => $status,
                'statistics' => $updatedStats,
                'message_id' => $message->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to toggle message read status: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update message status'
            ], 500);
        }
    }

    // ===== PRIVATE HELPER METHODS =====

    /**
     * Auto-mark messages as read when viewing thread
     */
    private function autoMarkMessagesAsRead($thread, $user)
    {
        $markedCount = 0;
        $currentTime = now();
        
        // Handle both single message and thread collection
        $messages = is_iterable($thread) ? $thread : [$thread];
        
        foreach ($messages as $message) {
            // Only mark as read if it's unread and directed to client
            if (!$message->is_read && $this->isMessageToClient($message)) {
                try {
                    $this->messageService->markAsRead($message, $user);
                    $markedCount++;
                    
                    Log::info('Auto-marked message as read', [
                        'message_id' => $message->id,
                        'user_id' => $user->id,
                        'message_type' => $message->type,
                        'marked_at' => $currentTime->toISOString()
                    ]);
                    
                } catch (\Exception $e) {
                    Log::warning('Failed to auto-mark message as read', [
                        'message_id' => $message->id,
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
        
        if ($markedCount > 0) {
            Log::info("Auto-marked {$markedCount} messages as read", [
                'user_id' => $user->id,
                'context' => 'thread_view'
            ]);
        }
        
        return $markedCount;
    }

    /**
     * Check if message is directed to client
     */
    private function isMessageToClient(Message $message): bool
    {
        return in_array($message->type, [
            'admin_to_client',
            'support_response',
            'admin_reply'
        ]);
    }

    /**
     * Handle bulk delete with proper security checks
     */
    private function handleBulkDelete(array $messageIds, $user): int
    {
        // Get messages that can be deleted (client's own messages, not admin replies)
        $deletableMessages = Message::whereIn('id', $messageIds)
            ->where('user_id', $user->id)
            ->whereNotIn('type', ['admin_to_client', 'admin_reply'])
            ->get();

        $deletedCount = 0;

        foreach ($deletableMessages as $message) {
            try {
                // Delete attachments first (will be handled by model event)
                $message->delete();
                $deletedCount++;
            } catch (\Exception $e) {
                Log::warning('Failed to delete message in bulk action', [
                    'message_id' => $message->id,
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
                // Continue with other messages
            }
        }

        return $deletedCount;
    }

    /**
     * Return consistent response for bulk actions
     */
    private function bulkActionResponse(string $type, string $message, int $count)
    {
        if (request()->expectsJson()) {
            return response()->json([
                'success' => $type === 'success',
                'message' => $message,
                'count' => $count
            ], $type === 'success' ? 200 : 422);
        }

        return redirect()->back()->with($type, $message);
    }

    /**
     * Clean up session temp files
     */
    private function cleanupSessionTempFiles()
    {
        try {
            $directory = 'temp/message-attachments/' . session()->getId();
            
            if (Storage::disk('public')->exists($directory)) {
                $files = Storage::disk('public')->files($directory);
                
                foreach ($files as $file) {
                    Storage::disk('public')->delete($file);
                }
                
                // Remove empty directory
                Storage::disk('public')->deleteDirectory($directory);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to cleanup temp files: ' . $e->getMessage());
            // Don't fail the main operation if cleanup fails
        }
    }

    /**
     * Move temp file to permanent message attachment location
     */
    private function moveTempFileToMessage(string $tempPath, Message $message): bool
    {
        try {
            // Security check - ensure file is in temp directory
            if (!str_starts_with($tempPath, 'temp/message-attachments/')) {
                Log::warning('Invalid temp file path: ' . $tempPath);
                return false;
            }

            if (!Storage::disk('public')->exists($tempPath)) {
                Log::warning('Temp file not found: ' . $tempPath);
                return false;
            }

            // Get original filename from temp path
            $tempFilename = basename($tempPath);
            
            // Extract original name if it follows our naming pattern: uniqid_originalname.ext
            if (preg_match('/^[a-f0-9]+_(.+)$/', $tempFilename, $matches)) {
                $originalFilename = $matches[1];
            } else {
                $originalFilename = $tempFilename;
            }
            
            // Generate permanent file path
            $permanentPath = 'message-attachments/' . $message->id . '/' . $originalFilename;
            
            // Ensure directory exists
            $directory = dirname($permanentPath);
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            // Move file from temp to permanent location
            if (Storage::disk('public')->move($tempPath, $permanentPath)) {
                // Get file info
                $fileSize = Storage::disk('public')->size($permanentPath);
                $mimeType = Storage::disk('public')->mimeType($permanentPath);
                
                // Create attachment record using MessageAttachment model structure
                $message->attachments()->create([
                    'file_path' => $permanentPath,
                    'file_name' => $originalFilename,
                    'file_type' => $mimeType,
                    'file_size' => $fileSize,
                ]);

                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Failed to move temp file to message: ' . $e->getMessage(), [
                'temp_path' => $tempPath,
                'message_id' => $message->id
            ]);
            
            return false;
        }
    }

    /**
     * Get available message types for clients
     */
    protected function getClientMessageTypes(): array
    {
        return [
            'general' => 'General Inquiry',
            'support' => 'Technical Support',
            'project_inquiry' => 'Project Related',
            'complaint' => 'Complaint',
            'feedback' => 'Feedback',
        ];
    }
}