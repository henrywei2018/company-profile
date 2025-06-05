<x-layouts.admin title="Edit Post: {{ $post->title }}">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Posts' => route('admin.posts.index'), 'Edit Post' => '']" />

        <form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data" class="space-y-2">
            @csrf
            @method('PUT')  {{-- This is the correct way to send PUT requests in Laravel --}}
            
            {{-- Rest of your form content stays the same --}}
            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Main Content -->
                <div class="flex-1 space-y-6">
                    <!-- Basic Information -->
                    <x-admin.card>
                        <x-slot name="header">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Basic Information</h3>
                        </x-slot>
        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    Post Title <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="title" 
                                       id="title"
                                       value="{{ old('title', $post->title) }}"
                                       placeholder="Enter post title..."
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('title') border-red-500 @enderror"
                                       required>
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="md:col-span-2">
                                <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    URL Slug
                                </label>
                                <input type="text" 
                                       name="slug" 
                                       id="slug"
                                       value="{{ old('slug', $post->slug) }}"
                                       placeholder="Auto-generated from title"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('slug') border-red-500 @enderror">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave blank to auto-generate from title</p>
                                @error('slug')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="md:col-span-2">
                                <label for="excerpt" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    Excerpt
                                </label>
                                <textarea name="excerpt" 
                                          id="excerpt"
                                          rows="3"
                                          placeholder="Brief description of the post..."
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('excerpt') border-red-500 @enderror">{{ old('excerpt', $post->excerpt) }}</textarea>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Optional short summary that appears in post listings</p>
                                @error('excerpt')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </x-admin.card>
        
                    <!-- Content -->
                    <x-admin.card>
                        <x-slot name="header">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Content</h3>
                        </x-slot>
        
                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Post Content <span class="text-red-500">*</span>
                            </label>
                            <textarea name="content" 
                                      id="content"
                                      rows="15"
                                      placeholder="Start writing your post..."
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm resize-y focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('content') border-red-500 @enderror"
                                      required>{{ old('content', $post->content) }}</textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </x-admin.card>
        
                    <!-- SEO Settings -->
                    <x-admin.card>
                        <x-slot name="header">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">SEO Settings</h3>
                        </x-slot>
        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label for="seo_title" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    Meta Title
                                </label>
                                <input type="text" 
                                       name="seo_title" 
                                       id="seo_title"
                                       value="{{ old('seo_title', $post->seo->title ?? '') }}"
                                       placeholder="SEO optimized title"
                                       maxlength="60"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('seo_title') border-red-500 @enderror">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave blank to use post title (max 60 characters)</p>
                                @error('seo_title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="md:col-span-2">
                                <label for="seo_description" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    Meta Description
                                </label>
                                <textarea name="seo_description" 
                                          id="seo_description"
                                          rows="3"
                                          placeholder="Brief description for search engines..."
                                          maxlength="160"
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('seo_description') border-red-500 @enderror">{{ old('seo_description', $post->seo->description ?? '') }}</textarea>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Recommended length: 150-160 characters</p>
                                @error('seo_description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="md:col-span-2">
                                <label for="seo_keywords" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    Meta Keywords
                                </label>
                                <input type="text" 
                                       name="seo_keywords" 
                                       id="seo_keywords"
                                       value="{{ old('seo_keywords', $post->seo->keywords ?? '') }}"
                                       placeholder="keyword1, keyword2, keyword3"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('seo_keywords') border-red-500 @enderror">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Comma-separated keywords</p>
                                @error('seo_keywords')
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
                        <x-slot name="header">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Publishing</h3>
                        </x-slot>
        
                        <div class="space-y-4">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select name="status" 
                                        id="status"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('status') border-red-500 @enderror"
                                        required>
                                    <option value="draft" {{ old('status', $post->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ old('status', $post->status) === 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="archived" {{ old('status', $post->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="published_at" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    Publish Date
                                </label>
                                <input type="datetime-local" 
                                       name="published_at" 
                                       id="published_at"
                                       value="{{ old('published_at', $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '') }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('published_at') border-red-500 @enderror">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave blank to publish immediately</p>
                                @error('published_at')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="featured" 
                                           value="1"
                                           {{ old('featured', $post->featured) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Featured Post</span>
                                </label>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Featured posts appear prominently on the website</p>
                            </div>
                        </div>
                    </x-admin.card>
        
                    <!-- Categories - FIXED for pivot table -->
                    <x-admin.card>
                        <x-slot name="header">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Categories</h3>
                        </x-slot>
        
                        <div class="space-y-3 max-h-60 overflow-y-auto">
                            @php
                                // FIXED: Get selected categories properly from pivot table
                                $selectedCategories = old('categories', $post->categories->pluck('id')->toArray());
                            @endphp
                            @forelse($categories as $category)
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="categories[]" 
                                           value="{{ $category->id }}"
                                           {{ in_array($category->id, $selectedCategories) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $category->name }}</span>
                                </label>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400">No categories available.</p>
                                <a href="{{ route('admin.post-categories.create') }}" class="text-sm text-blue-600 hover:underline">
                                    Create your first category
                                </a>
                            @endforelse
                        </div>
                        @error('categories')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </x-admin.card>
        
                    <!-- Featured Image -->
                    <x-admin.card>
                        <x-slot name="header">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Featured Image</h3>
                        </x-slot>
        
                        <div>
                            <!-- Current Image -->
                            @if($post->featured_image_url)
                                <div id="current-image" class="mb-4">
                                    <img src="{{ $post->featured_image_url }}" 
                                         alt="{{ $post->title }}" 
                                         class="w-full h-32 object-cover rounded-lg">
                                    <div class="flex justify-between items-center mt-2">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Current image</span>
                                        <form method="POST" action="{{ route('admin.posts.remove-image', $post) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    onclick="return confirm('Are you sure you want to remove this image?')"
                                                    class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                                Remove
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif
        
                            <label for="featured_image" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                {{ $post->featured_image_url ? 'Replace Image' : 'Upload Image' }}
                            </label>
                            <input type="file" 
                                   name="featured_image" 
                                   id="featured_image"
                                   accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('featured_image') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Recommended size: 1200x630px (max 2MB)</p>
                            @error('featured_image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            
                            <!-- Image Preview -->
                            <div id="image-preview" class="mt-3 hidden">
                                <img id="preview-img" src="" alt="Preview" class="w-full h-32 object-cover rounded-lg">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">New image preview</p>
                            </div>
                        </div>
                    </x-admin.card>
                </div>
            </div>
        
            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.posts.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Posts
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
                        Update Post
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

            // Check if slug was manually set
            if (slugInput.value && slugInput.value !== '') {
                slugModified = true;
            }

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

            // Image preview
            const imageInput = document.getElementById('featured_image');
            const imagePreview = document.getElementById('image-preview');
            const previewImg = document.getElementById('preview-img');
            const currentImage = document.getElementById('current-image');

            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        imagePreview.classList.remove('hidden');
                        
                        // Hide current image when new one is selected
                        if (currentImage) {
                            currentImage.style.opacity = '0.5';
                        }
                    };
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.classList.add('hidden');
                    
                    // Restore current image opacity
                    if (currentImage) {
                        currentImage.style.opacity = '1';
                    }
                }
            });

            // Character counters
            const seoTitleInput = document.getElementById('seo_title');
            const seoDescInput = document.getElementById('seo_description');

            function updateCharCount(input, max) {
                const current = input.value.length;
                let counter = input.parentNode.querySelector('.char-counter');
                
                if (!counter) {
                    counter = document.createElement('span');
                    counter.className = 'char-counter text-xs text-gray-500 dark:text-gray-400';
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

            if (seoTitleInput) {
                seoTitleInput.addEventListener('input', function() {
                    updateCharCount(this, 60);
                });
                updateCharCount(seoTitleInput, 60);
            }

            if (seoDescInput) {
                seoDescInput.addEventListener('input', function() {
                    updateCharCount(this, 160);
                });
                updateCharCount(seoDescInput, 160);
            }

            // Auto-save draft functionality (optional)
            let autoSaveTimeout;
            const form = document.querySelector('form');
            const inputs = form.querySelectorAll('input, textarea, select');
            
            function autoSave() {
                // Implement auto-save logic here if needed
                console.log('Auto-saving draft...');
            }
            
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    clearTimeout(autoSaveTimeout);
                    autoSaveTimeout = setTimeout(autoSave, 5000); // Auto-save after 5 seconds of inactivity
                });
            });

            // Warn before leaving page with unsaved changes
            let formChanged = false;
            
            inputs.forEach(input => {
                input.addEventListener('change', function() {
                    formChanged = true;
                });
            });
            
            window.addEventListener('beforeunload', function(e) {
                if (formChanged) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
            
            // Don't warn when form is submitted
            form.addEventListener('submit', function() {
                formChanged = false;
            });
        });
    </script>
    @endpush
</x-layouts.admin>