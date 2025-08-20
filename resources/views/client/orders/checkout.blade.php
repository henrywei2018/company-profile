{{-- resources/views/client/orders/checkout.blade.php --}}
<x-layouts.client>
    <x-slot name="title">Checkout</x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center space-x-4">
            <a href="{{ route('client.cart') }}" 
               class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Checkout</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Review your order and complete your purchase
                </p>
            </div>
        </div>

        <form action="{{ route('client.orders.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Checkout Form -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Customer Information -->
                    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Customer Information</h3>
                        </div>
                        
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Full Name
                                    </label>
                                    <input type="text" 
                                           value="{{ $user->name }}" 
                                           readonly
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Email
                                    </label>
                                    <input type="email" 
                                           value="{{ $user->email }}" 
                                           readonly
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                                </div>
                                
                                @if($user->phone)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Phone
                                        </label>
                                        <input type="text" 
                                               value="{{ $user->phone }}" 
                                               readonly
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                                    </div>
                                @endif
                                
                                @if($user->company)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Company
                                        </label>
                                        <input type="text" 
                                               value="{{ $user->company }}" 
                                               readonly
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Information -->
                    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Delivery Information</h3>
                        </div>
                        
                        <div class="p-6 space-y-4">
                            <div>
                                <label for="delivery_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Delivery Address <span class="text-red-500">*</span>
                                </label>
                                <textarea name="delivery_address" 
                                          id="delivery_address"
                                          rows="3"
                                          required
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('delivery_address') border-red-500 @enderror"
                                          placeholder="Enter complete delivery address...">{{ old('delivery_address', $user->address) }}</textarea>
                                @error('delivery_address')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="needed_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Required Date (Optional)
                                </label>
                                <input type="date" 
                                       name="needed_date" 
                                       id="needed_date"
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                       value="{{ old('needed_date') }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('needed_date') border-red-500 @enderror">
                                @error('needed_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Additional Notes (Optional)
                                </label>
                                <textarea name="notes" 
                                          id="notes"
                                          rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('notes') border-red-500 @enderror"
                                          placeholder="Any special instructions or requirements...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden sticky top-6">
                        
                        <!-- Summary Header -->
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Order Summary</h3>
                        </div>

                        <div class="p-6 space-y-4">
                            
                            <!-- Items List -->
                            <div class="space-y-3">
                                @foreach($cartItems as $item)
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            @if($item->product->image)
                                                <img src="{{ asset('storage/' . $item->product->image) }}" 
                                                     alt="{{ $item->product->name }}"
                                                     class="w-12 h-12 object-cover rounded border border-gray-200 dark:border-gray-600">
                                            @else
                                                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                {{ $item->product->name }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                Qty: {{ $item->quantity }}
                                            </p>
                                        </div>
                                        
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            @if($item->product->getCurrentPriceAttribute() > 0)
                                                Rp {{ number_format($item->quantity * $item->product->getCurrentPriceAttribute(), 0, ',', '.') }}
                                            @else
                                                <span class="text-yellow-600 dark:text-yellow-400">Quote</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <hr class="border-gray-200 dark:border-gray-600">

                            <!-- Pricing Details -->
                            @php
                                $hasQuoteItems = $cartItems->filter(fn($item) => $item->product->getCurrentPriceAttribute() <= 0)->count() > 0;
                                $pricedItems = $cartItems->filter(fn($item) => $item->product->getCurrentPriceAttribute() > 0);
                                $pricedTotal = $pricedItems->sum(fn($item) => $item->quantity * $item->product->getCurrentPriceAttribute());
                            @endphp

                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Items ({{ $cartItems->sum('quantity') }})</span>
                                    <span class="text-gray-900 dark:text-white">{{ $cartItems->count() }} product(s)</span>
                                </div>

                                @if($pricedItems->count() > 0)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                                        <span class="text-gray-900 dark:text-white">
                                            Rp {{ number_format($pricedTotal, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endif

                                @if($hasQuoteItems)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Quote Required</span>
                                        <span class="text-yellow-600 dark:text-yellow-400">
                                            {{ $cartItems->filter(fn($item) => $item->product->getCurrentPriceAttribute() <= 0)->count() }} item(s)
                                        </span>
                                    </div>
                                @endif

                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Delivery</span>
                                    <span class="text-gray-900 dark:text-white">To be calculated</span>
                                </div>
                            </div>

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

                            <!-- Submit Button -->
                            <div class="space-y-3">
                                <button type="submit" 
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                                    @if($hasQuoteItems)
                                        Submit Order & Request Quote
                                    @else
                                        Place Order
                                    @endif
                                </button>

                                <a href="{{ route('client.cart') }}" 
                                   class="block w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-4 rounded-lg transition-colors text-center">
                                    Back to Cart
                                </a>
                            </div>

                            <!-- Additional Info -->
                            <div class="mt-6 text-xs text-gray-500 dark:text-gray-400 space-y-1">
                                @if($hasQuoteItems)
                                    <p>• Items requiring quotation will be processed separately</p>
                                    <p>• You will receive a detailed quote within 24 hours</p>
                                @endif
                                <p>• Delivery fees will be calculated based on location</p>
                                <p>• You will receive order confirmation via email</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-layouts.client>