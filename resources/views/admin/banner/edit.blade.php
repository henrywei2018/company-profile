<x-layouts.admin title="Edit Banner: {{ $banner->title }}">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Banners' => route('admin.banners.index'), 'Edit Banner' => '']" />

    <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data" class="space-y-6" x-data="bannerEditForm()">
        @csrf
        @method('PUT')
        
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
                                Banner Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="title" 
                                   id="title"
                                   value="{{ old('title', $banner->title) }}"
                                   placeholder="Enter banner title..."
                                   x-model="formData.title"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('title') border-red-500 @enderror"
                                   required>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="subtitle" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Subtitle
                            </label>
                            <input type="text" 
                                   name="subtitle" 
                                   id="subtitle"
                                   value="{{ old('subtitle', $banner->subtitle) }}"
                                   placeholder="Enter banner subtitle..."
                                   x-model="formData.subtitle"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('subtitle') border-red-500 @enderror">
                            @error('subtitle')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Description
                            </label>
                            <textarea name="description" 
                                      id="description"
                                      rows="3"
                                      placeholder="Enter banner description..."
                                      x-model="formData.description"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('description') border-red-500 @enderror">{{ old('description', $banner->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>

                <!-- Call to Action -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Call to Action</h3>
                    </x-slot>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="button_text" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Button Text
                            </label>
                            <input type="text" 
                                   name="button_text" 
                                   id="button_text"
                                   value="{{ old('button_text', $banner->button_text) }}"
                                   placeholder="e.g., Learn More, Get Started..."
                                   maxlength="50"
                                   x-model="formData.button_text"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('button_text') border-red-500 @enderror">
                            @error('button_text')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="button_link" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Button Link
                            </label>
                            <input type="url" 
                                   name="button_link" 
                                   id="button_link"
                                   value="{{ old('button_link', $banner->button_link) }}"
                                   placeholder="https://example.com or /internal-page"
                                   x-model="formData.button_link"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('button_link') border-red-500 @enderror">
                            @error('button_link')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="open_in_new_tab" 
                                       value="1"
                                       {{ old('open_in_new_tab', $banner->open_in_new_tab) ? 'checked' : '' }}
                                       x-model="formData.open_in_new_tab"
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Open link in new tab</span>
                            </label>
                        </div>
                    </div>
                </x-admin.card>

                <!-- Banner Images with FilePond -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Banner Images</h3>
                    </x-slot>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Desktop Image -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-3">
                                Desktop Image
                            </label>
                             
                            <!-- Current Image Display -->
                            @if($banner->image)
                                <div class="mb-4 p-4 border-2 border-dashed border-blue-300 dark:border-blue-600 rounded-xl">
                                    <div class="text-center">
                                        <img src="{{ $banner->imageUrl }}" 
                                             alt="{{ $banner->title }}" 
                                             class="w-full h-32 object-cover rounded-lg mb-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Current desktop image</span>
                                            <button type="button" 
                                                    onclick="removeCurrentImage('desktop')"
                                                    class="text-sm text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium">
                                                Remove Current
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 hover:border-blue-400 transition-colors">
                                <div class="text-center">
                                    <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                        {{ $banner->image ? 'Replace Desktop Image' : 'Upload Desktop Image' }}
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                        Recommended: 1920x600px, Max 2MB<br>
                                        Formats: JPG, PNG, WebP
                                    </p>
                                </div>
                                
                                <!-- FilePond Image Upload -->
                                <div x-data="filepondUpload('desktop', false)" class="w-full">
                                    <input type="file" 
                                           x-ref="fileInput"
                                           name="desktop_image"
                                           accept="image/*"
                                           class="filepond"
                                           data-max-file-size="2MB">
                                    <input type="hidden" 
                                           name="desktop_image_filepond" 
                                           x-model="serverId">
                                </div>
                            </div>
                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Mobile Image -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-3">
                                Mobile Image (Optional)
                            </label>
                            
                            <!-- Current Mobile Image Display -->
                            @if($banner->mobile_image)
                                <div class="mb-4 p-4 border-2 border-dashed border-green-300 dark:border-green-600 rounded-xl">
                                    <div class="text-center">
                                        <img src="{{ $banner->mobileImageUrl }}" 
                                             alt="{{ $banner->title }}" 
                                             class="w-full h-32 object-cover rounded-lg mb-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Current mobile image</span>
                                            <button type="button" 
                                                    onclick="removeCurrentImage('mobile')"
                                                    class="text-sm text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium">
                                                Remove Current
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 hover:border-blue-400 transition-colors">
                                <div class="text-center">
                                    <div class="w-16 h-16 mx-auto mb-4 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                        {{ $banner->mobile_image ? 'Replace Mobile Image' : 'Upload Mobile Image' }}
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                        Recommended: 768x400px, Max 2MB<br>
                                        Formats: JPG, PNG, WebP
                                    </p>
                                </div>
                                
                                <!-- FilePond Mobile Image Upload -->
                                <div x-data="filepondUpload('mobile', false)" class="w-full">
                                    <input type="file" 
                                           x-ref="fileInput"
                                           name="mobile_image"
                                           accept="image/*"
                                           class="filepond"
                                           data-max-file-size="2MB">
                                    <input type="hidden" 
                                           name="mobile_image_filepond" 
                                           x-model="serverId">
                                </div>
                            </div>
                            @error('mobile_image')
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
                            <label for="banner_category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <select name="banner_category_id" 
                                    id="banner_category_id"
                                    x-model="formData.category_id"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('banner_category_id') border-red-500 @enderror"
                                    required>
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('banner_category_id', $banner->banner_category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('banner_category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="display_order" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Display Order
                            </label>
                            <input type="number" 
                                   name="display_order" 
                                   id="display_order"
                                   value="{{ old('display_order', $banner->display_order) }}"
                                   min="0"
                                   x-model="formData.display_order"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('display_order') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Lower numbers appear first</p>
                            @error('display_order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', $banner->is_active) ? 'checked' : '' }}
                                       x-model="formData.is_active"
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active Banner</span>
                            </label>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Only active banners will be displayed on the website</p>
                        </div>
                    </div>
                </x-admin.card>

                <!-- Schedule -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Schedule (Optional)</h3>
                    </x-slot>

                    <div class="space-y-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Start Date
                            </label>
                            <input type="datetime-local" 
                                   name="start_date" 
                                   id="start_date"
                                   value="{{ old('start_date', $banner->start_date ? $banner->start_date->format('Y-m-d\TH:i') : '') }}"
                                   x-model="formData.start_date"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('start_date') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">When to start showing this banner</p>
                            @error('start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                End Date
                            </label>
                            <input type="datetime-local" 
                                   name="end_date" 
                                   id="end_date"
                                   value="{{ old('end_date', $banner->end_date ? $banner->end_date->format('Y-m-d\TH:i') : '') }}"
                                   x-model="formData.end_date"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('end_date') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">When to stop showing this banner</p>
                            @error('end_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>

                <!-- Banner Statistics -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Banner Info</h3>
                    </x-slot>

                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Created</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $banner->created_at->format('M j, Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Last updated</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $banner->updated_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Current status</span>
                            <span class="text-sm font-medium">
                                @php
                                    $now = now();
                                    $isActive = $banner->is_active;
                                    $isScheduled = $banner->start_date && $banner->start_date > $now;
                                    $isExpired = $banner->end_date && $banner->end_date < $now;
                                    $isLive = $isActive && !$isScheduled && !$isExpired;
                                @endphp
                                
                                @if($isLive)
                                    <span class="text-green-600 dark:text-green-400">Live</span>
                                @elseif($isScheduled)
                                    <span class="text-yellow-600 dark:text-yellow-400">Scheduled</span>
                                @elseif($isExpired)
                                    <span class="text-red-600 dark:text-red-400">Expired</span>
                                @else
                                    <span class="text-gray-600 dark:text-gray-400">Inactive</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </x-admin.card>

                <!-- Live Preview -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Live Preview</h3>
                    </x-slot>

                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-6 text-white min-h-[200px] flex flex-col justify-end relative overflow-hidden"
                         :style="previewImageUrl ? `background-image: url('${previewImageUrl}'); background-size: cover; background-position: center;` : '{{ $banner->image ? "background-image: url('" . $banner->imageUrl . "'); background-size: cover; background-position: center;" : "" }}'">
                        <div class="bg-black bg-opacity-20 rounded p-4">
                            <p class="text-sm opacity-90 mb-1" x-text="formData.subtitle || '{{ $banner->subtitle }}'" x-show="formData.subtitle || '{{ $banner->subtitle }}'"></p>
                            <h3 class="text-lg font-bold mb-2" x-text="formData.title || '{{ $banner->title }}' || 'Your banner title will appear here'"></h3>
                            <p class="text-sm opacity-80 mb-4" x-text="formData.description || '{{ $banner->description }}'" x-show="formData.description || '{{ $banner->description }}'"></p>
                            <button type="button" 
                                    class="inline-flex items-center px-3 py-2 bg-white text-gray-900 rounded-lg text-sm font-medium"
                                    x-show="formData.button_text || '{{ $banner->button_text }}'"
                                    x-text="formData.button_text || '{{ $banner->button_text }}' || 'Button Text'">
                            </button>
                        </div>
                    </div>
                </x-admin.card>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.banners.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Banners
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
                    Update Banner
                </button>
            </div>
        </div>
    </form>

    <!-- Hidden forms for image removal -->
    <form id="remove-desktop-image-form" action="{{ route('admin.banners.remove-image', $banner) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
        <input type="hidden" name="image_type" value="desktop">
    </form>

    <form id="remove-mobile-image-form" action="{{ route('admin.banners.remove-image', $banner) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
        <input type="hidden" name="image_type" value="mobile">
    </form>

    @push('styles')
    <!-- FilePond CSS -->
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    @endpush

    @push('scripts')
    <!-- FilePond JS -->
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-resize/dist/filepond-plugin-image-resize.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-transform/dist/filepond-plugin-image-transform.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>

    <script>
        // Register FilePond plugins
        FilePond.registerPlugin(
            FilePondPluginFileValidateType,
            FilePondPluginFileValidateSize,
            FilePondPluginImagePreview,
            FilePondPluginImageResize,
            FilePondPluginImageTransform
        );

        // Banner edit form Alpine.js component
        function bannerEditForm() {
            return {
                formData: {
                    title: '{{ old("title", $banner->title) }}',
                    subtitle: '{{ old("subtitle", $banner->subtitle) }}',
                    description: '{{ old("description", $banner->description) }}',
                    button_text: '{{ old("button_text", $banner->button_text) }}',
                    button_link: '{{ old("button_link", $banner->button_link) }}',
                    open_in_new_tab: {{ old('open_in_new_tab', $banner->open_in_new_tab) ? 'true' : 'false' }},
                    category_id: '{{ old("banner_category_id", $banner->banner_category_id) }}',
                    display_order: {{ old('display_order', $banner->display_order) }},
                    is_active: {{ old('is_active', $banner->is_active) ? 'true' : 'false' }},
                    start_date: '{{ old("start_date", $banner->start_date ? $banner->start_date->format("Y-m-d\TH:i") : "") }}',
                    end_date: '{{ old("end_date", $banner->end_date ? $banner->end_date->format("Y-m-d\TH:i") : "") }}'
                },
                previewImageUrl: null,

                init() {
                    // Watch for date validation
                    this.$watch('formData.start_date', () => this.validateDates());
                    this.$watch('formData.end_date', () => this.validateDates());
                },

                validateDates() {
                    if (this.formData.start_date && this.formData.end_date) {
                        if (this.formData.start_date > this.formData.end_date) {
                            this.formData.end_date = '';
                            this.showNotification('End date must be after start date', 'error');
                        }
                    }
                },

                showNotification(message, type = 'info') {
                    // Simple notification - you can enhance this
                    alert(message);
                }
            };
        }

        // FilePond upload component for edit mode
        function filepondUpload(type, required = false) {
            return {
                serverId: null,
                pond: null,
                previewUrl: null,

                init() {
                    this.$nextTick(() => {
                        this.initFilePond();
                    });
                },

                initFilePond() {
                    const input = this.$refs.fileInput;
                    
                    this.pond = FilePond.create(input, {
                        acceptedFileTypes: ['image/*'],
                        maxFileSize: '2MB',
                        imagePreviewHeight: 170,
                        imageCropAspectRatio: type === 'desktop' ? '16:5' : '16:9',
                        imageResizeTargetWidth: type === 'desktop' ? 1920 : 768,
                        imageResizeTargetHeight: type === 'desktop' ? 600 : 400,
                        imageResizeMode: 'cover',
                        imageResizeUpscale: false,
                        server: {
                            process: {
                                url: '{{ route("admin.banners.filepond.upload") }}',
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                onload: (response) => {
                                    this.serverId = response;
                                    return response;
                                },
                                onerror: (response) => {
                                    console.error('Upload error:', response);
                                }
                            },
                            revert: {
                                url: '{{ route("admin.banners.filepond.delete") }}',
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            }
                        },
                        onprocessfile: (error, file) => {
                            if (!error && type === 'desktop') {
                                // Update preview for desktop image
                                const reader = new FileReader();
                                reader.onload = (e) => {
                                    this.$dispatch('image-uploaded', { url: e.target.result, type: type });
                                };
                                reader.readAsDataURL(file.file);
                            }
                        },
                        onremovefile: () => {
                            this.serverId = null;
                            if (type === 'desktop') {
                                this.$dispatch('image-removed', { type: type });
                            }
                        }
                    });

                    // Listen for image events
                    this.$el.addEventListener('image-uploaded', (e) => {
                        if (e.detail.type === 'desktop') {
                            // Update preview background in parent component
                            const bannerForm = this.$el.closest('[x-data*="bannerEditForm"]').__x.$data;
                            bannerForm.previewImageUrl = e.detail.url;
                        }
                    });

                    this.$el.addEventListener('image-removed', (e) => {
                        if (e.detail.type === 'desktop') {
                            const bannerForm = this.$el.closest('[x-data*="bannerEditForm"]').__x.$data;
                            bannerForm.previewImageUrl = null;
                        }
                    });
                }
            };
        }

        // Remove current image function
        function removeCurrentImage(type) {
            if (confirm('Are you sure you want to remove the current image?')) {
                if (type === 'desktop') {
                    document.getElementById('remove-desktop-image-form').submit();
                } else if (type === 'mobile') {
                    document.getElementById('remove-mobile-image-form').submit();
                }
            }
        }
    </script>
    @endpush
</x-layouts.admin>