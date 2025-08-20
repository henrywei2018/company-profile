<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ProductOrder;
use App\Models\Product;
use App\Models\ProductCategory;
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

    // ================================
    // PRODUCT BROWSING METHODS (NEW)
    // ================================

    /**
     * Browse products within client panel
     */
    public function browse(Request $request)
    {
        $query = Product::with(['category'])
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
        $query = ProductOrder::where('client_id', Auth::id())
            ->with(['items.product', 'quotation']);

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Quotation filter
        if ($request->filled('needs_quotation')) {
            $query->where('needs_quotation', $request->needs_quotation === '1');
        }

        // Search
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('order_number', 'like', "%{$searchTerm}%")
                  ->orWhere('notes', 'like', "%{$searchTerm}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $orders = $query->paginate(15)->withQueryString();

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
}