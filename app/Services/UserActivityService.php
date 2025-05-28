<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Support\Facades\Log;

class UserActivityService
{
    /**
     * Log user activity.
     */
    public function logActivity(User $user, string $action, array $context = []): void
    {
        try {
            UserActivity::create([
                'user_id' => $user->id,
                'action' => $action,
                'ip_address' => $context['ip'] ?? request()->ip(),
                'user_agent' => $context['user_agent'] ?? request()->userAgent(),
                'context' => $context,
                'performed_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log user activity', [
                'user_id' => $user->id,
                'action' => $action,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Log failed login attempt.
     */
    public function logFailedLogin(array $data): void
    {
        try {
            UserActivity::create([
                'user_id' => null,
                'action' => 'failed_login',
                'ip_address' => $data['ip'],
                'user_agent' => $data['user_agent'] ?? null,
                'context' => [
                    'email' => $data['email'],
                    'guard' => $data['guard'] ?? 'web',
                ],
                'performed_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log failed login attempt', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
        }
    }

    /**
     * Get user's recent activities.
     */
    public function getRecentActivities(User $user, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return UserActivity::where('user_id', $user->id)
            ->orderBy('performed_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Clean up old activity records.
     */
    public function cleanupOldActivities(int $daysToKeep = 90): int
    {
        return UserActivity::where('performed_at', '<', now()->subDays($daysToKeep))
            ->delete();
    }
}