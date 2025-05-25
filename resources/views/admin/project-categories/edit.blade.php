<x-layouts.admin title="Edit Category">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Post Categories' => route('admin.post-categories.index'), 'Edit Category' => '']" />

    <div class="max-w-2xl">
        <form action="{{ route('admin.post-categories.update', $postCategory) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <x-admin.form-section 
                title="Category Information" 
                description="Update the category details"
            >
                <div class="space-y-6">
                    <x-admin.input
                        name="name"
                        label="Category Name"
                        placeholder="Enter category name..."
                        :value="old('name', $postCategory->name)"
                        required
                        helper="This will be displayed as the category name on your website"
                    />
                    
                    <x-admin.input
                        name="slug"
                        label="URL Slug"
                        placeholder="Auto-generated from name"
                        :value="old('slug', $postCategory->slug)"
                        helper="This will be used in URLs. Changing this may break existing links."
                    />
                    
                    <x-admin.textarea
                        name="description"
                        label="Description"
                        placeholder="Brief description of this category..."
                        :value="old('description', $postCategory->description)"
                        rows="4"
                        helper="Optional description that explains what this category is about"
                    />
                </div>
            </x-admin.form-section>

            <!-- Category Statistics -->
            <x-admin.form-section 
                title="Category Statistics" 
                description="Information about this category's usage"
            >
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Posts in this category</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $postCategory->posts_count ?? 0 }}</div>
                    </div>
                    
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Created</div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $postCategory->created_at->format('M j, Y') }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $postCategory->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                
                @if($postCategory->posts_count > 0)
                    <div class="mt-4">
                        <x-admin.help-text type="info">
                            This category is currently used by {{ $postCategory->posts_count }} {{ Str::plural('post', $postCategory->posts_count) }}.
                            <a href="{{ route('admin.posts.index', ['category' => $postCategory->id]) }}" class="underline hover:no-underline">
                                View posts in this category
                            </a>
                        </x-admin.help-text>
                    </div>
                @endif
            </x-admin.form-section>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="flex gap-3">
                    <x-admin.button color="light" href="{{ route('admin.post-categories.index') }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Categories
                    </x-admin.button>
                    
                    @if($postCategory->posts_count === 0)
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
        @if($postCategory->posts_count === 0)
            <form id="delete-form" action="{{ route('admin.post-categories.destroy', $postCategory) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>

    @push('scripts')
    <script>
        // Auto-generate slug from name (but only if it's not manually modified)
        let slugModified = false;
        
        document.getElementById('name').addEventListener('input', function() {
            if (slugModified) return;
            
            const name = this.value;
            const slug = name.toLowerCase()
                .replace(/[^a-z0-9 -]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
            
            document.getElementById('slug').value = slug;
        });
        
        // Mark slug as manually modified
        document.getElementById('slug').addEventListener('input', function() {
            slugModified = true;
        });
        
        // Delete confirmation
        function confirmDelete() {
            if (confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
                document.getElementById('delete-form').submit();
            }
        }
    </script>
    @endpush
</x-layouts.admin>