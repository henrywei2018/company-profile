<?php
// app/Http/Controllers/Client/MessageController.php
// Clean version focused on web requests only

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Services\ClientAccessService;
use App\Services\MessageService;
use App\Services\DashboardService;
use App\Services\UniversalFileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
     * Store a newly created message.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'type' => 'required|in:general_inquiry,project_question,support_request,feedback,complaint',
            'priority' => 'nullable|in:normal,urgent',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
            'temp_files' => 'nullable|string', // JSON string of temp file paths
        ]);

        try {
            DB::beginTransaction();

            // Create the message
            $message = Message::create([
                'type' => 'client_to_admin',
                'subject' => $validated['subject'],
                'message' => $validated['message'],
                'priority' => $validated['priority'] ?? 'normal',
                'user_id' => $user->id,
                'is_read' => false,
                'is_replied' => false,
            ]);

            // Handle temp files if any
            $attachmentCount = 0;
            if ($request->filled('temp_files')) {
                $tempFilePaths = json_decode($request->input('temp_files'), true);

                if (is_array($tempFilePaths)) {
                    foreach ($tempFilePaths as $tempPath) {
                        if ($this->moveTempFileToMessage($tempPath, $message)) {
                            $attachmentCount++;
                        }
                    }
                }
            }

            // Clear dashboard cache
            $this->dashboardService->clearCache($user);

            DB::commit();

            $successMessage = 'Message sent successfully!';
            if ($attachmentCount > 0) {
                $successMessage .= " ({$attachmentCount} attachment" . ($attachmentCount > 1 ? 's' : '') . " included)";
            }

            return redirect()->route('client.messages.index')
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create message: ' . $e->getMessage());

            return redirect()->back()
                ->withErrors(['error' => 'Failed to send message. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Display the specified message with thread.
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

        // Mark as read if it's a message TO the client and unread
        if (!$message->is_read && $this->isMessageToClient($message)) {
            $this->messageService->markAsRead($message, $user);
            $this->dashboardService->clearCache($user);
        }

        // Get the complete conversation thread (root + all replies)
        $thread = $this->messageService->getMessageThread($message);

        // Get the root message for the conversation
        $rootMessage = $message->parent_id ? $message->parent : $message;

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
     * Reply to an admin message.
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

            // Create reply message
            $reply = Message::create([
                'type' => 'client_reply',
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

            // Mark original message as replied if it's from admin
            if ($message->type === 'admin_to_client') {
                $message->update(['is_replied' => true, 'replied_at' => now()]);
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
            $originalFilename = basename($tempPath);

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

                // Create attachment record using your MessageAttachment model structure
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
    public function uploadTempAttachment(Request $request, UniversalFileUploadService $uploadService)
    {
        try {
            $config = [
                'disk' => 'public',
                'max_file_size' => 10 * 1024 * 1024, // 10MB
                'max_files' => 5,
                'allowed_types' => [
                    'image/jpeg',
                    'image/png',
                    'image/gif',
                    'image/webp',
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'text/plain',
                    'text/csv',
                    'application/zip',
                    'application/x-rar-compressed'
                ]
            ];

            $directory = 'temp/message-attachments/' . session()->getId();

            $uploadedFiles = $uploadService->uploadFiles(
                $request,
                $directory,
                $config
            );

            return response()->json([
                'success' => true,
                'message' => 'Files uploaded successfully',
                'files' => $uploadedFiles
            ]);

        } catch (\Exception $e) {
            \Log::error('Temp attachment upload failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function deleteTempAttachment(Request $request, UniversalFileUploadService $uploadService)
    {
        try {
            $request->validate([
                'file_path' => 'required|string'
            ]);

            $filePath = $request->input('file_path');

            // Security check - ensure file is in temp directory for this session
            $allowedPath = 'temp/message-attachments/' . session()->getId();
            if (!str_starts_with($filePath, $allowedPath)) {
                throw new \Exception('Unauthorized file access');
            }

            $deleted = $uploadService->deleteFile($filePath);

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'File deleted successfully'
                ]);
            }

            throw new \Exception('Failed to delete file');

        } catch (\Exception $e) {
            \Log::error('Temp attachment deletion failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
    public function getTempFiles()
    {
        try {
            $directory = 'temp/message-attachments/' . session()->getId();
            $files = Storage::disk('public')->files($directory);

            $fileData = [];
            foreach ($files as $file) {
                if (Storage::disk('public')->exists($file)) {
                    $fileData[] = [
                        'path' => $file,
                        'name' => basename($file),
                        'size' => Storage::disk('public')->size($file),
                        'url' => Storage::disk('public')->url($file),
                        'uploaded_at' => Storage::disk('public')->lastModified($file)
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'files' => $fileData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
    public function cleanupTempFiles(UniversalFileUploadService $uploadService)
    {
        try {
            $directory = 'temp/message-attachments/' . session()->getId();
            $deletedCount = $uploadService->cleanupOldFiles($directory, 24);

            return response()->json([
                'success' => true,
                'message' => "Cleaned up {$deletedCount} temporary files"
            ]);

        } catch (\Exception $e) {
            \Log::error('Temp cleanup failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Mark message as urgent (escalate priority).
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
     * Toggle message read status.
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
     * Get project-specific messages.
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
     * Bulk action for multiple messages.
     */
    public function bulkAction(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'action' => 'required|string|in:mark_read,mark_unread,delete',
            'message_ids' => 'required|array',
            'message_ids.*' => 'integer|exists:messages,id',
        ]);

        // Get messages that belong to the client
        $messages = $this->clientAccessService->getClientMessages($user)
            ->whereIn('id', $validated['message_ids'])
            ->get();

        if ($messages->isEmpty()) {
            return redirect()->back()
                ->with('error', 'No valid messages selected.');
        }

        $count = 0;
        foreach ($messages as $message) {
            switch ($validated['action']) {
                case 'mark_read':
                    if (!$message->is_read) {
                        $this->messageService->markAsRead($message, $user);
                        $count++;
                    }
                    break;

                case 'mark_unread':
                    if ($message->is_read) {
                        $this->messageService->markAsUnread($message);
                        $count++;
                    }
                    break;

                case 'delete':
                    // Only allow deletion of client's own messages (not admin replies)
                    if (
                        $message->user_id === $user->id &&
                        !in_array($message->type, ['admin_to_client'])
                    ) {
                        $message->delete();
                        $count++;
                    }
                    break;
            }
        }

        // Clear dashboard cache
        $this->dashboardService->clearCache($user);

        $actionName = str_replace('_', ' ', $validated['action']);
        return redirect()->back()
            ->with('success', "{$count} messages {$actionName}.");
    }

    /**
     * Download an attachment.
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

    /**
     * Get available message types for clients.
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
    public function getStatistics()
    {
        $user = auth()->user();

        try {
            // Use existing MessageService method that already provides all needed data
            $statistics = $this->messageService->getMessageStatistics($user);

            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics'
            ], 500);
        }
    }
    public function markAllAsRead()
    {
        $user = auth()->user();

        try {
            $messages = $this->clientAccessService->getClientMessages($user)
                ->where('is_read', false)
                ->get();

            $count = 0;
            foreach ($messages as $message) {
                $this->messageService->markAsRead($message, $user);
                $count++;
            }

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

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark messages as read'
            ], 500);
        }
    }
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

            $originalStatus = $message->is_read;

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
            Log::error('Failed to toggle message read status: ' . $e->getMessage(), [
                'message_id' => $message->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update message status'
            ], 500);
        }
    }

    /**
     * Check if message is directed to client.
     */
    private function isMessageToClient(Message $message): bool
    {
        return in_array($message->type, ['admin_to_client', 'support_response']);
    }
}