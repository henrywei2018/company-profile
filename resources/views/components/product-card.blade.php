<div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow">
    
    <!-- Product Image -->
    <div class="aspect-w-16 aspect-h-12 bg-gray-200 dark:bg-gray-700">
        @if($product->image)
            <img src="{{ asset('storage/' . $product->image) }}" 
                 alt="{{ $product->name }}"
                 class="w-full h-48 object-cover">
        @else
            <div class="w-full h-48 flex items-center justify-center">
                <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
        @endif
    </div>

    <!-- Product Info -->
    <div class="p-6">
        <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white truncate">
                    {{ $product->name }}
                </h3>
                
                @if($product->sku)
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        SKU: {{ $product->sku }}
                    </p>
                @endif
            </div>
            
            @if($product->featured)
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                    Featured
                </span>
            @endif
        </div>

        <!-- Price -->
        <div class="mb-4">
            @if($product->current_price > 0)
                <div class="flex items-baseline space-x-2">
                    <span class="text-xl font-bold text-gray-900 dark:text-white">
                        Rp {{ number_format($product->current_price, 0, ',', '.') }}
                    </span>
                    @if($product->original_price > $product->current_price)
                        <span class="text-sm text-gray-500 dark:text-gray-400 line-through">
                            Rp {{ number_format($product->original_price, 0, ',', '.') }}
                        </span>
                    @endif
                </div>
            @else
                <span class="text-lg font-medium text-yellow-600 dark:text-yellow-400">
                    Quote Required
                </span>
            @endif
        </div>

        <!-- Stock Status -->
        @if($product->manage_stock)
            <div class="mb-4">
                @if($product->stock_quantity > 0)
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        {{ $product->stock_quantity }} in stock
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                        Out of stock
                    </span>
                @endif
            </div>
        @endif

        <!-- Actions -->
        <div class="flex items-center space-x-2">
            @if($product->canAddToCart())
                <!-- Quick Add to Cart -->
                <button onclick="quickAddToCart({{ $product->id }})" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6"></path>
                    </svg>
                    Add to Cart
                </button>
            @else
                <a href="{{ route('client.quotations.create', ['product_id' => $product->id]) }}" 
                   class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors text-center">
                    Request Quote
                </a>
            @endif
            
            <a href="{{ route('client.products.show', $product) }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                View
            </a>
        </div>
    </div>
</div>
<script>
// Quick add to cart from product cards
function quickAddToCart(productId, quantity = 1) {
    fetch('{{ route("client.orders.add-to-cart") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            updateCartCount(data.cart_count);
        } else {
            showToast('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Failed to add item to cart');
    });
}

// Simple toast notification
function showToast(type, message) {
    // Create and show toast notification
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transition-all duration-300 ${
        type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'
    }`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Update cart count in navbar
function updateCartCount(count) {
    const cartBadge = document.querySelector('[data-cart-count]');
    if (cartBadge) {
        cartBadge.textContent = count;
        cartBadge.style.display = count > 0 ? 'flex' : 'none';
    }
}
</script>