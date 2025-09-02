{{-- resources/views/pages/portfolio/index.blade.php --}}
<x-layouts.public
    title="Portofolio Kami - {{ $siteConfig['site_title'] }}"
    description="Explore our completed construction and engineering projects. See the quality and craftsmanship we deliver."
    keywords="construction portfolio, completed projects, construction gallery, engineering projects"
    type="website"
>

{{-- Hero Section --}}
<section class="relative pt-32 pb-20 bg-gradient-to-br from-orange-50 via-white to-amber-50 overflow-hidden">
    {{-- Background Pattern --}}
    <div class="absolute inset-0 bg-[url('/images/grid.svg')] bg-center [mask-image:linear-gradient(180deg,white,rgba(255,255,255,0))]"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10">
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
                        <span class="ml-1 text-orange-600 md:ml-2 font-medium">Portofolio</span>
                    </div>
                </li>
            </ol>
        </nav>
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
                Our 
                <span class="bg-gradient-to-r from-orange-600 to-amber-600 bg-clip-text text-transparent">
                    Portofolio
                </span>
            </h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-8">
                Discover our completed construction and engineering projects that showcase our expertise, quality, and commitment to excellence.
            </p>
        </div>

        {{-- Stats Section --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-12">
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-2">{{ $stats['total_projects'] }}+</div>
                <div class="text-gray-600">Selesai Projects</div>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-2">{{ $stats['satisfied_clients'] }}+</div>
                <div class="text-gray-600">Happy Kliens</div>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-2">{{ $stats['active_categories'] }}+</div>
                <div class="text-gray-600">Kategori Proyek</div>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-2">{{ $stats['years_experience'] }}+</div>
                <div class="text-gray-600">Years Experience</div>
            </div>
        </div>

        {{-- Proyek Unggulan --}}
        @if($featuredProjects && $featuredProjects->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($featuredProjects as $project)
            <div class="group relative rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                <div class="aspect-w-16 aspect-h-10 relative">
                    @php
                        // Get featured image atau first image from eager loaded relationship
                        $featuredImage = $project->images->where('is_featured', true)->first() ?: $project->images->first();
                    @endphp
                    @if($featuredImage)
                        <img src="{{ asset('storage/' . $featuredImage->image_path) }}" 
                             alt="{{ $featuredImage->alt_text ?: $project->title }}" 
                             class="w-full h-64 object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-64 bg-gradient-to-br from-orange-400 to-amber-500 flex items-center justify-center">
                            <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                    <div class="absolute top-4 left-4">
                        <span class="bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                            Unggulan
                        </span>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                    <h3 class="text-xl font-bold mb-2 group-hover:text-orange-300 transition-colors">
                        {{ $project->title }}
                    </h3>
                    @if($project->category)
                    <p class="text-orange-200 text-sm mb-2">{{ $project->category->name }}</p>
                    @endif
                    <a href="{{ route('portfolio.show', $project->slug) }}" 
                       class="inline-flex items-center text-orange-300 hover:text-white transition-colors">
                        Lihat Detail
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

{{-- Portofolio Content Section --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Sidebar Filters --}}
            <div class="lg:w-1/4">
                <div class="bg-gray-50 rounded-2xl p-6 sticky top-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Filter Projects</h3>
                    
                    <form method="GET" id="portfolio-filters" class="space-y-6">
                        {{-- Cari --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                            <input type="text" 
                                   name="search" 
                                   value="{{ $search }}"
                                   placeholder="Cari projects..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm">
                        </div>

                        {{-- Category Filter --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm">
                                <option value="all" {{ $category === 'all' ? 'selected' : '' }}>All Kategori</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->slug }}" {{ $category === $cat->slug ? 'selected' : '' }}>
                                    {{ $cat->name }} ({{ $cat->active_projects_count }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Service Filter --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Service</label>
                            <select name="service" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm">
                                <option value="all" {{ $service === 'all' ? 'selected' : '' }}>Semua Layanan</option>
                                @foreach($services as $svc)
                                <option value="{{ $svc->slug }}" {{ $service === $svc->slug ? 'selected' : '' }}>
                                    {{ $svc->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Year Filter --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                            <select name="year" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm">
                                <option value="all" {{ $year === 'all' ? 'selected' : '' }}>All Years</option>
                                @foreach($years as $yr)
                                <option value="{{ $yr }}" {{ $year == $yr ? 'selected' : '' }}>
                                    {{ $yr }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Sort Filter --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                            <select name="sort" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm">
                                <option value="latest" {{ $sortBy === 'latest' ? 'selected' : '' }}>Terbaru First</option>
                                <option value="oldest" {{ $sortBy === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                <option value="featured" {{ $sortBy === 'featured' ? 'selected' : '' }}>Unggulan First</option>
                                <option value="title" {{ $sortBy === 'title' ? 'selected' : '' }}>Title A-Z</option>
                            </select>
                        </div>

                        {{-- Filter Actions --}}
                        <div class="space-y-3">
                            <button type="submit" 
                                    class="w-full bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors text-sm font-medium">
                                Apply Filters
                            </button>
                            <a href="{{ route('portfolio.index') }}" 
                               class="w-full block text-center border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium">
                                Clear All
                            </a>
                        </div>
                    </form>

                    {{-- Quick Stats --}}
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h4 class="text-sm font-medium text-gray-900 mb-4">Quick Stats</h4>
                        <div class="space-y-2 text-sm text-gray-600">
                            <div class="flex justify-between">
                                <span>Showing:</span>
                                <span class="font-medium">{{ $projects->count() }} of {{ $projects->total() }}</span>
                            </div>
                            @if($search)
                            <div class="flex justify-between">
                                <span>Cari:</span>
                                <span class="font-medium">"{{ $search }}"</span>
                            </div>
                            @endif
                            @if($category && $category !== 'all')
                            <div class="flex justify-between">
                                <span>Category:</span>
                                <span class="font-medium">{{ $categories->where('slug', $category)->first()?->name }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Projects Grid --}}
            <div class="lg:w-3/4">
                {{-- Results Header --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">
                            @if($search || ($category && $category !== 'all') || ($service && $service !== 'all') || ($year && $year !== 'all'))
                                Filtered Projects
                            @else
                                Semua Proyek
                            @endif
                        </h2>
                        <p class="text-gray-600">
                            Showing {{ $projects->count() }} of {{ $projects->total() }} projects
                        </p>
                    </div>
                </div>

                {{-- Projects Grid --}}
                @if($projects && $projects->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                    @foreach($projects as $project)
                    <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden transform hover:-translate-y-2">
                        {{-- Project Image --}}
                        <div class="relative h-48 overflow-hidden">
                            @php
                                // Get featured image atau first image from eager loaded relationship
                                $featuredImage = $project->images->where('is_featured', true)->first() ?: $project->images->first();
                            @endphp
                            @if($featuredImage)
                                <img src="{{ asset('storage/' . $featuredImage->image_path) }}" 
                                     alt="{{ $featuredImage->alt_text ?: $project->title }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                            
                            {{-- Project Badges --}}
                            <div class="absolute top-3 left-3 flex gap-2">
                                @if($project->featured)
                                <span class="bg-orange-500 text-white px-2 py-1 rounded-lg text-xs font-medium">
                                    Unggulan
                                </span>
                                @endif
                                @if($project->category)
                                <span class="bg-white/90 text-gray-800 px-2 py-1 rounded-lg text-xs font-medium">
                                    {{ $project->category->name }}
                                </span>
                                @endif
                            </div>

                            @if($project->year)
                            <div class="absolute top-3 right-3">
                                <span class="bg-black/60 text-white px-2 py-1 rounded-lg text-xs font-medium">
                                    {{ $project->year }}
                                </span>
                            </div>
                            @endif
                        </div>
                        
                        {{-- Project Content --}}
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-orange-600 transition-colors">
                                {{ $project->title }}
                            </h3>
                            
                            <div class="flex items-center text-sm text-gray-500 mb-3">
                                @if($project->service)
                                <span class="text-orange-600 font-medium">{{ $project->service->title }}</span>
                                @endif
                                @if($project->location)
                                <span class="mx-2">•</span>
                                <span>{{ $project->location }}</span>
                                @endif
                            </div>
                            
                            @if($project->short_description)
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                {{ $project->short_description }}
                            </p>
                            @endif
                            
                            <div class="flex items-center justify-between">
                                @if($project->client)
                                <span class="text-xs text-gray-500">
                                    Klien: {{ $project->client->name }}
                                </span>
                                @endif
                                <a href="{{ route('portfolio.show', $project->slug) }}" 
                                   class="inline-flex items-center text-orange-600 font-medium text-sm hover:text-orange-700 transition-colors">
                                    Lihat Detail
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($projects->hasPages())
                <div class="flex justify-center">
                    {{ $projects->links() }}
                </div>
                @endif
                @else
                {{-- No Projects Found --}}
                <div class="text-center py-12">
                    <svg class="w-24 h-24 text-gray-300 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">No Projects Found</h3>
                    <p class="text-gray-600 mb-8">
                        We couldn't find any projects matching your criteria. Try adjusting your filters.
                    </p>
                    <a href="{{ route('portfolio.index') }}" 
                       class="inline-flex items-center px-6 py-3 bg-orange-600 text-white font-semibold rounded-xl hover:bg-orange-700 transition-colors">
                        View Semua Proyek
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- CTA Section --}}
<section class="py-20 bg-gradient-to-r from-orange-600 via-amber-600 to-orange-700">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
            Siap Memulai Proyek Anda?
        </h2>
        <p class="text-xl text-orange-100 mb-8">
            Join our satisfied clients and let us bring your construction vision to life.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('contact.index') }}" 
               class="inline-flex items-center px-8 py-4 bg-white text-orange-600 font-semibold rounded-xl hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.959 8.959 0 01-4.906-1.405L3 21l2.595-5.094A8.959 8.959 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"/>
                </svg>
                Get Free Consultation
            </a>
            <a href="{{ route('services.index') }}" 
               class="inline-flex items-center px-8 py-4 border-2 border-white text-white font-semibold rounded-xl hover:bg-white hover:text-orange-600 transition-all duration-300">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                View Layanan Kami
            </a>
        </div>
    </div>
</section>

{{-- JavaScript for Enhanced Interactions --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on filter change
    const filterForm = document.getElementById('portfolio-filters');
    const filterInputs = filterForm.querySelectorAll('select, input[name="search"]');
    
    filterInputs.forEach(input => {
        if (input.type === 'text') {
            // Debounce search input
            let timeout;
            input.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    filterForm.submit();
                }, 500);
            });
        } else {
            // Auto-submit on select change
            input.addEventListener('change', function() {
                filterForm.submit();
            });
        }
    });
    
    // Loading state for form submission
    filterForm.addEventListener('submit', function() {
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        submitButton.innerHTML = `
            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Filtering...
        `;
        submitButton.disabled = true;
    });
});
</script>
@endpush

{{-- Additional CSS --}}
@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.aspect-w-16 {
    position: relative;
    padding-bottom: 62.5%;
}

.aspect-w-16 > * {
    position: absolute;
    height: 100%;
    width: 100%;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
}

/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

/* Sticky sidebar on larger screens */
@media (min-width: 1024px) {
    .sticky {
        position: sticky;
        top: 2rem;
    }
}
</style>
@endpush

</x-layouts.public>