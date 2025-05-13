<?php

namespace App\Services;

use App\Models\Quotation;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class QuotationService
{
    /**
     * Get paginated quotations with filters
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedQuotations(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Quotation::with(['service', 'client']);
        
        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (isset($filters['service'])) {
            $query->where('service_id', $filters['service']);
        }
        
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%");
            });
        }
        
        // Return latest quotations
        return $query->latest()->paginate($perPage);
    }
    
    /**
     * Get client quotations
     *
     * @param User $client
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getClientQuotations(User $client, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Quotation::where('client_id', $client->id);
        
        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (isset($filters['service'])) {
            $query->where('service_id', $filters['service']);
        }
        
        // Return latest quotations
        return $query->latest()->paginate($perPage);
    }
    
    /**
     * Create a new quotation
     *
     * @param array $data
     * @param array $files
     * @return Quotation
     */
    public function createQuotation(array $data, array $files = []): Quotation
    {
        // Create quotation
        $quotation = Quotation::create($data);
        
        // Process attachments if any
        if (!empty($files)) {
            $this->processAttachments($quotation, $files);
        }
        
        return $quotation;
    }
    
    /**
     * Update quotation status
     *
     * @param Quotation $quotation
     * @param string $status
     * @param string|null $notes
     * @return Quotation
     */
    public function updateStatus(Quotation $quotation, string $status, ?string $notes = null): Quotation
    {
        $quotation->update([
            'status' => $status,
            'admin_notes' => $notes ?? $quotation->admin_notes,
        ]);
        
        return $quotation;
    }
    
    /**
     * Client approval of quotation
     *
     * @param Quotation $quotation
     * @param bool $approved
     * @param string|null $declineReason
     * @return Quotation
     */
    public function clientApproval(Quotation $quotation, bool $approved, ?string $declineReason = null): Quotation
    {
        $quotation->update([
            'client_approved' => $approved,
            'client_decline_reason' => $approved ? null : $declineReason,
            'client_approved_at' => now(),
        ]);
        
        return $quotation;
    }
    
    /**
     * Delete quotation
     *
     * @param Quotation $quotation
     * @return bool
     */
    public function deleteQuotation(Quotation $quotation): bool
    {
        // Delete attachments if any
        if ($quotation->attachments()->count() > 0) {
            foreach ($quotation->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
                $attachment->delete();
            }
        }
        
        return $quotation->delete();
    }
    
    /**
     * Process attachments
     *
     * @param Quotation $quotation
     * @param array $files
     * @return void
     */
    private function processAttachments(Quotation $quotation, array $files): void
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