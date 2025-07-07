{{-- resources/views/admin/products/edit.blade.php --}}
<x-layouts.admin title="Edit Product - {{ $product->name }}">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Products' => route('admin.products.index'), 
        'Edit Product' => '',
        $product->name => ''
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
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Product Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" placeholder="Enter product name..."
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
                            <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}" placeholder="Product SKU..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('sku') border-red-500 @enderror">
                            @error('sku')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                URL Slug
                            </label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug', $product->slug) }}" placeholder="Auto-generated from name"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('slug') border-red-500 @enderror">
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="brand" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Brand
                            </label>
                            <input type="text" name="brand" id="brand" value="{{ old('brand', $product->brand) }}" placeholder="Product brand..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('brand') border-red-500 @enderror">
                            @error('brand')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="model" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Model
                            </label>
                            <input type="text" name="model" id="model" value="{{ old('model', $product->model) }}" placeholder="Product model..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('model') border-red-500 @enderror">
                            @error('model')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="short_description" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Short Description
                            </label>
                            <textarea name="short_description" id="short_description" rows="3" placeholder="Brief product description..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('short_description') border-red-500 @enderror">{{ old('short_description', $product->short_description) }}</textarea>
                            @error('short_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
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
                            <label for="product_category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Product Category
                            </label>
                            <select name="product_category_id" id="product_category_id"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('product_category_id') border-red-500 @enderror">
                                <option value="">Select a category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('product_category_id', $product->product_category_id) == $category->id ? 'selected' : '' }}>
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
                                Primary Service
                            </label>
                            <select name="service_id" id="service_id"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('service_id') border-red-500 @enderror">
                                <option value="">Select a service</option>
                                @foreach ($services as $service)
                                    <option value="{{ $service->id }}" {{ old('service_id', $product->service_id) == $service->id ? 'selected' : '' }}>
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
                            <label for="price_type" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Price Type
                            </label>
                            <select name="price_type" id="price_type" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                                <option value="fixed" {{ old('price_type', $product->price_type) == 'fixed' ? 'selected' : '' }}>Fixed Price</option>
                                <option value="quote" {{ old('price_type', $product->price_type) == 'quote' ? 'selected' : '' }}>Request Quote</option>
                                <option value="contact" {{ old('price_type', $product->price_type) == 'contact' ? 'selected' : '' }}>Contact for Price</option>
                            </select>
                        </div>

                        <!-- Pricing Fields -->
                        <div id="pricing-fields" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    Regular Price
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">IDR</span>
                                    <input type="number" name="price" id="price" step="0.01" min="0" 
                                        value="{{ old('price', $product->price) }}" placeholder="0.00"
                                        class="w-full pl-12 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('price') border-red-500 @enderror">
                                </div>
                                @error('price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="sale_price" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    Sale Price
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">IDR</span>
                                    <input type="number" name="sale_price" id="sale_price" step="0.01" min="0"
                                        value="{{ old('sale_price', $product->sale_price) }}" placeholder="0.00"
                                        class="w-full pl-12 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('sale_price') border-red-500 @enderror">
                                </div>
                                @error('sale_price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="currency" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    Currency
                                </label>
                                <select name="currency" id="currency"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                                    <option value="IDR" {{ old('currency', $product->currency) == 'IDR' ? 'selected' : '' }}>IDR - Indonesian Rupiah</option>
                                    <option value="USD" {{ old('currency', $product->currency) == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                    <option value="EUR" {{ old('currency', $product->currency) == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                </select>
                            </div>
                        </div>

                        <!-- Stock Management -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <div class="flex items-center mb-4">
                                <input type="checkbox" name="manage_stock" id="manage_stock" value="1"
                                    {{ old('manage_stock', $product->manage_stock) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <label for="manage_stock" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Track inventory for this product
                                </label>
                            </div>

                            <div id="stock-fields" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="stock_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
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
                                    <label for="stock_status" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                        Stock Status
                                    </label>
                                    <select name="stock_status" id="stock_status"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                                        <option value="in_stock" {{ old('stock_status', $product->stock_status) == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                                        <option value="out_of_stock" {{ old('stock_status', $product->stock_status) == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                                        <option value="on_backorder" {{ old('stock_status', $product->stock_status) == 'on_backorder' ? 'selected' : '' }}>On Backorder</option>
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
                    </x-slot>

                    <div class="space-y-6">
                        <!-- Current Images Display -->
                        @if($product->featured_image || ($product->gallery && count($product->gallery) > 0))
                            <div class="space-y-4">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-200">Current Images</h4>
                                
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    @if($product->featured_image)
                                        <div class="relative group">
                                            <img src="{{ Storage::url($product->featured_image) }}" 
                                                 alt="Featured Image" 
                                                 class="w-full h-32 object-cover rounded-lg border-2 border-blue-500">
                                            <div class="absolute top-2 left-2">
                                                <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded">Featured</span>
                                            </div>
                                            <button type="button" 
                                                    onclick="deleteExistingImage('{{ $product->featured_image }}', 'featured')"
                                                    class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    @endif

                                    @if($product->gallery && is_array($product->gallery))
                                        @foreach($product->gallery as $image)
                                            <div class="relative group">
                                                <img src="{{ Storage::url($image) }}" 
                                                     alt="Gallery Image" 
                                                     class="w-full h-32 object-cover rounded-lg border border-gray-300">
                                                <button type="button" 
                                                        onclick="deleteExistingImage('{{ $image }}', 'gallery')"
                                                        class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Image Upload Component -->
                        <div>
                            <x-admin.universal-uploader 
                                :uploadUrl="route('admin.products.temp-upload')"
                                :deleteUrl="route('admin.products.temp-delete')"
                                :maxFiles="10"
                                :maxFileSize="2048"
                                :acceptedTypes="['image/jpeg', 'image/png', 'image/jpg', 'image/gif']"
                                component="product-images-uploader"
                                :showFeaturedToggle="true"
                                :allowReorder="true"
                                title="Upload Product Images"
                                description="Upload high-quality images of your product. The first image or marked featured image will be used as the main product image." />
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
                                @if(old('specifications', $product->specifications))
                                    @foreach(old('specifications', $product->specifications) as $key => $value)
                                        <div class="flex gap-3 specification-row">
                                            <input type="text" name="specifications[{{ $loop->index }}][key]" 
                                                   placeholder="Specification name" value="{{ $key }}"
                                                   class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                                            <input type="text" name="specifications[{{ $loop->index }}][value]" 
                                                   placeholder="Specification value" value="{{ $value }}"
                                                   class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                                            <button type="button" onclick="removeSpecification(this)" 
                                                    class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" onclick="addSpecification()" 
                                    class="mt-3 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-sm">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Specification
                            </button>
                        </div>

                        <!-- Weight & Dimensions -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
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
                                <label for="dimension_length" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    Length (cm)
                                </label>
                                <input type="number" name="dimensions[length]" id="dimension_length" step="0.01" min="0"
                                    value="{{ $dimensions['length'] ?? '' }}" placeholder="0.00"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                            </div>

                            <div>
                                <label for="dimension_width" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    Width (cm)
                                </label>
                                <input type="number" name="dimensions[width]" id="dimension_width" step="0.01" min="0"
                                    value="{{ $dimensions['width'] ?? '' }}" placeholder="0.00"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                            </div>

                            <div>
                                <label for="dimension_height" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    Height (cm)
                                </label>
                                <input type="number" name="dimensions[height]" id="dimension_height" step="0.01" min="0"
                                    value="{{ $dimensions['height'] ?? '' }}" placeholder="0.00"
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
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Publication Status
                            </label>
                            <select name="status" id="status"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                                <option value="draft" {{ old('status', $product->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status', $product->status) == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="archived" {{ old('status', $product->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1"
                                    {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <label for="is_active" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Active
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="is_featured" id="is_featured" value="1"
                                    {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <label for="is_featured" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Featured Product
                                </label>
                            </div>
                        </div>

                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
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

                        @if($product->sku)
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">SKU:</span>
                            <span class="font-medium font-mono">{{ $product->sku }}</span>
                        </div>
                        @endif

                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Current Status:</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                {{ $product->status === 'published' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 
                                   ($product->status === 'draft' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' : 
                                   'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100') }}">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            View Product
                        </a>

                        <button type="button" 
                                onclick="duplicateProduct({{ $product->id }})"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Duplicate Product
                        </button>

                        <button type="button" 
                                onclick="deleteProduct({{ $product->id }})"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-gray-800 dark:text-red-400 dark:border-red-600 dark:hover:bg-red-900/20">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Products
            </a>

            <div class="flex space-x-3">
                <button type="button" onclick="saveAsDraft()" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a.997.997 0 01-1.414 0l-7-7A1.997 1.997 0 013 12V7a4 4 0 014-4z" />
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
            document.addEventListener('DOMContentLoaded', function() {
                // Global variables to track uploaded images
                window.uploadedImages = {
                    featured: null,
                    gallery: []
                };

                // Initialize form handlers
                initializePriceTypeToggle();
                initializeStockManagement();
                initializeSlugGeneration();
                loadExistingTempFiles();

                // Listen for universal uploader events
                document.addEventListener('files-uploaded', function(event) {
                    if (event.detail.component === 'product-images-uploader') {
                        handleTempUploadSuccess(event.detail);
                    }
                });

                document.addEventListener('file-deleted', function(event) {
                    if (event.detail.component === 'product-images-uploader') {
                        handleTempFileDelete(event.detail);
                    }
                });

                // Form submission handling
                document.getElementById('product-form').addEventListener('submit', function(e) {
                    console.log('Product form submitting...');

                    // Basic validation
                    const name = document.getElementById('name').value.trim();
                    const priceType = document.getElementById('price_type').value;
                    const price = document.getElementById('price').value;

                    if (!name) {
                        e.preventDefault();
                        showNotification('Please enter a product name.', 'error');
                        return;
                    }

                    if (priceType === 'fixed' && (!price || parseFloat(price) <= 0)) {
                        e.preventDefault();
                        showNotification('Please enter a valid price for fixed price products.', 'error');
                        return;
                    }

                    // Show loading state
                    const submitButton = e.target.querySelector('button[type="submit"]');
                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.innerHTML = `
                            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Updating Product...
                        `;
                    }
                });
            });

            // Price type toggle functionality
            function initializePriceTypeToggle() {
                const priceTypeSelect = document.getElementById('price_type');
                const pricingFields = document.getElementById('pricing-fields');
                
                function togglePricingFields() {
                    if (priceTypeSelect.value === 'fixed') {
                        pricingFields.style.display = 'grid';
                    } else {
                        pricingFields.style.display = 'none';
                    }
                }
                
                priceTypeSelect.addEventListener('change', togglePricingFields);
                togglePricingFields(); // Initialize on page load
            }

            // Stock management toggle
            function initializeStockManagement() {
                const manageStockCheckbox = document.getElementById('manage_stock');
                const stockFields = document.getElementById('stock-fields');
                
                function toggleStockFields() {
                    if (manageStockCheckbox.checked) {
                        stockFields.style.display = 'grid';
                    } else {
                        stockFields.style.display = 'none';
                    }
                }
                
                manageStockCheckbox.addEventListener('change', toggleStockFields);
                toggleStockFields(); // Initialize on page load
            }

            // Auto-generate slug from name
            function initializeSlugGeneration() {
                const nameInput = document.getElementById('name');
                const slugInput = document.getElementById('slug');
                
                nameInput.addEventListener('input', function() {
                    if (!slugInput.value || slugInput.dataset.autoGenerated !== 'false') {
                        const slug = this.value
                            .toLowerCase()
                            .replace(/[^a-z0-9\s-]/g, '')
                            .replace(/\s+/g, '-')
                            .replace(/-+/g, '-')
                            .trim('-');
                        
                        slugInput.value = slug;
                        slugInput.dataset.autoGenerated = 'true';
                    }
                });

                slugInput.addEventListener('input', function() {
                    this.dataset.autoGenerated = 'false';
                });
            }

            // Specification management
            let specificationIndex = {{ old('specifications', $product->specifications) ? count(old('specifications', $product->specifications)) : 0 }};

            function addSpecification() {
                const container = document.getElementById('specifications-container');
                const newRow = document.createElement('div');
                newRow.className = 'flex gap-3 specification-row';
                newRow.innerHTML = `
                    <input type="text" name="specifications[${specificationIndex}][key]" 
                           placeholder="Specification name"
                           class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                    <input type="text" name="specifications[${specificationIndex}][value]" 
                           placeholder="Specification value"
                           class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                    <button type="button" onclick="removeSpecification(this)" 
                            class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                `;
                container.appendChild(newRow);
                specificationIndex++;
            }

            function removeSpecification(button) {
                button.closest('.specification-row').remove();
            }

            // Temporary image handling
            function handleTempUploadSuccess(detail) {
                console.log('Temp upload success:', detail);
                // The universal uploader component handles display
            }

            function handleTempFileDelete(detail) {
                console.log('Temp file deleted:', detail);
                // The universal uploader component handles removal
            }

            function loadExistingTempFiles() {
                fetch('{{ route("admin.products.temp-files") }}')
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            // Populate the uploader with existing temp files
                            const event = new CustomEvent('load-temp-files', {
                                detail: { files: data, component: 'product-images-uploader' }
                            });
                            document.dispatchEvent(event);
                        }
                    })
                    .catch(error => console.error('Error loading temp files:', error));
            }

            // Existing image management
            function deleteExistingImage(imagePath, type) {
                if (!confirm('Are you sure you want to delete this image?')) {
                    return;
                }

                fetch('{{ route("admin.products.delete-image", $product) }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        image_path: imagePath,
                        type: type
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the image element from the DOM
                        event.target.closest('.relative').remove();
                        showNotification('Image deleted successfully!', 'success');
                    } else {
                        showNotification('Failed to delete image.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Failed to delete image.', 'error');
                });
            }

            // Quick actions
            function saveAsDraft() {
                document.getElementById('status').value = 'draft';
                document.getElementById('product-form').submit();
            }

            function duplicateProduct(productId) {
                if (!confirm('Are you sure you want to duplicate this product?')) {
                    return;
                }

                fetch(`{{ route('admin.products.duplicate', '') }}/${productId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Product duplicated successfully!', 'success');
                        if (data.redirect) {
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 1500);
                        }
                    } else {
                        showNotification(data.message || 'Failed to duplicate product.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Failed to duplicate product.', 'error');
                });
            }

            function deleteProduct(productId) {
                if (!confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
                    return;
                }

                fetch(`{{ route('admin.products.destroy', '') }}/${productId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Product deleted successfully!', 'success');
                        setTimeout(() => {
                            window.location.href = '{{ route("admin.products.index") }}';
                        }, 1500);
                    } else {
                        showNotification(data.message || 'Failed to delete product.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Failed to delete product.', 'error');
                });
            }

            // Utility function for notifications
            function showNotification(message, type = 'info') {
                // This should integrate with your notification system
                // For now, using a simple alert
                if (type === 'error') {
                    alert('Error: ' + message);
                } else {
                    alert(message);
                }
            }
        </script>
    @endpush
</x-layouts.admin>