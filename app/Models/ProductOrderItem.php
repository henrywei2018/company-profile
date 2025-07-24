<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOrderItem extends Model
{
    protected $fillable = [
        'product_order_id', 'product_id', 'quantity', 'price', 'total',
        'specifications', 'notes'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function order() { return $this->belongsTo(ProductOrder::class, 'product_order_id'); }
    public function product() { return $this->belongsTo(Product::class); }

    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($item) {
            $item->total = $item->quantity * $item->price;
        });

        static::saved(function ($item) {
            $item->order->calculateTotal();
        });
    }
}