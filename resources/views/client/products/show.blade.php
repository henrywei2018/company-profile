{{-- resources/views/client/products/show.blade.php --}}
<x-layouts.client>
    <x-slot name="title">{{ $product->name }}</x-slot>

    <div class="space-y-6">
        <!-- Breadcrumb -->
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('client.products.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Produk
                    </a>
                </li>
                @if($product->category)
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('client.products.category', $product->category) }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white md:ml-2">
                                {{ $product->category->name }}
                            </a>
                        </div>
                    </li>
                @endif
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-400 md:ml-2">{{ Str::limit($product->name, 30) }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Product Detail -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Product Images -->
            <div class="space-y-4">
                <!-- Main Image -->
                <div class="aspect-w-1 aspect-h-1 bg-gray-200 dark:bg-gray-700 rounded-xl overflow-hidden relative group cursor-pointer" onclick="openLightbox()">
                    @php
                        $featuredImage = $product->images->where('is_featured', true)->first();
                        $firstImage = $featuredImage ?: $product->images->first();
                    @endphp
                    
                    @if($firstImage)
                        <img id="main-image" 
                             src="{{ asset('storage/' . $firstImage->image_path) }}" 
                             alt="{{ $product->name }}"
                             class="w-full h-96 object-cover transition-transform duration-300 group-hover:scale-105">
                    @else
                        <div class="w-full h-96 flex items-center justify-center">
                            <svg class="w-24 h-24 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                    @endif
                    
                    @if($firstImage)
                        <!-- Hover Overlay -->
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
                            <div class="bg-white dark:bg-gray-800 p-3 rounded-full shadow-lg transform group-hover:scale-110 transition-all duration-200">
                                <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Thumbnail Images -->
                @if($product->images && $product->images->count() > 0)
                    <div class="grid grid-cols-4 gap-2">
                        @foreach($product->images as $image)
                            <button onclick="changeMainImage('{{ asset('storage/' . $image->image_path) }}')"
                                    class="aspect-w-1 aspect-h-1 bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden border-2 border-transparent hover:border-blue-500 transition-colors">
                                <img src="{{ asset('storage/' . $image->image_path) }}" 
                                     alt="{{ $product->name }}"
                                     class="w-full h-20 object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Product Informasi -->
            <div class="space-y-6">
                
                <!-- Header -->
                <div>
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $product->name }}</h1>
                            
                            @if($product->sku)
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    SKU: {{ $product->sku }}
                                </p>
                            @endif
                        </div>

                        @if($product->featured)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                Unggulan
                            </span>
                        @endif
                    </div>

                    @if($product->category)
                        <div class="mt-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ $product->category->name }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Price -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    @if($product->getCurrentPriceAttribute() > 0)
                        <div class="flex items-baseline space-x-3">
                            <span class="text-3xl font-bold text-gray-900 dark:text-white">
                                Rp {{ number_format($product->getCurrentPriceAttribute(), 0, ',', '.') }}
                            </span>
                            @if($product->original_price > $product->getCurrentPriceAttribute())
                                <span class="text-xl text-gray-500 dark:text-gray-400 line-through">
                                    Rp {{ number_format($product->original_price, 0, ',', '.') }}
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    {{ round((($product->original_price - $product->getCurrentPriceAttribute()) / $product->original_price) * 100) }}% OFF
                                </span>
                            @endif
                        </div>
                    @else
                        <div class="flex items-center space-x-3">
                            <span class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                                Perlu Penawaran
                            </span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                Hubungi kami untuk informasi harga.
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Stock Status -->
                @if($product->manage_stock)
                    <div>
                        @if($product->stock_quantity > 0)
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                <span class="text-sm font-medium text-green-700 dark:text-green-400">
                                    {{ $product->stock_quantity }} in stock
                                </span>
                            </div>
                        @else
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                <span class="text-sm font-medium text-red-700 dark:text-red-400">
                                    Out of stock
                                </span>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Deskripsi -->
                @if($product->description)
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Deskripsi</h3>
                        <div class="text-gray-600 dark:text-gray-300 prose prose-sm max-w-none">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    </div>
                @endif

                <!-- Add to Cart Section -->
                @if($product->canAddToCart())
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <form id="add-to-cart-form" class="space-y-4">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            
                            <!-- Quantity -->
                            <div class="flex items-center space-x-4">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Quantity:</label>
                                <div class="flex items-center space-x-2">
                                    <button type="button" onclick="updateQuantity(-1)"
                                            class="w-10 h-10 flex items-center justify-center rounded-full border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                        </svg>
                                    </button>
                                    
                                    <input type="number" 
                                           id="quantity-input"
                                           name="quantity" 
                                           value="{{ $product->min_quantity ?? 1 }}" 
                                           min="{{ $product->min_quantity ?? 1 }}"
                                           max="{{ $product->manage_stock ? $product->stock_quantity : 999 }}"
                                           class="w-20 px-3 py-2 text-center border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    
                                    <button type="button" onclick="updateQuantity(1)"
                                            class="w-10 h-10 flex items-center justify-center rounded-full border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </button>
                                </div>
                                
                                @if($product->min_quantity > 1)
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        Min: {{ $product->min_quantity }}
                                    </span>
                                @endif
                            </div>

                            <!-- Specifications -->
                            <div>
                                <label for="specifications" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Catatan Tambahan (Optional)
                                </label>
                                <textarea name="specifications" 
                                          id="specifications"
                                          rows="3"
                                          placeholder="Masukkan persyaratan atau penyesuaian khusus apa pun..."
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                            </div>

                            <!-- Add to Keranjang Button -->
                            <div class="flex space-x-3">
                                <button type="submit" 
                                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6"></path>
                                    </svg>
                                    Add to Keranjang
                                </button>

                                <a href="{{ route('client.cart.index') }}" 
                                   class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                                    Lihat Keranjang
                                </a>
                            </div>
                        </form>
                    </div>
                @else
                    <!-- Quote Request Section -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4 mb-4">
                            <div class="flex items-start space-x-3">
                                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="flex-1">
                                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Perlu Penawaran</h3>
                                    <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                                        This product requires a custom quotation. Contact us to get pricing and availability.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex space-x-3">
                            <a href="{{ route('client.quotations.create', ['product_id' => $product->id]) }}" 
                               class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-lg transition-colors text-center">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Request Quotation
                            </a>

                            <a href="{{ route('client.messages.create') }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                                Hubungi Kami
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Product Specifications -->
                @if($product->specifications)
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Specifications</h3>
                        <div class="text-gray-600 dark:text-gray-300 prose prose-sm max-w-none">
                            {!! nl2br(e($product->specifications)) !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Related Produk -->
        @if($relatedProducts && $relatedProducts->count() > 0)
            <div class="border-t border-gray-200 dark:border-gray-700 pt-8">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Related Produk</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $relatedProduct)
                        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow">
                            
                            <!-- Product Image -->
                            <div class="aspect-w-16 aspect-h-12 bg-gray-200 dark:bg-gray-700 relative group">
                                @php
                                    $relatedFeaturedImage = $relatedProduct->images->where('is_featured', true)->first();
                                    $relatedFirstImage = $relatedFeaturedImage ?: $relatedProduct->images->first();
                                @endphp
                                
                                @if($relatedFirstImage)
                                    <img src="{{ asset('storage/' . $relatedFirstImage->image_path) }}" 
                                         alt="{{ $relatedProduct->name }}"
                                         class="w-full h-32 object-cover group-hover:scale-105 transition-transform duration-200">
                                @else
                                    <div class="w-full h-32 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                    </div>
                                @endif
                                
                                @if($relatedFirstImage)
                                    <!-- Hover Overlay for Related Produk -->
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
                                        <a href="{{ route('client.products.show', $relatedProduct) }}" 
                                           class="bg-white dark:bg-gray-800 p-2 rounded-full shadow-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 transform hover:scale-110">
                                            <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <!-- Product Info -->
                            <div class="p-4">
                                <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate mb-2">
                                    <a href="{{ route('client.products.show', $relatedProduct) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                        {{ $relatedProduct->name }}
                                    </a>
                                </h3>

                                <!-- Price -->
                                <div class="mb-3">
                                    @if($relatedProduct->getCurrentPriceAttribute() > 0)
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">
                                            Rp {{ number_format($relatedProduct->getCurrentPriceAttribute(), 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-sm font-medium text-yellow-600 dark:text-yellow-400">
                                            Perlu Penawaran
                                        </span>
                                    @endif
                                </div>

                                <!-- Actions -->
                                <div class="flex space-x-2">
                                    @if($relatedProduct->canAddToCart())
                                        <button onclick="quickAddToCart({{ $relatedProduct->id }})" 
                                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-1 px-2 rounded text-xs transition-colors">
                                            Add to Keranjang
                                        </button>
                                    @else
                                        <a href="{{ route('client.quotations.create', ['product_id' => $relatedProduct->id]) }}" 
                                           class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-1 px-2 rounded text-xs text-center transition-colors">
                                            Minta Penawaran
                                        </a>
                                    @endif
                                    
                                    <a href="{{ route('client.products.show', $relatedProduct) }}" 
                                       class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-1 px-2 rounded text-xs transition-colors">
                                        Lihat
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Image Lightbox Modal -->
    <div id="image-lightbox" class="fixed inset-0 z-50 hidden bg-black bg-opacity-75 flex items-center justify-center p-4">
        <div class="relative max-w-4xl max-h-full">
            <img id="lightbox-image" src="" alt="" class="max-w-full max-h-full object-contain rounded-lg">
            <button onclick="closeLightbox()" class="absolute top-4 right-4 bg-white dark:bg-gray-800 p-2 rounded-full shadow-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    @push('scripts')
    <script>
        // Change main image when thumbnail clicked
        function changeMainImage(imageSrc) {
            document.getElementById('main-image').src = imageSrc;
        }

        // Open lightbox
        function openLightbox() {
            const mainImage = document.getElementById('main-image');
            const lightboxImage = document.getElementById('lightbox-image');
            const lightbox = document.getElementById('image-lightbox');
            
            if (mainImage && mainImage.src) {
                lightboxImage.src = mainImage.src;
                lightboxImage.alt = mainImage.alt;
                lightbox.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        // Close lightbox
        function closeLightbox() {
            const lightbox = document.getElementById('image-lightbox');
            lightbox.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close lightbox when clicking outside the image
        document.getElementById('image-lightbox').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLightbox();
            }
        });

        // Close lightbox with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLightbox();
            }
        });

        // Update quantity input
        function updateQuantity(change) {
            const input = document.getElementById('quantity-input');
            const currentValue = parseInt(input.value);
            const minValue = parseInt(input.min);
            const maxValue = parseInt(input.max);
            
            const newValue = currentValue + change;
            
            if (newValue >= minValue && newValue <= maxValue) {
                input.value = newValue;
            }
        }

        // Add to cart form submission
        document.addEventListener('DOMContentLoaded', function() {
            const addToCartForm = document.getElementById('add-to-cart-form');
            
            if (addToCartForm) {
                addToCartForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const submitButton = this.querySelector('button[type="submit"]');
                    const originalText = submitButton.innerHTML;
                    
                    // Show loading state
                    submitButton.disabled = true;
                    submitButton.innerHTML = `
                        <svg class="animate-spin w-5 h-5 inline mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Adding...
                    `;
                    
                    fetch('{{ route("client.cart.add") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            showToast('success', data.message);
                            
                            // Update cart count in navbar
                            updateCartCount(data.cart_count);
                            
                            // Reset form
                            this.reset();
                            this.querySelector('input[name="quantity"]').value = {{ $product->min_quantity ?? 1 }};
                            
                        } else {
                            showToast('error', data.message || 'Failed to add item to cart');
                        }
                    })
                    .catch(error => {
                        console.error('Kesalahan:', error);
                        showToast('error', 'An error occurred. Please try again.');
                    })
                    .finally(() => {
                        // Restore button
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalText;
                    });
                });
            }
        });

        // Quick add to cart for related products
        function quickAddToCart(productId, quantity = 1) {
            fetch('{{ route("client.cart.add") }}', {
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
                console.error('Kesalahan:', error);
                showToast('error', 'Failed to add item to cart');
            });
        }

        // Toast notification function
        function showToast(type, message) {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 ${
                type === 'success' 
                    ? 'bg-green-600 text-white' 
                    : 'bg-red-600 text-white'
            }`;
            toast.innerHTML = `
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${type === 'success' 
                            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
                            : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>'
                        }
                    </svg>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            // Animate in
            setTimeout(() => toast.classList.add('translate-x-0'), 100);
            
            // Remove after 3 seconds
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => document.body.removeChild(toast), 300);
            }, 3000);
        }

        // Update cart count in navbar
        function updateCartCount(count) {
            const cartCountElements = document.querySelectorAll('[data-cart-count]');
            cartCountElements.forEach(element => {
                element.textContent = count;
                if (count > 0) {
                    element.classList.remove('hidden');
                } else {
                    element.classList.add('hidden');
                }
            });
        }
    </script>
    @endpush
</x-layouts.client>