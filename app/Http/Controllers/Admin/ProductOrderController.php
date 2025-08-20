<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductOrder;
use App\Models\User;
use App\Services\ProductOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

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
            'needs_quotation' => 'nullable|boolean'
        ]);

        $orders = ProductOrder::with(['client', 'items.product', 'quotation'])
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
            ->when(isset($filters['needs_quotation']), fn($q) => $q->where('needs_quotation', $filters['needs_quotation']))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        // Statistics for admin dashboard
        $statistics = [
            'total_orders' => ProductOrder::count(),
            'pending_orders' => ProductOrder::where('status', 'pending')->count(),
            'processing_orders' => ProductOrder::where('status', 'processing')->count(),
            'completed_orders' => ProductOrder::where('status', 'completed')->count(),
            'orders_need_quotation' => ProductOrder::where('needs_quotation', true)->count(),
            'total_revenue' => ProductOrder::where('status', 'completed')->sum('total_amount'),
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
        $order->load(['client', 'items.product.images', 'quotation']);
        
        return view('admin.orders.show', compact('order'));
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
            if ($order->quotation_id) {
                return redirect()->back()
                    ->with('info', 'Order already has a quotation.');
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
                'Needs Quotation', 'Notes'
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
                    $order->needs_quotation ? 'Yes' : 'No',
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
}