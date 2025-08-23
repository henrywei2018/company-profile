<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_order_id',
        'product_id',
        'quantity',
        'price',
        'total',
        'specifications',
        'notes',
        'requested_unit_price',
        'requested_total_price',
        'price_justification'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'total' => 'decimal:2',
        'requested_unit_price' => 'decimal:2',
        'requested_total_price' => 'decimal:2',
    ];

    // ================================
    // RELATIONSHIPS
    // ================================

    public function order()
    {
        return $this->belongsTo(ProductOrder::class, 'product_order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // ================================
    // MODEL EVENTS
    // ================================

    protected static function boot()
    {
        parent::boot();
        
        // Auto-calculate total when saving
        static::saving(function ($item) {
            $item->total = $item->quantity * $item->price;
        });

        // Recalculate order total when item is saved
        static::saved(function ($item) {
            $item->order->calculateTotal();
        });

        // Recalculate order total when item is deleted
        static::deleted(function ($item) {
            if ($item->order) {
                $item->order->calculateTotal();
            }
        });
    }
    public function getFormattedPrice()
    {
        return number_format((float)$this->price, 0, ',', '.');
    }

    public function getFormattedTotal()
    {
        return number_format((float)$this->total, 0, ',', '.');
    }
}