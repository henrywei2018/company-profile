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

        <!-- Order Tabs -->
        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Tab Navigation -->
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                    <a href="{{ route('client.orders.index') }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ !request('tab') || request('tab') === 'active' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Active Orders
                        <span class="ml-2 bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300 py-0.5 px-2 rounded-full text-xs font-medium">
                            {{ $activeCount ?? 0 }}
                        </span>
                    </a>
                    <a href="{{ route('client.orders.index', ['tab' => 'delivered']) }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ request('tab') === 'delivered' ? 'border-green-500 text-green-600 dark:text-green-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Order History
                        <span class="ml-2 bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300 py-0.5 px-2 rounded-full text-xs font-medium">
                            {{ $deliveredCount ?? 0 }}
                        </span>
                    </a>
                </nav>
            </div>

            <!-- Search Bar (Simple) -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-neutral-700">
                <form method="GET" action="{{ route('client.orders.index') }}" class="flex items-center space-x-3">
                    @if(request('tab'))
                        <input type="hidden" name="tab" value="{{ request('tab') }}">
                    @endif
                    <div class="flex-1">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Search orders by number or product name..." 
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    @if(request('search'))
                        <a href="{{ route('client.orders.index', request('tab') ? ['tab' => request('tab')] : []) }}" 
                           class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Clear
                        </a>
                    @endif
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Search
                    </button>
                </form>
            </div>
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
                                <span class="text-gray-600 dark:text-gray-400">Active Orders</span>
                                <span class="font-medium text-blue-600">{{ $activeCount }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Delivered</span>
                                <span class="font-medium text-green-600">{{ $deliveredCount }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Total Orders</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $activeCount + $deliveredCount }}</span>
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
                    <div class="overflow-x-auto order-table-container">
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
                                    <!-- Main Order Row -->
                                    <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700/50 expandable-row cursor-pointer" 
                                        onclick="toggleOrderItems('order-{{ $order->id }}')">
                                        <!-- Order Info -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 mr-3">
                                                    <svg class="w-4 h-4 text-gray-400 chevron-icon" 
                                                         id="chevron-order-{{ $order->id }}" 
                                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                    </svg>
                                                </div>
                                                <div class="flex flex-col">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                        #{{ $order->order_number }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Items -->
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 dark:text-white font-medium">
                                                {{ $order->items->count() }} item(s)
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $order->items->first()->product->name ?? 'N/A' }}
                                                @if ($order->items->count() > 1)
                                                    <span class="text-blue-600 dark:text-blue-400">+{{ $order->items->count() - 1 }} more</span>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Status -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $displayStatus = $order->getDisplayStatus();
                                            @endphp
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    {{ $displayStatus === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                                    {{ $displayStatus === 'processing' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                                    {{ $displayStatus === 'shipped' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200' : '' }}
                                                    {{ $displayStatus === 'awaiting_confirmation' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : '' }}
                                                    {{ $displayStatus === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                                    {{ $displayStatus === 'disputed' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                                    {{ $displayStatus === 'cancelled' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' : '' }}">
                                                @switch($displayStatus)
                                                    @case('awaiting_confirmation')
                                                        🚚 Awaiting Confirmation
                                                        @break
                                                    @case('completed')
                                                        ✅ Completed
                                                        @break
                                                    @case('disputed')
                                                        ⚠️ Disputed
                                                        @break
                                                    @default
                                                        {{ ucfirst($displayStatus) }}
                                                @endswitch
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
                                                   onclick="event.stopPropagation()"
                                                   class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 hover:underline">
                                                    View Details
                                                </a>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Expandable Items Row -->
                                    <tr id="items-order-{{ $order->id }}" class="hidden bg-gray-50 dark:bg-neutral-700/30">
                                        <td colspan="6" class="px-6 py-4">
                                            <div class="bg-white dark:bg-neutral-800 rounded-lg border border-gray-200 dark:border-gray-600 p-4">
                                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3 flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                                    </svg>
                                                    Order Items ({{ $order->items->count() }})
                                                </h4>
                                                
                                                <div class="space-y-3">
                                                    @foreach($order->items as $item)
                                                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-neutral-700 rounded-lg">
                                                            <div class="flex items-center space-x-3">
                                                                @php
                                                                    $featuredImage = $item->product->images->where('is_featured', true)->first();
                                                                    $firstImage = $featuredImage ?: $item->product->images->first();
                                                                @endphp
                                                                
                                                                <!-- Product Image -->
                                                                <div class="relative group cursor-pointer" onclick="window.location.href='{{ route('client.products.show', $item->product) }}'">
                                                                    @if($firstImage)
                                                                        <img src="{{ asset('storage/' . $firstImage->image_path) }}" 
                                                                             alt="{{ $item->product->name }}"
                                                                             class="w-12 h-12 object-cover rounded-lg border border-gray-200 dark:border-gray-600 transition-transform duration-200 group-hover:scale-105">
                                                                    @else
                                                                        <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                                                                            <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                                                            </svg>
                                                                        </div>
                                                                    @endif
                                                                    
                                                                    @if($firstImage)
                                                                        <!-- Hover Overlay -->
                                                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100 rounded-lg">
                                                                            <div class="bg-white dark:bg-gray-800 p-1 rounded-full shadow-sm">
                                                                                <svg class="w-3 h-3 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                                </svg>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </div>

                                                                <!-- Product Info -->
                                                                <div class="flex-1 min-w-0">
                                                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                                        <a href="{{ route('client.products.show', $item->product) }}" 
                                                                           class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                                                            {{ $item->product->name }}
                                                                        </a>
                                                                    </p>
                                                                    @if($item->product->sku)
                                                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                                                            SKU: {{ $item->product->sku }}
                                                                        </p>
                                                                    @endif
                                                                    @if($item->specifications)
                                                                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                                                            <span class="font-medium">Notes:</span> {{ Str::limit($item->specifications, 40) }}
                                                                        </p>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <!-- Quantity & Price -->
                                                            <div class="text-right">
                                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                                    Qty: {{ $item->quantity }}
                                                                </div>
                                                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                                                    @ Rp {{ number_format($item->price, 0, ',', '.') }}
                                                                </div>
                                                                <div class="text-sm font-semibold text-blue-600 dark:text-blue-400 mt-1">
                                                                    Rp {{ number_format($item->total, 0, ',', '.') }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <!-- Order Summary in Expanded View -->
                                                <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-600">
                                                    <div class="flex justify-between items-start">
                                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                                            @if($order->notes)
                                                                <div class="mb-1">
                                                                    <span class="font-medium">Notes:</span> {{ Str::limit($order->notes, 60) }}
                                                                </div>
                                                            @endif
                                                            <div>
                                                                <span class="font-medium">Delivery to:</span> {{ Str::limit($order->delivery_address, 50) }}
                                                            </div>
                                                        </div>
                                                        <div class="text-right">
                                                            <div class="text-lg font-bold text-gray-900 dark:text-white">
                                                                Total: Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Client Confirmation Actions -->
                                                    @if($order->canConfirmDelivery())
                                                        <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-700">
                                                            <div class="flex items-start space-x-4">
                                                                <div class="flex-shrink-0">
                                                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4-8-4m16 0v10l-8 4-8-4V7"></path>
                                                                    </svg>
                                                                </div>
                                                                <div class="flex-1">
                                                                    <h4 class="text-sm font-medium text-blue-900 dark:text-blue-300">
                                                                        Order Delivered - Confirmation Required
                                                                    </h4>
                                                                    <p class="mt-1 text-sm text-blue-700 dark:text-blue-400">
                                                                        Your order has been marked as delivered. Please confirm receipt or report any issues.
                                                                    </p>
                                                                    <div class="mt-3 flex flex-col sm:flex-row gap-2">
                                                                        <form method="POST" action="{{ route('client.orders.confirm-delivery', $order) }}" 
                                                                              onsubmit="return confirmAction('Are you sure you want to confirm delivery of this order?')" 
                                                                              class="inline">
                                                                            @csrf
                                                                            <button type="button" 
                                                                                    onclick="showConfirmDeliveryModal({{ $order->id }})"
                                                                                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                                </svg>
                                                                                Confirm Delivery
                                                                            </button>
                                                                        </form>
                                                                        <button type="button" 
                                                                                onclick="showDisputeModal({{ $order->id }})"
                                                                                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                                            </svg>
                                                                            Report Issue
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <!-- Display delivery confirmation status if already confirmed -->
                                                    @if($order->delivery_confirmed_by_client)
                                                        <div class="mt-4 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-700">
                                                            <div class="flex items-center">
                                                                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                </svg>
                                                                <div class="text-sm text-green-700 dark:text-green-300">
                                                                    <span class="font-medium">Delivery confirmed</span> on {{ $order->delivery_confirmed_at->format('M j, Y \a\t g:i A') }}
                                                                    @if($order->client_delivery_notes)
                                                                        <div class="mt-1 text-green-600 dark:text-green-400">
                                                                            Notes: {{ $order->client_delivery_notes }}
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <!-- Display dispute status if disputed -->
                                                    @if($order->delivery_disputed)
                                                        <div class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-700">
                                                            <div class="flex items-center">
                                                                <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                                </svg>
                                                                <div class="text-sm text-red-700 dark:text-red-300">
                                                                    <span class="font-medium">Delivery dispute reported</span> on {{ $order->dispute_reported_at->format('M j, Y \a\t g:i A') }}
                                                                    @if($order->dispute_reason)
                                                                        <div class="mt-1 text-red-600 dark:text-red-400">
                                                                            Reason: {{ $order->dispute_reason }}
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
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

    <!-- Confirm Delivery Modal -->
    <div id="confirmDeliveryModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" onclick="closeConfirmModal()"></div>
            
            <div class="inline-block align-bottom bg-white dark:bg-neutral-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="confirmDeliveryForm" method="POST">
                    @csrf
                    <div class="bg-white dark:bg-neutral-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900/30 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                    Confirm Delivery
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Are you sure you want to confirm that you have received this order? This action cannot be undone.
                                    </p>
                                    <div class="mt-4">
                                        <label for="delivery_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Delivery Notes (Optional)
                                        </label>
                                        <textarea name="notes" id="delivery_notes" rows="3" 
                                                  placeholder="Add any comments about the delivery..." 
                                                  class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-neutral-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Confirm Delivery
                        </button>
                        <button type="button" onclick="closeConfirmModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Dispute Modal -->
    <div id="disputeModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" onclick="closeDisputeModal()"></div>
            
            <div class="inline-block align-bottom bg-white dark:bg-neutral-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="disputeForm" method="POST">
                    @csrf
                    <div class="bg-white dark:bg-neutral-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                    Report Delivery Issue
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Please describe the issue with your delivery. Our team will review and contact you.
                                    </p>
                                    <div class="mt-4">
                                        <label for="dispute_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Issue Description <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="reason" id="dispute_reason" rows="4" required
                                                  placeholder="Please describe the issue in detail (e.g., damaged items, wrong items, not delivered, etc.)" 
                                                  class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-neutral-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Report Issue
                        </button>
                        <button type="button" onclick="closeDisputeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* Prevent layout shifts on table row hover */
        .order-table-container {
            overflow: visible !important;
        }
        
        .expandable-row {
            transition: all 0.2s ease;
        }
        
        .expandable-row:hover {
            transform: translateZ(0); /* Force hardware acceleration */
            position: relative;
            z-index: 1;
        }
        
        /* Smooth expand/collapse animations */
        .expandable-content {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        
        /* Chevron rotation animation */
        .chevron-icon {
            transition: transform 0.2s ease;
        }
    </style>

    @push('scripts')
    <script>
        // Simple search functionality and expandable orders
        document.addEventListener('DOMContentLoaded', function() {
            // Search field auto-submit on Enter
            const searchField = document.querySelector('input[name="search"]');
            if (searchField) {
                searchField.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        this.closest('form').submit();
                    }
                });
            }

            // Add active/hover effects to tabs
            const tabs = document.querySelectorAll('nav[aria-label="Tabs"] a');
            tabs.forEach(tab => {
                tab.addEventListener('mouseover', function() {
                    if (!this.classList.contains('border-blue-500') && !this.classList.contains('border-green-500')) {
                        this.classList.add('border-gray-300', 'dark:border-gray-600');
                    }
                });
                
                tab.addEventListener('mouseout', function() {
                    if (!this.classList.contains('border-blue-500') && !this.classList.contains('border-green-500')) {
                        this.classList.remove('border-gray-300', 'dark:border-gray-600');
                    }
                });
            });
        });

        // Toggle order items expand/collapse
        function toggleOrderItems(orderId) {
            const itemsRow = document.getElementById('items-' + orderId);
            const chevron = document.getElementById('chevron-' + orderId);
            
            if (itemsRow.classList.contains('hidden')) {
                // Expand
                itemsRow.classList.remove('hidden');
                chevron.style.transform = 'rotate(90deg)';
                
                // Add smooth animation
                itemsRow.style.opacity = '0';
                itemsRow.style.transform = 'translateY(-10px)';
                
                setTimeout(() => {
                    itemsRow.style.transition = 'all 0.3s ease-out';
                    itemsRow.style.opacity = '1';
                    itemsRow.style.transform = 'translateY(0)';
                }, 10);
                
            } else {
                // Collapse
                itemsRow.style.transition = 'all 0.2s ease-in';
                itemsRow.style.opacity = '0';
                itemsRow.style.transform = 'translateY(-10px)';
                chevron.style.transform = 'rotate(0deg)';
                
                setTimeout(() => {
                    itemsRow.classList.add('hidden');
                    itemsRow.style.transition = '';
                    itemsRow.style.opacity = '';
                    itemsRow.style.transform = '';
                }, 200);
            }
        }

        // Add visual feedback on hover for expandable rows
        document.addEventListener('DOMContentLoaded', function() {
            const expandableRows = document.querySelectorAll('tr[onclick*="toggleOrderItems"]');
            expandableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    // Remove scaling that causes scroll issues, just add subtle shadow
                    this.style.boxShadow = '0 2px 12px rgba(0,0,0,0.15)';
                    this.style.backgroundColor = 'rgba(59, 130, 246, 0.05)'; // subtle blue tint
                });
                
                row.addEventListener('mouseleave', function() {
                    this.style.boxShadow = 'none';
                    this.style.backgroundColor = '';
                });
            });
        });

        // Modal functions for delivery confirmation
        function showConfirmDeliveryModal(orderId) {
            const modal = document.getElementById('confirmDeliveryModal');
            const form = document.getElementById('confirmDeliveryForm');
            form.action = `/client/orders/${orderId}/confirm-delivery`;
            modal.classList.remove('hidden');
            
            // Focus on textarea after modal animation
            setTimeout(() => {
                document.getElementById('delivery_notes').focus();
            }, 150);
        }

        function closeConfirmModal() {
            const modal = document.getElementById('confirmDeliveryModal');
            modal.classList.add('hidden');
            document.getElementById('delivery_notes').value = '';
        }

        function showDisputeModal(orderId) {
            const modal = document.getElementById('disputeModal');
            const form = document.getElementById('disputeForm');
            form.action = `/client/orders/${orderId}/dispute-delivery`;
            modal.classList.remove('hidden');
            
            // Focus on textarea after modal animation
            setTimeout(() => {
                document.getElementById('dispute_reason').focus();
            }, 150);
        }

        function closeDisputeModal() {
            const modal = document.getElementById('disputeModal');
            modal.classList.add('hidden');
            document.getElementById('dispute_reason').value = '';
        }

        // Handle form submissions with loading states
        document.addEventListener('DOMContentLoaded', function() {
            // Confirm delivery form
            document.getElementById('confirmDeliveryForm').addEventListener('submit', function(e) {
                const submitButton = this.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Confirming...
                `;
            });

            // Dispute form  
            document.getElementById('disputeForm').addEventListener('submit', function(e) {
                const submitButton = this.querySelector('button[type="submit"]');
                const textarea = this.querySelector('textarea[name="reason"]');
                
                if (!textarea.value.trim()) {
                    e.preventDefault();
                    textarea.focus();
                    showToast('Please describe the issue before submitting.', 'error');
                    return;
                }
                
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Reporting...
                `;
            });

            // Close modals on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeConfirmModal();
                    closeDisputeModal();
                }
            });
        });

        // Toast notification for actions
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-600' : type === 'error' ? 'bg-red-600' : 'bg-blue-600';
            toast.className = `fixed top-4 right-4 z-50 px-4 py-2 ${bgColor} text-white text-sm rounded-lg shadow-lg transform transition-all duration-300 translate-x-full opacity-0`;
            toast.innerHTML = message;
            
            document.body.appendChild(toast);
            
            // Animate in
            setTimeout(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
            }, 100);
            
            // Remove after 3 seconds
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => document.body.removeChild(toast), 300);
            }, 3000);
        }
    </script>
    @endpush
</x-layouts.client>
