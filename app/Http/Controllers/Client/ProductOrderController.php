<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ProductOrder;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\CartItem;
use App\Models\PaymentMethod;
use App\Services\ProductOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductOrderController extends Controller
{
    protected ProductOrderService $orderService;

    public function __construct(ProductOrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    // ================================
    // PRODUCT BROWSING METHODS (NEW)
    // ================================

    /**
     * Browse products within client panel
     */
    public function browse(Request $request)
    {
        $query = Product::with(['category', 'images'])
            ->where('status', 'published')
            ->where('is_active', true);

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('sku', 'like', "%{$searchTerm}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('product_category_id', $request->category);
        }

        // Price filter
        if ($request->filled('price_range')) {
            switch ($request->price_range) {
                case 'under_1m':
                    $query->where('current_price', '<', 1000000);
                    break;
                case '1m_5m':
                    $query->whereBetween('current_price', [1000000, 5000000]);
                    break;
                case '5m_10m':
                    $query->whereBetween('current_price', [5000000, 10000000]);
                    break;
                case 'over_10m':
                    $query->where('current_price', '>', 10000000);
                    break;
                case 'quote_required':
                    $query->where(function($q) {
                        $q->whereNull('current_price')
                          ->orWhere('current_price', '<=', 0);
                    });
                    break;
            }
        }

        // Stock filter
        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'in_stock':
                    $query->where(function($q) {
                        $q->where('manage_stock', false)
                          ->orWhere(function($q2) {
                              $q2->where('manage_stock', true)
                                 ->where('stock_quantity', '>', 0);
                          });
                    });
                    break;
                case 'out_of_stock':
                    $query->where('manage_stock', true)
                          ->where('stock_quantity', '<=', 0);
                    break;
            }
        }

        // Sorting
        $sortBy = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        
        switch ($sortBy) {
            case 'price':
                $query->orderBy('current_price', $sortDirection);
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'featured':
                $query->orderBy('featured', 'desc')->orderBy('name', 'asc');
                break;
            default:
                $query->orderBy('name', $sortDirection);
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = ProductCategory::where('is_active', true)->get();

        return view('client.products.index', compact('products', 'categories'));
    }

    /**
     * Show product detail within client panel
     */
    public function showProduct(Product $product)
    {
        if ($product->status !== 'published' || !$product->is_active) {
            abort(404);
        }

        $product->load(['category', 'images']);
        
        // Get related products
        $relatedProducts = Product::where('product_category_id', $product->product_category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'published')
            ->where('is_active', true)
            ->with(['images'])
            ->limit(4)
            ->get();

        return view('client.products.show', compact('product', 'relatedProducts'));
    }

    /**
     * Browse products by category
     */
    public function browseCategory(ProductCategory $category, Request $request)
    {
        if (!$category->is_active) {
            abort(404);
        }

        $query = $category->products()
            ->with(['images'])
            ->where('status', 'published')
            ->where('is_active', true);

        // Apply filters (same as browse method)
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('sku', 'like', "%{$searchTerm}%");
            });
        }

        $products = $query->paginate(12)->withQueryString();

        return view('client.products.category', compact('category', 'products'));
    }

    // ================================
    // ORDER MANAGEMENT (EXISTING)
    // ================================

    /**
     * Display client's orders
     */
    public function index(Request $request)
    {
        $baseQuery = ProductOrder::where('client_id', Auth::id())
            ->with(['items.product.images']);

        // Tab filtering
        $tab = $request->get('tab', 'active');
        
        if ($tab === 'delivered') {
            // Order History: Only completed (client-confirmed) orders
            $query = clone $baseQuery;
            $query->where('status', 'delivered')
                  ->where('delivery_confirmed_by_client', true);
        } else {
            // Active orders: All orders that are not completed
            $query = clone $baseQuery;
            $query->where(function($q) {
                $q->where('status', '!=', 'delivered')
                  ->orWhere(function($subQuery) {
                      $subQuery->where('status', 'delivered')
                               ->where('delivery_confirmed_by_client', false);
                  });
            });
        }

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('order_number', 'like', "%{$searchTerm}%")
                  ->orWhere('notes', 'like', "%{$searchTerm}%")
                  ->orWhereHas('items.product', function($productQuery) use ($searchTerm) {
                      $productQuery->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // Order by most recent first
        $query->orderBy('created_at', 'desc');

        // Get paginated results
        $orders = $query->paginate(15)->withQueryString();

        // Get counts for tabs
        $activeCount = ProductOrder::where('client_id', Auth::id())
            ->where(function($q) {
                $q->where('status', '!=', 'delivered')
                  ->orWhere(function($subQuery) {
                      $subQuery->where('status', 'delivered')
                               ->where('delivery_confirmed_by_client', false);
                  });
            })
            ->count();
            
        $deliveredCount = ProductOrder::where('client_id', Auth::id())
            ->where('status', 'delivered')
            ->where('delivery_confirmed_by_client', true)
            ->count();

        return view('client.orders.index', compact('orders', 'activeCount', 'deliveredCount'));
    }

    /**
     * Show specific order
     */
    public function show(ProductOrder $order)
    {
        $this->authorize('view', $order);
        
        $order->load(['items.product.images']);
        
        return view('client.orders.show', compact('order'));
    }


    /**
     * Show cart
     */
    public function cart()
    {
        $cartItems = $this->orderService->getCart();
        $cartTotal = $cartItems->sum(function($item) {
            return $item->quantity * ($item->product->current_price ?? 0);
        });
        
        return view('client.cart.index', compact('cartItems', 'cartTotal'));
    }

    /**
     * Show checkout form
     */
    public function checkout()
    {
        $cartItems = $this->orderService->getCart();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('client.cart.index')
                ->with('error', 'Your cart is empty');
        }

        $user = Auth::user();
        $cartTotal = $cartItems->sum(function($item) {
            return $item->quantity * ($item->product->current_price ?? 0);
        });
        
        return view('client.orders.checkout', compact('cartItems', 'user', 'cartTotal'));
    }


    /**
     * Create order from cart
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'delivery_address' => 'required|string|max:1000',
            'needed_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string|max:2000'
        ]);

        $cartItems = $this->orderService->getCart();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('client.cart.index')
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
     * Cancel an order
     */
    public function cancel(ProductOrder $order)
    {
        $this->authorize('cancel', $order);
        
        if (!in_array($order->status, ['pending', 'processing'])) {
            return redirect()->back()
                ->with('error', 'Order cannot be cancelled. Current status: ' . ucfirst($order->status));
        }

        try {
            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'admin_notes' => ($order->admin_notes ?? '') . "\nOrder cancelled by client at " . now()->format('Y-m-d H:i:s')
            ]);

            return redirect()->route('client.orders.show', $order)
                ->with('success', 'Order has been cancelled successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to cancel order: ' . $e->getMessage());
        }
    }

    /**
     * Show negotiation form
     */
    public function negotiationForm(ProductOrder $order)
    {
        $this->authorize('view', $order);
        
        if (!$order->canNegotiate()) {
            return redirect()->route('client.orders.show', $order)
                ->with('error', 'Price negotiation is not available for this order.');
        }

        $order->load(['items.product']);
        
        return view('client.orders.negotiate', compact('order'));
    }

    /**
     * Submit negotiation request
     */
    public function submitNegotiation(Request $request, ProductOrder $order)
    {
        $this->authorize('view', $order);
        
        if (!$order->canNegotiate()) {
            return redirect()->route('client.orders.show', $order)
                ->with('error', "Price negotiation is not available. Status: {$order->status}, Needs Negotiation: " . ($order->needs_negotiation ? 'Yes' : 'No') . ", Negotiation Status: " . ($order->negotiation_status ?? 'None'));
        }

        $validated = $request->validate([
            'negotiation_message' => 'required|string|max:1000',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.requested_unit_price' => 'required|numeric|min:0',
            'items.*.price_justification' => 'nullable|string|max:500'
        ]);

        try {
            // Check if this is a counter-offer before starting transaction
            $isCounterOffer = $order->negotiation_status === 'in_progress';
            
            DB::transaction(function () use ($validated, $order, $isCounterOffer) {
                // Calculate new total
                $requestedTotal = 0;
                
                // Update order items with negotiation details
                foreach ($validated['items'] as $itemData) {
                    $orderItem = $order->items()->where('product_id', $itemData['product_id'])->first();
                    if ($orderItem) {
                        $requestedTotalPrice = $orderItem->quantity * $itemData['requested_unit_price'];
                        
                        $orderItem->update([
                            'requested_unit_price' => $itemData['requested_unit_price'],
                            'requested_total_price' => $requestedTotalPrice,
                            'price_justification' => $itemData['price_justification'] ?? null
                        ]);
                        
                        $requestedTotal += $requestedTotalPrice;
                    }
                }

                // Update order with negotiation request
                $order->update([
                    'needs_negotiation' => true,
                    'negotiation_message' => $validated['negotiation_message'],
                    'requested_total' => $requestedTotal,
                    'negotiation_status' => 'pending',
                    'negotiation_requested_at' => now(),
                    'admin_notes' => $isCounterOffer 
                        ? ($order->admin_notes ?? '') . "\nClient counter-offered: " . $validated['negotiation_message']
                        : $order->admin_notes
                ]);
            });

            $message = $isCounterOffer 
                ? 'Counter-offer submitted successfully! We will review your response and get back to you soon.'
                : 'Negotiation request submitted successfully! We will review your request and respond soon.';
                
            return redirect()->route('client.orders.show', $order)
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to submit negotiation request: ' . $e->getMessage());
        }
    }

    /**
     * Accept admin's negotiation offer
     */
    public function acceptNegotiation(ProductOrder $order)
    {
        $this->authorize('view', $order);
        
        if (!$order->canAcceptNegotiation()) {
            return redirect()->route('client.orders.show', $order)
                ->with('error', 'Cannot accept this negotiation at this time.');
        }

        try {
            DB::transaction(function () use ($order) {
                // Mark negotiation as accepted and completed
                $order->update([
                    'negotiation_status' => 'completed',
                    'negotiation_responded_at' => now(),
                    'admin_notes' => ($order->admin_notes ?? '') . "\nClient accepted admin's counter-offer at " . now()->format('Y-m-d H:i:s')
                ]);
            });

            return redirect()->route('client.orders.show', $order)
                ->with('success', 'Admin\'s offer accepted successfully! The negotiation is now complete.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to accept negotiation: ' . $e->getMessage());
        }
    }

    /**
     * Show payment methods and upload form
     */
    public function paymentForm(ProductOrder $order)
    {
        $this->authorize('view', $order);
        
        if (!$order->canMakePayment()) {
            return redirect()->route('client.orders.show', $order)
                ->with('error', 'Payment is not available for this order at this time.');
        }

        $order->load(['items.product']);
        
        // Get active payment methods from admin settings
        $paymentMethods = PaymentMethod::active()->ordered()->get();
        
        if ($paymentMethods->isEmpty()) {
            return redirect()->route('client.orders.show', $order)
                ->with('error', 'No payment methods are currently available. Please contact support.');
        }
        
        return view('client.orders.payment', compact('order', 'paymentMethods'));
    }

    /**
     * Upload payment proof
     */
    public function uploadPaymentProof(Request $request, ProductOrder $order)
    {
        $this->authorize('view', $order);
        
        if (!$order->canUploadPaymentProof()) {
            return redirect()->route('client.orders.show', $order)
                ->with('error', 'Cannot upload payment proof for this order at this time.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|string|max:255',
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'payment_notes' => 'nullable|string|max:500'
        ]);

        try {
            // Handle file upload
            $paymentProofPath = null;
            if ($request->hasFile('payment_proof')) {
                $paymentProofPath = $request->file('payment_proof')->store('payment_proofs', 'public');
            }

            DB::transaction(function () use ($validated, $order, $paymentProofPath) {
                $order->update([
                    'payment_method' => $validated['payment_method'],
                    'payment_proof' => $paymentProofPath,
                    'payment_notes' => $validated['payment_notes'] ?? null,
                    'payment_status' => 'proof_uploaded',
                    'payment_uploaded_at' => now(),
                ]);
            });

            return redirect()->route('client.orders.show', $order)
                ->with('success', 'Payment proof uploaded successfully! We will verify your payment and update the order status.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to upload payment proof: ' . $e->getMessage());
        }
    }

    // ================================
    // CART OPERATIONS (EXISTING API ENDPOINTS)
    // ================================

    /**
     * Add item to cart
     */
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:999',
            'specifications' => 'nullable|string|max:1000'
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
                'cart_count' => $this->orderService->getCartCount(),
                'cart_total' => $this->orderService->getCartTotal()
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

        try {
            $removed = $this->orderService->removeFromCart($validated['product_id']);

            return response()->json([
                'success' => $removed,
                'message' => $removed ? 'Product removed from cart' : 'Product not found in cart',
                'cart_count' => $this->orderService->getCartCount(),
                'cart_total' => $this->orderService->getCartTotal()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Update cart item quantity
     */
    public function updateCartQuantity(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0|max:999'
        ]);

        try {
            if ($validated['quantity'] == 0) {
                $this->orderService->removeFromCart($validated['product_id']);
                $message = 'Product removed from cart';
            } else {
                $this->orderService->updateCartQuantity(
                    $validated['product_id'],
                    $validated['quantity']
                );
                $message = 'Cart updated successfully';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'cart_count' => $this->orderService->getCartCount(),
                'cart_total' => $this->orderService->getCartTotal()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Clear entire cart
     */
    public function clearCart()
    {
        try {
            $cleared = $this->orderService->clearCart();

            return response()->json([
                'success' => true,
                'message' => $cleared > 0 ? 'Cart cleared successfully' : 'Cart was already empty',
                'cleared_items' => $cleared,
                'cart_count' => 0,
                'cart_total' => 0
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
        $cartCount = $this->orderService->getCartCount();
        
        return response()->json([
            'success' => true,
            'count' => $cartCount,
            'total' => $this->orderService->getCartTotal()
        ]);
    }

    /**
     * Get cart summary with negotiation info
     */
    public function getCartSummary()
    {
        try {
            $cartItems = $this->orderService->getCart();
            $cartTotal = $this->orderService->getCartTotal();
            $cartCount = $this->orderService->getCartCount();
            
            return response()->json([
                'success' => true,
                'count' => $cartCount,
                'total' => $cartTotal
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

}