<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'client_id',
        'client_name',
        'client_email',
        'client_phone',
        'status',
        'payment_status',
        'payment_method',
        'payment_proof',
        'payment_notes',
        'total_amount',
        'delivery_address',
        'needed_date',
        'notes',
        'admin_notes',
        'needs_negotiation',
        'negotiation_message',
        'requested_total',
        'negotiation_status',
        'negotiation_requested_at',
        'negotiation_responded_at',
        'payment_uploaded_at',
        'payment_verified_at'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'requested_total' => 'decimal:2',
        'needed_date' => 'date',
        'needs_negotiation' => 'boolean',
        'negotiation_requested_at' => 'datetime',
        'negotiation_responded_at' => 'datetime',
        'payment_uploaded_at' => 'datetime',
        'payment_verified_at' => 'datetime'
    ];

    // ================================
    // RELATIONSHIPS
    // ================================

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function items()
    {
        return $this->hasMany(ProductOrderItem::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'order_id');
    }


    // ================================
    // HELPER METHODS
    // ================================

    public function generateOrderNumber()
    {
        $prefix = 'PO';
        $date = date('Ym');
        $sequence = static::whereRaw("order_number LIKE '{$prefix}{$date}%'")->count() + 1;
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function calculateTotal()
    {
        $this->total_amount = $this->items()->sum(DB::raw('quantity * price'));
        $this->save();
        return $this->total_amount;
    }


    public function getNotifiableEntity()
    {
        return $this->client; // Always authenticated user in client dashboard
    }

    // ================================
    // STATUS HELPERS
    // ================================

    public function canBeModified()
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function canRequestNegotiation()
    {
        return $this->status === 'pending' && (!$this->needs_negotiation || $this->negotiation_status === 'rejected');
    }

    public function canCounterNegotiate()
    {
        return $this->status === 'pending' && $this->needs_negotiation && $this->negotiation_status === 'in_progress';
    }

    public function canAcceptNegotiation()
    {
        return $this->status === 'pending' && $this->needs_negotiation && $this->negotiation_status === 'in_progress';
    }

    public function canNegotiate()
    {
        // Allow negotiation if order is pending and either:
        // 1. No negotiation has started yet, OR
        // 2. Previous negotiation was rejected (can start fresh), OR  
        // 3. Admin made a counter-offer (status is in_progress)
        // NOTE: Don't allow negotiation if already completed
        return $this->status === 'pending' && (
            !$this->needs_negotiation || 
            in_array($this->negotiation_status, ['rejected', 'in_progress'])
        );
    }

    public function hasActiveNegotiation()
    {
        return $this->needs_negotiation && in_array($this->negotiation_status, ['pending', 'in_progress']);
    }

    public function isNegotiationCompleted()
    {
        return $this->needs_negotiation && in_array($this->negotiation_status, ['accepted', 'completed']);
    }

    public function canMakePayment()
    {
        return $this->status === 'pending' && 
               $this->payment_status === 'pending' && 
               (!$this->needs_negotiation || $this->negotiation_status === 'completed');
    }

    public function canUploadPaymentProof()
    {
        return $this->canMakePayment();
    }

    public function hasPaymentProof()
    {
        return !empty($this->payment_proof);
    }

    public function isPaymentVerified()
    {
        return $this->payment_status === 'verified';
    }

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'pending' => 'badge-warning',
            'confirmed' => 'badge-info',
            'processing' => 'badge-primary',
            'ready' => 'badge-success',
            'delivered' => 'badge-success',
            'completed' => 'badge-secondary',
            default => 'badge-light'
        };
    }

    public function getStatusLabel()
    {
        return match($this->status) {
            'pending' => 'Menunggu Review',
            'confirmed' => 'Dikonfirmasi',
            'processing' => 'Sedang Diproses',
            'ready' => 'Siap Diambil',
            'delivered' => 'Terkirim',
            'complete' => 'Selesai',
            default => ucfirst($this->status)
        };
    }

    public function getDisplayStatus()
    {
        return $this->getStatusLabel();
    }

    public function getPaymentStatusLabel()
    {
        return match($this->payment_status) {
            'pending' => 'Pending Payment',
            'proof_uploaded' => 'Payment Proof Uploaded',
            'verified' => 'Payment Verified',
            'rejected' => 'Payment Rejected',
            default => ucfirst(str_replace('_', ' ', $this->payment_status ?? 'pending'))
        };
    }

    public function getPaymentStatusBadgeClass()
    {
        return match($this->payment_status) {
            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'proof_uploaded' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'verified' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
        };
    }

    public function getNegotiationStatusLabel()
    {
        return match($this->negotiation_status) {
            'pending' => 'Pending Review',
            'in_progress' => 'In Progress',
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
            'completed' => 'Completed',
            default => ucfirst($this->negotiation_status ?? 'none')
        };
    }

    public function getNegotiationStatusBadgeClass()
    {
        return match($this->negotiation_status) {
            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'accepted' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            'completed' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
        };
    }

    public function getFormattedTotal()
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function getFormattedRequestedTotal()
    {
        return $this->requested_total ? 'Rp ' . number_format($this->requested_total, 0, ',', '.') : null;
    }

    public function hasActivePayment()
    {
        return in_array($this->payment_status, ['proof_uploaded', 'verified']);
    }

    public function canBeDeleted()
    {
        return $this->status === 'pending';
    }

    public function canConfirmDelivery()
    {
        return $this->status === 'delivered';
    }

    public function canBeModifiedByAdmin()
    {
        return in_array($this->status, ['pending', 'confirmed', 'processing']);
    }

    // ================================
    // SCOPES
    // ================================

    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }


    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

}