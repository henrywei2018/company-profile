{{-- resources/views/admin/orders/negotiation.blade.php --}}
<x-layouts.admin title="Negotiation Review - Order #{{ $order->order_number }}">
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.orders.show', $order) }}" 
               class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Negotiation Review</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Order #{{ $order->order_number }} • Review client's price negotiation request
                </p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.orders.negotiation.respond', $order) }}" method="POST" id="negotiationForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Client Request Information -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl p-6">
                    <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100 mb-4">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Client's Negotiation Request
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="font-medium text-blue-900 dark:text-blue-100 block mb-1">Original Total:</span>
                                <span class="text-blue-700 dark:text-blue-300 text-lg font-semibold">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </span>
                            </div>
                            
                            <div>
                                <span class="font-medium text-blue-900 dark:text-blue-100 block mb-1">Requested Total:</span>
                                <span class="text-orange-600 dark:text-orange-400 text-lg font-semibold">
                                    Rp {{ number_format($order->requested_total, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                        
                        <div>
                            <span class="font-medium text-blue-900 dark:text-blue-100 block mb-1">Price Difference:</span>
                            <span class="{{ $order->requested_total < $order->total_amount ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }} text-lg font-semibold">
                                {{ $order->requested_total < $order->total_amount ? '-' : '+' }}Rp {{ number_format(abs($order->total_amount - $order->requested_total), 0, ',', '.') }}
                                ({{ number_format((abs($order->total_amount - $order->requested_total) / $order->total_amount) * 100, 1) }}%)
                            </span>
                        </div>
                        
                        @if($order->negotiation_message)
                            <div>
                                <span class="font-medium text-blue-900 dark:text-blue-100 block mb-2">Client's Message:</span>
                                <p class="text-blue-800 dark:text-blue-200 bg-white dark:bg-blue-800/30 p-4 rounded-lg border border-blue-200 dark:border-blue-600">
                                    {{ $order->negotiation_message }}
                                </p>
                            </div>
                        @endif
                        
                        <div>
                            <span class="font-medium text-blue-900 dark:text-blue-100 block mb-1">Requested on:</span>
                            <span class="text-blue-700 dark:text-blue-300">
                                {{ $order->negotiation_requested_at->format('F j, Y \a\t g:i A') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Admin Response -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a9.863 9.863 0 01-4.906-1.285L3 21l2.085-5.104A9.863 9.863 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"></path>
                        </svg>
                        Your Response
                    </h3>
                    
                    <div class="space-y-4">
                        <!-- Action Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Response Action
                            </label>
                            <div class="grid grid-cols-3 gap-3">
                                <label class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <input type="radio" name="action" value="accept" class="text-green-600 focus:ring-green-500" required>
                                    <div class="ml-3">
                                        <span class="block text-sm font-medium text-green-700 dark:text-green-300">Accept</span>
                                        <span class="block text-xs text-green-600 dark:text-green-400">Accept client's prices</span>
                                    </div>
                                </label>
                                
                                <label class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <input type="radio" name="action" value="reject" class="text-red-600 focus:ring-red-500" required>
                                    <div class="ml-3">
                                        <span class="block text-sm font-medium text-red-700 dark:text-red-300">Reject</span>
                                        <span class="block text-xs text-red-600 dark:text-red-400">Reject the negotiation</span>
                                    </div>
                                </label>
                                
                                <label class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <input type="radio" name="action" value="counter" class="text-blue-600 focus:ring-blue-500" required>
                                    <div class="ml-3">
                                        <span class="block text-sm font-medium text-blue-700 dark:text-blue-300">Counter</span>
                                        <span class="block text-xs text-blue-600 dark:text-blue-400">Make counter offer</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Response Message -->
                        <div>
                            <label for="admin_response" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Response Message
                            </label>
                            <textarea name="admin_response" id="admin_response" rows="4" 
                                    class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                    placeholder="Explain your response to the client..."
                                    required>{{ old('admin_response') }}</textarea>
                            @error('admin_response')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Price Review (only show for accept/counter actions) -->
                <div id="price-review" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden" style="display: none;">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            Review & Set Final Prices
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Set the final prices for each item
                        </p>
                    </div>
                    
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($order->items as $index => $item)
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

                                    <!-- Product Details & Pricing -->
                                    <div class="flex-1 min-w-0">
                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                            <!-- Product Info -->
                                            <div>
                                                <h4 class="text-base font-medium text-gray-900 dark:text-white">
                                                    {{ $item->product->name }}
                                                </h4>
                                                
                                                @if($item->product->sku)
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                        SKU: {{ $item->product->sku }}
                                                    </p>
                                                @endif

                                                <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                                    <span class="font-medium">Quantity:</span> {{ $item->quantity }}
                                                </div>

                                                <div class="mt-2 space-y-1 text-sm">
                                                    <div>
                                                        <span class="font-medium text-gray-700 dark:text-gray-300">Current Price:</span>
                                                        <span class="text-gray-900 dark:text-white">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                                    </div>
                                                    @if($item->requested_unit_price && $item->requested_unit_price != $item->price)
                                                        <div>
                                                            <span class="font-medium text-orange-700 dark:text-orange-300">Client's Request:</span>
                                                            <span class="text-orange-600 dark:text-orange-400">Rp {{ number_format($item->requested_unit_price, 0, ',', '.') }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                @if($item->price_justification)
                                                    <div class="mt-3 p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                                                        <span class="font-medium text-orange-900 dark:text-orange-100 text-sm block mb-1">Client's Justification:</span>
                                                        <p class="text-orange-700 dark:text-orange-300 text-sm">{{ $item->price_justification }}</p>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Final Price Setting -->
                                            <div class="space-y-3">
                                                <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                                                
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                        Final Price (per unit)
                                                    </label>
                                                    <div class="relative">
                                                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">Rp</span>
                                                        <input type="number" 
                                                               name="items[{{ $index }}][final_unit_price]" 
                                                               id="final_price_{{ $index }}"
                                                               class="block w-full pl-8 pr-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:border-green-500 focus:ring-2 focus:ring-green-500/20" 
                                                               placeholder="0"
                                                               min="0"
                                                               step="0.01"
                                                               value="{{ old('items.'.$index.'.final_unit_price', $item->price) }}">
                                                    </div>
                                                </div>

                                                <!-- Price Suggestion Buttons -->
                                                <div class="flex gap-2">
                                                    <button type="button" 
                                                            class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-md transition-colors"
                                                            onclick="setPrice({{ $index }}, {{ $item->price }})">
                                                        Use Original
                                                    </button>
                                                    @if($item->requested_unit_price)
                                                        <button type="button" 
                                                                class="px-3 py-1 text-xs bg-orange-100 hover:bg-orange-200 dark:bg-orange-900 dark:hover:bg-orange-800 text-orange-700 dark:text-orange-300 rounded-md transition-colors"
                                                                onclick="setPrice({{ $index }}, {{ $item->requested_unit_price }})">
                                                            Accept Request
                                                        </button>
                                                        <button type="button" 
                                                                class="px-3 py-1 text-xs bg-blue-100 hover:bg-blue-200 dark:bg-blue-900 dark:hover:bg-blue-800 text-blue-700 dark:text-blue-300 rounded-md transition-colors"
                                                                onclick="setPrice({{ $index }}, {{ ($item->price + $item->requested_unit_price) / 2 }})">
                                                            Midpoint
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                
                <!-- Summary -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden sticky top-6">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Negotiation Summary</h3>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Original Total</span>
                                <span class="font-medium text-gray-900 dark:text-white" id="originalTotal">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Client Request</span>
                                <span class="font-medium text-orange-600 dark:text-orange-400" id="requestedTotal">
                                    Rp {{ number_format($order->requested_total, 0, ',', '.') }}
                                </span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Final Total</span>
                                <span class="font-medium text-green-600 dark:text-green-400" id="finalTotal">
                                    Rp {{ number_format($order->requested_total, 0, ',', '.') }}
                                </span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Difference</span>
                                <span class="font-medium text-blue-600 dark:text-blue-400" id="difference">
                                    Rp 0
                                </span>
                            </div>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-600">

                        <!-- Submit Button -->
                        <div class="space-y-3">
                            <button type="submit" 
                                    class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Submit Response
                            </button>
                            
                            <a href="{{ route('admin.orders.show', $order) }}" 
                               class="block w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition-colors text-center">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // Show/hide price review based on action selection
    document.querySelectorAll('input[name="action"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const priceReview = document.getElementById('price-review');
            if (this.value === 'accept' || this.value === 'counter') {
                priceReview.style.display = 'block';
            } else {
                priceReview.style.display = 'none';
            }
            updateTotals();
        });
    });

    // Set price for specific item
    function setPrice(index, price) {
        document.getElementById('final_price_' + index).value = price;
        updateTotals();
    }

    // Calculate totals dynamically
    function updateTotals() {
        const originalTotal = {{ $order->total_amount }};
        const requestedTotal = {{ $order->requested_total }};
        let finalTotal = 0;
        
        // Get all final price inputs
        const priceInputs = document.querySelectorAll('input[name*="[final_unit_price]"]');
        const quantities = {{ json_encode($order->items->pluck('quantity')->toArray()) }};
        
        priceInputs.forEach((input, index) => {
            const price = parseFloat(input.value) || 0;
            const quantity = quantities[index];
            finalTotal += price * quantity;
        });
        
        const difference = finalTotal - originalTotal;
        
        // Update display
        document.getElementById('finalTotal').textContent = 
            'Rp ' + finalTotal.toLocaleString('id-ID');
        
        const diffEl = document.getElementById('difference');
        diffEl.textContent = 
            (difference >= 0 ? '+' : '') + 'Rp ' + Math.abs(difference).toLocaleString('id-ID');
            
        // Change difference color based on positive/negative
        if (difference >= 0) {
            diffEl.className = 'font-medium text-red-600 dark:text-red-400';
        } else {
            diffEl.className = 'font-medium text-green-600 dark:text-green-400';
        }
    }

    // Add event listeners to all price inputs
    document.addEventListener('DOMContentLoaded', function() {
        const priceInputs = document.querySelectorAll('input[name*="[final_unit_price]"]');
        priceInputs.forEach(input => {
            input.addEventListener('input', updateTotals);
        });
        
        // Initial calculation
        updateTotals();
    });
</script>
</x-layouts.admin>