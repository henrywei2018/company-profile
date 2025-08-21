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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Browse Products
                </a>

                <a href="{{ route('client.cart.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6">
                        </path>
                    </svg>
                    View Cart
                </a>
            </div>
        </div>

        <!-- Filter & Search -->
        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div>
                <label for="status"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select name="status" id="status"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing
                    </option>
                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered
                    </option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled
                    </option>
                </select>
            </div>


            <!-- Sort Filter -->
            <div>
                <label for="sort" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sort
                    By</label>
                <select name="sort" id="sort"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="created_at" {{ request('sort', 'created_at') === 'created_at' ? 'selected' : '' }}>
                        Date Created</option>
                    <option value="order_number" {{ request('sort') === 'order_number' ? 'selected' : '' }}>Order Number
                    </option>
                    <option value="total_amount" {{ request('sort') === 'total_amount' ? 'selected' : '' }}>Total Amount
                    </option>
                    <option value="status" {{ request('sort') === 'status' ? 'selected' : '' }}>Status</option>
                </select>
            </div>


            <!-- Optional: Sort Direction (asc/desc) -->
            <div>
                <label for="direction"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Direction</label>
                <select name="direction" id="direction"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="desc" {{ request('direction', 'desc') === 'desc' ? 'selected' : '' }}>Descending
                    </option>
                    <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>Ascending</option>
                </select>
            </div>
        </div>


        <!-- Clear Filters -->
        @if (request()->hasAny(['search', 'status', 'sort', 'direction']))
            <div class="flex justify-end">
                <a href="{{ route('client.orders.index') }}"
                    class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    Clear all filters
                </a>
            </div>
        @endif
        </form>
    </div>

    <!-- Orders List -->
    <div class="grid grid-cols-1 lg:grid-cols-7 gap-6">

        <!-- Left Column - Actions Panel (similar to messages) -->
        <div class="lg:col-span-1">
            <div
                class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

                <!-- Header -->
                <div class="px-2 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg flex justify-center font-semibold text-gray-900 dark:text-white">Quick Actions
                    </h3>
                </div>

                <div class="p-4 space-y-3">

                    <!-- Primary Actions -->
                    <div class="space-y-3">
                        <!-- Browse Products -->
                        <a href="{{ route('client.products.index') }}"
                            class="group flex flex-col items-center justify-center p-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 rounded-lg transition-all duration-200 hover:scale-105 shadow-sm hover:shadow-md">
                            <svg class="w-6 h-6 text-white mb-1" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                </path>
                            </svg>
                            <span class="text-xs text-center text-white opacity-90 group-hover:opacity-100">
                                Browse Products
                            </span>
                        </a>

                        <!-- Cart -->
                        <a href="{{ route('client.cart.index') }}"
                            class="group flex flex-col items-center justify-center p-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 rounded-lg transition-all duration-200 hover:scale-105 shadow-sm hover:shadow-md">
                            <svg class="w-6 h-6 text-white mb-1" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6">
                                </path>
                            </svg>
                            <span class="text-xs text-center text-white opacity-90 group-hover:opacity-100">
                                My Cart
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
                                <span
                                    class="font-medium text-orange-600">{{ $orders->where('status', 'pending')->count() }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Processing</span>
                                <span
                                    class="font-medium text-blue-600">{{ $orders->where('status', 'processing')->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Orders List -->
        <div class="lg:col-span-6">
            @if ($orders->count() > 0)
                <div
                    class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

                    <!-- Orders Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-neutral-700">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Order
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Items
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Amount
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-neutral-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($orders as $order)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700/50 transition-colors">
                                        <!-- Order Info -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-col">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    #{{ $order->order_number }}
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Items -->
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 dark:text-white">
                                                {{ $order->items->count() }} item(s)
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $order->items->first()->product->name ?? 'N/A' }}
                                                @if ($order->items->count() > 1)
                                                    +{{ $order->items->count() - 1 }} more
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Status -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
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
                                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                            </div>
                                        </td>

                                        <!-- Date -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            <div>{{ $order->created_at->format('M j, Y') }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $order->created_at->format('g:i A') }}</div>
                                        </td>

                                        <!-- Actions -->
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-2">
                                                <a href="{{ route('client.orders.show', $order) }}"
                                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                    View
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($orders->hasPages())
                        <div
                            class="bg-white dark:bg-neutral-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                            {{ $orders->links() }}
                        </div>
                    @endif
                </div>
            @else
                <!-- Empty State -->
                <div
                    class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No orders found</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by browsing our products
                            and placing your first order.</p>
                        <div class="mt-6">
                            <a href="{{ route('client.products.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
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
