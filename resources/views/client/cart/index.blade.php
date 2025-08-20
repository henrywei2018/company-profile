{{-- resources/views/client/cart/index.blade.php --}}
<x-layouts.client>
    <x-slot name="title">My Cart</x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Cart</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Review your items before checkout
                </p>
            </div>
            
            <!-- Action Buttons -->
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <a href="{{ route('client.products.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Continue Shopping
                </a>
            </div>
        </div>

        @if($cartItems->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Cart Items -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        
                        <!-- Header -->
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    Cart Items ({{ $cartItems->count() }})
                                </h3>
                                <button type="button" onclick="clearCart()" 
                                        class="text-sm text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                    Clear Cart
                                </button>
                            </div>
                        </div>

                        <!-- Cart Items List -->
                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($cartItems as $item)
                                <div class="p-6" id="cart-item-{{ $item->product->id }}">
                                    <div class="flex items-start space-x-4">
                                        
                                        <!-- Product Image -->
                                        <div class="flex-shrink-0">
                                            @if($item->product->image)
                                                <img src="{{ asset('storage/' . $item->product->image) }}" 
                                                     alt="{{ $item->product->name }}"
                                                     class="w-16 h-16 object-cover rounded-lg border border-gray-200 dark:border-gray-600">
                                            @else
                                                <div class="w-16 h-16 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
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
                                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                        {{ $item->product->name }}
                                                    </h4>
                                                    
                                                    @if($item->product->sku)
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                            SKU: {{ $item->product->sku }}
                                                        </p>
                                                    @endif

                                                    @if($item->specifications)
                                                        <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                                                            <span class="font-medium">Specifications:</span> {{ $item->specifications }}
                                                        </p>
                                                    @endif

                                                    <!-- Price -->
                                                    <div class="mt-2">
                                                        @if($item->product->getCurrentPriceAttribute() > 0)
                                                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                                                Rp {{ number_format($item->product->getCurrentPriceAttribute(), 0, ',', '.') }}
                                                            </span>
                                                            @if($item->product->original_price > $item->product->getCurrentPriceAttribute())
                                                                <span class="text-xs text-gray-500 dark:text-gray-400 line-through ml-2">
                                                                    Rp {{ number_format($item->product->original_price, 0, ',', '.') }}
                                                                </span>
                                                            @endif
                                                        @else
                                                            <span class="text-sm text-yellow-600 dark:text-yellow-400">
                                                                Quote Required
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Remove Button -->
                                                <button type="button" 
                                                        onclick="removeFromCart({{ $item->product->id }})"
                                                        class="ml-4 text-gray-400 hover:text-red-500 dark:text-gray-500 dark:hover:text-red-400">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>

                                            <!-- Quantity Controls -->
                                            <div class="flex items-center mt-3">
                                                <label class="text-xs text-gray-600 dark:text-gray-400 mr-2">Quantity:</label>
                                                <div class="flex items-center space-x-2">
                                                    <button type="button" 
                                                            onclick="updateQuantity({{ $item->product->id }}, {{ max(1, $item->quantity - 1) }})"
                                                            class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                        </svg>
                                                    </button>
                                                    
                                                    <input type="number" 
                                                           value="{{ $item->quantity }}" 
                                                           min="{{ $item->product->min_quantity ?? 1 }}"
                                                           class="w-16 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                           onchange="updateQuantity({{ $item->product->id }}, this.value)">
                                                    
                                                    <button type="button" 
                                                            onclick="updateQuantity({{ $item->product->id }}, {{ $item->quantity + 1 }})"
                                                            class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                                
                                                @if($item->product->min_quantity > 1)
                                                    <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">
                                                        Min: {{ $item->product->min_quantity }}
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Item Total -->
                                            @if($item->product->getCurrentPriceAttribute() > 0)
                                                <div class="mt-3 text-right">
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                                        Subtotal: Rp {{ number_format($item->quantity * $item->product->getCurrentPriceAttribute(), 0, ',', '.') }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden sticky top-6">
                        
                        <!-- Summary Header -->
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Order Summary</h3>
                        </div>

                        <div class="p-6 space-y-4">
                            
                            <!-- Items Count -->
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Items ({{ $cartItems->sum('quantity') }})</span>
                                <span class="text-gray-900 dark:text-white">{{ $cartItems->count() }} product(s)</span>
                            </div>

                            <!-- Price breakdown -->
                            @php
                                $hasQuoteItems = $cartItems->filter(fn($item) => $item->product->getCurrentPriceAttribute() <= 0)->count() > 0;
                                $pricedItems = $cartItems->filter(fn($item) => $item->product->getCurrentPriceAttribute() > 0);
                                $pricedTotal = $pricedItems->sum(fn($item) => $item->quantity * $item->product->getCurrentPriceAttribute());
                            @endphp

                            @if($pricedItems->count() > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Priced Items</span>
                                    <span class="text-gray-900 dark:text-white">
                                        Rp {{ number_format($pricedTotal, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endif

                            @if($hasQuoteItems)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Quote Required</span>
                                    <span class="text-yellow-600 dark:text-yellow-400">{{ $cartItems->filter(fn($item) => $item->product->getCurrentPriceAttribute() <= 0)->count() }} item(s)</span>
                                </div>
                            @endif

                            <hr class="border-gray-200 dark:border-gray-600">

                            <!-- Total -->
                            <div class="flex justify-between">
                                <span class="text-base font-medium text-gray-900 dark:text-white">Total</span>
                                <span class="text-lg font-bold text-gray-900 dark:text-white">
                                    @if($hasQuoteItems)
                                        <div class="text-right">
                                            @if($pricedTotal > 0)
                                                <div>Rp {{ number_format($pricedTotal, 0, ',', '.') }}</div>
                                                <div class="text-sm text-yellow-600 dark:text-yellow-400">+ Quote Required</div>
                                            @else
                                                <div class="text-yellow-600 dark:text-yellow-400">Quote Required</div>
                                            @endif
                                        </div>
                                    @else
                                        Rp {{ number_format($cartTotal, 0, ',', '.') }}
                                    @endif
                                </span>
                            </div>

                            <hr class="border-gray-200 dark:border-gray-600">

                            <!-- Action Buttons -->
                            <div class="space-y-3">
                                @if($hasQuoteItems)
                                    <!-- Request Quotation -->
                                    <button type="button" 
                                            onclick="requestQuotation()" 
                                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                                        Request Quotation
                                    </button>
                                    
                                    @if($pricedItems->count() > 0)
                                        <!-- Checkout Priced Items Only -->
                                        <button type="button" 
                                                onclick="checkoutPricedItems()" 
                                                class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                                            Checkout Priced Items
                                        </button>
                                    @endif
                                @else
                                    <!-- Regular Checkout -->
                                    <a href="{{ route('client.orders.checkout') }}" 
                                       class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors text-center">
                                        Proceed to Checkout
                                    </a>
                                @endif

                                <!-- Continue Shopping -->
                                <a href="{{ route('client.products.index') }}" 
                                   class="block w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-4 rounded-lg transition-colors text-center">
                                    Continue Shopping
                                </a>
                            </div>

                            <!-- Additional Info -->
                            <div class="mt-6 text-xs text-gray-500 dark:text-gray-400 space-y-1">
                                <p>• Items requiring quotation will be processed separately</p>
                                <p>• Delivery fees will be calculated at checkout</p>
                                <p>• All prices are in Indonesian Rupiah (IDR)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @else
            <!-- Empty Cart -->
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Your cart is empty</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Start adding some products to your cart.</p>
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

    @push('scripts')
    <script>
        // Update quantity
        function updateQuantity(productId, quantity) {
            if (quantity < 1) return;
            
            fetch('{{ route("client.cart.update-quantity") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: parseInt(quantity)
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Failed to update quantity');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update quantity');
            });
        }

        // Remove from cart
        function removeFromCart(productId) {
            if (!confirm('Are you sure you want to remove this item from your cart?')) {
                return;
            }
            
            fetch('{{ route("client.orders.remove-from-cart") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    product_id: productId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('cart-item-' + productId).remove();
                    location.reload();
                } else {
                    alert(data.message || 'Failed to remove item');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to remove item');
            });
        }

        // Clear cart
        function clearCart() {
            if (!confirm('Are you sure you want to clear your entire cart?')) {
                return;
            }
            
            fetch('{{ route("client.orders.clear-cart") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Failed to clear cart');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to clear cart');
            });
        }

        // Request quotation for all items
        function requestQuotation() {
            // Redirect to quotation creation with cart items
            window.location.href = '{{ route("client.quotations.create") }}?from_cart=1';
        }

        // Checkout only priced items
        function checkoutPricedItems() {
            // This would need additional backend logic to handle partial checkout
            alert('This feature will checkout only items with fixed prices. Quote-required items will remain in cart.');
            window.location.href = '{{ route("client.orders.checkout") }}?priced_only=1';
        }
    </script>
    @endpush
</x-layouts.client>