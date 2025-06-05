<x-layouts.admin title="Posts Management">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Posts' => '']" />

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Posts Management</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Create and manage your blog posts</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <!-- Export Button -->
            <button type="button" 
                    onclick="exportPosts()"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export
            </button>

            <!-- Statistics Button -->
            <button type="button" 
                    onclick="showStatistics()"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Statistics
            </button>

            <!-- Create Button -->
            <a href="{{ route('admin.posts.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create New Post
            </a>
        </div>
    </div>

    <!-- Filters -->
    <x-admin.card class="mb-6">
        <form method="GET" action="{{ route('admin.posts.index') }}"  class="space-y-4 sm:space-y-0 sm:flex sm:items-end sm:gap-4">
            <!-- Search -->
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                    Search
                </label>
                <input type="text" 
                       name="search" 
                       id="search"
                       value="{{ request('search') }}"
                       placeholder="Search by title, excerpt, or content..."
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
            </div>

            <!-- Status Filter -->
            <div class="w-full sm:w-48">
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                    Status
                </label>
                <select name="status" 
                        id="status"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>

            <!-- Category Filter -->
            <div class="w-full sm:w-48">
                <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                    Category
                </label>
                <select name="category" 
                        id="category"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }} ({{ $cat->posts_count }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Actions -->
            <div class="flex gap-2">
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Filter
                </button>

                @if(request()->hasAny(['search', 'status', 'category']))
                    <a href="{{ route('admin.posts.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </x-admin.card>

    <!-- Bulk Actions -->
    <form id="bulk-form" method="POST" action="{{ route('admin.posts.bulk-action') }}" class="hidden">
        @csrf
        <input type="hidden" name="action" id="bulk-action">
        <input type="hidden" name="posts" id="bulk-posts">
    </form>

    <!-- Posts Table -->
    <x-admin.card>
        @if($posts->count() > 0)
            <!-- Bulk Actions Bar -->
            <div id="bulk-actions" class="hidden mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <div class="flex items-center justify-between">
                    <span id="selected-count" class="text-sm font-medium text-blue-900 dark:text-blue-100">
                        0 posts selected
                    </span>
                    <div class="flex gap-2">
                        <button type="button" onclick="bulkAction('publish')" class="px-3 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-md hover:bg-green-200 dark:bg-green-800 dark:text-green-200">
                            Publish
                        </button>
                        <button type="button" onclick="bulkAction('unpublish')" class="px-3 py-1 text-xs font-medium text-yellow-700 bg-yellow-100 rounded-md hover:bg-yellow-200 dark:bg-yellow-800 dark:text-yellow-200">
                            Unpublish
                        </button>
                        <button type="button" onclick="bulkAction('feature')" class="px-3 py-1 text-xs font-medium text-purple-700 bg-purple-100 rounded-md hover:bg-purple-200 dark:bg-purple-800 dark:text-purple-200">
                            Feature
                        </button>
                        <button type="button" onclick="bulkAction('unfeature')" class="px-3 py-1 text-xs font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200">
                            Unfeature
                        </button>
                        <button type="button" onclick="bulkAction('delete')" class="px-3 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-md hover:bg-red-200 dark:bg-red-800 dark:text-red-200">
                            Delete
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th scope="col" class="w-8 px-6 py-3">
                                <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Post
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Author
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Categories
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Published
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($posts as $post)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-6 py-4">
                                    <input type="checkbox" name="post_ids[]" value="{{ $post->id }}" class="post-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-start space-x-3">
                                        @if($post->featured_image_url)
                                            <img src="{{ $post->thumbnail_url ?: $post->featured_image_url }}" 
                                                 alt="{{ $post->title }}" 
                                                 class="w-12 h-12 object-cover rounded-lg flex-shrink-0">
                                        @else
                                            <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('admin.posts.edit', $post) }}" 
                                                   class="text-sm font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 truncate">
                                                    {{ $post->title }}
                                                </a>
                                                @if($post->featured)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                        </svg>
                                                        Featured
                                                    </span>
                                                @endif
                                            </div>
                                            @if($post->excerpt)
                                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">
                                                    {{ Str::limit($post->excerpt, 100) }}
                                                </p>
                                            @endif
                                            <div class="flex items-center gap-4 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                <span>{{ $post->reading_time }} min read</span>
                                                <span>{{ $post->created_at->format('M d, Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $post->author->name }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($post->categories as $category)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                                {{ $category->name }}
                                            </span>
                                        @empty
                                            <span class="text-sm text-gray-500 dark:text-gray-400">No categories</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                                        @if($post->status === 'published') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                        @elseif($post->status === 'draft') bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100
                                        @else bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100 @endif">
                                        {{ ucfirst($post->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    @if($post->published_at)
                                        {{ $post->published_at->format('M d, Y') }}
                                    @else
                                        Not published
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Quick Actions -->
                                        @if($post->status !== 'published')
                                            <form method="POST" action="{{ route('admin.posts.change-status', $post) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="published">
                                                <button type="submit" 
                                                        class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                                        title="Publish">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif

                                        <form method="POST" action="{{ route('admin.posts.toggle-featured', $post) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
                                                    title="{{ $post->featured ? 'Remove from featured' : 'Make featured' }}">
                                                <svg class="w-4 h-4" fill="{{ $post->featured ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                                </svg>
                                            </button>
                                        </form>

                                        <!-- Dropdown Menu -->
                                        <div class="relative inline-block text-left" x-data="{ open: false }">
                                            <button @click="open = !open" 
                                                    class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                                </svg>
                                            </button>

                                            <div x-show="open" @click.away="open = false" 
                                                 class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
                                                <div class="py-1">
                                                    <a href="{{ route('admin.posts.edit', $post) }}" 
                                                       class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                        Edit
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.posts.duplicate', $post) }}" class="inline w-full">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-left">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                            </svg>
                                                            Duplicate
                                                        </button>
                                                    </form>
                                                    <div class="border-t border-gray-100 dark:border-gray-700"></div>
                                                    <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" 
                                                          onsubmit="return confirm('Are you sure you want to delete this post?')" class="inline w-full">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 text-left">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($posts->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $posts->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No posts found</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    @if(request()->hasAny(['search', 'status', 'category']))
                        Try adjusting your search criteria or filters.
                    @else
                        Get started by creating your first blog post.
                    @endif
                </p>
                <div class="mt-6">
                    @if(request()->hasAny(['search', 'status', 'category']))
                        <a href="{{ route('admin.posts.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                            Clear filters
                        </a>
                    @else
                        <a href="{{ route('admin.posts.create') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Create your first post
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </x-admin.card>

    <!-- Statistics Modal -->
    <div id="statistics-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Posts Statistics</h3>
                <button onclick="closeStatistics()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="statistics-content">
                <!-- Statistics content will be loaded here -->
                <div class="flex items-center justify-center py-8">
                    <svg class="animate-spin h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Bulk selection functionality
            const selectAllCheckbox = document.getElementById('select-all');
            const postCheckboxes = document.querySelectorAll('.post-checkbox');
            const bulkActions = document.getElementById('bulk-actions');
            const selectedCount = document.getElementById('selected-count');
            
            // Select all functionality
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    const isChecked = this.checked;
                    postCheckboxes.forEach(checkbox => {
                        checkbox.checked = isChecked;
                    });
                    updateBulkActions();
                });
            }
            
            // Individual checkbox change
            postCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const checkedBoxes = document.querySelectorAll('.post-checkbox:checked');
                    if (selectAllCheckbox) {
                        selectAllCheckbox.checked = checkedBoxes.length === postCheckboxes.length;
                        selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < postCheckboxes.length;
                    }
                    updateBulkActions();
                });
            });
            
            function updateBulkActions() {
                const checkedBoxes = document.querySelectorAll('.post-checkbox:checked');
                const count = checkedBoxes.length;
                
                if (count > 0) {
                    bulkActions.classList.remove('hidden');
                    selectedCount.textContent = `${count} post${count === 1 ? '' : 's'} selected`;
                } else {
                    bulkActions.classList.add('hidden');
                }
            }
        });
        
        // Bulk actions
        function bulkAction(action) {
            const checkedBoxes = document.querySelectorAll('.post-checkbox:checked');
            if (checkedBoxes.length === 0) {
                alert('Please select at least one post.');
                return;
            }
            
            const postIds = Array.from(checkedBoxes).map(cb => cb.value);
            
            let confirmMessage = '';
            switch(action) {
                case 'delete':
                    confirmMessage = `Are you sure you want to delete ${postIds.length} post(s)?`;
                    break;
                case 'publish':
                    confirmMessage = `Are you sure you want to publish ${postIds.length} post(s)?`;
                    break;
                case 'unpublish':
                    confirmMessage = `Are you sure you want to unpublish ${postIds.length} post(s)?`;
                    break;
                default:
                    confirmMessage = `Are you sure you want to ${action} ${postIds.length} post(s)?`;
            }
            
            if (confirm(confirmMessage)) {
                document.getElementById('bulk-action').value = action;
                document.getElementById('bulk-posts').value = JSON.stringify(postIds);
                document.getElementById('bulk-form').submit();
            }
        }
        
        // Export functionality
        function exportPosts() {
            const params = new URLSearchParams(window.location.search);
            const exportUrl = '{{ route("admin.posts.export") }}?' + params.toString();
            window.open(exportUrl, '_blank');
        }
        
        // Statistics functionality
        function showStatistics() {
            document.getElementById('statistics-modal').classList.remove('hidden');
            loadStatistics();
        }
        
        function closeStatistics() {
            document.getElementById('statistics-modal').classList.add('hidden');
        }
        
        function loadStatistics() {
            fetch('{{ route("admin.posts.statistics") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderStatistics(data.data);
                    } else {
                        document.getElementById('statistics-content').innerHTML = 
                            '<div class="text-center py-8 text-red-600">Failed to load statistics</div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading statistics:', error);
                    document.getElementById('statistics-content').innerHTML = 
                        '<div class="text-center py-8 text-red-600">Failed to load statistics</div>';
                });
        }
        
        function renderStatistics(stats) {
            const content = `
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">${stats.total_posts}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Total Posts</div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">${stats.published_posts}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Published</div>
                    </div>
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">${stats.draft_posts}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Drafts</div>
                    </div>
                    <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">${stats.featured_posts}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Featured</div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Recent Posts</h4>
                        <div class="space-y-2">
                            ${stats.recent_posts.map(post => `
                                <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                    <div>
                                        <div class="font-medium text-sm">${post.title}</div>
                                        <div class="text-xs text-gray-500">${post.author} • ${post.created_at}</div>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded ${post.status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">${post.status}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Popular Categories</h4>
                        <div class="space-y-2">
                            ${stats.popular_categories.map(category => `
                                <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                    <div class="font-medium text-sm">${category.name}</div>
                                    <span class="text-xs text-gray-500">${category.posts_count} posts</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded">
                        <span class="font-medium">This Month:</span> ${stats.posts_this_month} posts
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded">
                        <span class="font-medium">Last Month:</span> ${stats.posts_last_month} posts
                    </div>
                </div>
            `;
            
            document.getElementById('statistics-content').innerHTML = content;
        }
        
        // Close modal when clicking outside
        document.getElementById('statistics-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeStatistics();
            }
        });
    </script>
    @endpush
</x-layouts.admin>