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
use App\Services\DashboardService;
use App\Services\TempNotifiable;

class MessageController extends Controller
{

    protected $dashboardService;
    public function __construct(DashboardService $dashboardService)
    {
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

        $unreadMessages = Message::excludeAdminMessages()->unread()->count();
        $pendingQuotations = \App\Models\Quotation::where('status', 'pending')->count();

        return view('admin.messages.index', compact('messages', 'statusCounts', 'unreadMessages', 'pendingQuotations'));
    }

    /**
     * Show the form for creating a new direct message to a client.
     */
    public function create(Request $request)
    {
        // Get clients for dropdown
        $clients = User::whereHas('roles', function($query) {
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
        $user = auth()->user();
        $request->validate([
            'recipient_type' => 'required|in:client,email',
            'client_id' => 'required_if:recipient_type,client|exists:users,id',
            'recipient_email' => 'required_if:recipient_type,email|email',
            'recipient_name' => 'required_if:recipient_type,email|string|max:255',
            'subject' => 'required|string|max:255|min:3',
            'message' => 'required|string|min:10|max:10000',
            'priority' => 'required|in:low,normal,high,urgent',
            'project_id' => 'nullable|exists:projects,id',
            'temp_files' => 'nullable|string', // JSON string of temp file paths
        ]);

        try {
            DB::beginTransaction();

            // Prepare message data
            $messageData = [
                'type' => 'admin_to_client',
                'name' => auth()->user()->name ?? 'Admin',
                'email' => settings('mail_from_address', config('mail.from.address', 'admin@company.com')),
                'subject' => trim($request->subject),
                'message' => trim($request->message),
                'priority' => $request->priority,
                'project_id' => $request->project_id,
                'is_read' => true, // Admin messages start as read
                'is_replied' => false,
                'read_at' => now(),
                'replied_by' => auth()->id(),
            ];

            // Set recipient details
            if ($request->recipient_type === 'client') {
                $client = User::findOrFail($request->client_id);
                $messageData['user_id'] = $client->id;
            }

            // Create message
            $message = Message::create($messageData);

            // Handle temp file attachments
            $attachmentCount = $this->processAttachments($message, $request->temp_files);

            // Clear dashboard caches
            $this->dashboardService->clearCache($user);
            if (isset($client)) {
                $this->dashboardService->clearCache($client);
            }

            DB::commit();
            
            Log::info('Admin message created', [
                'message_id' => $message->id,
                'admin_id' => auth()->id(),
                'recipient_type' => $request->recipient_type,
                'priority' => $request->priority,
                'attachment_count' => $attachmentCount,
            ]);

            $successMessage = 'Message sent successfully!';
            if ($attachmentCount > 0) {
                $successMessage .= " ({$attachmentCount} attachment" . ($attachmentCount > 1 ? 's' : '') . " included)";
            }

            return redirect()->route('admin.messages.index')
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Admin message creation failed', [
                'admin_id' => auth()->id(),
                'error_message' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'Failed to send message. Please try again.'])
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

    /**
     * Store a message from public contact form using centralized notifications
     */
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

            $callback = function() use ($messages) {
                $file = fopen('php://output', 'w');
                
                // Add CSV headers
                fputcsv($file, [
                    'ID', 'Date', 'Name', 'Email', 'Company', 'Phone', 
                    'Subject', 'Type', 'Priority', 'Status', 'Project', 
                    'Is Read', 'Is Replied', 'Reply Date'
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
            'clients' => User::whereHas('roles', function($query) {
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
                    if ($attachmentCount >= 5) break; // Limit to 5 attachments
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
    protected function processTempFile(Message $message, string $tempPath): bool
    {
        try {
            $fullPath = storage_path('app/public/' . $tempPath);
            
            if (!file_exists($fullPath)) {
                Log::warning('Temp file not found for admin message', ['path' => $tempPath]);
                return false;
            }

            // Get file info
            $originalName = basename($tempPath);
            $fileSize = filesize($fullPath);
            $mimeType = mime_content_type($fullPath);

            // Generate permanent path
            $permanentDir = 'message-attachments/' . $message->id;
            $permanentPath = $permanentDir . '/' . $originalName;

            // Ensure directory exists
            if (!Storage::disk('public')->exists($permanentDir)) {
                Storage::disk('public')->makeDirectory($permanentDir);
            }

            // Move file to permanent location
            if (Storage::disk('public')->move($tempPath, $permanentPath)) {
                // Create attachment record
                $message->attachments()->create([
                    'file_path' => $permanentPath,
                    'file_name' => $originalName,
                    'file_type' => $mimeType,
                    'file_size' => $fileSize,
                ]);

                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Failed to process temp file for admin message', [
                'temp_path' => $tempPath,
                'message_id' => $message->id,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
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

    /**
     * Display the specified message.
     */
    public function show(Message $message)
    {
        $message->load(['user', 'attachments', 'parent', 'replies', 'repliedBy']);

        if (!$message->is_read && $message->type !== 'admin_to_client') {
            $message->markAsRead();
        }

        $relatedMessages = collect();
        try {
            $relatedMessages = $message->getThreadMessages()
                ->where('id', '!=', $message->id)
                ->with(['attachments'])
                ->get();
        } catch (\Exception $e) {
            $relatedMessages = collect();
        }

        $clientMessages = Message::where('email', $message->email)
            ->where('id', '!=', $message->id)
            ->excludeAdminMessages()
            ->latest()
            ->take(5)
            ->get();

        $totalClientMessages = Message::where('email', $message->email)
            ->excludeAdminMessages()
            ->count();

        $unreadMessages = Message::excludeAdminMessages()->unread()->count();
        $pendingQuotations = \App\Models\Quotation::where('status', 'pending')->count();

        return view('admin.messages.show', compact(
            'message',
            'relatedMessages',
            'clientMessages',
            'totalClientMessages',
            'unreadMessages',
            'pendingQuotations'
        ));
    }

    /**
     * Reply to a message using centralized notifications.
     */
    public function reply(Request $request, Message $message)
    {
        $request->validate([
            'subject' => 'required|string|max:255|min:3',
            'message' => 'required|string|min:10|max:10000',
            'attachments.*' => 'nullable|file|max:2048|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif,zip,rar',
            'attachments' => 'nullable|array|max:5',
        ]);

        try {
            DB::beginTransaction();

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

            // Mark the original message as replied
            if (!$message->is_replied) {
                $message->markAsReplied(auth()->id());
            }

            // Handle attachments
            $attachmentCount = 0;
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    if ($file->isValid() && $attachmentCount < 5) {
                        try {
                            $reply->addAttachment($file);
                            $attachmentCount++;
                        } catch (\Exception $e) {
                            Log::error('Failed to add reply attachment: ' . $e->getMessage());
                        }
                    }
                }
            }

            // Send notification using centralized system
            try {
                if ($message->user) {
                    // Send to registered client
                    Notifications::send('message.reply', $reply, $message->user);
                } else {
                    // Send to custom email
                    $tempNotifiable = new TempNotifiable($message->email, $message->name);
                    Notifications::send('message.reply', $reply, $tempNotifiable);
                }

                Log::info('Message reply sent', [
                    'reply_id' => $reply->id,
                    'original_message_id' => $message->id,
                    'recipient_email' => $message->email
                ]);

            } catch (\Exception $e) {
                Log::error('Failed to send reply notification: ' . $e->getMessage());

                // Still commit but show warning
                DB::commit();

                return redirect()->route('admin.messages.show', $message)
                    ->with('warning', 'Reply saved but there was an issue sending the email notification.');
            }

            DB::commit();

            $successMessage = 'Reply sent successfully!';
            if ($attachmentCount > 0) {
                $successMessage .= " ({$attachmentCount} attachment" . ($attachmentCount > 1 ? 's' : '') . " included)";
            }

            return redirect()->route('admin.messages.show', $message)
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
     * Toggle the read status of the message.
     */
    public function toggleRead(Message $message)
    {
        $message->toggleReadStatus();
        return redirect()->back()->with('success', 'Message status updated!');
    }

    /**
     * Mark a specific message as unread.
     */
    public function markAsUnread(Message $message)
    {
        $message->markAsUnread();
        return redirect()->back()->with('success', 'Message marked as unread!');
    }

    /**
     * Remove the specified message.
     */
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