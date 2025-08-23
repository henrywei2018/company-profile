<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductOrder;
use App\Models\User;
use App\Services\ProductOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

class ProductOrderController extends Controller
{
    protected ProductOrderService $orderService;

    public function __construct(ProductOrderService $orderService)
    {
        $this->middleware(['auth', 'role:admin|manager|super-admin']);
        $this->orderService = $orderService;
    }

    /**
     * Display admin orders dashboard
     * Route: GET /admin/orders
     */
    public function index(Request $request)
    {
        $filters = $request->validate([
            'status' => 'nullable|string|in:pending,confirmed,processing,ready,delivered,completed',
            'search' => 'nullable|string|max:255',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'client_id' => 'nullable|exists:users,id',
            'needs_negotiation' => 'nullable|boolean',
            'negotiation_status' => 'nullable|string|in:pending,in_progress,accepted,rejected,completed'
        ]);

        $orders = ProductOrder::with(['client', 'items.product'])
            ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status))
            ->when($filters['search'] ?? null, function($q, $search) {
                $q->where(function($query) use ($search) {
                    $query->where('order_number', 'like', "%{$search}%")
                          ->orWhereHas('client', function($clientQuery) use ($search) {
                              $clientQuery->where('name', 'like', "%{$search}%")
                                        ->orWhere('email', 'like', "%{$search}%");
                          });
                });
            })
            ->when($filters['date_from'] ?? null, fn($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn($q, $date) => $q->whereDate('created_at', '<=', $date))
            ->when($filters['client_id'] ?? null, fn($q, $clientId) => $q->where('client_id', $clientId))
            ->when(isset($filters['needs_negotiation']), fn($q) => $q->where('needs_negotiation', $filters['needs_negotiation']))
            ->when($filters['negotiation_status'] ?? null, fn($q, $status) => $q->where('negotiation_status', $status))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        // Statistics for admin dashboard
        $statistics = [
            'total_orders' => ProductOrder::count(),
            'pending_orders' => ProductOrder::where('status', 'pending')->count(),
            'processing_orders' => ProductOrder::where('status', 'processing')->count(),
            'delivered_orders' => ProductOrder::where('status', 'delivered')->count(),
            'completed_orders' => ProductOrder::where('delivery_confirmed_by_client', true)->count(),
            'awaiting_confirmation' => ProductOrder::where('status', 'delivered')->where('delivery_confirmed_by_client', false)->where('delivery_disputed', false)->count(),
            'disputed_orders' => ProductOrder::where('delivery_disputed', true)->count(),
            'orders_need_negotiation' => ProductOrder::where('needs_negotiation', true)->count(),
            'pending_negotiations' => ProductOrder::where('negotiation_status', 'pending')->count(),
            'total_revenue' => ProductOrder::where('delivery_confirmed_by_client', true)->sum('total_amount'),
            'avg_order_value' => ProductOrder::avg('total_amount'),
            'today_orders' => ProductOrder::whereDate('created_at', today())->count(),
        ];

        // Get clients for filter dropdown
        $clients = User::role('client')->orderBy('name')->get(['id', 'name', 'email']);

        return view('admin.orders.index', compact('orders', 'filters', 'statistics', 'clients'));
    }

    /**
     * Show order details
     * Route: GET /admin/orders/{order}
     */
    public function show(ProductOrder $order)
    {
        $order->load(['client', 'items.product.images']);
        
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show negotiation details and response form
     * Route: GET /admin/orders/{order}/negotiation
     */
    public function showNegotiation(ProductOrder $order)
    {
        if (!$order->needs_negotiation) {
            return redirect()->route('admin.orders.show', $order)
                ->with('error', 'This order does not have a negotiation request.');
        }

        $order->load(['client', 'items.product.images']);
        
        return view('admin.orders.negotiation', compact('order'));
    }

    /**
     * Respond to negotiation request
     * Route: POST /admin/orders/{order}/negotiation/respond
     */
    public function respondToNegotiation(Request $request, ProductOrder $order)
    {
        if (!$order->needs_negotiation || $order->negotiation_status !== 'pending') {
            return redirect()->route('admin.orders.show', $order)
                ->with('error', 'This negotiation cannot be processed.');
        }

        $validated = $request->validate([
            'action' => 'required|in:accept,reject,counter',
            'admin_response' => 'required|string|max:1000',
            'items' => 'required_if:action,accept,counter|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.final_unit_price' => 'required_if:action,accept,counter|numeric|min:0',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        try {
            DB::transaction(function () use ($validated, $order) {
                if ($validated['action'] === 'reject') {
                    // Reject negotiation
                    $order->update([
                        'negotiation_status' => 'rejected',
                        'negotiation_responded_at' => now(),
                        'admin_notes' => ($order->admin_notes ?? '') . "\nNegotiation rejected: " . $validated['admin_response']
                    ]);
                } else {
                    // Accept or counter the negotiation
                    $newTotal = 0;
                    
                    // Update order items with final negotiated prices
                    foreach ($validated['items'] as $itemData) {
                        $orderItem = $order->items()->where('product_id', $itemData['product_id'])->first();
                        if ($orderItem) {
                            $finalTotalPrice = $orderItem->quantity * $itemData['final_unit_price'];
                            
                            $orderItem->update([
                                'price' => $itemData['final_unit_price'], // Update actual price
                                'total' => $finalTotalPrice // Update actual total
                            ]);
                            
                            $newTotal += $finalTotalPrice;
                        }
                    }

                    // Update order with negotiated terms
                    $order->update([
                        'total_amount' => $newTotal,
                        'negotiation_status' => $validated['action'] === 'accept' ? 'accepted' : 'in_progress', // Allow client to counter
                        'negotiation_responded_at' => now(),
                        'admin_notes' => ($order->admin_notes ?? '') . "\nNegotiation " . $validated['action'] . "ed: " . $validated['admin_response']
                    ]);
                }
            });

            $message = match($validated['action']) {
                'accept' => 'Negotiation accepted successfully! Prices have been updated.',
                'reject' => 'Negotiation rejected.',
                'counter' => 'Counter-offer sent successfully! Prices have been updated.',
            };

            return redirect()->route('admin.orders.show', $order)
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to process negotiation: ' . $e->getMessage());
        }
    }

    /**
     * Update order status
     * Route: PUT /admin/orders/{order}/status
     */
    public function updateStatus(Request $request, ProductOrder $order)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,confirmed,processing,ready,delivered,completed',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        try {
            $this->orderService->updateOrderStatus(
                $order, 
                $validated['status'], 
                $validated['admin_notes'] ?? null
            );

            return redirect()->back()
                ->with('success', 'Order status updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update order status: ' . $e->getMessage());
        }
    }

    /**
     * Convert order to quotation
     * Route: POST /admin/orders/{order}/convert-quotation
     */
    public function convertToQuotation(ProductOrder $order)
    {
        try {
            // Check if conversion service method exists
            if (!method_exists($this->orderService, 'convertToQuotation')) {
                return redirect()->back()
                    ->with('error', 'Quotation conversion is not available.');
            }

            $quotation = $this->orderService->convertToQuotation($order);

            return redirect()->route('admin.quotations.show', $quotation)
                ->with('success', 'Order converted to quotation successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to convert to quotation: ' . $e->getMessage());
        }
    }

    /**
     * Export orders to CSV
     * Route: GET /admin/orders/export/csv
     */
    public function exportCsv(Request $request)
    {
        $filters = $request->only(['status', 'date_from', 'date_to', 'client_id']);
        
        $orders = ProductOrder::with(['client', 'items.product'])
            ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status))
            ->when($filters['date_from'] ?? null, fn($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn($q, $date) => $q->whereDate('created_at', '<=', $date))
            ->when($filters['client_id'] ?? null, fn($q, $clientId) => $q->where('client_id', $clientId))
            ->orderBy('created_at', 'desc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="product-orders-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Order Number', 'Client Name', 'Client Email', 'Status', 
                'Total Amount (IDR)', 'Items Count', 'Created At', 'Delivery Address',
                'Negotiation Status', 'Notes'
            ]);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->client->name ?? 'N/A',
                    $order->client->email ?? 'N/A',
                    ucfirst($order->status),
                    number_format($order->total_amount, 0, ',', '.'),
                    $order->items->count(),
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->delivery_address,
                    $order->negotiation_status ?? 'None',
                    $order->notes ?? ''
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Get order statistics (AJAX)
     * Route: GET /admin/orders/api/statistics
     */
    public function statistics()
    {
        try {
            $stats = [
                'daily_orders' => ProductOrder::whereDate('created_at', today())->count(),
                'weekly_orders' => ProductOrder::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'monthly_orders' => ProductOrder::whereMonth('created_at', now()->month)->count(),
                'status_breakdown' => ProductOrder::selectRaw('status, count(*) as count')
                    ->groupBy('status')
                    ->pluck('count', 'status'),
                'top_products' => \DB::table('product_order_items')
                    ->join('products', 'product_order_items.product_id', '=', 'products.id')
                    ->selectRaw('products.name, sum(product_order_items.quantity) as total_sold')
                    ->groupBy('products.id', 'products.name')
                    ->orderByDesc('total_sold')
                    ->limit(10)
                    ->get(),
                'recent_orders' => ProductOrder::with('client')
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->map(function($order) {
                        return [
                            'id' => $order->id,
                            'order_number' => $order->order_number,
                            'client_name' => $order->client->name ?? 'N/A',
                            'status' => $order->status,
                            'total_amount' => number_format($order->total_amount, 0, ',', '.'),
                            'created_at' => $order->created_at->format('M d, H:i')
                        ];
                    })
            ];

            return response()->json($stats);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get statistics'
            ], 500);
        }
    }

    /**
     * Show payment review for specific order
     */
    public function showPayment(ProductOrder $order)
    {
        $this->authorize('update', $order);
        
        $order->load(['client', 'items.product']);
        
        return view('admin.orders.payment', compact('order'));
    }

    /**
     * Verify payment proof
     */
    public function verifyPayment(Request $request, ProductOrder $order)
    {
        $this->authorize('update', $order);
        
        $validated = $request->validate([
            'action' => 'required|in:verify,reject',
            'admin_notes' => 'nullable|string|max:500'
        ]);

        try {
            $message = '';
            
            DB::transaction(function () use ($validated, $order, &$message) {
                if ($validated['action'] === 'verify') {
                    $order->update([
                        'payment_status' => 'verified',
                        'payment_verified_at' => now(),
                        'admin_notes' => ($order->admin_notes ?? '') . "\nPayment verified by admin at " . now()->format('Y-m-d H:i:s') . 
                                        ($validated['admin_notes'] ? "\nAdmin notes: " . $validated['admin_notes'] : '')
                    ]);
                    
                    $message = 'Payment verified successfully! Order can now proceed to processing.';
                } else {
                    $order->update([
                        'payment_status' => 'rejected',
                        'admin_notes' => ($order->admin_notes ?? '') . "\nPayment rejected by admin at " . now()->format('Y-m-d H:i:s') . 
                                        ($validated['admin_notes'] ? "\nReason: " . $validated['admin_notes'] : '')
                    ]);
                    
                    $message = 'Payment rejected. Client will need to upload new payment proof.';
                }
            });

            return redirect()->route('admin.orders.show', $order)
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update payment status: ' . $e->getMessage());
        }
    }

    /**
     * Payment management dashboard
     */
    public function paymentsIndex(Request $request)
    {
        $query = ProductOrder::with(['client', 'items'])
            ->whereNotNull('payment_proof')
            ->orWhere('payment_status', '!=', 'pending');

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('payment_uploaded_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('payment_uploaded_at', '<=', $request->date_to);
        }

        $orders = $query->latest('payment_uploaded_at')->paginate(20);

        // Payment statistics
        $stats = [
            'total_payments' => ProductOrder::whereNotNull('payment_proof')->count(),
            'pending_review' => ProductOrder::where('payment_status', 'proof_uploaded')->count(),
            'verified_payments' => ProductOrder::where('payment_status', 'verified')->count(),
            'rejected_payments' => ProductOrder::where('payment_status', 'rejected')->count(),
            'total_amount_pending' => ProductOrder::where('payment_status', 'proof_uploaded')->sum('total_amount'),
            'total_amount_verified' => ProductOrder::where('payment_status', 'verified')->sum('total_amount'),
        ];

        return view('admin.orders.payments.index', compact('orders', 'stats'));
    }

    /**
     * Show delivery confirmation management
     * Route: GET /admin/orders/{order}/delivery
     */
    public function showDelivery(ProductOrder $order)
    {
        $order->load(['client', 'items.product.images']);
        
        return view('admin.orders.delivery', compact('order'));
    }

    /**
     * Resolve delivery dispute
     * Route: POST /admin/orders/{order}/resolve-dispute
     */
    public function resolveDispute(Request $request, ProductOrder $order)
    {
        if (!$order->delivery_disputed) {
            return redirect()->route('admin.orders.show', $order)
                ->with('error', 'This order does not have a delivery dispute.');
        }

        $validated = $request->validate([
            'action' => 'required|in:acknowledge,resolve',
            'admin_resolution' => 'required|string|max:1000',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        try {
            DB::transaction(function () use ($validated, $order) {
                if ($validated['action'] === 'acknowledge') {
                    // Acknowledge the dispute - waiting for client response
                    $order->update([
                        'dispute_status' => 'acknowledged',
                        'admin_dispute_response' => $validated['admin_resolution'],
                        'admin_responded_at' => now(),
                        'admin_notes' => ($order->admin_notes ?? '') . 
                            "\nDispute acknowledged by admin at " . now()->format('Y-m-d H:i:s') . 
                            "\nAdmin response: " . $validated['admin_resolution'] .
                            ($validated['admin_notes'] ? "\nAdditional notes: " . $validated['admin_notes'] : '')
                    ]);
                } else {
                    // Propose dispute resolution - waiting for client acceptance
                    $order->update([
                        'dispute_status' => 'resolved',
                        'admin_dispute_response' => $validated['admin_resolution'],
                        'admin_responded_at' => now(),
                        'admin_notes' => ($order->admin_notes ?? '') . 
                            "\nDispute resolution proposed by admin at " . now()->format('Y-m-d H:i:s') . 
                            "\nProposed resolution: " . $validated['admin_resolution'] .
                            ($validated['admin_notes'] ? "\nAdditional notes: " . $validated['admin_notes'] : '')
                    ]);
                }
            });

            $message = $validated['action'] === 'acknowledge' 
                ? 'Dispute acknowledged. Customer will be notified and can respond.'
                : 'Resolution proposed. Waiting for customer to accept the resolution.';

            return redirect()->route('admin.orders.show', $order)
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to process dispute: ' . $e->getMessage());
        }
    }

    /**
     * Force confirm delivery (admin override)
     * Route: POST /admin/orders/{order}/force-confirm
     */
    public function forceConfirm(Request $request, ProductOrder $order)
    {
        if ($order->delivery_confirmed_by_client) {
            return redirect()->route('admin.orders.show', $order)
                ->with('error', 'This order is already confirmed by the client.');
        }

        $validated = $request->validate([
            'admin_reason' => 'required|string|max:1000',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        try {
            $order->update([
                'delivery_confirmed_by_client' => true,
                'delivery_confirmed_at' => now(),
                'delivery_disputed' => false, // Clear any disputes
                'admin_notes' => ($order->admin_notes ?? '') . 
                    "\nDelivery force-confirmed by admin at " . now()->format('Y-m-d H:i:s') . 
                    "\nReason: " . $validated['admin_reason'] .
                    ($validated['admin_notes'] ? "\nAdditional notes: " . $validated['admin_notes'] : '')
            ]);

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Order delivery has been force-confirmed successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to force-confirm delivery: ' . $e->getMessage());
        }
    }

    /**
     * Get delivery confirmation statistics (AJAX)
     * Route: GET /admin/orders/api/delivery-stats
     */
    public function deliveryStats()
    {
        try {
            $stats = [
                'delivered_total' => ProductOrder::where('status', 'delivered')->count(),
                'awaiting_confirmation' => ProductOrder::where('status', 'delivered')
                    ->where('delivery_confirmed_by_client', false)
                    ->where('delivery_disputed', false)
                    ->count(),
                'confirmed_by_client' => ProductOrder::where('delivery_confirmed_by_client', true)->count(),
                'disputed' => ProductOrder::where('delivery_disputed', true)->count(),
                'recent_confirmations' => ProductOrder::where('delivery_confirmed_by_client', true)
                    ->with('client')
                    ->latest('delivery_confirmed_at')
                    ->limit(5)
                    ->get()
                    ->map(function($order) {
                        return [
                            'id' => $order->id,
                            'order_number' => $order->order_number,
                            'client_name' => $order->client->name ?? 'N/A',
                            'confirmed_at' => $order->delivery_confirmed_at->format('M d, H:i'),
                            'notes' => $order->client_delivery_notes
                        ];
                    }),
                'recent_disputes' => ProductOrder::where('delivery_disputed', true)
                    ->with('client')
                    ->latest('dispute_reported_at')
                    ->limit(5)
                    ->get()
                    ->map(function($order) {
                        return [
                            'id' => $order->id,
                            'order_number' => $order->order_number,
                            'client_name' => $order->client->name ?? 'N/A',
                            'dispute_reason' => $order->dispute_reason,
                            'reported_at' => $order->dispute_reported_at->format('M d, H:i')
                        ];
                    })
            ];

            return response()->json($stats);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get delivery statistics'
            ], 500);
        }
    }
}