{{-- resources/views/client/orders/show.blade.php --}}
<x-layouts.client>
    <x-slot name="title">Order #{{ $order->order_number }}</x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('client.orders.index') }}" 
                   class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Order #{{ $order->order_number }}</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Placed on {{ $order->created_at->format('F j, Y \a\t g:i A') }}
                    </p>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex space-x-3">
                
                <a href="{{ route('client.messages.create', ['order_id' => $order->id]) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a9.863 9.863 0 01-4.906-1.285L3 21l2.085-5.104A9.863 9.863 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"></path>
                    </svg>
                    Contact Support
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Order Status -->
                <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Order Status</h3>
                    </div>
                    
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                {{ $order->status === 'processing' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                {{ $order->status === 'shipped' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200' : '' }}
                                {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                            
                        </div>

                        <!-- Status Timeline -->
                        <div class="flex items-center space-x-4 text-sm">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                                <span class="text-gray-600 dark:text-gray-400">Ordered</span>
                            </div>
                            
                            <div class="flex-1 h-px bg-gray-200 dark:bg-gray-600
                                {{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'bg-green-500' : '' }}"></div>
                            
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 rounded-full 
                                    {{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                                <span class="text-gray-600 dark:text-gray-400">Processing</span>
                            </div>
                            
                            <div class="flex-1 h-px bg-gray-200 dark:bg-gray-600
                                {{ in_array($order->status, ['shipped', 'delivered']) ? 'bg-green-500' : '' }}"></div>
                            
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 rounded-full 
                                    {{ in_array($order->status, ['shipped', 'delivered']) ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                                <span class="text-gray-600 dark:text-gray-400">Shipped</span>
                            </div>
                            
                            <div class="flex-1 h-px bg-gray-200 dark:bg-gray-600
                                {{ $order->status === 'delivered' ? 'bg-green-500' : '' }}"></div>
                            
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 rounded-full 
                                    {{ $order->status === 'delivered' ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                                <span class="text-gray-600 dark:text-gray-400">Delivered</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            Order Items ({{ $order->items->count() }})
                        </h3>
                    </div>
                    
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($order->items as $item)
                            <div class="p-6">
                                <div class="flex items-start space-x-4">
                                    
                                    <!-- Product Image -->
                                    <div class="flex-shrink-0">
                                        @if($item->product->image)
                                            <img src="{{ asset('storage/' . $item->product->image) }}" 
                                                 alt="{{ $item->product->name }}"
                                                 class="w-20 h-20 object-cover rounded-lg border border-gray-200 dark:border-gray-600">
                                        @else
                                            <div class="w-20 h-20 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Product Details -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h4 class="text-base font-medium text-gray-900 dark:text-white">
                                                    {{ $item->product->name }}
                                                </h4>
                                                
                                                @if($item->product->sku)
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                        SKU: {{ $item->product->sku }}
                                                    </p>
                                                @endif

                                                @if($item->specifications)
                                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">
                                                        <span class="font-medium">Specifications:</span> {{ $item->specifications }}
                                                    </p>
                                                @endif

                                                <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                                    <span>Quantity: {{ $item->quantity }}</span>
                                                    @if($item->unit_price > 0)
                                                        <span>Unit Price: Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Item Total -->
                                            <div class="text-right">
                                                <div class="text-lg font-medium text-gray-900 dark:text-white">
                                                    Rp {{ number_format($item->total_price, 0, ',', '.') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Delivery Information -->
                <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Delivery Information</h3>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Delivery Address
                            </label>
                            <p class="text-sm text-gray-900 dark:text-white">
                                {{ $order->delivery_address }}
                            </p>
                        </div>
                        
                        @if($order->needed_date)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Required Date
                                </label>
                                <p class="text-sm text-gray-900 dark:text-white">
                                    {{ $order->needed_date ? $order->needed_date->format('F j, Y') : 'Not specified' }}
                                </p>
                            </div>
                        @endif
                        
                        @if($order->notes)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Additional Notes
                                </label>
                                <p class="text-sm text-gray-900 dark:text-white">
                                    {{ $order->notes }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                
                <!-- Order Summary -->
                <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden sticky top-6">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Order Summary</h3>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Order Number</span>
                                <span class="font-medium text-gray-900 dark:text-white">#{{ $order->order_number }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Order Date</span>
                                <span class="text-gray-900 dark:text-white">{{ $order->created_at->format('M j, Y') }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Items</span>
                                <span class="text-gray-900 dark:text-white">{{ $order->items->count() }} product(s)</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Total Quantity</span>
                                <span class="text-gray-900 dark:text-white">{{ $order->items->sum('quantity') }}</span>
                            </div>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-600">

                        <!-- Pricing -->
                        <div class="space-y-2 text-sm">
                            @if($order->total_amount > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                                    <span class="text-gray-900 dark:text-white">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Delivery</span>
                                    <span class="text-gray-900 dark:text-white">
                                        {{ $order->delivery_fee > 0 ? 'Rp ' . number_format($order->delivery_fee, 0, ',', '.') : 'TBD' }}
                                    </span>
                                </div>
                            @endif

                        </div>

                        <hr class="border-gray-200 dark:border-gray-600">

                        <!-- Total -->
                        <div class="flex justify-between">
                            <span class="text-base font-medium text-gray-900 dark:text-white">Total</span>
                            <span class="text-lg font-bold text-gray-900 dark:text-white">
                                Rp {{ number_format($order->total_amount + ($order->delivery_fee ?? 0), 0, ',', '.') }}
                            </span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-3 pt-4">
                            
                            <a href="{{ route('client.messages.create', ['order_id' => $order->id]) }}" 
                               class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors text-center">
                                Contact Support
                            </a>
                            
                            <a href="{{ route('client.orders.index') }}" 
                               class="block w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition-colors text-center">
                                Back to Orders
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Quick Actions</h3>
                    </div>
                    
                    <div class="p-6 space-y-3">
                        <a href="{{ route('client.products.index') }}" 
                           class="flex items-center px-4 py-2 text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Browse Products
                        </a>
                        
                        
                        <a href="{{ route('client.orders.index') }}" 
                           class="flex items-center px-4 py-2 text-sm text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900/20 rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            View All Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.client>