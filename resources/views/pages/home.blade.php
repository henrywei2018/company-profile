<!-- resources/views/pages/home.blade.php -->
@extends('layouts.app')

@section('title', 'CV Usaha Prima Lestari - Professional Construction & General Supplier')
@section('meta_description', 'Leading construction and general supplier company in Indonesia providing quality civil engineering, building maintenance, and project management services.')

@section('content')
    <!-- Hero Section -->
    <section class="hero-section relative">
        <div class="container mx-auto px-4 py-24 md:py-32">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div class="text-left" data-aos="fade-right">
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-6">Construction solutions<br>for your business.</h1>
                    <p class="text-xl md:text-2xl text-gray-700 dark:text-gray-300 mb-8">Professional Construction & General Supplier Services</p>
                    <a href="{{ route('services.index') }}" class="btn-primary">
                        Explore Services
                    </a>
                </div>
                <div class="hidden md:block relative" data-aos="fade-left">
                    <img src="{{ asset('images/hero-image.jpg') }}" alt="Construction Professional" class="rounded-lg">
                    <div class="absolute -bottom-5 -right-5 bg-amber-600 p-6 rounded-lg shadow-lg text-white">
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div>
                                <div class="text-white text-2xl font-bold">{{ isset($companyProfile) ? $companyProfile->projects_completed : '250+' }}</div>
                                <div class="text-white text-sm">Projects</div>
                            </div>
                            <div>
                                <div class="text-white text-2xl font-bold">{{ isset($companyProfile) ? $companyProfile->established_year : '15+' }}</div>
                                <div class="text-white text-sm">Years Experience</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Service Cards Section -->
    <section class="service-cards py-16 bg-gray-50 dark:bg-gray-800">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-4 md:grid-cols-6 gap-4">
                <!-- Card 1 -->
                <div class="service-card text-center p-8" data-aos="fade-up">
                    <div class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mt-6 mb-4">Get Info</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Learn about our complete range of construction and supplier services tailored to your specific needs.
                    </p>
                </div>
                
                <!-- Card 2 -->
                <div class="service-card text-center p-8" data-aos="fade-up" data-aos-delay="100">
                    <div class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mt-6 mb-4">Make A Plan</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Let us help you design and develop the perfect plan for your construction or supply project.
                    </p>
                </div>
                
                <!-- Card 3 -->
                <div class="service-card text-center p-8" data-aos="fade-up" data-aos-delay="200">
                    <div class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mt-6 mb-4">Boost Your Business</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Elevate your business with our quality construction services and reliable material supplies.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section py-20 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="about-image" data-aos="fade-right">
                    <img src="{{ asset('images/about-image.jpg') }}" alt="About Us" class="rounded-lg shadow-lg">
                </div>
                <div data-aos="fade-left">
                    <h2 class="section-title">About Consulting</h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">
                        CV Usaha Prima Lestari has established itself as a trusted name in construction and supply services. With years of experience in the industry, we've built a reputation for quality, reliability, and excellence in all our projects.
                    </p>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">
                        Our team of experienced professionals is dedicated to delivering exceptional results, whether you're looking for construction services, material supplies, or project management.
                    </p>
                    <a href="{{ route('about') }}" class="btn-secondary">Learn More</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services-section py-20 bg-gray-50 dark:bg-gray-800">
        <div class="container mx-auto px-4">
            <h2 class="section-title text-center mb-12">Our Services</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($services ?? [] as $service)
                    <div class="service-box" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                        <img src="{{ asset('images/service-' . $loop->iteration . '.jpg') }}" alt="{{ $service->title }}" class="w-full h-48 object-cover rounded-t-lg">
                        <div class="p-6 bg-white dark:bg-gray-700 rounded-b-lg">
                            <h3 class="text-xl font-semibold mb-3">{{ $service->title }}</h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                {{ $service->short_description ?? Str::limit(strip_tags($service->description), 100) }}
                            </p>
                            <a href="{{ route('services.show', $service->slug) }}" class="text-link">Learn More</a>
                        </div>
                    </div>
                @empty
                    @for($i = 1; $i <= 3; $i++)
                        <div class="service-box" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                            <img src="{{ asset('images/service-' . $i . '.jpg') }}" alt="Service {{ $i }}" class="w-full h-48 object-cover rounded-t-lg">
                            <div class="p-6 bg-white dark:bg-gray-700 rounded-b-lg">
                                <h3 class="text-xl font-semibold mb-3">{{ ['Construction Services', 'General Supplier', 'Project Management'][$i-1] }}</h3>
                                <p class="text-gray-600 dark:text-gray-300 mb-4">
                                    {{ ['Professional construction services for all your building needs.', 'Quality construction materials and supplies at competitive prices.', 'Expert project management for successful construction projects.'][$i-1] }}
                                </p>
                                <a href="{{ route('services.index') }}" class="text-link">Learn More</a>
                            </div>
                        </div>
                    @endfor
                @endforelse
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section py-20 bg-gray-900 text-white">
        <div class="container mx-auto px-4">
            <h2 class="section-title text-white text-center mb-12">What Our Clients Say</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                @forelse($testimonials ?? [] as $testimonial)
                    <div class="testimonial-card" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 50 }}">
                        <div class="testimonial-quote">"</div>
                        <p class="text-gray-200 mb-6">{{ Str::limit($testimonial->content, 150) }}</p>
                        <div class="flex items-center">
                            @if($testimonial->image)
                                <img src="{{ asset('storage/' . $testimonial->image) }}" alt="{{ $testimonial->client_name }}" class="w-12 h-12 rounded-full object-cover mr-4">
                            @else
                                <div class="w-12 h-12 bg-amber-600 rounded-full flex items-center justify-center mr-4">
                                    <span class="text-white font-bold">{{ substr($testimonial->client_name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div>
                                <h4 class="font-semibold">{{ $testimonial->client_name }}</h4>
                                <p class="text-gray-400 text-sm">{{ $testimonial->client_position }}{{ $testimonial->client_company ? ', ' . $testimonial->client_company : '' }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    @foreach(['John Smith', 'Sarah Johnson', 'Michael Wong', 'Ana Taylor'] as $index => $name)
                        <div class="testimonial-card" data-aos="fade-up" data-aos-delay="{{ $index * 50 }}">
                            <div class="testimonial-quote">"</div>
                            <p class="text-gray-200 mb-6">{{ ['Working with CV Usaha Prima Lestari has been an excellent experience. Their team is professional and delivers quality results.', 
                                'We are extremely satisfied with the construction services provided. They completed our project on time and within budget.', 
                                'The team demonstrated exceptional expertise throughout our project. Their commitment to quality is commendable.',
                                'Their attention to detail and professional approach made our project a success. Highly recommended!'][$index] }}</p>
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-amber-600 rounded-full flex items-center justify-center mr-4">
                                    <span class="text-white font-bold">{{ substr($name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <h4 class="font-semibold">{{ $name }}</h4>
                                    <p class="text-gray-400 text-sm">{{ ['Project Manager, ABC Corp', 'Director, XYZ Properties', 'Facilities Manager, Tech Innovations', 'CEO, Skyline Developers'][$index] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforelse
            </div>
        </div>
    </section>

    <!-- Project Gallery -->
    <section class="project-gallery py-20 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <h2 class="section-title text-center mb-12">Project Gallery</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($featuredProjects ?? [] as $project)
                    <div class="gallery-item" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                        <img src="{{ $project->getFeaturedImageUrlAttribute() }}" alt="{{ $project->title }}" class="w-full h-64 object-cover rounded-lg">
                    </div>
                @empty
                    @for($i = 1; $i <= 3; $i++)
                        <div class="gallery-item" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                            <img src="{{ asset('images/project-' . $i . '.jpg') }}" alt="Project {{ $i }}" class="w-full h-64 object-cover rounded-lg">
                        </div>
                    @endfor
                @endforelse
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section py-20 bg-gray-50 dark:bg-gray-800">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div data-aos="fade-right">
                    <h2 class="section-title">Don't Hesitate to Contact us for any Information</h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">
                        Have questions about our services or want to discuss your project? Get in touch with us today.
                    </p>
                    <div class="contact-info mt-8">
                        <p class="flex items-center text-gray-600 dark:text-gray-300 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            {{ isset($companyProfile) && $companyProfile->phone ? $companyProfile->phone : '+62 21 123 4567' }}
                        </p>
                    </div>
                    <a href="{{ route('contact.index') }}" class="btn-primary mt-6">Send Request</a>
                </div>
                <div class="contact-image" data-aos="fade-left">
                    <img src="{{ asset('images/contact.jpg') }}" alt="Contact Us" class="rounded-lg shadow-lg">
                </div>
            </div>
        </div>
    </section>

    <!-- Blog Section -->
    <section class="blog-section py-20 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <h2 class="section-title text-center mb-4">From Our Blog</h2>
            <p class="text-center text-gray-600 dark:text-gray-300 mb-12 max-w-2xl mx-auto">
                Stay updated with our latest news, industry insights, and company updates
            </p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($latestPosts ?? [] as $post)
                    <div class="blog-card" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                        <a href="{{ route('blog.show', $post->slug) }}">
                            @if($post->featured_image)
                                <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover rounded-t-lg">
                            @else
                                <img src="{{ asset('images/blog-' . $loop->iteration . '.jpg') }}" alt="{{ $post->title }}" class="w-full h-48 object-cover rounded-t-lg">
                            @endif
                        </a>
                        <div class="p-6 bg-white dark:bg-gray-700 rounded-b-lg">
                            <div class="text-gray-500 dark:text-gray-400 text-sm mb-2">
                                {{ $post->published_at->format('M d, Y') }}
                            </div>
                            <h3 class="text-xl font-semibold mb-3">
                                <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-amber-600 transition-colors">{{ $post->title }}</a>
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                {{ $post->excerpt ?? Str::limit(strip_tags($post->content), 100) }}
                            </p>
                            <a href="{{ route('blog.show', $post->slug) }}" class="text-link">Read More</a>
                        </div>
                    </div>
                @empty
                    @for($i = 1; $i <= 3; $i++)
                        <div class="blog-card" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                            <a href="{{ route('blog.index') }}">
                                <img src="{{ asset('images/blog-' . $i . '.jpg') }}" alt="Blog Post {{ $i }}" class="w-full h-48 object-cover rounded-t-lg">
                            </a>
                            <div class="p-6 bg-white dark:bg-gray-700 rounded-b-lg">
                                <div class="text-gray-500 dark:text-gray-400 text-sm mb-2">
                                    {{ now()->subDays($i * 5)->format('M d, Y') }}
                                </div>
                                <h3 class="text-xl font-semibold mb-3">
                                    <a href="{{ route('blog.index') }}" class="hover:text-amber-600 transition-colors">
                                        {{ ['The Future of Sustainable Construction', 'Top Construction Trends for 2023', 'How to Choose the Right Construction Materials'][$i-1] }}
                                    </a>
                                </h3>
                                <p class="text-gray-600 dark:text-gray-300 mb-4">
                                    {{ ['Exploring eco-friendly building practices and sustainable materials for the future of construction.', 'Stay ahead of the curve with these emerging trends in the construction industry.', 'A comprehensive guide to selecting the best materials for your construction project.'][$i-1] }}
                                </p>
                                <a href="{{ route('blog.index') }}" class="text-link">Read More</a>
                            </div>
                        </div>
                    @endfor
                @endforelse
            </div>
        </div>
    </section>

    <!-- Partners Section -->
    <section class="partners-section py-16 bg-gray-50 dark:bg-gray-800">
        <div class="container mx-auto px-4">
            <div class="flex flex-wrap justify-center items-center gap-12">
                @for($i = 1; $i <= 4; $i++)
                    <div class="partner-logo" data-aos="fade-up" data-aos-delay="{{ $i * 50 }}">
                        <img src="{{ asset('images/partner-' . $i . '.png') }}" alt="Partner {{ $i }}" class="h-12 grayscale hover:grayscale-0 transition-all duration-300">
                    </div>
                @endfor
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
    /* Hero Section */
    .hero-section {
        background-color: #f8f9fa;
        position: relative;
    }
    
    /* Service Cards */
    .service-card {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    
    .service-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    }
    
    .icon-wrapper {
        width: 80px;
        height: 80px;
        background-color: rgba(245, 158, 11, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        color: #f59e0b;
    }
    
    /* Section Titles */
    .section-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 1.5rem;
    }
    
    .dark .section-title {
        color: white;
    }
    
    /* Buttons */
    .btn-primary {
        display: inline-block;
        background-color: #f59e0b;
        color: white;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 0.375rem;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        background-color: #d97706;
    }
    
    .btn-secondary {
        display: inline-block;
        background-color: transparent;
        color: #f59e0b;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border: 2px solid #f59e0b;
        border-radius: 0.375rem;
        transition: all 0.3s ease;
    }
    
    .btn-secondary:hover {
        background-color: #f59e0b;
        color: white;
    }
    
    /* Text Link */
    .text-link {
        color: #f59e0b;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        transition: color 0.3s ease;
    }
    
    .text-link:hover {
        color: #d97706;
    }
    
    .text-link:after {
        content: '→';
        margin-left: 0.5rem;
        transition: transform 0.3s ease;
    }
    
    .text-link:hover:after {
        transform: translateX(4px);
    }
    
    /* Testimonial Cards */
    .testimonial-card {
        background-color: #1f2937;
        border-radius: 0.5rem;
        padding: 2rem;
        position: relative;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    }
    
    .testimonial-quote {
        position: absolute;
        top: 10px;
        right: 20px;
        font-size: 4rem;
        line-height: 1;
        font-family: serif;
        color: rgba(245, 158, 11, 0.2);
    }
    
    /* Blog Cards */
    .blog-card {
        transition: transform 0.3s ease;
    }
    
    .blog-card:hover {
        transform: translateY(-10px);
    }
    
    /* Service Box */
    .service-box {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 0.5rem;
        overflow: hidden;
        transition: transform 0.3s ease;
    }
    
    .service-box:hover {
        transform: translateY(-10px);
    }
    
    /* Gallery Item */
    .gallery-item {
        overflow: hidden;
        border-radius: 0.5rem;
    }
    
    .gallery-item img {
        transition: transform 0.5s ease;
    }
    
    .gallery-item:hover img {
        transform: scale(1.05);
    }
    
    /* Partner Logo */
    .partner-logo {
        transition: all 0.3s ease;
    }
    
    .partner-logo:hover {
        transform: scale(1.1);
    }
    
    /* Contact Info */
    .contact-info {
        color: #4b5563;
    }
    
    .dark .contact-info {
        color: #d1d5db;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .section-title {
            font-size: 1.75rem;
        }
        
        .testimonial-card {
            padding: 1.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
        });
    });
</script>
@endpush