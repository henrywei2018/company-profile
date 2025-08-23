{{-- resources/views/client/orders/negotiate.blade.php --}}
<x-layouts.client>
    <x-slot name="title">Price Negotiation - Order #{{ $order->order_number }}</x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('client.orders.show', $order) }}" 
                   class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        @if($order->negotiation_status === 'in_progress')
                            Counter Negotiation
                        @else
                            Price Negotiation
                        @endif
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Order #{{ $order->order_number }} • 
                        @if($order->negotiation_status === 'in_progress')
                            Respond to admin's counter-offer
                        @else
                            Request better pricing
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <form action="{{ route('client.orders.negotiate.submit', $order) }}" method="POST" id="negotiationForm">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    
                    @if($order->negotiation_status === 'in_progress')
                        <!-- Admin's Counter-Offer Notice -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl p-6">
                            <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100 mb-4">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Admin's Counter-Offer
                            </h3>
                            
                            <div class="space-y-4">
                                <p class="text-blue-800 dark:text-blue-200">
                                    The admin has responded to your negotiation request with adjusted prices. You can either:
                                </p>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-4">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <span class="font-medium text-green-900 dark:text-green-100">Accept</span>
                                        </div>
                                        <p class="text-sm text-green-800 dark:text-green-200">
                                            Accept the admin's prices and complete the negotiation
                                        </p>
                                        <form action="{{ route('client.orders.negotiate.accept', $order) }}" method="POST" class="mt-3"
                                              onsubmit="return confirm('Accept admin\'s offer? This will complete the negotiation.')">
                                            @csrf
                                            <button type="submit" class="w-full px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-md transition-colors">
                                                Accept Offer
                                            </button>
                                        </form>
                                    </div>
                                    
                                    <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-700 rounded-lg p-4">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                            </svg>
                                            <span class="font-medium text-orange-900 dark:text-orange-100">Counter</span>
                                        </div>
                                        <p class="text-sm text-orange-800 dark:text-orange-200">
                                            Make a counter-offer with your preferred prices
                                        </p>
                                        <p class="text-xs text-orange-700 dark:text-orange-300 mt-2">
                                            Continue below to adjust prices
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Negotiation Message -->
                    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a9.863 9.863 0 01-4.906-1.285L3 21l2.085-5.104A9.863 9.863 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"></path>
                            </svg>
                            Your Negotiation Message
                        </h3>
                        
                        <div>
                            <label for="negotiation_message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Explain why you're requesting price negotiation
                            </label>
                            <textarea name="negotiation_message" id="negotiation_message" rows="4" 
                                    class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-neutral-900 px-3 py-2 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                    placeholder="Please explain your reasoning for price negotiation (e.g., bulk purchase, long-term partnership, competitive pricing, etc.)"
                                    required>{{ old('negotiation_message') }}</textarea>
                            @error('negotiation_message')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Order Items with Price Negotiation -->
                    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                Negotiate Item Prices ({{ $order->items->count() }} items)
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Adjust the prices you'd like to pay for each item
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
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

                                                    @if($item->specifications)
                                                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">
                                                            <span class="font-medium">Specifications:</span> {{ $item->specifications }}
                                                        </p>
                                                    @endif

                                                    <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                                        <span class="font-medium">Quantity:</span> {{ $item->quantity }}
                                                    </div>

                                                    <div class="mt-2 text-sm">
                                                        <span class="font-medium text-gray-700 dark:text-gray-300">
                                                            @if($order->negotiation_status === 'in_progress')
                                                                Admin's Counter Price:
                                                            @else
                                                                Current Price:
                                                            @endif
                                                        </span>
                                                        <span class="text-gray-900 dark:text-white">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                                        <span class="text-gray-500 dark:text-gray-400">per unit</span>
                                                    </div>
                                                    
                                                    @if($order->negotiation_status === 'in_progress' && $item->requested_unit_price)
                                                        <div class="mt-1 text-sm">
                                                            <span class="font-medium text-orange-700 dark:text-orange-300">Your Original Request:</span>
                                                            <span class="text-orange-600 dark:text-orange-400">Rp {{ number_format($item->requested_unit_price, 0, ',', '.') }}</span>
                                                            <span class="text-gray-500 dark:text-gray-400">per unit</span>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Price Negotiation -->
                                                <div class="space-y-3">
                                                    <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                                                    
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                            Your Proposed Price (per unit)
                                                        </label>
                                                        <div class="relative">
                                                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">Rp</span>
                                                            <input type="number" 
                                                                   name="items[{{ $index }}][requested_unit_price]" 
                                                                   class="block w-full pl-8 pr-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-neutral-900 text-gray-900 dark:text-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20" 
                                                                   placeholder="0"
                                                                   min="0"
                                                                   step="0.01"
                                                                   value="{{ old('items.'.$index.'.requested_unit_price', $item->price) }}"
                                                                   required>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                            Justification (Optional)
                                                        </label>
                                                        <textarea name="items[{{ $index }}][price_justification]" 
                                                                rows="2"
                                                                class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-neutral-900 px-3 py-2 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20"
                                                                placeholder="Explain why this price is reasonable"
                                                                maxlength="500">{{ old('items.'.$index.'.price_justification') }}</textarea>
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
                    
                    <!-- Price Comparison -->
                    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden sticky top-6">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Price Comparison</h3>
                        </div>
                        
                        <div class="p-6 space-y-4">
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Current Total</span>
                                    <span class="font-medium text-gray-900 dark:text-white" id="currentTotal">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Requested Total</span>
                                    <span class="font-medium text-orange-600 dark:text-orange-400" id="requestedTotal">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Potential Savings</span>
                                    <span class="font-medium text-green-600 dark:text-green-400" id="savings">
                                        Rp 0
                                    </span>
                                </div>
                            </div>

                            <hr class="border-gray-200 dark:border-gray-600">

                            <!-- Submit Button -->
                            <div class="space-y-3">
                                <button type="submit" 
                                        class="block w-full bg-orange-600 hover:bg-orange-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    @if($order->negotiation_status === 'in_progress')
                                        Submit Counter Offer
                                    @else
                                        Submit Negotiation Request
                                    @endif
                                </button>
                                
                                <a href="{{ route('client.orders.show', $order) }}" 
                                   class="block w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition-colors text-center">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Tips -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl p-6 mt-6">
                        <h4 class="text-base font-medium text-blue-900 dark:text-blue-100 mb-3">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Negotiation Tips
                        </h4>
                        <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-2">
                            <li class="flex items-start space-x-2">
                                <span class="text-blue-600 dark:text-blue-400 mt-0.5">•</span>
                                <span>Be reasonable with your pricing requests</span>
                            </li>
                            <li class="flex items-start space-x-2">
                                <span class="text-blue-600 dark:text-blue-400 mt-0.5">•</span>
                                <span>Explain your justification for each price</span>
                            </li>
                            <li class="flex items-start space-x-2">
                                <span class="text-blue-600 dark:text-blue-400 mt-0.5">•</span>
                                <span>Bulk orders often get better pricing</span>
                            </li>
                            <li class="flex items-start space-x-2">
                                <span class="text-blue-600 dark:text-blue-400 mt-0.5">•</span>
                                <span>We'll review and respond within 24-48 hours</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Calculate totals dynamically
        function updateTotals() {
            const currentTotal = {{ $order->total_amount }};
            let requestedTotal = 0;
            
            // Get all price inputs
            const priceInputs = document.querySelectorAll('input[name*="[requested_unit_price]"]');
            
            priceInputs.forEach((input, index) => {
                const price = parseFloat(input.value) || 0;
                const quantity = {{ json_encode($order->items->pluck('quantity')->toArray()) }}[index];
                requestedTotal += price * quantity;
            });
            
            const savings = currentTotal - requestedTotal;
            
            // Update display
            document.getElementById('requestedTotal').textContent = 
                'Rp ' + requestedTotal.toLocaleString('id-ID');
            
            document.getElementById('savings').textContent = 
                'Rp ' + Math.abs(savings).toLocaleString('id-ID');
                
            // Change savings color based on positive/negative
            const savingsEl = document.getElementById('savings');
            if (savings >= 0) {
                savingsEl.className = 'font-medium text-green-600 dark:text-green-400';
            } else {
                savingsEl.className = 'font-medium text-red-600 dark:text-red-400';
                savingsEl.textContent = '-' + savingsEl.textContent;
            }
        }

        // Add event listeners to all price inputs
        document.addEventListener('DOMContentLoaded', function() {
            const priceInputs = document.querySelectorAll('input[name*="[requested_unit_price]"]');
            priceInputs.forEach(input => {
                input.addEventListener('input', updateTotals);
            });
            
            // Initial calculation
            updateTotals();
        });
    </script>
</x-layouts.client>