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
                        <div class="text-sm text-gray-600">Clients</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-orange-600">{{ $stats['years_experience'] }}+</div>
                        <div class="text-sm text-gray-600">Years</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-orange-600">{{ $stats['active_services'] }}+</div>
                        <div class="text-sm text-gray-600">Services</div>
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
                Explore Our Services
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
                <div class="text-gray-600 font-medium">Completed Projects</div>
            </div>
            <div class="animate-in animation-delay-200">
                <div class="text-4xl md:text-5xl font-bold text-orange-600 mb-2">{{ $stats['happy_clients'] }}+</div>
                <div class="text-gray-600 font-medium">Happy Clients</div>
            </div>
            <div class="animate-in animation-delay-400">
                <div class="text-4xl md:text-5xl font-bold text-orange-600 mb-2">{{ $stats['years_experience'] }}+</div>
                <div class="text-gray-600 font-medium">Years Experience</div>
            </div>
            <div class="animate-in animation-delay-600">
                <div class="text-4xl md:text-5xl font-bold text-orange-600 mb-2">{{ $stats['active_services'] }}+</div>
                <div class="text-gray-600 font-medium">Services</div>
            </div>
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
                    Learn More About Us
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>
            
            {{-- Image --}}
            <div class="animate-fade-in-right">
                @if($companyProfile->image)
                    <img 
                        src="{{ Storage::url($companyProfile->image) }}" 
                        alt="{{ $companyProfile->company_name }}"
                        class="w-full h-auto rounded-2xl shadow-2xl"
                        loading="lazy"
                    >
                @else
                    <div class="w-full h-80 bg-gradient-to-br from-orange-100 to-amber-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-24 h-24 text-orange-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endif

{{-- Services Section --}}
@if($featuredServices && $featuredServices->count() > 0)
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4 animate-in">Our Services</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto animate-in animation-delay-200">
                Comprehensive solutions tailored to meet your business needs with professional excellence.
            </p>
        </div>
        
        {{-- Services Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($featuredServices as $index => $service)
                <div class="service-card bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 animate-in" style="animation-delay: {{ $index * 200 }}ms;">
                    {{-- Service Icon --}}
                    <div class="w-16 h-16 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        @if($service->icon)
                            <i class="{{ $service->icon }} text-2xl"></i>
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
                        Learn More
                        <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                </div>
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

{{-- Portfolio Section --}}
@if($featuredProjects && $featuredProjects->count() > 0)
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4 animate-in">Our Recent Work</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto animate-in animation-delay-200">
                Explore our portfolio of successful projects that showcase our expertise and commitment to excellence.
            </p>
        </div>
        
        {{-- Projects Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($featuredProjects as $index => $project)
                <div class="project-card group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 animate-in" style="animation-delay: {{ $index * 200 }}ms;">
                    {{-- Project Image --}}
                    <div class="relative overflow-hidden h-64">
                        @if($project->featured_image)
                            <img 
                                src="{{ Storage::url($project->featured_image) }}" 
                                alt="{{ $project->title }}"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                loading="lazy"
                            >
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-orange-100 to-amber-100 flex items-center justify-center">
                                <svg class="w-16 h-16 text-orange-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        @endif
                        
                        {{-- Overlay --}}
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center">
                            <a href="{{ route('portfolio.show', $project->slug) }}" 
                               class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-white text-orange-600 px-6 py-3 rounded-lg font-semibold transform translate-y-4 group-hover:translate-y-0">
                                View Project
                            </a>
                        </div>
                    </div>
                    
                    {{-- Project Content --}}
                    <div class="p-6">
                        @if($project->category)
                            <span class="inline-block px-3 py-1 bg-orange-100 text-orange-600 text-sm font-medium rounded-full mb-3">
                                {{ $project->category->name }}
                            </span>
                        @endif
                        
                        <h3 class="text-xl font-semibold text-gray-900 mb-3 group-hover:text-orange-600 transition-colors duration-300">
                            {{ $project->title }}
                        </h3>
                        
                        <p class="text-gray-600 leading-relaxed">
                            {{ Str::limit($project->description, 100) }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
        
        {{-- View All Projects Button --}}
        <div class="text-center mt-12">
            <a href="{{ route('portfolio.index') }}" 
               class="inline-flex items-center px-8 py-4 border-2 border-orange-600 text-orange-600 font-semibold rounded-xl hover:bg-orange-600 hover:text-white transition-all duration-300">
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
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4 animate-in">What Our Clients Say</h2>
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
                        
                        {{-- Client Info --}}
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

{{-- Call to Action Section --}}
<section class="py-20 bg-gradient-to-br from-orange-600 to-amber-600 text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl md:text-5xl font-bold mb-6 animate-in">
            Ready to Start Your Project?
        </h2>
        <p class="text-xl mb-8 max-w-3xl mx-auto leading-relaxed animate-in animation-delay-200">
            Get in touch with our team today and let's discuss how we can help bring your vision to life with our professional services.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center animate-in animation-delay-400">
            <a href="{{ route('contact.index') }}" 
               class="inline-flex items-center px-8 py-4 bg-white text-orange-600 font-semibold rounded-xl hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                Contact Us Today
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </a>
            
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
    
    // Update dots
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
    
    // Update active dot
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