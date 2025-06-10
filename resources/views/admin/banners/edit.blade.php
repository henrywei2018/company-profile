<x-layouts.admin title="Edit Banner: {{ $banner->title }}">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Banners' => route('admin.banners.index'), 'Edit Banner' => '']" />

    <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="banner-form">
        @csrf
        @method('PUT')
        
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
                            <select name="banner_category_id" 
                                    id="banner_category_id"
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

                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Banner Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="title" 
                                   id="title"
                                   value="{{ old('title', $banner->title) }}"
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
                            <input type="text" 
                                   name="subtitle" 
                                   id="subtitle"
                                   value="{{ old('subtitle', $banner->subtitle) }}"
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
                            <textarea name="description" 
                                      id="description"
                                      rows="4"
                                      placeholder="Enter banner description..."
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('description') border-red-500 @enderror">{{ old('description', $banner->description) }}</textarea>
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
                            <input type="text" 
                                   name="button_text" 
                                   id="button_text"
                                   value="{{ old('button_text', $banner->button_text) }}"
                                   placeholder="e.g., Learn More, Shop Now"
                                   maxlength="50"
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
                                   placeholder="https://example.com"
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
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Open link in new tab</span>
                            </label>
                        </div>
                    </div>
                </x-admin.card>

                <!-- Banner Images -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Banner Images</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Upload desktop and mobile versions of your banner</p>
                    </x-slot>

                    <!-- Current Images Display -->
                    @if($banner->hasImages())
                        <div class="mb-6">
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">Current Images</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($banner->hasDesktopImage())
                                    <div class="relative">
                                        <div class="aspect-w-16 aspect-h-9 bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden">
                                            <img src="{{ $banner->imageUrl }}" 
                                                 alt="Desktop Banner" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                        <div class="mt-2 flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">Desktop Image</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $banner->getImageFileSize('desktop') }}</p>
                                            </div>
                                            <button type="button" 
                                                    onclick="removeImage('desktop')"
                                                    class="px-3 py-1 text-xs font-medium text-red-600 bg-red-50 border border-red-200 rounded hover:bg-red-100 dark:bg-red-900/20 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-900/30">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                @if($banner->hasMobileImage())
                                    <div class="relative">
                                        <div class="aspect-w-9 aspect-h-16 bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden max-w-xs">
                                            <img src="{{ $banner->mobileImageUrl }}" 
                                                 alt="Mobile Banner" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                        <div class="mt-2 flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">Mobile Image</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $banner->getImageFileSize('mobile') }}</p>
                                            </div>
                                            <button type="button" 
                                                    onclick="removeImage('mobile')"
                                                    class="px-3 py-1 text-xs font-medium text-red-600 bg-red-50 border border-red-200 rounded hover:bg-red-100 dark:bg-red-900/20 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-900/30">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Universal File Uploader -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">
                            {{ $banner->hasImages() ? 'Replace Images' : 'Upload Images' }}
                        </h4>
                        
                        <x-universal-file-uploader
                            name="files"
                            :multiple="true"
                            :maxFiles="2"
                            maxFileSize="5MB"
                            :acceptedFileTypes="['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']"
                            dropDescription="Drop banner images here or click to browse"
                            :uploadEndpoint="route('admin.banners.upload-images', $banner)"
                            :deleteEndpoint="route('admin.banners.delete-image', $banner)"
                            :allowPreview="true"
                            :enableCategories="true"
                            :categories="[
                                ['value' => 'desktop', 'label' => 'Desktop Image'],
                                ['value' => 'mobile', 'label' => 'Mobile Image']
                            ]"
                            :enableDescription="true"
                            :autoUpload="false"
                            :existingFiles="$banner->getImagesForUploader()"
                            theme="modern"
                            id="banner-image-uploader"
                        />
                    </div>

                    <!-- Traditional File Upload Fallback -->
                    <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">Traditional Upload</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="desktop_image" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    {{ $banner->hasDesktopImage() ? 'Replace Desktop Image' : 'Desktop Image' }}
                                </label>
                                <input type="file" 
                                       name="desktop_image" 
                                       id="desktop_image"
                                       accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Recommended: 1920x1080px (max 5MB)</p>
                            </div>
                            
                            <div>
                                <label for="mobile_image" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    {{ $banner->hasMobileImage() ? 'Replace Mobile Image' : 'Mobile Image' }}
                                </label>
                                <input type="file" 
                                       name="mobile_image" 
                                       id="mobile_image"
                                       accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Recommended: 768x1024px (max 5MB)</p>
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
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', $banner->is_active) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                            </label>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Make this banner active</p>
                        </div>
                        
                        <div>
                            <label for="display_order" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Display Order
                            </label>
                            <input type="number" 
                                   name="display_order" 
                                   id="display_order"
                                   value="{{ old('display_order', $banner->display_order) }}"
                                   placeholder="Auto-assigned if empty"
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
                            <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Start Date
                            </label>
                            <input type="datetime-local" 
                                   name="start_date" 
                                   id="start_date"
                                   value="{{ old('start_date', $banner->start_date?->format('Y-m-d\TH:i')) }}"
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
                            <input type="datetime-local" 
                                   name="end_date" 
                                   id="end_date"
                                   value="{{ old('end_date', $banner->end_date?->format('Y-m-d\TH:i')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('end_date') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave blank for no expiration</p>
                            @error('end_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>

                <!-- Banner Status -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Status Information</h3>
                    </x-slot>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Current Status:</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                @if($banner->status === 'active') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                @elseif($banner->status === 'scheduled') bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100
                                @elseif($banner->status === 'expired') bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                                @else bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100 @endif">
                                {{ $banner->formatted_status }}
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Created:</span>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $banner->created_at->format('M j, Y g:i A') }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Last Updated:</span>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $banner->updated_at->format('M j, Y g:i A') }}</span>
                        </div>
                    </div>
                </x-admin.card>

                <!-- Preview -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Preview</h3>
                    </x-slot>

                    <div id="banner-preview" class="space-y-4">
                        <!-- Preview will be generated here -->
                    </div>
                    <button type="button" 
                            onclick="updatePreview()"
                            class="w-full mt-4 px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-400 dark:hover:bg-blue-900/30">
                        Update Preview
                    </button>
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
                
                <!-- Delete Button -->
                <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" class="inline"
                      onsubmit="return confirm('Are you sure you want to delete this banner? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800 dark:hover:bg-red-900/30">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete Banner
                    </button>
                </form>
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

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Image preview functionality
            function previewImage(input, previewId) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.getElementById(previewId);
                        if (img) {
                            img.src = e.target.result;
                            img.classList.remove('hidden');
                        }
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
                    console.log('Banner images uploaded:', event.detail.files);
                    updatePreview();
                    
                    // Optionally reload the page to show updated images
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }
            });

            document.addEventListener('file-deleted', function(event) {
                if (event.detail.component === 'banner-image-uploader') {
                    console.log('Banner image deleted:', event.detail.file);
                    updatePreview();
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
                
                const existingCounter = document.getElementById(inputId + '_counter');
                if (existingCounter) return; // Don't create if already exists
                
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

            // Initialize preview
            updatePreview();
        });

        // Update preview function
        function updatePreview() {
            const title = document.getElementById('title').value || 'Banner Title';
            const subtitle = document.getElementById('subtitle').value || '';
            const description = document.getElementById('description').value || '';
            const buttonText = document.getElementById('button_text').value || '';
            
            // Use existing banner image as background if available
            const backgroundImage = '{{ $banner->hasDesktopImage() ? $banner->imageUrl : "" }}';
            const backgroundStyle = backgroundImage ? 
                `background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('${backgroundImage}'); background-size: cover; background-position: center;` :
                'background: linear-gradient(to right, #2563eb, #7c3aed);';
            
            const previewHtml = `
                <div class="relative rounded-lg p-6 text-white min-h-32" style="${backgroundStyle}">
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

        // Remove image function
        function removeImage(imageType) {
            if (confirm(`Are you sure you want to remove the ${imageType} image?`)) {
                fetch('{{ route("admin.banners.delete-image", $banner) }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        image_type: imageType
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload the page to reflect changes
                        window.location.reload();
                    } else {
                        alert('Failed to remove image: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while removing the image.');
                });
            }
        }
    </script>
    @endpush
</x-layouts.admin>