{{-- resources/views/admin/orders/show.blade.php --}}
<x-layouts.admin title="Order #{{ $order->order_number }}">
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.orders.index') }}" 
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
            @if($order->needs_negotiation && $order->negotiation_status === 'pending')
                <a href="{{ route('admin.orders.negotiation', $order) }}" 
                   class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    Review Negotiation
                </a>
            @endif
            
            @if($order->status === 'delivered' || $order->delivery_disputed || $order->delivery_confirmed_by_client)
                <a href="{{ route('admin.orders.delivery', $order) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4-8-4m16 0v10l-8 4-8-4V7"></path>
                    </svg>
                    Manage Delivery
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            
            @if($order->needs_negotiation)
                <!-- Negotiation Status -->
                <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-700 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-orange-200 dark:border-orange-700">
                        <h3 class="text-lg font-medium text-orange-900 dark:text-orange-100">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            Price Negotiation Status
                        </h3>
                    </div>
                    
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $order->negotiation_status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                {{ $order->negotiation_status === 'in_progress' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                {{ $order->negotiation_status === 'accepted' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                {{ $order->negotiation_status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}">
                                {{ ucfirst($order->negotiation_status) }}
                            </span>
                            
                            @if($order->negotiation_status === 'pending')
                                <a href="{{ route('admin.orders.negotiation', $order) }}" 
                                   class="inline-flex items-center px-3 py-1 text-sm bg-orange-600 hover:bg-orange-700 text-white rounded-md transition-colors">
                                    Review & Respond
                                </a>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 gap-6 text-sm">
                            <div>
                                <span class="font-medium text-gray-900 dark:text-white block mb-1">Original Total:</span>
                                <span class="text-gray-700 dark:text-gray-300">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </span>
                            </div>
                            
                            @if($order->requested_total)
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-white block mb-1">Requested Total:</span>
                                    <span class="text-orange-600 dark:text-orange-400 font-medium">
                                        Rp {{ number_format($order->requested_total, 0, ',', '.') }}
                                    </span>
                                    <span class="text-gray-500 dark:text-gray-400">
                                        ({{ $order->requested_total < $order->total_amount ? 'Savings' : 'Increase' }}: 
                                        Rp {{ number_format(abs($order->total_amount - $order->requested_total), 0, ',', '.') }})
                                    </span>
                                </div>
                            @endif
                            
                            @if($order->negotiation_message)
                                <div class="col-span-2">
                                    <span class="font-medium text-gray-900 dark:text-white block mb-2">Client Message:</span>
                                    <p class="text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 p-3 rounded-lg border">
                                        {{ $order->negotiation_message }}
                                    </p>
                                </div>
                            @endif
                            
                            <div>
                                <span class="font-medium text-gray-900 dark:text-white block mb-1">Requested on:</span>
                                <span class="text-gray-700 dark:text-gray-300">
                                    {{ $order->negotiation_requested_at?->format('F j, Y \a\t g:i A') }}
                                </span>
                            </div>
                            
                            @if($order->negotiation_responded_at)
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-white block mb-1">Responded on:</span>
                                    <span class="text-gray-700 dark:text-gray-300">
                                        {{ $order->negotiation_responded_at->format('F j, Y \a\t g:i A') }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Delivery Confirmation Status -->
            @if($order->status === 'delivered' || $order->delivery_disputed || $order->delivery_confirmed_by_client)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4-8-4m16 0v10l-8 4-8-4V7"></path>
                            </svg>
                            Delivery Status
                        </h3>
                    </div>
                    
                    <div class="p-6">
                        @php
                            $displayStatus = $order->getDisplayStatus();
                        @endphp

                        @if($displayStatus === 'awaiting_confirmation')
                            <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-700 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-sm font-medium text-orange-800 dark:text-orange-200">
                                            🚚 Awaiting Client Confirmation
                                        </span>
                                    </div>
                                    <a href="{{ route('admin.orders.delivery', $order) }}" 
                                       class="text-sm text-orange-600 hover:text-orange-700 dark:text-orange-400 dark:hover:text-orange-300">
                                        Manage →
                                    </a>
                                </div>
                                <p class="text-sm text-orange-700 dark:text-orange-300 mt-1">
                                    Order marked as delivered, waiting for client to confirm receipt.
                                </p>
                            </div>

                        @elseif($displayStatus === 'completed')
                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-sm font-medium text-green-800 dark:text-green-200">
                                            ✅ Delivery Confirmed
                                        </span>
                                    </div>
                                    <span class="text-sm text-green-600 dark:text-green-400">
                                        {{ $order->delivery_confirmed_at->format('M j, Y') }}
                                    </span>
                                </div>
                                <p class="text-sm text-green-700 dark:text-green-300 mt-1">
                                    Client confirmed delivery on {{ $order->delivery_confirmed_at->format('F j, Y \a\t g:i A') }}
                                </p>
                                @if($order->client_delivery_notes)
                                    <div class="mt-2 p-2 bg-green-100 dark:bg-green-900/30 rounded border border-green-200 dark:border-green-800">
                                        <p class="text-xs text-green-700 dark:text-green-300">
                                            <strong>Client Notes:</strong> {{ $order->client_delivery_notes }}
                                        </p>
                                    </div>
                                @endif
                            </div>

                        @elseif($displayStatus === 'disputed')
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                        <span class="text-sm font-medium text-red-800 dark:text-red-200">
                                            ⚠️ Delivery Disputed
                                        </span>
                                    </div>
                                    <a href="{{ route('admin.orders.delivery', $order) }}" 
                                       class="text-sm text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                        Resolve →
                                    </a>
                                </div>
                                <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                                    Client reported delivery issue on {{ $order->dispute_reported_at->format('F j, Y \a\t g:i A') }}
                                </p>
                                <div class="mt-2 p-2 bg-red-100 dark:bg-red-900/30 rounded border border-red-200 dark:border-red-800">
                                    <p class="text-xs text-red-700 dark:text-red-300">
                                        <strong>Issue:</strong> {{ $order->dispute_reason }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Order Status -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Order Status</h3>
                </div>
                
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                            {{ $order->status === 'confirmed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                            {{ $order->status === 'processing' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200' : '' }}
                            {{ $order->status === 'ready' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                            {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                            {{ $order->status === 'completed' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' : '' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                        
                        <!-- Status Update Form -->
                        <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="flex items-center space-x-2">
                            @csrf
                            @method('PUT')
                            <select name="status" class="text-sm border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}>Ready</option>
                                <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                            <button type="submit" class="px-3 py-1 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors">
                                Update
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
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
                                                <span>Unit Price: Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                            </div>
                                            
                                            @if($order->needs_negotiation && ($item->requested_unit_price || $item->price_justification))
                                                <div class="mt-3 p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                                                    @if($item->requested_unit_price)
                                                        <div class="text-sm">
                                                            <span class="font-medium text-orange-900 dark:text-orange-100">Requested Price:</span>
                                                            <span class="text-orange-700 dark:text-orange-300">
                                                                Rp {{ number_format($item->requested_unit_price, 0, ',', '.') }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                    @if($item->price_justification)
                                                        <div class="mt-1 text-sm">
                                                            <span class="font-medium text-orange-900 dark:text-orange-100">Justification:</span>
                                                            <p class="text-orange-700 dark:text-orange-300">{{ $item->price_justification }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Item Total -->
                                        <div class="text-right">
                                            <div class="text-lg font-medium text-gray-900 dark:text-white">
                                                Rp {{ number_format($item->total, 0, ',', '.') }}
                                            </div>
                                            @if($order->needs_negotiation && $item->requested_total_price)
                                                <div class="text-sm text-orange-600 dark:text-orange-400">
                                                    Requested: Rp {{ number_format($item->requested_total_price, 0, ',', '.') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Delivery Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
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
                                {{ $order->needed_date->format('F j, Y') }}
                            </p>
                        </div>
                    @endif
                    
                    @if($order->notes)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Client Notes
                            </label>
                            <p class="text-sm text-gray-900 dark:text-white">
                                {{ $order->notes }}
                            </p>
                        </div>
                    @endif
                    
                    @if($order->admin_notes)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Admin Notes
                            </label>
                            <p class="text-sm text-gray-900 dark:text-white">
                                {{ $order->admin_notes }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Order Summary -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
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

                    <!-- Total -->
                    <div class="flex justify-between">
                        <span class="text-base font-medium text-gray-900 dark:text-white">Total</span>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Client Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Client Information</h3>
                </div>
                
                <div class="p-6 space-y-3">
                    <div class="text-sm">
                        <span class="font-medium text-gray-700 dark:text-gray-300 block">Name:</span>
                        <span class="text-gray-900 dark:text-white">{{ $order->client->name ?? $order->client_name }}</span>
                    </div>
                    
                    <div class="text-sm">
                        <span class="font-medium text-gray-700 dark:text-gray-300 block">Email:</span>
                        <span class="text-gray-900 dark:text-white">{{ $order->client->email ?? $order->client_email }}</span>
                    </div>
                    
                    @if($order->client_phone)
                        <div class="text-sm">
                            <span class="font-medium text-gray-700 dark:text-gray-300 block">Phone:</span>
                            <span class="text-gray-900 dark:text-white">{{ $order->client_phone }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Payment Status</h3>
                </div>
                
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Status:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $order->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                            {{ $order->payment_status === 'proof_uploaded' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                            {{ $order->payment_status === 'verified' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                            {{ $order->payment_status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                        </span>
                    </div>
                    
                    @if($order->payment_method)
                        <div class="text-sm">
                            <span class="font-medium text-gray-700 dark:text-gray-300 block">Payment Method:</span>
                            <span class="text-gray-900 dark:text-white">{{ $order->payment_method }}</span>
                        </div>
                    @endif
                    
                    @if($order->payment_uploaded_at)
                        <div class="text-sm">
                            <span class="font-medium text-gray-700 dark:text-gray-300 block">Uploaded:</span>
                            <span class="text-gray-900 dark:text-white">{{ $order->payment_uploaded_at->format('M j, Y g:i A') }}</span>
                        </div>
                    @endif
                    
                    @if($order->payment_verified_at)
                        <div class="text-sm">
                            <span class="font-medium text-gray-700 dark:text-gray-300 block">Verified:</span>
                            <span class="text-gray-900 dark:text-white">{{ $order->payment_verified_at->format('M j, Y g:i A') }}</span>
                        </div>
                    @endif
                    
                    <!-- Action Buttons -->
                    <div class="pt-4 space-y-2">
                        @if($order->payment_status === 'proof_uploaded')
                            <a href="{{ route('admin.orders.payment', $order) }}" 
                               class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center font-medium py-2 px-4 rounded-lg transition-colors">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Review Payment
                            </a>
                        @elseif($order->hasPaymentProof())
                            <a href="{{ route('admin.orders.payment', $order) }}" 
                               class="block w-full bg-gray-600 hover:bg-gray-700 text-white text-center font-medium py-2 px-4 rounded-lg transition-colors">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                View Payment
                            </a>
                        @endif
                        
                        @if($order->needs_negotiation && in_array($order->negotiation_status, ['pending', 'in_progress']))
                            <a href="{{ route('admin.orders.negotiation', $order) }}" 
                               class="block w-full bg-orange-600 hover:bg-orange-700 text-white text-center font-medium py-2 px-4 rounded-lg transition-colors">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                Manage Negotiation
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-layouts.admin>