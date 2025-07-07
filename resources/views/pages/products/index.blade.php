{{-- resources/views/pages/products/index.blade.php --}}
<x-layouts.public :title="'Our Products - ' . $siteConfig['site_title']"
    description="Explore our complete range of construction and engineering products. Quality materials and solutions for your projects."
    keywords="construction products, building materials, engineering products, construction supplies" type="website">

    {{-- Hero Section - ORANGE THEME TO MATCH PORTFOLIO --}}
    <section class="relative pt-32 pb-20 bg-gradient-to-br from-orange-50 via-white to-amber-50 overflow-hidden">
        {{-- Background Pattern --}}
        <div
            class="absolute inset-0 bg-[url('/images/grid.svg')] bg-center [mask-image:linear-gradient(180deg,white,rgba(255,255,255,0))]">
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10">
            <div class="text-center mb-12">
                <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
                    Our
                    <span class="bg-gradient-to-r from-orange-600 to-amber-600 bg-clip-text text-transparent">
                        Products
                    </span>
                </h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-8">
                    Discover our comprehensive range of high-quality construction and engineering products designed to
                    meet your project needs.
                </p>

                {{-- Quick Stats --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-orange-600">{{ $stats['total_products'] }}+</div>
                        <div class="text-gray-600 text-sm">Total Products</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-orange-600">{{ $stats['active_categories'] }}+</div>
                        <div class="text-gray-600 text-sm">Categories</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-orange-600">{{ $stats['featured_products'] }}+</div>
                        <div class="text-gray-600 text-sm">Featured</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-orange-600">{{ $stats['in_stock_products'] }}+</div>
                        <div class="text-gray-600 text-sm">In Stock</div>
                    </div>
                </div>
            </div>

            {{-- Featured Products Preview --}}
            @if ($featuredProducts && $featuredProducts->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($featuredProducts->take(3) as $product)
                        <div
                            class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden">
                            <div class="relative h-48 overflow-hidden">
                                @php
                                    // Get the main image (featured or first image)
                                    $mainImage =
                                        $product->images->where('is_featured', true)->first() ?:
                                        $product->images->first();
                                @endphp

                                @if ($mainImage)
                                    <img src="{{ $mainImage->image_url }}"
                                        alt="{{ $mainImage->alt_text ?: $product->name }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div
                                        class="w-full h-full bg-gradient-to-br from-orange-100 to-amber-100 flex items-center justify-center">
                                        <svg class="w-16 h-16 text-orange-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>
                                @endif
                                <div class="absolute top-3 left-3">
                                    <span class="bg-orange-500 text-white px-2 py-1 rounded-lg text-xs font-medium">
                                        Featured
                                    </span>
                                </div>
                            </div>
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">{{ $product->name }}</h3>
                                <p class="text-orange-600 font-semibold mb-3">{!! $product->formatted_price !!}</p>
                                <a href="{{ route('products.show', $product->slug) }}"
                                    class="inline-flex items-center text-orange-600 hover:text-orange-700 font-medium">
                                    View Details
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Products Content Section --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-8">
                {{-- Sidebar Filters --}}
                <div class="lg:w-1/4">
                    <div class="bg-gray-50 rounded-2xl p-6 sticky top-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-6">Filter Products</h3>

                        <form method="GET" id="product-filters" class="space-y-6">
                            {{-- Search --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                                <input type="text" name="search" value="{{ $search }}"
                                    placeholder="Search products..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm">
                            </div>

                            {{-- Category Filter --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                <select name="category"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm">
                                    <option value="all" {{ $category === 'all' ? 'selected' : '' }}>All Categories
                                    </option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->slug }}"
                                            {{ $category === $cat->slug ? 'selected' : '' }}>
                                            {{ $cat->name }} ({{ $cat->active_products_count }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Service Filter --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Service</label>
                                <select name="service"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm">
                                    <option value="all" {{ $service === 'all' ? 'selected' : '' }}>All Services
                                    </option>
                                    @foreach ($services as $svc)
                                        <option value="{{ $svc->slug }}"
                                            {{ $service === $svc->slug ? 'selected' : '' }}>
                                            {{ $svc->title }} ({{ $svc->active_products_count }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Brand Filter --}}
                            @if ($brands && $brands->count() > 0)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                                    <select name="brand"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm">
                                        <option value="all" {{ $brand === 'all' ? 'selected' : '' }}>All Brands
                                        </option>
                                        @foreach ($brands as $productBrand)
                                            <option value="{{ $productBrand }}"
                                                {{ $brand === $productBrand ? 'selected' : '' }}>
                                                {{ $productBrand }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            {{-- Price Range Filter --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
                                <select name="price_range"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm">
                                    <option value="all" {{ $priceRange === 'all' ? 'selected' : '' }}>All Prices
                                    </option>
                                    <option value="under-100" {{ $priceRange === 'under-100' ? 'selected' : '' }}>Under
                                        Rp 100,000</option>
                                    <option value="100-500" {{ $priceRange === '100-500' ? 'selected' : '' }}>Rp
                                        100,000 - 500,000</option>
                                    <option value="500-1000" {{ $priceRange === '500-1000' ? 'selected' : '' }}>Rp
                                        500,000 - 1,000,000</option>
                                    <option value="1000-5000" {{ $priceRange === '1000-5000' ? 'selected' : '' }}>Rp
                                        1,000,000 - 5,000,000</option>
                                    <option value="over-5000" {{ $priceRange === 'over-5000' ? 'selected' : '' }}>Over
                                        Rp 5,000,000</option>
                                </select>
                            </div>

                            {{-- Sort By --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                                <select name="sort"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm">
                                    <option value="latest" {{ $sortBy === 'latest' ? 'selected' : '' }}>Latest</option>
                                    <option value="name" {{ $sortBy === 'name' ? 'selected' : '' }}>Name A-Z</option>
                                    <option value="price_low" {{ $sortBy === 'price_low' ? 'selected' : '' }}>Price:
                                        Low to High</option>
                                    <option value="price_high" {{ $sortBy === 'price_high' ? 'selected' : '' }}>Price:
                                        High to Low</option>
                                    <option value="featured" {{ $sortBy === 'featured' ? 'selected' : '' }}>Featured
                                        First</option>
                                </select>
                            </div>

                            <button type="submit"
                                class="w-full bg-orange-600 text-white py-2 px-4 rounded-lg hover:bg-orange-700 transition-colors">
                                Apply Filters
                            </button>

                            @if ($search || $category !== 'all' || $service !== 'all' || $brand !== 'all' || $priceRange !== 'all')
                                <a href="{{ route('products.index') }}"
                                    class="block w-full text-center bg-gray-200 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-300 transition-colors">
                                    Clear All
                                </a>
                            @endif
                        </form>
                    </div>
                </div>

                {{-- Products Grid --}}
                <div class="lg:w-3/4">
                    {{-- Results Header --}}
                    <div class="flex justify-between items-center mb-8">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">
                                @if ($search)
                                    Search Results for "{{ $search }}"
                                @elseif($category && $category !== 'all')
                                    @php $selectedCategory = $categories->firstWhere('slug', $category) @endphp
                                    {{ $selectedCategory ? $selectedCategory->name : 'Category' }} Products
                                @else
                                    All Products
                                @endif
                            </h2>
                            <p class="text-gray-600">{{ $products->total() }} products found</p>
                        </div>
                    </div>

                    {{-- Products Grid --}}
                    @if ($products->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                            @foreach ($products as $product)
                                <div
                                    class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden">
                                    {{-- Product Image - FIXED: Using ProductImage relationship --}}
                                    <div class="relative h-64 overflow-hidden">
                                        @php
                                            // Get the main image (featured or first image)
                                            $mainImage =
                                                $product->images->where('is_featured', true)->first() ?:
                                                $product->images->first();
                                        @endphp

                                        @if ($mainImage)
                                            <img src="{{ $mainImage->image_url }}"
                                                alt="{{ $mainImage->alt_text ?: $product->name }}"
                                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                        @else
                                            <div
                                                class="w-full h-full bg-gradient-to-br from-orange-100 to-amber-100 flex items-center justify-center">
                                                <svg class="w-16 h-16 text-orange-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1"
                                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                            </div>
                                        @endif

                                        {{-- Badges --}}
                                        <div class="absolute top-3 left-3 flex flex-col space-y-2">
                                            @if ($product->is_featured)
                                                <span
                                                    class="bg-orange-500 text-white px-2 py-1 rounded-lg text-xs font-medium">
                                                    Featured
                                                </span>
                                            @endif

                                            @if ($product->stock_status === 'out_of_stock')
                                                <span
                                                    class="bg-red-500 text-white px-2 py-1 rounded-lg text-xs font-medium">
                                                    Out of Stock
                                                </span>
                                            @elseif($product->stock_status === 'on_backorder')
                                                <span
                                                    class="bg-yellow-500 text-white px-2 py-1 rounded-lg text-xs font-medium">
                                                    Backorder
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Category Badge --}}
                                        @if ($product->category)
                                            <div class="absolute top-3 right-3">
                                                <span
                                                    class="bg-white/90 text-gray-800 px-2 py-1 rounded-lg text-xs font-medium">
                                                    {{ $product->category->name }}
                                                </span>
                                            </div>
                                        @endif

                                        {{-- Overlay --}}
                                        <div
                                            class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center">
                                            <a href="{{ route('products.show', $product->slug) }}"
                                                class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-white text-orange-600 px-6 py-3 rounded-lg font-semibold hover:bg-orange-50 transform translate-y-4 group-hover:translate-y-0">
                                                View Details
                                            </a>
                                        </div>
                                    </div>

                                    {{-- Product Content --}}
                                    <div class="p-6">
                                        {{-- Brand --}}
                                        @if ($product->brand)
                                            <div class="text-sm text-orange-600 font-medium mb-2">
                                                {{ $product->brand }}</div>
                                        @endif

                                        {{-- Product Name --}}
                                        <h3
                                            class="text-xl font-bold text-gray-900 mb-3 group-hover:text-orange-600 transition-colors line-clamp-2">
                                            <a href="{{ route('products.show', $product->slug) }}">
                                                {{ $product->name }}
                                            </a>
                                        </h3>

                                        {{-- Product Description --}}
                                        @if ($product->short_description)
                                            <p class="text-gray-600 mb-4 line-clamp-3">
                                                {{ $product->short_description }}
                                            </p>
                                        @endif

                                        {{-- Price and Stock Status --}}
                                        <div class="flex items-center justify-between">
                                            <div class="text-lg font-bold text-orange-600">
                                                {!! $product->formatted_price !!}
                                            </div>

                                            <div class="text-sm">
                                                <span
                                                    class="px-2 py-1 rounded text-xs {{ $product->stock_status_color }}">
                                                    {{ $product->stock_status_label }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="flex justify-center">
                            {{ $products->appends(request()->query())->links() }}
                        </div>
                    @else
                        {{-- No Products Found --}}
                        <div class="text-center py-16">
                            <svg class="w-24 h-24 text-gray-300 mx-auto mb-6" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">No Products Found</h3>
                            <p class="text-gray-600 mb-6">We couldn't find any products matching your criteria. Try
                                adjusting your filters or search terms.</p>
                            <a href="{{ route('products.index') }}"
                                class="inline-flex items-center px-6 py-3 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 transition-colors">
                                View All Products
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section - ORANGE THEME --}}
    <section class="py-20 bg-gradient-to-br from-orange-600 to-amber-600 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-black bg-opacity-20"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl md:text-5xl font-bold mb-6">
                Need Custom Solutions?
            </h2>
            <p class="text-xl mb-8 text-orange-100 max-w-3xl mx-auto">
                Can't find exactly what you're looking for? Our team can help you find the perfect products for your
                specific project requirements.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('contact.index') }}"
                    class="inline-flex items-center px-8 py-4 bg-white text-orange-600 font-semibold rounded-xl hover:bg-orange-50 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    Contact Our Team
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </a>
                <a href="{{ route('services.index') }}"
                    class="inline-flex items-center px-8 py-4 border-2 border-white text-white font-semibold rounded-xl hover:bg-white hover:text-orange-600 transition-all duration-300">
                    View Our Services
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-submit form when filters change
            const form = document.getElementById('product-filters');
            const selects = form.querySelectorAll('select');

            selects.forEach(select => {
                select.addEventListener('change', function() {
                    form.submit();
                });
            });

            // Add search input delay
            const searchInput = form.querySelector('input[name="search"]');
            let searchTimeout;

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    form.submit();
                }, 500);
            });
        });
    </script>

</x-layouts.public>
