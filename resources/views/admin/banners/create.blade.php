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
                            <label for="banner_category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Banner Category <span class="text-red-500">*</span>
                            </label>
                            <select name="banner_category_id" id="banner_category_id"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('banner_category_id') border-red-500 @enderror"
                                required>
                                <option value="">Select a category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('banner_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('banner_category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
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
                            <label for="subtitle" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
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
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
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
                            <label for="button_text" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
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
                            <label for="link_type" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Link Type
                            </label>
                            <select name="link_type" id="link_type" onchange="updateLinkPlaceholder()"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                                <option value="auto" {{ old('link_type', 'auto') === 'auto' ? 'selected' : '' }}>Auto-detect</option>
                                <option value="internal" {{ old('link_type') === 'internal' ? 'selected' : '' }}>Internal Link</option>
                                <option value="external" {{ old('link_type') === 'external' ? 'selected' : '' }}>External Link</option>
                                <option value="route" {{ old('link_type') === 'route' ? 'selected' : '' }}>Laravel Route</option>
                                <option value="email" {{ old('link_type') === 'email' ? 'selected' : '' }}>Email Address</option>
                                <option value="phone" {{ old('link_type') === 'phone' ? 'selected' : '' }}>Phone Number</option>
                                <option value="anchor" {{ old('link_type') === 'anchor' ? 'selected' : '' }}>Anchor Link</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label for="button_link" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
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
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                            </label>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Make this banner active immediately</p>
                        </div>

                        <div>
                            <label for="display_order" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Display Order
                            </label>
                            <input type="number" name="display_order" id="display_order" value="{{ old('display_order') }}"
                                placeholder="Auto-assigned if empty" min="0"
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
                            <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Start Date
                            </label>
                            <input type="datetime-local" name="start_date" id="start_date" value="{{ old('start_date') }}"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('start_date') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave blank to start immediately</p>
                            @error('start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                End Date
                            </label>
                            <input type="datetime-local" name="end_date" id="end_date" value="{{ old('end_date') }}"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('end_date') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave blank for no expiration</p>
                            @error('end_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>

                <!-- Banner Images Upload -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Banner Images</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Upload desktop and mobile versions</p>
                    </x-slot>

                    <!-- Note about creating banner first -->
                    <div class="mb-4 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
                        <div class="flex">
                            <svg class="w-5 h-5 text-amber-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-amber-800 dark:text-amber-200">Create Banner First</h4>
                                <p class="text-sm text-amber-700 dark:text-amber-300 mt-1">
                                    Please save the banner information first, then you can upload images on the edit page.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Image Guidelines -->
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <h4 class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-2">Image Guidelines</h4>
                        <div class="grid grid-cols-1 gap-3 text-xs text-blue-800 dark:text-blue-200">
                            <div>
                                <strong>Desktop Image:</strong>
                                <ul class="mt-1 space-y-1 ml-2">
                                    <li>• Recommended: 1920x1080px</li>
                                    <li>• Aspect ratio: 16:9</li>
                                    <li>• Maximum size: 5MB</li>
                                </ul>
                            </div>
                            <div>
                                <strong>Mobile Image:</strong>
                                <ul class="mt-1 space-y-1 ml-2">
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

                <!-- Preview -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Preview</h3>
                    </x-slot>

                    <div id="banner-preview" class="space-y-4">
                        <!-- Preview Content -->
                        <div class="relative bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg p-6 text-white min-h-32">
                            <div class="relative z-10">
                                <p id="preview-subtitle" class="text-sm opacity-90 mb-1"></p>
                                <h3 id="preview-title" class="text-lg font-bold mb-2">Banner Title</h3>
                                <p id="preview-description" class="text-sm opacity-80 mb-3"></p>
                                <div id="preview-button" class="hidden">
                                    <span class="inline-block px-4 py-2 bg-white text-blue-600 rounded-lg text-sm font-medium">
                                        Button Text
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Update Button -->
                        <button type="button" onclick="updatePreview()"
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Cancel
            </a>

            <button type="submit"
                class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                Create Banner
            </button>
        </div>
    </form>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize preview
            updatePreview();

            // Add input listeners for live preview
            ['title', 'subtitle', 'description', 'button_text'].forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.addEventListener('input', updatePreview);
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

            document.getElementById('preview-title').textContent = title;
            document.getElementById('preview-subtitle').textContent = subtitle;
            document.getElementById('preview-description').textContent = description;

            const buttonElement = document.getElementById('preview-button');
            if (buttonText) {
                buttonElement.querySelector('span').textContent = buttonText;
                buttonElement.classList.remove('hidden');
            } else {
                buttonElement.classList.add('hidden');
            }
        }

        // Update link placeholder based on type
        function updateLinkPlaceholder() {
            const linkType = document.getElementById('link_type').value;
            const linkInput = document.getElementById('button_link');
            const helpText = document.getElementById('link-help');

            const placeholders = {
                'auto': 'https://example.com or /about',
                'internal': '/about or contact-us',
                'external': 'https://example.com',
                'route': 'home or pages.about',
                'email': 'contact@example.com',
                'phone': '+1234567890',
                'anchor': '#section-id'
            };

            const helpTexts = {
                'auto': 'The system will automatically detect the link type',
                'internal': 'Links within your website (relative URLs)',
                'external': 'Links to other websites (must include https://)',
                'route': 'Laravel route names (e.g., home, pages.about)',
                'email': 'Email addresses (will create mailto: links)',
                'phone': 'Phone numbers (will create tel: links)',
                'anchor': 'Links to sections on the same page'
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

        // Initialize link placeholder
        updateLinkPlaceholder();
    </script>
    @endpush
</x-layouts.admin>