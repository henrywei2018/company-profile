<x-layouts.admin title="Create New Category">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Post Categories' => route('admin.post-categories.index'), 'Create New Category' => '']" />

    <div class="max-w-2xl">
        <form action="{{ route('admin.post-categories.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Basic Information -->
            <x-admin.form-section title="Category Information" description="Create a new category to organize your blog posts">
                <div class="space-y-6">
                    <x-admin.input
                        name="name"
                        label="Category Name"
                        placeholder="Enter category name..."
                        :value="old('name')"
                        required
                        helper="This will be displayed as the category name on your website"
                    />
                    
                    <x-admin.input
                        name="slug"
                        label="URL Slug"
                        placeholder="Auto-generated from name"
                        :value="old('slug')"
                        helper="Leave blank to auto-generate from category name. This will be used in URLs."
                    />
                    
                    <x-admin.textarea
                        name="description"
                        label="Description"
                        placeholder="Brief description of this category..."
                        :value="old('description')"
                        rows="4"
                        helper="Optional description that explains what this category is about"
                    />
                </div>
            </x-admin.form-section>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                <x-admin.button color="light" href="{{ route('admin.post-categories.index') }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Categories
                </x-admin.button>
                
                <x-admin.button type="submit" color="primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create Category
                </x-admin.button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        // Auto-generate slug from name
        document.getElementById('name').addEventListener('input', function() {
            const name = this.value;
            const slug = name.toLowerCase()
                .replace(/[^a-z0-9 -]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
            
            const slugField = document.getElementById('slug');
            if (!slugField.dataset.modified) {
                slugField.value = slug;
            }
        });
        
        // Mark slug as manually modified
        document.getElementById('slug').addEventListener('input', function() {
            this.dataset.modified = 'true';
        });
    </script>
    @endpush
</x-layouts.admin>