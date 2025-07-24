<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'specifications'
    ];

    // ================================
    // RELATIONSHIPS
    // ================================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // ================================
    // STATIC METHODS
    // ================================

    public static function getCartForUser($userId)
    {
        return static::with(['product' => function($query) {
            $query->where('status', 'published')
                  ->where('is_active', true);
        }])
        ->where('user_id', $userId)
        ->get();
    }

    public static function getCartCount($userId)
    {
        return static::where('user_id', $userId)->sum('quantity');
    }

    public static function getCartTotal($userId)
    {
        return static::where('user_id', $userId)
            ->with('product')
            ->get()
            ->sum(function($item) {
                return $item->quantity * $item->product->current_price; // Use attribute
            });
    }


    public function getSubtotal()
    {
        return $this->quantity * $this->product->current_price; // Use attribute
    }

    public function getFormattedSubtotal()
    {
        return number_format($this->getSubtotal(), 0, ',', '.');
    }
}