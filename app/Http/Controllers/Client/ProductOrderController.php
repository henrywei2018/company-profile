<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ProductOrder;
use App\Models\CartItem;
use App\Services\ProductOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductOrderController extends Controller
{
    protected ProductOrderService $orderService;

    public function __construct(ProductOrderService $orderService)
    {
        $this->middleware(['auth', 'role:client']);
        $this->orderService = $orderService;
    }

    /**
     * Display client's orders
     */
    public function index()
    {
        $orders = ProductOrder::where('client_id', Auth::id())
            ->with(['items.product', 'quotation'])
            ->latest()
            ->paginate(15);

        return view('client.orders.index', compact('orders'));
    }

    /**
     * Show specific order
     */
    public function show(ProductOrder $order)
    {
        $this->authorize('view', $order);
        
        $order->load(['items.product', 'quotation']);
        
        return view('client.orders.show', compact('order'));
    }

    /**
     * Show cart
     */
    public function cart()
    {
        $cartItems = $this->orderService->getCart();
        $cartTotal = $cartItems->sum(function($item) {
            return $item->quantity * ($item->product->getCurrentPrice() ?? 0);
        });
        
        return view('client.cart.index', compact('cartItems', 'cartTotal'));
    }

    /**
     * Add to cart
     */
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'specifications' => 'nullable|string'
        ]);

        try {
            $cartItem = $this->orderService->addToCart(
                $validated['product_id'],
                $validated['quantity'],
                $validated['specifications'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully!',
                'cart_count' => $this->orderService->getCart()->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remove from cart
     */
    public function removeFromCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $removed = $this->orderService->removeFromCart($validated['product_id']);

        return response()->json([
            'success' => $removed,
            'message' => $removed ? 'Product removed from cart' : 'Product not found in cart',
            'cart_count' => $this->orderService->getCart()->count()
        ]);
    }

    /**
     * Show checkout form
     */
    public function checkout()
    {
        $cartItems = $this->orderService->getCart();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('client.cart')
                ->with('error', 'Your cart is empty');
        }

        $user = Auth::user();
        $cartTotal = $cartItems->sum(function($item) {
            return $item->quantity * ($item->product->getCurrentPrice() ?? 0);
        });
        
        return view('client.orders.checkout', compact('cartItems', 'user', 'cartTotal'));
    }

    /**
     * Create order from cart
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'delivery_address' => 'required|string',
            'needed_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string'
        ]);

        $cartItems = $this->orderService->getCart();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('client.cart')
                ->with('error', 'Your cart is empty');
        }

        try {
            $deliveryInfo = [
                'address' => $validated['delivery_address'],
                'needed_date' => $validated['needed_date'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ];

            $order = $this->orderService->createOrderFromCart($deliveryInfo);

            return redirect()->route('client.orders.show', $order)
                ->with('success', 'Order created successfully! Order number: ' . $order->order_number);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    /**
     * Update cart item quantity
     */
    public function updateCartQuantity(Request $request)
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
     * Clear cart
     */
    public function clearCart()
    {
        $this->orderService->clearCart();
        
        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully'
        ]);
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