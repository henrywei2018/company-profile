<x-layouts.public 
    :title="$seoData['title']"
    :description="$seoData['description']"
    :keywords="$seoData['keywords']"
    :breadcrumbs="$seoData['breadcrumbs']"
>

    <!-- Hero Section -->
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <div class="max-w-2xl mx-auto text-center mb-10 lg:mb-14">
            <h1 class="text-3xl font-bold md:text-4xl md:leading-tight dark:text-white">Our Blog</h1>
            <p class="mt-3 text-gray-800 dark:text-gray-200">
                Insights, updates, and expertise from CV Usaha Prima Lestari
            </p>
        </div>

        <div class="grid lg:grid-cols-4 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-3">
                <!-- Filters & Search -->
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-orange-200 dark:border-slate-700 p-6 mb-8">
                    <form method="GET" action="{{ route('blog.index') }}" class="grid md:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div>
                            <input 
                                type="text" 
                                name="search" 
                                placeholder="Search articles..."
                                value="{{ request('search') }}"
                                class="w-full px-4 py-2 border border-orange-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-slate-700 dark:text-white"
                            >
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <select name="category" class="w-full px-4 py-2 border border-orange-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-slate-700 dark:text-white">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                        {{ $category->name }} ({{ $category->published_posts_count }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sort -->
                        <div>
                            <select name="sort" class="w-full px-4 py-2 border border-orange-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-slate-700 dark:text-white">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Popular</option>
                            </select>
                        </div>

                        <!-- Submit & Featured Filter -->
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors">
                                Filter
                            </button>
                            <label class="flex items-center">
                                <input type="checkbox" name="featured" value="1" {{ request('featured') == '1' ? 'checked' : '' }} class="mr-2">
                                <span class="text-sm dark:text-gray-300">Featured</span>
                            </label>
                        </div>
                    </form>

                    <!-- Active Filters -->
                    @if(request()->hasAny(['search', 'category', 'featured', 'sort']))
                        <div class="mt-4 pt-4 border-t border-orange-200 dark:border-slate-600">
                            <div class="flex flex-wrap gap-2 items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Active filters:</span>
                                
                                @if(request('search'))
                                    <span class="inline-flex items-center px-3 py-1 bg-blue-100 dark:bg-blue-900 text-orange-800 dark:text-orange-200 text-sm rounded-full">
                                        Search: "{{ request('search') }}"
                                        <a href="{{ request()->fullUrlWithoutQuery('search') }}" class="ml-2 hover:text-orange-600">×</a>
                                    </span>
                                @endif

                                @if(request('category'))
                                    <span class="inline-flex items-center px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-sm rounded-full">
                                        Category: {{ $categories->where('slug', request('category'))->first()?->name }}
                                        <a href="{{ request()->fullUrlWithoutQuery('category') }}" class="ml-2 hover:text-green-600">×</a>
                                    </span>
                                @endif

                                @if(request('featured'))
                                    <span class="inline-flex items-center px-3 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 text-sm rounded-full">
                                        Featured Only
                                        <a href="{{ request()->fullUrlWithoutQuery('featured') }}" class="ml-2 hover:text-yellow-600">×</a>
                                    </span>
                                @endif

                                <a href="{{ route('blog.index') }}" class="text-sm text-red-600 dark:text-red-400 hover:underline">Clear all</a>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Posts Results -->
                @if($posts->isNotEmpty())
                    <!-- Results Info -->
                    <div class="flex justify-between items-center mb-6">
                        <p class="text-gray-600 dark:text-gray-400">
                            Showing {{ $posts->firstItem() }}-{{ $posts->lastItem() }} of {{ $posts->total() }} articles
                        </p>
                    </div>

                    <!-- Posts Grid -->
                    <div class="grid md:grid-cols-2 gap-6 mb-8">
                        @foreach($posts as $post)
                            <article class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-orange-200 dark:border-slate-700 overflow-hidden hover:shadow-md transition-all duration-300 group">
                                @if($post->featured_image_url)
                                    <div class="aspect-video overflow-hidden">
                                        <img 
                                            src="{{ $post->featured_image_url }}" 
                                            alt="{{ $post->title }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                        >
                                    </div>
                                @endif
                                
                                <div class="p-6">
                                    <!-- Meta Info -->
                                    <div class="flex items-center gap-4 mb-3 text-sm text-gray-500 dark:text-gray-400">
                                        <time datetime="{{ $post->published_at->toISOString() }}">
                                            {{ $post->published_at->format('M d, Y') }}
                                        </time>
                                        <span>{{ $post->reading_time }} min read</span>
                                        @if($post->featured)
                                            <span class="inline-flex items-center px-2 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 text-xs rounded-full">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                                Featured
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Categories -->
                                    @if($post->categories->isNotEmpty())
                                        <div class="mb-3">
                                            @foreach($post->categories->take(2) as $category)
                                                <span class="inline-block bg-blue-100 dark:bg-blue-900 text-orange-800 dark:text-orange-200 text-xs px-2 py-1 rounded-full mr-2">
                                                    {{ $category->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Title -->
                                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-3 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">
                                        <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                                    </h2>

                                    <!-- Excerpt -->
                                    @if($post->excerpt)
                                        <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 line-clamp-3">{{ $post->excerpt }}</p>
                                    @endif

                                    <!-- Author & Read More -->
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                                {{ substr($post->author->name, 0, 1) }}
                                            </div>
                                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">{{ $post->author->name }}</span>
                                        </div>
                                        <a href="{{ route('blog.show', $post->slug) }}" class="inline-flex items-center gap-x-1 text-orange-600 dark:text-orange-400 hover:text-orange-800 dark:hover:text-orange-300 text-sm font-medium">
                                            Read more
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($posts->hasPages())
                        <div class="flex justify-center">
                            {{ $posts->links() }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-16">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No articles found</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-6">
                            @if(request()->hasAny(['search', 'category', 'featured']))
                                Try adjusting your filters or search terms.
                            @else
                                Check back soon for our latest insights and updates.
                            @endif
                        </p>
                        @if(request()->hasAny(['search', 'category', 'featured']))
                            <a href="{{ route('blog.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Clear Filters
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <aside class="lg:col-span-1 space-y-6">
                <!-- Featured Posts -->
                @if($featuredPosts->isNotEmpty())
                    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-orange-200 dark:border-slate-700 p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Featured Posts</h3>
                        <div class="space-y-4">
                            @foreach($featuredPosts as $featured)
                                <article class="group">
                                    @if($featured->featured_image_url)
                                        <div class="aspect-video overflow-hidden rounded-lg mb-3">
                                            <img src="{{ $featured->featured_image_url }}" alt="{{ $featured->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        </div>
                                    @endif
                                    <h4 class="font-medium text-gray-900 dark:text-gray-100 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors mb-1">
                                        <a href="{{ route('blog.show', $featured->slug) }}">{{ Str::limit($featured->title, 60) }}</a>
                                    </h4>
                                    <time class="text-xs text-gray-500 dark:text-gray-400">{{ $featured->published_at->format('M d, Y') }}</time>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Categories -->
                @if($categories->isNotEmpty())
                    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-orange-200 dark:border-slate-700 p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Categories</h3>
                        <div class="space-y-2">
                            @foreach($categories as $category)
                                <a href="{{ route('blog.index', ['category' => $category->slug]) }}" 
                                   class="flex items-center justify-between py-2 px-3 rounded-lg text-gray-600 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors {{ request('category') == $category->slug ? 'bg-blue-50 dark:bg-blue-900/20 text-orange-600 dark:text-orange-400' : '' }}">
                                    <span>{{ $category->name }}</span>
                                    <span class="bg-gray-200 dark:bg-slate-600 text-gray-600 dark:text-gray-300 text-xs px-2 py-1 rounded-full">
                                        {{ $category->published_posts_count }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Recent Posts -->
                @if($recentPosts->isNotEmpty())
                    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-orange-200 dark:border-slate-700 p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Recent Posts</h3>
                        <div class="space-y-4">
                            @foreach($recentPosts as $recent)
                                <article class="group">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors mb-1">
                                        <a href="{{ route('blog.show', $recent->slug) }}">{{ Str::limit($recent->title, 60) }}</a>
                                    </h4>
                                    <time class="text-xs text-gray-500 dark:text-gray-400">{{ $recent->published_at->format('M d, Y') }}</time>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Archive -->
                @if($archiveData->isNotEmpty())
                    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-orange-200 dark:border-slate-700 p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Archive</h3>
                        <div class="space-y-2">
                            @foreach($archiveData->take(6) as $archive)
                                <div class="flex items-center justify-between py-1 text-sm">
                                    <span class="text-gray-600 dark:text-gray-300">{{ date('F Y', mktime(0, 0, 0, $archive->month, 1, $archive->year)) }}</span>
                                    <span class="text-gray-500 dark:text-gray-400">({{ $archive->count }})</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </aside>
        </div>
    </div>
</x-layouts.public>