{{-- resources/views/pages/home.blade.php --}}
<x-layouts.public 
    :title="$siteConfig['site_title']" 
    :description="$siteConfig['site_description']"
    :keywords="$siteConfig['site_keywords']"
    bodyClass="homepage"
>

{{-- Hero Section dengan Banner --}}
@if($heroBanners && $heroBanners->count() > 0)
<section class="hero-section relative min-h-screen overflow-hidden">
    <div class="hero-slider relative h-screen">
        @foreach($heroBanners as $index => $banner)
            <div class="hero-slide absolute inset-0 {{ $index === 0 ? 'opacity-100' : 'opacity-0' }} transition-opacity duration-1000">
                <div class="absolute inset-0 bg-black bg-opacity-40 z-10"></div>
                
                @if($banner->image)
                    <img 
                        src="{{ Storage::url($banner->image) }}" 
                        alt="{{ $banner->title }}"
                        class="w-full h-full object-cover"
                        loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                    >
                @else
                    <div class="w-full h-full bg-gradient-to-br from-orange-600 to-amber-600"></div>
                @endif
                
                <div class="absolute inset-0 z-20 flex items-center justify-center">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
                        <h1 class="text-5xl md:text-7xl font-bold mb-6 animate-fade-in-up">
                            {{ $banner->title }}
                        </h1>
                        
                        @if($banner->description)
                            <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto leading-relaxed animate-fade-in-up animation-delay-300">
                                {{ $banner->description }}
                            </p>
                        @endif
                        
                        @if($banner->button_text && $banner->button_link)
                            <div class="animate-fade-in-up animation-delay-600">
                                <a href="{{ $banner->button_link }}" 
                                   class="cta-button inline-flex items-center px-8 py-4 bg-orange-600 text-white font-semibold rounded-xl hover:bg-orange-700 transition-all duration-300 transform hover:scale-105 shadow-xl hover:shadow-2xl">
                                    {{ $banner->button_text }}
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    {{-- Hero Navigation --}}
    @if($heroBanners->count() > 1)
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-30">
            <div class="flex space-x-2">
                @foreach($heroBanners as $index => $banner)
                    <button 
                        class="hero-dot w-3 h-3 rounded-full transition-all duration-300 {{ $index === 0 ? 'bg-white' : 'bg-white bg-opacity-50' }}"
                        onclick="switchHeroSlide({{ $index }})"
                    ></button>
                @endforeach
            </div>
        </div>
    @endif
    
    {{-- Statistics Overlay --}}
    @if($stats)
        <div class="floating-stats absolute bottom-20 right-8 hidden lg:block">
            <div class="bg-white bg-opacity-95 backdrop-blur-sm rounded-2xl p-6 shadow-2xl animate-float">
                <div class="grid grid-cols-2 gap-4 text-center">
                    <div>
                        <div class="text-3xl font-bold text-orange-600">{{ $stats['completed_projects'] }}+</div>
                        <div class="text-sm text-gray-600">Projects</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-orange-600">{{ $stats['happy_clients'] }}+</div>
                        <div class="text-sm text-gray-600">Kliens</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-orange-600">{{ $stats['years_experience'] }}+</div>
                        <div class="text-sm text-gray-600">Years</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-orange-600">{{ $stats['active_services'] }}+</div>
                        <div class="text-sm text-gray-600">Layanan</div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</section>
@else
{{-- Fallback Hero Section --}}
<section class="hero-section bg-gradient-to-br from-orange-600 to-amber-600 min-h-screen flex items-center justify-center text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-5xl md:text-7xl font-bold mb-6 animate-fade-in-up">
            {{ $siteConfig['site_title'] }}
        </h1>
        <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto leading-relaxed animate-fade-in-up animation-delay-300">
            {{ $siteConfig['site_description'] }}
        </p>
        <div class="animate-fade-in-up animation-delay-600">
            <a href="{{ route('services.index') }}" 
               class="cta-button inline-flex items-center px-8 py-4 bg-white text-orange-600 font-semibold rounded-xl hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-xl hover:shadow-2xl">
                Explore Layanan Kami
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>
    </div>
</section>
@endif

{{-- Statistics Section (Mobile) --}}
@if($stats)
<section class="py-16 bg-gray-50 lg:hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="stats-grid grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div class="animate-in">
                <div class="text-4xl md:text-5xl font-bold text-orange-600 mb-2">{{ $stats['completed_projects'] }}+</div>
                <div class="text-gray-600 font-medium">Selesai Projects</div>
            </div>
            <div class="animate-in animation-delay-200">
                <div class="text-4xl md:text-5xl font-bold text-orange-600 mb-2">{{ $stats['happy_clients'] }}+</div>
                <div class="text-gray-600 font-medium">Happy Kliens</div>
            </div>
            <div class="animate-in animation-delay-400">
                <div class="text-4xl md:text-5xl font-bold text-orange-600 mb-2">{{ $stats['years_experience'] }}+</div>
                <div class="text-gray-600 font-medium">Years Experience</div>
            </div>
            <div class="animate-in animation-delay-600">
                <div class="text-4xl md:text-5xl font-bold text-orange-600 mb-2">{{ $stats['active_services'] }}+</div>
                <div class="text-gray-600 font-medium">Layanan</div>
            </div>
        </div>
    </div>
</section>
@endif

{{-- Tentang Section --}}
@if($companyProfile)
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            {{-- Content --}}
            <div class="animate-fade-in-left">
                <h2 class="text-4xl font-bold text-gray-900 mb-6">
                    Why Choose 
                    <span class="text-orange-600">{{ $companyProfile->company_name ?? config('app.name') }}</span>?
                </h2>
                
                @if($companyProfile->about_us)
                    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                        {{ Str::limit($companyProfile->about_us, 300) }}
                    </p>
                @endif
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 w-6 h-6 bg-orange-100 rounded-full flex items-center justify-center mt-1">
                            <svg class="w-4 h-4 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Professional Excellence</h4>
                            <p class="text-gray-600">Delivering quality solutions with expert precision</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 w-6 h-6 bg-orange-100 rounded-full flex items-center justify-center mt-1">
                            <svg class="w-4 h-4 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Trusted Partnership</h4>
                            <p class="text-gray-600">Building lasting relationships with every client</p>
                        </div>
                    </div>
                </div>
                
                <a href="{{ route('about.index') }}" 
                   class="inline-flex items-center px-6 py-3 border-2 border-orange-600 text-orange-600 font-semibold rounded-lg hover:bg-orange-600 hover:text-white transition-all duration-300">
                    Pelajari Lebih Lanjut Tentang Kami
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>
            
            {{-- Image --}}
            <div class="animate-fade-in-right">
                    <img 
                        src="https://picsum.photos/id/1/200/300" 
                        alt="{{ $companyProfile->company_name }}"
                        class="w-full h-200 rounded-2xl shadow-2xl"
                        loading="lazy"
                    >
            </div>
        </div>
    </div>
</section>
@endif

{{-- Layanan Section --}}
@if($featuredServices && $featuredServices->count() > 0)
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4 animate-in">Layanan Kami</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto animate-in animation-delay-200">
                Comprehensive solutions tailored to meet your business needs with professional excellence.
            </p>
        </div>
        
        {{-- Layanan Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($featuredServices as $index => $service)
                <div class="service-card bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 animate-in" style="animation-delay: {{ $index * 200 }}ms;">
                    {{-- Service Icon --}}
                    <div class="w-16 h-16 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        @if($service->icon)
                            <img src="{{ asset('storage/' . $service->icon) }}" alt="{{ $service->name }} Icon" class="w-16 h-16 object-contain" />
                        @else
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"/>
                            </svg>
                        @endif
                    </div>
                    
                    {{-- Service Content --}}
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ $service->name }}</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        {{ Str::limit($service->description, 120) }}
                    </p>
                    
                    {{-- Service Link --}}
                    <a href="{{ route('services.show', $service->slug) }}" 
                       class="inline-flex items-center text-orange-600 font-medium hover:text-orange-700 transition-colors duration-300 group">
                        Pelajari Lebih Lanjut
                        <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                </div>
            @endforeach
        </div>
        
        {{-- View Semua Layanan Button --}}
        <div class="text-center mt-12">
            <a href="{{ route('services.index') }}" 
               class="inline-flex items-center px-8 py-4 bg-orange-600 text-white font-semibold rounded-xl hover:bg-orange-700 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                View Semua Layanan
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>
    </div>
</section>
@endif

{{-- Portofolio Section --}}
@if($featuredProjects && $featuredProjects->count() > 0)
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <div class="inline-flex items-center px-4 py-2 bg-orange-100 text-orange-600 rounded-full text-sm font-semibold mb-4 animate-in">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Keunggulan Terbukti
            </div>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4 animate-in">Portofolio Kesuksesan Kami</h2>
            <p class="text-xl text-gray-600 max-w-4xl mx-auto animate-in animation-delay-200 leading-relaxed">
                Temukan bagaimana kami membantu para pemimpin industri mencapai hasil yang luar biasa melalui solusi inovatif dan keahlian yang terpercaya. Setiap proyek mencerminkan komitmen kami untuk memberikan kualitas yang melampaui ekspektasi.
            </p>
            
            {{-- Portofolio Statistics --}}
            <div class="flex flex-wrap justify-center gap-8 mt-8 animate-in animation-delay-400">
                <div class="text-center">
                    <div class="text-2xl font-bold text-orange-600">{{ $stats['completed_projects'] }}+</div>
                    <div class="text-sm text-gray-500">Proyek Selesai</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-orange-600">{{ $stats['happy_clients'] }}+</div>
                    <div class="text-sm text-gray-500">Klien Puas</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-orange-600">95%</div>
                    <div class="text-sm text-gray-500">Tingkat Keberhasilan</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-orange-600">{{ $stats['years_experience'] }}+</div>
                    <div class="text-sm text-gray-500">Tahun Pengalaman</div>
                </div>
            </div>
        </div>
        
        {{-- Projects Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($featuredProjects as $index => $project)
                <div class="project-card group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 animate-in" style="animation-delay: {{ $index * 200 }}ms;">
                    
                    {{-- Project Image - CORRECTED STRUCTURE --}}
                    <div class="relative overflow-hidden h-64">
                        @if($project->featured_image_url)
        <img src="{{ $project->featured_image_url }}"
            alt="{{ $project->title }}"
            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
            loading="lazy"
        >
    @else
        <div class="w-full h-full bg-gradient-to-br from-orange-100 to-amber-100 flex items-center justify-center">
            <svg class="w-16 h-16 text-orange-300" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
            </svg>
        </div>
    @endif
                        
                        {{-- Hover Overlay --}}
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center">
                            <a href="{{ route('portfolio.show', $project->slug) }}" 
                               class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-white text-orange-600 px-6 py-3 rounded-lg font-semibold hover:bg-orange-50 transform translate-y-4 group-hover:translate-y-0">
                                Lihat Proyek
                            </a>
                        </div>
                    </div>
                    
                    {{-- Project Content --}}
                    <div class="p-6">
                        {{-- Category Badge --}}
                        @if($project->category)
                            <span class="inline-block px-3 py-1 bg-orange-100 text-orange-600 text-sm font-medium rounded-full mb-3">
                                {{ $project->category->name }}
                            </span>
                        @endif
                        
                        {{-- Project Title --}}
                        <h3 class="text-xl font-semibold text-gray-900 mb-3 group-hover:text-orange-600 transition-colors duration-300">
                            <a href="{{ route('portfolio.show', $project->slug) }}">
                                {{ $project->title }}
                            </a>
                        </h3>
                        
                        {{-- Project Description --}}
                        @if($project->excerpt)
                            <p class="text-gray-600 leading-relaxed mb-4 line-clamp-3">
                                {{ $project->excerpt }}
                            </p>
                        @elseif($project->description)
                            <p class="text-gray-600 leading-relaxed mb-4 line-clamp-3">
                                {{ Str::limit(strip_tags($project->description), 120) }}
                            </p>
                        @endif
                        
                        {{-- Project Meta Information --}}
                        <div class="flex items-center justify-between text-sm text-gray-500">
                            {{-- Klien Info --}}
                            @if($project->client)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $project->client->name }}
                                </div>
                            @endif
                            
                            {{-- Project Status or Date --}}
                            @if($project->completed_at)
                                <div>
                                    {{ $project->completed_at->format('M Y') }}
                                </div>
                            @elseif($project->status)
                                <div class="px-2 py-1 rounded text-xs {{ $project->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div> 
            @endforeach
        </div>
        
        {{-- View Semua Proyek Button --}}
        <div class="text-center mt-12">
            <a href="{{ route('portfolio.index') }}" 
               class="inline-flex items-center px-8 py-4 bg-orange-600 text-white font-semibold rounded-xl hover:bg-orange-700 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                Lihat Semua Proyek
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>
    </div>
</section>
@endif
{{-- Unggulan Produk Section --}}
@if(isset($featuredProducts) && $featuredProducts && $featuredProducts->count() > 0)
<section class="py-20 bg-gradient-to-br from-orange-50 via-white to-amber-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4 animate-in">Unggulan Produk</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto animate-in animation-delay-200">
                Discover our top-quality construction and engineering products designed to meet your project requirements.
            </p>
        </div>
        
        {{-- Produk Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($featuredProducts->take(6) as $index => $product)
                <div class="product-card group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 animate-in" style="animation-delay: {{ $index * 200 }}ms;">
                    {{-- Product Image --}}
                    <div class="relative overflow-hidden h-64">
                        @php
                                            // Get the main image (featured or first image)
                                            $mainImage =
                                                $product->images->where('is_featured', true)->first() ?:
                                                $product->images->first();
                                        @endphp

                                        @if ($mainImage)
                                            <img src="{{ $mainImage->image_url }}"
                                                alt="{{ $mainImage->alt_text ?: $product->name }}"
                                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                        @else
                                            <div
                                                class="w-full h-full bg-gradient-to-br from-orange-100 to-amber-100 flex items-center justify-center">
                                                <svg class="w-16 h-16 text-orange-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1"
                                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                            </div>
                                        @endif
                        
                        {{-- Product Badges --}}
                        <div class="absolute top-3 left-3 flex flex-col space-y-2">
                            <span class="bg-amber-500 text-white px-2 py-1 rounded-lg text-xs font-medium">
                                Unggulan
                            </span>
                            
                            @if($product->stock_status === 'in_stock')
                            <span class="bg-green-500 text-white px-2 py-1 rounded-lg text-xs font-medium">
                                In Stock
                            </span>
                            @elseif($product->stock_status === 'out_of_stock')
                            <span class="bg-red-500 text-white px-2 py-1 rounded-lg text-xs font-medium">
                                Out of Stock
                            </span>
                            @endif
                        </div>

                        {{-- Category Badge --}}
                        @if($product->category)
                        <div class="absolute top-3 right-3">
                            <span class="bg-white/90 text-gray-800 px-2 py-1 rounded-lg text-xs font-medium">
                                {{ $product->category->name }}
                            </span>
                        </div>
                        @endif
                        
                        {{-- Hover Overlay --}}
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center">
                            <a href="{{ route('products.show', $product->slug) }}" 
                               class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-white text-amber-600 px-6 py-3 rounded-lg font-semibold hover:bg-amber-50 transform translate-y-4 group-hover:translate-y-0">
                                View Product
                            </a>
                        </div>
                    </div>
                    
                    {{-- Product Content --}}
                    <div class="p-6">
                        {{-- Brand --}}
                        @if($product->brand)
                            <div class="text-sm text-amber-600 font-medium mb-2">{{ $product->brand }}</div>
                        @endif
                        
                        {{-- Product Title --}}
                        <h3 class="text-xl font-semibold text-gray-900 mb-3 group-hover:text-amber-600 transition-colors duration-300 line-clamp-2">
                            <a href="{{ route('products.show', $product->slug) }}">
                                {{ $product->name }}
                            </a>
                        </h3>
                        
                        {{-- Product Description --}}
                        @if($product->short_description)
                            <p class="text-gray-600 leading-relaxed mb-4 line-clamp-3">
                                {{ $product->short_description }}
                            </p>
                        @endif
                        
                        {{-- Price and Action --}}
                        <div class="flex items-center justify-between">
                            <div class="text-lg font-bold text-amber-600">
                                {!! $product->formatted_price !!}
                            </div>
                            
                            <a href="{{ route('products.show', $product->slug) }}" 
                               class="text-amber-600 hover:text-amber-700 font-medium text-sm">
                                Lihat Detail →
                            </a>
                        </div>
                    </div>
                </div> 
            @endforeach
        </div>
        
        {{-- View Semua Produk Button --}}
        <div class="text-center mt-12">
            <a href="{{ route('products.index') }}" 
               class="inline-flex items-center px-8 py-4 bg-amber-600 text-white font-semibold rounded-xl hover:bg-amber-700 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                View Semua Produk
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
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4 animate-in">What Our Kliens Say</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto animate-in animation-delay-200">
                Don't just take our word for it. Here's what our satisfied clients have to say about our services.
            </p>
        </div>
        
        {{-- Testimonials Container --}}
        <div class="testimonials-container overflow-x-auto pb-6">
            <div class="flex space-x-8 min-w-max">
                @foreach($testimonials as $index => $testimonial)
                    <div class="testimonial-card flex-shrink-0 w-96 bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 animate-in" style="animation-delay: {{ $index * 200 }}ms;">
                        {{-- Rating Stars --}}
                        <div class="flex items-center mb-6">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $i <= ($testimonial->rating ?? 5) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        
                        {{-- Testimonial Content --}}
                        <blockquote class="text-gray-700 mb-6 leading-relaxed italic">
                            "{{ $testimonial->content }}"
                        </blockquote>
                        
                        {{-- Klien Info --}}
                        <div class="flex items-center">
                            @if($testimonial->client_photo)
                                <img 
                                    src="{{ Storage::url($testimonial->client_photo) }}" 
                                    alt="{{ $testimonial->client_name }}"
                                    class="w-12 h-12 rounded-full object-cover mr-4"
                                    loading="lazy"
                                >
                            @else
                                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mr-4">
                                    <span class="text-orange-600 font-semibold text-lg">
                                        {{ substr($testimonial->client_name, 0, 1) }}
                                    </span>
                                </div>
                            @endif
                            
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ $testimonial->client_name }}</h4>
                                @if($testimonial->client_company)
                                    <p class="text-sm text-gray-600">{{ $testimonial->client_company }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- Social Media Integration Section --}}
<section class="py-16 bg-gradient-to-br from-gray-50 via-white to-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-600 rounded-full text-sm font-semibold mb-4">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/>
                </svg>
                Terhubung Dengan Kami
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Ikuti Perkembangan Proyek & Tips Terbaru
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-8">
                Dapatkan inspirasi desain, tips konstruksi, dan update proyek terbaru langsung di media sosial kami. Bergabunglah dengan komunitas {{ $stats['happy_clients'] }}+ klien yang sudah mempercayai kami.
            </p>
        </div>

        {{-- Social Media Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            @if($socialMedia['instagram'] ?? false)
            <div class="group bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Instagram</h3>
                        <p class="text-sm text-gray-500">Portofolio Visual</p>
                    </div>
                </div>
                <p class="text-gray-600 text-sm mb-4">Lihat galeri proyek terbaru dan behind-the-scenes proses konstruksi.</p>
                <a href="{{ $socialMedia['instagram'] }}" target="_blank" 
                   class="inline-flex items-center text-purple-600 font-semibold hover:text-purple-700 transition-colors group-hover:translate-x-1 duration-300">
                    <span>Follow Kami</span>
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>
            @endif

            @if($socialMedia['facebook'] ?? false)
            <div class="group bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Facebook</h3>
                        <p class="text-sm text-gray-500">Komunitas & Update</p>
                    </div>
                </div>
                <p class="text-gray-600 text-sm mb-4">Bergabung dengan diskusi komunitas dan dapatkan tips konstruksi.</p>
                <a href="{{ $socialMedia['facebook'] }}" target="_blank" 
                   class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-700 transition-colors group-hover:translate-x-1 duration-300">
                    <span>Like Page</span>
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>
            @endif

            @if($socialMedia['whatsapp'] ?? false)
            <div class="group bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">WhatsApp</h3>
                        <p class="text-sm text-gray-500">Konsultasi Langsung</p>
                    </div>
                </div>
                <p class="text-gray-600 text-sm mb-4">Chat langsung untuk konsultasi cepat dan tanya jawab seputar proyek.</p>
                <a href="{{ $socialMedia['whatsapp'] }}" target="_blank" 
                   class="inline-flex items-center text-green-600 font-semibold hover:text-green-700 transition-colors group-hover:translate-x-1 duration-300">
                    <span>Chat Sekarang</span>
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>
            @endif

            @if($socialMedia['youtube'] ?? false)
            <div class="group bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-red-600 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">YouTube</h3>
                        <p class="text-sm text-gray-500">Video Tutorial</p>
                    </div>
                </div>
                <p class="text-gray-600 text-sm mb-4">Tonton proses konstruksi dan tutorial perawatan dari para ahli.</p>
                <a href="{{ $socialMedia['youtube'] }}" target="_blank" 
                   class="inline-flex items-center text-red-600 font-semibold hover:text-red-700 transition-colors group-hover:translate-x-1 duration-300">
                    <span>Berlangganan</span>
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>
            @endif
        </div>

        {{-- Bagian Berbagi Sosial --}}
        <div class="bg-gradient-to-r from-orange-500 to-amber-500 rounded-2xl p-8 text-center text-white">
            <h3 class="text-2xl font-bold mb-4">Bagikan Pengalaman Anda</h3>
            <p class="text-orange-100 mb-6 max-w-2xl mx-auto">
                Sudah puas dengan layanan kami? Bantu teman-teman Anda menemukan solusi konstruksi terbaik dengan membagikan website kami.
            </p>
            
            <div class="flex flex-wrap justify-center gap-4">
                <button onclick="shareToFacebook()" 
                        class="flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-300">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    Bagikan ke Facebook
                </button>
                
                <button onclick="shareToWhatsApp()" 
                        class="flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-300">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                    </svg>
                    Bagikan ke WhatsApp
                </button>
                
                <button onclick="copyWebsiteLink()" 
                        class="flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                    Salin Link
                </button>
            </div>
        </div>
    </div>
</section>

{{-- Enhanced Call to Action Section --}}
<section class="py-20 bg-gradient-to-br from-orange-600 via-orange-500 to-amber-600 text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-black bg-opacity-30"></div>
    
    {{-- Background Decoration --}}
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 w-32 h-32 bg-white rounded-full"></div>
        <div class="absolute bottom-10 right-10 w-24 h-24 bg-white rounded-full"></div>
        <div class="absolute top-1/2 right-1/4 w-16 h-16 bg-white rounded-full"></div>
    </div>
    
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 rounded-full text-sm font-semibold mb-6 animate-in">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
            </svg>
            Solusi Terpercaya
        </div>
        
        <h2 class="text-4xl md:text-6xl font-bold mb-6 animate-in">
            Siap Mewujudkan Proyek <br class="hidden md:block">
            <span class="text-yellow-200">Impian Anda?</span>
        </h2>
        
        <p class="text-xl md:text-2xl mb-10 max-w-4xl mx-auto leading-relaxed animate-in animation-delay-200">
            Dapatkan konsultasi <strong>GRATIS</strong> dari tim ahli kami. Kami siap membantu mewujudkan visi Anda dengan layanan profesional yang telah terpercaya oleh <strong>{{ $stats['happy_clients'] }}+ klien</strong> di seluruh Indonesia.
        </p>
        
        {{-- Enhanced CTA Buttons --}}
        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-12 animate-in animation-delay-400">
            <a href="{{ route('contact.index') }}" 
               class="group inline-flex items-center px-8 py-4 bg-white text-orange-600 font-bold rounded-xl hover:bg-yellow-50 transition-all duration-300 transform hover:scale-105 shadow-xl hover:shadow-2xl">
                <svg class="w-5 h-5 mr-2 group-hover:animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z" clip-rule="evenodd"/>
                </svg>
                Minta Penawaran Gratis
                <div class="ml-2 px-2 py-1 bg-red-500 text-white text-xs rounded-full">HOT!</div>
            </a>
            
            <a href="{{ route('contact.index') }}" 
               class="inline-flex items-center px-8 py-4 bg-transparent border-2 border-white text-white font-semibold rounded-xl hover:bg-white hover:text-orange-600 transition-all duration-300 transform hover:scale-105">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Konsultasi Sekarang
            </a>
        </div>
        
        {{-- Trust Indicators --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-3xl mx-auto animate-in animation-delay-600">
            <div class="text-center">
                <div class="flex items-center justify-center w-12 h-12 bg-white bg-opacity-20 rounded-full mx-auto mb-2">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="text-sm font-semibold">Konsultasi Gratis</div>
            </div>
            
            <div class="text-center">
                <div class="flex items-center justify-center w-12 h-12 bg-white bg-opacity-20 rounded-full mx-auto mb-2">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="text-sm font-semibold">Respon 24 Jam</div>
            </div>
            
            <div class="text-center">
                <div class="flex items-center justify-center w-12 h-12 bg-white bg-opacity-20 rounded-full mx-auto mb-2">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="text-sm font-semibold">{{ $stats['happy_clients'] }}+ Klien Puas</div>
            </div>
            
            <div class="text-center">
                <div class="flex items-center justify-center w-12 h-12 bg-white bg-opacity-20 rounded-full mx-auto mb-2">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="text-sm font-semibold">{{ $stats['years_experience'] }} Tahun Berpengalaman</div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
// Hero Slider Functionality
let currentSlide = 0;
const slides = document.querySelectorAll('.hero-slide');
const dots = document.querySelectorAll('.hero-dot');

function switchHeroSlide(index) {
    // Hide current slide
    if (slides[currentSlide]) {
        slides[currentSlide].classList.remove('opacity-100');
        slides[currentSlide].classList.add('opacity-0');
    }
    
    // Perbarui titik
    if (dots[currentSlide]) {
        dots[currentSlide].classList.remove('bg-white');
        dots[currentSlide].classList.add('bg-white', 'bg-opacity-50');
    }
    
    // Show new slide
    currentSlide = index;
    if (slides[currentSlide]) {
        slides[currentSlide].classList.remove('opacity-0');
        slides[currentSlide].classList.add('opacity-100');
    }
    
    // Perbarui titik aktif
    if (dots[currentSlide]) {
        dots[currentSlide].classList.remove('bg-opacity-50');
        dots[currentSlide].classList.add('bg-white');
    }
}

// Auto slide functionality
if (slides.length > 1) {
    setInterval(() => {
        const nextSlide = (currentSlide + 1) % slides.length;
        switchHeroSlide(nextSlide);
    }, 5000);
}

// Intersection Observer for animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
};

// Social Media Sharing Functions
function shareToFacebook() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent('{{ $siteConfig["site_title"] }} - Solusi Konstruksi Terpercaya');
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}&quote=${title}`, '_blank', 'width=626,height=436');
}

function shareToWhatsApp() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent('Saya merekomendasikan {{ $companyProfile->company_name ?? config("app.name") }} untuk kebutuhan konstruksi Anda! Lihat portfolio dan layanan mereka di: ');
    window.open(`https://wa.me/?text=${text}${url}`, '_blank');
}

function copyWebsiteLink() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        // Show success message
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = `
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Link Disalin!
        `;
        
        setTimeout(() => {
            button.innerHTML = originalText;
        }, 2000);
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = window.location.href;
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = `
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Link Disalin!
        `;
        
        setTimeout(() => {
            button.innerHTML = originalText;
        }, 2000);
    });
}

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('animate-in');
        }
    });
}, observerOptions);

// Observe all elements with animation classes
document.addEventListener('DOMContentLoaded', () => {
    const animatedElements = document.querySelectorAll('.animate-fade-in-up, .animate-fade-in-left, .animate-fade-in-right, .animate-in');
    animatedElements.forEach(el => observer.observe(el));
});
</script>
@endpush

@push('styles')

@endpush

</x-layouts.public>