{{-- resources/views/client/orders/index.blade.php --}}
<x-layouts.client>
    <x-slot name="title">My Orders</x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Orders</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Track and manage your product orders
                </p>
            </div>
            
            <!-- Action Buttons -->
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <a href="{{ route('client.products.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Browse Products
                </a>
                
                <a href="{{ route('client.cart') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6"></path>
                    </svg>
                    View Cart
                </a>
            </div>
        </div>

        <!-- Filter & Search -->
        <x-admin.filters-and-search 
            :route="route('client.orders.index')" 
            :filters="[
                [
                    'name' => 'status',
                    'label' => 'Status',
                    'allLabel' => 'All Status',
                    'options' => [
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ],
                ],
                [
                    'name' => 'needs_quotation',
                    'label' => 'Quotation',
                    'allLabel' => 'All Orders',
                    'options' => [
                        '1' => 'Needs Quotation',
                        '0' => 'Direct Order',
                    ],
                ],
            ]" 
            :sortOptions="[
                'created_at' => 'Date Created',
                'order_number' => 'Order Number',
                'total_amount' => 'Total Amount',
                'status' => 'Status',
            ]" />

        <!-- Orders List -->
        <div class="grid grid-cols-1 lg:grid-cols-7 gap-6">

            <!-- Left Column - Actions Panel (similar to messages) -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

                    <!-- Header -->
                    <div class="px-2 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg flex justify-center font-semibold text-gray-900 dark:text-white">Quick Actions</h3>
                    </div>

                    <div class="p-4 space-y-3">

                        <!-- Primary Actions -->
                        <div class="space-y-3">
                            <!-- Browse Products -->
                            <a href="{{ route('client.products.index') }}"
                                class="group flex flex-col items-center justify-center p-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 rounded-lg transition-all duration-200 hover:scale-105 shadow-sm hover:shadow-md">
                                <svg class="w-6 h-6 text-white mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                <span class="text-xs text-center text-white opacity-90 group-hover:opacity-100">
                                    Browse Products
                                </span>
                            </a>

                            <!-- Cart -->
                            <a href="{{ route('client.cart') }}"
                                class="group flex flex-col items-center justify-center p-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 rounded-lg transition-all duration-200 hover:scale-105 shadow-sm hover:shadow-md">
                                <svg class="w-6 h-6 text-white mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6"></path>
                                </svg>
                                <span class="text-xs text-center text-white opacity-90 group-hover:opacity-100">
                                    My Cart
                                </span>
                            </a>

                            <!-- Request Quotation -->
                            <a href="{{ route('client.quotations.create') }}"
                                class="group flex flex-col items-center justify-center p-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 rounded-lg transition-all duration-200 hover:scale-105 shadow-sm hover:shadow-md">
                                <svg class="w-6 h-6 text-white mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="text-xs text-center text-white opacity-90 group-hover:opacity-100">
                                    Request Quote
                                </span>
                            </a>
                        </div>

                        <!-- Quick Stats -->
                        <div class="pt-4 border-t border-gray-200 dark:border-gray-600">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Total Orders</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $orders->total() }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Pending</span>
                                    <span class="font-medium text-orange-600">{{ $orders->where('status', 'pending')->count() }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Processing</span>
                                    <span class="font-medium text-blue-600">{{ $orders->where('status', 'processing')->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Orders List -->
            <div class="lg:col-span-6">
                @if($orders->count() > 0)
                    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        
                        <!-- Orders Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-neutral-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Order
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Items
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Amount
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Date
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-neutral-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($orders as $order)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700/50 transition-colors">
                                            <!-- Order Info -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex flex-col">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                        #{{ $order->order_number }}
                                                    </div>
                                                    @if($order->needs_quotation)
                                                        <div class="flex items-center mt-1">
                                                            <svg class="w-3 h-3 text-blue-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                            </svg>
                                                            <span class="text-xs text-blue-600 dark:text-blue-400">Needs Quotation</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>

                                            <!-- Items -->
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900 dark:text-white">
                                                    {{ $order->items->count() }} item(s)
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $order->items->first()->product->name ?? 'N/A' }}
                                                    @if($order->items->count() > 1)
                                                        +{{ $order->items->count() - 1 }} more
                                                    @endif
                                                </div>
                                            </td>

                                            <!-- Status -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                                    {{ $order->status === 'processing' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                                    {{ $order->status === 'shipped' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200' : '' }}
                                                    {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                                    {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>

                                            <!-- Amount -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    @if($order->total_amount > 0)
                                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                                    @else
                                                        <span class="text-gray-500 dark:text-gray-400">Pending Quote</span>
                                                    @endif
                                                </div>
                                            </td>

                                            <!-- Date -->
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                <div>{{ $order->created_at->format('M j, Y') }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $order->created_at->format('g:i A') }}</div>
                                            </td>

                                            <!-- Actions -->
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex items-center justify-end space-x-2">
                                                    <a href="{{ route('client.orders.show', $order) }}" 
                                                       class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                        View
                                                    </a>
                                                    @if($order->quotation)
                                                        <a href="{{ route('client.quotations.show', $order->quotation) }}" 
                                                           class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                                            Quote
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($orders->hasPages())
                            <div class="bg-white dark:bg-neutral-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                                {{ $orders->links() }}
                            </div>
                        @endif
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No orders found</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by browsing our products and placing your first order.</p>
                            <div class="mt-6">
                                <a href="{{ route('client.products.index') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Browse Products
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.client>