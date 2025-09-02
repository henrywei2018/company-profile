{{-- resources/views/pages/blog/show.blade.php --}}
<x-layouts.public
    :title="$post->title . ' - ' . $siteConfig['site_title']"
    :description="$post->excerpt ?: strip_tags(substr($post->content, 0, 160))"
    :keywords="'blog, article, ' . $post->categories->pluck('name')->implode(', ')"
    type="article"
>

{{-- Hero Section --}}
<section class="relative pt-32 pb-16 bg-gradient-to-br from-orange-50 via-white to-amber-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumbs --}}
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-orange-600 inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Beranda
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('blog.index') }}" class="ml-1 text-gray-700 hover:text-orange-600 md:ml-2">Blog</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-orange-600 md:ml-2 font-medium line-clamp-1">{{ $post->title }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="max-w-7xl mx-auto">
            {{-- Post Kategori --}}
            @if($post->categories->count() > 0)
            <div class="flex flex-wrap gap-2 mb-6">
                @foreach($post->categories as $category)
                <a href="{{ route('blog.index', ['category' => $category->slug]) }}" 
                   class="bg-orange-100 text-orange-600 px-3 py-1 rounded-full text-sm font-medium hover:bg-orange-200 transition-colors">
                    {{ $category->name }}
                </a>
                @endforeach
                @if($post->featured)
                <span class="bg-amber-100 text-amber-600 px-3 py-1 rounded-full text-sm font-medium">
                    Unggulan Article
                </span>
                @endif
            </div>
            @endif
            
            {{-- Post Title --}}
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6 leading-tight">
                {{ $post->title }}
            </h1>
            
            {{-- Post Excerpt --}}
            @if($post->excerpt)
            <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                {{ $post->excerpt }}
            </p>
            @endif
            
            {{-- Post Meta --}}
            <div class="flex flex-wrap items-center gap-6 text-gray-600 mb-8">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="font-medium">{{ $post->author->name }}</span>
                </div>
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>{{ $post->published_at->format('F j, Y') }}</span>
                </div>
                @if($post->reading_time)
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $post->reading_time }} min read</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- Main Content --}}
<section class="py-8 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
            {{-- Article Content --}}
            <div class="lg:col-span-3">
                <article class="prose prose-lg max-w-none">
                    {{-- Unggulan Image --}}
                    @if($post->featured_image)
                    <div class="mb-8 rounded-2xl overflow-hidden shadow-xl">
                        <img src="{{ asset('storage/' . $post->featured_image) }}" 
                             alt="{{ $post->title }}" 
                             class="w-full h-96 object-cover" style="margin-bottom: 0px; margin-top: 0px; border-radius: 1rem;">
                    </div>
                    @endif
                    
                    {{-- Post Content --}}
                    <div class="prose-content text-gray-700 leading-relaxed">
                        {!! $post->content !!}
                    </div>
                </article>
                
                {{-- Article Footer --}}
                <div class="mt-12 pt-8 border-t border-gray-200">
                    {{-- Share Buttons --}}
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Bagikan artikel ini</h3>
                            <div class="flex gap-4">
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($post->title) }}" 
                                   target="_blank"
                                   class="flex items-center justify-center w-10 h-10 bg-blue-500 text-white rounded-full hover:bg-blue-600 transition-colors">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0020 3.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.073 4.073 0 01.8 7.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 16.407a11.616 11.616 0 006.29 1.84"/>
                                    </svg>
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" 
                                   target="_blank"
                                   class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition-colors">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M20 10C20 4.477 15.523 0 10 0S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10z" clip-rule="evenodd"/>
                                    </svg>
                                </a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}" 
                                   target="_blank"
                                   class="flex items-center justify-center w-10 h-10 bg-blue-700 text-white rounded-full hover:bg-blue-800 transition-colors">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.338 16.338H13.67V12.16c0-.995-.017-2.277-1.387-2.277-1.39 0-1.601 1.086-1.601 2.207v4.248H8.014v-8.59h2.559v1.174h.037c.356-.675 1.227-1.387 2.526-1.387 2.703 0 3.203 1.778 3.203 4.092v4.711zM5.005 6.575a1.548 1.548 0 11-.003-3.096 1.548 1.548 0 01.003 3.096zm-1.337 9.763H6.34v-8.59H3.667v8.59zM17.668 1H2.328C1.595 1 1 1.581 1 2.298v15.403C1 18.418 1.595 19 2.328 19h15.34c.734 0 1.332-.582 1.332-1.299V2.298C19 1.581 18.402 1 17.668 1z" clip-rule="evenodd"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        
                        {{-- Kembali ke Blog --}}
                        <a href="{{ route('blog.index') }}" 
                           class="inline-flex items-center px-6 py-3 border border-orange-600 text-orange-600 font-semibold rounded-xl hover:bg-orange-600 hover:text-white transition-all duration-300">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
                            </svg>
                            Kembali ke Blog
                        </a>
                    </div>
                </div>
            </div>
            
            {{-- Sidebar --}}
            <div class="lg:col-span-1">
                {{-- Recent Posts --}}
                @if($recentPosts && $recentPosts->count() > 0)
                <div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-2xl p-6 mb-8 border border-orange-100">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Recent Articles</h3>
                    <div class="space-y-4">
                        @foreach($recentPosts as $recent)
                        <article class="group">
                            <h4 class="font-semibold text-gray-900 mb-2 line-clamp-2 group-hover:text-orange-600 transition-colors">
                                <a href="{{ route('blog.show', $recent->slug) }}">
                                    {{ $recent->title }}
                                </a>
                            </h4>
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $recent->published_at->format('M j, Y') }}
                            </div>
                        </article>
                        @endforeach
                    </div>
                </div>
                @endif
                
                {{-- Kategori --}}
                @if($categories && $categories->count() > 0)
                <div class="bg-white border border-gray-200 rounded-2xl p-6 mb-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Kategori</h3>
                    <div class="space-y-2">
                        @foreach($categories as $category)
                        <a href="{{ route('blog.index', ['category' => $category->slug]) }}" 
                           class="flex items-center justify-between p-3 rounded-xl hover:bg-orange-50 transition-colors group">
                            <span class="font-medium text-gray-700 group-hover:text-orange-600">
                                {{ $category->name }}
                            </span>
                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-lg text-sm group-hover:bg-orange-100 group-hover:text-orange-600">
                                {{ $category->published_posts_count }}
                            </span>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
                
                
        </div>
    </div>
</section>

{{-- Related Posts --}}
@if($relatedPosts && $relatedPosts->count() > 0)
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Related Articles</h2>
            <p class="text-gray-600">Lanjutkan membaca with these related articles.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($relatedPosts as $related)
            <article class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                {{-- Post Image --}}
                <div class="relative h-48 overflow-hidden">
                    @if($related->featured_image)
                        <img src="{{ asset('storage/' . $related->featured_image) }}" 
                             alt="{{ $related->title }}" 
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-orange-400 to-amber-500 flex items-center justify-center">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                            </svg>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                    
                    @if($related->categories->count() > 0)
                    <div class="absolute top-3 right-3">
                        <span class="bg-white/90 text-gray-800 px-2 py-1 rounded-lg text-xs font-medium">
                            {{ $related->categories->first()->name }}
                        </span>
                    </div>
                    @endif
                </div>
                
                {{-- Post Content --}}
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-3 line-clamp-2 hover:text-orange-600 transition-colors">
                        <a href="{{ route('blog.show', $related->slug) }}">
                            {{ $related->title }}
                        </a>
                    </h3>
                    
                    @if($related->excerpt)
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                        {{ $related->excerpt }}
                    </p>
                    @endif
                    
                    <div class="flex items-center justify-between text-sm text-gray-500">
                        <span>{{ $related->author->name }}</span>
                        <span>{{ $related->published_at->format('M j, Y') }}</span>
                    </div>
                </div>
            </article>
            @endforeach
        </div>
        
        <div class="text-center mt-8">
            <a href="{{ route('blog.index') }}" 
               class="inline-flex items-center px-6 py-3 bg-orange-600 text-white font-semibold rounded-xl hover:bg-orange-700 transition-colors">
                View All Articles
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>
    </div>
</section>
@endif

@push('styles')
<style>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Custom prose styling to remove excessive spacing */
.prose-content > *:first-child {
    margin-top: 0 !important;
}

.prose-content p:first-child {
    margin-top: 0 !important;
}

.prose-content h1:first-child,
.prose-content h2:first-child,
.prose-content h3:first-child {
    margin-top: 0 !important;
}

.prose img {
    border-radius: 1rem;
}

.prose h2 {
    color: #f97316;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.prose h3 {
    color: #ea580c;
}

.prose blockquote {
    border-left: 4px solid #fed7aa;
    background: #fff7ed;
    padding: 1rem 1.5rem;
    border-radius: 0.5rem;
}

/* Remove default prose margins that might cause white space */
.prose > *:first-child {
    margin-top: 0 !important;
}

.prose ul, .prose ol {
    margin: 1rem 0;
}

.prose p {
    margin: 1rem 0;
}
</style>
@endpush

</x-layouts.public>