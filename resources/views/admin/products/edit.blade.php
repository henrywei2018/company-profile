{{-- resources/views/admin/products/edit.blade.php --}}
@push('meta')
    <meta name="update-route" content="{{ route('admin.products.update', $product) }}">
    <meta name="temp-files-route" content="{{ route('admin.products.temp-files') }}">
@endpush
<x-layouts.admin title="Edit Product - {{ $product->name }}">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Products' => route('admin.products.index'),
        'Edit Product' => '',
        $product->name => '',
    ]" />

    <form action="{{ route('admin.products.update', $product) }}" method="POST" class="space-y-6" id="product-form">
        @csrf
        @method('PUT')

        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Main Content -->
            <div class="flex-1 space-y-6">
                <!-- Basic Information -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Product Information</h3>
                    </x-slot>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Product Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name"
                                value="{{ old('name', $product->name) }}" placeholder="Enter product name..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('name') border-red-500 @enderror"
                                required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="sku"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                SKU
                            </label>
                            <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}"
                                placeholder="Product SKU..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('sku') border-red-500 @enderror">
                            @error('sku')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="slug"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                URL Slug
                            </label>
                            <input type="text" name="slug" id="slug"
                                value="{{ old('slug', $product->slug) }}" placeholder="Auto-generated from name"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('slug') border-red-500 @enderror">
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="brand"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Brand
                            </label>
                            <input type="text" name="brand" id="brand"
                                value="{{ old('brand', $product->brand) }}" placeholder="Product brand..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('brand') border-red-500 @enderror">
                            @error('brand')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="model"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Model
                            </label>
                            <input type="text" name="model" id="model"
                                value="{{ old('model', $product->model) }}" placeholder="Product model..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('model') border-red-500 @enderror">
                            @error('model')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="short_description"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Short Description
                            </label>
                            <textarea name="short_description" id="short_description" rows="3" placeholder="Brief product description..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('short_description') border-red-500 @enderror">{{ old('short_description', $product->short_description) }}</textarea>
                            @error('short_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="description"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Full Description
                            </label>
                            <textarea name="description" id="description" rows="6" placeholder="Detailed product description..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>

                <!-- Categorization -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Categorization</h3>
                    </x-slot>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="product_category_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Product Category
                            </label>
                            <select name="product_category_id" id="product_category_id"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('product_category_id') border-red-500 @enderror">
                                <option value="">Select a category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('product_category_id', $product->product_category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="service_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Primary Service
                            </label>
                            <select name="service_id" id="service_id"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('service_id') border-red-500 @enderror">
                                <option value="">Select a service</option>
                                @foreach ($services as $service)
                                    <option value="{{ $service->id }}"
                                        {{ old('service_id', $product->service_id) == $service->id ? 'selected' : '' }}>
                                        {{ $service->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('service_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>

                <!-- Pricing & Inventory -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pricing & Inventory</h3>
                    </x-slot>

                    <div class="space-y-6">
                        <!-- Price Type -->
                        <div>
                            <label for="price_type"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Price Type
                            </label>
                            <select name="price_type" id="price_type"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                                <option value="fixed"
                                    {{ old('price_type', $product->price_type) == 'fixed' ? 'selected' : '' }}>Fixed
                                    Price</option>
                                <option value="quote"
                                    {{ old('price_type', $product->price_type) == 'quote' ? 'selected' : '' }}>Request
                                    Quote</option>
                                <option value="contact"
                                    {{ old('price_type', $product->price_type) == 'contact' ? 'selected' : '' }}>
                                    Contact for Price</option>
                            </select>
                        </div>

                        <!-- Pricing Fields -->
                        <div id="pricing-fields" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="price"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    Regular Price
                                </label>
                                <div class="relative">
                                    <span
                                        class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">IDR</span>
                                    <input type="number" name="price" id="price" step="0.01"
                                        min="0" value="{{ old('price', $product->price) }}"
                                        placeholder="0.00"
                                        class="w-full pl-12 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('price') border-red-500 @enderror">
                                </div>
                                @error('price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="sale_price"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    Sale Price
                                </label>
                                <div class="relative">
                                    <span
                                        class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">IDR</span>
                                    <input type="number" name="sale_price" id="sale_price" step="0.01"
                                        min="0" value="{{ old('sale_price', $product->sale_price) }}"
                                        placeholder="0.00"
                                        class="w-full pl-12 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('sale_price') border-red-500 @enderror">
                                </div>
                                @error('sale_price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="currency"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    Currency
                                </label>
                                <select name="currency" id="currency"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                                    <option value="IDR"
                                        {{ old('currency', $product->currency) == 'IDR' ? 'selected' : '' }}>IDR -
                                        Indonesian Rupiah</option>
                                    <option value="USD"
                                        {{ old('currency', $product->currency) == 'USD' ? 'selected' : '' }}>USD - US
                                        Dollar</option>
                                    <option value="EUR"
                                        {{ old('currency', $product->currency) == 'EUR' ? 'selected' : '' }}>EUR - Euro
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Stock Management -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <div class="flex items-center mb-4">
                                <input type="checkbox" name="manage_stock" id="manage_stock" value="1"
                                    {{ old('manage_stock', $product->manage_stock) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <label for="manage_stock"
                                    class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Track inventory for this product
                                </label>
                            </div>

                            <div id="stock-fields" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="stock_quantity"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                        Stock Quantity
                                    </label>
                                    <input type="number" name="stock_quantity" id="stock_quantity" min="0"
                                        value="{{ old('stock_quantity', $product->stock_quantity) }}" placeholder="0"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('stock_quantity') border-red-500 @enderror">
                                    @error('stock_quantity')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="stock_status"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                        Stock Status
                                    </label>
                                    <select name="stock_status" id="stock_status"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                                        <option value="in_stock"
                                            {{ old('stock_status', $product->stock_status) == 'in_stock' ? 'selected' : '' }}>
                                            In Stock</option>
                                        <option value="out_of_stock"
                                            {{ old('stock_status', $product->stock_status) == 'out_of_stock' ? 'selected' : '' }}>
                                            Out of Stock</option>
                                        <option value="on_backorder"
                                            {{ old('stock_status', $product->stock_status) == 'on_backorder' ? 'selected' : '' }}>
                                            On Backorder</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-admin.card>

                <!-- Product Images -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Product Images</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage product images</p>
                    </x-slot>

                    <div class="space-y-6">
                        {{-- Current Images Display --}}
                        @if ($product->images->count() > 0 || $product->featured_image || ($product->gallery && count($product->gallery) > 0))
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-200">Current Images
                                    </h4>
                                    <span class="text-xs text-gray-500">
                                        {{ $product->images->count() }} relationship +
                                        {{ $product->featured_image ? 1 : 0 }} featured +
                                        {{ $product->gallery ? count($product->gallery) : 0 }} gallery
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"
                                    id="current-images-grid">

                                    {{-- ProductImage Relationship (New Approach) --}}
                                    @foreach ($product->images as $image)
                                        <div class="relative group image-item" data-image-id="{{ $image->id }}"
                                            data-image-type="relationship"
                                            data-featured="{{ $image->is_featured ? 'true' : 'false' }}">

                                            <div
                                                class="aspect-square rounded-lg overflow-hidden {{ $image->is_featured ? 'ring-2 ring-blue-500' : 'border border-gray-300' }} bg-gray-100">
                                                <img src="{{ $image->image_url }}" alt="{{ $image->alt_text }}"
                                                    class="w-full h-full object-cover">
                                            </div>

                                            {{-- Featured Badge --}}
                                            @if ($image->is_featured)
                                                <div class="absolute top-2 left-2">
                                                    <span
                                                        class="bg-blue-500 text-white text-xs px-2 py-1 rounded font-medium">
                                                        Featured
                                                    </span>
                                                </div>
                                            @endif

                                            {{-- Image Controls --}}
                                            <div
                                                class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                                                <div class="flex space-x-2">
                                                    {{-- Featured Toggle --}}
                                                    <button type="button"
                                                        onclick="toggleFeaturedImage({{ $image->id }})"
                                                        class="p-2 rounded-full transition-colors {{ $image->is_featured ? 'bg-yellow-500 text-white' : 'bg-white text-gray-700 hover:bg-yellow-100' }}"
                                                        title="{{ $image->is_featured ? 'Remove from featured' : 'Set as featured' }}">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                            <path
                                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                        </svg>
                                                    </button>

                                                    {{-- Delete Button --}}
                                                    <button type="button"
                                                        onclick="deleteProductImage({{ $image->id }})"
                                                        class="p-2 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors"
                                                        title="Delete image">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>

                                            {{-- Drag Handle --}}
                                            @if (!$image->is_featured)
                                                <div
                                                    class="drag-handle absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity cursor-move">
                                                    <div class="p-1 bg-white rounded shadow">
                                                        <svg class="w-3 h-3 text-gray-600" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path
                                                                d="M7 2a2 2 0 00-2 2v12a2 2 0 002 2h6a2 2 0 002-2V4a2 2 0 00-2-2H7zM8 4h4v2H8V4zm0 4h4v2H8V8zm0 4h4v2H8v-2z" />
                                                        </svg>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- Alt Text Input --}}
                                            <div class="mt-2">
                                                <input type="text"
                                                    onchange="updateImageAltText({{ $image->id }}, this.value)"
                                                    value="{{ $image->alt_text }}" placeholder="Alt text for SEO"
                                                    class="w-full text-xs px-2 py-1 border border-gray-300 dark:border-gray-600 rounded text-gray-700 dark:text-gray-300 dark:bg-gray-700 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                            </div>
                                        </div>
                                    @endforeach

                                    {{-- Legacy Images (if any) --}}
                                    @if ($product->featured_image)
                                        <div class="relative group image-item"
                                            data-image-path="{{ $product->featured_image }}"
                                            data-image-type="legacy-featured">
                                            <div
                                                class="aspect-square rounded-lg overflow-hidden ring-2 ring-orange-500 bg-gray-100">
                                                <img src="{{ Storage::url($product->featured_image) }}"
                                                    alt="Legacy Featured Image" class="w-full h-full object-cover">
                                            </div>
                                            <div class="absolute top-2 left-2">
                                                <span
                                                    class="bg-orange-500 text-white text-xs px-2 py-1 rounded font-medium">
                                                    Legacy Featured
                                                </span>
                                            </div>
                                            <div
                                                class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                                                <button type="button"
                                                    onclick="deleteExistingImage('{{ $product->featured_image }}', 'featured')"
                                                    class="p-2 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors"
                                                    title="Delete legacy image">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($product->gallery && is_array($product->gallery))
                                        @foreach ($product->gallery as $index => $image)
                                            <div class="relative group image-item"
                                                data-image-path="{{ $image }}"
                                                data-image-type="legacy-gallery">
                                                <div
                                                    class="aspect-square rounded-lg overflow-hidden border border-orange-300 bg-gray-100">
                                                    <img src="{{ Storage::url($image) }}"
                                                        alt="Legacy Gallery Image {{ $index + 1 }}"
                                                        class="w-full h-full object-cover">
                                                </div>
                                                <div class="absolute top-2 left-2">
                                                    <span
                                                        class="bg-orange-400 text-white text-xs px-2 py-1 rounded font-medium">
                                                        Legacy Gallery
                                                    </span>
                                                </div>
                                                <div
                                                    class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                                                    <button type="button"
                                                        onclick="deleteExistingImage('{{ $image }}', 'gallery', {{ $index }})"
                                                        class="p-2 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors"
                                                        title="Delete legacy image">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                {{-- Instructions --}}
                                <div
                                    class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h5 class="text-sm font-medium text-blue-800 dark:text-blue-200">Image
                                                Management</h5>
                                            <div class="mt-1 text-sm text-blue-700 dark:text-blue-300">
                                                <ul class="list-disc list-inside space-y-1">
                                                    <li><strong>Blue border</strong>: Current system (ProductImage
                                                        relationship)</li>
                                                    <li><strong>Orange border</strong>: Legacy system (database fields)
                                                    </li>
                                                    <li>Drag to reorder • Click star to set featured • New uploads use
                                                        current system</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Upload New Images --}}
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Upload New Images</h4>

                            <x-universal-file-uploader :uploadUrl="route('admin.products.temp-upload')" :deleteUrl="route('admin.products.temp-delete')" :maxFiles="10"
                                :maxFileSize="2048" :acceptedTypes="['image/jpeg', 'image/png', 'image/jpg', 'image/gif']" component="product-images-uploader"
                                :showFeaturedToggle="false" :allowReorder="false" title="Upload Product Images"
                                description="Upload high-quality images. New images will be stored using the current ProductImage system."
                                :enableCategories="false" :autoUpload="true" :galleryMode="true" theme="modern" />
                        </div>
                    </div>
                </x-admin.card>

                <!-- Specifications -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Product Specifications</h3>
                    </x-slot>

                    <div class="space-y-6">
                        <!-- Basic Specs -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-3">
                                General Specifications
                            </label>
                            <div id="specifications-container" class="space-y-3">
                                @if (old('specifications', $product->specifications))
                                    @foreach (old('specifications', $product->specifications) as $key => $value)
                                        <div class="flex gap-3 specification-row">
                                            <input type="text" name="specifications[{{ $loop->index }}][key]"
                                                placeholder="Specification name" value="{{ $key }}"
                                                class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                                            <input type="text" name="specifications[{{ $loop->index }}][value]"
                                                placeholder="Specification value" value="{{ $value }}"
                                                class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                                            <button type="button" onclick="removeSpecification(this)"
                                                class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" onclick="addSpecification()"
                                class="mt-3 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-sm">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Specification
                            </button>
                        </div>

                        <!-- Weight & Dimensions -->
                        <div
                            class="grid grid-cols-1 md:grid-cols-4 gap-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div>
                                <label for="weight"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    Weight (kg)
                                </label>
                                <input type="number" name="weight" id="weight" step="0.01" min="0"
                                    value="{{ old('weight', $product->weight) }}" placeholder="0.00"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                            </div>

                            @php
                                $dimensions = old('dimensions', $product->dimensions ?? []);
                            @endphp

                            <div>
                                <label for="dimension_length"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    Length (cm)
                                </label>
                                <input type="number" name="dimensions[length]" id="dimension_length" step="0.01"
                                    min="0" value="{{ $dimensions['length'] ?? '' }}" placeholder="0.00"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                            </div>

                            <div>
                                <label for="dimension_width"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    Width (cm)
                                </label>
                                <input type="number" name="dimensions[width]" id="dimension_width" step="0.01"
                                    min="0" value="{{ $dimensions['width'] ?? '' }}" placeholder="0.00"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                            </div>

                            <div>
                                <label for="dimension_height"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    Height (cm)
                                </label>
                                <input type="number" name="dimensions[height]" id="dimension_height" step="0.01"
                                    min="0" value="{{ $dimensions['height'] ?? '' }}" placeholder="0.00"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                            </div>
                        </div>
                    </div>
                </x-admin.card>
            </div>

            <!-- Sidebar -->
            <div class="lg:w-80 space-y-6">
                <!-- Product Status -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Product Status</h3>
                    </x-slot>

                    <div class="space-y-4">
                        <div>
                            <label for="status"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Publication Status
                            </label>
                            <select name="status" id="status"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                                <option value="draft"
                                    {{ old('status', $product->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published"
                                    {{ old('status', $product->status) == 'published' ? 'selected' : '' }}>Published
                                </option>
                                <option value="archived"
                                    {{ old('status', $product->status) == 'archived' ? 'selected' : '' }}>Archived
                                </option>
                            </select>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1"
                                    {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <label for="is_active"
                                    class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Active
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="is_featured" id="is_featured" value="1"
                                    {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <label for="is_featured"
                                    class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Featured Product
                                </label>
                            </div>
                        </div>

                        <div>
                            <label for="sort_order"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Sort Order
                            </label>
                            <input type="number" name="sort_order" id="sort_order" min="0"
                                value="{{ old('sort_order', $product->sort_order) }}" placeholder="0"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                            <p class="mt-1 text-xs text-gray-500">Lower numbers appear first</p>
                        </div>
                    </div>
                </x-admin.card>

                <!-- Product Information -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Product Information</h3>
                    </x-slot>

                    <div class="space-y-4 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Product ID:</span>
                            <span class="font-medium">{{ $product->id }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Created:</span>
                            <span class="font-medium">{{ $product->created_at->format('M d, Y') }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Last Updated:</span>
                            <span class="font-medium">{{ $product->updated_at->format('M d, Y') }}</span>
                        </div>

                        @if ($product->sku)
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">SKU:</span>
                                <span class="font-medium font-mono">{{ $product->sku }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Current Status:</span>
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                {{ $product->status === 'published'
                                    ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'
                                    : ($product->status === 'draft'
                                        ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100'
                                        : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100') }}">
                                {{ ucfirst($product->status) }}
                            </span>
                        </div>
                    </div>
                </x-admin.card>

                <!-- Quick Actions -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h3>
                    </x-slot>

                    <div class="space-y-3">
                        <a href="{{ route('admin.products.show', $product) }}"
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                            View Product
                        </a>

                        <button type="button" onclick="deleteProduct('{{ $product->slug }}')"
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-gray-800 dark:text-red-400 dark:border-red-600 dark:hover:bg-red-900/20">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                            Delete Product
                        </button>
                    </div>
                </x-admin.card>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('admin.products.index') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Products
            </a>

            <div class="flex space-x-3">
                <button type="button" onclick="saveAsDraft()"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a.997.997 0 01-1.414 0l-7-7A1.997 1.997 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    Save as Draft
                </button>

                <button type="submit"
                    class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Update Product
                </button>
            </div>
        </div>
    </form>
@push('scripts')
<script>
// Clean Product Edit Script
document.addEventListener('DOMContentLoaded', function() {
    initializeProductEdit();
});

function initializeProductEdit() {
    // Initialize image management
    initializeImageHandlers();
    
    // Initialize universal uploader events
    initializeUploaderEvents();
    
    // Load existing temp files
    loadExistingTempFiles();
    
    // Initialize other form handlers
    initializeFormHandlers();
}

// Image Management Functions
function initializeImageHandlers() {
    // Initialize sortable if library is available
    const currentImagesGrid = document.getElementById('current-images-grid');
    if (currentImagesGrid && typeof Sortable !== 'undefined') {
        new Sortable(currentImagesGrid, {
            animation: 150,
            ghostClass: 'opacity-50',
            chosenClass: 'ring-2 ring-blue-500',
            dragClass: 'transform rotate-2 scale-105',
            handle: '.drag-handle',
            filter: '[data-featured="true"]', // Don't sort featured images
            onEnd: function(evt) {
                updateImageOrder();
            }
        });
    }
}

// Toggle featured image (ProductImage relationship)
function toggleFeaturedImage(imageId) {
    showLoading();
    
    makeRequest('toggle_featured_product_image', {
        image_id: imageId
    })
    .then(data => {
        if (data.success) {
            location.reload(); // Refresh to show updated status
        } else {
            showNotification(data.message || 'Error updating featured image', 'error');
        }
    })
    .finally(() => {
        hideLoading();
    });
}

// Delete ProductImage (new approach)
function deleteProductImage(imageId) {
    if (!confirm('Are you sure you want to delete this image?')) {
        return;
    }

    showLoading();
    
    makeRequest('delete_product_image', {
        image_id: imageId
    })
    .then(data => {
        if (data.success) {
            removeImageFromUI(`[data-image-id="${imageId}"]`);
            showNotification('Image deleted successfully', 'success');
        } else {
            showNotification(data.message || 'Error deleting image', 'error');
        }
    })
    .finally(() => {
        hideLoading();
    });
}

// Update alt text for ProductImage
function updateImageAltText(imageId, altText) {
    makeRequest('update_image_alt_text', {
        image_id: imageId,
        alt_text: altText
    })
    .then(data => {
        if (data.success) {
            showNotification('Alt text updated', 'success');
        } else {
            showNotification('Error updating alt text', 'error');
        }
    });
}

// Delete legacy image (featured_image or gallery)
function deleteExistingImage(imagePath, imageType, galleryIndex = null) {
    if (!confirm('Are you sure you want to delete this image?')) {
        return;
    }

    showLoading();
    
    makeRequest('delete_legacy_image', {
        image_path: imagePath,
        image_type: imageType,
        gallery_index: galleryIndex
    })
    .then(data => {
        if (data.success) {
            removeImageFromUI(`[data-image-path="${imagePath}"]`);
            showNotification('Image deleted successfully', 'success');
        } else {
            showNotification(data.message || 'Error deleting image', 'error');
        }
    })
    .finally(() => {
        hideLoading();
    });
}

// Update image order after drag & drop
function updateImageOrder() {
    const imageItems = document.querySelectorAll('#current-images-grid [data-image-id]');
    const imageOrder = Array.from(imageItems).map((item, index) => ({
        id: item.dataset.imageId,
        sort_order: index + 1
    }));

    if (imageOrder.length === 0) return;

    makeRequest('update_image_order', {
        image_order: imageOrder
    })
    .then(data => {
        if (data.success) {
            showNotification('Image order updated', 'success');
        }
    });
}

// Universal Uploader Event Handlers
function initializeUploaderEvents() {
    // Listen for upload success
    document.addEventListener('files-uploaded', function(event) {
        if (event.detail.component === 'product-images-uploader') {
            handleTempUploadSuccess(event.detail);
        }
    });

    // Listen for file deletion
    document.addEventListener('file-deleted', function(event) {
        if (event.detail.component === 'product-images-uploader') {
            handleTempFileDelete(event.detail);
        }
    });
}

function handleTempUploadSuccess(detail) {
    console.log('Temp upload success:', detail);
    showNotification('Images uploaded successfully! Save the product to make them permanent.', 'success');
}

function handleTempFileDelete(detail) {
    console.log('Temp file deleted:', detail);
    showNotification('Temporary image removed', 'info');
}

// Load existing temporary files
function loadExistingTempFiles() {
    fetch('{{ route('admin.products.temp-files') }}')
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                const event = new CustomEvent('load-temp-files', {
                    detail: {
                        files: data,
                        component: 'product-images-uploader'
                    }
                });
                document.dispatchEvent(event);
            }
        })
        .catch(error => console.error('Error loading temp files:', error));
}

// Initialize other form handlers
function initializeFormHandlers() {
    // Price type toggle
    const priceTypeSelect = document.getElementById('price_type');
    if (priceTypeSelect) {
        priceTypeSelect.addEventListener('change', togglePriceFields);
        togglePriceFields(); // Initialize on load
    }

    // Stock management toggle
    const manageStockCheckbox = document.getElementById('manage_stock');
    if (manageStockCheckbox) {
        manageStockCheckbox.addEventListener('change', toggleStockFields);
        toggleStockFields(); // Initialize on load
    }

    // Slug generation
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    if (nameInput && slugInput) {
        nameInput.addEventListener('input', function() {
            if (!slugInput.dataset.manual) {
                slugInput.value = generateSlug(this.value);
            }
        });
        
        slugInput.addEventListener('input', function() {
            this.dataset.manual = 'true';
        });
    }
}

// Helper Functions
function togglePriceFields() {
    const priceType = document.getElementById('price_type')?.value;
    const priceField = document.getElementById('price-field');
    const salePriceField = document.getElementById('sale-price-field');
    
    if (priceField && salePriceField && priceType) {
        if (priceType === 'fixed') {
            priceField.style.display = 'block';
            salePriceField.style.display = 'block';
        } else {
            priceField.style.display = 'none';
            salePriceField.style.display = 'none';
        }
    }
}

function toggleStockFields() {
    const manageStock = document.getElementById('manage_stock')?.checked;
    const stockQuantityField = document.getElementById('stock-quantity-field');
    
    if (stockQuantityField) {
        stockQuantityField.style.display = manageStock ? 'block' : 'none';
    }
}

function generateSlug(text) {
    return text
        .toLowerCase()
        .replace(/[^\w\s-]/g, '') // Remove special characters
        .replace(/\s+/g, '-') // Replace spaces with hyphens
        .replace(/-+/g, '-') // Replace multiple hyphens with single
        .trim('-'); // Remove leading/trailing hyphens
}

function removeImageFromUI(selector) {
    const imageElement = document.querySelector(selector);
    if (imageElement) {
        imageElement.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        imageElement.style.opacity = '0';
        imageElement.style.transform = 'scale(0.8)';
        
        setTimeout(() => {
            imageElement.remove();
            updateEmptyState();
        }, 300);
    }
}

function updateEmptyState() {
    const currentImagesSection = document.querySelector('#current-images-grid')?.parentElement;
    const remainingImages = document.querySelectorAll('#current-images-grid .image-item').length;
    
    if (currentImagesSection && remainingImages === 0) {
        currentImagesSection.style.display = 'none';
    }
}

// Utility Functions
function makeRequest(action, data) {
    return fetch('{{ route('admin.products.update', $product) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'X-HTTP-Method-Override': 'PATCH'
        },
        body: JSON.stringify({
            action: action,
            ...data
        })
    })
    .then(response => response.json())
    .catch(error => {
        console.error('Request error:', error);
        showNotification('Network error occurred', 'error');
        throw error;
    });
}

function getCsrfToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.getAttribute('content') : '';
}

function showLoading() {
    let loader = document.getElementById('page-loader');
    if (!loader) {
        loader = document.createElement('div');
        loader.id = 'page-loader';
        loader.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        loader.innerHTML = `
            <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                <span class="text-gray-700">Processing...</span>
            </div>
        `;
        document.body.appendChild(loader);
    } else {
        loader.style.display = 'flex';
    }
}

function hideLoading() {
    const loader = document.getElementById('page-loader');
    if (loader) {
        loader.style.display = 'none';
    }
}

function showNotification(message, type = 'info') {
    // Remove existing notifications
    document.querySelectorAll('.notification-toast').forEach(n => n.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification-toast fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full max-w-sm`;
    
    // Set colors based on type
    const colors = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        warning: 'bg-yellow-500 text-white',
        info: 'bg-blue-500 text-white'
    };
    
    notification.className += ` ${colors[type] || colors.info}`;
    
    notification.innerHTML = `
        <div class="flex items-center">
            <div class="flex-1">
                <div class="flex items-center">
                    ${getNotificationIcon(type)}
                    <span class="ml-2">${message}</span>
                </div>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200 focus:outline-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove
    setTimeout(() => {
        if (notification.parentNode) {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 300);
        }
    }, 5000);
}

function getNotificationIcon(type) {
    const icons = {
        success: `<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>`,
        error: `<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>`,
        warning: `<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>`,
        info: `<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
        </svg>`
    };
    return icons[type] || icons.info;
}
</script>
@endpush
</x-layouts.admin>
