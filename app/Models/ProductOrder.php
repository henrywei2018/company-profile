<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasActiveTrait;
use App\Traits\HasSlugTrait;
use App\Traits\HasSortOrderTrait;

class ProductOrder extends Model
{
    protected $fillable = [
        'order_number', 'client_id', 'client_name', 'client_email', 'client_phone',
        'status', 'total_amount', 'delivery_address', 'needed_date', 
        'notes', 'admin_notes', 'quotation_id', 'needs_quotation'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'needed_date' => 'date',
        'needs_quotation' => 'boolean',
    ];

    // Relationships
    public function client() { return $this->belongsTo(User::class, 'client_id'); }
    public function items() { return $this->hasMany(ProductOrderItem::class); }
    public function quotation() { return $this->belongsTo(Quotation::class); }

    // Simple helper methods
    public function generateOrderNumber()
    {
        return 'PO' . date('Ym') . str_pad($this->id ?? 1, 4, '0', STR_PAD_LEFT);
    }

    public function calculateTotal()
    {
        $this->total_amount = $this->items()->sum(\DB::raw('quantity * price'));
        $this->save();
    }

    public function shouldCreateQuotation()
    {
        return $this->needs_quotation || 
               $this->items()->whereHas('product', function($q) {
                   $q->where('purchase_type', 'quote');
               })->exists();
    }
}