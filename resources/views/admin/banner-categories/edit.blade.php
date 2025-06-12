<x-layouts.admin title="Edit Banner Category">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Banner Categories' => route('admin.banner-categories.index'), 'Edit Category' => '']" />


    <form action="{{ route('admin.banner-categories.update', $bannerCategory) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        <div class="w-full">
            <!-- Category Statistics -->
            <x-admin.form-section title="Category Statistics" description="Information about this category's usage">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Banners in this category</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $bannerCategory->banners()->count() }}</div>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Active banners</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $bannerCategory->banners()->where('is_active', true)->count() }}</div>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Created</div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $bannerCategory->created_at->format('M j, Y') }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $bannerCategory->created_at->diffForHumans() }}</div>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Last updated</div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $bannerCategory->updated_at->format('M j, Y') }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $bannerCategory->updated_at->diffForHumans() }}</div>
                    </div>
                </div>

                @if ($bannerCategory->banners()->count() > 0)
                    <div class="mt-4">
                        <x-admin.help-text type="info">
                            This category is currently used by {{ $bannerCategory->banners()->count() }}
                            {{ Str::plural('banner', $bannerCategory->banners()->count()) }}.
                            <a href="{{ route('admin.banners.index', ['category' => $bannerCategory->id]) }}"
                                class="underline hover:no-underline">
                                View banners in this category
                            </a>
                        </x-admin.help-text>
                    </div>
                @endif
            </x-admin.form-section>
            <div class="flex flex-col lg:flex-row gap-6">
                <div class="w-full lg:flex-1 space-y-6">
                    <!-- Basic Information -->
                    <x-admin.form-section title="Category Information" description="Update the banner category details">
                        <div class="space-y-6">
                            <x-admin.input name="name" label="Category Name" placeholder="Enter category name..."
                                :value="old('name', $bannerCategory->name)" required
                                helper="This will be displayed as the category name in your banner management" />

                            <x-admin.input name="slug" label="URL Slug" placeholder="Auto-generated from name"
                                :value="old('slug', $bannerCategory->slug)"
                                helper="This will be used in URLs and component calls. Changing this may break existing implementations." />

                            <x-admin.textarea name="description" label="Description"
                                placeholder="Brief description of this category..." :value="old('description', $bannerCategory->description)" rows="4"
                                helper="Optional description that explains what this banner category is for" />
                        </div>
                    </x-admin.form-section>
                </div>
                <div class="w-full lg:w-80 space-y-6">
                    <!-- Settings -->
                    <x-admin.form-section title="Category Settings"
                        description="Configure display and ordering options">
                        <div class="space-y-6">
                            <x-admin.input type="number" name="display_order" label="Display Order" placeholder="0"
                                :value="old('display_order', $bannerCategory->display_order)" min="0"
                                helper="Lower numbers will appear first. Leave 0 for default ordering." />

                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_active" value="1"
                                        {{ old('is_active', $bannerCategory->is_active) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active Category</span>
                                </label>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Only active categories will be
                                    available for banner assignment</p>
                            </div>
                        </div>
                    </x-admin.form-section>
                    <div class="flex items-center justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-4">

                            <button type="submit" name="action" value="save"
                                class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                Update
                            </button>
                        </div>
    </form>
    </div>

    </div>

    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
        <div class="flex gap-3">
            <x-admin.button color="light" href="{{ route('admin.banner-categories.index') }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back
            </x-admin.button>
        </div>
        <div class="flex gap-3">
            <form id="delete-form" action="{{ route('admin.banner-categories.destroy', $bannerCategory) }}"
                method="POST">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800 dark:hover:bg-red-900/30">
                    <svg class="w-4 h-4 mr-2" onclick="confirmDelete()" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete Banner
                </button>
            </form>
        </div>


    </div>

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
