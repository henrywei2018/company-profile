{{-- resources/views/admin/product-categories/create.blade.php --}}
<x-layouts.admin title="Create New Product Category">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Product Categories' => route('admin.product-categories.index'), 
        'Create New Category' => ''
    ]" />

    <form action="{{ route('admin.product-categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="category-form">
        @csrf

        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Main Content -->
            <div class="flex-1 space-y-6">
                <!-- Basic Information -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Category Information</h3>
                    </x-slot>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Category Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Enter category name..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('name') border-red-500 @enderror"
                                required>
                            @error('name')
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
                            <p class="mt-1 text-xs text-gray-500">Leave empty to auto-generate from category name</p>
                        </div>

                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Sort Order
                            </label>
                            <input type="number" name="sort_order" id="sort_order" min="0" value="{{ old('sort_order') }}" placeholder="0"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('sort_order') border-red-500 @enderror">
                            @error('sort_order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Lower numbers appear first</p>
                        </div>

                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Description
                            </label>
                            <textarea name="description" id="description" rows="4" placeholder="Describe this category..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>

                <!-- Hierarchy & Relationships -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Category Hierarchy & Relationships</h3>
                    </x-slot>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="parent_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Parent Category
                            </label>
                            <select name="parent_id" id="parent_id"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('parent_id') border-red-500 @enderror">
                                <option value="">None (Root Category)</option>
                                @foreach ($parentCategories as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Select a parent to create a subcategory</p>
                        </div>

                        <div>
                            <label for="service_category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Service Category
                            </label>
                            <select name="service_category_id" id="service_category_id"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('service_category_id') border-red-500 @enderror">
                                <option value="">Select a service category</option>
                                @foreach ($serviceCategories as $serviceCategory)
                                    <option value="{{ $serviceCategory->id }}" {{ old('service_category_id') == $serviceCategory->id ? 'selected' : '' }}>
                                        {{ $serviceCategory->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('service_category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Associate this category with a service</p>
                        </div>
                    </div>
                </x-admin.card>

                <!-- Category Icon -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Category Icon</h3>
                    </x-slot>

                    <div class="space-y-4">
                        <div>
                            <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Upload Icon
                            </label>
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div id="icon-preview" class="w-16 h-16 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center bg-gray-50 dark:bg-gray-700">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <input type="file" name="icon" id="icon" accept="image/*"
                                        class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-300 @error('icon') border-red-500 @enderror">
                                    @error('icon')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF, SVG up to 1MB. Recommended size: 64x64px</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-admin.card>
            </div>

            <!-- Sidebar -->
            <div class="lg:w-80 space-y-6">
                <!-- Category Status -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Category Status</h3>
                    </x-slot>

                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" 
                                {{ old('is_active', true) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <label for="is_active" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                                Active
                            </label>
                        </div>
                        <p class="text-xs text-gray-500">Inactive categories won't be visible to customers</p>
                    </div>
                </x-admin.card>

                <!-- Category Preview -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Category Preview</h3>
                    </x-slot>

                    <div class="space-y-4" id="category-preview">
                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0" id="preview-icon">
                                    <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white" id="preview-name">
                                        Category Name
                                    </h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" id="preview-slug">
                                        /category-slug
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2" id="preview-description">
                                        Category description will appear here...
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-admin.card>

                <!-- Category Guidelines -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Category Guidelines</h3>
                    </x-slot>

                    <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                        <div class="flex items-start space-x-2">
                            <svg class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Use clear, descriptive names that customers will understand</span>
                        </div>
                        <div class="flex items-start space-x-2">
                            <svg class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Keep category hierarchy simple (max 2 levels deep)</span>
                        </div>
                        <div class="flex items-start space-x-2">
                            <svg class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Add icons to make categories more visual and recognizable</span>
                        </div>
                        <div class="flex items-start space-x-2">
                            <svg class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Link categories to relevant services for better organization</span>
                        </div>
                    </div>
                </x-admin.card>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('admin.product-categories.index') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Categories
            </a>

            <div class="flex space-x-3">
                <button type="button" onclick="saveAsDraft()" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a.997.997 0 01-1.414 0l-7-7A1.997 1.997 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    Save as Inactive
                </button>

                <button type="submit" 
                        class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Create Category
                </button>
            </div>
        </div>
    </form>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize form handlers
                initializeSlugGeneration();
                initializeIconPreview();
                initializeLivePreview();

                // Form submission handling
                document.getElementById('category-form').addEventListener('submit', function(e) {
                    // Basic validation
                    const name = document.getElementById('name').value.trim();

                    if (!name) {
                        e.preventDefault();
                        alert('Please enter a category name.');
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
                            Creating Category...
                        `;
                    }
                });
            });

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
                        updatePreview();
                    }
                });

                slugInput.addEventListener('input', function() {
                    this.dataset.autoGenerated = 'false';
                    updatePreview();
                });

                // Also update preview when name changes
                nameInput.addEventListener('input', updatePreview);
                document.getElementById('description').addEventListener('input', updatePreview);
            }

            // Initialize icon preview
            function initializeIconPreview() {
                const iconInput = document.getElementById('icon');
                const iconPreview = document.getElementById('icon-preview');
                const previewIcon = document.getElementById('preview-icon');
                
                iconInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'w-full h-full object-cover rounded-lg';
                            
                            iconPreview.innerHTML = '';
                            iconPreview.appendChild(img);
                            
                            // Update preview icon as well
                            const previewImg = img.cloneNode();
                            previewImg.className = 'w-12 h-12 object-cover rounded-lg';
                            previewIcon.innerHTML = '';
                            previewIcon.appendChild(previewImg);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Initialize live preview
            function initializeLivePreview() {
                updatePreview(); // Initial preview
            }

            // Update category preview
            function updatePreview() {
                const name = document.getElementById('name').value || 'Category Name';
                const slug = document.getElementById('slug').value || 'category-slug';
                const description = document.getElementById('description').value || 'Category description will appear here...';
                
                document.getElementById('preview-name').textContent = name;
                document.getElementById('preview-slug').textContent = '/' + slug;
                document.getElementById('preview-description').textContent = description;
            }

            // Save as draft (inactive)
            function saveAsDraft() {
                document.getElementById('is_active').checked = false;
                document.getElementById('category-form').submit();
            }
        </script>
    @endpush
</x-layouts.admin>