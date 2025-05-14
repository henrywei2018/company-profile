<!-- resources/views/admin/blog/create.blade.php -->
<x-admin-layout :title="isset($post) ? 'Edit Article: ' . $post->title : 'Create New Article'">
    <div class="mb-6">
        <a href="{{ route('admin.blog.index') }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900">
            <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Articles
        </a>
    </div>

    <form action="{{ isset($post) ? route('admin.blog.update', $post->id) : route('admin.blog.store') }}" 
          method="POST" 
          enctype="multipart/form-data" 
          class="space-y-8">
        @csrf
        @if(isset($post))
            @method('PUT')
        @endif

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ isset($post) ? 'Edit Article Details' : 'Article Details' }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ isset($post) ? 'Update the information for this blog article.' : 'Fill in the details for the new blog article.' }}
                </p>
            </div>

            <div class="p-6 bg-white space-y-6">
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <!-- Article Title -->
                    <div class="sm:col-span-4">
                        <label for="title" class="block text-sm font-medium text-gray-700">Article Title</label>
                        <div class="mt-1">
                            <input type="text" 
                                   name="title" 
                                   id="title" 
                                   value="{{ old('title', isset($post) ? $post->title : '') }}" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                   required>
                        </div>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Slug -->
                    <div class="sm:col-span-4">
                        <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
                        <div class="mt-1">
                            <input type="text" 
                                   name="slug" 
                                   id="slug" 
                                   value="{{ old('slug', isset($post) ? $post->slug : '') }}" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Leave blank to auto-generate from title.</p>
                        @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Categories -->
                    <div class="sm:col-span-3">
                        <label for="categories" class="block text-sm font-medium text-gray-700">Categories</label>
                        <div class="mt-1">
                            <select id="categories" 
                                    name="categories[]" 
                                    multiple 
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ in_array($category->id, old('categories', isset($post) ? $post->categories->pluck('id')->toArray() : [])) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Hold Ctrl (or Cmd) to select multiple categories.</p>
                        @error('categories')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="sm:col-span-3">
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <div class="mt-1">
                            <select id="status" 
                                    name="status" 
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                <option value="draft" {{ old('status', isset($post) ? $post->status : 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status', isset($post) ? $post->status : '') == 'published' ? 'selected' : '' }}>Published</option>
                            </select>
                        </div>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Publication Date -->
                    <div class="sm:col-span-3">
                        <label for="published_at" class="block text-sm font-medium text-gray-700">Publication Date</label>
                        <div class="mt-1">
                            <input type="datetime-local" 
                                   name="published_at" 
                                   id="published_at" 
                                   value="{{ old('published_at', isset($post) && $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">When the article should be published. Only applies if status is "Published".</p>
                        @error('published_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Author/User Selection (if needed) -->
                    <div class="sm:col-span-3">
                        <label for="user_id" class="block text-sm font-medium text-gray-700">Author</label>
                        <div class="mt-1">
                            <select id="user_id" 
                                    name="user_id" 
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" 
                                            {{ old('user_id', isset($post) ? $post->user_id : auth()->id()) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('user_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Featured Toggle -->
                    <div class="sm:col-span-6">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" 
                                       name="featured" 
                                       id="featured" 
                                       value="1" 
                                       {{ old('featured', isset($post) && $post->featured ? 'checked' : '') }} 
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="featured" class="font-medium text-gray-700">Featured Article</label>
                                <p class="text-gray-500">Featured articles appear on the homepage and at the top of the blog list.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Article Content -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Article Content</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Write your article content using the editor below.
                </p>
            </div>

            <div class="p-6 bg-white space-y-6">
                <!-- Excerpt -->
                <div>
                    <label for="excerpt" class="block text-sm font-medium text-gray-700">Excerpt (Summary)</label>
                    <div class="mt-1">
                        <textarea id="excerpt" 
                                  name="excerpt" 
                                  rows="3" 
                                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('excerpt', isset($post) ? $post->excerpt : '') }}</textarea>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">A brief summary of the article (optional). If left blank, an excerpt will be generated from the content.</p>
                    @error('excerpt')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Content Editor -->
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                    <div class="mt-1">
                        <textarea id="content" 
                                  name="content" 
                                  rows="20" 
                                  class="tinymce-editor shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('content', isset($post) ? $post->content : '') }}</textarea>
                    </div>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Featured Image -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Featured Image</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Upload an image to be displayed with this article.
                </p>
            </div>

            <div class="p-6 bg-white">
                <div class="mb-6">
                    <label for="featured_image" class="block text-sm font-medium text-gray-700">Featured Image</label>
                    @if(isset($post) && $post->featured_image)
                        <div class="mt-2 mb-3">
                            <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="h-48 w-auto object-cover rounded-md">
                        </div>
                    @endif
                    <div class="mt-1">
                        <input type="file" 
                               id="featured_image" 
                               name="featured_image" 
                               accept="image/*" 
                               class="focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">This image will be displayed as the main image for this article. Recommended size: 1200×800 pixels.</p>
                    @error('featured_image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image Alt Text -->
                <div>
                    <label for="image_alt" class="block text-sm font-medium text-gray-700">Image Alt Text</label>
                    <div class="mt-1">
                        <input type="text" 
                               name="image_alt" 
                               id="image_alt" 
                               value="{{ old('image_alt', isset($post) ? $post->image_alt : '') }}" 
                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Descriptive text for the featured image (for accessibility and SEO).</p>
                    @error('image_alt')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- SEO Settings -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">SEO Settings</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Optimize this article for search engines.
                </p>
            </div>

            <div class="p-6 bg-white space-y-6">
                <!-- Meta Title -->
                <div>
                    <label for="meta_title" class="block text-sm font-medium text-gray-700">Meta Title</label>
                    <div class="mt-1">
                        <input type="text" 
                               id="meta_title" 
                               name="meta_title" 
                               value="{{ old('meta_title', isset($post->seo) ? $post->seo->title : '') }}" 
                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Keep it under 60 characters. Leave blank to use the article title.</p>
                    @error('meta_title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Meta Description -->
                <div>
                    <label for="meta_description" class="block text-sm font-medium text-gray-700">Meta Description</label>
                    <div class="mt-1">
                        <textarea id="meta_description" 
                                  name="meta_description" 
                                  rows="2" 
                                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('meta_description', isset($post->seo) ? $post->seo->description : '') }}</textarea>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Keep it between 150-160 characters for best results.</p>
                    @error('meta_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Meta Keywords -->
                <div>
                    <label for="meta_keywords" class="block text-sm font-medium text-gray-700">Meta Keywords</label>
                    <div class="mt-1">
                        <input type="text" 
                               id="meta_keywords" 
                               name="meta_keywords" 
                               value="{{ old('meta_keywords', isset($post->seo) ? $post->seo->keywords : '') }}" 
                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Separate keywords with commas. Example: construction, building, design</p>
                    @error('meta_keywords')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- OG Image -->
                <div>
                    <label for="og_image" class="block text-sm font-medium text-gray-700">Social Media Image</label>
                    @if(isset($post->seo) && $post->seo->og_image)
                        <div class="mt-2 mb-3">
                            <img src="{{ asset('storage/' . $post->seo->og_image) }}" alt="Social Media Image" class="h-32 w-auto object-cover rounded-md">
                        </div>
                    @endif
                    <div class="mt-1">
                        <input type="file" 
                               id="og_image" 
                               name="og_image" 
                               accept="image/*" 
                               class="focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">This image will be used when sharing on social media. Recommended size: 1200×630 pixels.</p>
                    @error('og_image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Form Buttons -->
        <div class="flex justify-end">
            <a href="{{ route('admin.blog.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Cancel
            </a>
            <button type="submit" name="save_draft" value="1" class="ml-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Save as Draft
            </button>
            <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ isset($post) ? 'Update Article' : 'Publish Article' }}
            </button>
        </div>
    </form>
</x-admin-layout>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tinymce@5/dist/skins/content/default/content.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tinymce@5/tinymce.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Title to Slug Generation
        const titleInput = document.getElementById('title');
        const slugInput = document.getElementById('slug');
        
        if (titleInput && slugInput) {
            titleInput.addEventListener('blur', function() {
                if (slugInput.value === '') {
                    slugInput.value = titleInput.value
                        .toLowerCase()
                        .replace(/[^\w\s-]/g, '')
                        .replace(/[\s_-]+/g, '-')
                        .replace(/^-+|-+$/g, '');
                }
            });
        }
        
        // Initialize TinyMCE
        tinymce.init({
            selector: 'textarea.tinymce-editor',
            height: 500,
            menubar: true,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount'
            ],
            toolbar: 'undo redo | formatselect | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | link image | code | help',
            images_upload_url: '{{ route("admin.media.upload") }}',
            images_upload_handler: function (blobInfo, success, failure) {
                var xhr, formData;
                xhr = new XMLHttpRequest();
                xhr.withCredentials = false;
                xhr.open('POST', '{{ route("admin.media.upload") }}');
                xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                xhr.onload = function() {
                    var json;
                    if (xhr.status != 200) {
                        failure('HTTP Error: ' + xhr.status);
                        return;
                    }
                    json = JSON.parse(xhr.responseText);
                    if (!json || typeof json.location != 'string') {
                        failure('Invalid JSON: ' + xhr.responseText);
                        return;
                    }
                    success(json.location);
                };
                formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());
                xhr.send(formData);
            },
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            }
        });
    });
</script>
@endpush