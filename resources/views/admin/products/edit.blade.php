{{-- resources/views/admin/products/edit.blade.php --}}
<x-layouts.admin title="Edit Product: {{ $product->name }}">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Products' => route('admin.products.index'), 
        $product->name => route('admin.products.show', $product),
        'Edit' => ''
    ]" />

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="product-form">
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
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Product Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" 
                                placeholder="Enter product name..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('name') border-red-500 @enderror"
                                required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="sku" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                SKU
                            </label>
                            <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}" 
                                placeholder="Product SKU (optional)"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('sku') border-red-500 @enderror">
                            @error('sku')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                URL Slug
                            </label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug', $product->slug) }}" 
                                placeholder="url-friendly-name"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('slug') border-red-500 @enderror">
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Leave empty to auto-generate from product name</p>
                        </div>

                        <div class="md:col-span-2">
                            <label for="short_description" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Short Description
                            </label>
                            <textarea name="short_description" id="short_description" rows="2" 
                                placeholder="Brief product description..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('short_description') border-red-500 @enderror">{{ old('short_description', $product->short_description) }}</textarea>
                            @error('short_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Description
                            </label>
                            <textarea name="description" id="description" rows="4" 
                                placeholder="Detailed product description..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>

                <!-- Category & Service -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Category & Service</h3>
                    </x-slot>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="product_category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Product Category
                            </label>
                            <select name="product_category_id" id="product_category_id" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('product_category_id') border-red-500 @enderror">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
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
                            <label for="service_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Related Service
                            </label>
                            <select name="service_id" id="service_id" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('service_id') border-red-500 @enderror">
                                <option value="">Select Service</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" 
                                        {{ old('service_id', $product->service_id) == $service->id ? 'selected' : '' }}>
                                        {{ $service->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('service_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>

                <!-- Product Images -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Product Images</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage product gallery and featured image</p>
                    </x-slot>

                    <!-- Current Images (ProductImage relationship) -->
                    @if($product->images->count() > 0)
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Current Images</h4>
                            <div id="current-images-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sortable-container">
                                @foreach($product->images as $image)
                                    <div data-image-id="{{ $image->id }}" 
                                         class="relative group bg-white dark:bg-gray-800 rounded-lg border-2 border-blue-200 dark:border-blue-700 overflow-hidden hover:border-blue-400 dark:hover:border-blue-500 transition-colors">
                                        
                                        <!-- Image Display -->
                                        <div class="aspect-w-1 aspect-h-1">
                                            <img src="{{ $image->image_url }}" 
                                                 alt="{{ $image->alt_text }}" 
                                                 class="w-full h-32 object-cover">
                                        </div>

                                        <!-- Image Overlay Actions -->
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100">
                                            <div class="flex space-x-2">
                                                <!-- Featured Toggle -->
                                                <button type="button" 
                                                        onclick="toggleFeaturedImage({{ $image->id }})"
                                                        class="p-2 rounded-full transition-colors {{ $image->is_featured ? 'bg-yellow-500 text-white' : 'bg-white bg-opacity-90 text-gray-700 hover:bg-yellow-500 hover:text-white' }}"
                                                        title="{{ $image->is_featured ? 'Remove featured' : 'Set as featured' }}">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                </button>

                                                <!-- Delete Button -->
                                                <button type="button" 
                                                        onclick="deleteProductImage({{ $image->id }})"
                                                        class="p-2 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors"
                                                        title="Delete image">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Featured Badge -->
                                        @if($image->is_featured)
                                            <div class="absolute top-2 left-2">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                    Featured
                                                </span>
                                            </div>
                                        @endif

                                        <!-- Drag Handle -->
                                        <div class="drag-handle absolute top-2 right-2 p-1 bg-gray-900 bg-opacity-50 rounded cursor-move opacity-0 group-hover:opacity-100 transition-opacity">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                            </svg>
                                        </div>

                                        <!-- Alt Text (Bottom) -->
                                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-2">
                                            <input type="text" 
                                                   value="{{ $image->alt_text }}" 
                                                   placeholder="Alt text..."
                                                   onblur="updateImageAltText({{ $image->id }}, this.value)"
                                                   class="w-full text-xs bg-transparent text-white placeholder-gray-300 border-none focus:outline-none focus:ring-1 focus:ring-white rounded px-1">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                                <p><strong>Tip:</strong> Drag images to reorder • Click star to set featured • Click trash to delete</p>
                            </div>
                        </div>
                    @endif

                    <!-- Upload New Images -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">
                            {{ $product->images->count() > 0 ? 'Add More Images' : 'Upload Product Images' }}
                        </h4>

                        <x-universal-file-uploader 
                            :maxFiles="10"
                            maxFileSize="5MB" 
                            :acceptedFileTypes="['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']" 
                            :id="'product-images-uploader'"
                            name="product_images_display"
                            dropDescription="Drop product images here or click to browse"
                            :multiple="true"
                            :galleryMode="true"
                            :instantUpload="false"
                            :autoUpload="false"
                            theme="modern"
                            containerClass="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6"
                        />
                        
                        <!-- Hidden file input for form submission -->
                        <input type="file" id="product_images_hidden" name="product_images[]" multiple accept="image/*" style="display: none;">

                        <div class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                            <p><strong>Note:</strong> New images will be processed when you save the product. Maximum 5MB per image.</p>
                        </div>
                    </div>
                </x-admin.card>

            </div>

            <!-- Sidebar -->
            <div class="w-full lg:w-80 space-y-6">
                
                <!-- Publishing Options -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Publishing</h3>
                    </x-slot>

                    <div class="space-y-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Status
                            </label>
                            <select name="status" id="status" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                                <option value="draft" {{ old('status', $product->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status', $product->status) === 'published' ? 'selected' : '' }}>Published</option>
                                <option value="archived" {{ old('status', $product->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </div>

                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" 
                                    {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                            </label>

                            <label class="flex items-center">
                                <input type="checkbox" name="is_featured" value="1" 
                                    {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Featured Product</span>
                            </label>
                        </div>
                    </div>
                </x-admin.card>

                <!-- Product Details -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Product Details</h3>
                    </x-slot>

                    <div class="space-y-4">
                        <div>
                            <label for="brand" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Brand
                            </label>
                            <input type="text" name="brand" id="brand" value="{{ old('brand', $product->brand) }}" 
                                placeholder="Product brand"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                        </div>

                        <div>
                            <label for="price_type" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Price Type <span class="text-red-500">*</span>
                            </label>
                            <select name="price_type" id="price_type" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('price_type') border-red-500 @enderror"
                                onchange="togglePriceFields(this)">
                                <option value="fixed" {{ old('price_type', $product->price_type ?? 'fixed') === 'fixed' ? 'selected' : '' }}>Fixed Price</option>
                                <option value="quote" {{ old('price_type', $product->price_type ?? 'fixed') === 'quote' ? 'selected' : '' }}>Request Quote</option>
                                <option value="contact" {{ old('price_type', $product->price_type ?? 'fixed') === 'contact' ? 'selected' : '' }}>Contact for Price</option>
                            </select>
                            @error('price_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="price-fields" class="space-y-4" style="display: {{ old('price_type', $product->price_type ?? 'fixed') === 'fixed' ? 'block' : 'none' }};">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                        Regular Price
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">{{ old('currency', $product->currency ?? 'IDR') }}</span>
                                        </div>
                                        <input type="number" name="price" id="price" 
                                            value="{{ old('price', $product->price) }}" 
                                            min="0" step="0.01"
                                            placeholder="0.00"
                                            class="w-full pl-12 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('price') border-red-500 @enderror">
                                    </div>
                                    @error('price')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="sale_price" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                        Sale Price (Optional)
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">{{ old('currency', $product->currency ?? 'IDR') }}</span>
                                        </div>
                                        <input type="number" name="sale_price" id="sale_price" 
                                            value="{{ old('sale_price', $product->sale_price) }}" 
                                            min="0" step="0.01"
                                            placeholder="0.00"
                                            class="w-full pl-12 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('sale_price') border-red-500 @enderror">
                                    </div>
                                    @error('sale_price')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            
                            <div>
                                <label for="currency" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    Currency
                                </label>
                                <select name="currency" id="currency"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('currency') border-red-500 @enderror">
                                    <option value="IDR" {{ old('currency', $product->currency ?? 'IDR') === 'IDR' ? 'selected' : '' }}>Indonesian Rupiah (IDR)</option>
                                    <option value="USD" {{ old('currency', $product->currency ?? 'IDR') === 'USD' ? 'selected' : '' }}>US Dollar (USD)</option>
                                    <option value="EUR" {{ old('currency', $product->currency ?? 'IDR') === 'EUR' ? 'selected' : '' }}>Euro (EUR)</option>
                                </select>
                                @error('currency')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="flex items-center mb-4">
                                <input type="checkbox" name="manage_stock" value="1" 
                                    {{ old('manage_stock', $product->manage_stock ?? false) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    onchange="toggleStockFields(this)">
                                <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Manage Stock</span>
                            </label>
                        </div>

                        <div id="stock-fields" class="space-y-4" style="display: {{ old('manage_stock', $product->manage_stock ?? false) ? 'block' : 'none' }};">
                            <div>
                                <label for="stock_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    Stock Quantity
                                </label>
                                <input type="number" name="stock_quantity" id="stock_quantity" 
                                    value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}" 
                                    min="0" step="1"
                                    placeholder="Available stock quantity"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('stock_quantity') border-red-500 @enderror">
                                @error('stock_quantity')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="stock_status" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Stock Status <span class="text-red-500">*</span>
                            </label>
                            <select name="stock_status" id="stock_status" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('stock_status') border-red-500 @enderror">
                                <option value="in_stock" {{ old('stock_status', $product->stock_status ?? 'in_stock') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                                <option value="out_of_stock" {{ old('stock_status', $product->stock_status ?? 'in_stock') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                                <option value="on_backorder" {{ old('stock_status', $product->stock_status ?? 'in_stock') === 'on_backorder' ? 'selected' : '' }}>On Backorder</option>
                            </select>
                            @error('stock_status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Overall stock availability status</p>
                        </div>

                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Sort Order
                            </label>
                            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $product->sort_order ?? 0) }}" 
                                min="0" step="1"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                            <p class="mt-1 text-xs text-gray-500">Lower numbers appear first</p>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            View Product
                        </a>

                        <form action="{{ route('admin.products.duplicate', $product) }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" 
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                Duplicate Product
                            </button>
                        </form>
                    </div>
                </x-admin.card>

            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('admin.products.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Cancel
            </a>

            <div class="flex space-x-3">
                <button type="submit" name="save_and_continue" value="1"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Save & Continue Editing
                </button>

                <button type="submit" id="submit-button"
                    class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="button-text">Update Product</span>
                </button>
            </div>
        </div>
    </form>

    @push('styles')
        <style>
            /* Drag and Drop Styles */
            .sortable-container [data-image-id] {
                transition: all 0.2s ease;
            }

            .sortable-container [data-image-id].dragging {
                transform: scale(1.05);
                z-index: 1000;
                box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            }

            .sortable-container [data-image-id].drag-over {
                border-color: #3B82F6 !important;
                background-color: rgba(59, 130, 246, 0.1);
                transform: scale(1.02);
            }

            .sortable-container [data-image-id]:hover .drag-handle {
                opacity: 1;
            }

            .drag-handle {
                cursor: grab;
            }

            .drag-handle:active {
                cursor: grabbing;
            }

            /* Image Action Button Styles */
            .image-action-btn {
                padding: 8px;
                border-radius: 50%;
                backdrop-filter: blur(4px);
                background-color: rgba(0,0,0,0.5);
                transition: all 0.2s ease;
            }

            .image-action-btn:hover {
                transform: scale(1.1);
                background-color: rgba(0,0,0,0.7);
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                initializeProductEdit();
            });

            function initializeProductEdit() {
                // Initialize image management
                initializeImageHandlers();
                initializeImageSorting();
                
                // Initialize universal uploader events
                initializeUploaderEvents();
                
                // Initialize form handlers
                initializeFormHandlers();
            }

            // =========================================================================
            // IMAGE MANAGEMENT (ProductImage System)
            // =========================================================================

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
                        onEnd: function(evt) {
                            updateImageOrder();
                        }
                    });
                }
            }

            // =========================================================================
            // PRODUCTIMAGE MANAGEMENT FUNCTIONS
            // =========================================================================

            function toggleFeaturedImage(imageId) {
                showLoading();
                
                makeImageRequest('toggle_featured_product_image', {
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

            function deleteProductImage(imageId) {
                if (!confirm('Are you sure you want to delete this image?')) {
                    return;
                }

                showLoading();
                
                makeImageRequest('delete_product_image', {
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

            function updateImageAltText(imageId, altText) {
                makeImageRequest('update_image_alt_text', {
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

            function updateImageOrder() {
                const imageItems = document.querySelectorAll('#current-images-grid [data-image-id]');
                const imageOrder = Array.from(imageItems).map((item, index) => ({
                    id: parseInt(item.dataset.imageId),
                    sort_order: index + 1
                }));

                if (imageOrder.length === 0) return;

                makeImageRequest('update_image_order', {
                    image_order: imageOrder
                })
                .then(data => {
                    if (data.success) {
                        showNotification('Image order updated', 'success');
                    }
                });
            }

            // =========================================================================
            // DRAG AND DROP FUNCTIONALITY
            // =========================================================================

            function initializeImageSorting() {
                const container = document.getElementById('current-images-grid');
                if (!container) return;

                // Add drag and drop event listeners to all image cards
                const imageCards = container.querySelectorAll('[data-image-id]');
                imageCards.forEach(card => {
                    card.draggable = true;
                    card.addEventListener('dragstart', handleDragStart);
                    card.addEventListener('dragend', handleDragEnd);
                    card.addEventListener('dragover', handleDragOver);
                    card.addEventListener('drop', handleDrop);
                    card.addEventListener('dragenter', handleDragEnter);
                    card.addEventListener('dragleave', handleDragLeave);
                });
            }

            let draggedElement = null;

            function handleDragStart(e) {
                draggedElement = this;
                this.style.opacity = '0.5';
                this.classList.add('dragging');
                
                // Set drag data
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/html', this.outerHTML);
            }

            function handleDragEnd(e) {
                this.style.opacity = '';
                this.classList.remove('dragging');
                
                // Clean up drag indicators
                document.querySelectorAll('.drag-over').forEach(el => {
                    el.classList.remove('drag-over');
                });
                
                draggedElement = null;
            }

            function handleDragOver(e) {
                if (e.preventDefault) {
                    e.preventDefault();
                }
                e.dataTransfer.dropEffect = 'move';
                return false;
            }

            function handleDragEnter(e) {
                if (this !== draggedElement) {
                    this.classList.add('drag-over');
                }
            }

            function handleDragLeave(e) {
                this.classList.remove('drag-over');
            }

            function handleDrop(e) {
                if (e.stopPropagation) {
                    e.stopPropagation();
                }

                if (draggedElement !== this) {
                    // Get all image cards
                    const container = document.getElementById('current-images-grid');
                    const allCards = Array.from(container.querySelectorAll('[data-image-id]'));
                    
                    // Find the indices
                    const draggedIndex = allCards.indexOf(draggedElement);
                    const targetIndex = allCards.indexOf(this);
                    
                    // Reorder the elements
                    if (draggedIndex < targetIndex) {
                        // Insert after target
                        container.insertBefore(draggedElement, this.nextSibling);
                    } else {
                        // Insert before target
                        container.insertBefore(draggedElement, this);
                    }
                    
                    // Update the order on server
                    setTimeout(() => {
                        updateImageOrder();
                    }, 100);
                }

                this.classList.remove('drag-over');
                return false;
            }

            // =========================================================================
            // UNIVERSAL FILE UPLOADER EVENTS
            // =========================================================================

            function initializeUploaderEvents() {
                // Listen for files being selected (no upload yet)
                document.addEventListener('files-selected', function(event) {
                    if (event.detail.component === 'product-images-uploader') {
                        console.log('Files selected:', event.detail.files);
                        const fileCount = event.detail.files.length;
                        if (fileCount > 0) {
                            // Update hidden file input
                            updateHiddenFileInput(event.detail.files);
                            showNotification(`${fileCount} image${fileCount > 1 ? 's' : ''} selected. Click "Update Product" to save them.`, 'info');
                        }
                    }
                });

                // Listen for file removal
                document.addEventListener('file-removed', function(event) {
                    if (event.detail.component === 'product-images-uploader') {
                        console.log('File removed:', event.detail);
                        // Update hidden file input with remaining files
                        updateHiddenFileInput(event.detail.files || []);
                        showNotification('Image removed from selection', 'info');
                    }
                });
            }

            // Update the hidden file input with selected files
            function updateHiddenFileInput(files) {
                const hiddenInput = document.getElementById('product_images_hidden');
                if (!hiddenInput) return;

                // Create a new FileList from the files array
                const dt = new DataTransfer();
                files.forEach(fileObj => {
                    if (fileObj.file) {
                        dt.items.add(fileObj.file);
                    }
                });
                
                hiddenInput.files = dt.files;
                console.log('Updated hidden input with', dt.files.length, 'files');
            }

            // =========================================================================
            // FORM HANDLERS
            // =========================================================================

            function initializeFormHandlers() {
                // Auto-generate slug from name
                const nameInput = document.getElementById('name');
                const slugInput = document.getElementById('slug');
                
                if (nameInput && slugInput) {
                    nameInput.addEventListener('input', function() {
                        if (!slugInput.value || slugInput.dataset.autoGenerated) {
                            const slug = generateSlug(this.value);
                            slugInput.value = slug;
                            slugInput.dataset.autoGenerated = 'true';
                        }
                    });

                    slugInput.addEventListener('input', function() {
                        // User manually edited slug
                        delete this.dataset.autoGenerated;
                    });
                }

                // Form submission handler with file processing
                const form = document.getElementById('product-form');
                const submitButton = document.getElementById('submit-button');
                
                if (form && submitButton) {
                    submitButton.addEventListener('click', function(e) {
                        e.preventDefault(); // Prevent default submission
                        console.log('Submit button clicked'); // Debug log
                        
                        // Show loading state
                        this.disabled = true;
                        const buttonText = this.querySelector('.button-text');
                        if (buttonText) {
                            buttonText.textContent = 'Updating...';
                        }
                        
                        // Files are already in the hidden input, submit normally
                        const hiddenInput = document.getElementById('product_images_hidden');
                        console.log('Files in hidden input:', hiddenInput ? hiddenInput.files.length : 'Hidden input not found');
                        
                        // Submit the form normally - files will be sent via hidden input
                        setTimeout(() => {
                            form.submit();
                        }, 100);
                    });
                }
                
                // Debug: Check if form exists
                if (!form) {
                    console.error('Form with ID "product-form" not found!');
                }
                if (!submitButton) {
                    console.error('Submit button with ID "submit-button" not found!');
                }
            }

            function generateSlug(text) {
                return text.toLowerCase()
                    .replace(/[^\w\s-]/g, '') // Remove special characters
                    .replace(/[\s_-]+/g, '-') // Replace spaces and underscores with hyphens
                    .replace(/^-+|-+$/g, ''); // Remove leading/trailing hyphens
            }

            // Toggle stock management fields
            function toggleStockFields(checkbox) {
                const stockFields = document.getElementById('stock-fields');
                if (stockFields) {
                    stockFields.style.display = checkbox.checked ? 'block' : 'none';
                    
                    // Clear stock quantity if unchecking manage stock
                    if (!checkbox.checked) {
                        const stockQuantityField = document.getElementById('stock_quantity');
                        if (stockQuantityField) {
                            stockQuantityField.value = '';
                        }
                    }
                }
            }

            // Toggle price fields based on price type
            function togglePriceFields(select) {
                const priceFields = document.getElementById('price-fields');
                if (priceFields) {
                    priceFields.style.display = select.value === 'fixed' ? 'block' : 'none';
                    
                    // Clear price fields if not using fixed pricing
                    if (select.value !== 'fixed') {
                        const priceField = document.getElementById('price');
                        const salePriceField = document.getElementById('sale_price');
                        if (priceField) priceField.value = '';
                        if (salePriceField) salePriceField.value = '';
                    }
                }
            }


            // =========================================================================
            // UTILITY FUNCTIONS
            // =========================================================================

            function makeImageRequest(action, data) {
                const productId = {{ $product->id }};
                const url = `{{ route('admin.products.image-action', $product) }}`;
                
                console.log('Making image request to:', url, 'with data:', { action, ...data });
                
                return fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        action: action,
                        ...data
                    })
                })
                .then(response => response.json())
                .catch(error => {
                    console.error('Request failed:', error);
                    showNotification('Request failed. Please try again.', 'error');
                    throw error;
                });
            }

            function removeImageFromUI(selector) {
                const element = document.querySelector(selector);
                if (element) {
                    element.style.opacity = '0';
                    element.style.transform = 'scale(0.8)';
                    setTimeout(() => {
                        element.remove();
                    }, 300);
                }
            }

            function showLoading() {
                // Add loading state to submit buttons only, not all buttons
                const submitButtons = document.querySelectorAll('button[type="submit"]');
                submitButtons.forEach(button => {
                    button.disabled = true;
                    button.classList.add('opacity-75');
                    
                    // Update button text if it has .button-text span
                    const buttonText = button.querySelector('.button-text');
                    if (buttonText) {
                        buttonText.textContent = 'Updating...';
                    }
                });
            }

            function hideLoading() {
                // Remove loading state from submit buttons
                const submitButtons = document.querySelectorAll('button[type="submit"]');
                submitButtons.forEach(button => {
                    button.disabled = false;
                    button.classList.remove('opacity-75');
                    
                    // Restore original button text
                    const buttonText = button.querySelector('.button-text');
                    if (buttonText) {
                        buttonText.textContent = 'Update Product';
                    }
                });
            }

            function showNotification(message, type = 'info') {
                // Simple notification implementation
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white z-50 transition-all duration-300 ${getNotificationColor(type)}`;
                notification.textContent = message;
                
                document.body.appendChild(notification);
                
                // Animate in
                setTimeout(() => {
                    notification.style.transform = 'translateX(0)';
                    notification.style.opacity = '1';
                }, 10);
                
                // Auto remove
                setTimeout(() => {
                    notification.style.transform = 'translateX(100%)';
                    notification.style.opacity = '0';
                    setTimeout(() => {
                        document.body.removeChild(notification);
                    }, 300);
                }, 3000);
            }

            function getNotificationColor(type) {
                switch (type) {
                    case 'success': return 'bg-green-500';
                    case 'error': return 'bg-red-500';
                    case 'warning': return 'bg-yellow-500';
                    default: return 'bg-blue-500';
                }
            }
        </script>
    @endpush
</x-layouts.admin>