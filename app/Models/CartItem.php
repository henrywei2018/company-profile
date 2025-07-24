<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'user_id', 'session_id', 'product_id', 'quantity', 'specifications'
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function product() { return $this->belongsTo(Product::class); }

    // Get cart for user or session
    public static function getCart($userId = null, $sessionId = null)
    {
        $query = static::with('product');
        
        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($sessionId) {
            $query->where('session_id', $sessionId);
        }
        
        return $query->get();
    }

    // Clear old carts (run in scheduler)
    public static function clearOldCarts()
    {
        static::where('created_at', '<', now()->subDays(7))->delete();
    }
}