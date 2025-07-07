<!-- resources/views/admin/service-categories/edit.blade.php -->
<x-layouts.admin title="Edit Service Category" :unreadMessages="$unreadMessages" :pendingQuotations="$pendingQuotations">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'Service Categories' => route('admin.service-categories.index'),
            'Edit Category' => route('admin.service-categories.edit', $category),
        ]" />
        
        <div class="mt-4 md:mt-0 flex flex-wrap gap-2">
            <form action="{{ route('admin.service-categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?')" class="inline">
                @csrf
                @method('DELETE')
                <x-admin.button
                    type="submit"
                    color="danger"
                >
                    <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete Category
                </x-admin.button>
            </form>
        </div>
    </div>

    <form action="{{ route('admin.service-categories.update', $category) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Main Content -->
            <div class="flex-1">
                <!-- Basic Information -->
                <x-admin.card>
                    <x-slot name="title">Category Information</x-slot>
                    <x-slot name="subtitle">Update the details of the service category</x-slot>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Category Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name"
                                   value="{{ old('name', $category->name) }}"
                                   placeholder="Enter category name"
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
                            <input type="text" 
                                   name="slug" 
                                   id="slug"
                                   value="{{ old('slug', $category->slug) }}"
                                   placeholder="Auto-generated from name"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('slug') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave blank to auto-generate from name</p>
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Description
                            </label>
                            <textarea name="description" 
                                      id="description"
                                      rows="4"
                                      placeholder="Enter category description"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm resize-y focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('description') border-red-500 @enderror">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>
            </div>

            <!-- Sidebar -->
            <div class="lg:w-80 space-y-6">
                <!-- Settings -->
                <x-admin.card>
                    <x-slot name="title">Settings</x-slot>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label for="is_active" class="text-sm font-medium text-gray-700 dark:text-gray-200">
                                Active Status
                            </label>
                            <input type="checkbox" 
                                   name="is_active" 
                                   id="is_active"
                                   value="1" 
                                   {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        </div>
                        
                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Sort Order
                            </label>
                            <input type="number" 
                                   name="sort_order" 
                                   id="sort_order"
                                   value="{{ old('sort_order', $category->sort_order ?? 0) }}"
                                   min="0"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Lower numbers appear first</p>
                        </div>
                    </div>
                </x-admin.card>

                <!-- Icon Upload -->
                <x-admin.card>
                    <x-slot name="title">Category Icon</x-slot>
                    
                    <div class="space-y-4">
                        @if($category->icon)
                            <div class="text-center">
                                <img src="{{ asset('storage/' . $category->icon) }}" 
                                     alt="Current Icon" 
                                     class="w-24 h-24 object-cover rounded border mx-auto">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Current icon</p>
                            </div>
                        @endif
                        
                        <div>
                            <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Upload New Icon
                            </label>
                            <input type="file" 
                                   name="icon" 
                                   id="icon"
                                   accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Square icon image. PNG, JPG, SVG up to 1MB.<br>
                                Recommended size: 128x128px
                            </p>
                            @error('icon')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>

                <!-- Category Stats -->
                <x-admin.card>
                    <x-slot name="title">Category Statistics</x-slot>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Total Services:</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $category->services_count ?? $category->services()->count() }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Active Services:</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $category->activeServices()->count() }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Created:</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $category->created_at->format('M d, Y') }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Last Updated:</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $category->updated_at->format('M d, Y') }}
                            </span>
                        </div>
                    </div>
                </x-admin.card>
            </div>
        </div>

        <!-- Form Buttons -->
        <div class="flex justify-end mt-8 gap-3">
            <a href="{{ route('admin.service-categories.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Cancel
            </a>

            <button type="submit" 
                    class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                </svg>
                Update Category
            </button>
        </div>
    </form>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-generate slug from name
            const nameInput = document.getElementById('name');
            const slugInput = document.getElementById('slug');
            
            nameInput.addEventListener('input', function() {
                if (!slugInput.value || slugInput.value === nameInput.getAttribute('data-original-slug')) {
                    slugInput.value = generateSlug(this.value);
                }
            });
            
            function generateSlug(text) {
                return text
                    .toLowerCase()
                    .replace(/[^a-z0-9 -]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim('-');
            }
            
            // Store original slug for comparison
            nameInput.setAttribute('data-original-slug', slugInput.value);
        });
    </script>
    @endpush
</x-layouts.admin>