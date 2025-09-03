{{-- resources/views/client/products/index.blade.php --}}
<x-layouts.client>
    <x-slot name="title">Jelajahi Produk</x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Jelajahi Produk</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Temukan produk kami dan tambahkan ke keranjang Anda
                </p>
            </div>
            
            <!-- Quick Actions -->
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <a href="{{ route('client.cart.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6"></path>
                    </svg>
                    Lihat Keranjang
                </a>
                
                <a href="{{ route('client.quotations.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Minta Penawaran
                </a>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <form method="GET" action="{{ route('client.products.index') }}" class="space-y-4">
                
                <!-- Search Bar -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari Produk</label>
                        <input type="text" 
                               name="search" 
                               id="search"
                               value="{{ request('search') }}"
                               placeholder="Cari berdasarkan nama, deskripsi, atau SKU..."
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Cari
                        </button>
                    </div>
                </div>

                <!-- Filter Row -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    
                    <!-- Category Filter -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kategori</label>
                        <select name="category" id="category" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Price Range Filter -->
                    <div>
                        <label for="price_range" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rentang Harga</label>
                        <select name="price_range" id="price_range" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Semua Harga</option>
                            <option value="under_1m" {{ request('price_range') == 'under_1m' ? 'selected' : '' }}>Dibawah Rp 1M</option>
                            <option value="1m_5m" {{ request('price_range') == '1m_5m' ? 'selected' : '' }}>Rp 1M - 5M</option>
                            <option value="5m_10m" {{ request('price_range') == '5m_10m' ? 'selected' : '' }}>Rp 5M - 10M</option>
                            <option value="over_10m" {{ request('price_range') == 'over_10m' ? 'selected' : '' }}>Diatas Rp 10M</option>
                            <option value="quote_required" {{ request('price_range') == 'quote_required' ? 'selected' : '' }}>Perlu Penawaran</option>
                        </select>
                    </div>

                    <!-- Stock Filter -->
                    <div>
                        <label for="stock_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ketersediaan</label>
                        <select name="stock_status" id="stock_status" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Semua Produk</option>
                            <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>Tersedia</option>
                            <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Habis</option>
                        </select>
                    </div>

                    <!-- Sort Filter -->
                    <div>
                        <label for="sort" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Urutkan</label>
                        <select name="sort" id="sort" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nama A-Z</option>
                            <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>Harga Rendah-Tinggi</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="featured" {{ request('sort') == 'featured' ? 'selected' : '' }}>Unggulan</option>
                        </select>
                    </div>
                </div>

                <!-- Clear Filters -->
                @if(request()->hasAny(['search', 'category', 'price_range', 'stock_status', 'sort']))
                    <div class="flex justify-end">
                        <a href="{{ route('client.products.index') }}" 
                           class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            Hapus semua filter
                        </a>
                    </div>
                @endif
            </form>
        </div>

        <!-- Products Grid -->
        @if($products->count() > 0)
            <!-- Results Summary -->
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Menampilkan {{ $products->firstItem() }} sampai {{ $products->lastItem() }} dari {{ $products->total() }} produk
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Tampilan:</span>
                    <button onclick="toggleGridView('grid')" id="grid-btn" 
                            class="p-2 rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                    </button>
                    <button onclick="toggleGridView('list')" id="list-btn" 
                            class="p-2 rounded-lg text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Products Grid View -->
            <div id="products-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($products as $product)
                    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-all duration-200 group">
                        
                        <!-- Product Image -->
                        <div class="relative aspect-w-16 aspect-h-12 bg-gray-200 dark:bg-gray-700">
                            @php
                                $featuredImage = $product->images->where('is_featured', true)->first();
                                $firstImage = $featuredImage ?: $product->images->first();
                            @endphp
                            
                            @if($firstImage)
                                <img src="{{ asset('storage/' . $firstImage->image_path) }}" 
                                     alt="{{ $product->name }}"
                                     class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-200">
                            @else
                                <div class="w-full h-48 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                            @endif

                            <!-- Featured Badge -->
                            @if($product->featured)
                                <div class="absolute top-3 left-3">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        Featured
                                    </span>
                                </div>
                            @endif

                            <!-- Hover Overlay -->
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
                                <div class="flex space-x-2">
                                    <!-- Quick View -->
                                    <a href="{{ route('client.products.show', $product) }}" 
                                       class="bg-white dark:bg-gray-800 p-3 rounded-full shadow-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 transform hover:scale-110"
                                       title="Lihat Cepat">
                                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    
                                    @if($product->canAddToCart())
                                        <!-- Quick Add to Cart -->
                                        <button onclick="quickAddToCart({{ $product->id }})" 
                                                class="bg-blue-600 hover:bg-blue-700 p-3 rounded-full shadow-lg transition-all duration-200 transform hover:scale-110"
                                                title="Tambah ke Keranjang">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6"></path>
                                            </svg>
                                        </button>
                                    @else
                                        <!-- Minta Penawaran -->
                                        <a href="{{ route('client.quotations.create', ['product_id' => $product->id]) }}" 
                                           class="bg-green-600 hover:bg-green-700 p-3 rounded-full shadow-lg transition-all duration-200 transform hover:scale-110"
                                           title="Minta Penawaran">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Product Info -->
                        <div class="p-4">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        <a href="{{ route('client.products.show', $product) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                            {{ $product->name }}
                                        </a>
                                    </h3>
                                    
                                    @if($product->sku)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            SKU: {{ $product->sku }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Category -->
                            @if($product->category)
                                <div class="mb-3">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                        {{ $product->category->name }}
                                    </span>
                                </div>
                            @endif

                            <!-- Price -->
                            <div class="mb-4">
                                @if($product->getCurrentPriceAttribute() > 0)
                                    <div class="flex items-baseline space-x-2">
                                        <span class="text-lg font-bold text-gray-900 dark:text-white">
                                            Rp {{ number_format($product->getCurrentPriceAttribute(), 0, ',', '.') }}
                                        </span>
                                        @if($product->original_price > $product->getCurrentPriceAttribute())
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
                                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-3 rounded-lg transition-colors text-sm">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6"></path>
                                        </svg>
                                        Add to Keranjang
                                    </button>
                                @else
                                    <a href="{{ route('client.quotations.create', ['product_id' => $product->id]) }}" 
                                       class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-3 rounded-lg transition-colors text-center text-sm">
                                        Minta Penawaran
                                    </a>
                                @endif
                                
                                <a href="{{ route('client.products.show', $product) }}" 
                                   class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-3 rounded-lg transition-colors text-sm">
                                    View
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Products List View (Hidden by default) -->
            <div id="products-list" class="hidden space-y-4">
                @foreach($products as $product)
                    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-start space-x-4">
                            
                            <!-- Product Image -->
                            <div class="flex-shrink-0">
                                @php
                                    $featuredImage = $product->images->where('is_featured', true)->first();
                                    $firstImage = $featuredImage ?: $product->images->first();
                                @endphp
                                
                                @if($firstImage)
                                    <img src="{{ asset('storage/' . $firstImage->image_path) }}" 
                                         alt="{{ $product->name }}"
                                         class="w-20 h-20 object-cover rounded-lg">
                                @else
                                    <div class="w-20 h-20 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
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
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                            <a href="{{ route('client.products.show', $product) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                {{ $product->name }}
                                            </a>
                                        </h3>
                                        
                                        @if($product->sku)
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                SKU: {{ $product->sku }}
                                            </p>
                                        @endif

                                        @if($product->category)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 mt-2">
                                                {{ $product->category->name }}
                                            </span>
                                        @endif

                                        @if($product->description)
                                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2 line-clamp-2">
                                                {{ Str::limit($product->description, 150) }}
                                            </p>
                                        @endif
                                    </div>

                                    <!-- Price & Actions -->
                                    <div class="text-right ml-4">
                                        <!-- Price -->
                                        <div class="mb-3">
                                            @if($product->getCurrentPriceAttribute() > 0)
                                                <div class="text-right">
                                                    <span class="text-xl font-bold text-gray-900 dark:text-white">
                                                        Rp {{ number_format($product->getCurrentPriceAttribute(), 0, ',', '.') }}
                                                    </span>
                                                    @if($product->original_price > $product->getCurrentPriceAttribute())
                                                        <div class="text-sm text-gray-500 dark:text-gray-400 line-through">
                                                            Rp {{ number_format($product->original_price, 0, ',', '.') }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-lg font-medium text-yellow-600 dark:text-yellow-400">
                                                    Quote Required
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Stock -->
                                        @if($product->manage_stock)
                                            <div class="mb-3">
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
                                                <button onclick="quickAddToCart({{ $product->id }})" 
                                                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors text-sm">
                                                    Add to Keranjang
                                                </button>
                                            @else
                                                <a href="{{ route('client.quotations.create', ['product_id' => $product->id]) }}" 
                                                   class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors text-sm">
                                                    Minta Penawaran
                                                </a>
                                            @endif
                                            
                                            <a href="{{ route('client.products.show', $product) }}" 
                                               class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition-colors text-sm">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <div class="flex justify-center">
                    {{ $products->links() }}
                </div>
            @endif

        @else
            <!-- Empty State -->
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No products found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Try adjusting your search or filter criteria.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('client.products.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Clear Filters
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        // Toggle between grid and list view
        function toggleGridView(view) {
            const gridView = document.getElementById('products-grid');
            const listView = document.getElementById('products-list');
            const gridBtn = document.getElementById('grid-btn');
            const listBtn = document.getElementById('list-btn');

            if (view === 'grid') {
                gridView.classList.remove('hidden');
                listView.classList.add('hidden');
                gridBtn.classList.add('bg-blue-100', 'text-blue-600', 'dark:bg-blue-900', 'dark:text-blue-300');
                gridBtn.classList.remove('text-gray-400', 'hover:bg-gray-100', 'dark:hover:bg-gray-700');
                listBtn.classList.add('text-gray-400', 'hover:bg-gray-100', 'dark:hover:bg-gray-700');
                listBtn.classList.remove('bg-blue-100', 'text-blue-600', 'dark:bg-blue-900', 'dark:text-blue-300');
            } else {
                gridView.classList.add('hidden');
                listView.classList.remove('hidden');
                listBtn.classList.add('bg-blue-100', 'text-blue-600', 'dark:bg-blue-900', 'dark:text-blue-300');
                listBtn.classList.remove('text-gray-400', 'hover:bg-gray-100', 'dark:hover:bg-gray-700');
                gridBtn.classList.add('text-gray-400', 'hover:bg-gray-100', 'dark:hover:bg-gray-700');
                gridBtn.classList.remove('bg-blue-100', 'text-blue-600', 'dark:bg-blue-900', 'dark:text-blue-300');
            }
        }

        // Quick add to cart function
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

        // Auto-submit form on filter change
        document.addEventListener('DOMContentLoaded', function() {
            const filterSelects = document.querySelectorAll('#category, #price_range, #stock_status, #sort');
            filterSelects.forEach(select => {
                select.addEventListener('change', function() {
                    this.form.submit();
                });
            });
        });
    </script>
    @endpush
</x-layouts.client>