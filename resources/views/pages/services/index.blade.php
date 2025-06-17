{{-- resources/views/pages/services/index.blade.php --}}
<x-layouts.public
    title="Our Services - {{ $siteConfig['site_title'] }}"
    description="Discover our comprehensive range of professional construction and engineering services. Quality solutions for all your project needs."
    keywords="construction services, engineering, building, renovation, consultation"
    type="website"
>

{{-- Hero Section --}}
<section class="relative min-h-screen flex items-center bg-gradient-to-br from-orange-50 via-white to-amber-50 overflow-hidden">
    {{-- Background Pattern --}}
    <div class="absolute inset-0 bg-[url('/images/grid.svg')] bg-center [mask-image:linear-gradient(180deg,white,rgba(255,255,255,0))]"></div>
    
    {{-- Floating Geometric Shapes --}}
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-1/4 left-1/4 w-32 h-32 bg-orange-200/30 rounded-full animate-float"></div>
        <div class="absolute bottom-1/3 right-1/4 w-48 h-48 bg-amber-200/20 rounded-full animate-float animation-delay-1000"></div>
        <div class="absolute top-1/2 right-1/3 w-24 h-24 bg-orange-300/40 rounded-full animate-float animation-delay-2000"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            {{-- Hero Content --}}
            <div class="text-center lg:text-left">
                <div class="flex items-center justify-center lg:justify-start mb-6">
                    <div class="w-16 h-1 bg-gradient-to-r from-orange-500 to-amber-500 rounded-full mr-4"></div>
                    <span class="text-orange-600 font-semibold text-lg">Professional Services</span>
                </div>
                
                <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6 animate-fade-in-up">
                    Comprehensive 
                    <span class="bg-gradient-to-r from-orange-600 to-amber-600 bg-clip-text text-transparent">
                        Construction
                    </span>
                    Solutions
                </h1>
                
                <p class="text-xl text-gray-600 mb-8 leading-relaxed animate-fade-in-up animation-delay-200">
                    From innovative design to flawless execution, we deliver exceptional construction and engineering services tailored to meet your specific needs and requirements.
                </p>
                
                {{-- CTA Buttons --}}
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start animate-fade-in-up animation-delay-400">
                    <a href="#services-grid" 
                       class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-orange-600 to-amber-600 text-white font-semibold rounded-xl hover:from-orange-700 hover:to-amber-700 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        Explore Services
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    </a>
                    <a href="{{ route('contact.index') }}" 
                       class="inline-flex items-center px-8 py-4 border-2 border-orange-600 text-orange-600 font-semibold rounded-xl hover:bg-orange-600 hover:text-white transition-all duration-300">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        Get Free Quote
                    </a>
                </div>
                
                {{-- Contact Info --}}
                @if($contactInfo['phone'])
                <div class="flex items-center justify-center lg:justify-start mt-8 animate-fade-in-up animation-delay-600">
                    <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <span class="font-medium text-gray-700">Call us: {{ $contactInfo['phone'] }}</span>
                </div>
                @endif
            </div>
            
            {{-- Hero Visual --}}
            <div class="relative animate-fade-in-left animation-delay-800">
                <div class="relative rounded-2xl overflow-hidden shadow-2xl transform hover:scale-105 transition-transform duration-500">
                    @if($featuredServices->first() && $featuredServices->first()->featured_image)
                        <img src="{{ asset('storage/' . $featuredServices->first()->featured_image) }}" 
                             alt="Featured Service" 
                             class="w-full h-96 object-cover">
                    @else
                        <div class="w-full h-96 bg-gradient-to-br from-orange-400 to-amber-500 flex items-center justify-center">
                            <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                </div>
                
                {{-- Floating Stats Cards --}}
                <div class="absolute -bottom-6 -left-6 bg-white rounded-xl shadow-lg p-4 animate-float">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">{{ $stats['total_services'] }}+</div>
                        <div class="text-sm text-gray-600">Services</div>
                    </div>
                </div>
                
                <div class="absolute -top-6 -right-6 bg-white rounded-xl shadow-lg p-4 animate-float animation-delay-500">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">{{ $stats['satisfied_clients'] }}+</div>
                        <div class="text-sm text-gray-600">Happy Clients</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Scroll Indicator --}}
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
        </svg>
    </div>
</section>

{{-- Stats Section --}}
<section class="py-16 bg-white relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="text-center group">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-orange-200 transition-colors duration-300">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-2 counter" data-count="{{ $stats['total_services'] }}">0</div>
                <div class="text-gray-600">Professional Services</div>
            </div>
            
            <div class="text-center group">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-orange-200 transition-colors duration-300">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-2 counter" data-count="{{ $stats['completed_projects'] }}">0</div>
                <div class="text-gray-600">Completed Projects</div>
            </div>
            
            <div class="text-center group">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-orange-200 transition-colors duration-300">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-2 counter" data-count="{{ $stats['satisfied_clients'] }}">0</div>
                <div class="text-gray-600">Happy Clients</div>
            </div>
            
            <div class="text-center group">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-orange-200 transition-colors duration-300">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-2 counter" data-count="{{ $stats['team_experts'] }}">0</div>
                <div class="text-gray-600">Expert Team</div>
            </div>
        </div>
    </div>
</section>

{{-- Featured Services Showcase --}}
@if($featuredServices && $featuredServices->count() > 0)
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Featured Services</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Our most popular and specialized services that showcase our expertise and commitment to excellence.
            </p>
        </div>
        
        {{-- Featured Services Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            @foreach($featuredServices as $service)
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden transform hover:-translate-y-2">
                {{-- Service Image --}}
                <div class="relative h-64 overflow-hidden">
                    @if($service->featured_image)
                        <img src="{{ asset('storage/' . $service->featured_image) }}" 
                             alt="{{ $service->title }}" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-orange-400 to-amber-500 flex items-center justify-center">
                            <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                    <div class="absolute top-4 right-4">
                        <span class="bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                            Featured
                        </span>
                    </div>
                </div>
                
                {{-- Service Content --}}
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-orange-600 transition-colors">
                        {{ $service->title }}
                    </h3>
                    <p class="text-gray-600 mb-4 line-clamp-3">
                        {{ $service->short_description ?: $service->description }}
                    </p>
                    
                    {{-- Service Meta --}}
                    <div class="flex items-center justify-between mb-4">
                        @if($service->category)
                        <span class="text-orange-600 text-sm font-medium">
                            {{ $service->category->name }}
                        </span>
                        @endif
                        @if($service->base_price)
                        <span class="text-gray-900 font-semibold">
                            From ${{ number_format($service->base_price) }}
                        </span>
                        @endif
                    </div>
                    
                    {{-- CTA Button --}}
                    <a href="{{ route('services.show', $service->slug) }}" 
                       class="inline-flex items-center justify-center w-full px-6 py-3 bg-gradient-to-r from-orange-600 to-amber-600 text-white font-semibold rounded-xl hover:from-orange-700 hover:to-amber-700 transition-all duration-300 transform hover:scale-105">
                        Learn More
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Search and Filter Section --}}
<section class="py-12 bg-white border-t border-gray-200" id="services-grid">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Search and Filter Header --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">All Services</h2>
                <p class="text-gray-600">
                    Showing {{ $services->count() }} of {{ $services->total() }} services
                    @if($search)
                        for "{{ $search }}"
                    @endif
                    @if($category && $category !== 'all')
                        in {{ $categories->where('slug', $category)->first()?->name }}
                    @endif
                </p>
            </div>
            
            {{-- Quick Actions --}}
            <div class="flex flex-col sm:flex-row gap-4 mt-4 lg:mt-0">
                <a href="{{ route('contact.index') }}" 
                   class="inline-flex items-center px-6 py-3 bg-orange-600 text-white font-semibold rounded-xl hover:bg-orange-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.959 8.959 0 01-4.906-1.405L3 21l2.595-5.094A8.959 8.959 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"/>
                    </svg>
                    Free Consultation
                </a>
            </div>
        </div>
        
        {{-- Search and Filter Form --}}
        <form method="GET" class="bg-gray-50 rounded-2xl p-6 mb-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- Search Input --}}
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                        Search Services
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="search" 
                               name="search" 
                               value="{{ $search }}"
                               placeholder="Search by service name or description..."
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
                        <option value="all" {{ $category === 'all' ? 'selected' : '' }}>All Categories</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}" {{ $category === $cat->slug ? 'selected' : '' }}>
                            {{ $cat->name }} ({{ $cat->active_services_count }})
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
                        <option value="featured" {{ $sortBy === 'featured' ? 'selected' : '' }}>Featured First</option>
                        <option value="title" {{ $sortBy === 'title' ? 'selected' : '' }}>Name A-Z</option>
                        <option value="newest" {{ $sortBy === 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="price" {{ $sortBy === 'price' ? 'selected' : '' }}>Price Low to High</option>
                    </select>
                </div>
            </div>
            
            {{-- Filter Actions --}}
            <div class="flex flex-col sm:flex-row gap-4 mt-6">
                <button type="submit" 
                        class="inline-flex items-center justify-center px-6 py-3 bg-orange-600 text-white font-semibold rounded-xl hover:bg-orange-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Apply Filters
                </button>
                <a href="{{ route('services.index') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Clear All
                </a>
            </div>
        </form>
    </div>
</section>

{{-- Services Grid --}}
@if($services && $services->count() > 0)
<section class="pb-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Services Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            @foreach($services as $service)
            <div class="group bg-white rounded-2xl border border-gray-200 hover:border-orange-300 shadow-md hover:shadow-xl transition-all duration-500 overflow-hidden transform hover:-translate-y-1">
                {{-- Service Image --}}
                <div class="relative h-48 overflow-hidden">
                    @if($service->featured_image)
                        <img src="{{ asset('storage/' . $service->featured_image) }}" 
                             alt="{{ $service->title }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                    
                    {{-- Featured Badge --}}
                    @if($service->featured)
                    <div class="absolute top-3 left-3">
                        <span class="bg-orange-500 text-white px-2 py-1 rounded-lg text-xs font-medium">
                            Featured
                        </span>
                    </div>
                    @endif
                    
                    {{-- Category Badge --}}
                    @if($service->category)
                    <div class="absolute top-3 right-3">
                        <span class="bg-white/90 text-gray-800 px-2 py-1 rounded-lg text-xs font-medium">
                            {{ $service->category->name }}
                        </span>
                    </div>
                    @endif
                </div>
                
                {{-- Service Content --}}
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-orange-600 transition-colors line-clamp-2">
                        {{ $service->title }}
                    </h3>
                    <p class="text-gray-600 mb-4 line-clamp-3">
                        {{ $service->short_description ?: Str::limit($service->description, 120) }}
                    </p>
                    
                    {{-- Service Features --}}
                    @if($service->projects && $service->projects->count() > 0)
                    <div class="flex items-center text-sm text-gray-500 mb-4">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        {{ $service->projects->count() }} completed projects
                    </div>
                    @endif
                    
                    {{-- Pricing --}}
                    <div class="flex items-center justify-between mb-4">
                        @if($service->base_price)
                        <div class="text-lg font-bold text-gray-900">
                            From ${{ number_format($service->base_price) }}
                        </div>
                        @endif
                        <div class="flex items-center text-yellow-500">
                            @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            @endfor
                        </div>
                    </div>
                    
                    {{-- CTA Buttons --}}
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('services.show', $service->slug) }}" 
                           class="flex-1 inline-flex items-center justify-center px-4 py-3 bg-orange-600 text-white font-semibold rounded-xl hover:bg-orange-700 transition-colors">
                            Learn More
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                        <a href="{{ route('contact.index', ['service' => $service->slug]) }}" 
                           class="flex-1 inline-flex items-center justify-center px-4 py-3 border border-orange-600 text-orange-600 font-semibold rounded-xl hover:bg-orange-50 transition-colors">
                            Get Quote
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        {{-- Pagination --}}
        @if($services->hasPages())
        <div class="flex justify-center">
            {{ $services->links() }}
        </div>
        @endif
    </div>
</section>
@else
{{-- No Services Found --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="max-w-md mx-auto">
            <svg class="w-24 h-24 text-gray-300 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <h3 class="text-2xl font-bold text-gray-900 mb-4">No Services Found</h3>
            <p class="text-gray-600 mb-8">
                We couldn't find any services matching your criteria. Try adjusting your search or filters.
            </p>
            <a href="{{ route('services.index') }}" 
               class="inline-flex items-center px-6 py-3 bg-orange-600 text-white font-semibold rounded-xl hover:bg-orange-700 transition-colors">
                View All Services
            </a>
        </div>
    </div>
</section>
@endif

{{-- Recent Projects Showcase --}}
@if($recentProjects && $recentProjects->count() > 0)
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Recent Projects</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                See how we bring our services to life through successful project implementations and satisfied clients.
            </p>
        </div>
        
        {{-- Projects Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            @foreach($recentProjects as $project)
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden transform hover:-translate-y-2">
                {{-- Project Image --}}
                <div class="relative h-56 overflow-hidden">
                    @if($project->featured_image)
                        <img src="{{ asset('storage/' . $project->featured_image) }}" 
                             alt="{{ $project->title }}" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-orange-400 to-amber-500 flex items-center justify-center">
                            <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                    @if($project->category)
                    <div class="absolute top-4 left-4">
                        <span class="bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                            {{ $project->category->name }}
                        </span>
                    </div>
                    @endif
                </div>
                
                {{-- Project Content --}}
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-orange-600 transition-colors">
                        {{ $project->title }}
                    </h3>
                    @if($project->client)
                    <p class="text-orange-600 text-sm font-medium mb-3">
                        {{ $project->client->name }}
                    </p>
                    @endif
                    <p class="text-gray-600 mb-4 line-clamp-3">
                        {{ $project->short_description ?: Str::limit($project->description, 120) }}
                    </p>
                    
                    {{-- Project Meta --}}
                    <div class="flex items-center justify-between">
                        @if($project->completed_at)
                        <span class="text-gray-500 text-sm">
                            Completed {{ $project->completed_at->format('M Y') }}
                        </span>
                        @endif
                        <a href="{{ route('portfolio.show', $project->slug) }}" 
                           class="inline-flex items-center text-orange-600 font-semibold hover:text-orange-700 transition-colors">
                            View Project
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        {{-- View All Projects Button --}}
        <div class="text-center">
            <a href="{{ route('portfolio.index') }}" 
               class="inline-flex items-center px-8 py-4 bg-orange-600 text-white font-semibold rounded-xl hover:bg-orange-700 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                View All Projects
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>
    </div>
</section>
@endif

{{-- Testimonials Section --}}
@if($testimonials && $testimonials->count() > 0)
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">What Our Clients Say</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Real feedback from satisfied clients who have experienced our professional services firsthand.
            </p>
        </div>
        
        {{-- Testimonials Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($testimonials as $testimonial)
            <div class="bg-gray-50 rounded-2xl p-8 hover:shadow-lg transition-shadow duration-300">
                {{-- Rating Stars --}}
                <div class="flex items-center mb-4">
                    @for($i = 1; $i <= 5; $i++)
                    <svg class="w-5 h-5 {{ $i <= ($testimonial->rating ?? 5) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    @endfor
                </div>
                
                {{-- Testimonial Content --}}
                <blockquote class="text-gray-700 mb-6 italic">
                    "{{ $testimonial->content }}"
                </blockquote>
                
                {{-- Client Info --}}
                <div class="flex items-center">
                    @if($testimonial->client_photo)
                    <img src="{{ asset('storage/' . $testimonial->client_photo) }}" 
                         alt="{{ $testimonial->client_name }}" 
                         class="w-12 h-12 rounded-full object-cover mr-4">
                    @else
                    <div class="w-12 h-12 bg-orange-200 rounded-full flex items-center justify-center mr-4">
                        <span class="text-orange-600 font-semibold text-lg">
                            {{ substr($testimonial->client_name, 0, 1) }}
                        </span>
                    </div>
                    @endif
                    <div>
                        <h4 class="font-semibold text-gray-900">{{ $testimonial->client_name }}</h4>
                        @if($testimonial->client_position || $testimonial->client_company)
                        <p class="text-sm text-gray-600">
                            {{ $testimonial->client_position }}
                            @if($testimonial->client_position && $testimonial->client_company), @endif
                            {{ $testimonial->client_company }}
                        </p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CTA Section --}}
<section class="py-20 bg-gradient-to-r from-orange-600 via-amber-600 to-orange-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl font-bold text-white mb-6">
            Ready to Start Your Project?
        </h2>
        <p class="text-xl text-orange-100 mb-8 max-w-3xl mx-auto">
            Get in touch with our experts today for a free consultation and detailed quote for your construction needs.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('contact.index') }}" 
               class="inline-flex items-center px-8 py-4 bg-white text-orange-600 font-semibold rounded-xl hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.959 8.959 0 01-4.906-1.405L3 21l2.595-5.094A8.959 8.959 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"/>
                </svg>
                Get Free Consultation
            </a>
            @if($contactInfo['phone'])
            <a href="tel:{{ $contactInfo['phone'] }}" 
               class="inline-flex items-center px-8 py-4 border-2 border-white text-white font-semibold rounded-xl hover:bg-white hover:text-orange-600 transition-all duration-300">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                Call {{ $contactInfo['phone'] }}
            </a>
            @endif
        </div>
    </div>
</section>

{{-- Custom JavaScript for Enhanced Interactions --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Counter Animation
    const counters = document.querySelectorAll('.counter');
    const animateCounter = (counter) => {
        const target = parseInt(counter.getAttribute('data-count'));
        const count = +counter.innerText;
        const increment = target / 100;
        
        if (count < target) {
            counter.innerText = Math.ceil(count + increment);
            setTimeout(() => animateCounter(counter), 20);
        } else {
            counter.innerText = target;
        }
    };
    
    // Intersection Observer for counter animation
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                counterObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.7 });
    
    counters.forEach(counter => {
        counterObserver.observe(counter);
    });
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Auto-submit form on filter change
    const filterForm = document.querySelector('form');
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
    
    // Enhanced card hover effects
    const serviceCards = document.querySelectorAll('.group');
    serviceCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Loading animation for buttons
    const buttons = document.querySelectorAll('button[type="submit"], a[href*="contact"]');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (this.tagName === 'BUTTON') {
                // Add loading spinner to submit buttons
                const originalText = this.innerHTML;
                this.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                `;
                this.disabled = true;
                
                // Reset after form submission
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                }, 2000);
            }
        });
    });
    
    // Parallax effect for floating shapes
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('.animate-float');
        
        parallaxElements.forEach((element, index) => {
            const speed = 0.5 + (index * 0.1);
            element.style.transform = `translateY(${scrolled * speed}px)`;
        });
    });
    
    // Lazy loading for images
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('opacity-0');
                img.classList.add('opacity-100');
                imageObserver.unobserve(img);
            }
        });
    });
    
    images.forEach(img => {
        imageObserver.observe(img);
    });
});
</script>
@endpush

{{-- Custom CSS for additional styling --}}
@push('styles')
<style>
    /* Animation keyframes */
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }
    
    @keyframes fade-in-up {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fade-in-left {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    /* Animation classes */
    .animate-float {
        animation: float 6s ease-in-out infinite;
    }
    
    .animate-fade-in-up {
        animation: fade-in-up 1s ease-out forwards;
    }
    
    .animate-fade-in-left {
        animation: fade-in-left 1s ease-out forwards;
    }
    
    /* Animation delays */
    .animation-delay-200 {
        animation-delay: 200ms;
    }
    
    .animation-delay-400 {
        animation-delay: 400ms;
    }
    
    .animation-delay-500 {
        animation-delay: 500ms;
    }
    
    .animation-delay-600 {
        animation-delay: 600ms;
    }
    
    .animation-delay-800 {
        animation-delay: 800ms;
    }
    
    .animation-delay-1000 {
        animation-delay: 1000ms;
    }
    
    .animation-delay-2000 {
        animation-delay: 2000ms;
    }
    
    /* Line clamp utilities */
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
    
    /* Custom gradient backgrounds */
    .bg-gradient-orange {
        background: linear-gradient(135deg, #f97316 0%, #fb923c 25%, #fbbf24 50%, #f59e0b 75%, #ea580c 100%);
    }
    
    /* Enhanced hover effects */
    .hover-lift {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .hover-lift:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(249, 115, 22, 0.1), 0 10px 10px -5px rgba(249, 115, 22, 0.04);
    }
    
    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    ::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #f97316, #fbbf24);
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(180deg, #ea580c, #f59e0b);
    }
    
    /* Focus states */
    .focus-ring:focus {
        outline: 2px solid #f97316;
        outline-offset: 2px;
    }
    
    /* Loading states */
    .loading {
        pointer-events: none;
        opacity: 0.7;
    }
    
    /* Responsive text sizing */
    @media (max-width: 640px) {
        .text-4xl {
            font-size: 2.25rem;
            line-height: 2.5rem;
        }
        
        .text-6xl {
            font-size: 3rem;
            line-height: 1;
        }
    }
</style>
@endpush

</x-layouts.public>