<x-layouts.admin title="Create New Banner Category">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Banner Categories' => route('admin.banner-categories.index'), 'Create New Category' => '']" />

    
        <form action="{{ route('admin.banner-categories.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="flex flex-col lg:flex-row gap-6">
                <div class="w-full lg:flex-1 space-y-6">
                    <!-- Basic Information -->
                    <x-admin.form-section title="Category Information"
                        description="Create a new category to organize your banners">
                        <div class="space-y-6">
                            <x-admin.input name="name" label="Category Name" placeholder="Enter category name..."
                                :value="old('name')" required
                                helper="This will be displayed as the category name in your banner management" />

                            <x-admin.input name="slug" label="URL Slug" placeholder="Auto-generated from name"
                                :value="old('slug')"
                                helper="Leave blank to auto-generate from category name. This will be used in URLs and component calls." />

                            <x-admin.textarea name="description" label="Description"
                                placeholder="Brief description of this category..." :value="old('description')" rows="4"
                                helper="Optional description that explains what this banner category is for" />
                        </div>
                    </x-admin.form-section>
                </div>
                <div class="w-full lg:w-80 space-y-6">
                    <!-- Settings -->
                    <x-admin.form-section title="Category Settings" description="Configure display and ordering options">
                        <div class="space-y-6">
                            <x-admin.input type="number" name="display_order" label="Display Order" placeholder="0"
                                :value="old('display_order', 0)" min="0"
                                helper="Lower numbers will appear first. Leave 0 for default ordering." />

                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_active" value="1"
                                        {{ old('is_active', true) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active Category</span>
                                </label>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Only active categories will be
                                    available for banner assignment</p>
                            </div>
                        </div>
                    </x-admin.form-section>
                    
                <x-admin.button type="submit" color="primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Create Category
                </x-admin.button>
                </div>
            
            </div>
            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                <x-admin.button color="light" href="{{ route('admin.banner-categories.index') }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Categories
                </x-admin.button>

            </div>
        </form>
    

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const nameInput = document.getElementById('name');
                const slugInput = document.getElementById('slug');
                const slugPreview = document.getElementById('slug-preview');
                let slugModified = false;

                // Auto-generate slug from name
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

                // Initialize preview
                if (slugInput.value) {
                    slugPreview.textContent = slugInput.value;
                }
            });
        </script>
    @endpush
</x-layouts.admin>
