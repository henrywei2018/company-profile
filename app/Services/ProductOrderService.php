<?php

namespace App\Services;

use App\Models\ProductOrder;
use App\Models\ProductOrderItem;
use App\Models\CartItem;
use App\Models\Product;
use App\Facades\Notifications;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class ProductOrderService
{
    // ================================
    // ORDER CREATION & NEGOTIATION
    // ================================

    public function createOrderFromCart(array $deliveryInfo)
    {
        return DB::transaction(function () use ($deliveryInfo) {
            $cartItems = $this->getCart();
            
            if ($cartItems->isEmpty()) {
                throw new \Exception('Cart is empty.');
            }

            // Validate all products are still available
            foreach ($cartItems as $cartItem) {
                if (!$cartItem->product || !$cartItem->product->is_active) {
                    throw new \Exception("Product '{$cartItem->product->name}' is no longer available.");
                }
            }

            // Get client information
            $user = Auth::user();
            
            // Create the order
            $order = ProductOrder::create([
                'order_number' => $this->generateOrderNumber(),
                'client_id' => $user->id,
                'client_name' => $user->name,
                'client_email' => $user->email,
                'client_phone' => $user->phone ?? null,
                'delivery_address' => $deliveryInfo['address'],
                'needed_date' => $deliveryInfo['needed_date'] ?? null,
                'notes' => $deliveryInfo['notes'] ?? null,
                'status' => 'pending',
            ]);

            $totalAmount = 0;

            // Create order items from cart
            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;
                $unitPrice = $product->current_price ?? 0;
                
                // Skip items with no price
                if ($unitPrice <= 0) {
                    continue;
                }

                ProductOrderItem::create([
                    'product_order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $cartItem->quantity,
                    'price' => $unitPrice,
                    'total' => $unitPrice * $cartItem->quantity,
                    'specifications' => $cartItem->specifications,
                ]);

                $totalAmount += $unitPrice * $cartItem->quantity;
            }

            // Update order totals
            $order->update([
                'total_amount' => $totalAmount,
            ]);

            // Clear cart after successful order creation
            $this->clearCart();

            return $order;
        });
    }




    // ================================
    // CART MANAGEMENT (UPDATED)
    // ================================

    public function addToCart($productId, $quantity = 1, $specifications = null)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('client')) {
            throw new \Exception('Only authenticated clients can add to cart.');
        }

        $product = Product::findOrFail($productId);
        
        if (!$product->canAddToCart()) {
            throw new \Exception('This product cannot be added to cart.');
        }

        // Check minimum quantity
        if ($quantity < $product->min_quantity) {
            throw new \Exception("Minimum order quantity for this product is {$product->min_quantity}.");
        }

        // Check stock if managed
        if ($product->manage_stock && $quantity > $product->stock_quantity) {
            throw new \Exception("Only {$product->stock_quantity} items available in stock.");
        }

        $cartItem = CartItem::updateOrCreate([
            'user_id' => $user->id,
            'product_id' => $productId,
        ], [
            'quantity' => $quantity,
            'specifications' => $specifications,
        ]);

        return $cartItem;
    }

    public function updateCartQuantity($productId, $quantity)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('client')) {
            throw new \Exception('Only authenticated clients can update cart.');
        }

        $cartItem = CartItem::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->firstOrFail();

        $product = $cartItem->product;
        
        if ($quantity < $product->min_quantity) {
            throw new \Exception("Minimum order quantity for this product is {$product->min_quantity}.");
        }

        // Check stock if managed
        if ($product->manage_stock && $quantity > $product->stock_quantity) {
            throw new \Exception("Only {$product->stock_quantity} items available in stock.");
        }

        $cartItem->update(['quantity' => $quantity]);
        
        return $cartItem;
    }

    public function getCart(): Collection
    {
        return CartItem::with(['product' => function($query) {
            $query->where('status', 'published')
                  ->where('is_active', true);
        }, 'product.images'])
        ->where('user_id', Auth::id())
        ->get();
    }

    public function removeFromCart($productId): bool
    {
        return CartItem::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->delete() > 0;
    }

    public function clearCart(): int
    {
        return CartItem::where('user_id', Auth::id())->delete();
    }
    public function getCartCount(): int
    {
        return CartItem::where('user_id', Auth::id())->sum('quantity');
    }
    public function getCartTotal(): float
    {
        return $this->getCart()->sum(function($item) {
            return $item->quantity * ($item->product->current_price ?? 0);
        });
    }

    public function getCartSummary()
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('client')) {
            return [
                'count' => 0,
                'total' => 0,
                'items' => collect()
            ];
        }

        $items = CartItem::getCartForUser($user->id);
        
        return [
            'count' => $items->sum('quantity'),
            'total' => $items->sum(function($item) {
                return $item->quantity * $item->product->current_price; // Use attribute
            }),
            'items' => $items
        ];
    }

    // ================================
    // VALIDATION HELPERS
    // ================================

    public function validateCartItems()
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('client')) {
            return ['valid' => false, 'errors' => ['User not authenticated']];
        }

        $cartItems = CartItem::getCartForUser($user->id);
        $errors = [];

        foreach ($cartItems as $cartItem) {
            $product = $cartItem->product;
            
            if (!$product || !$product->is_active) {
                $errors[] = "Product '{$product->name}' is no longer available.";
                continue;
            }

            if (!$product->canAddToCart()) {
                $errors[] = "Product '{$product->name}' cannot be added to cart.";
                continue;
            }


            if ($cartItem->quantity < $product->min_quantity) {
                $errors[] = "Product '{$product->name}' requires minimum {$product->min_quantity} items.";
                continue;
            }

            if ($product->manage_stock && $cartItem->quantity > $product->stock_quantity) {
                $errors[] = "Product '{$product->name}' has only {$product->stock_quantity} items in stock.";
                continue;
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'items' => $cartItems
        ];
    }

    // ================================
    // ADMIN METHODS
    // ================================

    public function updateOrderStatus(ProductOrder $order, string $newStatus, string $adminNotes = null)
    {
        $oldStatus = $order->status;
        
        $order->update([
            'status' => $newStatus,
            'admin_notes' => $adminNotes ? $order->admin_notes . "\n" . $adminNotes : $order->admin_notes
        ]);

        Log::info('Product order status updated', [
            'order_id' => $order->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'admin_notes' => $adminNotes
        ]);

        return $order;
    }

    // ================================
    // PRIVATE HELPER METHODS
    // ================================

    private function generateOrderNumber()
    {
        $prefix = 'PO';
        $date = date('Ym');
        $sequence = ProductOrder::whereRaw("order_number LIKE '{$prefix}{$date}%'")->count() + 1;
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }


}