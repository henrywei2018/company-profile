<x-layouts.admin title="Create Post Category">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Posts' => route('admin.posts.index'), 'Categories' => route('admin.post-categories.index'), 'Create Category' => '']" />

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Post Category</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Create a new category to organize your blog posts</p>
        </div>
        
        <div class="flex items-center gap-3">
            <x-admin.button color="light" href="{{ route('admin.post-categories.index') }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Categories
            </x-admin.button>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.post-categories.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Basic Information -->
        <x-admin.form-section title="Basic Information" description="Provide the basic details for the category.">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.input 
                    name="name" 
                    label="Category Name" 
                    placeholder="Enter category name"
                    :value="old('name')"
                    required
                    helper="The display name for this category" />
                
                <x-admin.input 
                    name="slug" 
                    label="Slug" 
                    placeholder="category-slug (auto-generated if empty)"
                    :value="old('slug')"
                    helper="URL-friendly version of the name. Leave empty to auto-generate." />
            </div>
            
            <x-admin.textarea 
                name="description" 
                label="Description" 
                placeholder="Enter category description"
                :value="old('description')"
                rows="4"
                helper="Optional description for this category" />
        </x-admin.form-section>

        <!-- SEO Settings -->
        <x-admin.form-section title="SEO Settings" description="Optimize this category for search engines.">
            <div class="space-y-4">
                <x-admin.input 
                    name="seo_title" 
                    label="SEO Title" 
                    placeholder="Enter SEO title (max 60 characters)"
                    :value="old('seo_title')"
                    maxlength="60"
                    helper="Title that appears in search engine results. Leave empty to use category name." />
                
                <x-admin.textarea 
                    name="seo_description" 
                    label="SEO Description" 
                    placeholder="Enter SEO description (max 160 characters)"
                    :value="old('seo_description')"
                    rows="3"
                    maxlength="160"
                    helper="Description that appears in search engine results." />
                
                <x-admin.input 
                    name="seo_keywords" 
                    label="SEO Keywords" 
                    placeholder="keyword1, keyword2, keyword3"
                    :value="old('seo_keywords')"
                    helper="Comma-separated keywords related to this category." />
            </div>
        </x-admin.form-section>

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
            <x-admin.button type="button" color="light" href="{{ route('admin.post-categories.index') }}">
                Cancel
            </x-admin.button>
            
            <x-admin.button type="submit" color="primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Create Category
            </x-admin.button>
        </div>
    </form>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const nameInput = document.querySelector('input[name="name"]');
            const slugInput = document.querySelector('input[name="slug"]');
            
            // Auto-generate slug from name
            if (nameInput && slugInput) {
                nameInput.addEventListener('input', function() {
                    if (!slugInput.value || slugInput.dataset.userModified !== 'true') {
                        const slug = this.value
                            .toLowerCase()
                            .trim()
                            .replace(/[^a-z0-9\s-]/g, '') // Remove special characters
                            .replace(/\s+/g, '-') // Replace spaces with hyphens
                            .replace(/-+/g, '-') // Replace multiple hyphens with single
                            .replace(/^-|-$/g, ''); // Remove leading/trailing hyphens
                        
                        slugInput.value = slug;
                    }
                });
                
                // Mark slug as user-modified when manually changed
                slugInput.addEventListener('input', function() {
                    this.dataset.userModified = 'true';
                });
            }

            // Character counter for SEO fields
            const seoTitleInput = document.querySelector('input[name="seo_title"]');
            const seoDescInput = document.querySelector('textarea[name="seo_description"]');

            function addCharacterCounter(input, maxLength) {
                if (!input) return;
                
                const counter = document.createElement('div');
                counter.className = 'text-xs text-gray-500 dark:text-gray-400 mt-1 text-right';
                
                function updateCounter() {
                    const remaining = maxLength - input.value.length;
                    counter.textContent = `${input.value.length}/${maxLength} characters`;
                    
                    if (remaining < 10) {
                        counter.className = 'text-xs text-red-500 mt-1 text-right';
                    } else if (remaining < 20) {
                        counter.className = 'text-xs text-amber-500 mt-1 text-right';
                    } else {
                        counter.className = 'text-xs text-gray-500 dark:text-gray-400 mt-1 text-right';
                    }
                }
                
                input.addEventListener('input', updateCounter);
                input.parentNode.appendChild(counter);
                updateCounter();
            }

            addCharacterCounter(seoTitleInput, 60);
            addCharacterCounter(seoDescInput, 160);
        });
    </script>
    @endpush
</x-layouts.admin>