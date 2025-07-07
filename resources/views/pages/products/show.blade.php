{{-- resources/views/pages/products/show.blade.php --}}
<x-layouts.public
    :title="$product->name . ' - Products - ' . $siteConfig['site_title']"
    :description="$product->short_description ?: 'Learn more about our ' . $product->name . ' product.'"
    :keywords="$product->name . ', ' . $product->brand . ', construction product, building material'"
    type="product"
>

{{-- Breadcrumbs --}}
<section class="pt-32 pb-8 bg-gradient-to-br from-orange-50 via-white to-amber-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-orange-600 inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('products.index') }}" class="ml-1 text-gray-700 hover:text-orange-600 md:ml-2">Products</a>
                    </div>
                </li>
                @if($product->category)
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="ml-1 text-gray-700 hover:text-orange-600 md:ml-2">{{ $product->category->name }}</a>
                    </div>
                </li>
                @endif
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-gray-500 md:ml-2">{{ Str::limit($product->name, 30) }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>
</section>

{{-- Product Details Section --}}
<section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            {{-- Product Images - CLEAN PRODUCTIMAGE ONLY --}}
            <div class="space-y-4">
                {{-- Main Image --}}
                <div class="relative aspect-square rounded-2xl overflow-hidden bg-gray-100">
                    @if($product->images->count() > 0)
                        @php $mainImage = $product->images->first() @endphp
                        <img id="main-image" 
                             src="{{ $mainImage->image_url }}" 
                             alt="{{ $mainImage->alt_text ?: $product->name }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-orange-100 to-amber-100 flex items-center justify-center">
                            <svg class="w-24 h-24 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                    @endif
                    
                    {{-- Image Badges --}}
                    <div class="absolute top-4 left-4 flex flex-col space-y-2">
                        @if($product->is_featured)
                        <span class="bg-orange-500 text-white px-3 py-1 rounded-lg text-sm font-medium">
                            Featured
                        </span>
                        @endif
                        
                        @if($product->stock_status === 'out_of_stock')
                        <span class="bg-red-500 text-white px-3 py-1 rounded-lg text-sm font-medium">
                            Out of Stock
                        </span>
                        @elseif($product->stock_status === 'on_backorder')
                        <span class="bg-yellow-500 text-white px-3 py-1 rounded-lg text-sm font-medium">
                            Backorder
                        </span>
                        @elseif($product->stock_status === 'in_stock')
                        <span class="bg-green-500 text-white px-3 py-1 rounded-lg text-sm font-medium">
                            In Stock
                        </span>
                        @endif
                    </div>
                </div>

                {{-- Image Thumbnails --}}
                @if($product->images->count() > 1)
                <div class="grid grid-cols-4 gap-4">
                    @foreach($product->images as $image)
                    <button class="image-thumb aspect-square rounded-lg overflow-hidden bg-gray-100 border-2 border-transparent hover:border-orange-500 focus:border-orange-500 transition-colors {{ $loop->first ? 'border-orange-500' : '' }}"
                            onclick="changeMainImage('{{ $image->image_url }}', '{{ $image->alt_text ?: $product->name }}')">
                        <img src="{{ $image->image_url }}" 
                             alt="{{ $image->alt_text ?: $product->name }}"
                             class="w-full h-full object-cover">
                    </button>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Product Information --}}
            <div class="space-y-6">
                {{-- Header Info --}}
                <div>
                    @if($product->brand)
                    <div class="text-orange-600 font-medium mb-2">{{ $product->brand }}</div>
                    @endif
                    
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">{{ $product->name }}</h1>
                    
                    @if($product->sku)
                    <div class="text-gray-600 mb-4">SKU: <span class="font-mono">{{ $product->sku }}</span></div>
                    @endif
                </div>

                {{-- Price --}}
                <div class="border-t border-b border-gray-200 py-6">
                    <div class="text-3xl font-bold text-orange-600 mb-2">
                        {!! $product->formatted_price !!}
                    </div>
                    
                    @if($product->sale_price && $product->sale_price < $product->price)
                    <div class="text-lg text-gray-500">
                        You save: <span class="text-green-600 font-semibold">
                            Rp {{ number_format($product->price - $product->sale_price, 0, ',', '.') }}
                        </span>
                    </div>
                    @endif
                </div>

                {{-- Short Description --}}
                @if($product->short_description)
                <div class="prose prose-lg max-w-none">
                    <p class="text-gray-700 leading-relaxed">{{ $product->short_description }}</p>
                </div>
                @endif

                {{-- Product Details --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 py-6 border-t border-gray-200">
                    @if($product->category)
                    <div>
                        <span class="text-gray-600">Category:</span>
                        <span class="font-medium ml-2">{{ $product->category->name }}</span>
                    </div>
                    @endif
                    
                    @if($product->service)
                    <div>
                        <span class="text-gray-600">Service:</span>
                        <span class="font-medium ml-2">{{ $product->service->title }}</span>
                    </div>
                    @endif
                    
                    <div>
                        <span class="text-gray-600">Stock Status:</span>
                        <span class="font-medium ml-2 {{ $product->stock_status_color }}">{{ $product->stock_status_label }}</span>
                    </div>
                    
                    @if($product->manage_stock && $product->stock_quantity !== null)
                    <div>
                        <span class="text-gray-600">Available:</span>
                        <span class="font-medium ml-2">{{ $product->stock_quantity }} units</span>
                    </div>
                    @endif
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('contact.index') }}?inquiry=product&product={{ $product->slug }}" 
                       class="flex-1 border-2 border-orange-600 text-orange-600 text-center py-4 px-8 rounded-xl font-semibold hover:bg-orange-50 transition-colors">
                        Ask Question
                    </a>
                </div>

                {{-- Additional Info --}}
                @if($product->category || $product->service)
                <div class="flex flex-wrap gap-2 pt-6 border-t border-gray-200">
                    @if($product->category)
                    <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" 
                       class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $product->category->name }}
                    </a>
                    @endif
                    
                    @if($product->service)
                    <a href="{{ route('services.show', $product->service->slug) }}" 
                       class="inline-flex items-center px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-sm hover:bg-orange-200 transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $product->service->title }}
                    </a>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- Product Description --}}
@if($product->description)
<section class="py-20 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Product Description</h2>
        <div class="prose prose-lg max-w-none">
            <div class="text-gray-700 leading-relaxed">
                {!! nl2br(e($product->description)) !!}
            </div>
        </div>
    </div>
</section>
@endif

{{-- Specifications --}}
@if($product->specifications && count($product->specifications) > 0)
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Specifications</h2>
        <div class="bg-gray-50 rounded-2xl p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($product->specifications as $spec)
                <div class="flex justify-between items-center py-3 border-b border-gray-200">
                    <span class="font-medium text-gray-700">{{ $spec['name'] ?? 'N/A' }}</span>
                    <span class="text-gray-900">{{ $spec['value'] ?? 'N/A' }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- Related Products --}}
@if($relatedProducts && $relatedProducts->count() > 0)
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-12 text-center">Related Products</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($relatedProducts as $relatedProduct)
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden">
                {{-- Product Image --}}
                <div class="relative h-48 overflow-hidden">
                    @if($relatedProduct->images->first())
                        <img src="{{ $relatedProduct->images->first()->image_url }}" 
                             alt="{{ $relatedProduct->name }}" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-orange-100 to-amber-100 flex items-center justify-center">
                            <svg class="w-12 h-12 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                    @endif
                    
                    @if($relatedProduct->is_featured)
                    <div class="absolute top-3 left-3">
                        <span class="bg-orange-500 text-white px-2 py-1 rounded-lg text-xs font-medium">
                            Featured
                        </span>
                    </div>
                    @endif
                </div>
                
                {{-- Product Content --}}
                <div class="p-6">
                    @if($relatedProduct->brand)
                    <div class="text-sm text-orange-600 mb-2">{{ $relatedProduct->brand }}</div>
                    @endif
                    
                    <h3 class="text-lg font-bold text-gray-900 mb-3 group-hover:text-orange-600 transition-colors line-clamp-2">
                        {{ $relatedProduct->name }}
                    </h3>
                    
                    <div class="flex items-center justify-between">
                        <div class="text-lg font-bold text-orange-600">
                            {!! $relatedProduct->formatted_price !!}
                        </div>
                        
                        <a href="{{ route('products.show', $relatedProduct->slug) }}" 
                           class="text-orange-600 hover:text-orange-700 font-medium text-sm">
                            View Details →
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="text-center mt-12">
            <a href="{{ route('products.index') }}" 
               class="inline-flex items-center px-8 py-4 bg-orange-600 text-white font-semibold rounded-xl hover:bg-orange-700 transition-colors">
                View All Products
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>
    </div>
</section>
@endif

{{-- CTA Section --}}
<section class="py-20 bg-gradient-to-br from-orange-600 to-amber-600 text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl md:text-5xl font-bold mb-6">
            Interested in This Product?
        </h2>
        <p class="text-xl mb-8 text-orange-100 max-w-3xl mx-auto">
            Get in touch with our team to discuss your requirements and get a customized quote for {{ $product->name }}.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('contact.index') }}?inquiry=product&product={{ $product->slug }}" 
               class="inline-flex items-center px-8 py-4 border-2 border-white text-white font-semibold rounded-xl hover:bg-white hover:text-orange-600 transition-all duration-300">
                Ask Questions
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </a>
        </div>
    </div>
</section>

<script>
function changeMainImage(src, alt) {
    const mainImage = document.getElementById('main-image');
    mainImage.src = src;
    mainImage.alt = alt;
    
    // Update active thumbnail
    document.querySelectorAll('.image-thumb').forEach(thumb => {
        thumb.classList.remove('border-orange-500');
        thumb.classList.add('border-transparent');
    });
    
    event.currentTarget.classList.remove('border-transparent');
    event.currentTarget.classList.add('border-orange-500');
}

// Zoom functionality for main image
document.addEventListener('DOMContentLoaded', function() {
    const mainImage = document.getElementById('main-image');
    if (mainImage) {
        mainImage.addEventListener('click', function() {
            // Create modal for image zoom
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4';
            modal.onclick = function() { document.body.removeChild(modal); };
            
            const img = document.createElement('img');
            img.src = this.src;
            img.className = 'max-w-full max-h-full object-contain';
            img.onclick = function(e) { e.stopPropagation(); };
            
            modal.appendChild(img);
            document.body.appendChild(modal);
        });
    }
});
</script>

</x-layouts.public>