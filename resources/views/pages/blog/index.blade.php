{{-- resources/views/pages/blog/index.blade.php --}}
<x-layouts.public
    title="Blog - {{ $siteConfig['site_title'] }}"
    description="Baca wawasan terbaru, berita industri, dan update proyek dari para ahli konstruksi dan teknik kami."
    keywords="blog, berita konstruksi, wawasan teknik, update proyek"
    type="website"
>

{{-- Hero Section --}}
<section class="relative pt-32 pb-16 bg-gradient-to-br from-orange-50 via-white to-amber-50 overflow-hidden">
    {{-- Background Pattern --}}
    <div class="absolute inset-0 bg-[url('/images/grid.svg')] bg-center [mask-image:linear-gradient(180deg,white,rgba(255,255,255,0))]"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10">
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
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-orange-600 md:ml-2 font-medium">Blog</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                Blog 
                <span class="bg-gradient-to-r from-orange-600 to-amber-600 bg-clip-text text-transparent">
                    Kami
                </span>
            </h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-8">
                Ikuti perkembangan wawasan terbaru, berita industri, dan update proyek dari para ahli konstruksi dan teknik kami.
            </p>
            
            {{-- Quick Stats --}}
            <div class="flex justify-center gap-8 text-sm text-gray-600">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                    {{ $stats['total_posts'] }} Articles
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    {{ $stats['total_categories'] }} Kategori
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ $stats['this_month_posts'] }} This Month
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Main Content --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            {{-- Main Content Area --}}
            <div class="lg:col-span-3">
                {{-- Filter Bar --}}
                <div class="bg-gray-50 rounded-2xl p-6 mb-8">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        {{-- Cari Input --}}
                        <div class="md:col-span-2">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                                Cari Artikel
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       id="search" 
                                       name="search" 
                                       value="{{ $search }}"
                                       placeholder="Cari berdasarkan judul, konten..."
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>
                        
                        {{-- Category Filter --}}
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                Category
                            </label>
                            <select id="category" 
                                    name="category" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                <option value="all" {{ $category === 'all' ? 'selected' : '' }}>Semua Kategori</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->slug }}" {{ $category === $cat->slug ? 'selected' : '' }}>
                                    {{ $cat->name }} ({{ $cat->published_posts_count }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- Sort Filter --}}
                        <div>
                            <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">
                                Sort By
                            </label>
                            <select id="sort" 
                                    name="sort" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                <option value="latest" {{ $sortBy === 'latest' ? 'selected' : '' }}>Terbaru First</option>
                                <option value="oldest" {{ $sortBy === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                <option value="title" {{ $sortBy === 'title' ? 'selected' : '' }}>Title A-Z</option>
                                <option value="featured" {{ $sortBy === 'featured' ? 'selected' : '' }}>Unggulan First</option>
                            </select>
                        </div>
                    </form>
                    
                    {{-- Filter Actions --}}
                    <div class="flex flex-col sm:flex-row gap-4 mt-6">
                        <button type="submit" 
                                form="filter-form"
                                class="inline-flex items-center justify-center px-6 py-3 bg-orange-600 text-white font-semibold rounded-xl hover:bg-orange-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Apply Filters
                        </button>
                        <a href="{{ route('blog.index') }}" 
                           class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Clear All
                        </a>
                    </div>
                    
                    {{-- Results Summary --}}
                    @if($search || $category !== 'all')
                    <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                        <p class="text-sm text-blue-800">
                            Showing {{ $posts->count() }} of {{ $posts->total() }} articles
                            @if($search)
                                for "<strong>{{ $search }}</strong>"
                            @endif
                            @if($category && $category !== 'all')
                                in <strong>{{ $categories->where('slug', $category)->first()?->name }}</strong>
                            @endif
                        </p>
                    </div>
                    @endif
                </div>

                {{-- Posts Grid --}}
                @if($posts && $posts->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                    @foreach($posts as $post)
                    <article class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden transform hover:-translate-y-2">
                        {{-- Post Image --}}
                        <div class="relative h-48 overflow-hidden">
                            @if($post->featured_image)
                                <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                     alt="{{ $post->title }}" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-orange-400 to-amber-500 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                    </svg>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                            
                            {{-- Unggulan Badge --}}
                            @if($post->featured)
                            <div class="absolute top-3 left-3">
                                <span class="bg-orange-500 text-white px-2 py-1 rounded-lg text-xs font-medium">
                                    Unggulan
                                </span>
                            </div>
                            @endif
                            
                            {{-- Kategori --}}
                            @if($post->categories->count() > 0)
                            <div class="absolute top-3 right-3">
                                <span class="bg-white/90 text-gray-800 px-2 py-1 rounded-lg text-xs font-medium">
                                    {{ $post->categories->first()->name }}
                                </span>
                            </div>
                            @endif
                        </div>
                        
                        {{-- Post Content --}}
                        <div class="p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-orange-600 transition-colors line-clamp-2">
                                <a href="{{ route('blog.show', $post->slug) }}">
                                    {{ $post->title }}
                                </a>
                            </h2>
                            
                            @if($post->excerpt)
                            <p class="text-gray-600 mb-4 line-clamp-3">
                                {{ $post->excerpt }}
                            </p>
                            @endif
                            
                            {{-- Post Meta --}}
                            <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    {{ $post->author->name }}
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $post->published_at->format('M j, Y') }}
                                </div>
                            </div>
                            
                            {{-- Baca Selengkapnya Button --}}
                            <a href="{{ route('blog.show', $post->slug) }}" 
                               class="inline-flex items-center justify-center w-full px-4 py-3 bg-gradient-to-r from-orange-600 to-amber-600 text-white font-semibold rounded-xl hover:from-orange-700 hover:to-amber-700 transition-all duration-300 transform hover:scale-105">
                                Read Article
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </a>
                        </div>
                    </article>
                    @endforeach
                </div>
                
                {{-- Pagination --}}
                @if($posts->hasPages())
                <div class="flex justify-center">
                    {{ $posts->links() }}
                </div>
                @endif
                @else
                {{-- No Posts Found --}}
                <div class="text-center py-12">
                    <svg class="w-24 h-24 text-gray-300 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">No Articles Found</h3>
                    <p class="text-gray-600 mb-8">
                        We couldn't find any articles matching your criteria. Try adjusting your search or filters.
                    </p>
                    <a href="{{ route('blog.index') }}" 
                       class="inline-flex items-center px-6 py-3 bg-orange-600 text-white font-semibold rounded-xl hover:bg-orange-700 transition-colors">
                        Lihat Semua Artikel
                    </a>
                </div>
                @endif
            </div>
            
            {{-- Sidebar --}}
            <div class="lg:col-span-1">
                {{-- Unggulan Posts --}}
                @if($featuredPosts && $featuredPosts->count() > 0)
                <div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-2xl p-6 mb-8 border border-orange-100">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Unggulan Articles</h3>
                    <div class="space-y-4">
                        @foreach($featuredPosts as $featured)
                        <article class="group">
                            <div class="flex gap-4">
                                <div class="w-20 h-20 bg-gradient-to-br from-orange-400 to-amber-500 rounded-xl flex-shrink-0 overflow-hidden">
                                    @if($featured->featured_image)
                                        <img src="{{ asset('storage/' . $featured->featured_image) }}" 
                                             alt="{{ $featured->title }}" 
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-gray-900 mb-1 line-clamp-2 group-hover:text-orange-600 transition-colors">
                                        <a href="{{ route('blog.show', $featured->slug) }}">
                                            {{ $featured->title }}
                                        </a>
                                    </h4>
                                    <p class="text-xs text-gray-500">
                                        {{ $featured->published_at->format('M j, Y') }}
                                    </p>
                                </div>
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
                        @foreach($categories as $cat)
                        <a href="{{ route('blog.index', ['category' => $cat->slug]) }}" 
                           class="flex items-center justify-between p-3 rounded-xl hover:bg-orange-50 transition-colors group">
                            <span class="font-medium text-gray-700 group-hover:text-orange-600">
                                {{ $cat->name }}
                            </span>
                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-lg text-sm group-hover:bg-orange-100 group-hover:text-orange-600">
                                {{ $cat->published_posts_count }}
                            </span>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
                
                {{-- Recent Posts --}}
                @if($recentPosts && $recentPosts->count() > 0)
                <div class="bg-gray-50 rounded-2xl p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Artikel Terbaru</h3>
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
            </div>
        </div>
    </div>
</section>

{{-- JavaScript for Form Enhancement --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on filter change
    const filterForm = document.querySelector('form');
    const filterInputs = filterForm.querySelectorAll('select, input[name="search"]');
    
    // Add form ID for the submit button
    filterForm.setAttribute('id', 'filter-form');
    
    filterInputs.forEach(input => {
        if (input.type === 'text') {
            // Debounce search input
            let timeout;
            input.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    filterForm.submit();
                }, 800);
            });
        } else {
            // Auto-submit on select change
            input.addEventListener('change', function() {
                filterForm.submit();
            });
        }
    });
});
</script>
@endpush

{{-- Custom CSS --}}
@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush

</x-layouts.public>