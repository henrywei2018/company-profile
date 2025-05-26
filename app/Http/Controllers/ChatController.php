<?php
// app/Http/Controllers/ChatController.php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Start a new chat session
     */
    public function start(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $visitorInfo = $request->only(['name', 'email', 'phone']);
            $user = auth()->user();

            $session = $this->chatService->startSession(
                !empty(array_filter($visitorInfo)) ? $visitorInfo : null,
                $user
            );

            // Store session ID in browser session
            session(['chat_session_id' => $session->session_id]);

            return response()->json([
                'success' => true,
                'session_id' => $session->session_id,
                'messages' => $this->formatMessages($session->messages)
            ]);

        } catch (\Exception $e) {
            \Log::error('Chat session start failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to start chat session'
            ], 500);
        }
    }

    /**
     * Send a message in chat
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string|exists:chat_sessions,session_id',
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $session = $this->chatService->getSession($request->session_id);
            
            if (!$session || $session->status === 'closed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chat session not found or closed'
                ], 404);
            }

            $message = $this->chatService->sendMessage(
                $session,
                $request->message,
                'visitor',
                auth()->user()
            );

            // Get updated messages (last 10 messages)
            $messages = $this->chatService->getMessages($session, 10);

            return response()->json([
                'success' => true,
                'message_id' => $message->id,
                'messages' => $this->formatMessages($messages)
            ]);

        } catch (\Exception $e) {
            \Log::error('Chat message failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message'
            ], 500);
        }
    }

    /**
     * Get messages for a chat session
     */
    public function getMessages(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string|exists:chat_sessions,session_id',
            'last_message_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $session = $this->chatService->getSession($request->session_id);
            
            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chat session not found'
                ], 404);
            }

            $query = $session->messages()->orderBy('created_at');
            
            // Get only new messages if last_message_id is provided
            if ($request->last_message_id) {
                $query->where('id', '>', $request->last_message_id);
            }

            $messages = $query->get();

            return response()->json([
                'success' => true,
                'messages' => $this->formatMessages($messages),
                'session_status' => $session->status
            ]);

        } catch (\Exception $e) {
            \Log::error('Get chat messages failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get messages'
            ], 500);
        }
    }

    /**
     * Update visitor information
     */
    public function updateVisitorInfo(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string|exists:chat_sessions,session_id',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $session = $this->chatService->getSession($request->session_id);
            
            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chat session not found'
                ], 404);
            }

            $info = array_filter($request->only(['name', 'email', 'phone']));
            $this->chatService->updateVisitorInfo($session, $info);

            // Send confirmation message
            if (!empty($info)) {
                $this->chatService->sendBotMessage(
                    $session, 
                    "Thank you for providing your information! Our team will be able to assist you better now. 😊"
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Information updated successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Update visitor info failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update information'
            ], 500);
        }
    }

    /**
     * Close chat session
     */
    public function close(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string|exists:chat_sessions,session_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $session = $this->chatService->getSession($request->session_id);
            
            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chat session not found'
                ], 404);
            }

            $this->chatService->closeSession($session, 'Closed by visitor');

            // Remove from browser session
            session()->forget('chat_session_id');

            return response()->json([
                'success' => true,
                'message' => 'Chat session closed'
            ]);

        } catch (\Exception $e) {
            \Log::error('Close chat session failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to close chat session'
            ], 500);
        }
    }

    /**
     * Get existing session from browser session
     */
    public function getSession(Request $request): JsonResponse
    {
        $sessionId = session('chat_session_id');
        
        if (!$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'No active chat session'
            ], 404);
        }

        try {
            $session = $this->chatService->getSession($sessionId);
            
            if (!$session || $session->status === 'closed') {
                session()->forget('chat_session_id');
                return response()->json([
                    'success' => false,
                    'message' => 'Chat session expired'
                ], 404);
            }

            $messages = $this->chatService->getMessages($session);

            return response()->json([
                'success' => true,
                'session_id' => $session->session_id,
                'status' => $session->status,
                'messages' => $this->formatMessages($messages),
                'visitor_info' => $session->visitor_info
            ]);

        } catch (\Exception $e) {
            \Log::error('Get chat session failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get chat session'
            ], 500);
        }
    }

    /**
     * Format messages for API response
     */
    protected function formatMessages($messages): array
    {
        return $messages->map(function ($message) {
            return [
                'id' => $message->id,
                'sender_type' => $message->sender_type,
                'sender_name' => $message->getSenderName(),
                'message' => $message->message,
                'message_type' => $message->message_type,
                'created_at' => $message->created_at->toISOString(),
                'formatted_time' => $message->created_at->format('H:i'),
                'is_from_visitor' => $message->isFromVisitor(),
                'is_from_operator' => $message->isFromOperator(),
                'is_from_bot' => $message->isFromBot(),
            ];
        })->toArray();
    }
}

// app/Http/Controllers/Admin/ChatController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use App\Models\ChatOperator;
use App\Services\ChatService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Display chat dashboard
     */
    public function index()
    {
        $statistics = $this->chatService->getStatistics();
        
        $activeSessions = ChatSession::with(['user', 'latestMessage'])
            ->where('status', 'active')
            ->orderBy('last_activity_at', 'desc')
            ->get();

        $waitingSessions = ChatSession::with(['user', 'latestMessage'])
            ->where('status', 'waiting')
            ->orderBy('priority')
            ->orderBy('created_at')
            ->get();

        return view('admin.chat.index', compact(
            'statistics',
            'activeSessions', 
            'waitingSessions'
        ));
    }

    /**
     * Display chat session
     */
    public function show(ChatSession $chatSession)
    {
        $chatSession->load(['user', 'operator', 'messages.sender']);
        
        return view('admin.chat.show', compact('chatSession'));
    }

    /**
     * Chat settings
     */
    public function settings()
    {
        return view('admin.chat.settings');
    }

    /**
     * Get chat statistics for API
     */
    public function statistics()
    {
        return response()->json($this->chatService->getStatistics());
    }
}