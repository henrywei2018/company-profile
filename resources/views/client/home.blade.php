<!-- resources/views/client/home.blade.php -->
<x-app-layout>
    <!-- Hero Section -->
    <div class="relative">
        <div class="absolute inset-0">
            <div class="w-full h-full">
                <div class="swiper-container w-full h-full">
                    <div class="swiper-wrapper">
                        @foreach($heroSlides as $slide)
                        <div class="swiper-slide">
                            <div class="relative w-full h-full">
                                <img src="{{ asset('storage/' . $slide->image) }}" alt="{{ $slide->title }}" class="absolute inset-0 w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black bg-opacity-50"></div>
                                <div class="absolute inset-0 flex items-center">
                                    <div class="text-center md:text-left max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight">
                                            {{ $slide->title }}
                                        </h1>
                                        <p class="mt-4 text-xl md:text-2xl text-white max-w-3xl">
                                            {{ $slide->subtitle }}
                                        </p>
                                        @if($slide->button_text)
                                        <div class="mt-8">
                                            <a href="{{ $slide->button_url }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                                                {{ $slide->button_text }}
                                            </a>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
            </div>
        </div>
        <div class="relative h-screen"></div>
    </div>

    <!-- About Section -->
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:grid lg:grid-cols-2 lg:gap-8 lg:items-center">
                <div>
                    <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                        About CV Usaha Prima Lestari
                    </h2>
                    <p class="mt-3 max-w-3xl text-lg text-gray-500">
                        {{ isset($companyProfile) ? Str::limit(strip_tags($companyProfile->about), 300) : 'CV Usaha Prima Lestari is a leading construction and general supplier company with years of experience delivering quality services across Indonesia.' }}
                    </p>
                    <div class="mt-8">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-amber-500 text-white">
                                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Quality Assurance</h3>
                                <p class="mt-2 text-base text-gray-500">
                                    We prioritize quality in every project, adhering to industry standards and best practices.
                                </p>
                            </div>
                        </div>
                        <div class="flex mt-6">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-amber-500 text-white">
                                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Expert Team</h3>
                                <p class="mt-2 text-base text-gray-500">
                                    Our team of skilled professionals brings experience and expertise to every project.
                                </p>
                            </div>
                        </div>
                        <div class="flex mt-6">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-amber-500 text-white">
                                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Timely Delivery</h3>
                                <p class="mt-2 text-base text-gray-500">
                                    We are committed to completing projects on schedule and within budget.
                                </p>
                            </div>
                        </div>
                        <div class="mt-8">
                            <a href="{{ route('about') }}" class="text-base font-medium text-amber-600 hover:text-amber-700">
                                Learn more about our company
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="mt-10 lg:mt-0">
                    <div class="relative rounded-lg overflow-hidden">
                        <img src="{{ asset('images/about-company.jpg') }}" alt="About CV Usaha Prima Lestari" class="w-full h-96 object-cover">
                        <div class="absolute inset-0 bg-amber-600 bg-opacity-20"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Section -->
    <div class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    Our Services
                </h2>
                <p class="mt-3 max-w-2xl mx-auto text-xl text-gray-500 sm:mt-4">
                    Comprehensive solutions for construction and supply needs
                </p>
            </div>

            <div class="mt-12 grid gap-8 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                @foreach($featuredServices as $service)
                    <x-service-card :service="$service" />
                @endforeach
            </div>

            <div class="mt-10 text-center">
                <a href="{{ route('services.index') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700">
                    View All Services
                </a>
            </div>
        </div>
    </div>

    <!-- Featured Projects Section -->
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    Featured Projects
                </h2>
                <p class="mt-3 max-w-2xl mx-auto text-xl text-gray-500 sm:mt-4">
                    Explore our showcase of successful projects
                </p>
            </div>

            <div class="mt-12">
                <div class="grid gap-8 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($featuredProjects as $project)
                        <x-project-card :project="$project" />
                    @endforeach
                </div>
            </div>

            <div class="mt-10 text-center">
                <a href="{{ route('portfolio.index') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700">
                    View All Projects
                </a>
            </div>
        </div>
    </div>

    <!-- Testimonials Section -->
    <div class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    What Our Clients Say
                </h2>
                <p class="mt-3 max-w-2xl mx-auto text-xl text-gray-500 sm:mt-4">
                    Trusted by businesses and individuals across Indonesia
                </p>
            </div>

            <div class="mt-12">
                <div class="grid gap-8 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($testimonials as $testimonial)
                        <x-testimonial-card :testimonial="$testimonial" />
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-amber-600">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between">
            <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                <span class="block">Ready to start your project?</span>
                <span class="block text-amber-200">Get in touch with our team today.</span>
            </h2>
            <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0">
                <div class="inline-flex rounded-md shadow">
                    <a href="{{ route('contact.index') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-amber-600 bg-white hover:bg-amber-50">
                        Contact Us
                    </a>
                </div>
                <div class="ml-3 inline-flex rounded-md shadow">
                    <a href="{{ route('quotation.index') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-amber-800 hover:bg-amber-700">
                        Request a Quote
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Partners Section -->
    @if(isset($partners) && $partners->count() > 0)
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-gray-900">Our Partners</h2>
                <p class="mt-3 max-w-2xl mx-auto text-xl text-gray-500">
                    We collaborate with leading companies in the industry
                </p>
            </div>
            <div class="mt-10">
                <div class="flex flex-wrap justify-center items-center gap-8 md:gap-16">
                    @foreach($partners as $partner)
                    <div class="flex justify-center">
                        <img class="h-16 object-contain filter grayscale hover:grayscale-0 transition-all duration-300" 
                             src="{{ asset('storage/' . $partner->logo) }}" 
                             alt="{{ $partner->name }}">
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</x-app-layout>