<x-layouts.public 
    :title="$seoData['title']"
    :description="$seoData['description']"
    :keywords="$seoData['keywords']"
    :breadcrumbs="$seoData['breadcrumbs']"
>

    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-x-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Blog
            </a>
        </div>

        <div class="grid lg:grid-cols-4 gap-8">
            <!-- Main Content -->
            <article class="lg:col-span-3">
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <!-- Featured Image -->
                    @if($post->featured_image_url)
                        <div class="aspect-video overflow-hidden">
                            <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                        </div>
                    @endif

                    <div class="p-8">
                        <!-- Header -->
                        <header class="mb-8">
                            <!-- Categories -->
                            @if($post->categories->isNotEmpty())
                                <div class="mb-4">
                                    @foreach($post->categories as $category)
                                        <a href="{{ route('blog.index', ['category' => $category->slug]) }}" 
                                           class="inline-block bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-sm px-3 py-1 rounded-full mr-2 hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors">
                                            {{ $category->name }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Title -->
                            <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-gray-100 mb-6">{{ $post->title }}</h1>

                            <!-- Meta -->
                            <div class="flex flex-wrap items-center gap-4 text-gray-600 dark:text-gray-300 text-sm mb-6">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-medium mr-3">
                                        {{ substr($post->author->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $post->author->name }}</div>
                                        <div class="text-sm">Author</div>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <time datetime="{{ $post->published_at->toISOString() }}">{{ $post->published_at->format('F d, Y') }}</time>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>{{ $post->reading_time }} minute read</span>
                                </div>
                                @if($post->featured)
                                    <span class="inline-flex items-center px-2 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 text-xs rounded-full">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        Featured Article
                                    </span>
                                @endif
                            </div>

                            <!-- Excerpt -->
                            @if($post->excerpt)
                                <div class="text-xl text-gray-600 dark:text-gray-300 leading-relaxed border-l-4 border-blue-500 pl-6 italic">
                                    {{ $post->excerpt }}
                                </div>
                            @endif
                        </header>

                        <!-- Content -->
                        <div class="prose prose-lg dark:prose-invert max-w-none">
                            {!! $post->content !!}
                        </div>

                        <!-- Footer -->
                        <footer class="mt-12 pt-8 border-t border-gray-200 dark:border-slate-600">
                            <!-- Share -->
                            <div class="flex items-center justify-between mb-8">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Share this article</h3>
                                <div class="flex gap-3">
                                    <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(route('blog.show', $post->slug)) }}" 
                                       target="_blank" rel="noopener"
                                       class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-sm">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                        </svg>
                                        Twitter
                                    </a>
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blog.show', $post->slug)) }}" 
                                       target="_blank" rel="noopener"
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                        </svg>
                                        Facebook
                                    </a>
                                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('blog.show', $post->slug)) }}" 
                                       target="_blank" rel="noopener"
                                       class="inline-flex items-center px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 transition-colors text-sm">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                        </svg>
                                        LinkedIn
                                    </a>
                                </div>
                            </div>

                            <!-- Tags -->
                            @if($post->categories->isNotEmpty())
                                <div class="mb-8">
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Tagged in:</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($post->categories as $category)
                                            <a href="{{ route('blog.index', ['category' => $category->slug]) }}" 
                                               class="inline-block bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300 text-sm px-3 py-1 rounded-full hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors">
                                                #{{ $category->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </footer>
                    </div>
                </div>

                <!-- Related Posts -->
                @if($relatedPosts->isNotEmpty())
                    <section class="mt-12">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-8">Related Articles</h2>
                        <div class="grid md:grid-cols-2 gap-6">
                            @foreach($relatedPosts as $related)
                                <article class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden hover:shadow-md transition-all duration-300 group">
                                    @if($related->featured_image_url)
                                        <div class="aspect-video overflow-hidden">
                                            <img src="{{ $related->featured_image_url }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        </div>
                                    @endif
                                    <div class="p-6">
                                        <div class="flex items-center mb-3 text-sm text-gray-500 dark:text-gray-400">
                                            <time datetime="{{ $related->published_at->toISOString() }}">{{ $related->published_at->format('M d, Y') }}</time>
                                            <span class="mx-2">•</span>
                                            <span>{{ $related->reading_time }} min read</span>
                                        </div>
                                        
                                        @if($related->categories->isNotEmpty())
                                            <div class="mb-3">
                                                @foreach($related->categories->take(2) as $category)
                                                    <span class="inline-block bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 text-xs px-2 py-1 rounded-full mr-2">
                                                        {{ $category->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                        
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                            <a href="{{ route('blog.show', $related->slug) }}">{{ $related->title }}</a>
                                        </h3>
                                        
                                        @if($related->excerpt)
                                            <p class="text-gray-600 dark:text-gray-300 text-sm">{{ Str::limit($related->excerpt, 100) }}</p>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif
            </article>

            <!-- Sidebar -->
            <aside class="lg:col-span-1 space-y-6">
                <!-- Categories -->
                @if($categories->isNotEmpty())
                    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Categories</h3>
                        <div class="space-y-2">
                            @foreach($categories as $category)
                                <a href="{{ route('blog.index', ['category' => $category->slug]) }}" 
                                   class="flex items-center justify-between py-2 px-3 rounded-lg text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors {{ $post->categories->contains($category) ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
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
                    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Recent Posts</h3>
                        <div class="space-y-4">
                            @foreach($recentPosts as $recent)
                                <article class="group">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors mb-1">
                                        <a href="{{ route('blog.show', $recent->slug) }}">{{ Str::limit($recent->title, 60) }}</a>
                                    </h4>
                                    <time class="text-xs text-gray-500 dark:text-gray-400">{{ $recent->published_at->format('M d, Y') }}</time>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif
            </aside>
        </div>
    </div>
</x-layouts.public>