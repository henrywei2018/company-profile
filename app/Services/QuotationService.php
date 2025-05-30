<?php

namespace App\Services;

use App\Models\Quotation;
use App\Models\User;
use App\Models\Service;
use App\Services\TempNotifiable;
use App\Facades\Notifications;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class QuotationService
{
    public function getFilteredQuotations(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Quotation::with(['service', 'client']);
        
        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (!empty($filters['service'])) {
            $query->where('service_id', $filters['service']);
        }
        
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhere('project_type', 'like', "%{$search}%");
            });
        }
        
        if (!empty($filters['date_range'])) {
            $this->applyDateFilter($query, $filters['date_range']);
        }
        
        if (isset($filters['client_approved'])) {
            if ($filters['client_approved'] === '1') {
                $query->where('client_approved', true);
            } elseif ($filters['client_approved'] === '0') {
                $query->where('client_approved', false);
            }
        }
        
        // Apply sorting
        $sortField = $filters['sort'] ?? 'created_at';
        $sortDirection = $filters['direction'] ?? 'desc';
        
        return $query->orderBy($sortField, $sortDirection)->paginate($perPage);
    }
    
    public function createQuotation(array $data, array $files = []): Quotation
    {
        // Check if client exists
        $client = User::where('email', $data['email'])->first();
        if ($client) {
            $data['client_id'] = $client->id;
        }

        // Set expiry date if not provided
        if (!isset($data['expires_at'])) {
            $expiryDays = settings('quotation_expiry_days', 30);
            $data['expires_at'] = now()->addDays($expiryDays);
        }

        $quotation = Quotation::create($data);
        
        // Process attachments
        if (!empty($files)) {
            $this->processAttachments($quotation, $files);
        }
        
        // Send notifications
        $this->sendQuotationNotifications('quotation.created', $quotation);
        $this->sendClientConfirmation($quotation);
        
        return $quotation;
    }
    
    public function updateQuotationStatus(Quotation $quotation, string $status, ?string $notes = null): Quotation
    {
        $oldStatus = $quotation->status;
        
        $updateData = [
            'status' => $status,
            'admin_notes' => $notes ?? $quotation->admin_notes,
        ];
        
        // Add timestamp for specific statuses
        if ($status === 'reviewed') {
            $updateData['reviewed_at'] = now();
        } elseif ($status === 'approved') {
            $updateData['approved_at'] = now();
        }
        
        $quotation->update($updateData);
        
        // Send notifications based on status change
        if ($oldStatus !== $status) {
            $this->sendStatusChangeNotifications($quotation, $oldStatus, $status);
        }
        
        return $quotation;
    }

    public function approveQuotation(Quotation $quotation, ?string $notes = null, ?array $terms = null): Quotation
    {
        $updateData = [
            'status' => 'approved',
            'approved_at' => now(),
        ];

        if ($notes) {
            $updateData['admin_notes'] = $notes;
        }

        if ($terms) {
            $updateData['terms_and_conditions'] = $terms;
        }

        $quotation->update($updateData);

        // Send approval notifications
        $this->sendQuotationNotifications('quotation.approved', $quotation);

        return $quotation;
    }

    public function rejectQuotation(Quotation $quotation, string $reason): Quotation
    {
        $quotation->update([
            'status' => 'rejected',
            'admin_notes' => $reason,
            'rejected_at' => now(),
        ]);

        // Send rejection notifications
        $this->sendQuotationNotifications('quotation.rejected', $quotation);

        return $quotation;
    }

    public function clientApproval(Quotation $quotation, bool $approved, ?string $notes = null): Quotation
    {
        $updateData = [
            'client_approved' => $approved,
            'client_approved_at' => now(),
        ];

        if ($notes) {
            $updateData['client_decline_reason'] = $notes;
        }

        $quotation->update($updateData);

        // Send notifications based on client decision
        $notificationType = $approved ? 'quotation.client_approved' : 'quotation.client_declined';
        $this->sendQuotationNotifications($notificationType, $quotation);

        return $quotation;
    }

    public function convertToProject(Quotation $quotation, array $projectData = []): \App\Models\Project
    {
        // Ensure quotation is approved
        if ($quotation->status !== 'approved' || !$quotation->client_approved) {
            throw new \Exception('Only approved quotations can be converted to projects');
        }

        // Prepare project data
        $defaultProjectData = [
            'title' => $quotation->project_type,
            'description' => $quotation->requirements,
            'client_id' => $quotation->client_id,
            'quotation_id' => $quotation->id,
            'location' => $quotation->location,
            'status' => 'planning',
            'start_date' => $quotation->start_date,
        ];

        $projectData = array_merge($defaultProjectData, $projectData);

        // Create project using ProjectService
        $projectService = app(\App\Services\ProjectService::class);
        $project = $projectService->createProject($projectData);

        // Update quotation
        $quotation->update([
            'project_created' => true,
            'project_created_at' => now(),
        ]);

        // Send conversion notifications
        $this->sendQuotationNotifications('quotation.converted', $quotation);

        return $project;
    }

    public function sendExpiryReminders(): int
    {
        $expiringQuotations = Quotation::where('status', 'approved')
            ->whereNull('client_approved')
            ->whereBetween('expires_at', [now(), now()->addDays(5)])
            ->get();

        $sent = 0;

        foreach ($expiringQuotations as $quotation) {
            $daysUntilExpiry = now()->diffInDays($quotation->expires_at, false);
            
            // Send reminders at 5, 3, and 1 day(s) before expiry
            if (in_array($daysUntilExpiry, [5, 3, 1])) {
                $lastSent = $quotation->reminder_sent_at;
                $shouldSend = !$lastSent || $lastSent->diffInDays(now()) >= 1;

                if ($shouldSend) {
                    $this->sendQuotationNotifications('quotation.expiry_reminder', $quotation);
                    $quotation->update(['reminder_sent_at' => now()]);
                    $sent++;
                }
            }
        }

        return $sent;
    }

    public function markExpiredQuotations(): int
    {
        $expiredCount = Quotation::where('status', 'approved')
            ->whereNull('client_approved')
            ->where('expires_at', '<', now())
            ->whereNull('expiry_notification_sent_at')
            ->count();

        if ($expiredCount > 0) {
            $expiredQuotations = Quotation::where('status', 'approved')
                ->whereNull('client_approved')
                ->where('expires_at', '<', now())
                ->whereNull('expiry_notification_sent_at')
                ->get();

            foreach ($expiredQuotations as $quotation) {
                $quotation->update([
                    'status' => 'expired',
                    'expiry_notification_sent_at' => now(),
                ]);

                $this->sendQuotationNotifications('quotation.expired', $quotation);
            }
        }

        return $expiredCount;
    }

    public function extendExpiry(Quotation $quotation, int $days): Quotation
    {
        $quotation->update([
            'expires_at' => $quotation->expires_at->addDays($days),
            'reminder_sent_at' => null, // Reset reminder tracking
        ]);

        // Send notification about extension
        $this->sendQuotationNotifications('quotation.extended', $quotation);

        return $quotation;
    }

    public function bulkUpdateStatus(array $quotationIds, string $status): int
    {
        $updated = 0;
        
        foreach ($quotationIds as $id) {
            $quotation = Quotation::find($id);
            if ($quotation instanceof Quotation) {
                $this->updateQuotationStatus($quotation, $status);
                $updated++;
            }
        }

        // Send bulk notification
        if ($updated > 0) {
            Notifications::send('quotation.bulk_status_updated', [
                'count' => $updated,
                'status' => $status
            ]);
        }

        return $updated;
    }

    public function deleteQuotation(Quotation $quotation): bool
    {
        // Send notification before deletion
        $this->sendQuotationNotifications('quotation.deleted', $quotation);

        // Delete attachments
        if ($quotation->attachments()->count() > 0) {
            foreach ($quotation->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
                $attachment->delete();
            }
        }
        
        return $quotation->delete();
    }
    
    public function getStatistics(): array
    {
        $totalQuotations = Quotation::count();
        
        return [
            'total' => $totalQuotations,
            'pending' => Quotation::where('status', 'pending')->count(),
            'reviewed' => Quotation::where('status', 'reviewed')->count(),
            'approved' => Quotation::where('status', 'approved')->count(),
            'rejected' => Quotation::where('status', 'rejected')->count(),
            'expired' => Quotation::where('status', 'expired')->count(),
            'client_approved' => Quotation::where('client_approved', true)->count(),
            'awaiting_client_approval' => Quotation::where('status', 'approved')
                ->whereNull('client_approved')
                ->count(),
            'this_month' => Quotation::whereMonth('created_at', Carbon::now()->month)->count(),
            'conversion_rate' => $totalQuotations > 0 ? 
                round((Quotation::where('status', 'approved')->count() / $totalQuotations) * 100, 1) : 0,
            'project_conversion_rate' => $totalQuotations > 0 ?
                round((Quotation::where('project_created', true)->count() / $totalQuotations) * 100, 1) : 0,
        ];
    }
    
    public function getMonthlyTrends(int $months = 12): array
    {
        $trends = [];
        $startDate = Carbon::now()->subMonths($months - 1)->startOfMonth();
        
        for ($i = 0; $i < $months; $i++) {
            $date = $startDate->copy()->addMonths($i);
            $count = Quotation::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
                
            $trends[] = [
                'month' => $date->format('M Y'),
                'count' => $count,
            ];
        }
        
        return $trends;
    }
    
    public function getServiceStatistics(): array
    {
        return Quotation::join('services', 'quotations.service_id', '=', 'services.id')
            ->selectRaw('services.title as service_name, COUNT(*) as total_quotations, 
                        SUM(CASE WHEN quotations.status = "approved" THEN 1 ELSE 0 END) as approved_count')
            ->groupBy('services.id', 'services.title')
            ->orderBy('total_quotations', 'desc')
            ->get()
            ->toArray();
    }

    protected function sendQuotationNotifications(string $type, Quotation $quotation): void
    {
        try {
            Notifications::send($type, $quotation);
        } catch (\Exception $e) {
            \Log::error("Failed to send quotation notification: {$type}", [
                'quotation_id' => $quotation->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function sendClientConfirmation(Quotation $quotation): void
    {
        try {
            // Create appropriate notifiable
            $clientNotifiable = $quotation->client 
                ? $quotation->client 
                : TempNotifiable::forQuotation($quotation->email, $quotation->name);

            Notifications::send('quotation.confirmation', $quotation, $clientNotifiable);
        } catch (\Exception $e) {
            \Log::error('Failed to send quotation confirmation', [
                'quotation_id' => $quotation->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function sendStatusChangeNotifications(Quotation $quotation, string $oldStatus, string $newStatus): void
    {
        // Create appropriate notifiable for client
        $clientNotifiable = $quotation->client 
            ? $quotation->client 
            : TempNotifiable::forQuotation($quotation->email, $quotation->name);

        // Send specific notification based on new status
        switch ($newStatus) {
            case 'reviewed':
                Notifications::send('quotation.reviewed', $quotation, $clientNotifiable);
                break;
            case 'approved':
                Notifications::send('quotation.approved', $quotation, $clientNotifiable);
                break;
            case 'rejected':
                Notifications::send('quotation.rejected', $quotation, $clientNotifiable);
                break;
            default:
                Notifications::send('quotation.status_updated', $quotation, $clientNotifiable);
        }

        // Always update the client notification timestamp
        $quotation->update(['client_notification_sent_at' => now()]);
    }
    
    protected function applyDateFilter($query, string $range)
    {
        $now = Carbon::now();
        
        switch ($range) {
            case 'today':
                $query->whereDate('created_at', $now->toDateString());
                break;
            case 'week':
                $query->whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', $now->month)
                      ->whereYear('created_at', $now->year);
                break;
            case 'quarter':
                $query->whereBetween('created_at', [$now->firstOfQuarter(), $now->lastOfQuarter()]);
                break;
            case 'year':
                $query->whereYear('created_at', $now->year);
                break;
        }
        
        return $query;
    }
    
    protected function processAttachments(Quotation $quotation, array $files): void
    {
        foreach ($files as $file) {
            $path = $file->store('quotation_attachments/' . $quotation->id, 'public');
            
            $quotation->attachments()->create([
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_type' => $file->getMimeType(),
            ]);
        }
    }
}