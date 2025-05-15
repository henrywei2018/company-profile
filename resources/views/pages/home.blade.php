<!-- resources/views/pages/home.blade.php -->
@extends('layouts.app')

@section('title', 'CV Usaha Prima Lestari - Professional Construction & General Supplier')
@section('meta_description', 'Leading construction and general supplier company in Indonesia providing quality civil engineering, building maintenance, and project management services.')

@section('content')
    <!-- Hero Section -->
    <section class="hero-section relative bg-amber-600 py-24 md:py-32">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div class="text-white" data-aos="fade-right">
                    <h1 class="text-4xl md:text-5xl font-bold mb-6">Building Excellence, Crafting Quality</h1>
                    <p class="text-xl md:text-2xl mb-6">Professional Construction & General Supplier Services</p>
                    <p class="text-white/80 mb-8">CV Usaha Prima Lestari delivers reliable construction solutions and quality supplies for projects of all sizes across Indonesia.</p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('services.index') }}" class="btn btn-white">
                            Our Services
                        </a>
                        <a href="{{ route('contact.index') }}" class="btn btn-outline-white">
                            Get a Quote
                        </a>
                    </div>
                </div>
                <div class="hidden md:block relative" data-aos="fade-left">
                    <img src="{{ asset('images/hero-image.jpg') }}" alt="Construction Site" class="rounded-lg shadow-lg">
                    <div class="absolute -bottom-5 -right-5 bg-white p-6 rounded-lg shadow-lg">
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div>
                                <div class="text-amber-600 text-2xl font-bold">{{ isset($companyProfile) ? $companyProfile->projects_completed : '250+' }}</div>
                                <div class="text-gray-600 text-sm">Projects</div>
                            </div>
                            <div>
                                <div class="text-amber-600 text-2xl font-bold">{{ isset($companyProfile) ? $companyProfile->established_year : '15+' }}</div>
                                <div class="text-gray-600 text-sm">Years Experience</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Services Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h5 class="text-amber-600 font-medium uppercase tracking-wider mb-1" data-aos="fade-up">What We Offer</h5>
                <h2 class="text-3xl font-bold mb-4" data-aos="fade-up" data-aos-delay="100">Our Services</h2>
                <p class="max-w-2xl mx-auto text-gray-600" data-aos="fade-up" data-aos-delay="200">
                    We provide comprehensive solutions for all your construction and supply needs
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($services ?? [] as $service)
                    <div class="bg-gray-50 rounded-lg p-8 shadow-md transition-all duration-300 hover:shadow-lg" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                        @if($service->icon)
                            <img src="{{ asset('storage/' . $service->icon) }}" alt="{{ $service->title }}" class="w-16 h-16 mb-6">
                        @else
                            <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center mb-6">
                                <i class="bi bi-building"></i>
                            </div>
                        @endif
                        <h3 class="text-xl font-semibold mb-3">{{ $service->title }}</h3>
                        <p class="text-gray-600 mb-6">
                            {{ $service->short_description ?? Str::limit(strip_tags($service->description), 150) }}
                        </p>
                        <a href="{{ route('services.show', $service->slug) }}" class="text-amber-600 font-medium hover:text-amber-700 flex items-center">
                            Learn More
                            <i class="bi bi-arrow-right ml-2"></i>
                        </a>
                    </div>
                @empty
                    <!-- Default Services if none in database -->
                    <div class="bg-gray-50 rounded-lg p-8 shadow-md transition-all duration-300 hover:shadow-lg" data-aos="fade-up">
                        <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center mb-6">
                            <i class="bi bi-building"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Construction Services</h3>
                        <p class="text-gray-600 mb-6">
                            Professional construction services for commercial, residential, and industrial projects with focus on quality and timely delivery.
                        </p>
                        <a href="{{ route('services.index') }}" class="text-amber-600 font-medium hover:text-amber-700 flex items-center">
                            Learn More
                            <i class="bi bi-arrow-right ml-2"></i>
                        </a>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-8 shadow-md transition-all duration-300 hover:shadow-lg" data-aos="fade-up" data-aos-delay="100">
                        <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center mb-6">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">General Supplier</h3>
                        <p class="text-gray-600 mb-6">
                            Comprehensive supply solutions for construction materials, equipment, and tools from trusted manufacturers and suppliers.
                        </p>
                        <a href="{{ route('services.index') }}" class="text-amber-600 font-medium hover:text-amber-700 flex items-center">
                            Learn More
                            <i class="bi bi-arrow-right ml-2"></i>
                        </a>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-8 shadow-md transition-all duration-300 hover:shadow-lg" data-aos="fade-up" data-aos-delay="200">
                        <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center mb-6">
                            <i class="bi bi-hammer"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Building Maintenance</h3>
                        <p class="text-gray-600 mb-6">
                            Comprehensive maintenance services to keep your buildings in optimal condition, extending their lifespan and functionality.
                        </p>
                        <a href="{{ route('services.index') }}" class="text-amber-600 font-medium hover:text-amber-700 flex items-center">
                            Learn More
                            <i class="bi bi-arrow-right ml-2"></i>
                        </a>
                    </div>
                @endforelse
            </div>
            
            <div class="text-center mt-12">
                <a href="{{ route('services.index') }}" class="btn btn-primary">View All Services</a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="relative" data-aos="fade-right">
                    <img src="{{ asset('images/about-image.jpg') }}" alt="About Us" class="rounded-lg shadow-lg">
                    <div class="absolute -bottom-5 -left-5 bg-amber-600 p-6 rounded-lg shadow-lg text-white">
                        <p class="text-xl font-bold">
                            {{ isset($companyProfile) ? $companyProfile->established_year : '15+' }} Years of Excellence
                        </p>
                    </div>
                </div>
                <div data-aos="fade-left">
                    <h5 class="text-amber-600 font-medium uppercase tracking-wider mb-1">Who We Are</h5>
                    <h2 class="text-3xl font-bold mb-6">Quality Construction & Supply Services</h2>
                    <p class="text-gray-600 mb-6">
                        CV Usaha Prima Lestari is a leading construction and general supplier company in Indonesia. With years of experience and a commitment to excellence, we have established ourselves as a trusted partner for all construction and supply needs.
                    </p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                        <div class="flex items-start">
                            <div class="mr-4 mt-1 bg-amber-100 text-amber-600 p-2 rounded-full">
                                <i class="bi bi-check2"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold mb-2">Professional Team</h4>
                                <p class="text-gray-600">Experienced professionals dedicated to excellence</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="mr-4 mt-1 bg-amber-100 text-amber-600 p-2 rounded-full">
                                <i class="bi bi-check2"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold mb-2">Quality Materials</h4>
                                <p class="text-gray-600">Only the best materials for lasting results</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="mr-4 mt-1 bg-amber-100 text-amber-600 p-2 rounded-full">
                                <i class="bi bi-check2"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold mb-2">On-time Delivery</h4>
                                <p class="text-gray-600">We respect deadlines and deliver on time</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="mr-4 mt-1 bg-amber-100 text-amber-600 p-2 rounded-full">
                                <i class="bi bi-check2"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold mb-2">Customer Satisfaction</h4>
                                <p class="text-gray-600">Your satisfaction is our top priority</p>
                            </div>
                        </div>
                    </div>
                    
                    <a href="{{ route('about') }}" class="btn btn-primary">Learn More About Us</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Projects Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h5 class="text-amber-600 font-medium uppercase tracking-wider mb-1" data-aos="fade-up">Our Work</h5>
                <h2 class="text-3xl font-bold mb-4" data-aos="fade-up" data-aos-delay="100">Featured Projects</h2>
                <p class="max-w-2xl mx-auto text-gray-600" data-aos="fade-up" data-aos-delay="200">
                    Explore our latest and most notable projects that showcase our expertise and dedication to excellence
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($featuredProjects ?? [] as $project)
                    <div class="group bg-white rounded-lg shadow-md overflow-hidden" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                        <div class="relative overflow-hidden h-64">
                            <img src="{{ $project->getFeaturedImageUrlAttribute() }}" 
                                 alt="{{ $project->title }}" 
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity duration-300">
                                <a href="{{ route('portfolio.show', $project->slug) }}" class="bg-amber-600 text-white py-2 px-6 rounded-md hover:bg-amber-700 transition-all transform translate-y-8 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 duration-300">
                                    View Project
                                </a>
                            </div>
                            @if($project->category)
                                <div class="absolute bottom-4 left-4 bg-amber-600 text-white text-xs py-1 px-3 rounded-full">
                                    {{ $project->category }}
                                </div>
                            @endif
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-2">{{ $project->title }}</h3>
                            <p class="text-gray-600 mb-4">{{ Str::limit(strip_tags($project->description), 100) }}</p>
                            <div class="flex justify-between items-center">
                                @if($project->location)
                                    <span class="text-gray-500 text-sm">
                                        <i class="bi bi-geo-alt mr-1"></i> {{ $project->location }}
                                    </span>
                                @endif
                                <a href="{{ route('portfolio.show', $project->slug) }}" class="text-amber-600 font-medium hover:text-amber-700">
                                    Details <i class="bi bi-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Default Projects if none in database -->
                    @for($i = 1; $i <= 3; $i++)
                        <div class="group bg-white rounded-lg shadow-md overflow-hidden" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                            <div class="relative overflow-hidden h-64">
                                <img src="{{ asset('images/project-' . $i . '.jpg') }}" 
                                     alt="Project {{ $i }}" 
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity duration-300">
                                    <a href="{{ route('portfolio.index') }}" class="bg-amber-600 text-white py-2 px-6 rounded-md hover:bg-amber-700 transition-all transform translate-y-8 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 duration-300">
                                        View Project
                                    </a>
                                </div>
                                <div class="absolute bottom-4 left-4 bg-amber-600 text-white text-xs py-1 px-3 rounded-full">
                                    {{ ['Commercial', 'Residential', 'Industrial'][$i-1] }}
                                </div>
                            </div>
                            <div class="p-6">
                                <h3 class="text-xl font-semibold mb-2">{{ ['Office Building Project', 'Residential Complex', 'Factory Renovation'][$i-1] }}</h3>
                                <p class="text-gray-600 mb-4">{{ ['Modern office building with sustainable features', 'Luxury residential complex with amenities', 'Complete renovation of manufacturing facility'][$i-1] }}</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500 text-sm">
                                        <i class="bi bi-geo-alt mr-1"></i> {{ ['Jakarta', 'Bandung', 'Surabaya'][$i-1] }}
                                    </span>
                                    <a href="{{ route('portfolio.index') }}" class="text-amber-600 font-medium hover:text-amber-700">
                                        Details <i class="bi bi-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endfor
                @endforelse
            </div>
            
            <div class="text-center mt-12">
                <a href="{{ route('portfolio.index') }}" class="btn btn-outline-primary">View All Projects</a>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h5 class="text-amber-600 font-medium uppercase tracking-wider mb-1" data-aos="fade-up">Testimonials</h5>
                <h2 class="text-3xl font-bold mb-4" data-aos="fade-up" data-aos-delay="100">What Our Clients Say</h2>
                <p class="max-w-2xl mx-auto text-gray-600" data-aos="fade-up" data-aos-delay="200">
                    Hear from our satisfied clients about their experience working with CV Usaha Prima Lestari
                </p>
            </div>
            
            <div class="testimonial-slider">
                <div class="swiper-container">
                    <div class="swiper-wrapper">
                        @forelse($testimonials ?? [] as $testimonial)
                            <div class="swiper-slide">
                                <div class="bg-white p-8 rounded-lg shadow-md" data-aos="fade-up">
                                    <div class="flex justify-end mb-4">
                                        <i class="bi bi-quote text-amber-200 text-6xl"></i>
                                    </div>
                                    <p class="text-gray-600 italic mb-6">
                                        "{{ Str::limit($testimonial->content, 200) }}"
                                    </p>
                                    <div class="flex items-center">
                                        @if($testimonial->image)
                                            <img src="{{ asset('storage/' . $testimonial->image) }}" alt="{{ $testimonial->client_name }}" class="w-12 h-12 rounded-full object-cover mr-4">
                                        @else
                                            <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mr-4">
                                                <i class="bi bi-person"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h4 class="font-semibold">{{ $testimonial->client_name }}</h4>
                                            <p class="text-gray-500 text-sm">{{ $testimonial->client_position }}{{ $testimonial->client_company ? ', ' . $testimonial->client_company : '' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <!-- Default Testimonials -->
                            @foreach(['John Smith', 'Sarah Johnson', 'Michael Wong'] as $index => $name)
                                <div class="swiper-slide">
                                    <div class="bg-white p-8 rounded-lg shadow-md" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                                        <div class="flex justify-end mb-4">
                                            <i class="bi bi-quote text-amber-200 text-6xl"></i>
                                        </div>
                                        <p class="text-gray-600 italic mb-6">
                                            "{{ ['Working with CV Usaha Prima Lestari has been an excellent experience. Their team is professional, responsive, and committed to delivering high-quality results. I highly recommend their services.', 
                                            'We are extremely satisfied with the construction services provided by CV Usaha Prima Lestari. They completed our project on time and within budget, exceeding our expectations.', 
                                            'The team at CV Usaha Prima Lestari demonstrated exceptional expertise and attention to detail throughout our project. Their commitment to quality and customer satisfaction is truly commendable.'][$index] }}"
                                        </p>
                                        <div class="flex items-center">
                                            <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mr-4">
                                                <i class="bi bi-person"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-semibold">{{ $name }}</h4>
                                                <p class="text-gray-500 text-sm">{{ ['Project Manager, ABC Corporation', 'Director, XYZ Properties', 'Facilities Manager, Tech Innovations'][$index] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endforelse
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-20 bg-amber-600 text-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div data-aos="fade-right">
                    <h2 class="text-3xl font-bold mb-4">Ready to Start Your Project?</h2>
                    <p class="mb-6">Contact us today for a free consultation and quote. Our team is ready to bring your vision to life.</p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('contact.index') }}" class="btn btn-white">Contact Us</a>
                        <a href="{{ route('quotation.create') }}" class="btn btn-outline-white">Get a Quote</a>
                    </div>
                </div>
                <div class="hidden md:block" data-aos="fade-left">
                    <img src="{{ asset('images/cta-image.jpg') }}" alt="Start Your Project" class="rounded-lg shadow-lg">
                </div>
            </div>
        </div>
    </section>

    <!-- Latest News -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h5 class="text-amber-600 font-medium uppercase tracking-wider mb-1" data-aos="fade-up">Latest News</h5>
                <h2 class="text-3xl font-bold mb-4" data-aos="fade-up" data-aos-delay="100">From Our Blog</h2>
                <p class="max-w-2xl mx-auto text-gray-600" data-aos="fade-up" data-aos-delay="200">
                    Stay updated with our latest news, industry insights, and company updates
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($latestPosts ?? [] as $post)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                        @if($post->featured_image)
                            <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                 alt="{{ $post->title }}" 
                                 class="w-full h-48 object-cover">
                        @endif
                        <div class="p-6">
                            <div class="flex items-center mb-2 text-gray-500 text-sm">
                                <i class="bi bi-calendar mr-2"></i>
                                <span>{{ $post->published_at->format('M d, Y') }}</span>
                            </div>
                            <h3 class="text-xl font-semibold mb-3">{{ $post->title }}</h3>
                            <p class="text-gray-600 mb-4">{{ $post->excerpt ?? Str::limit(strip_tags($post->content), 120) }}</p>
                            <a href="{{ route('blog.show', $post->slug) }}" class="text-amber-600 font-medium hover:text-amber-700 flex items-center">
                                Read More
                                <i class="bi bi-arrow-right ml-2"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <!-- Default Blog Posts -->
                    @for($i = 1; $i <= 3; $i++)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                            <img src="{{ asset('images/blog-' . $i . '.jpg') }}" 
                                 alt="Blog Post {{ $i }}" 
                                 class="w-full h-48 object-cover">
                            <div class="p-6">
                                <div class="flex items-center mb-2 text-gray-500 text-sm">
                                    <i class="bi bi-calendar mr-2"></i>
                                    <span>{{ now()->subDays($i * 5)->format('M d, Y') }}</span>
                                </div>
                                <h3 class="text-xl font-semibold mb-3">{{ ['The Future of Sustainable Construction', 'Top Construction Trends for 2025', 'How to Choose the Right Construction Materials'][$i-1] }}</h3>
                                <p class="text-gray-600 mb-4">{{ ['Exploring eco-friendly building practices and sustainable materials for the future of construction.', 'Stay ahead of the curve with these emerging trends in the construction industry.', 'A comprehensive guide to selecting the best materials for your construction project.'][$i-1] }}</p>
                                <a href="{{ route('blog.index') }}" class="text-amber-600 font-medium hover:text-amber-700 flex items-center">
                                    Read More
                                    <i class="bi bi-arrow-right ml-2"></i>
                                </a>
                            </div>
                        </div>
                    @endfor
                @endforelse
            </div>
            
            <div class="text-center mt-12">
                <a href="{{ route('blog.index') }}" class="btn btn-outline-primary">View All Posts</a>
            </div>
        </div>
    </section>

    <!-- Partners/Clients -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-8">
                <h5 class="text-amber-600 font-medium uppercase tracking-wider mb-1" data-aos="fade-up">Our Partners</h5>
                <h2 class="text-3xl font-bold mb-4" data-aos="fade-up" data-aos-delay="100">Trusted by</h2>
            </div>
            
            <div class="flex flex-wrap justify-center items-center gap-12">
                @for($i = 1; $i <= 6; $i++)
                    <div class="grayscale hover:grayscale-0 transition-all duration-300" data-aos="fade-up" data-aos-delay="{{ $i * 50 }}">
                        <img src="{{ asset('images/client-' . $i . '.png') }}" alt="Client {{ $i }}" class="h-12">
                    </div>
                @endfor
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Swiper
        new Swiper('.swiper-container', {
            slidesPerView: 1,
            spaceBetween: 30,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                // when window width is >= 640px
                640: {
                    slidesPerView: 1,
                },
                // when window width is >= 768px
                768: {
                    slidesPerView: 2,
                },
                // when window width is >= 1024px
                1024: {
                    slidesPerView: 3,
                }
            },
            autoplay: {
                delay: 5000,
            },
        });
        
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
        });
    });
</script>
@endpush