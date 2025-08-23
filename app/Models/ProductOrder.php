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
            'pending' => 'Pending Review',
            'confirmed' => 'Confirmed',
            'processing' => 'Being Processed',
            'ready' => 'Ready for Pickup',
            'delivered' => 'Delivered',
            'completed' => 'Completed',
            default => ucfirst($this->status)
        };
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