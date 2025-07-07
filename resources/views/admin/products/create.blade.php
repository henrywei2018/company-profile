<x-layouts.admin title="Create New Product">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Products' => route('admin.products.index'), 'Create New Product' => '']" />

    <form action="{{ route('admin.products.store') }}" method="POST" class="space-y-6" id="product-form">
        @csrf

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
                            <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Enter product name..."
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
                            <input type="text" name="sku" id="sku" value="{{ old('sku') }}" placeholder="Product SKU..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('sku') border-red-500 @enderror">
                            @error('sku')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                URL Slug
                            </label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug') }}" placeholder="Auto-generated from name"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('slug') border-red-500 @enderror">
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="short_description" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Short Description
                            </label>
                            <textarea name="short_description" id="short_description" rows="3" placeholder="Brief product description..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('short_description') border-red-500 @enderror">{{ old('short_description') }}</textarea>
                            @error('short_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Description
                            </label>
                            <textarea name="description" id="description" rows="6" placeholder="Detailed product description..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
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
                                    <option value="{{ $category->id }}" {{ old('product_category_id') == $category->id ? 'selected' : '' }}>
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
                                    <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                        {{ $service->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('service_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="brand" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Brand
                            </label>
                            <input type="text" name="brand" id="brand" value="{{ old('brand') }}" placeholder="Product brand..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('brand') border-red-500 @enderror">
                            @error('brand')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="model" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Model
                            </label>
                            <input type="text" name="model" id="model" value="{{ old('model') }}" placeholder="Product model..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('model') border-red-500 @enderror">
                            @error('model')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>

                <!-- Pricing -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pricing</h3>
                    </x-slot>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="price_type" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Price Type <span class="text-red-500">*</span>
                            </label>
                            <select name="price_type" id="price_type" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('price_type') border-red-500 @enderror">
                                <option value="fixed" {{ old('price_type', 'fixed') === 'fixed' ? 'selected' : '' }}>Fixed Price</option>
                                <option value="quote" {{ old('price_type') === 'quote' ? 'selected' : '' }}>Request Quote</option>
                                <option value="contact" {{ old('price_type') === 'contact' ? 'selected' : '' }}>Contact for Price</option>
                            </select>
                            @error('price_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="price-field">
                            <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Price (IDR)
                            </label>
                            <input type="number" name="price" id="price" value="{{ old('price') }}" placeholder="0" step="0.01" min="0"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('price') border-red-500 @enderror">
                            @error('price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="sale-price-field">
                            <label for="sale_price" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Sale Price (IDR)
                            </label>
                            <input type="number" name="sale_price" id="sale_price" value="{{ old('sale_price') }}" placeholder="0" step="0.01" min="0"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('sale_price') border-red-500 @enderror">
                            @error('sale_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>

                <!-- Stock Management -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Stock Management</h3>
                    </x-slot>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="stock_status" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Stock Status <span class="text-red-500">*</span>
                            </label>
                            <select name="stock_status" id="stock_status" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('stock_status') border-red-500 @enderror">
                                <option value="in_stock" {{ old('stock_status', 'in_stock') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                                <option value="out_of_stock" {{ old('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                                <option value="on_backorder" {{ old('stock_status') === 'on_backorder' ? 'selected' : '' }}>On Backorder</option>
                            </select>
                            @error('stock_status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="flex items-center mb-2">
                                <input type="checkbox" name="manage_stock" id="manage_stock" value="1" {{ old('manage_stock') ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Manage Stock</span>
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Enable stock quantity tracking</p>
                        </div>

                        <div id="stock-quantity-field" style="display: none;">
                            <label for="stock_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Stock Quantity
                            </label>
                            <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', 0) }}" min="0"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('stock_quantity') border-red-500 @enderror">
                            @error('stock_quantity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>

                <!-- Product Images -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Product Images</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Upload featured image and gallery images</p>
                    </x-slot>

                    <!-- Universal File Uploader for Product Images -->
                    <x-universal-file-uploader :id="'product-images-uploader-' . uniqid()" name="product_images" :multiple="true"
                        :maxFiles="10" maxFileSize="5MB" :acceptedFileTypes="['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']" 
                        :uploadEndpoint="route('admin.products.temp-upload')" :deleteEndpoint="route('admin.products.temp-delete')"
                        dropDescription="Drop product images here or click to browse" :enableCategories="true"
                        :categories="[
                            ['value' => 'featured', 'label' => 'Featured Image'],
                            ['value' => 'gallery', 'label' => 'Gallery Image'],
                        ]" :autoUpload="true" :galleryMode="true" containerClass="mb-4" theme="modern" />

                    <!-- Hidden inputs to store temp file data -->
                    <div id="temp-files-data"></div>
                </x-admin.card>

                <!-- Specifications -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Specifications</h3>
                    </x-slot>

                    <div class="space-y-6">
                        <!-- Weight -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    Weight (kg)
                                </label>
                                <input type="number" name="weight" id="weight" value="{{ old('weight') }}" placeholder="0.00" step="0.01" min="0"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('weight') border-red-500 @enderror">
                                @error('weight')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Dimensions -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Dimensions
                            </label>
                            <div class="grid grid-cols-4 gap-4">
                                <div>
                                    <input type="number" name="dimensions[length]" placeholder="Length" step="0.01" min="0" value="{{ old('dimensions.length') }}"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                                </div>
                                <div>
                                    <input type="number" name="dimensions[width]" placeholder="Width" step="0.01" min="0" value="{{ old('dimensions.width') }}"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                                </div>
                                <div>
                                    <input type="number" name="dimensions[height]" placeholder="Height" step="0.01" min="0" value="{{ old('dimensions.height') }}"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                                </div>
                                <div>
                                    <select name="dimensions[unit]" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                                        <option value="">Unit</option>
                                        <option value="cm" {{ old('dimensions.unit') === 'cm' ? 'selected' : '' }}>cm</option>
                                        <option value="m" {{ old('dimensions.unit') === 'm' ? 'selected' : '' }}>m</option>
                                        <option value="mm" {{ old('dimensions.unit') === 'mm' ? 'selected' : '' }}>mm</option>
                                        <option value="inch" {{ old('dimensions.unit') === 'inch' ? 'selected' : '' }}>inch</option>
                                        <option value="ft" {{ old('dimensions.unit') === 'ft' ? 'selected' : '' }}>ft</option>
                                    </select>
                                </div>
                            </div>
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
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" id="status" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('status') border-red-500 @enderror">
                                <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                                <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                            </label>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Make this product active</p>
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Featured</span>
                            </label>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Show in featured products</p>
                        </div>

                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Sort Order
                            </label>
                            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order') }}" placeholder="Auto-assigned if empty" min="0"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('sort_order') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Lower numbers appear first</p>
                            @error('sort_order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('admin.products.index') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
                Create Product
            </button>
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
                        document.getElementById('name').focus();
                        return;
                    }

                    if (priceType === 'fixed' && !price) {
                        e.preventDefault();
                        showNotification('Please enter a price for fixed price products.', 'error');
                        document.getElementById('price').focus();
                        return;
                    }

                    console.log('Product form validation passed, submitting...');
                });
            });

            function initializePriceTypeToggle() {
                const priceTypeSelect = document.getElementById('price_type');
                const priceField = document.getElementById('price-field');
                const salePriceField = document.getElementById('sale-price-field');

                function togglePriceFields() {
                    const priceType = priceTypeSelect.value;
                    
                    if (priceType === 'fixed') {
                        priceField.style.display = 'block';
                        salePriceField.style.display = 'block';
                        document.getElementById('price').required = true;
                    } else {
                        priceField.style.display = 'none';
                        salePriceField.style.display = 'none';
                        document.getElementById('price').required = false;
                        document.getElementById('price').value = '';
                        document.getElementById('sale_price').value = '';
                    }
                }

                priceTypeSelect.addEventListener('change', togglePriceFields);
                togglePriceFields(); // Initialize on page load
            }

            function initializeStockManagement() {
                const manageStockCheckbox = document.getElementById('manage_stock');
                const stockQuantityField = document.getElementById('stock-quantity-field');

                function toggleStockQuantity() {
                    if (manageStockCheckbox.checked) {
                        stockQuantityField.style.display = 'block';
                        document.getElementById('stock_quantity').required = true;
                    } else {
                        stockQuantityField.style.display = 'none';
                        document.getElementById('stock_quantity').required = false;
                    }
                }

                manageStockCheckbox.addEventListener('change', toggleStockQuantity);
                toggleStockQuantity(); // Initialize on page load
            }

            function initializeSlugGeneration() {
                const nameInput = document.getElementById('name');
                const slugInput = document.getElementById('slug');
                let slugModified = false;

                nameInput.addEventListener('input', function() {
                    if (!slugModified) {
                        const name = this.value;
                        const slug = name.toLowerCase()
                            .replace(/[^a-z0-9 -]/g, '')
                            .replace(/\s+/g, '-')
                            .replace(/-+/g, '-')
                            .replace(/^-|-$/g, '');
                        slugInput.value = slug;
                    }
                });

                slugInput.addEventListener('input', function() {
                    slugModified = true;
                });
            }

            function loadExistingTempFiles() {
                fetch('{{ route('admin.products.temp-files') }}', {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.files.length > 0) {
                            data.files.forEach(file => {
                                if (file.category === 'featured') {
                                    window.uploadedImages.featured = file.url;
                                } else if (file.category === 'gallery') {
                                    window.uploadedImages.gallery.push(file.url);
                                }
                            });
                        }
                    })
                    .catch(error => {
                        console.warn('Could not load existing temp files:', error);
                    });
            }

            function handleTempUploadSuccess(detail) {
                const files = detail.files || [];

                files.forEach(file => {
                    if (file.category === 'featured') {
                        window.uploadedImages.featured = file.url;
                    } else if (file.category === 'gallery') {
                        window.uploadedImages.gallery.push(file.url);
                    }
                });

                showNotification(detail.message || 'Images uploaded successfully!', 'success');
            }

            function handleTempFileDelete(detail) {
                const file = detail.file;

                if (file.category === 'featured') {
                    window.uploadedImages.featured = null;
                } else if (file.category === 'gallery') {
                    const index = window.uploadedImages.gallery.indexOf(file.url);
                    if (index > -1) {
                        window.uploadedImages.gallery.splice(index, 1);
                    }
                }
            }

            function showNotification(message, type = 'info') {
                const notification = document.createElement('div');
                notification.className = 
                    `fixed top-4 right-4 z-50 max-w-sm w-full shadow-lg rounded-lg p-4 ${getNotificationClasses(type)} transform transition-all duration-300 ease-in-out`;
                notification.innerHTML = `
                    <div class="flex">
                        <div class="flex-shrink-0">
                            ${getNotificationIcon(type)}
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium">${message}</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <button onclick="this.closest('.fixed').remove()" class="inline-flex text-current hover:opacity-75">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                `;

                document.body.appendChild(notification);
                setTimeout(() => notification?.remove(), 5000);
            }

            function getNotificationClasses(type) {
                const classes = {
                    success: 'bg-green-50 border border-green-200 text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-400',
                    error: 'bg-red-50 border border-red-200 text-red-800 dark:bg-red-900/20 dark:border-red-800 dark:text-red-400',
                    warning: 'bg-yellow-50 border border-yellow-200 text-yellow-800 dark:bg-yellow-900/20 dark:border-yellow-800 dark:text-yellow-400',
                    info: 'bg-blue-50 border border-blue-200 text-blue-800 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-400'
                };
                return classes[type] || classes.info;
            }

            function getNotificationIcon(type) {
                const icons = {
                    success: '<svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
                    error: '<svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
                    warning: '<svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>',
                    info: '<svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>'
                };
                return icons[type] || icons.info;
            }
        </script>
    @endpush
</x-layouts.admin>