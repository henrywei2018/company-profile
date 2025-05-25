<x-layouts.admin title="Edit Post">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Posts' => route('admin.posts.index'), 'Edit Post' => '']" />

    <form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Main Content -->
            <div class="flex-1 space-y-6">
                <!-- Basic Information -->
                <x-admin.form-section title="Basic Information" description="Update the main details for your post">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-admin.input
                                name="title"
                                label="Post Title"
                                placeholder="Enter post title..."
                                :value="old('title', $post->title)"
                                required
                                helper="This will be the main heading of your post"
                            />
                        </div>
                        
                        <div class="md:col-span-2">
                            <x-admin.input
                                name="slug"
                                label="URL Slug"
                                placeholder="Auto-generated from title"
                                :value="old('slug', $post->slug)"
                                helper="Changing this may break existing links to this post"
                            />
                        </div>
                        
                        <div class="md:col-span-2">
                            <x-admin.textarea
                                name="excerpt"
                                label="Excerpt"
                                placeholder="Brief description of the post..."
                                :value="old('excerpt', $post->excerpt)"
                                rows="3"
                                helper="Optional short summary that appears in post listings"
                            />
                        </div>
                    </div>
                </x-admin.form-section>

                <!-- Content -->
                <x-admin.form-section title="Content" description="Update your post content">
                    <x-admin.rich-editor
                        name="content"
                        label="Post Content"
                        :value="old('content', $post->content)"
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
                                :value="old('seo_title', $post->seo->title ?? '')"
                                helper="Leave blank to use post title"
                            />
                        </div>
                        
                        <div class="md:col-span-2">
                            <x-admin.textarea
                                name="seo_description"
                                label="Meta Description"
                                placeholder="Brief description for search engines..."
                                :value="old('seo_description', $post->seo->description ?? '')"
                                rows="3"
                                helper="Recommended length: 150-160 characters"
                            />
                        </div>
                        
                        <div class="md:col-span-2">
                            <x-admin.input
                                name="seo_keywords"
                                label="Meta Keywords"
                                placeholder="keyword1, keyword2, keyword3"
                                :value="old('seo_keywords', $post->seo->keywords ?? '')"
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
                            :selected="old('status', $post->status)"
                            required
                        />
                        
                        <x-admin.input
                            type="datetime-local"
                            name="published_at"
                            label="Publish Date"
                            :value="old('published_at', $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '')"
                            helper="Leave blank to publish immediately"
                        />
                        
                        <x-admin.checkbox
                            name="featured"
                            label="Featured Post"
                            :checked="old('featured', $post->featured)"
                            helper="Featured posts appear prominently on the website"
                        />
                    </div>
                </x-admin.card>

                <!-- Post Statistics -->
                <x-admin.card title="Post Statistics">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Author:</span>
                            <span class="text-sm font-medium">{{ $post->author->name }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Created:</span>
                            <span class="text-sm font-medium">{{ $post->created_at->format('M j, Y') }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Last Updated:</span>
                            <span class="text-sm font-medium">{{ $post->updated_at->diffForHumans() }}</span>
                        </div>
                        
                        @if($post->published_at)
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Published:</span>
                            <span class="text-sm font-medium">{{ $post->published_at->format('M j, Y') }}</span>
                        </div>
                        @endif
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
                                :checked="in_array($category->id, old('categories', $post->categories->pluck('id')->toArray()))"
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
                    @if($post->featured_image)
                        <div class="mb-4">
                            <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                 alt="{{ $post->title }}" 
                                 class="w-full h-32 object-cover rounded-lg">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Current featured image</p>
                        </div>
                    @endif
                    
                    <x-admin.file-upload
                        name="featured_image"
                        label=""
                        accept="image/*"
                        helper="Upload a new image to replace the current one. Recommended size: 1200x630px"
                    >
                        {{ $post->featured_image ? 'Replace image (max 2MB)' : 'Upload image (max 2MB)' }}
                    </x-admin.file-upload>
                </x-admin.card>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
            <div class="flex gap-3">
                <x-admin.button color="light" href="{{ route('admin.posts.index') }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Posts
                </x-admin.button>
                
                <x-admin.button 
                    color="danger" 
                    type="button"
                    onclick="confirmDelete()"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete Post
                </x-admin.button>
            </div>
            
            <div class="flex gap-3">
                <x-admin.button type="submit" name="action" value="draft" color="light">
                    Save as Draft
                </x-admin.button>
                
                <x-admin.button type="submit" name="action" value="update" color="primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update Post
                </x-admin.button>
            </div>
        </div>
    </form>

    <!-- Hidden Delete Form -->
    <form id="delete-form" action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
    <script>
        // Auto-generate slug from title (but only if it's not manually modified)
        let slugModified = false;
        
        document.getElementById('title').addEventListener('input', function() {
            if (slugModified) return;
            
            const title = this.value;
            const slug = title.toLowerCase()
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
            if (confirm('Are you sure you want to delete this post? This action cannot be undone.')) {
                document.getElementById('delete-form').submit();
            }
        }
    </script>
    @endpush
</x-layouts.admin>