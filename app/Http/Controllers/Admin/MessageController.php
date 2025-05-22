<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\User;
use App\Notifications\MessageReplyNotification;
use App\Notifications\DirectMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    /**
     * Display a listing of the messages with filtering.
     */
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
        // Get all verified users with 'client' role using Spatie
        $clients = User::whereHas('roles', function ($query) {
                $query->where('name', 'client');
            })
            ->whereNotNull('email_verified_at')
            ->where('is_active', true) // Only active clients
            ->orderBy('name')
            ->get();

        // If user_id is provided in query, pre-select that client
        $selectedClient = null;
        if ($request->filled('user_id')) {
            $selectedClient = User::find($request->user_id);
            // Verify the user is actually a client
            if ($selectedClient && !$selectedClient->hasRole('client')) {
                $selectedClient = null;
            }
        }

        // If email is provided, try to find existing client
        $selectedEmail = $request->get('email');

        $unreadMessages = Message::excludeAdminMessages()->unread()->count();
        $pendingQuotations = \App\Models\Quotation::where('status', 'pending')->count();

        return view('admin.messages.create', compact('clients', 'selectedClient', 'selectedEmail', 'unreadMessages', 'pendingQuotations'));
    }

    /**
     * Store a newly created direct message.
     */
    public function store(Request $request)
    {
        // Comprehensive validation with custom rules
        $validated = $request->validate([
            'recipient_type' => 'required|in:existing_client,custom_email',
            'user_id' => [
                'required_if:recipient_type,existing_client',
                'nullable',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->recipient_type === 'existing_client' && $value) {
                        $user = User::find($value);
                        if (!$user) {
                            $fail('The selected user does not exist.');
                            return;
                        }
                        if (!$user->hasRole('client')) {
                            $fail('The selected user must be a registered client.');
                            return;
                        }
                        if (!$user->email_verified_at) {
                            $fail('The selected client must have a verified email address.');
                            return;
                        }
                        if (!$user->is_active) {
                            $fail('The selected client account is not active.');
                            return;
                        }
                    }
                },
            ],
            'custom_email' => [
                'required_if:recipient_type,custom_email',
                'nullable',
                'email:rfc,dns',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->recipient_type === 'custom_email' && $value) {
                        // Check if email already exists as a registered client
                        $existingUser = User::where('email', strtolower(trim($value)))->first();
                        if ($existingUser && $existingUser->hasRole('client')) {
                            $fail('This email belongs to a registered client. Please select them from the client list instead.');
                        }
                    }
                },
            ],
            'custom_name' => 'required_if:recipient_type,custom_email|nullable|string|max:255|min:2',
            'subject' => 'required|string|max:255|min:3',
            'message' => 'required|string|min:10|max:10000',
            'attachments.*' => 'nullable|file|max:2048|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif,zip,rar',
            'attachments' => 'nullable|array|max:5',
        ], [
            'user_id.required_if' => 'Please select a client when sending to an existing client.',
            'custom_email.required_if' => 'Email address is required when sending to a custom email.',
            'custom_email.email' => 'Please enter a valid email address.',
            'custom_email.dns' => 'The email domain does not exist.',
            'custom_name.required_if' => 'Recipient name is required when sending to a custom email.',
            'custom_name.min' => 'Recipient name must be at least 2 characters.',
            'subject.min' => 'Subject must be at least 3 characters.',
            'subject.max' => 'Subject cannot exceed 255 characters.',
            'message.min' => 'Message must be at least 10 characters.',
            'message.max' => 'Message cannot exceed 10,000 characters.',
            'attachments.max' => 'You can attach a maximum of 5 files.',
            'attachments.*.max' => 'Each file must be smaller than 2MB.',
            'attachments.*.mimes' => 'Only PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, GIF, ZIP, and RAR files are allowed.',
        ]);

        try {
            DB::beginTransaction();

            // Determine recipient details
            if ($validated['recipient_type'] === 'existing_client') {
                $client = User::findOrFail($validated['user_id']);
                
                // Final verification of client role
                if (!$client->hasRole('client')) {
                    throw new \Exception('Selected user is not a client.');
                }
                
                $recipientName = $client->name;
                $recipientEmail = $client->email;
                $userId = $client->id;
            } else {
                $recipientName = trim($validated['custom_name']);
                $recipientEmail = strtolower(trim($validated['custom_email']));
                $userId = null;
            }

            // Create the direct message
            $message = Message::create([
                'type' => 'admin_to_client',
                'name' => auth()->user()->name ?? 'Admin',
                'email' => config('mail.from.address', 'admin@company.com'),
                'subject' => trim($validated['subject']),
                'message' => trim($validated['message']),
                'user_id' => $userId,
                'is_read' => true, // Admin messages are read by default
                'is_replied' => false,
                'read_at' => now(),
                'replied_by' => auth()->id(),
            ]);

            // Handle file attachments
            $attachmentCount = 0;
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    if ($file->isValid() && $attachmentCount < 5) {
                        try {
                            $message->addAttachment($file);
                            $attachmentCount++;
                        } catch (\Exception $e) {
                            Log::error('Failed to add attachment: ' . $e->getMessage());
                            // Continue with other attachments
                        }
                    }
                }
            }

            // Send email notification
            try {
                if ($userId) {
                    $client->notify(new DirectMessageNotification($message));
                    $logMessage = "Direct message sent to registered client: {$recipientName} ({$recipientEmail})";
                } else {
                    Notification::route('mail', $recipientEmail)
                        ->notify(new DirectMessageNotification($message));
                    $logMessage = "Direct message sent to custom email: {$recipientName} ({$recipientEmail})";
                }
                
                Log::info($logMessage, [
                    'message_id' => $message->id,
                    'sent_by' => auth()->id(),
                    'attachments' => $attachmentCount
                ]);
                
            } catch (\Exception $e) {
                Log::error('Failed to send direct message notification: ' . $e->getMessage(), [
                    'message_id' => $message->id,
                    'recipient_email' => $recipientEmail
                ]);
                
                // Still commit the transaction but show warning
                DB::commit();
                
                return redirect()->route('admin.messages.index')
                    ->with('warning', "Message saved successfully but there was an issue sending the email notification to {$recipientName}. Please check your email configuration.");
            }

            DB::commit();

            // Success message
            $successMessage = "Direct message sent successfully to {$recipientName}!";
            if ($attachmentCount > 0) {
                $successMessage .= " ({$attachmentCount} attachment" . ($attachmentCount > 1 ? 's' : '') . " included)";
            }

            return redirect()->route('admin.messages.index')
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create direct message: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to send message. Please try again.'])
                ->withInput();
        }
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
     * Reply to a message.
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
                'email' => config('mail.from.address', 'admin@company.com'),
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

            // Send notification
            try {
                if ($message->user) {
                    $message->user->notify(new MessageReplyNotification($reply));
                } else {
                    Notification::route('mail', $message->email)
                        ->notify(new MessageReplyNotification($reply));
                }
            } catch (\Exception $e) {
                Log::error('Failed to send reply notification: ' . $e->getMessage());
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
                ->with('error', 'Failed to update messages. Please try again.');
        }
    }

    /**
     * Delete multiple messages.
     */
    public function destroyMultiple(Request $request)
    {
        $request->validate(['ids' => 'required|string']);

        $ids = array_filter(explode(',', $request->ids));
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'No messages selected for deletion.');
        }

        try {
            DB::beginTransaction();

            $messages = Message::whereIn('id', $ids)
                ->excludeAdminMessages()
                ->with('attachments')
                ->get();
            
            $count = 0;
            foreach ($messages as $message) {
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
        $attachment = MessageAttachment::findOrFail($attachmentId);
        
        if ($attachment->message_id !== $message->id) {
            abort(403, 'Unauthorized access to attachment');
        }
        
        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'Attachment file not found');
        }
        
        return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
    }

    /**
     * Handle email reply webhook (for external email replies).
     */
    public function handleEmailReply(Request $request)
    {
        // This would handle incoming emails from external providers
        // Implementation depends on your email service provider
        return response()->json(['success' => true]);
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
            'urgent_messages' => Message::excludeAdminMessages()->unread()->unreplied()->count(),
        ];
    }
}