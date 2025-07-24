<?php

namespace App\Services;

use App\Models\ProductOrder;
use App\Models\ProductOrderItem;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Quotation;
use App\Facades\Notifications;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ProductOrderService
{
    // ================================
    // ORDER CREATION
    // ================================

    public function createOrderFromCart(array $deliveryInfo)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('client')) {
            throw new \Exception('Only authenticated clients can create orders.');
        }

        return DB::transaction(function () use ($user, $deliveryInfo) {
            try {
                $cartItems = CartItem::getCartForUser($user->id);
                
                if ($cartItems->isEmpty()) {
                    throw new \Exception('Cart is empty.');
                }

                // Validate all products are still available
                foreach ($cartItems as $cartItem) {
                    if (!$cartItem->product || !$cartItem->product->is_active) {
                        throw new \Exception("Product '{$cartItem->product->name}' is no longer available.");
                    }
                    
                    if (!$cartItem->product->canAddToCart()) {
                        throw new \Exception("Product '{$cartItem->product->name}' cannot be ordered directly.");
                    }

                    // Check if price is available
                    if ($cartItem->product->current_price <= 0) {
                        throw new \Exception("Product '{$cartItem->product->name}' has no valid price.");
                    }
                }

                // Create the order
                $order = ProductOrder::create([
                    'order_number' => $this->generateOrderNumber(),
                    'client_id' => $user->id,
                    'delivery_address' => $deliveryInfo['address'],
                    'needed_date' => $deliveryInfo['needed_date'] ?? null,
                    'notes' => $deliveryInfo['notes'] ?? null,
                    'status' => 'pending',
                ]);

                // Add items to order
                foreach ($cartItems as $cartItem) {
                    $order->items()->create([
                        'product_id' => $cartItem->product_id,
                        'quantity' => $cartItem->quantity,
                        'price' => $cartItem->product->current_price, // Use attribute
                        'specifications' => $cartItem->specifications,
                    ]);
                }

                // Calculate total
                $order->calculateTotal();

                // Check if needs quotation
                if ($order->shouldCreateQuotation()) {
                    $this->convertToQuotation($order);
                } else {
                    $order->update(['status' => 'confirmed']);
                }

                // Clear cart after successful order
                CartItem::where('user_id', $user->id)->delete();

                Log::info('Product order created successfully', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'client_id' => $order->client_id,
                    'total_amount' => $order->total_amount,
                    'needs_quotation' => $order->needs_quotation
                ]);

                return $order;

            } catch (\Exception $e) {
                Log::error('Failed to create product order', [
                    'error' => $e->getMessage(),
                    'client_id' => $user->id
                ]);
                throw $e;
            }
        });
    }

    public function convertToQuotation(ProductOrder $order)
    {
        try {
            $quotation = Quotation::create([
                'quotation_number' => $this->generateQuotationNumber(),
                'product_order_id' => $order->id,
                'name' => $order->client->name,
                'email' => $order->client->email,
                'phone' => $order->client->phone,
                'client_id' => $order->client_id,
                'project_type' => 'Product Order - ' . $order->order_number,
                'requirements' => $this->generateRequirementsFromOrder($order),
                'has_products' => true,
                'status' => 'pending',
                'source' => 'product_order',
            ]);

            $order->update([
                'quotation_id' => $quotation->id,
                'needs_quotation' => true,
                'status' => 'pending'
            ]);

            Log::info('Product order converted to quotation', [
                'order_id' => $order->id,
                'quotation_id' => $quotation->id
            ]);

            return $quotation;

        } catch (\Exception $e) {
            Log::error('Failed to convert order to quotation', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
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
            // Give more specific error message based on product type
            if ($product->requiresQuote()) {
                throw new \Exception('This product requires a quotation. Please contact us for pricing.');
            }
            
            if ($product->current_price <= 0) {
                throw new \Exception('This product has no valid price set.');
            }
            
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

    public function getCart()
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('client')) {
            return collect();
        }

        return CartItem::getCartForUser($user->id);
    }

    public function removeFromCart($productId)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('client')) {
            return false;
        }

        return CartItem::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->delete() > 0;
    }

    public function clearCart()
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('client')) {
            return 0;
        }

        return CartItem::where('user_id', $user->id)->delete();
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
                $errors[] = "Product '{$product->name}' cannot be ordered directly.";
                continue;
            }

            if ($product->current_price <= 0) {
                $errors[] = "Product '{$product->name}' has invalid pricing.";
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

    private function generateQuotationNumber()
    {
        $prefix = 'QUO';
        $date = date('Ym');
        $sequence = Quotation::whereRaw("quotation_number LIKE '{$prefix}{$date}%'")->count() + 1;
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    private function generateRequirementsFromOrder(ProductOrder $order)
    {
        $requirements = "Product Order Requirements:\n\n";
        
        foreach ($order->items as $item) {
            $requirements .= "- {$item->product->name} (Qty: {$item->quantity})\n";
            $requirements .= "  Price: " . number_format($item->price) . " IDR\n";
            if ($item->specifications) {
                $requirements .= "  Specifications: {$item->specifications}\n";
            }
            $requirements .= "\n";
        }
        
        $requirements .= "Total Amount: " . number_format($order->total_amount) . " IDR\n";
        $requirements .= "Delivery Address: {$order->delivery_address}\n";
        
        if ($order->needed_date) {
            $requirements .= "Required Date: {$order->needed_date->format('d/m/Y')}\n";
        }
        
        if ($order->notes) {
            $requirements .= "\nSpecial Notes:\n{$order->notes}";
        }
        
        return $requirements;
    }
}