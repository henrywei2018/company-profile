@extends('layouts.app')

@section('title', 'Professional Construction & General Supplier')
@section('meta_description', 'CV Usaha Prima Lestari - Leading construction and general supplier company in Indonesia providing quality civil engineering, building maintenance, and project management services.')

@section('content')
    <!-- Hero Section -->
    <section class="hero-section relative bg-gradient-to-r from-gray-900 to-gray-800 py-20 md:py-32">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div class="text-white" data-aos="fade-right" data-aos-delay="100">
                    <h1 class="text-4xl md:text-5xl font-bold mb-6">CV Usaha Prima Lestari</h1>
                    <p class="text-xl md:text-2xl mb-4">{{ isset($companyProfile) ? $companyProfile->tagline : 'Building Excellence with Precision and Reliability' }}</p>
                    <p class="text-gray-300 mb-8">Professional construction and general supplier company delivering quality solutions and services across Indonesia.</p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('services.index') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition">
                            Our Services
                        </a>
                        <a href="{{ route('contact.index') }}" class="inline-flex items-center px-6 py-3 border border-white text-base font-medium rounded-md text-white hover:bg-white hover:text-gray-900 transition">
                            Contact Us
                        </a>
                    </div>
                </div>
                <div class="hidden md:block" data-aos="fade-left" data-aos-delay="200">
                    <img src="{{ asset('images/hero-image.jpg') }}" alt="Construction Project" class="rounded-lg shadow-xl">
                </div>
            </div>
        </div>
        
        <!-- Stats Section -->
        <div class="container mx-auto px-4 mt-16">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg py-10 px-6 -mb-32 relative z-10">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="text-center" data-aos="fade-up" data-aos-delay="100">
                        <div class="text-amber-600 text-4xl font-bold mb-2">{{ isset($companyProfile) ? $companyProfile->established_year : '2008' }}</div>
                        <p class="text-gray-600 dark:text-gray-400">Year Established</p>
                    </div>
                    <div class="text-center" data-aos="fade-up" data-aos-delay="200">
                        <div class="text-amber-600 text-4xl font-bold mb-2">{{ isset($companyProfile) ? $companyProfile->projects_completed : '250+' }}</div>
                        <p class="text-gray-600 dark:text-gray-400">Projects Completed</p>
                    </div>
                    <div class="text-center" data-aos="fade-up" data-aos-delay="300">
                        <div class="text-amber-600 text-4xl font-bold mb-2">{{ isset($companyProfile) ? $companyProfile->clients_count : '125+' }}</div>
                        <p class="text-gray-600 dark:text-gray-400">Satisfied Clients</p>
                    </div>
                    <div class="text-center" data-aos="fade-up" data-aos-delay="400">
                        <div class="text-amber-600 text-4xl font-bold mb-2">{{ isset($companyProfile) ? $companyProfile->employees_count : '50+' }}</div>
                        <p class="text-gray-600 dark:text-gray-400">Skilled Professionals</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-32 bg-gray-50 dark:bg-gray-900">
        <div class="container mx-auto px-4 mt-20">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div data-aos="fade-right" data-aos-delay="100">
                    <img src="{{ asset('images/about-image.jpg') }}" alt="Our Company" class="rounded-lg shadow-lg">
                </div>
                <div data-aos="fade-left" data-aos-delay="200">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-1 bg-amber-600 mr-3"></div>
                        <p class="text-amber-600 font-medium uppercase">About Our Company</p>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Committed to Quality and Excellence</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        {{ isset($companyProfile) && $companyProfile->about ? Str::limit(strip_tags($companyProfile->about), 300) : 'CV Usaha Prima Lestari is a leading construction and general supplier company in Indonesia. With years of experience in the industry, we have established ourselves as a reliable partner for all construction and supply needs. Our commitment to quality, timely delivery, and customer satisfaction has made us the preferred choice for clients across the country.' }}
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-gray-700 dark:text-gray-300">Quality Assurance</span>
                        </div>
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-gray-700 dark:text-gray-300">Experienced Team</span>
                        </div>
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-gray-700 dark:text-gray-300">Modern Equipment</span>
                        </div>
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-gray-700 dark:text-gray-300">On-Time Delivery</span>
                        </div>
                    </div>
                    <a href="{{ route('about') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition">
                        Learn More About Us
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-20 bg-white dark:bg-gray-800">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-12 h-1 bg-amber-600 mr-3"></div>
                    <p class="text-amber-600 font-medium uppercase">Our Services</p>
                    <div class="w-12 h-1 bg-amber-600 ml-3"></div>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">What We Offer</h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                    We provide comprehensive solutions for all your construction and supply needs with our expert team and modern equipment.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($services as $service)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow-md p-6 transition-all duration-300 hover:shadow-lg" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                        @if($service->icon)
                            <img src="{{ asset('storage/' . $service->icon) }}" alt="{{ $service->title }}" class="w-16 h-16 mb-6">
                        @else
                            <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900 text-amber-600 dark:text-amber-400 rounded-lg flex items-center justify-center mb-6">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        @endif
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">{{ $service->title }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            {{ $service->short_description ?? Str::limit(strip_tags($service->description), 150) }}
                        </p>
                        <a href="{{ route('services.show', $service->slug) }}" class="inline-flex items-center text-amber-600 dark:text-amber-400 font-medium hover:text-amber-700 dark:hover:text-amber-300">
                            Learn More
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                @empty
                    <div class="col-span-full text-center py-8">
                        <p class="text-gray-600 dark:text-gray-400">Services information will be available soon.</p>
                    </div>
                @endforelse
            </div>
            
            <div class="text-center mt-12">
                <a href="{{ route('services.index') }}" class="inline-flex items-center px-6 py-3 border border-amber-600 text-base font-medium rounded-md text-amber-600 hover:bg-amber-600 hover:text-white transition">
                    View All Services
                </a>
            </div>
        </div>
    </section>

    <!-- Projects Section -->
    <section class="py-20 bg-gray-50 dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-12 h-1 bg-amber-600 mr-3"></div>
                    <p class="text-amber-600 font-medium uppercase">Our Projects</p>
                    <div class="w-12 h-1 bg-amber-600 ml-3"></div>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Featured Projects</h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                    Explore our latest and most notable projects that showcase our expertise and dedication to excellence.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($featuredProjects as $project)
                    <div class="group bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                        <div class="relative overflow-hidden h-60">
                            @if($project->getFeaturedImageUrlAttribute())
                                <img src="{{ $project->getFeaturedImageUrlAttribute() }}" 
                                     alt="{{ $project->title }}" 
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            @else
                                <div class="w-full h-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                <a href="{{ route('portfolio.show', $project->slug) }}" class="px-6 py-3 bg-amber-600 text-white font-medium rounded-md hover:bg-amber-700 transition-all transform translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 duration-300">
                                    View Project
                                </a>
                            </div>
                            
                            @if($project->featured)
                                <div class="absolute top-4 left-4 bg-yellow-500 text-white px-3 py-1 text-xs font-bold rounded-full">
                                    Featured
                                </div>
                            @endif
                            
                            @if($project->category)
                                <div class="absolute bottom-4 left-4 bg-gray-900 bg-opacity-70 text-white px-3 py-1 text-xs font-medium rounded-full">
                                    {{ $project->category }}
                                </div>
                            @endif
                        </div>
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">
                                    {{ $project->title }}
                                </h3>
                                @if($project->year)
                                    <span class="text-sm text-gray-500 dark:text-gray-400 font-medium">{{ $project->year }}</span>
                                @endif
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">
                                {{ Str::limit(strip_tags($project->description), 100) }}
                            </p>
                            <div class="flex justify-between items-center">
                                @if($project->location)
                                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        {{ $project->location }}
                                    </div>
                                @endif
                                <a href="{{ route('portfolio.show', $project->slug) }}" class="text-amber-600 dark:text-amber-400 hover:text-amber-800 dark:hover:text-amber-300 text-sm font-medium transition-colors flex items-center">
                                    Learn More
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-8">
                        <p class="text-gray-600 dark:text-gray-400">Projects information will be available soon.</p>
                    </div>
                @endforelse
            </div>
            
            <div class="text-center mt-12">
                <a href="{{ route('portfolio.index') }}" class="inline-flex items-center px-6 py-3 border border-amber-600 text-base font-medium rounded-md text-amber-600 hover:bg-amber-600 hover:text-white transition">
                    View All Projects
                </a>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-20 bg-white dark:bg-gray-800">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-12 h-1 bg-amber-600 mr-3"></div>
                    <p class="text-amber-600 font-medium uppercase">Testimonials</p>
                    <div class="w-12 h-1 bg-amber-600 ml-3"></div>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">What Our Clients Say</h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                    We take pride in our work and the satisfaction of our clients. Here's what they have to say about working with us.
                </p>
            </div>
            
            <div class="swiper-container testimonials-slider">
                <div class="swiper-wrapper pb-10">
                    @forelse($testimonials as $testimonial)
                        <div class="swiper-slide">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-8 shadow-md" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                                <div class="flex justify-end mb-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-amber-200 dark:text-amber-700" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                                    </svg>
                                </div>
                                <p class="text-gray-700 dark:text-gray-300 italic mb-6">
                                    {{ Str::limit($testimonial->content, 200) }}
                                </p>
                                <div class="flex items-center">
                                    @if($testimonial->image)
                                        <img src="{{ asset('storage/' . $testimonial->image) }}" alt="{{ $testimonial->client_name }}" class="w-12 h-12 rounded-full object-cover mr-4">
                                    @endif
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $testimonial->client_name }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $testimonial->client_position }}
                                            @if($testimonial->client_company)
                                                , {{ $testimonial->client_company }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="swiper-slide">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-8 shadow-md">
                                <p class="text-gray-700 dark:text-gray-300 italic mb-6">
                                    "CV Usaha Prima Lestari has consistently delivered high-quality construction projects. Their attention to detail and commitment to timelines is commendable. We look forward to working with them on future projects."
                                </p>
                                <div class="flex items-center">
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">John Doe</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Project Manager, ABC Company</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>

    <!-- Latest News Section -->
    <section class="py-20 bg-gray-50 dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-12 h-1 bg-amber-600 mr-3"></div>
                    <p class="text-amber-600 font-medium uppercase">Latest News</p>
                    <div class="w-12 h-1 bg-amber-600 ml-3"></div>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Recent Updates</h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                    Stay updated with our latest news, industry insights, and company updates.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($latestPosts as $post)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                        @if($post->featured_image)
                            <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover">
                        @endif
                        <div class="p-6">
                            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $post->published_at->format('M d, Y') }}
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                                {{ $post->title }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                {{ $post->excerpt ?? Str::limit(strip_tags($post->content), 120) }}
                            </p>
                            <a href="{{ route('blog.show', $post->slug) }}" class="text-amber-600 dark:text-amber-400 hover:text-amber-800 dark:hover:text-amber-300 font-medium inline-flex items-center">
                                Read More
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-8">
                        <p class="text-gray-600 dark:text-gray-400">Blog posts will be available soon.</p>
                    </div>
                @endforelse
            </div>
            
            <div class="text-center mt-12">
                <a href="{{ route('blog.index') }}" class="inline-flex items-center px-6 py-3 border border-amber-600 text-base font-medium rounded-md text-amber-600 hover:bg-amber-600 hover:text-white transition">
                    View All News
                </a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-amber-600 to-amber-700 text-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div data-aos="fade-right" data-aos-delay="100">
                    <h2 class="text-3xl font-bold mb-4">Ready to Start Your Project?</h2>
                    <p class="text-lg text-amber-100 mb-8">
                        Contact us today for a free consultation and estimate for your construction or supplier needs. Our team is ready to help you bring your vision to life.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('quotation.create') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-amber-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-amber-600 focus:ring-white transition">
                            Get a Free Quote
                        </a>
                        <a href="{{ route('contact.index') }}" class="inline-flex items-center px-6 py-3 border border-white text-base font-medium rounded-md text-white hover:bg-white hover:text-amber-700 transition">
                            Contact Us
                        </a>
                    </div>
                </div>
                <div class="hidden md:block" data-aos="fade-left" data-aos-delay="200">
                    <img src="{{ asset('images/cta-image.jpg') }}" alt="Construction Site" class="rounded-lg shadow-lg">
                </div>
            </div>
        </div>
    </section>

    <!-- Clients/Partners Section -->
    <section class="py-16 bg-white dark:bg-gray-800">
        <div class="container mx-auto px-4">
            <div class="text-center mb-10">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Our Trusted Partners</h2>
                <p class="text-gray-600 dark:text-gray-400">We collaborate with leading companies and organizations across Indonesia.</p>
            </div>
            
            <div class="flex flex-wrap justify-center items-center gap-8 md:gap-16">
                <!-- Partner logos would go here -->
                <div class="grayscale opacity-70 hover:grayscale-0 hover:opacity-100 transition">
                    <img src="{{ asset('images/partners/partner-1.png') }}" alt="Partner 1" class="h-12">
                </div>
                <div class="grayscale opacity-70 hover:grayscale-0 hover:opacity-100 transition">
                    <img src="{{ asset('images/partners/partner-2.png') }}" alt="Partner 2" class="h-12">
                </div>
                <div class="grayscale opacity-70 hover:grayscale-0 hover:opacity-100 transition">
                    <img src="{{ asset('images/partners/partner-3.png') }}" alt="Partner 3" class="h-12">
                </div>
                <div class="grayscale opacity-70 hover:grayscale-0 hover:opacity-100 transition">
                    <img src="{{ asset('images/partners/partner-4.png') }}" alt="Partner 4" class="h-12">
                </div>
                <div class="grayscale opacity-70 hover:grayscale-0 hover:opacity-100 transition">
                    <img src="{{ asset('images/partners/partner-5.png') }}" alt="Partner 5" class="h-12">
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Swiper slider
        new Swiper('.testimonials-slider', {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                640: {
                    slidesPerView: 1,
                },
                768: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 3,
                },
            },
            autoplay: {
                delay: 5000,
            },
        });
        
        // Initialize AOS (Animate on Scroll)
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
        });
    });
</script>
@endpush