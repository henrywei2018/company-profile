<!-- resources/views/admin/services/create.blade.php -->
<x-layouts.admin title="Add New Service" :unreadMessages="$unreadMessages" :pendingQuotations="$pendingQuotations">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'Services Management' => route('admin.services.index'),
            'Add New Service' => route('admin.services.create'),
        ]" />
    </div>

    <form action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Main Content -->
            <div class="flex-1 space-y-6">
                <!-- Basic Information -->
                <x-admin.card>
                    <x-slot name="title">Basic Information</x-slot>
                    <x-slot name="subtitle">Enter the basic details of the service</x-slot>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Service Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="title" 
                                   id="title"
                                   value="{{ old('title') }}"
                                   placeholder="Enter service title..."
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('title') border-red-500 @enderror"
                                   required>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Category
                            </label>
                            <select name="category_id" 
                                    id="category_id"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('category_id') border-red-500 @enderror">
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                URL Slug
                            </label>
                            <input type="text" 
                                   name="slug" 
                                   id="slug"
                                   value="{{ old('slug') }}"
                                   placeholder="Auto-generated from title"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('slug') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave blank to auto-generate from title</p>
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="short_description" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Short Description
                            </label>
                            <textarea name="short_description" 
                                      id="short_description"
                                      rows="3"
                                      placeholder="Brief description of the service (max 255 characters)..."
                                      maxlength="255"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('short_description') border-red-500 @enderror">{{ old('short_description') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Optional short summary that appears in service listings</p>
                            @error('short_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>

                <!-- Content -->
                <x-admin.card>
                    <x-slot name="title">Service Content</x-slot>
                    <x-slot name="subtitle">Detailed description and information</x-slot>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Full Description <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" 
                                  id="description"
                                  rows="15"
                                  placeholder="Start writing your service description..."
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm resize-y focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('description') border-red-500 @enderror"
                                  required>{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </x-admin.card>

                <!-- SEO Settings -->
                <x-admin.card>
                    <x-slot name="title">SEO Settings</x-slot>
                    <x-slot name="subtitle">Optimize service for search engines</x-slot>

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Meta Title
                            </label>
                            <input type="text" 
                                   name="meta_title" 
                                   id="meta_title"
                                   value="{{ old('meta_title') }}"
                                   placeholder="SEO optimized title"
                                   maxlength="60"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('meta_title') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave blank to use service title (max 60 characters)</p>
                            @error('meta_title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Meta Description
                            </label>
                            <textarea name="meta_description" 
                                      id="meta_description"
                                      rows="3"
                                      placeholder="Brief description for search engines..."
                                      maxlength="160"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('meta_description') border-red-500 @enderror">{{ old('meta_description') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Recommended length: 150-160 characters</p>
                            @error('meta_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="meta_keywords" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Meta Keywords
                            </label>
                            <input type="text" 
                                   name="meta_keywords" 
                                   id="meta_keywords"
                                   value="{{ old('meta_keywords') }}"
                                   placeholder="keyword1, keyword2, keyword3"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('meta_keywords') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Comma-separated keywords</p>
                            @error('meta_keywords')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>
            </div>

            <!-- Sidebar -->
            <div class="w-full lg:w-80 space-y-6">
                <!-- Publishing Options -->
                <x-admin.card>
                    <x-slot name="title">Publishing</x-slot>
                    <x-slot name="subtitle">Service status and visibility</x-slot>

                    <div class="space-y-4">
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active Service</span>
                            </label>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Service will be visible on the website</p>
                        </div>
                        
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="featured" 
                                       value="1"
                                       {{ old('featured') ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Featured Service</span>
                            </label>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Featured services appear prominently on the website</p>
                        </div>
                        
                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Sort Order
                            </label>
                            <input type="number" 
                                   name="sort_order" 
                                   id="sort_order"
                                   value="{{ old('sort_order', 0) }}"
                                   placeholder="0"
                                   min="0"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('sort_order') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Lower numbers appear first</p>
                            @error('sort_order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>

                <!-- Service Icon -->
                <x-admin.card>
                    <x-slot name="title">Service Icon</x-slot>
                    <x-slot name="subtitle">Small icon for service</x-slot>

                    <div>
                        <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Upload Icon
                        </label>
                        <input type="file" 
                               name="icon" 
                               id="icon"
                               accept="image/jpeg,image/png,image/jpg,image/svg"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('icon') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Square icon. Recommended size: 128x128px (max 1MB)</p>
                        @error('icon')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        
                        <!-- Icon Preview -->
                        <div id="icon-preview" class="mt-3 hidden">
                            <img id="preview-icon" src="" alt="Preview" class="w-16 h-16 object-cover rounded-lg border border-gray-200 dark:border-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Icon preview</p>
                        </div>
                    </div>
                </x-admin.card>

                <!-- Service Image -->
                <x-admin.card>
                    <x-slot name="title">Service Image</x-slot>
                    <x-slot name="subtitle">Main service image</x-slot>

                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Upload Image
                        </label>
                        <input type="file" 
                               name="image" 
                               id="image"
                               accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('image') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Recommended size: 1200x800px (max 2MB)</p>
                        @error('image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        
                        <!-- Image Preview -->
                        <div id="image-preview" class="mt-3 hidden">
                            <img id="preview-img" src="" alt="Preview" class="w-full h-32 object-cover rounded-lg border border-gray-200 dark:border-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Image preview</p>
                        </div>
                    </div>
                </x-admin.card>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700 mt-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.services.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Services
                </a>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" 
                        name="action" 
                        value="save"
                        class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    Create Service
                </button>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-generate slug from title
            const titleInput = document.getElementById('title');
            const slugInput = document.getElementById('slug');
            let slugModified = false;

            titleInput.addEventListener('input', function() {
                if (!slugModified) {
                    const title = this.value;
                    const slug = title.toLowerCase()
                        .replace(/[^a-z0-9 -]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-')
                        .replace(/^-|-$/g, '');
                    
                    slugInput.value = slug;
                }
            });
            
            // Mark slug as manually modified
            slugInput.addEventListener('input', function() {
                slugModified = true;
            });

            // Icon preview
            const iconInput = document.getElementById('icon');
            const iconPreview = document.getElementById('icon-preview');
            const previewIcon = document.getElementById('preview-icon');

            iconInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewIcon.src = e.target.result;
                        iconPreview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                    iconPreview.classList.add('hidden');
                }
            });

            // Image preview
            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('image-preview');
            const previewImg = document.getElementById('preview-img');

            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        imagePreview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.classList.add('hidden');
                }
            });

            // Character counters
            const metaTitleInput = document.getElementById('meta_title');
            const metaDescInput = document.getElementById('meta_description');
            const shortDescInput = document.getElementById('short_description');

            function updateCharCount(input, max) {
                const current = input.value.length;
                let counter = input.parentNode.querySelector('.char-counter');
                
                if (!counter) {
                    counter = document.createElement('span');
                    counter.className = 'char-counter text-xs text-gray-500 dark:text-gray-400 block mt-1';
                    input.parentNode.appendChild(counter);
                }
                
                counter.textContent = `${current}/${max} characters`;
                
                if (current > max) {
                    counter.classList.add('text-red-500');
                    counter.classList.remove('text-gray-500', 'dark:text-gray-400');
                } else {
                    counter.classList.remove('text-red-500');
                    counter.classList.add('text-gray-500', 'dark:text-gray-400');
                }
            }

            if (metaTitleInput) {
                metaTitleInput.addEventListener('input', function() {
                    updateCharCount(this, 60);
                });
                updateCharCount(metaTitleInput, 60);
            }

            if (metaDescInput) {
                metaDescInput.addEventListener('input', function() {
                    updateCharCount(this, 160);
                });
                updateCharCount(metaDescInput, 160);
            }

            if (shortDescInput) {
                shortDescInput.addEventListener('input', function() {
                    updateCharCount(this, 255);
                });
                updateCharCount(shortDescInput, 255);
            }
        });
    </script>
    @endpush
</x-layouts.admin>