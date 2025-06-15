<?php
// Create new file: app/Http/Controllers/Api/Client/MessageController.php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Services\ClientAccessService;
use App\Services\MessageService;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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
     * Get unread message count
     */
    public function getUnreadCount(): JsonResponse
    {
        $user = auth()->user();
        $count = $this->messageService->getUnreadCount($user);
        
        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    /**
     * Get message statistics
     */
    public function getStatistics(): JsonResponse
    {
        $user = auth()->user();
        $statistics = $this->messageService->getMessageStatistics($user);
        
        return response()->json([
            'success' => true,
            'data' => $statistics,
        ]);
    }

    /**
     * Mark all messages as read
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = auth()->user();
        
        $messageIds = $this->clientAccessService->getClientMessages($user)
            ->where('is_read', false)
            ->pluck('id')
            ->toArray();
        
        if (empty($messageIds)) {
            return response()->json([
                'success' => true,
                'count' => 0,
                'message' => 'No unread messages found',
            ]);
        }
        
        $count = $this->messageService->bulkMarkAsRead($messageIds, $user);
        
        // Clear dashboard cache
        $this->dashboardService->clearCache($user);
        
        return response()->json([
            'success' => true,
            'count' => $count,
            'message' => "{$count} messages marked as read",
        ]);
    }

    /**
     * Get activity data for dashboard
     */
    public function getActivity(Request $request): JsonResponse
    {
        $user = auth()->user();
        
        $filters = $request->validate([
            'days' => 'nullable|integer|min:1|max:365',
            'type' => 'nullable|string',
        ]);
        
        $days = $filters['days'] ?? 30;
        $since = now()->subDays($days);
        
        // Get messages using existing service
        $query = $this->clientAccessService->getClientMessages($user)
            ->where('created_at', '>=', $since);
        
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        $messages = $query->with(['project', 'parent'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Calculate activity metrics using existing fields
        $activity = [
            'period' => "{$days} days",
            'total_messages' => $messages->count(),
            'unread_messages' => $messages->where('is_read', false)->count(),
            'pending_replies' => $messages->where('is_replied', false)
                ->whereIn('type', ['general', 'support', 'project_inquiry', 'complaint'])
                ->count(),
            'urgent_messages' => $messages->where('priority', 'urgent')->count(),
            'by_type' => $messages->groupBy('type')->map->count(),
            'by_priority' => $messages->groupBy('priority')->map->count(),
            'by_project' => $messages->whereNotNull('project_id')
                ->groupBy('project.title')->map->count(),
            'daily_activity' => $this->getDailyActivity($messages),
            'response_metrics' => $this->getResponseMetrics($messages),
        ];
        
        return response()->json([
            'success' => true,
            'data' => $activity,
        ]);
    }

    /**
     * Get recent notifications
     */
    public function getNotifications(): JsonResponse
    {
        $user = auth()->user();
        $notifications = $this->clientAccessService->getRecentNotifications($user);
        
        return response()->json([
            'success' => true,
            'data' => $notifications,
        ]);
    }

    /**
     * Get message summary for dashboard widget
     */
    public function getSummary(): JsonResponse
    {
        $user = auth()->user();
        $summary = $this->clientAccessService->getMessageActivitySummary($user);
        
        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    /**
     * Check for urgent messages
     */
    public function checkUrgent(): JsonResponse
    {
        $user = auth()->user();
        $hasUrgent = $this->clientAccessService->hasUrgentMessages($user);
        
        $urgentMessages = [];
        if ($hasUrgent) {
            $urgentMessages = $this->clientAccessService->getClientMessages($user)
                ->where('priority', 'urgent')
                ->where('is_replied', false)
                ->select('id', 'subject', 'created_at')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function($message) {
                    return [
                        'id' => $message->id,
                        'subject' => $message->subject,
                        'url' => route('client.messages.show', $message),
                        'created_at' => $message->created_at->diffForHumans(),
                    ];
                })->toArray();
        }
        
        return response()->json([
            'success' => true,
            'has_urgent' => $hasUrgent,
            'urgent_messages' => $urgentMessages,
        ]);
    }

    /**
     * Helper: Get daily activity breakdown
     */
    protected function getDailyActivity($messages): array
    {
        return $messages->groupBy(function($message) {
            return $message->created_at->format('Y-m-d');
        })->map(function($dayMessages) {
            return [
                'total' => $dayMessages->count(),
                'unread' => $dayMessages->where('is_read', false)->count(),
                'urgent' => $dayMessages->where('priority', 'urgent')->count(),
                'types' => $dayMessages->groupBy('type')->map->count(),
            ];
        })->sortKeys()->toArray();
    }

    /**
     * Helper: Get response metrics
     */
    protected function getResponseMetrics($messages): array
    {
        $repliedMessages = $messages->where('is_replied', true)
            ->whereNotNull('replied_at');
        
        if ($repliedMessages->isEmpty()) {
            return [
                'average_response_time' => 0,
                'fastest_response' => 0,
                'slowest_response' => 0,
                'total_replied' => 0,
            ];
        }
        
        $responseTimes = $repliedMessages->map(function($message) {
            return $message->created_at->diffInHours($message->replied_at);
        })->filter();
        
        return [
            'average_response_time' => round($responseTimes->avg(), 1),
            'fastest_response' => $responseTimes->min(),
            'slowest_response' => $responseTimes->max(),
            'total_replied' => $repliedMessages->count(),
        ];
    }
}