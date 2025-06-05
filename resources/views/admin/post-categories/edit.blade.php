<x-layouts.admin title="Edit Post Category">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Posts' => route('admin.posts.index'), 'Categories' => route('admin.post-categories.index'), $postCategory->name => '']" />

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Category: {{ $postCategory->name }}</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Update the category information and settings</p>
        </div>
        
        <div class="flex items-center gap-3">
            <!-- View Posts Button -->
            @if($postCategory->posts_count > 0)
            <x-admin.button color="info" href="{{ route('admin.posts.index', ['category' => $postCategory->id]) }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                View Posts ({{ $postCategory->posts_count }})
            </x-admin.button>
            @endif

            <x-admin.button color="light" href="{{ route('admin.post-categories.index') }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Categories
            </x-admin.button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <x-admin.stat-card 
            title="Total Posts" 
            :value="$postCategory->posts_count ?? 0"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'
            iconColor="text-blue-500" 
            iconBg="bg-blue-100 dark:bg-blue-800/30" />

        <x-admin.stat-card 
            title="Published Posts" 
            :value="$postCategory->published_posts_count ?? 0"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
            iconColor="text-green-500" 
            iconBg="bg-green-100 dark:bg-green-800/30" />

        <x-admin.stat-card 
            title="Created" 
            :value="$postCategory->created_at->format('M d, Y')"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>'
            iconColor="text-gray-500" 
            iconBg="bg-gray-100 dark:bg-gray-800/30" />
    </div>

    <!-- Form -->
    <form action="{{ route('admin.post-categories.update', $postCategory) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Basic Information -->
        <x-admin.form-section title="Basic Information" description="Update the basic details for the category.">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.input 
                    name="name" 
                    label="Category Name" 
                    placeholder="Enter category name"
                    :value="old('name', $postCategory->name)"
                    required
                    helper="The display name for this category" />
                
                <x-admin.input 
                    name="slug" 
                    label="Slug" 
                    placeholder="category-slug"
                    :value="old('slug', $postCategory->slug)"
                    helper="URL-friendly version of the name. Must be unique." />
            </div>
            
            <x-admin.textarea 
                name="description" 
                label="Description" 
                placeholder="Enter category description"
                :value="old('description', $postCategory->description)"
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
                    :value="old('seo_title', $postCategory->seo->title ?? '')"
                    maxlength="60"
                    helper="Title that appears in search engine results. Leave empty to use category name." />
                
                <x-admin.textarea 
                    name="seo_description" 
                    label="SEO Description" 
                    placeholder="Enter SEO description (max 160 characters)"
                    :value="old('seo_description', $postCategory->seo->description ?? '')"
                    rows="3"
                    maxlength="160"
                    helper="Description that appears in search engine results." />
                
                <x-admin.input 
                    name="seo_keywords" 
                    label="SEO Keywords" 
                    placeholder="keyword1, keyword2, keyword3"
                    :value="old('seo_keywords', $postCategory->seo->keywords ?? '')"
                    helper="Comma-separated keywords related to this category." />
            </div>
        </x-admin.form-section>

        <!-- Danger Zone -->
        @if($postCategory->posts_count === 0)
        <x-admin.form-section title="Danger Zone" description="Irreversible and destructive actions.">
            <div class="border border-red-200 dark:border-red-800 rounded-lg p-4 bg-red-50 dark:bg-red-900/20">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="flex-1">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Delete Category</h3>
                        <p class="text-sm text-red-600 dark:text-red-300 mt-1">
                            This category has no posts associated with it and can be safely deleted. This action cannot be undone.
                        </p>
                        <div class="mt-3">
                            <button type="button" onclick="confirmDelete()" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete Category
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </x-admin.form-section>
        @else
        <x-admin.form-section title="Category Status" description="Information about this category.">
            <div class="border border-amber-200 dark:border-amber-800 rounded-lg p-4 bg-amber-50 dark:bg-amber-900/20">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-amber-800 dark:text-amber-200">Category In Use</h3>
                        <p class="text-sm text-amber-600 dark:text-amber-300 mt-1">
                            This category has {{ $postCategory->posts_count }} post(s) associated with it and cannot be deleted. 
                            You can still update its information.
                        </p>
                    </div>
                </div>
            </div>
        </x-admin.form-section>
        @endif

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
            <x-admin.button type="button" color="light" href="{{ route('admin.post-categories.index') }}">
                Cancel
            </x-admin.button>
            
            <x-admin.button type="submit" color="primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Update Category
            </x-admin.button>
        </div>
    </form>

    <!-- Delete Form (hidden) -->
    <form id="delete-form" action="{{ route('admin.post-categories.destroy', $postCategory) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const nameInput = document.querySelector('input[name="name"]');
            const slugInput = document.querySelector('input[name="slug"]');
            
            // Auto-generate slug from name (only if slug hasn't been manually modified)
            if (nameInput && slugInput) {
                nameInput.addEventListener('input', function() {
                    if (!slugInput.dataset.userModified) {
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

        function confirmDelete() {
            if (confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
                document.getElementById('delete-form').submit();
            }
        }
    </script>
    @endpush
</x-layouts.admin>