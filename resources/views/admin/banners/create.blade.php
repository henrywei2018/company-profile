<x-layouts.admin title="Create New Banner">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Banners' => route('admin.banners.index'), 'Create New Banner' => '']" />

    <form action="{{ route('admin.banners.store') }}" method="POST" class="space-y-6" id="banner-form">
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
                            <input type="text" name="button_text" id="button_text" value="{{ old('button_text') }}"
                                placeholder="e.g., Learn More, Shop Now, Contact Us" maxlength="50"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('button_text') border-red-500 @enderror">
                            @error('button_text')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="link_type"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Link Type
                            </label>
                            <select name="link_type" id="link_type" onchange="updateLinkPlaceholder()"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                                <option value="auto" {{ old('link_type', 'auto') === 'auto' ? 'selected' : '' }}>
                                    Auto-detect</option>
                                <option value="internal" {{ old('link_type') === 'internal' ? 'selected' : '' }}>
                                    Internal Link</option>
                                <option value="external" {{ old('link_type') === 'external' ? 'selected' : '' }}>
                                    External Link</option>
                                <option value="route" {{ old('link_type') === 'route' ? 'selected' : '' }}>Laravel
                                    Route</option>
                                <option value="email" {{ old('link_type') === 'email' ? 'selected' : '' }}>Email
                                    Address</option>
                                <option value="phone" {{ old('link_type') === 'phone' ? 'selected' : '' }}>Phone
                                    Number</option>
                                <option value="anchor" {{ old('link_type') === 'anchor' ? 'selected' : '' }}>Anchor
                                    Link</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label for="button_link"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Button Link
                            </label>
                            <input type="text" name="button_link" id="button_link" value="{{ old('button_link') }}"
                                placeholder="https://example.com"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('button_link') border-red-500 @enderror">
                            <div id="link-help" class="mt-1 text-xs text-gray-500 dark:text-gray-400"></div>
                            @error('button_link')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2 space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" name="open_in_new_tab" id="open_in_new_tab" value="1"
                                    {{ old('open_in_new_tab') ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Open link in new tab</span>
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 ml-6">
                                External links will automatically open in new tab regardless of this setting
                            </p>
                        </div>
                    </div>
                </x-admin.card>
                <!-- Banner Images Upload -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Banner Images</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Upload desktop and mobile versions</p>
                    </x-slot>

                    <!-- Universal File Uploader for Temporary Upload -->
                    <x-universal-file-uploader :id="'banner-temp-uploader-' . uniqid()" name="temp_images" :multiple="false" :maxFiles="1"
                        maxFileSize="5MB" :acceptedFileTypes="['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']" :uploadEndpoint="route('admin.banners.temp-upload')" :deleteEndpoint="route('admin.banners.temp-delete')"
                        dropDescription="Drop banner images here or click to browse" :enableCategories="true"
                        :categories="[
                            ['value' => 'desktop', 'label' => 'Desktop Image'],
                            ['value' => 'mobile', 'label' => 'Mobile Image'],
                        ]" :autoUpload="true" containerClass="mb-2" />

                    <!-- Hidden inputs to store temp file data -->
                    <div id="temp-files-data"></div>
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
                            <button type="button" id="preview-desktop" onclick="switchPreviewDevice('desktop')"
                                class="px-3 py-1 text-xs font-medium text-blue-600 bg-blue-100 border border-blue-200 rounded-lg hover:bg-blue-200 dark:bg-blue-900/30 dark:border-blue-800 dark:text-blue-400 dark:hover:bg-blue-900/50">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                Desktop
                            </button>
                            <button type="button" id="preview-mobile" onclick="switchPreviewDevice('mobile')"
                                class="px-3 py-1 text-xs font-medium text-gray-600 bg-gray-100 border border-gray-200 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-600">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                Mobile
                            </button>
                        </div>

                        <!-- Preview Container -->
                        <div id="preview-container"
                            class="relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                            <!-- Desktop Preview -->
                            <div id="desktop-preview" class="preview-device active transition-all duration-300">
                                <div class="aspect-w-16 aspect-h-9 bg-gray-100 dark:bg-gray-700 relative">
                                    <div id="desktop-bg" class="absolute inset-0 bg-cover bg-center bg-no-repeat">
                                    </div>
                                    <div class="absolute inset-0 bg-black bg-opacity-30"></div>
                                    <div class="relative z-10 flex items-center justify-start p-8">
                                        <div class="max-w-lg text-white">
                                            <p id="desktop-subtitle" class="text-sm opacity-90 mb-2"></p>
                                            <h3 id="desktop-title" class="text-2xl font-bold mb-3">Banner Title</h3>
                                            <p id="desktop-description" class="text-sm opacity-80 mb-4"></p>
                                            <div id="desktop-button" class="hidden">
                                                <span
                                                    class="inline-block px-6 py-3 bg-white text-gray-900 rounded-lg text-sm font-medium hover:bg-gray-100 transition-colors">
                                                    Button Text
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="desktop-placeholder"
                                        class="absolute inset-0 flex items-center justify-center">
                                        <div class="text-center">
                                            <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <p class="text-gray-500 dark:text-gray-400 text-sm">Desktop Preview</p>
                                            <p class="text-gray-400 dark:text-gray-500 text-xs">Upload desktop image to
                                                see preview</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mobile Preview -->
                            <div id="mobile-preview" class="preview-device hidden transition-all duration-300">
                                <div class="max-w-sm mx-auto">
                                    <div
                                        class="aspect-w-9 aspect-h-16 bg-gray-100 dark:bg-gray-700 relative rounded-lg overflow-hidden">
                                        <div id="mobile-bg" class="absolute inset-0 bg-cover bg-center bg-no-repeat">
                                        </div>
                                        <div class="absolute inset-0 bg-black bg-opacity-30"></div>
                                        <div class="relative z-10 flex items-end p-6">
                                            <div class="text-white">
                                                <p id="mobile-subtitle" class="text-xs opacity-90 mb-1"></p>
                                                <h3 id="mobile-title" class="text-lg font-bold mb-2">Banner Title</h3>
                                                <p id="mobile-description" class="text-xs opacity-80 mb-3"></p>
                                                <div id="mobile-button" class="hidden">
                                                    <span
                                                        class="inline-block px-4 py-2 bg-white text-gray-900 rounded text-xs font-medium">
                                                        Button Text
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="mobile-placeholder"
                                            class="absolute inset-0 flex items-center justify-center">
                                            <div class="text-center">
                                                <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                                <p class="text-gray-500 dark:text-gray-400 text-sm">Mobile Preview</p>
                                                <p class="text-gray-400 dark:text-gray-500 text-xs">Upload mobile image
                                                    or use desktop</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Update Button -->
                        <button type="button" onclick="updatePreview()"
                            class="w-full px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-400 dark:hover:bg-blue-900/30">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
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

            <button type="submit"
                class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
                Create Banner
            </button>
        </div>
    </form>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Global variables to track uploaded images
                window.uploadedImages = {
                    desktop: null,
                    mobile: null
                };

                // Track temp file data
                window.tempFileData = {};

                // Initialize preview
                updatePreview();

                // Add input listeners for live preview
                ['title', 'subtitle', 'description', 'button_text'].forEach(id => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.addEventListener('input', debounce(updatePreview, 300));
                    }
                });

                // Listen for universal uploader events
                document.addEventListener('files-uploaded', function(event) {
                    if (event.detail.component === 'banner-temp-uploader') {
                        handleTempUploadSuccess(event.detail);
                    }
                });

                document.addEventListener('file-deleted', function(event) {
                    if (event.detail.component === 'banner-temp-uploader') {
                        handleTempFileDelete(event.detail);
                    }
                });
                loadExistingTempFiles();

                function loadExistingTempFiles() {
                    fetch('{{ route('admin.banners.temp-files') }}', {
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
                                    if (file.category === 'desktop') {
                                        window.uploadedImages.desktop = file.url;
                                    } else if (file.category === 'mobile') {
                                        window.uploadedImages.mobile = file.url;
                                    }
                                });
                                updatePreview();
                            }
                        })
                        .catch(error => {
                            console.warn('Could not load existing temp files:', error);
                        });
                }

                // Form submission handling - CRITICAL FIX
                document.getElementById('banner-form').addEventListener('submit', function(e) {
                    console.log('Form submitting...');

                    // Basic validation
                    const title = document.getElementById('title').value.trim();
                    const category = document.getElementById('banner_category_id').value;

                    if (!title) {
                        e.preventDefault();
                        showNotification('Please enter a banner title.', 'error');
                        document.getElementById('title').focus();
                        return;
                    }

                    if (!category) {
                        e.preventDefault();
                        showNotification('Please select a banner category.', 'error');
                        document.getElementById('banner_category_id').focus();
                        return;
                    }

                    // Check if button text is provided but no link
                    const buttonText = document.getElementById('button_text').value.trim();
                    const buttonLink = document.getElementById('button_link').value.trim();

                    if (buttonText && !buttonLink) {
                        e.preventDefault();
                        showNotification('Please provide a button link when button text is specified.',
                            'warning');
                        document.getElementById('button_link').focus();
                        return;
                    }

                    // Form is valid, let it submit normally
                    // The temp images are already in session, no need to add them to form
                    console.log('Form validation passed, submitting...');
                });

                // Character counters
                addCharCounter('title', 255);
                addCharCounter('subtitle', 255);
                addCharCounter('button_text', 50);

                // Initialize link placeholder
                updateLinkPlaceholder();
            });

            // Handle temporary upload success
            function handleTempUploadSuccess(detail) {
                const files = detail.files || [];

                files.forEach(file => {
                    if (file.category === 'desktop') {
                        window.uploadedImages.desktop = file.url;
                    } else if (file.category === 'mobile') {
                        window.uploadedImages.mobile = file.url;
                    }
                });

                updatePreview();
                showNotification(detail.message || 'Images uploaded successfully!', 'success');
            }

            // Handle temporary file deletion
            function handleTempFileDelete(detail) {
                const file = detail.file;

                if (file.category === 'desktop') {
                    window.uploadedImages.desktop = null;
                } else if (file.category === 'mobile') {
                    window.uploadedImages.mobile = null;
                }

                updatePreview();
            }

            // Preview device switching
            window.switchPreviewDevice = function(device) {
                const desktopBtn = document.getElementById('preview-desktop');
                const mobileBtn = document.getElementById('preview-mobile');
                const desktopPreview = document.getElementById('desktop-preview');
                const mobilePreview = document.getElementById('mobile-preview');

                if (device === 'desktop') {
                    desktopBtn.className =
                        'px-3 py-1 text-xs font-medium text-blue-600 bg-blue-100 border border-blue-200 rounded-lg hover:bg-blue-200 dark:bg-blue-900/30 dark:border-blue-800 dark:text-blue-400 dark:hover:bg-blue-900/50';
                    mobileBtn.className =
                        'px-3 py-1 text-xs font-medium text-gray-600 bg-gray-100 border border-gray-200 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-600';
                    desktopPreview.classList.remove('hidden');
                    mobilePreview.classList.add('hidden');
                } else {
                    mobileBtn.className =
                        'px-3 py-1 text-xs font-medium text-blue-600 bg-blue-100 border border-blue-200 rounded-lg hover:bg-blue-200 dark:bg-blue-900/30 dark:border-blue-800 dark:text-blue-400 dark:hover:bg-blue-900/50';
                    desktopBtn.className =
                        'px-3 py-1 text-xs font-medium text-gray-600 bg-gray-100 border border-gray-200 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-600';
                    mobilePreview.classList.remove('hidden');
                    desktopPreview.classList.add('hidden');
                }
            };

            // Update preview function
            window.updatePreview = function() {
                const title = document.getElementById('title').value || 'Banner Title';
                const subtitle = document.getElementById('subtitle').value || '';
                const description = document.getElementById('description').value || '';
                const buttonText = document.getElementById('button_text').value || '';

                // Update desktop preview
                document.getElementById('desktop-title').textContent = title;
                document.getElementById('desktop-subtitle').textContent = subtitle;
                document.getElementById('desktop-description').textContent = description;

                const desktopButton = document.getElementById('desktop-button');
                if (buttonText) {
                    desktopButton.querySelector('span').textContent = buttonText;
                    desktopButton.classList.remove('hidden');
                } else {
                    desktopButton.classList.add('hidden');
                }

                // Update mobile preview
                document.getElementById('mobile-title').textContent = title;
                document.getElementById('mobile-subtitle').textContent = subtitle;
                document.getElementById('mobile-description').textContent = description;

                const mobileButton = document.getElementById('mobile-button');
                if (buttonText) {
                    mobileButton.querySelector('span').textContent = buttonText;
                    mobileButton.classList.remove('hidden');
                } else {
                    mobileButton.classList.add('hidden');
                }

                // Update background images if available
                if (window.uploadedImages.desktop) {
                    document.getElementById('desktop-bg').style.backgroundImage = `url(${window.uploadedImages.desktop})`;
                    document.getElementById('desktop-placeholder').style.display = 'none';
                } else {
                    document.getElementById('desktop-bg').style.backgroundImage = '';
                    document.getElementById('desktop-placeholder').style.display = 'flex';
                }

                if (window.uploadedImages.mobile) {
                    document.getElementById('mobile-bg').style.backgroundImage = `url(${window.uploadedImages.mobile})`;
                    document.getElementById('mobile-placeholder').style.display = 'none';
                } else if (window.uploadedImages.desktop) {
                    // Use desktop image for mobile if no mobile image
                    document.getElementById('mobile-bg').style.backgroundImage = `url(${window.uploadedImages.desktop})`;
                    document.getElementById('mobile-placeholder').style.display = 'none';
                } else {
                    document.getElementById('mobile-bg').style.backgroundImage = '';
                    document.getElementById('mobile-placeholder').style.display = 'flex';
                }
            };

            // Update link placeholder based on type
            function updateLinkPlaceholder() {
                const linkType = document.getElementById('link_type').value;
                const linkInput = document.getElementById('button_link');
                const helpText = document.getElementById('link-help');

                const placeholders = {
                    'auto': 'https://example.com or /about-us',
                    'internal': '/about-us or pages/contact',
                    'external': 'https://example.com',
                    'route': 'home or contact.index',
                    'email': 'contact@example.com',
                    'phone': '+1234567890',
                    'anchor': '#section-id'
                };

                const helpTexts = {
                    'auto': 'System will automatically detect the link type',
                    'internal': 'Link to pages within your website',
                    'external': 'Link to external websites (opens in new tab)',
                    'route': 'Laravel route name (e.g., home, contact.index)',
                    'email': 'Email address for mailto links',
                    'phone': 'Phone number for tel links',
                    'anchor': 'Link to section on same page'
                };

                linkInput.placeholder = placeholders[linkType] || placeholders['auto'];
                helpText.textContent = helpTexts[linkType] || helpTexts['auto'];
            }

            // Add character counter
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

            // Utility functions
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
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

        <style>
            .aspect-w-16 {
                position: relative;
                padding-bottom: 56.25%;
                /* 16:9 aspect ratio */
            }

            .aspect-w-9 {
                position: relative;
                padding-bottom: 177.78%;
                /* 9:16 aspect ratio */
            }

            .aspect-w-16>*,
            .aspect-w-9>* {
                position: absolute;
                height: 100%;
                width: 100%;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
            }
        </style>
    @endpush
</x-layouts.admin>
