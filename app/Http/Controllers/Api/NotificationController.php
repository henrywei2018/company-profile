<?php

// File: app/Http/Controllers/Api/NotificationController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use App\Facades\Notifications;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get notification statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = $this->notificationService->getStatistics();
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get available notification types
     */
    public function types(): JsonResponse
    {
        $types = $this->notificationService->getAvailableTypes();
        
        return response()->json([
            'success' => true,
            'data' => $types
        ]);
    }

    /**
     * Test notification system
     */
    public function test(Request $request): JsonResponse
    {
        $user = $request->user();
        $results = $this->notificationService->test($user);
        
        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }

    /**
     * Send manual notification
     */
    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|string',
            'recipients' => 'nullable|array',
            'recipients.*' => 'integer|exists:users,id',
            'data' => 'nullable|array',
        ]);

        $recipients = null;
        if ($request->has('recipients')) {
            $recipients = \App\Models\User::whereIn('id', $request->recipients)->get();
        }

        $success = $this->notificationService->send(
            $request->type,
            $request->data,
            $recipients
        );

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Notification sent successfully' : 'Failed to send notification'
        ]);
    }

    /**
     * Clear notification cache
     */
    public function clearCache(): JsonResponse
    {
        $this->notificationService->clearCache();
        
        return response()->json([
            'success' => true,
            'message' => 'Notification cache cleared'
        ]);
    }
}