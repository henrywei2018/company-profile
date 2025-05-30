<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\User;

class AuditService
{
    public function logUserAction(string $action, $model = null, array $data = [], ?User $user = null): void
    {
        $user = $user ?? auth()->user();
        
        $logData = [
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'action' => $action,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
        ];

        if ($model) {
            $logData['model_type'] = get_class($model);
            $logData['model_id'] = $model->id ?? null;
        }

        if (!empty($data)) {
            $logData['data'] = $data;
        }

        Log::channel('audit')->info($action, $logData);
    }

    public function logLogin(User $user, Request $request): void
    {
        $this->logUserAction('user.login', $user, [
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ], $user);
    }

    public function logLogout(User $user): void
    {
        $this->logUserAction('user.logout', $user, [], $user);
    }

    public function logPasswordChange(User $user): void
    {
        $this->logUserAction('user.password_changed', $user, [], $user);
    }

    public function logPermissionChange(User $user, array $oldRoles, array $newRoles): void
    {
        $this->logUserAction('user.roles_changed', $user, [
            'old_roles' => $oldRoles,
            'new_roles' => $newRoles,
        ], $user);
    }

    public function logModelCreated($model, ?User $user = null): void
    {
        $this->logUserAction('model.created', $model, [
            'model_data' => $this->getModelAuditData($model),
        ], $user);
    }

    public function logModelUpdated($model, array $changes = [], ?User $user = null): void
    {
        $this->logUserAction('model.updated', $model, [
            'changes' => $changes,
        ], $user);
    }

    public function logModelDeleted($model, ?User $user = null): void
    {
        $this->logUserAction('model.deleted', $model, [
            'model_data' => $this->getModelAuditData($model),
        ], $user);
    }

    public function logSystemEvent(string $event, array $data = []): void
    {
        Log::channel('system')->info($event, array_merge([
            'timestamp' => now()->toISOString(),
            'event' => $event,
        ], $data));
    }

    public function logSecurityEvent(string $event, array $data = []): void
    {
        Log::channel('security')->warning($event, array_merge([
            'timestamp' => now()->toISOString(),
            'event' => $event,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ], $data));
    }

    protected function getModelAuditData($model): array
    {
        $sensitiveFields = ['password', 'remember_token', 'api_token'];
        $data = $model->toArray();
        
        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '[REDACTED]';
            }
        }
        
        return $data;
    }

    public function getAuditLogs(array $filters = [], int $perPage = 50): array
    {
        // This would read from audit log files or database
        // For now, return mock data structure
        return [
            'logs' => [],
            'total' => 0,
            'filters' => $filters,
        ];
    }
}