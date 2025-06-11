<x-layouts.admin title="Create New Banner">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Banners' => route('admin.banners.index'), 'Create New Banner' => '']" />

    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6"
        id="banner-form">
        @csrf

        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Main Content -->
            <div class="flex-1 space-y-6">
                <!-- Basic Information -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Banner Information</h3>
                    </x-slot>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="banner_category_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Banner Category <span class="text-red-500">*</span>
                            </label>
                            <select name="banner_category_id" id="banner_category_id"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('banner_category_id') border-red-500 @enderror"
                                required>
                                <option value="">Select a category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('banner_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('banner_category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="title"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Banner Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}"
                                placeholder="Enter banner title..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('title') border-red-500 @enderror"
                                required>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="subtitle"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Subtitle
                            </label>
                            <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle') }}"
                                placeholder="Enter banner subtitle..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('subtitle') border-red-500 @enderror">
                            @error('subtitle')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="description"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Description
                            </label>
                            <textarea name="description" id="description" rows="4" placeholder="Enter banner description..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>

                <!-- Call-to-Action -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Call-to-Action</h3>
                    </x-slot>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="button_text"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Button Text
                            </label>
                            <input type="text" name="button_text" id="button_text"
                                value="{{ old('button_text', $banner->button_text ?? '') }}"
                                placeholder="e.g., Learn More, Shop Now, Contact Us" maxlength="50"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('button_text') border-red-500 @enderror">
                            @error('button_text')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Link Type -->
                        <div>
                            <label for="link_type"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Link Type
                            </label>
                            <select name="link_type" id="link_type" onchange="updateLinkPlaceholder()"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('link_type') border-red-500 @enderror">
                                <option value="auto"
                                    {{ old('link_type', $banner->link_type ?? 'auto') === 'auto' ? 'selected' : '' }}>
                                    Auto-detect
                                </option>
                                <option value="internal"
                                    {{ old('link_type', $banner->link_type ?? '') === 'internal' ? 'selected' : '' }}>
                                    Internal Link
                                </option>
                                <option value="external"
                                    {{ old('link_type', $banner->link_type ?? '') === 'external' ? 'selected' : '' }}>
                                    External Link
                                </option>
                                <option value="route"
                                    {{ old('link_type', $banner->link_type ?? '') === 'route' ? 'selected' : '' }}>
                                    Laravel Route
                                </option>
                                <option value="email"
                                    {{ old('link_type', $banner->link_type ?? '') === 'email' ? 'selected' : '' }}>
                                    Email Address
                                </option>
                                <option value="phone"
                                    {{ old('link_type', $banner->link_type ?? '') === 'phone' ? 'selected' : '' }}>
                                    Phone Number
                                </option>
                                <option value="anchor"
                                    {{ old('link_type', $banner->link_type ?? '') === 'anchor' ? 'selected' : '' }}>
                                    Anchor Link
                                </option>
                            </select>
                            @error('link_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Button Link -->
                        <div class="md:col-span-2">
                            <label for="button_link"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Button Link
                            </label>
                            <input type="text" name="button_link" id="button_link"
                                value="{{ old('button_link', $banner->button_link ?? '') }}"
                                placeholder="https://example.com"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('button_link') border-red-500 @enderror">
                            <div id="link-help" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                <!-- Dynamic help text based on link type -->
                            </div>
                            @error('button_link')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Link Options -->
                        <div class="md:col-span-2 space-y-3">
                            <!-- Open in New Tab -->
                            <label class="flex items-center">
                                <input type="checkbox" name="open_in_new_tab" id="open_in_new_tab" value="1"
                                    {{ old('open_in_new_tab', $banner->open_in_new_tab ?? false) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Open link in new tab</span>
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 ml-6">
                                External links will automatically open in new tab regardless of this setting
                            </p>

                            <!-- Link Preview -->
                            <div id="link-preview"
                                class="hidden p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                <p class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-1">Link Preview:</p>
                                <p id="preview-url" class="text-sm text-blue-700 dark:text-blue-300 break-all"></p>
                                <p id="preview-type" class="text-xs text-blue-600 dark:text-blue-400 mt-1"></p>
                            </div>
                        </div>
                    </div>
                </x-admin.card>

                <!-- Banner Images -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Banner Images</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Upload desktop and mobile versions of
                            your banner</p>
                    </x-slot>
                    <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded">
                        <h4 class="font-medium text-yellow-800 mb-2">🧪 Test: Traditional File Upload</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Desktop Image:</label>
                                <input type="file" name="desktop_image" accept="image/*" class="block w-full text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Mobile Image:</label>
                                <input type="file" name="mobile_image" accept="image/*" class="block w-full text-sm">
                            </div>
                        </div>
                        <p class="text-xs text-yellow-600 mt-2">This is for testing - use this instead of the fancy uploader</p>
                    </div>
                    <!-- Single Universal File Uploader with categories -->
                    <x-universal-file-uploader name="files" :multiple="false" :maxFiles="1" maxFileSize="5MB"
                        :acceptedFileTypes="['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']" dropDescription="Drop banner image here or click to browse" :uploadEndpoint="isset($banner) ? route('admin.banners.upload-image', $banner) : null"
                        :deleteEndpoint="isset($banner) ? route('admin.banners.delete-image', $banner) : null" :allowPreview="true" :enableCategories="true" :categories="[
                            ['value' => 'desktop', 'label' => 'Desktop Image'],
                            ['value' => 'mobile', 'label' => 'Mobile Image'],
                        ]" :enableDescription="false"
                        :autoUpload="isset($banner) ? true : false" :singleMode="true" :existingFiles="isset($banner) ? $banner->getImagesForUploader() : []" theme="modern"
                        id="banner-image-uploader" />

                    <!-- Image Guidelines -->
                    <div
                        class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <h4 class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-2">Image Guidelines</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-blue-800 dark:text-blue-200">
                            <div>
                                <strong>Desktop Image:</strong>
                                <ul class="mt-1 space-y-1">
                                    <li>• Recommended: 1920x1080px</li>
                                    <li>• Aspect ratio: 16:9</li>
                                    <li>• Maximum size: 5MB</li>
                                </ul>
                            </div>
                            <div>
                                <strong>Mobile Image:</strong>
                                <ul class="mt-1 space-y-1">
                                    <li>• Recommended: 768x1024px</li>
                                    <li>• Aspect ratio: 3:4</li>
                                    <li>• Maximum size: 5MB</li>
                                </ul>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-blue-700 dark:text-blue-300">
                            💡 If no mobile image is uploaded, the desktop image will be used for all devices.
                        </p>
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
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1"
                                    {{ old('is_active', true) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                            </label>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Make this banner active
                                immediately</p>
                        </div>

                        <div>
                            <label for="display_order"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Display Order
                            </label>
                            <input type="number" name="display_order" id="display_order"
                                value="{{ old('display_order') }}" placeholder="Auto-assigned if empty"
                                min="0"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('display_order') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Lower numbers appear first</p>
                            @error('display_order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>

                <!-- Schedule -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Schedule</h3>
                    </x-slot>

                    <div class="space-y-4">
                        <div>
                            <label for="start_date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Start Date
                            </label>
                            <input type="datetime-local" name="start_date" id="start_date"
                                value="{{ old('start_date') }}"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('start_date') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave blank to start immediately
                            </p>
                            @error('start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                End Date
                            </label>
                            <input type="datetime-local" name="end_date" id="end_date"
                                value="{{ old('end_date') }}"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('end_date') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave blank for no expiration</p>
                            @error('end_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>

                <!-- Preview -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Preview</h3>
                    </x-slot>
                
                    <div id="banner-preview" class="space-y-4">
                        <!-- Device Toggle -->
                        <div class="flex items-center justify-center space-x-2 mb-4">
                            <button type="button" 
                                    id="preview-desktop" 
                                    onclick="switchPreviewDevice('desktop')"
                                    class="px-3 py-1 text-xs font-medium text-blue-600 bg-blue-100 border border-blue-200 rounded-lg hover:bg-blue-200 dark:bg-blue-900/30 dark:border-blue-800 dark:text-blue-400 dark:hover:bg-blue-900/50">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Desktop
                            </button>
                            <button type="button" 
                                    id="preview-mobile" 
                                    onclick="switchPreviewDevice('mobile')"
                                    class="px-3 py-1 text-xs font-medium text-gray-600 bg-gray-100 border border-gray-200 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-600">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                Mobile
                            </button>
                        </div>
                
                        <!-- Preview Container -->
                        <div id="preview-container" class="relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                            <!-- Desktop Preview -->
                            <div id="desktop-preview" class="preview-device active transition-all duration-300">
                                <div class="aspect-w-16 aspect-h-9 bg-gray-100 dark:bg-gray-700 relative">
                                    <!-- Background Image -->
                                    <div id="desktop-bg" class="absolute inset-0 bg-cover bg-center bg-no-repeat"></div>
                                    <!-- Overlay -->
                                    <div class="absolute inset-0 bg-black bg-opacity-30"></div>
                                    <!-- Content -->
                                    <div class="relative z-10 flex items-center justify-start p-8">
                                        <div class="max-w-lg text-white">
                                            <p id="desktop-subtitle" class="text-sm opacity-90 mb-2"></p>
                                            <h3 id="desktop-title" class="text-2xl font-bold mb-3">Banner Title</h3>
                                            <p id="desktop-description" class="text-sm opacity-80 mb-4"></p>
                                            <div id="desktop-button" class="hidden">
                                                <span class="inline-block px-6 py-3 bg-white text-gray-900 rounded-lg text-sm font-medium hover:bg-gray-100 transition-colors">
                                                    Button Text
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- No Image Placeholder -->
                                    <div id="desktop-placeholder" class="absolute inset-0 flex items-center justify-center">
                                        <div class="text-center">
                                            <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <p class="text-gray-500 dark:text-gray-400 text-sm">Desktop Preview</p>
                                            <p class="text-gray-400 dark:text-gray-500 text-xs">Upload desktop image to see preview</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                
                            <!-- Mobile Preview -->
                            <div id="mobile-preview" class="preview-device hidden transition-all duration-300">
                                <div class="max-w-sm mx-auto">
                                    <div class="aspect-w-9 aspect-h-16 bg-gray-100 dark:bg-gray-700 relative rounded-lg overflow-hidden">
                                        <!-- Background Image -->
                                        <div id="mobile-bg" class="absolute inset-0 bg-cover bg-center bg-no-repeat"></div>
                                        <!-- Overlay -->
                                        <div class="absolute inset-0 bg-black bg-opacity-30"></div>
                                        <!-- Content -->
                                        <div class="relative z-10 flex items-end p-6">
                                            <div class="text-white">
                                                <p id="mobile-subtitle" class="text-xs opacity-90 mb-1"></p>
                                                <h3 id="mobile-title" class="text-lg font-bold mb-2">Banner Title</h3>
                                                <p id="mobile-description" class="text-xs opacity-80 mb-3"></p>
                                                <div id="mobile-button" class="hidden">
                                                    <span class="inline-block px-4 py-2 bg-white text-gray-900 rounded text-xs font-medium">
                                                        Button Text
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- No Image Placeholder -->
                                        <div id="mobile-placeholder" class="absolute inset-0 flex items-center justify-center">
                                            <div class="text-center">
                                                <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                </svg>
                                                <p class="text-gray-500 dark:text-gray-400 text-sm">Mobile Preview</p>
                                                <p class="text-gray-400 dark:text-gray-500 text-xs">Upload mobile image or use desktop</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                
                        <!-- Update Button -->
                        <button type="button" 
                                onclick="updatePreview()"
                                class="w-full px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-400 dark:hover:bg-blue-900/30">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Update Preview
                        </button>
                    </div>
                </x-admin.card>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('admin.banners.index') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Cancel
            </a>

            <div class="flex gap-3">
                <button type="submit" name="action" value="draft"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                    Save as Draft
                </button>

                <button type="submit" name="action" value="publish"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    Create Banner
                </button>
            </div>
        </div>
    </form>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Image preview functionality
                function previewImage(input, previewId) {
                    if (input.files && input.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            document.getElementById(previewId).src = e.target.result;
                            document.getElementById(previewId).classList.remove('hidden');
                        };
                        reader.readAsDataURL(input.files[0]);
                    }
                }

                // Add event listeners for traditional file inputs
                document.getElementById('desktop_image')?.addEventListener('change', function() {
                    previewImage(this, 'desktop_preview');
                    updatePreview();
                });

                document.getElementById('mobile_image')?.addEventListener('change', function() {
                    previewImage(this, 'mobile_preview');
                    updatePreview();
                });

                // Listen for universal file uploader events
                document.addEventListener('files-uploaded', function(event) {
                    if (event.detail.component === 'banner-image-uploader') {
                        console.log('Banner image uploaded:', event.detail);

                        // Update preview
                        updatePreview();

                        // For edit mode, refresh the page to show updated images
                        if (typeof bannerEditMode !== 'undefined' && bannerEditMode) {
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        }

                        // Show success message
                        showNotification('Image uploaded successfully!', 'success');
                    }
                });

                document.addEventListener('file-deleted', function(event) {
                    if (event.detail.component === 'banner-image-uploader') {
                        console.log('Banner image deleted:', event.detail.file);
                        updatePreview();
                        showNotification('Image removed successfully!', 'success');
                    }
                });

                // Form validation
                document.getElementById('banner-form').addEventListener('submit', function(e) {
                    const title = document.getElementById('title').value.trim();
                    const category = document.getElementById('banner_category_id').value;

                    if (!title) {
                        e.preventDefault();
                        alert('Please enter a banner title.');
                        document.getElementById('title').focus();
                        return;
                    }

                    if (!category) {
                        e.preventDefault();
                        alert('Please select a banner category.');
                        document.getElementById('banner_category_id').focus();
                        return;
                    }

                    // Check if button text is provided but no link
                    const buttonText = document.getElementById('button_text').value.trim();
                    const buttonLink = document.getElementById('button_link').value.trim();

                    if (buttonText && !buttonLink) {
                        e.preventDefault();
                        alert('Please provide a button link when button text is specified.');
                        document.getElementById('button_link').focus();
                        return;
                    }
                });

                // Character counters
                function addCharCounter(inputId, maxLength) {
                    const input = document.getElementById(inputId);
                    if (!input) return;

                    const counter = document.createElement('div');
                    counter.className = 'text-xs text-gray-500 dark:text-gray-400 mt-1';
                    counter.id = inputId + '_counter';

                    input.parentNode.appendChild(counter);

                    function updateCounter() {
                        const remaining = maxLength - input.value.length;
                        counter.textContent = `${input.value.length}/${maxLength} characters`;

                        if (remaining < 10) {
                            counter.className = 'text-xs text-red-500 mt-1';
                        } else {
                            counter.className = 'text-xs text-gray-500 dark:text-gray-400 mt-1';
                        }
                    }

                    input.addEventListener('input', updateCounter);
                    updateCounter();
                }

                // Add character counters
                addCharCounter('title', 255);
                addCharCounter('subtitle', 255);
                addCharCounter('button_text', 50);
            });

            // Update preview function
            function updatePreview() {
                const title = document.getElementById('title').value || 'Banner Title';
                const subtitle = document.getElementById('subtitle').value || '';
                const description = document.getElementById('description').value || '';
                const buttonText = document.getElementById('button_text').value || '';

                const previewHtml = `
                <div class="relative bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg p-6 text-white min-h-32">
                    <div class="relative z-10">
                        ${subtitle ? `<p class="text-sm opacity-90 mb-1">${subtitle}</p>` : ''}
                        <h3 class="text-lg font-bold mb-2">${title}</h3>
                        ${description ? `<p class="text-sm opacity-80 mb-3">${description}</p>` : ''}
                        ${buttonText ? `<span class="inline-block px-4 py-2 bg-white text-blue-600 rounded-lg text-sm font-medium">${buttonText}</span>` : ''}
                    </div>
                </div>
            `;

                document.getElementById('banner-preview').innerHTML = previewHtml;
            }

            // Initialize preview
            updatePreview();
        </script>
    @endpush
</x-layouts.admin>
