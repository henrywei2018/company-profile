{{-- resources/views/admin/products/edit.blade.php --}}
<x-layouts.admin title="Edit Product: {{ $product->name }}">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Products' => route('admin.products.index'), 
        $product->name => route('admin.products.show', $product),
        'Edit' => ''
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
                            <div id="current-images-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
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
                            :uploadEndpoint="route('admin.products.temp-upload')" 
                            :deleteEndpoint="route('admin.products.temp-delete')" 
                            :maxFiles="10"
                            maxFileSize="5MB" 
                            :acceptedFileTypes="['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']" 
                            :id="'product-images-uploader'"
                            name="product_images"
                            dropDescription="Drop product images here or click to browse"
                            :multiple="true"
                            :galleryMode="true"
                            theme="modern"
                            containerClass="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6"
                        />

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

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                initializeProductEdit();
            });

            function initializeProductEdit() {
                // Initialize image management
                initializeImageHandlers();
                
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
            // UNIVERSAL FILE UPLOADER EVENTS
            // =========================================================================

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
                showNotification('Temporary file removed', 'info');
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

                // Form submission handler - FIXED
                const form = document.getElementById('product-form');
                const submitButton = document.getElementById('submit-button');
                
                if (form && submitButton) {
                    // Remove any existing event listeners that might prevent submission
                    form.removeEventListener('submit', preventFormSubmission);
                    
                    form.addEventListener('submit', function(e) {
                        console.log('Form submitting...'); // Debug log
                        
                        // Show loading state
                        if (submitButton) {
                            submitButton.disabled = true;
                            const buttonText = submitButton.querySelector('.button-text');
                            if (buttonText) {
                                buttonText.textContent = 'Updating...';
                            }
                        }
                        
                        // Don't prevent the form submission
                        // Let it proceed normally to the controller
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

            // Remove this function if it exists and might be preventing submission
            function preventFormSubmission(e) {
                e.preventDefault();
                return false;
            }

            function generateSlug(text) {
                return text.toLowerCase()
                    .replace(/[^\w\s-]/g, '') // Remove special characters
                    .replace(/[\s_-]+/g, '-') // Replace spaces and underscores with hyphens
                    .replace(/^-+|-+$/g, ''); // Remove leading/trailing hyphens
            }

            // =========================================================================
            // UTILITY FUNCTIONS
            // =========================================================================

            function makeImageRequest(action, data) {
                const productId = {{ $product->id }};
                
                return fetch(`/admin/products/${productId}/image-action`, {
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
</x-admin.card>