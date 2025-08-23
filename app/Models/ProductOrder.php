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
        'payment_verified_at',
        'delivery_confirmed_by_client',
        'delivery_confirmed_at',
        'client_delivery_notes',
        'delivery_disputed',
        'dispute_reason',
        'dispute_reported_at',
        'dispute_status',
        'admin_dispute_response',
        'admin_responded_at',
        'client_dispute_feedback',
        'client_responded_at'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'requested_total' => 'decimal:2',
        'needed_date' => 'date',
        'needs_negotiation' => 'boolean',
        'negotiation_requested_at' => 'datetime',
        'negotiation_responded_at' => 'datetime',
        'payment_uploaded_at' => 'datetime',
        'payment_verified_at' => 'datetime',
        'delivery_confirmed_by_client' => 'boolean',
        'delivery_confirmed_at' => 'datetime',
        'delivery_disputed' => 'boolean',
        'dispute_reported_at' => 'datetime',
        'admin_responded_at' => 'datetime',
        'client_responded_at' => 'datetime',
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

    // ================================
    // CLIENT DELIVERY CONFIRMATION
    // ================================

    /**
     * Check if order is waiting for client confirmation
     */
    public function isAwaitingClientConfirmation()
    {
        return $this->status === 'delivered' && 
               !$this->delivery_confirmed_by_client && 
               !$this->delivery_disputed;
    }

    /**
     * Check if order is completed (client confirmed delivery)
     */
    public function isCompleted()
    {
        return $this->status === 'delivered' && 
               $this->delivery_confirmed_by_client;
    }

    /**
     * Check if delivery is disputed by client
     */
    public function isDisputed()
    {
        return $this->delivery_disputed;
    }

    /**
     * Get the actual order status for display
     */
    public function getDisplayStatus()
    {
        if ($this->delivery_disputed) {
            return 'disputed';
        }
        
        if ($this->status === 'delivered' && !$this->delivery_confirmed_by_client) {
            return 'awaiting_confirmation';
        }
        
        if ($this->status === 'delivered' && $this->delivery_confirmed_by_client) {
            return 'completed';
        }
        
        return $this->status;
    }

    /**
     * Confirm delivery by client
     */
    public function confirmDelivery($notes = null)
    {
        $this->update([
            'delivery_confirmed_by_client' => true,
            'delivery_confirmed_at' => now(),
            'client_delivery_notes' => $notes,
        ]);
    }

    /**
     * Report delivery dispute
     */
    public function reportDispute($reason)
    {
        $this->update([
            'delivery_disputed' => true,
            'dispute_reason' => $reason,
            'dispute_reported_at' => now(),
        ]);
    }

    /**
     * Check if client can confirm delivery
     */
    public function canConfirmDelivery()
    {
        return $this->isAwaitingClientConfirmation();
    }

    /**
     * Check if client can dispute delivery
     */
    public function canDisputeDelivery()
    {
        return $this->isAwaitingClientConfirmation();
    }

    /**
     * Check if client can respond to admin's dispute acknowledgment
     */
    public function canRespondToDispute()
    {
        return $this->delivery_disputed && 
               in_array($this->dispute_status, ['acknowledged', 'resolved']);
    }

    /**
     * Check if admin has acknowledged or resolved dispute
     */
    public function hasAdminResponse()
    {
        return !empty($this->admin_dispute_response) && !empty($this->admin_responded_at);
    }

    /**
     * Check if dispute is waiting for client response
     */
    public function isAwaitingClientResponse()
    {
        return $this->delivery_disputed && 
               $this->dispute_status === 'acknowledged' && 
               empty($this->client_dispute_feedback);
    }

    /**
     * Check if dispute resolution is awaiting client acceptance
     */
    public function isAwaitingResolutionAcceptance()
    {
        return $this->delivery_disputed && 
               $this->dispute_status === 'resolved' && 
               $this->dispute_status !== 'accepted_by_client';
    }

    /**
     * Accept admin's dispute resolution
     */
    public function acceptDisputeResolution($clientFeedback = null)
    {
        $this->update([
            'dispute_status' => 'accepted_by_client',
            'delivery_confirmed_by_client' => true,
            'delivery_confirmed_at' => now(),
            'delivery_disputed' => false, // Clear dispute flag
            'client_dispute_feedback' => $clientFeedback,
            'client_responded_at' => now(),
        ]);
    }

    /**
     * Respond to admin's dispute acknowledgment
     */
    public function respondToAcknowledgment($clientResponse)
    {
        $this->update([
            'client_dispute_feedback' => $clientResponse,
            'client_responded_at' => now(),
        ]);
    }
}