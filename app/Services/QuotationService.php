<?php

namespace App\Services;

use App\Models\Quotation;
use App\Models\User;
use App\Models\Service;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\QuotationReceived;
use App\Mail\QuotationStatusUpdated;
use Carbon\Carbon;

class QuotationService
{
    /**
     * Get filtered and paginated quotations
     */
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
    
    /**
     * Apply date range filter
     */
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
    
    /**
     * Create quotation with attachments
     */
    public function createQuotation(array $data, array $files = []): Quotation
    {
        // Check if client exists
        $client = User::where('email', $data['email'])->first();
        if ($client) {
            $data['client_id'] = $client->id;
        }
        
        $quotation = Quotation::create($data);
        
        // Process attachments
        if (!empty($files)) {
            $this->processAttachments($quotation, $files);
        }
        
        // Send notification emails
        $this->sendNotifications($quotation);
        
        return $quotation;
    }
    
    /**
     * Update quotation status with notifications
     */
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
        
        // Send notification if status changed
        if ($oldStatus !== $status) {
            try {
                Mail::to($quotation->email)->send(new QuotationStatusUpdated($quotation));
            } catch (\Exception $e) {
                \Log::error('Failed to send quotation status email: ' . $e->getMessage());
            }
        }
        
        return $quotation;
    }
    
    /**
     * Get quotation statistics
     */
    public function getStatistics(): array
    {
        $totalQuotations = Quotation::count();
        
        return [
            'total' => $totalQuotations,
            'pending' => Quotation::where('status', 'pending')->count(),
            'reviewed' => Quotation::where('status', 'reviewed')->count(),
            'approved' => Quotation::where('status', 'approved')->count(),
            'rejected' => Quotation::where('status', 'rejected')->count(),
            'client_approved' => Quotation::where('client_approved', true)->count(),
            'this_month' => Quotation::whereMonth('created_at', Carbon::now()->month)->count(),
            'conversion_rate' => $totalQuotations > 0 ? 
                round((Quotation::where('status', 'approved')->count() / $totalQuotations) * 100, 1) : 0,
        ];
    }
    
    /**
     * Get monthly quotation trends
     */
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
    
    /**
     * Get service-wise quotation statistics
     */
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
    
    /**
     * Process file attachments
     */
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
    
    /**
     * Send notification emails
     */
    protected function sendNotifications(Quotation $quotation): void
    {
        try {
            // Send confirmation to client
            Mail::to($quotation->email)->send(new QuotationReceived($quotation));
            
            // Send notification to admin
            $adminEmail = config('mail.admin_email', 'admin@usahaprimalestari.com');
            Mail::to($adminEmail)->send(new QuotationReceived($quotation, true));
            
        } catch (\Exception $e) {
            \Log::error('Failed to send quotation notification emails: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete quotation with cleanup
     */
    public function deleteQuotation(Quotation $quotation): bool
    {
        // Delete attachments
        if ($quotation->attachments()->count() > 0) {
            foreach ($quotation->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
                $attachment->delete();
            }
        }
        
        return $quotation->delete();
    }
    
    /**
     * Bulk update quotations
     */
    public function bulkUpdateStatus(array $quotationIds, string $status): int
    {
        $updated = 0;
        
        foreach ($quotationIds as $id) {
            $quotation = Quotation::find($id);
            if ($quotation) {
                $this->updateQuotationStatus($quotation, $status);
                $updated++;
            }
        }
        
        return $updated;
    }    
    
}