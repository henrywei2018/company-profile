<x-layouts.admin title="Posts Management">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Posts Management</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage your blog posts and articles</p>
        </div>
        <div class="flex gap-3">
            <x-admin.button color="primary" href="{{ route('admin.posts.create') }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add New Post
            </x-admin.button>
        </div>
    </div>

    <!-- Filters -->
    <x-admin.filter action="{{ route('admin.posts.index') }}" resetRoute="{{ route('admin.posts.index') }}">
        <x-admin.select name="category" label="Category" :options="$categories->pluck('name', 'id')->toArray()" :selected="request('category')"
            placeholder="All Categories" />

        <x-admin.select name="status" label="Status" :options="['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived']" :selected="request('status')" placeholder="All Statuses" />

        <x-admin.input name="search" label="Search" placeholder="Search posts..." :value="request('search')" />
    </x-admin.filter>

    <!-- Posts Table -->
    <x-admin.card noPadding>
        <x-slot name="title">
            <div class="flex items-center justify-between w-full">
                <span>Posts ({{ $posts->total() }})</span>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Showing {{ $posts->firstItem() ?? 0 }} to {{ $posts->lastItem() ?? 0 }} of {{ $posts->total() }}
                    results
                </div>
            </div>
        </x-slot>

        <x-admin.data-table>
            <x-slot name="columns">
                <x-admin.table-column sortable field="title" :direction="request('sort') === 'title' ? request('direction') : null">
                    Title
                </x-admin.table-column>
                <x-admin.table-column>Author</x-admin.table-column>
                <x-admin.table-column>Categories</x-admin.table-column>
                <x-admin.table-column sortable field="status" :direction="request('sort') === 'status' ? request('direction') : null">
                    Status
                </x-admin.table-column>
                <x-admin.table-column sortable field="published_at" :direction="request('sort') === 'published_at' ? request('direction') : null">
                    Published
                </x-admin.table-column>
                <x-admin.table-column class="text-center">Actions</x-admin.table-column>
            </x-slot>

            @forelse($posts as $post)
                <x-admin.table-row>
                    <x-admin.table-cell highlight>
                        <div class="flex items-center gap-3">
                            @if ($post->featured_image)
                                <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}"
                                    class="w-12 h-12 rounded-lg object-cover">
                            @else
                                <div
                                    class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-white line-clamp-1">{{ $post->title }}
                                </h3>
                                @if ($post->excerpt)
                                    <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-1 mt-1">
                                        {{ \Illuminate\Support\Str::limit($post->excerpt, 70) }}</p>
                                @endif
                            </div>
                        </div>
                    </x-admin.table-cell>

                    <x-admin.table-cell>
                        <div class="flex items-center gap-2">
                            <x-admin.avatar size="sm" :src="$post->author->profile_photo_url ?? null" :placeholder="$post->author->name" />
                            
                        </div>
                    </x-admin.table-cell>

                    <x-admin.table-cell>
                        <div class="flex flex-wrap gap-1">
                            @forelse($post->categories as $category)
                                <x-admin.badge type="info" size="sm">{{ $category->name }}</x-admin.badge>
                            @empty
                                <span class="text-sm text-gray-400">No categories</span>
                            @endforelse
                        </div>
                    </x-admin.table-cell>

                    <x-admin.table-cell>
                        <div class="flex flex-col gap-1">
                            <x-admin.badge :type="$post->status === 'published' ? 'success' : ($post->status === 'draft' ? 'warning' : 'danger')" size="sm">
                                {{ ucfirst($post->status) }}
                            </x-admin.badge>
                            @if ($post->featured)
                                <x-admin.badge type="primary" size="sm">Featured</x-admin.badge>
                            @endif
                        </div>
                    </x-admin.table-cell>

                    <x-admin.table-cell>
                        @if ($post->published_at)
                            <div class="text-sm">
                                <div class="text-gray-900 dark:text-white">{{ $post->published_at->format('M j, Y') }}
                                </div>
                                <div class="text-gray-500 dark:text-gray-400">
                                    {{ $post->published_at->format('g:i A') }}</div>
                            </div>
                        @else
                            <span class="text-sm text-gray-400">Not published</span>
                        @endif
                    </x-admin.table-cell>

                    <x-admin.table-cell>
                        <div class="flex items-center space-x-2">
                            <!-- View Button -->
                            <div class="relative group">
                                <a href="{{ route('admin.posts.show', $post) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors dark:text-neutral-400 dark:hover:text-blue-400 dark:hover:bg-blue-900/30">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <!-- Tooltip -->
                                <div
                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 whitespace-nowrap z-50 dark:bg-gray-700">
                                    View Post
                                    <div
                                        class="absolute top-full left-1/2 transform -translate-x-1/2 w-2 h-2 bg-gray-900 rotate-45 dark:bg-gray-700">
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Button -->
                            <div class="relative group">
                                <a href="{{ route('admin.posts.edit', $post) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-md transition-colors dark:text-neutral-400 dark:hover:text-green-400 dark:hover:bg-green-900/30">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <!-- Tooltip -->
                                <div
                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 whitespace-nowrap z-50 dark:bg-gray-700">
                                    Edit Post
                                    <div
                                        class="absolute top-full left-1/2 transform -translate-x-1/2 w-2 h-2 bg-gray-900 rotate-45 dark:bg-gray-700">
                                    </div>
                                </div>
                            </div>

                            <!-- Publish Button (only show if not published) -->
                            @if ($post->status !== 'published')
                                <div class="relative group">
                                    <form action="{{ route('admin.posts.change-status', $post) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="published">
                                        <button type="submit"
                                            class="inline-flex items-center justify-center w-8 h-8 text-gray-600 hover:text-emerald-600 hover:bg-emerald-50 rounded-md transition-colors dark:text-neutral-400 dark:hover:text-emerald-400 dark:hover:bg-emerald-900/30">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </button>
                                    </form>
                                    <!-- Tooltip -->
                                    <div
                                        class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 whitespace-nowrap z-50 dark:bg-gray-700">
                                        Publish Post
                                        <div
                                            class="absolute top-full left-1/2 transform -translate-x-1/2 w-2 h-2 bg-gray-900 rotate-45 dark:bg-gray-700">
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Feature/Unfeature Button -->
                            <div class="relative group">
                                <form action="{{ route('admin.posts.toggle-featured', $post) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center justify-center w-8 h-8 text-gray-600 hover:text-amber-600 hover:bg-amber-50 rounded-md transition-colors dark:text-neutral-400 dark:hover:text-amber-400 dark:hover:bg-amber-900/30">
                                        <svg class="w-4 h-4" fill="{{ $post->featured ? 'currentColor' : 'none' }}"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                    </button>
                                </form>
                                <!-- Tooltip -->
                                <div
                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 whitespace-nowrap z-50 dark:bg-gray-700">
                                    {{ $post->featured ? 'Unfeature Post' : 'Feature Post' }}
                                    <div
                                        class="absolute top-full left-1/2 transform -translate-x-1/2 w-2 h-2 bg-gray-900 rotate-45 dark:bg-gray-700">
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Button -->
                            <div class="relative group">
                                <form action="{{ route('admin.posts.destroy', $post) }}" method="POST"
                                    class="inline"
                                    onsubmit="return confirm('Are you sure you want to delete this post? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center justify-center w-8 h-8 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-md transition-colors dark:text-neutral-400 dark:hover:text-red-400 dark:hover:bg-red-900/30">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                                <!-- Tooltip -->
                                <div
                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 whitespace-nowrap z-50 dark:bg-gray-700">
                                    Delete Post
                                    <div
                                        class="absolute top-full left-1/2 transform -translate-x-1/2 w-2 h-2 bg-gray-900 rotate-45 dark:bg-gray-700">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-admin.table-cell>
                </x-admin.table-row>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12">
                        <x-admin.empty-state title="No posts found"
                            description="You haven't created any posts yet or no posts match your search criteria."
                            :actionText="request()->hasAny(['search', 'category', 'status'])
                                ? null
                                : 'Create Your First Post'" :actionUrl="request()->hasAny(['search', 'category', 'status'])
                                ? null
                                : route('admin.posts.create')"
                            icon='<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>' />
                    </td>
                </tr>
            @endforelse
        </x-admin.data-table>

        @if ($posts->hasPages())
            <x-slot name="footer">
                <x-admin.pagination :paginator="$posts" :appends="request()->query()" />
            </x-slot>
        @endif
    </x-admin.card>

    @push('scripts')
        <script>
            // Enhanced toggle function with positioning for Solution 3
            function toggleDropdown(dropdownId) {
                const dropdown = document.getElementById(dropdownId);
                const button = dropdown.previousElementSibling.querySelector('button');

                if (dropdown.classList.contains('hidden')) {
                    // Position the dropdown
                    const rect = button.getBoundingClientRect();
                    dropdown.style.top = (rect.bottom + window.scrollY + 5) + 'px';
                    dropdown.style.left = (rect.right + window.scrollX - 224) + 'px'; // 224px = w-56

                    // Show dropdown
                    dropdown.classList.remove('hidden');
                } else {
                    dropdown.classList.add('hidden');
                }

                // Close other dropdowns
                document.querySelectorAll('[id^="dropdown-category-"]').forEach(otherDropdown => {
                    if (otherDropdown.id !== dropdownId) {
                        otherDropdown.classList.add('hidden');
                    }
                });
            }

            // Close dropdowns when clicking outside
            document.addEventListener('click', function(event) {
                if (!event.target.closest('[id^="dropdown-category-"]') && !event.target.closest(
                        'button[onclick*="toggleDropdown"]')) {
                    document.querySelectorAll('[id^="dropdown-category-"]').forEach(dropdown => {
                        dropdown.classList.add('hidden');
                    });
                }
            });
        </script>
    @endpush
</x-layouts.admin>
