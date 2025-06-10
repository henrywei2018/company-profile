<x-layouts.admin title="Edit Banner Category">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Banner Categories' => route('admin.banner-categories.index'), 'Edit Category' => '']" />

    <div class="max-w-2xl">
        <form action="{{ route('admin.banner-categories.update', $bannerCategory) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <x-admin.form-section 
                title="Category Information" 
                description="Update the banner category details"
            >
                <div class="space-y-6">
                    <x-admin.input
                        name="name"
                        label="Category Name"
                        placeholder="Enter category name..."
                        :value="old('name', $bannerCategory->name)"
                        required
                        helper="This will be displayed as the category name in your banner management"
                    />
                    
                    <x-admin.input
                        name="slug"
                        label="URL Slug"
                        placeholder="Auto-generated from name"
                        :value="old('slug', $bannerCategory->slug)"
                        helper="This will be used in URLs and component calls. Changing this may break existing implementations."
                    />
                    
                    <x-admin.textarea
                        name="description"
                        label="Description"
                        placeholder="Brief description of this category..."
                        :value="old('description', $bannerCategory->description)"
                        rows="4"
                        helper="Optional description that explains what this banner category is for"
                    />
                </div>
            </x-admin.form-section>

            <!-- Settings -->
            <x-admin.form-section title="Category Settings" description="Configure display and ordering options">
                <div class="space-y-6">
                    <x-admin.input
                        type="number"
                        name="display_order"
                        label="Display Order"
                        placeholder="0"
                        :value="old('display_order', $bannerCategory->display_order)"
                        min="0"
                        helper="Lower numbers will appear first. Leave 0 for default ordering."
                    />
                    
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active', $bannerCategory->is_active) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active Category</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Only active categories will be available for banner assignment</p>
                    </div>
                </div>
            </x-admin.form-section>

            <!-- Category Statistics -->
            <x-admin.form-section 
                title="Category Statistics" 
                description="Information about this category's usage"
            >
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Banners in this category</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $bannerCategory->banners()->count() }}</div>
                    </div>
                    
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Active banners</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $bannerCategory->banners()->where('is_active', true)->count() }}</div>
                    </div>
                    
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Created</div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $bannerCategory->created_at->format('M j, Y') }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $bannerCategory->created_at->diffForHumans() }}</div>
                    </div>
                    
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Last updated</div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $bannerCategory->updated_at->format('M j, Y') }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $bannerCategory->updated_at->diffForHumans() }}</div>
                    </div>
                </div>
                
                @if($bannerCategory->banners()->count() > 0)
                    <div class="mt-4">
                        <x-admin.help-text type="info">
                            This category is currently used by {{ $bannerCategory->banners()->count() }} {{ Str::plural('banner', $bannerCategory->banners()->count()) }}.
                            <a href="{{ route('admin.banners.index', ['category' => $bannerCategory->id]) }}" class="underline hover:no-underline">
                                View banners in this category
                            </a>
                        </x-admin.help-text>
                    </div>
                @endif
            </x-admin.form-section>

            <!-- Usage Examples -->
            <x-admin.form-section title="Usage Examples" description="How to use this category in your templates">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Component Usage:</h4>
                    <code class="text-sm text-gray-700 dark:text-gray-300 block mb-2">
                        &lt;x-banner-slider categorySlug="<span id="slug-preview">{{ $bannerCategory->slug }}</span>" /&gt;
                    </code>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                        Use this component tag in your Blade templates to display banners from this category.
                    </p>
                    
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Example Implementation:</h4>
                    <code class="text-sm text-gray-700 dark:text-gray-300 block">
                        // In your Blade template<br>
                        @if($bannerCategory->slug === 'homepage-hero')<br>
                        &nbsp;&nbsp;&lt;x-banner-slider categorySlug="homepage-hero" /&gt;<br>
                        @endif
                    </code>
                </div>
            </x-admin.form-section>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="flex gap-3">
                    <x-admin.button color="light" href="{{ route('admin.banner-categories.index') }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Categories
                    </x-admin.button>
                    
                    @if($bannerCategory->banners()->count() === 0)
                        <x-admin.button 
                            color="danger" 
                            type="button"
                            onclick="confirmDelete()"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete Category
                        </x-admin.button>
                    @endif
                </div>
                
                <x-admin.button type="submit" color="primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update Category
                </x-admin.button>
            </div>
        </form>
        
        <!-- Hidden Delete Form -->
        @if($bannerCategory->banners()->count() === 0)
            <form id="delete-form" action="{{ route('admin.banner-categories.destroy', $bannerCategory) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const nameInput = document.getElementById('name');
            const slugInput = document.getElementById('slug');
            const slugPreview = document.getElementById('slug-preview');
            let slugModified = false;

            // Check if slug was manually set
            if (slugInput.value && slugInput.value !== '') {
                slugModified = true;
            }

            // Auto-generate slug from name (but only if it's not manually modified)
            nameInput.addEventListener('input', function() {
                if (!slugModified) {
                    const name = this.value;
                    const slug = name.toLowerCase()
                        .replace(/[^a-z0-9 -]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-')
                        .replace(/^-|-$/g, '');
                    
                    slugInput.value = slug;
                    slugPreview.textContent = slug || 'category-slug';
                }
            });
            
            // Mark slug as manually modified
            slugInput.addEventListener('input', function() {
                slugModified = true;
                slugPreview.textContent = this.value || 'category-slug';
            });
        });
        
        // Delete confirmation
        function confirmDelete() {
            if (confirm('Are you sure you want to delete this banner category? This action cannot be undone.')) {
                document.getElementById('delete-form').submit();
            }
        }
    </script>
    @endpush
</x-layouts.admin>