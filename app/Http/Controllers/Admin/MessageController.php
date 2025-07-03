<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\User;
use App\Models\Project;
use App\Facades\Notifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Services\DashboardService;
use App\Services\MessageService;
use App\Services\TempNotifiable;

class MessageController extends Controller
{

    protected MessageService $messageService;
    protected DashboardService $dashboardService;

    public function __construct(MessageService $messageService, DashboardService $dashboardService)
    {
        $this->messageService = $messageService;
        $this->dashboardService = $dashboardService;
    }
    public function index(Request $request)
    {
        $query = Message::query()
            ->with(['user', 'attachments', 'repliedBy'])
            ->excludeAdminMessages();

        // Apply search filter
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Apply status filter  
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'unread':
                    $query->where('is_read', false);
                    break;
                case 'read':
                    $query->where('is_read', true);
                    break;
                case 'unreplied':
                    $query->where('is_replied', false);
                    break;
                case 'replied':
                    $query->where('is_replied', true);
                    break;
                case 'unread_unreplied':
                    $query->where('is_read', false)->where('is_replied', false);
                    break;
            }
        }

        // Apply type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Apply date range filter
        if ($request->filled('created_from') || $request->filled('created_to')) {
            if ($request->filled('created_from') && $request->filled('created_to')) {
                $query->whereBetween('created_at', [
                    $request->created_from . ' 00:00:00',
                    $request->created_to . ' 23:59:59'
                ]);
            } elseif ($request->filled('created_from')) {
                $query->where('created_at', '>=', $request->created_from . ' 00:00:00');
            } elseif ($request->filled('created_to')) {
                $query->where('created_at', '<=', $request->created_to . ' 23:59:59');
            }
        }

        // Apply sorting
        if ($request->filled('sort') && $request->filled('direction')) {
            $query->orderBy($request->sort, $request->direction);
        } else {
            $query->orderByRaw('
                CASE 
                    WHEN is_replied = 0 AND is_read = 0 THEN 1
                    WHEN is_replied = 0 AND is_read = 1 THEN 2  
                    WHEN is_replied = 1 AND is_read = 0 THEN 3
                    ELSE 4
                END ASC, created_at DESC
            ');
        }

        $messages = $query->paginate(15)->withQueryString();

        // Get counts for different statuses
        $statusCounts = [
            'total' => Message::excludeAdminMessages()->count(),
            'unread' => Message::excludeAdminMessages()->unread()->count(),
            'unreplied' => Message::excludeAdminMessages()->unreplied()->count(),
            'unread_unreplied' => Message::excludeAdminMessages()->unread()->unreplied()->count(),
        ];
        $statistics = $this->getStats();
        $unreadMessages = Message::excludeAdminMessages()->unread()->count();
        $pendingQuotations = \App\Models\Quotation::where('status', 'pending')->count();

        return view('admin.messages.index', compact('messages', 'statistics', 'statusCounts', 'unreadMessages', 'pendingQuotations'));
    }

    /**
     * Show the form for creating a new direct message to a client.
     */
    public function create(Request $request)
    {
        // Get clients for dropdown
        $clients = User::whereHas('roles', function ($query) {
            $query->where('name', 'client');
        })
            ->select('id', 'name', 'email', 'company')
            ->orderBy('name')
            ->get();

        // Get projects for dropdown
        $projects = Project::with('client')
            ->select('id', 'title', 'client_id', 'status')
            ->orderBy('title')
            ->get();

        // Pre-fill data if specified
        $selectedClient = null;
        $selectedProject = null;

        if ($request->filled('client_id')) {
            $selectedClient = $clients->find($request->client_id);
        }

        if ($request->filled('project_id')) {
            $selectedProject = $projects->find($request->project_id);
            if ($selectedProject && !$selectedClient) {
                $selectedClient = $clients->find($selectedProject->client_id);
            }
        }

        return view('admin.messages.create', compact(
            'clients',
            'projects',
            'selectedClient',
            'selectedProject'
        ));
    }

    /**
     * Store a newly created direct message using centralized notifications.
     */
    public function store(Request $request)
    {
        try {
            // Validation - make temp_files nullable to handle no file submissions
            $validated = $request->validate([
                'priority' => 'nullable|in:normal,urgent',
                'subject' => 'required|string|max:255|min:3',
                'message' => 'required|string|min:10|max:10000',
                'temp_files' => 'nullable|string', // Allow null when no files
                'recipient_email' => 'required|email',
                'recipient_name' => 'nullable|string|max:255',
                'type' => 'nullable|string|in:admin_to_client,general',
            ]);

            Log::info('Admin message validation passed', [
                'has_temp_files' => !empty($validated['temp_files']),
                'temp_files_length' => strlen($validated['temp_files'] ?? ''),
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Admin message validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->except(['temp_files', '_token'])
            ]);
            throw $e;
        }

        try {
            DB::beginTransaction();

            // Create the message
            $messageData = [
                'type' => $validated['type'] ?? 'admin_to_client',
                'name' => $validated['recipient_name'] ?? 'Client',
                'email' => $validated['recipient_email'],
                'subject' => $validated['subject'],
                'message' => $validated['message'],
                'priority' => $validated['priority'] ?? 'normal',
                'user_id' => null, // Admin messages don't need user_id
                'is_read' => true, // Admin messages are read by default
                'is_replied' => false,
                'sent_by_admin' => true,
                'sent_by' => auth()->id(),
            ];

            Log::info('Creating admin message', $messageData);

            $message = Message::create($messageData);

            Log::info('Admin message created successfully', [
                'message_id' => $message->id,
                'message_type' => $message->type
            ]);

            // Handle temp files - FIXED to properly handle empty submissions
            $attachmentCount = 0;

            // Only process temp_files if it's not empty
            if (!empty($validated['temp_files']) && trim($validated['temp_files']) !== '' && $validated['temp_files'] !== 'null') {
                Log::info('Processing admin temp files', [
                    'temp_files_raw' => $validated['temp_files']
                ]);

                $tempFilePaths = json_decode($validated['temp_files'], true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::error('JSON decode error for admin temp_files', [
                        'json_error' => json_last_error_msg(),
                        'temp_files_raw' => $validated['temp_files']
                    ]);
                } elseif (is_array($tempFilePaths) && !empty($tempFilePaths)) {
                    Log::info('Found admin temp files to process', [
                        'file_count' => count($tempFilePaths),
                        'file_paths' => $tempFilePaths
                    ]);

                    foreach ($tempFilePaths as $tempPath) {
                        try {
                            if ($this->moveTempFileToMessage($tempPath, $message)) {
                                $attachmentCount++;
                                Log::info('Admin attachment processed successfully', [
                                    'temp_path' => $tempPath,
                                    'message_id' => $message->id
                                ]);
                            } else {
                                Log::warning('Failed to process admin attachment', [
                                    'temp_path' => $tempPath,
                                    'message_id' => $message->id
                                ]);
                            }
                        } catch (\Exception $attachmentError) {
                            Log::error('Admin attachment processing error', [
                                'temp_path' => $tempPath,
                                'message_id' => $message->id,
                                'error' => $attachmentError->getMessage()
                            ]);
                        }
                    }
                } else {
                    Log::info('No valid temp files found in admin submission', [
                        'temp_files_decoded' => $tempFilePaths
                    ]);
                }
            } else {
                Log::info('No temp files submitted with admin message - this is OK');
            }

            // Clean up temp directory after successful processing
            try {
                $this->cleanupSessionTempFiles();
            } catch (\Exception $cleanupError) {
                Log::warning('Failed to cleanup temp files after admin message: ' . $cleanupError->getMessage());
            }

            DB::commit();

            $successMessage = 'Message sent successfully!';
            if ($attachmentCount > 0) {
                $successMessage .= " ({$attachmentCount} attachment" . ($attachmentCount > 1 ? 's' : '') . " included)";
            }

            Log::info('Admin message sent successfully', [
                'message_id' => $message->id,
                'attachment_count' => $attachmentCount,
                'sent_by' => auth()->id()
            ]);

            return redirect()->route('admin.messages.index')
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to send admin message', [
                'sent_by' => auth()->id(),
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to send message. Please try again.')
                ->withInput();
        }
    }
    public function storeReply(Request $request, Message $message)
{
    // Enhanced validation
    $request->validate([
        'subject' => 'required|string|max:255|min:3',
        'message' => 'required|string|min:10|max:10000',
        'temp_files' => 'nullable|string', // Critical: This accepts the JSON string of file paths
    ], [
        'subject.required' => 'Please enter a subject for your reply.',
        'subject.min' => 'Subject must be at least 3 characters long.',
        'message.required' => 'Please enter your reply message.',
        'message.min' => 'Reply message must be at least 10 characters long.',
        'message.max' => 'Reply message cannot exceed 10,000 characters.',
    ]);

    try {
        DB::beginTransaction();

        // Create the reply message
        $reply = Message::create([
            'type' => 'admin_to_client',
            'name' => auth()->user()->name ?? 'Admin',
            'email' => settings('mail_from_address', config('mail.from.address', 'admin@company.com')),
            'subject' => trim($request->subject),
            'message' => trim($request->message),
            'parent_id' => $message->id,
            'user_id' => $message->user_id,
            'is_read' => true,
            'is_replied' => false,
            'read_at' => now(),
            'replied_by' => auth()->id(),
        ]);

        Log::info('Admin reply created', [
            'reply_id' => $reply->id,
            'parent_id' => $message->id,
            'admin_id' => auth()->id()
        ]);

        // Mark the original message as replied
        if (!$message->is_replied) {
            $message->markAsReplied(auth()->id());
        }

        // ========================================
        // FIXED: Complete temp files processing
        // ========================================
        $attachmentCount = 0;
        
        // Check if temp_files is provided and not empty
        if ($request->filled('temp_files') && trim($request->temp_files) !== '' && $request->temp_files !== 'null') {
            Log::info('Processing admin reply temp files', [
                'temp_files_raw' => $request->input('temp_files'),
                'reply_id' => $reply->id
            ]);

            // Decode JSON
            $tempFilePaths = json_decode($request->input('temp_files'), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON decode error for admin reply temp_files', [
                    'json_error' => json_last_error_msg(),
                    'temp_files_raw' => $request->input('temp_files'),
                    'reply_id' => $reply->id
                ]);
            } elseif (is_array($tempFilePaths) && !empty($tempFilePaths)) {
                Log::info('Found admin reply temp files to process', [
                    'file_count' => count($tempFilePaths),
                    'file_paths' => $tempFilePaths,
                    'reply_id' => $reply->id
                ]);

                // Process each temp file
                foreach ($tempFilePaths as $tempPath) {
                    try {
                        if ($this->moveTempFileToMessage($tempPath, $reply)) {
                            $attachmentCount++;
                            Log::info('Admin reply attachment processed successfully', [
                                'temp_path' => $tempPath,
                                'reply_id' => $reply->id,
                                'attachment_number' => $attachmentCount
                            ]);
                        } else {
                            Log::warning('Failed to process admin reply attachment', [
                                'temp_path' => $tempPath,
                                'reply_id' => $reply->id
                            ]);
                        }
                    } catch (\Exception $attachmentError) {
                        Log::error('Admin reply attachment processing error', [
                            'temp_path' => $tempPath,
                            'reply_id' => $reply->id,
                            'error' => $attachmentError->getMessage(),
                            'trace' => $attachmentError->getTraceAsString()
                        ]);
                    }
                }
            } else {
                Log::info('No valid temp files found in admin reply submission', [
                    'temp_files_decoded' => $tempFilePaths,
                    'reply_id' => $reply->id
                ]);
            }
        } else {
            Log::info('No temp files submitted with admin reply', [
                'reply_id' => $reply->id,
                'temp_files_filled' => $request->filled('temp_files'),
                'temp_files_value' => $request->input('temp_files')
            ]);
        }

        // Clean up temp directory after successful processing
        try {
            $this->cleanupSessionTempFiles();
        } catch (\Exception $cleanupError) {
            Log::warning('Failed to cleanup temp files after admin reply: ' . $cleanupError->getMessage());
        }

        // Send notification to client
        try {
            if ($message->user) {
                // Send to registered client - use your existing notification system
                Notifications::send('message.reply', $reply, $message->user);
            } else {
                // Send to email address - use your existing notification system
                $tempNotifiable = new TempNotifiable($message->email, $message->name);
                Notifications::send('message.reply', $reply, $tempNotifiable);
            }

            Log::info('Admin reply notification sent', [
                'reply_id' => $reply->id,
                'recipient_email' => $message->email,
                'attachment_count' => $attachmentCount
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send admin reply notification: ' . $e->getMessage());

            // Still commit but show warning
            DB::commit();

            return redirect()->route('admin.messages.show', $message)
                ->with('warning', 'Reply saved but there was an issue sending the email notification. Attachment count: ' . $attachmentCount);
        }

        DB::commit();

        // Success message with attachment count
        $successMessage = 'Reply sent successfully!';
        if ($attachmentCount > 0) {
            $successMessage .= " ({$attachmentCount} attachment" . ($attachmentCount > 1 ? 's' : '') . " included)";
        }

        Log::info('Admin reply sent successfully', [
            'reply_id' => $reply->id,
            'attachment_count' => $attachmentCount,
            'admin_id' => auth()->id()
        ]);

        return redirect()->route('admin.messages.show', $message)
            ->with('success', $successMessage);

    } catch (\Exception $e) {
        DB::rollBack();
        
        Log::error('Failed to send admin reply', [
            'message_id' => $message->id,
            'admin_id' => auth()->id(),
            'error_message' => $e->getMessage(),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine(),
            'stack_trace' => $e->getTraceAsString()
        ]);

        return redirect()->back()
            ->with('error', 'Failed to send reply. Please try again.')
            ->withInput();
    }
}
    public function forward(Request $request, Message $message)
    {
        $request->validate([
            'recipient_type' => 'required|in:client,email',
            'client_id' => 'required_if:recipient_type,client|exists:users,id',
            'recipient_email' => 'required_if:recipient_type,email|email',
            'recipient_name' => 'required_if:recipient_type,email|string|max:255',
            'forward_message' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Prepare forwarded message content
            $forwardedContent = $this->prepareForwardedContent($message, $request->forward_message);

            // Prepare message data
            $messageData = [
                'type' => 'admin_to_client',
                'name' => auth()->user()->name ?? 'Admin',
                'email' => settings('mail_from_address', config('mail.from.address', 'admin@company.com')),
                'subject' => 'Fwd: ' . $message->subject,
                'message' => $forwardedContent,
                'priority' => $message->priority,
                'project_id' => $message->project_id,
                'is_read' => true,
                'is_replied' => false,
                'read_at' => now(),
                'replied_by' => auth()->id(),
            ];

            // Set recipient details
            if ($request->recipient_type === 'client') {
                $client = User::findOrFail($request->client_id);
                $messageData['user_id'] = $client->id;
            }

            // Create forwarded message
            $forwardedMessage = Message::create($messageData);

            // Copy attachments from original message
            $this->copyAttachments($message, $forwardedMessage);

            DB::commit();

            Log::info('Message forwarded successfully', [
                'original_message_id' => $message->id,
                'forwarded_message_id' => $forwardedMessage->id,
                'admin_id' => auth()->id(),
                'recipient_type' => $request->recipient_type,
            ]);

            return redirect()->route('admin.messages.show', $message)
                ->with('success', 'Message forwarded successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Message forward failed', [
                'message_id' => $message->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'Failed to forward message. Please try again.'])
                ->withInput();
        }
    }

    public function storePublicMessage(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
            'priority' => 'nullable|in:normal,urgent',
            'attachments.*' => 'nullable|file|max:5120|mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);

        try {
            DB::beginTransaction();

            // Check if user exists
            $user = User::where('email', $validated['email'])->first();

            // Determine priority and check for urgent keywords
            $priority = $validated['priority'] ?? 'normal';
            $isUrgent = $priority === 'urgent' ||
                str_contains(strtolower($validated['subject']), 'urgent') ||
                str_contains(strtolower($validated['subject']), 'emergency');

            // Create the message
            $message = Message::create([
                'type' => 'contact_form',
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'company' => $validated['company'] ?? null,
                'subject' => $validated['subject'],
                'message' => $validated['message'],
                'priority' => $isUrgent ? 'urgent' : 'normal',
                'user_id' => $user?->id,
                'is_read' => false,
                'is_replied' => false,
            ]);

            // Handle attachments
            $attachmentCount = 0;
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    if ($file->isValid() && $attachmentCount < 3) {
                        try {
                            $message->addAttachment($file);
                            $attachmentCount++;
                        } catch (\Exception $e) {
                            Log::error('Failed to add public message attachment: ' . $e->getMessage());
                        }
                    }
                }
            }

            // Send notifications using centralized system
            try {
                // Create temporary notifiable for sender
                $senderNotifiable = new TempNotifiable($validated['email'], $validated['name']);

                // Send auto-reply to sender if enabled
                if (settings('message_auto_reply_enabled', true)) {
                    Notifications::send('message.auto_reply', $message, $senderNotifiable);
                }

                // Send notification to admin - use appropriate type based on urgency
                $notificationType = $isUrgent ? 'message.urgent' : 'message.created';
                Notifications::send($notificationType, $message);

                Log::info('Public message received and notifications sent', [
                    'message_id' => $message->id,
                    'sender_email' => $validated['email'],
                    'is_urgent' => $isUrgent,
                    'auto_reply_sent' => settings('message_auto_reply_enabled', true)
                ]);

            } catch (\Exception $e) {
                Log::error('Failed to send message notifications: ' . $e->getMessage(), [
                    'message_id' => $message->id
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Thank you for your message! We will get back to you soon.',
                'data' => [
                    'id' => $message->id,
                    'auto_reply_sent' => settings('message_auto_reply_enabled', true),
                    'is_urgent' => $isUrgent
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to store public message: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to send message. Please try again.'
            ], 500);
        }
    }
    public function bulkMarkAsUnread(Request $request): JsonResponse
    {
        $request->validate([
            'message_ids' => 'required|array',
            'message_ids.*' => 'exists:messages,id'
        ]);

        try {
            $updated = Message::whereIn('id', $request->message_ids)
                ->where('is_read', true)
                ->update([
                    'is_read' => false,
                    'read_at' => null,
                ]);

            // Clear caches
            $this->clearMessageCaches();

            Log::info('Bulk messages marked as unread by admin', [
                'admin_id' => auth()->id(),
                'message_count' => $updated,
            ]);

            return response()->json([
                'success' => true,
                'message' => "{$updated} messages marked as unread",
                'updated_count' => $updated
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk mark as unread failed', [
                'admin_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark messages as unread'
            ], 500);
        }
    }
    public function bulkAction(Request $request)
    {
        $admin = auth()->user();

        // Validate request with admin-specific actions
        $validated = $request->validate([
            'action' => 'required|string|in:mark_read,mark_unread,delete,delete_thread,archive,change_priority,assign_to_admin',
            'message_ids' => 'required|array|min:1',
            'message_ids.*' => 'integer|exists:messages,id',
            'force' => 'sometimes|boolean',
            'priority' => 'sometimes|string|in:low,normal,high,urgent', // For priority change
            'assigned_admin_id' => 'sometimes|integer|exists:users,id', // For assignment
        ]);

        // Admin can access all messages (no client restriction like in client controller)
        $messageIds = $validated['message_ids'];

        // Validate that messages exist and are not admin-to-admin messages
        $validMessageIds = Message::excludeAdminMessages()
            ->whereIn('id', $messageIds)
            ->pluck('id')
            ->toArray();

        if (empty($validMessageIds)) {
            return $this->bulkActionResponse('error', 'No valid messages selected.', 0);
        }

        // Validate bulk operation permissions for admin
        $validation = $this->messageService->validateBulkOperation(
            $validated['action'],
            $validMessageIds,
            $admin
        );

        if (!$validation['valid']) {
            return response()->json([
                'success' => false,
                'message' => 'Operation not allowed',
                'errors' => $validation['errors']
            ], 400);
        }

        $count = 0;
        $actionName = str_replace('_', ' ', $validated['action']);

        try {
            DB::beginTransaction();

            switch ($validated['action']) {
                case 'mark_read':
                    $count = $this->messageService->bulkMarkAsRead($validMessageIds, $admin);
                    break;

                case 'mark_unread':
                    $count = $this->messageService->bulkMarkAsUnread($validMessageIds, $admin);
                    break;

                case 'delete':
                    // Admin can force delete
                    $count = $this->messageService->bulkDeleteMessages($validMessageIds, $admin, [
                        'delete_threads' => false,
                        'force' => $validated['force'] ?? false
                    ]);
                    break;

                case 'delete_thread':
                    // Delete entire conversations
                    $count = $this->messageService->bulkDeleteMessages($validMessageIds, $admin, [
                        'delete_threads' => true,
                        'force' => $validated['force'] ?? false
                    ]);
                    $actionName = 'conversations deleted';
                    break;

                case 'change_priority':
                    // Change priority of messages
                    if (!isset($validated['priority'])) {
                        throw new \Exception('Priority is required for priority change action');
                    }
                    $count = $this->messageService->bulkChangePriority($validMessageIds, $validated['priority'], $admin);
                    $actionName = "priority changed to {$validated['priority']}";
                    break;

                case 'assign_to_admin':
                    // Assign messages to another admin (if you have this feature)
                    if (!isset($validated['assigned_admin_id'])) {
                        throw new \Exception('Admin ID is required for assignment action');
                    }
                    $count = $this->messageService->bulkAssignToAdmin($validMessageIds, $validated['assigned_admin_id'], $admin);
                    $actionName = 'messages assigned';
                    break;

                default:
                    throw new \Exception('Invalid action specified');
            }

            // Clear dashboard cache after successful operation
            $this->dashboardService->clearCache($admin);

            DB::commit();

            Log::info('Admin bulk action performed', [
                'admin_id' => $admin->id,
                'admin_name' => $admin->name,
                'action' => $validated['action'],
                'message_count' => $count,
                'message_ids' => $validMessageIds,
                'additional_data' => [
                    'priority' => $validated['priority'] ?? null,
                    'assigned_admin_id' => $validated['assigned_admin_id'] ?? null,
                    'force' => $validated['force'] ?? false
                ]
            ]);

            $responseData = ['warnings' => $validation['warnings'] ?? []];

            // Add warnings if any
            if (!empty($validation['warnings'])) {
                $message = "{$count} {$actionName} completed with warnings";
                return $this->bulkActionResponse('success', $message, $count, $responseData);
            }

            $message = "{$count} {$actionName} completed successfully";
            return $this->bulkActionResponse('success', $message, $count, $responseData);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Admin bulk action failed', [
                'admin_id' => $admin->id,
                'action' => $validated['action'],
                'message_ids' => $validMessageIds,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->bulkActionResponse('error', 'Failed to perform bulk action: ' . $e->getMessage(), 0);
        }
    }

    /**
     * Get available bulk actions for admin
     */
    public function getBulkActions()
    {
        $admin = auth()->user();
        $options = $this->messageService->getBulkOperationOptions($admin);

        return response()->json([
            'success' => true,
            'actions' => $options['available_operations'],
            'default_delete_mode' => $options['default_delete_mode'],
            'can_force_delete' => $options['can_force_delete'],
            'can_delete_admin_messages' => $options['can_delete_admin_messages'],
        ]);
    }

    /**
     * Bulk update priority
     */
    public function bulkUpdatePriority(Request $request)
    {
        $admin = auth()->user();

        $validated = $request->validate([
            'priority' => 'required|string|in:low,normal,high,urgent',
            'message_ids' => 'required|array|min:1',
            'message_ids.*' => 'integer|exists:messages,id',
        ]);

        try {
            DB::beginTransaction();

            $validMessageIds = Message::excludeAdminMessages()
                ->whereIn('id', $validated['message_ids'])
                ->pluck('id')
                ->toArray();

            if (empty($validMessageIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid messages selected'
                ], 400);
            }

            $count = $this->messageService->bulkChangePriority(
                $validMessageIds,
                $validated['priority'],
                $admin
            );

            // Clear dashboard cache
            $this->dashboardService->clearCache($admin);

            DB::commit();

            Log::info('Admin bulk priority update', [
                'admin_id' => $admin->id,
                'priority' => $validated['priority'],
                'message_count' => $count,
                'message_ids' => $validMessageIds
            ]);

            return $this->bulkActionResponse(
                'success',
                "Successfully updated priority to {$validated['priority']} for {$count} message(s)",
                $count
            );

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Admin bulk priority update failed', [
                'admin_id' => $admin->id,
                'priority' => $validated['priority'],
                'message_ids' => $validated['message_ids'],
                'error' => $e->getMessage()
            ]);

            return $this->bulkActionResponse(
                'error',
                'Failed to update priority: ' . $e->getMessage(),
                0
            );
        }
    }

    /**
     * Preview bulk action impact (admin version)
     */
    public function previewBulkAction(Request $request)
    {
        $admin = auth()->user();

        $validated = $request->validate([
            'action' => 'required|string|in:mark_read,mark_unread,delete,delete_thread,archive,change_priority',
            'message_ids' => 'required|array|min:1',
            'message_ids.*' => 'integer|exists:messages,id',
        ]);

        try {
            $validMessageIds = Message::excludeAdminMessages()
                ->whereIn('id', $validated['message_ids'])
                ->pluck('id')
                ->toArray();

            if (empty($validMessageIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid messages selected'
                ], 400);
            }

            $impactInfo = $this->getAdminActionImpactInfo($validated['action'], $validMessageIds);

            return response()->json([
                'success' => true,
                'impact_info' => $impactInfo,
                'requires_confirmation' => $impactInfo['high_impact'] || $impactInfo['has_urgent'],
                'message' => $this->getImpactMessage($impactInfo)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to preview bulk action impact', [
                'admin_id' => $admin->id,
                'action' => $validated['action'],
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to preview action impact'
            ], 500);
        }
    }

    /**
     * Helper method for bulk action responses with admin statistics
     */
    private function bulkActionResponse(string $type, string $message, int $count, array $extra = []): JsonResponse
    {
        $response = [
            'success' => $type === 'success',
            'message' => $message,
            'count' => $count,
        ];

        if ($type === 'success') {
            // Get updated statistics after successful operation
            try {
                $response['statistics'] = $this->getStats(); // Use existing method
            } catch (\Exception $e) {
                // Don't fail the response if stats fail
                Log::warning('Failed to get updated statistics after admin bulk action', [
                    'error' => $e->getMessage()
                ]);
            }
        }

        return response()->json(array_merge($response, $extra));
    }

    /**
     * Get impact information for admin actions
     */
    private function getAdminActionImpactInfo(string $action, array $messageIds): array
    {
        $messages = Message::whereIn('id', $messageIds)->get();

        $impactInfo = [
            'total_messages' => $messages->count(),
            'urgent_messages' => $messages->where('priority', 'urgent')->count(),
            'unread_messages' => $messages->where('is_read', false)->count(),
            'replied_messages' => $messages->where('is_replied', true)->count(),
            'project_linked' => $messages->whereNotNull('project_id')->count(),
            'with_attachments' => $messages->filter(fn($m) => $m->attachments()->count() > 0)->count(),
            'unique_clients' => $messages->whereNotNull('user_id')->unique('user_id')->count(),
            'threads_affected' => $this->getAffectedThreadsCount($messages),
            'has_urgent' => $messages->where('priority', 'urgent')->count() > 0,
            'high_impact' => false,
        ];

        // Determine if this is high impact for admin
        $impactInfo['high_impact'] = 
            $impactInfo['urgent_messages'] > 2 ||
            $impactInfo['project_linked'] > 5 ||
            $impactInfo['unique_clients'] > 3 ||
            ($action === 'delete' && $impactInfo['replied_messages'] > 10);

        return $impactInfo;
    }

    /**
     * Get count of affected conversation threads
     */
    private function getAffectedThreadsCount($messages): int
    {
        $rootIds = $messages->map(function ($message) {
            return $message->parent_id ?? $message->id;
        })->unique();

        return $rootIds->count();
    }

    /**
     * Generate impact message for preview
     */
    private function getImpactMessage(array $impactInfo): string
    {
        $parts = [];

        if ($impactInfo['urgent_messages'] > 0) {
            $parts[] = "{$impactInfo['urgent_messages']} urgent message(s)";
        }

        if ($impactInfo['project_linked'] > 0) {
            $parts[] = "{$impactInfo['project_linked']} project-related message(s)";
        }

        if ($impactInfo['unique_clients'] > 1) {
            $parts[] = "messages from {$impactInfo['unique_clients']} different client(s)";
        }

        if (empty($parts)) {
            return "This action will affect {$impactInfo['total_messages']} message(s).";
        }

        return "This action will affect " . implode(', ', $parts) . ".";
    }
    public function assignToProject(Request $request, Message $message): JsonResponse
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id'
        ]);

        try {
            $project = Project::findOrFail($request->project_id);

            // Verify project belongs to the message sender
            if ($message->user_id && $project->client_id !== $message->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project does not belong to the message sender'
                ], 400);
            }

            $message->update(['project_id' => $request->project_id]);

            // Clear caches
            $this->clearMessageCaches();

            Log::info('Message assigned to project', [
                'message_id' => $message->id,
                'project_id' => $request->project_id,
                'admin_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Message assigned to project successfully',
                'project' => [
                    'id' => $project->id,
                    'title' => $project->title,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to assign message to project', [
                'message_id' => $message->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to assign message to project'
            ], 500);
        }
    }
    public function export(Request $request)
    {
        try {
            $query = Message::excludeAdminMessages()
                ->with(['user', 'project']);

            // Apply same filters as index
            $this->applyFilters($query, $request);

            if ($request->filled('search')) {
                $this->applySearch($query, $request->search);
            }

            $messages = $query->orderBy('created_at', 'desc')->get();

            $filename = 'messages_export_' . now()->format('Y-m-d_H-i-s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function () use ($messages) {
                $file = fopen('php://output', 'w');

                // Add CSV headers
                fputcsv($file, [
                    'ID',
                    'Date',
                    'Name',
                    'Email',
                    'Company',
                    'Phone',
                    'Subject',
                    'Type',
                    'Priority',
                    'Status',
                    'Project',
                    'Is Read',
                    'Is Replied',
                    'Reply Date'
                ]);

                // Add data rows
                foreach ($messages as $message) {
                    fputcsv($file, [
                        $message->id,
                        $message->created_at->format('Y-m-d H:i:s'),
                        $message->name,
                        $message->email,
                        $message->company,
                        $message->phone,
                        $message->subject,
                        $message->type,
                        $message->priority,
                        $this->getMessageStatus($message),
                        $message->project ? $message->project->title : '',
                        $message->is_read ? 'Yes' : 'No',
                        $message->is_replied ? 'Yes' : 'No',
                        $message->replied_at ? $message->replied_at->format('Y-m-d H:i:s') : '',
                    ]);
                }

                fclose($file);
            };

            Log::info('Messages exported by admin', [
                'admin_id' => auth()->id(),
                'message_count' => $messages->count(),
            ]);

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Failed to export messages', [
                'admin_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to export messages');
        }
    }
    protected function applyFilters($query, Request $request): void
    {
        // Status filter
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'unread':
                    $query->unread();
                    break;
                case 'read':
                    $query->read();
                    break;
                case 'urgent':
                    $query->where('priority', 'urgent');
                    break;
                case 'pending':
                    $query->unreplied();
                    break;
                case 'replied':
                    $query->replied();
                    break;
            }
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Priority filter
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Project filter
        if ($request->filled('project')) {
            $query->where('project_id', $request->project);
        }

        // Client filter
        if ($request->filled('client')) {
            $query->where('user_id', $request->client);
        }

        // Date range filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
    }
    protected function applySearch($query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('subject', 'like', "%{$search}%")
                ->orWhere('message', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('company', 'like', "%{$search}%");
        });
    }
    protected function applySorting($query, Request $request): void
    {
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');

        $allowedSorts = ['created_at', 'subject', 'name', 'priority', 'is_read', 'is_replied'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->latest();
        }
    }
    protected function getFilterOptions(): array
    {
        return [
            'types' => [
                'contact_form' => 'Contact Form',
                'client_to_admin' => 'Client Message',
                'client_reply' => 'Client Reply',
                'general' => 'General Inquiry',
                'support' => 'Support Request',
                'project_inquiry' => 'Project Inquiry',
                'complaint' => 'Complaint',
                'feedback' => 'Feedback',
            ],
            'priorities' => [
                'low' => 'Low',
                'normal' => 'Normal',
                'high' => 'High',
                'urgent' => 'Urgent',
            ],
            'statuses' => [
                'unread' => 'Unread',
                'read' => 'Read',
                'pending' => 'Pending Reply',
                'replied' => 'Replied',
                'urgent' => 'Urgent',
            ],
            'clients' => User::whereHas('roles', function ($query) {
                $query->where('name', 'client');
            })->select('id', 'name', 'email')->orderBy('name')->get(),
            'projects' => Project::select('id', 'title')->orderBy('title')->get(),
        ];
    }
    protected function getClientInformation(Message $message): array
    {
        $clientInfo = [
            'name' => $message->name,
            'email' => $message->email,
            'phone' => $message->phone,
            'company' => $message->company,
            'is_registered' => $message->user_id !== null,
            'user' => $message->user,
        ];

        if ($message->user) {
            $clientInfo['registration_date'] = $message->user->created_at;
            $clientInfo['total_projects'] = $message->user->projects()->count();
            $clientInfo['active_projects'] = $message->user->projects()->where('status', 'active')->count();
        }

        return $clientInfo;
    }
    protected function getClientMessageStats(string $email): array
    {
        return [
            'total' => Message::where('email', $email)->excludeAdminMessages()->count(),
            'unread' => Message::where('email', $email)->excludeAdminMessages()->unread()->count(),
            'urgent' => Message::where('email', $email)->excludeAdminMessages()->where('priority', 'urgent')->count(),
            'this_month' => Message::where('email', $email)
                ->excludeAdminMessages()
                ->where('created_at', '>=', now()->startOfMonth())
                ->count(),
        ];
    }
    protected function canDeleteMessage(Message $message): bool
    {
        return $message->replies()->count() === 0 && !$message->parent_id;
    }
    protected function getMessageStatus(Message $message): string
    {
        if ($message->priority === 'urgent') {
            return 'Urgent';
        }

        if (!$message->is_read) {
            return 'Unread';
        }

        if (!$message->is_replied) {
            return 'Pending Reply';
        }

        return 'Replied';
    }
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
                Storage::disk('public')->makeDirectory($directory);
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
    protected function cleanupSessionTempFiles(): void
    {
        try {
            $tempDir = 'temp/message-attachments/' . session()->getId();

            if (Storage::disk('public')->exists($tempDir)) {
                $files = Storage::disk('public')->allFiles($tempDir);

                foreach ($files as $file) {
                    Storage::disk('public')->delete($file);
                }

                // Remove the directory if it's empty
                Storage::disk('public')->deleteDirectory($tempDir);

                Log::info('Cleaned up session temp files', [
                    'session_id' => session()->getId(),
                    'files_deleted' => count($files)
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to cleanup session temp files: ' . $e->getMessage());
        }
    }

    protected function moveTempFileToMessage(string $tempPath, Message $message): bool
{
    try {
        // Clean up the temp path - remove leading slashes and ensure proper format
        $tempPath = ltrim($tempPath, '/');
        
        // Security check - ensure file is in temp directory
        if (!str_starts_with($tempPath, 'temp/message-attachments/')) {
            Log::warning('Invalid temp file path detected', [
                'temp_path' => $tempPath,
                'message_id' => $message->id
            ]);
            return false;
        }

        // Check if temp file exists
        if (!Storage::disk('public')->exists($tempPath)) {
            Log::warning('Temp file not found', [
                'temp_path' => $tempPath,
                'message_id' => $message->id,
                'full_path' => storage_path('app/public/' . $tempPath),
                'storage_files' => Storage::disk('public')->files(dirname($tempPath))
            ]);
            return false;
        }

        // Get file info
        $tempFilename = basename($tempPath);
        $fileSize = Storage::disk('public')->size($tempPath);
        $mimeType = Storage::disk('public')->mimeType($tempPath);

        // Extract original name from temp filename if it follows pattern: uniqid_originalname.ext
        if (preg_match('/^[a-f0-9]+_(.+)$/', $tempFilename, $matches)) {
            $displayName = $matches[1];
        } else {
            $displayName = $tempFilename;
        }

        // Create permanent directory for message attachments
        $permanentDir = 'message-attachments/' . $message->id;
        $permanentFilename = uniqid() . '_' . Str::slug(pathinfo($displayName, PATHINFO_FILENAME)) . '.' . pathinfo($displayName, PATHINFO_EXTENSION);
        $permanentPath = $permanentDir . '/' . $permanentFilename;

        // Ensure permanent directory exists
        if (!Storage::disk('public')->exists($permanentDir)) {
            Storage::disk('public')->makeDirectory($permanentDir);
        }

        // Move file from temp to permanent location
        if (Storage::disk('public')->move($tempPath, $permanentPath)) {
            // Create attachment record using MessageAttachment model
            MessageAttachment::create([
                'message_id' => $message->id,
                'file_name' => $displayName,
                'file_path' => $permanentPath,
                'file_size' => $fileSize,
                'file_type' => $mimeType,
            ]);

            Log::info('Admin reply attachment moved successfully', [
                'from' => $tempPath,
                'to' => $permanentPath,
                'message_id' => $message->id,
                'file_name' => $displayName,
                'file_size' => $fileSize
            ]);

            return true;
        } else {
            Log::error('Failed to move admin reply temp file', [
                'from' => $tempPath,
                'to' => $permanentPath,
                'message_id' => $message->id
            ]);
            return false;
        }

    } catch (\Exception $e) {
        Log::error('Error moving admin reply temp file to message', [
            'temp_path' => $tempPath,
            'message_id' => $message->id,
            'error' => $e->getMessage(),
            'stack_trace' => $e->getTraceAsString()
        ]);
        return false;
    }
}
    protected function processAttachments(Message $message, ?string $tempFiles): int
    {
        $attachmentCount = 0;

        if (!$tempFiles) {
            return $attachmentCount;
        }

        try {
            $tempFilePaths = json_decode($tempFiles, true);

            if (!is_array($tempFilePaths)) {
                return $attachmentCount;
            }

            foreach ($tempFilePaths as $tempPath) {
                if ($this->processTempFile($message, $tempPath)) {
                    $attachmentCount++;
                    if ($attachmentCount >= 5)
                        break; // Limit to 5 attachments
                }
            }

        } catch (\Exception $e) {
            Log::error('Error processing temp files for admin message', [
                'message_id' => $message->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $attachmentCount;
    }
    protected function prepareForwardedContent(Message $originalMessage, ?string $forwardMessage): string
    {
        $content = '';

        if ($forwardMessage) {
            $content .= $forwardMessage . "\n\n";
            $content .= "--- Forwarded Message ---\n\n";
        } else {
            $content .= "--- Forwarded Message ---\n\n";
        }

        $content .= "From: {$originalMessage->name} <{$originalMessage->email}>\n";
        $content .= "Date: {$originalMessage->created_at->format('Y-m-d H:i:s')}\n";
        $content .= "Subject: {$originalMessage->subject}\n\n";
        $content .= $originalMessage->message;

        return $content;
    }
    protected function copyAttachments(Message $originalMessage, Message $forwardedMessage): void
    {
        foreach ($originalMessage->attachments as $attachment) {
            try {
                $originalPath = $attachment->file_path;
                $newPath = 'message-attachments/' . $forwardedMessage->id . '/' . $attachment->file_name;

                // Ensure directory exists
                $newDir = dirname($newPath);
                if (!Storage::disk('public')->exists($newDir)) {
                    Storage::disk('public')->makeDirectory($newDir);
                }

                // Copy file
                if (Storage::disk('public')->copy($originalPath, $newPath)) {
                    // Create new attachment record
                    $forwardedMessage->attachments()->create([
                        'file_path' => $newPath,
                        'file_name' => $attachment->file_name,
                        'file_type' => $attachment->file_type,
                        'file_size' => $attachment->file_size,
                    ]);
                }

            } catch (\Exception $e) {
                Log::error('Failed to copy attachment for forwarded message', [
                    'original_attachment_id' => $attachment->id,
                    'forwarded_message_id' => $forwardedMessage->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
    protected function getRecentActivity(): array
    {
        return Cache::remember('admin_message_recent_activity', now()->addMinutes(5), function () {
            return [
                'new_messages' => Message::excludeAdminMessages()
                    ->where('created_at', '>=', now()->subHours(24))
                    ->with(['user'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(),
                'recent_replies' => Message::where('type', 'admin_to_client')
                    ->where('created_at', '>=', now()->subHours(24))
                    ->with(['parent', 'user'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(),
            ];
        });
    }

    public function show($id)
    {
        $message = Message::with(['attachments', 'user', 'project'])->findOrFail($id);

        $rootMessage = $message->parent_id ? $message->parent : $message;
        $thread = $rootMessage->getCompleteThread();
        $canReply = auth()->user() && auth()->user()->isAdmin();
        $user = $message->user;
        $clientInfo = null;
        if ($user) {
            $clientInfo = [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? null,
                'company' => $user->company ?? null,
                'total_messages' => $user->messages()->count(),
                'member_since' => $user->created_at,
            ];
        }
        $recentActivity = collect();
        if ($user && method_exists($user, 'activities')) {
            $recentActivity = $user->activities()->latest()->limit(10)->get()->map(function ($act) {
                return [
                    'description' => $act->description,
                    'created_at' => $act->created_at,
                ];
            });
        }
        return view('admin.messages.show', compact(
            'message',
            'rootMessage',
            'thread',
            'canReply',
            'clientInfo',
            'recentActivity'
        ));
    }

    public function toggleRead(Message $message)
    {
        $message->toggleReadStatus();
        return redirect()->back()->with('success', 'Message status updated!');
    }

    public function markAsUnread(Message $message)
    {
        $message->markAsUnread();
        return redirect()->back()->with('success', 'Message marked as unread!');
    }

    public function destroy(Message $message)
    {
        try {
            DB::beginTransaction();

            $message->load('attachments');

            // Delete all attachments
            foreach ($message->attachments as $attachment) {
                $attachment->delete();
            }

            $message->delete();

            DB::commit();

            return redirect()->route('admin.messages.index')
                ->with('success', 'Message deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete message: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to delete message. Please try again.');
        }
    }

    /**
     * Mark a batch of messages as read.
     */
    public function markAsRead(Request $request)
    {
        try {
            $count = Message::excludeAdminMessages()
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);

            return redirect()->back()
                ->with('success', "{$count} messages marked as read.");

        } catch (\Exception $e) {
            Log::error('Failed to mark messages as read: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to mark messages as read. Please try again.');
        }
    }

    /**
     * Delete multiple messages.
     */
    public function destroyMultiple(Request $request)
    {
        $validated = $request->validate([
            'message_ids' => 'required|array',
            'message_ids.*' => 'integer|exists:messages,id',
        ]);

        try {
            DB::beginTransaction();

            $messages = Message::whereIn('id', $validated['message_ids'])->get();
            $count = 0;

            foreach ($messages as $message) {
                // Delete attachments first
                foreach ($message->attachments as $attachment) {
                    $attachment->delete();
                }

                $message->delete();
                $count++;
            }

            DB::commit();

            return redirect()->back()
                ->with('success', "{$count} messages deleted successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete multiple messages: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to delete messages. Please try again.');
        }
    }

    /**
     * Download an attachment file.
     */
    public function downloadAttachment(Message $message, $attachmentId)
    {
        try {
            $attachment = $message->attachments()->findOrFail($attachmentId);

            // Check if file exists
            if (!Storage::disk('public')->exists($attachment->file_path)) {
                abort(404, 'File not found.');
            }

            return Storage::disk('public')->download(
                $attachment->file_path,
                $attachment->file_name
            );

        } catch (\Exception $e) {
            Log::error('Failed to download attachment: ' . $e->getMessage());
            abort(404, 'Attachment not found.');
        }
    }

    /**
     * Handle email reply webhook (for external email providers).
     */
    public function handleEmailReply(Request $request)
    {
        try {
            // Log the incoming webhook for debugging
            Log::info('Email reply webhook received', $request->all());

            // Basic validation
            $validated = $request->validate([
                'message_id' => 'required|exists:messages,id',
                'reply_content' => 'required|string',
                'from_email' => 'required|email',
            ]);

            $message = Message::findOrFail($validated['message_id']);

            // Create reply
            $reply = Message::create([
                'parent_id' => $message->id,
                'user_id' => $message->user_id,
                'subject' => 'Re: ' . $message->subject,
                'message' => $validated['reply_content'],
                'type' => 'email_reply',
                'priority' => $message->priority,
                'project_id' => $message->project_id,
                'is_read' => false,
            ]);

            // Mark original message as replied
            $message->markAsReplied();

            return response()->json([
                'success' => true,
                'message' => 'Email reply processed successfully',
                'reply_id' => $reply->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process email reply webhook: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to process email reply'
            ], 500);
        }
    }

    /**
     * Get dashboard statistics for messages.
     */
    public function getStats()
    {
        return [
            'total_messages' => Message::excludeAdminMessages()->count(),
            'unread_messages' => Message::excludeAdminMessages()->unread()->count(),
            'unreplied_messages' => Message::excludeAdminMessages()->unreplied()->count(),
            'today_messages' => Message::excludeAdminMessages()->whereDate('created_at', today())->count(),
            'urgent_messages' => Message::excludeAdminMessages()->where('priority', 'urgent')->unread()->count(),
        ];
    }
    public function getStatisticsApi()
    {
        try {
            $statistics = $this->getStats(); // Use existing method

            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get admin message statistics: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics'
            ], 500);
        }
    }
}