<x-layouts.admin title="Create New Banner">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Banners' => route('admin.banners.index'), 'Create New Banner' => '']" />

    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" x-data="bannerForm()">
        @csrf
        
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
                                   value="{{ old('title') }}"
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
                                   value="{{ old('subtitle') }}"
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
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
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
                                   value="{{ old('button_text') }}"
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
                                   value="{{ old('button_link') }}"
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
                                       {{ old('open_in_new_tab') ? 'checked' : '' }}
                                       x-model="formData.open_in_new_tab"
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Open link in new tab</span>
                            </label>
                        </div>
                    </div>
                </x-admin.card>

                <!-- Banner Images with Modern File Uploader -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Banner Images</h3>
                    </x-slot>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Desktop Image -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-3">
                                Desktop Image <span class="text-red-500">*</span>
                            </label>
                            
                            <x-admin.modern-file-uploader 
                                name="desktop_image"
                                :multiple="false"
                                :maxFiles="1"
                                maxFileSize="2MB"
                                :acceptedFileTypes="['image/*']"
                                dropDescription="Drop desktop banner image here or click to browse"
                                category="banners"
                                :isPublic="true"
                            />
                            
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                Recommended: 1920x600px, Max 2MB, Formats: JPG, PNG, WebP
                            </p>
                            
                            @error('desktop_image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Mobile Image -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-3">
                                Mobile Image (Optional)
                            </label>
                            
                            <x-admin.modern-file-uploader 
                                name="mobile_image"
                                :multiple="false"
                                :maxFiles="1"
                                maxFileSize="2MB"
                                :acceptedFileTypes="['image/*']"
                                dropDescription="Drop mobile banner image here or click to browse"
                                category="banners"
                                :isPublic="true"
                            />
                            
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                Recommended: 768x400px, Max 2MB, Formats: JPG, PNG, WebP
                            </p>
                            
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
                                    <option value="{{ $category->id }}" {{ old('banner_category_id') == $category->id ? 'selected' : '' }}>
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
                                   value="{{ old('display_order', 0) }}"
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
                                       {{ old('is_active', true) ? 'checked' : '' }}
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
                                   value="{{ old('start_date') }}"
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
                                   value="{{ old('end_date') }}"
                                   x-model="formData.end_date"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('end_date') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">When to stop showing this banner</p>
                            @error('end_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>

                <!-- Live Preview -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Live Preview</h3>
                    </x-slot>

                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-6 text-white min-h-[200px] flex flex-col justify-end relative overflow-hidden"
                         :style="previewImageUrl ? `background-image: url('${previewImageUrl}'); background-size: cover; background-position: center;` : ''">
                        <div class="bg-black bg-opacity-20 rounded p-4">
                            <p class="text-sm opacity-90 mb-1" x-text="formData.subtitle" x-show="formData.subtitle"></p>
                            <h3 class="text-lg font-bold mb-2" x-text="formData.title || 'Your banner title will appear here'"></h3>
                            <p class="text-sm opacity-80 mb-4" x-text="formData.description" x-show="formData.description"></p>
                            <button type="button" 
                                    class="inline-flex items-center px-3 py-2 bg-white text-gray-900 rounded-lg text-sm font-medium"
                                    x-show="formData.button_text"
                                    x-text="formData.button_text || 'Button Text'">
                            </button>
                        </div>
                    </div>
                </x-admin.card>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('admin.banners.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                Cancel
            </a>
            
            <div class="flex gap-3">
                <button type="submit" 
                        name="action" 
                        value="draft"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                    Save as Draft
                </button>
                
                <button type="submit" 
                        name="action" 
                        value="publish"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Create Banner
                </button>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        // Banner form Alpine.js component
        function bannerForm() {
            return {
                formData: {
                    title: '{{ old("title") }}',
                    subtitle: '{{ old("subtitle") }}',
                    description: '{{ old("description") }}',
                    button_text: '{{ old("button_text") }}',
                    button_link: '{{ old("button_link") }}',
                    open_in_new_tab: {{ old('open_in_new_tab') ? 'true' : 'false' }},
                    category_id: '{{ old("banner_category_id") }}',
                    display_order: {{ old('display_order', 0) }},
                    is_active: {{ old('is_active', true) ? 'true' : 'false' }},
                    start_date: '{{ old("start_date") }}',
                    end_date: '{{ old("end_date") }}'
                },
                previewImageUrl: null,

                init() {
                    // Watch for date validation
                    this.$watch('formData.start_date', () => this.validateDates());
                    this.$watch('formData.end_date', () => this.validateDates());
                    
                    // Listen for image upload events from the modern file uploader
                    this.$el.addEventListener('file-uploaded', (e) => {
                        if (e.detail.category === 'banners' && e.detail.files && e.detail.files.length > 0) {
                            // Update preview with the first uploaded image (desktop)
                            const fileData = e.detail.files[0];
                            if (fileData.preview_url) {
                                this.previewImageUrl = fileData.preview_url;
                            }
                        }
                    });
                    
                    this.$el.addEventListener('file-removed', (e) => {
                        if (e.detail.category === 'banners') {
                            this.previewImageUrl = null;
                        }
                    });
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
    </script>
    @endpush
</x-layouts.admin>