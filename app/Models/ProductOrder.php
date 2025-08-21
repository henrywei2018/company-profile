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
        'total_amount',
        'delivery_address',
        'needed_date',
        'notes',
        'admin_notes'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'needed_date' => 'date',
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

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function canBeModified()
    {
        return in_array($this->status, ['pending', 'confirmed']);
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