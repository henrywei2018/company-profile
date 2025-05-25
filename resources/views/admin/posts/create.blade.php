<x-layouts.admin title="Create New Post">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Posts' => route('admin.posts.index'), 'Create New Post' => '']" />

    <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Main Content -->
            <div class="flex-1 space-y-6">
                <!-- Basic Information -->
                <x-admin.form-section title="Basic Information" description="Enter the main details for your post">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-admin.input
                                name="title"
                                label="Post Title"
                                placeholder="Enter post title..."
                                :value="old('title')"
                                required
                                helper="This will be the main heading of your post"
                            />
                        </div>
                        
                        <div class="md:col-span-2">
                            <x-admin.input
                                name="slug"
                                label="URL Slug"
                                placeholder="Auto-generated from title"
                                :value="old('slug')"
                                helper="Leave blank to auto-generate from title"
                            />
                        </div>
                        
                        <div class="md:col-span-2">
                            <x-admin.textarea
                                name="excerpt"
                                label="Excerpt"
                                placeholder="Brief description of the post..."
                                :value="old('excerpt')"
                                rows="3"
                                helper="Optional short summary that appears in post listings"
                            />
                        </div>
                    </div>
                </x-admin.form-section>

                <!-- Content -->
                <x-admin.form-section title="Content" description="Write your post content">
                    <x-admin.rich-editor
                        name="content"
                        label="Post Content"
                        :value="old('content')"
                        placeholder="Start writing your post..."
                        required
                        minHeight="400px"
                    />
                </x-admin.form-section>

                <!-- SEO Settings -->
                <x-admin.form-section title="SEO Settings" description="Optimize your post for search engines">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-admin.input
                                name="seo_title"
                                label="Meta Title"
                                placeholder="SEO optimized title"
                                :value="old('seo_title')"
                                helper="Leave blank to use post title"
                            />
                        </div>
                        
                        <div class="md:col-span-2">
                            <x-admin.textarea
                                name="seo_description"
                                label="Meta Description"
                                placeholder="Brief description for search engines..."
                                :value="old('seo_description')"
                                rows="3"
                                helper="Recommended length: 150-160 characters"
                            />
                        </div>
                        
                        <div class="md:col-span-2">
                            <x-admin.input
                                name="seo_keywords"
                                label="Meta Keywords"
                                placeholder="keyword1, keyword2, keyword3"
                                :value="old('seo_keywords')"
                                helper="Comma-separated keywords"
                            />
                        </div>
                    </div>
                </x-admin.form-section>
            </div>

            <!-- Sidebar -->
            <div class="w-full lg:w-80 space-y-6">
                <!-- Publishing Options -->
                <x-admin.card title="Publishing">
                    <div class="space-y-4">
                        <x-admin.select
                            name="status"
                            label="Status"
                            :options="['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived']"
                            :selected="old('status', 'draft')"
                            required
                        />
                        
                        <x-admin.input
                            type="datetime-local"
                            name="published_at"
                            label="Publish Date"
                            :value="old('published_at')"
                            helper="Leave blank to publish immediately"
                        />
                        
                        <x-admin.checkbox
                            name="featured"
                            label="Featured Post"
                            :checked="old('featured', false)"
                            helper="Featured posts appear prominently on the website"
                        />
                    </div>
                </x-admin.card>

                <!-- Categories -->
                <x-admin.card title="Categories">
                    <div class="space-y-3">
                        @forelse($categories as $category)
                            <x-admin.checkbox
                                name="categories[]"
                                :value="$category->id"
                                :label="$category->name"
                                :checked="in_array($category->id, old('categories', []))"
                            />
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">No categories available.</p>
                            <a href="{{ route('admin.post-categories.create') }}" class="text-sm text-blue-600 hover:underline">
                                Create your first category
                            </a>
                        @endforelse
                    </div>
                </x-admin.card>

                <!-- Featured Image -->
                <x-admin.card title="Featured Image">
                    <x-admin.file-upload
                        name="featured_image"
                        label=""
                        accept="image/*"
                        helper="Recommended size: 1200x630px"
                    >
                        Upload image (max 2MB)
                    </x-admin.file-upload>
                </x-admin.card>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
            <x-admin.button color="light" href="{{ route('admin.posts.index') }}">
                Cancel
            </x-admin.button>
            
            <div class="flex gap-3">
                <x-admin.button type="submit" name="action" value="draft" color="light">
                    Save as Draft
                </x-admin.button>
                
                <x-admin.button type="submit" name="action" value="publish" color="primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Publish Post
                </x-admin.button>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        // Auto-generate slug from title
        document.getElementById('title').addEventListener('input', function() {
            const title = this.value;
            const slug = title.toLowerCase()
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