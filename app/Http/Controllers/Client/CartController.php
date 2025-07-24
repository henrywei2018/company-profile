<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\ProductOrderService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected ProductOrderService $orderService;

    public function __construct(ProductOrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Show cart
     */
    public function index()
    {
        $cartItems = $this->orderService->getCart();
        $cartTotal = $cartItems->sum(function($item) {
            return $item->quantity * $item->product->getCurrentPrice();
        });
        
        return view('client.cart.index', compact('cartItems', 'cartTotal'));
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            $this->orderService->addToCart(
                $validated['product_id'],
                $validated['quantity']
            );

            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get cart count for navbar
     */
    public function getCartCount()
    {
        $cartCount = $this->orderService->getCart()->count();
        
        return response()->json(['count' => $cartCount]);
    }
}