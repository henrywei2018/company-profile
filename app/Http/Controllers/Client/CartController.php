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
     * Display cart page
     */
    public function index()
    {
        $cartItems = $this->orderService->getCart();
        
        // Calculate totals
        $cartTotal = $cartItems->sum(function($item) {
            return $item->quantity * ($item->product->current_price ?? 0);
        });

        // Validate cart items (check for any issues)
        $validation = $this->orderService->validateCartItems();
        $cartErrors = $validation['errors'] ?? [];

        return view('client.cart.index', compact(
            'cartItems', 
            'cartTotal',
            'cartErrors'
        ));
    }

    /**
     * Update cart item quantity (PATCH request)
     */
    public function updateQuantity(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0|max:999'
        ]);

        try {
            if ($validated['quantity'] == 0) {
                // Remove item if quantity is 0
                $this->orderService->removeFromCart($validated['product_id']);
                $message = 'Product removed from cart';
            } else {
                // Update quantity
                $this->orderService->updateCartQuantity(
                    $validated['product_id'],
                    $validated['quantity']
                );
                $message = 'Cart updated successfully';
            }

            // Get updated cart info
            $cartCount = $this->orderService->getCartCount();
            $cartTotal = $this->orderService->getCartTotal();

            return response()->json([
                'success' => true,
                'message' => $message,
                'cart_count' => $cartCount,
                'cart_total' => $cartTotal,
                'formatted_total' => 'Rp ' . number_format($cartTotal, 0, ',', '.')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get cart count and summary for navbar/widgets
     */
    public function getCartCount()
    {
        $cartCount = $this->orderService->getCartCount();
        $cartTotal = $this->orderService->getCartTotal();
        $cartItems = $this->orderService->getCart();

        // Get cart summary
        $summary = [
            'count' => $cartCount,
            'total' => $cartTotal,
            'formatted_total' => 'Rp ' . number_format($cartTotal, 0, ',', '.'),
            'items_count' => $cartItems->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }

    /**
     * Get cart summary for quick preview (e.g., dropdown in navbar)
     */
    public function getSummary()
    {
        $cartItems = $this->orderService->getCart();
        
        $summary = $cartItems->take(5)->map(function($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->product->id,
                'name' => $item->product->name,
                'quantity' => $item->quantity,
                'price' => $item->product->current_price,
                'formatted_price' => 'Rp ' . number_format($item->product->current_price, 0, ',', '.'),
                'subtotal' => $item->quantity * ($item->product->current_price ?? 0),
                'formatted_subtotal' => 'Rp ' . number_format($item->quantity * $item->product->current_price, 0, ',', '.'),
                'image' => $item->product->image 
                    ? asset('storage/' . $item->product->image) 
                    : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $summary,
                'total_items' => $cartItems->count(),
                'total_quantity' => $cartItems->sum('quantity'),
                'total_amount' => $this->orderService->getCartTotal(),
                'formatted_total' => 'Rp ' . number_format($this->orderService->getCartTotal(), 0, ',', '.'),
                'has_more' => $cartItems->count() > 5
            ]
        ]);
    }

    /**
     * Validate cart items and return issues
     */
    public function validateItems()
    {
        try {
            $validation = $this->orderService->validateCartItems();
            
            return response()->json([
                'success' => true,
                'valid' => $validation['valid'],
                'errors' => $validation['errors'] ?? [],
                'warnings' => $validation['warnings'] ?? []
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync cart with current product availability and prices
     */
    public function sync()
    {
        try {
            $cartItems = $this->orderService->getCart();
            $updated = [];
            $removed = [];

            foreach ($cartItems as $item) {
                $product = $item->product;
                
                // Check if product is still available
                if (!$product || $product->status !== 'published' || !$product->is_active) {
                    $this->orderService->removeFromCart($item->product_id);
                    $removed[] = [
                        'name' => $product->name ?? 'Unknown Product',
                        'reason' => 'Product no longer available'
                    ];
                    continue;
                }

                // Check stock
                if ($product->manage_stock && $item->quantity > $product->stock_quantity) {
                    if ($product->stock_quantity > 0) {
                        $this->orderService->updateCartQuantity($item->product_id, $product->stock_quantity);
                        $updated[] = [
                            'name' => $product->name,
                            'old_quantity' => $item->quantity,
                            'new_quantity' => $product->stock_quantity,
                            'reason' => 'Stock limit reached'
                        ];
                    } else {
                        $this->orderService->removeFromCart($item->product_id);
                        $removed[] = [
                            'name' => $product->name,
                            'reason' => 'Out of stock'
                        ];
                    }
                }

                // Check minimum quantity
                if ($item->quantity < ($product->min_quantity ?? 1)) {
                    $this->orderService->updateCartQuantity($item->product_id, $product->min_quantity ?? 1);
                    $updated[] = [
                        'name' => $product->name,
                        'old_quantity' => $item->quantity,
                        'new_quantity' => $product->min_quantity ?? 1,
                        'reason' => 'Minimum quantity required'
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Cart synchronized successfully',
                'updated' => $updated,
                'removed' => $removed,
                'cart_count' => $this->orderService->getCartCount(),
                'cart_total' => $this->orderService->getCartTotal()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync cart: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export cart items (for quotation request or sharing)
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'json'); // json, csv, pdf
        $cartItems = $this->orderService->getCart();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty'
            ], 400);
        }

        try {
            switch ($format) {
                case 'csv':
                    return $this->exportToCsv($cartItems);
                case 'pdf':
                    return $this->exportToPdf($cartItems);
                default:
                    return $this->exportToJson($cartItems);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export cart: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Move cart items to wishlist/save for later
     */
    public function saveForLater(Request $request)
    {
        $validated = $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id'
        ]);

        try {
            $saved = [];
            foreach ($validated['product_ids'] as $productId) {
                // Here you would implement wishlist/save for later functionality
                // For now, just remove from cart
                $removed = $this->orderService->removeFromCart($productId);
                if ($removed) {
                    $saved[] = $productId;
                }
            }

            return response()->json([
                'success' => true,
                'message' => count($saved) . ' item(s) saved for later',
                'saved_items' => $saved,
                'cart_count' => $this->orderService->getCartCount()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // ================================
    // PRIVATE HELPER METHODS
    // ================================

    private function exportToJson($cartItems)
    {
        $data = [
            'cart_export' => [
                'exported_at' => now()->toISOString(),
                'client_id' => auth()->id(),
                'client_name' => auth()->user()->name,
                'items' => $cartItems->map(function($item) {
                    return [
                        'product_id' => $item->product->id,
                        'product_name' => $item->product->name,
                        'sku' => $item->product->sku,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->product->current_price,
                        'subtotal' => $item->quantity * ($item->product->current_price ?? 0),
                        'specifications' => $item->specifications,
                    ];
                }),
                'summary' => [
                    'total_items' => $cartItems->count(),
                    'total_quantity' => $cartItems->sum('quantity'),
                    'total_amount' => $this->orderService->getCartTotal(),
                ]
            ]
        ];

        return response()->json($data);
    }

    private function exportToCsv($cartItems)
    {
        $filename = 'cart_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($cartItems) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Product Name',
                'SKU',
                'Quantity',
                'Unit Price',
                'Subtotal',
                'Specifications',
            ]);

            // CSV data
            foreach ($cartItems as $item) {
                fputcsv($file, [
                    $item->product->name,
                    $item->product->sku,
                    $item->quantity,
                    $item->product->current_price,
                    $item->quantity * $item->product->current_price,
                    $item->specifications ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportToPdf($cartItems)
    {
        // This would require a PDF library like DomPDF or similar
        // For now, return JSON with a message
        return response()->json([
            'success' => false,
            'message' => 'PDF export not implemented yet. Please use JSON or CSV format.'
        ], 501);
    }
}