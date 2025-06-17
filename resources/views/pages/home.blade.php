{{-- resources/views/pages/home.blade.php --}}
<x-layouts.public
    title="Home - {{ $siteConfig['site_title'] }}"
    description="{{ $siteConfig['site_description'] }}"
    keywords="{{ $siteConfig['site_keywords'] }}"
    type="website"
>

{{-- Hero Section --}}
<section class="relative min-h-screen flex items-center bg-gradient-to-br from-orange-50 via-white to-amber-50 overflow-hidden">
    {{-- Background Pattern --}}
    <div class="absolute inset-0 bg-[url('/images/grid.svg')] bg-center [mask-image:linear-gradient(180deg,white,rgba(255,255,255,0))]"></div>
    
    {{-- Hero Banners Carousel (if available) --}}
    @if($heroBanners && $heroBanners->count() > 0)
    <div class="absolute inset-0">
        <div class="relative h-full w-full overflow-hidden">
            @foreach($heroBanners as $index => $banner)
            <div class="hero-slide absolute inset-0 transition-opacity duration-1000 {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}"
                 data-slide="{{ $index }}">
                @if($banner->image)
                <img src="{{ asset('storage/' . $banner->image) }}" 
                     alt="{{ $banner->title }}" 
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-transparent"></div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            {{-- Hero Content --}}
            <div class="text-center lg:text-left">
                @if($companyProfile?->tagline)
                <p class="text-orange-600 font-semibold text-lg mb-4 animate-fade-in-up">
                    {{ $companyProfile->tagline }}
                </p>
                @endif
                
                <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6 animate-fade-in-up animation-delay-200">
                    Professional 
                    <span class="bg-gradient-to-r from-orange-600 to-amber-600 bg-clip-text text-transparent">
                        Construction
                    </span>
                    Solutions
                </h1>
                
                <p class="text-xl text-gray-600 mb-8 leading-relaxed animate-fade-in-up animation-delay-400">
                    {{ $companyProfile?->about ?? 'We deliver exceptional construction and engineering services with quality, innovation, and reliability at the forefront of everything we do.' }}
                </p>
                
                {{-- CTA Buttons --}}
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start animate-fade-in-up animation-delay-600">
                    <a href="{{ route('quotation.create') }}" 
                       class="cta-button inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-orange-600 to-amber-600 text-white font-semibold rounded-xl hover:from-orange-700 hover:to-amber-700 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Get Free Quote
                    </a>
                    
                    <a href="{{ route('portfolio.index') }}" 
                       class="inline-flex items-center justify-center px-8 py-4 border-2 border-orange-600 text-orange-600 font-semibold rounded-xl hover:bg-orange-600 hover:text-white transition-all duration-300 transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        View Portfolio
                    </a>
                </div>
                
                {{-- Contact Info --}}
                @if($contactInfo['phone'])
                <div class="mt-8 flex items-center justify-center lg:justify-start text-gray-600 animate-fade-in-up animation-delay-800">
                    <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <span class="font-medium">Call us: {{ $contactInfo['phone'] }}</span>
                </div>
                @endif
            </div>
            
            {{-- Hero Image/Video --}}
            <div class="relative animate-fade-in-left animation-delay-1000">
                <div class="relative rounded-2xl overflow-hidden shadow-2xl transform rotate-3 hover:rotate-0 transition-transform duration-500">
                    @if($featuredProjects && $featuredProjects->first() && $featuredProjects->first()->featured_image)
                    <img src="{{ asset('storage/' . $featuredProjects->first()->featured_image) }}" 
                         alt="Featured Project" 
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
                        <div class="text-2xl font-bold text-orange-600">{{ $stats['completed_projects'] }}+</div>
                        <div class="text-sm text-gray-600">Projects</div>
                    </div>
                </div>
                
                <div class="absolute -top-6 -right-6 bg-white rounded-xl shadow-lg p-4 animate-float animation-delay-500">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">{{ $stats['years_experience'] }}+</div>
                        <div class="text-sm text-gray-600">Years</div>
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
                <div class="text-3xl font-bold text-gray-900 mb-2 counter" data-count="{{ $stats['happy_clients'] }}">0</div>
                <div class="text-gray-600">Happy Clients</div>
            </div>
            
            <div class="text-center group">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-orange-200 transition-colors duration-300">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-2 counter" data-count="{{ $stats['years_experience'] }}">0</div>
                <div class="text-gray-600">Years Experience</div>
            </div>
            
            <div class="text-center group">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-orange-200 transition-colors duration-300">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-2 counter" data-count="{{ $stats['active_services'] }}">0</div>
                <div class="text-gray-600">Services</div>
            </div>
        </div>
    </div>
</section>

{{-- Services Section --}}
@if($featuredServices && $featuredServices->count() > 0)
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Our Services</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                We offer comprehensive construction and engineering services tailored to meet your specific needs and requirements.
            </p>
        </div>
        
        {{-- Services Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($featuredServices as $service)
                <x-public.service-card :service="$service" />
            @endforeach
        </div>
        
        {{-- View All Services Button --}}
        <div class="text-center mt-12">
            <a href="{{ route('services.index') }}" 
               class="inline-flex items-center px-8 py-4 bg-orange-600 text-white font-semibold rounded-xl hover:bg-orange-700 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                View All Services
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>
    </div>
</section>
@endif

{{-- About Section --}}
@if($companyProfile)
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            {{-- Content --}}
            <div>
                <h2 class="text-4xl font-bold text-gray-900 mb-6">
                    Why Choose 
                    <span class="text-orange-600">{{ $companyProfile->company_name ?? config('app.name') }}</span>?
                </h2>
                
                @if($companyProfile->about)
                <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                    {{ $companyProfile->about }}
                </p>
                @endif
                
                {{-- Features/Benefits --}}
                <div class="space-y-4 mb-8">
                    <div class="flex items-start">
                        <div class="w-6 h-6 bg-orange-100 rounded-full flex items-center justify-center mr-4 mt-1">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-1">Professional Quality</h4>
                            <p class="text-gray-600">Delivering exceptional results with attention to detail and quality craftsmanship.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="w-6 h-6 bg-orange-100 rounded-full flex items-center justify-center mr-4 mt-1">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-1">Timely Delivery</h4>
                            <p class="text-gray-600">Committed to completing projects on time and within budget specifications.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="w-6 h-6 bg-orange-100 rounded-full flex items-center justify-center mr-4 mt-1">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-1">Expert Team</h4>
                            <p class="text-gray-600">Experienced professionals with expertise in various construction disciplines.</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('about.index') }}" 
                       class="inline-flex items-center justify-center px-6 py-3 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 transition-colors duration-300">
                        Learn More About Us
                    </a>
                    
                    @if($contactInfo['phone'])
                    <a href="tel:{{ $contactInfo['phone'] }}" 
                       class="inline-flex items-center justify-center px-6 py-3 border border-orange-600 text-orange-600 font-semibold rounded-lg hover:bg-orange-600 hover:text-white transition-all duration-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        Call Now
                    </a>
                    @endif
                </div>
            </div>
            
            {{-- Image --}}
            <div class="relative">
                @if($companyProfile->logo)
                <div class="relative rounded-2xl overflow-hidden shadow-2xl">
                    <img src="{{ $companyProfile->logo_url }}" 
                         alt="{{ $companyProfile->company_name }}" 
                         class="w-full h-96 object-cover">
                </div>
                @else
                <div class="grid grid-cols-2 gap-4">
                    @if($featuredProjects && $featuredProjects->count() >= 2)
                        @foreach($featuredProjects->take(4) as $index => $project)
                        <div class="relative rounded-xl overflow-hidden shadow-lg {{ $index % 2 === 0 ? 'mt-8' : '' }}">
                            @if($project->featured_image)
                            <img src="{{ asset('storage/' . $project->featured_image) }}" 
                                 alt="{{ $project->title }}" 
                                 class="w-full h-48 object-cover hover:scale-110 transition-transform duration-500">
                            @else
                            <div class="w-full h-48 bg-gradient-to-br from-orange-400 to-amber-500"></div>
                            @endif
                        </div>
                        @endforeach
                    @else
                    <div class="col-span-2 relative rounded-2xl overflow-hidden shadow-2xl">
                        <div class="w-full h-96 bg-gradient-to-br from-orange-400 to-amber-500 flex items-center justify-center">
                            <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endif

{{-- Portfolio/Projects Section --}}
@if($featuredProjects && $featuredProjects->count() > 0)
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Featured Projects</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Discover our latest completed projects showcasing our expertise and commitment to excellence.
            </p>
        </div>
        
        {{-- Projects Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($featuredProjects->take(6) as $project)
                <x-public.project-card :project="$project" />
            @endforeach
        </div>
        
        {{-- View All Projects Button --}}
        <div class="text-center mt-12">
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
                Don't just take our word for it. Here's what our satisfied clients have to say about our work.
            </p>
        </div>
        
        {{-- Testimonials Slider --}}
        <div class="relative">
            <div class="testimonials-container overflow-hidden">
                <div class="flex transition-transform duration-500 ease-in-out" id="testimonials-track">
                    @foreach($testimonials as $testimonial)
                        <x-public.testimonial-card :testimonial="$testimonial" />
                    @endforeach
                </div>
            </div>
            
            {{-- Navigation Buttons --}}
            @if($testimonials->count() > 1)
            <button class="absolute left-4 top-1/2 transform -translate-y-1/2 w-12 h-12 bg-white rounded-full shadow-lg flex items-center justify-center text-orange-600 hover:bg-orange-50 transition-colors duration-300" 
                    onclick="prevTestimonial()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            
            <button class="absolute right-4 top-1/2 transform -translate-y-1/2 w-12 h-12 bg-white rounded-full shadow-lg flex items-center justify-center text-orange-600 hover:bg-orange-50 transition-colors duration-300" 
                    onclick="nextTestimonial()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
            @endif
        </div>
    </div>
</section>
@endif

{{-- Call to Action Section --}}
<section class="py-20 bg-gradient-to-r from-orange-600 via-amber-600 to-orange-700 relative overflow-hidden">
    {{-- Background Pattern --}}
    <div class="absolute inset-0 bg-black/10"></div>
    <div class="absolute inset-0 bg-[url('/images/grid.svg')] bg-center opacity-20"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
            Ready to Start Your Project?
        </h2>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('quotation.create') }}" 
               class="inline-flex items-center justify-center px-8 py-4 bg-white text-orange-600 font-semibold rounded-xl hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Get Free Quote
            </a>
            
            <a href="{{ route('contact.index') }}" 
               class="inline-flex items-center justify-center px-8 py-4 border-2 border-white text-white font-semibold rounded-xl hover:bg-white hover:text-orange-600 transition-all duration-300 transform hover:scale-105">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Contact Us
            </a>
        </div>
        
        {{-- Contact Info --}}
        <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8">
            @if($contactInfo['phone'])
            <div class="flex items-center justify-center text-white">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                <span class="font-medium">{{ $contactInfo['phone'] }}</span>
            </div>
            @endif
            
            @if($contactInfo['email'])
            <div class="flex items-center justify-center text-white">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span class="font-medium">{{ $contactInfo['email'] }}</span>
            </div>
            @endif
            
            @if($contactInfo['address'])
            <div class="flex items-center justify-center text-white">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="font-medium">{{ $contactInfo['address'] }}</span>
            </div>
            @endif
        </div>
    </div>
</section>

{{-- WhatsApp Button Component --}}
@if($contactInfo['whatsapp'] ?? $socialMedia['whatsapp'])
<x-public.whatsapp-button 
    :number="$contactInfo['whatsapp'] ?? $socialMedia['whatsapp']"
    :message="'Hello! I would like to inquire about your construction services from ' . ($companyProfile->company_name ?? config('app.name')) . '.'" />
@endif

{{-- Scroll to Top Component --}}
<x-public.scroll-to-top />

{{-- Custom Scripts for Homepage --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Counter Animation
    const counters = document.querySelectorAll('.counter');
    let countersAnimated = false;
    
    const animateCounters = () => {
        if (countersAnimated) return;
        countersAnimated = true;
        
        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-count'));
            const increment = target / 100;
            let current = 0;
            
            const updateCounter = () => {
                if (current < target) {
                    current += increment;
                    counter.textContent = Math.ceil(current);
                    setTimeout(updateCounter, 20);
                } else {
                    counter.textContent = target;
                }
            };
            
            updateCounter();
        });
    };

    // Intersection Observer for counter animation
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !countersAnimated) {
                animateCounters();
            }
        });
    }, observerOptions);

    const statsSection = document.querySelector('.counter')?.closest('section');
    if (statsSection) {
        observer.observe(statsSection);
    }

    // Hero Banner Carousel
    const heroSlides = document.querySelectorAll('.hero-slide');
    if (heroSlides.length > 1) {
        let currentSlide = 0;
        
        const showSlide = (index) => {
            heroSlides.forEach((slide, i) => {
                slide.classList.toggle('opacity-100', i === index);
                slide.classList.toggle('opacity-0', i !== index);
            });
        };
        
        const nextSlide = () => {
            currentSlide = (currentSlide + 1) % heroSlides.length;
            showSlide(currentSlide);
        };
        
        // Auto-advance slides every 5 seconds
        setInterval(nextSlide, 5000);
    }

    // Testimonials Navigation
    let currentTestimonial = 0;
    const testimonialTrack = document.getElementById('testimonials-track');
    const testimonialCount = {{ $testimonials ? $testimonials->count() : 0 }};
    
    window.nextTestimonial = () => {
        if (testimonialCount > 1) {
            currentTestimonial = (currentTestimonial + 1) % testimonialCount;
            updateTestimonialPosition();
        }
    };
    
    window.prevTestimonial = () => {
        if (testimonialCount > 1) {
            currentTestimonial = (currentTestimonial - 1 + testimonialCount) % testimonialCount;
            updateTestimonialPosition();
        }
    };
    
    const updateTestimonialPosition = () => {
        if (testimonialTrack) {
            testimonialTrack.style.transform = `translateX(-${currentTestimonial * 100}%)`;
        }
    };

    // Auto-advance testimonials every 8 seconds
    if (testimonialCount > 1) {
        setInterval(() => {
            window.nextTestimonial();
        }, 8000);
    }

    // Smooth scrolling for anchor links
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

    // Add scroll-triggered animations
    const animateOnScroll = () => {
        const elements = document.querySelectorAll('.animate-fade-in-up, .animate-fade-in-left');
        
        elements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const elementVisible = 150;
            
            if (elementTop < window.innerHeight - elementVisible) {
                element.classList.add('animate-in');
            }
        });
    };

    // Throttle scroll event for better performance
    let ticking = false;
    const handleScroll = () => {
        if (!ticking) {
            requestAnimationFrame(() => {
                animateOnScroll();
                ticking = false;
            });
            ticking = true;
        }
    };

    window.addEventListener('scroll', handleScroll);
    animateOnScroll(); // Check on load

    // Add loading states for images
    const images = document.querySelectorAll('img');
    images.forEach(img => {
        img.addEventListener('load', function() {
            this.classList.add('loaded');
        });
        
        if (img.complete) {
            img.classList.add('loaded');
        }
    });

    // Lazy loading for better performance
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
});
</script>
@endpush

{{-- Custom Styles for Homepage --}}
@push('styles')
<style>
/* Custom animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInLeft {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

.animate-fade-in-up {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.animate-fade-in-left {
    opacity: 0;
    transform: translateX(-30px);
    transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.animate-in {
    opacity: 1 !important;
    transform: translate(0) !important;
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

/* Animation delays */
.animation-delay-200 {
    animation-delay: 200ms;
    transition-delay: 200ms;
}

.animation-delay-400 {
    animation-delay: 400ms;
    transition-delay: 400ms;
}

.animation-delay-500 {
    animation-delay: 500ms;
    transition-delay: 500ms;
}

.animation-delay-600 {
    animation-delay: 600ms;
    transition-delay: 600ms;
}

.animation-delay-800 {
    animation-delay: 800ms;
    transition-delay: 800ms;
}

.animation-delay-1000 {
    animation-delay: 1000ms;
    transition-delay: 1000ms;
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

/* Custom scrollbar for testimonials */
.testimonials-container::-webkit-scrollbar {
    display: none;
}

.testimonials-container {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* Image loading states */
img {
    transition: opacity 0.3s ease;
}

img.lazy {
    opacity: 0;
}

img.loaded {
    opacity: 1;
}

/* Enhanced hover effects */
.group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
}

.group:hover .group-hover\:translate-x-1 {
    transform: translateX(0.25rem);
}

/* CTA Button enhancements */
.cta-button {
    position: relative;
    overflow: hidden;
}

.cta-button::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.cta-button:hover::before {
    left: 100%;
}

/* Responsive improvements */
@media (max-width: 640px) {
    .hero-slide img {
        object-position: center;
    }
    
    .stats-grid {
        grid-template-columns: 1fr 1fr;
    }
    
    .floating-stats {
        position: static;
        margin-top: 1rem;
        transform: none;
    }
}

/* Performance optimizations */
.testimonials-container {
    will-change: transform;
}

.hero-slide {
    will-change: opacity;
}

/* Accessibility improvements */
@media (prefers-reduced-motion: reduce) {
    .animate-fade-in-up,
    .animate-fade-in-left,
    .animate-float {
        animation: none;
        transition: none;
    }
    
    .animate-in {
        opacity: 1;
        transform: none;
    }
}

/* Focus states for accessibility */
.cta-button:focus-visible,
button:focus-visible,
a:focus-visible {
    outline: 2px solid #f97316;
    outline-offset: 2px;
}
</style>
@endpush

</x-layouts.public>