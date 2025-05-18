<!-- resources/views/services/show.blade.php -->
<x-layouts.app>
    @section('title', $service->seo->title ?? $service->title . ' - ' . config('app.name'))
    @section('meta_description', $service->seo->description ?? $service->short_description)
    @if($service->seo && $service->seo->keywords)
        @section('meta_keywords', $service->seo->keywords)
    @endif
    
    <!-- Hero Section -->
    <section class="bg-blue-600 dark:bg-blue-900 py-16 relative overflow-hidden">
        <div class="absolute inset-0 opacity-20 bg-pattern"></div>
        <div class="container mx-auto px-4 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div>
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-6">{{ $service->title }}</h1>
                    
                    @if($service->short_description)
                        <p class="text-lg md:text-xl text-blue-100 mb-8">
                            {{ $service->short_description }}
                        </p>
                    @endif
                    
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('contact') }}?service={{ $service->id }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-blue-700 bg-white hover:bg-blue-50 focus:outline-none">
                            Contact Us
                        </a>
                        <a href="{{ route('quotation.create') }}?service_id={{ $service->id }}" class="inline-flex items-center px-6 py-3 border border-white text-base font-medium rounded-md shadow-sm text-white hover:bg-blue-700 focus:outline-none">
                            Request a Quote
                        </a>
                    </div>
                </div>
                
                <div class="hidden md:block">
                    @if($service->image)
                        <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->title }}" class="w-full h-auto rounded-lg shadow-lg">
                    @elseif($service->icon)
                        <div class="w-full aspect-video bg-white/10 rounded-lg flex items-center justify-center">
                            <img src="{{ asset('storage/' . $service->icon) }}" alt="{{ $service->title }}" class="h-32 w-32">
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
    
    <!-- Mobile Image (Only visible on mobile) -->
    @if($service->image)
        <div class="md:hidden bg-white dark:bg-gray-900 pt-6">
            <div class="container mx-auto px-4">
                <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->title }}" class="w-full h-auto rounded-lg shadow-md">
            </div>
        </div>
    @endif
    
    <!-- Main Content -->
    <section class="bg-white dark:bg-gray-900 py-16">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Service Description -->
                <div class="lg:col-span-2">
                    <div class="prose prose-lg max-w-none dark:prose-invert prose-headings:text-gray-900 dark:prose-headings:text-white prose-img:rounded-lg">
                        {!! $service->description !!}
                    </div>
                    
                    <!-- Contact CTA -->
                    <div class="mt-12 bg-gray-50 dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Interested in this service?</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-6">
                            Contact us today to discuss how our {{ $service->title }} service can benefit your business.
                        </p>
                        <div class="flex flex-wrap gap-4">
                            <a href="{{ route('contact') }}?service={{ $service->id }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none">
                                Contact Us
                            </a>
                            <a href="{{ route('quotation.create') }}?service_id={{ $service->id }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md shadow-sm text-gray-700 dark:text-white bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none">
                                Request a Quote
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div class="space-y-8">
                    <!-- Service Icon -->
                    @if($service->icon)
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="flex justify-center">
                                <img src="{{ asset('storage/' . $service->icon) }}" alt="{{ $service->title }} Icon" class="h-24 w-24">
                            </div>
                        </div>
                    @endif
                    
                    <!-- Category -->
                    @if($service->category)
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Category</h3>
                            <a href="{{ route('services.index', ['category' => $service->category->slug]) }}" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                                {{ $service->category->name }}
                            </a>
                        </div>
                    @endif
                    
                    <!-- Related Services -->
                    @if($relatedServices->count() > 0)
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Related Services</h3>
                            <div class="space-y-4">
                                @foreach($relatedServices as $relatedService)
                                    <div class="flex items-start">
                                        @if($relatedService->icon)
                                            <div class="flex-shrink-0 mr-3">
                                                <img src="{{ asset('storage/' . $relatedService->icon) }}" alt="{{ $relatedService->title }}" class="h-8 w-8">
                                            </div>
                                        @endif
                                        <div>
                                            <a href="{{ route('services.show', $relatedService->slug) }}" class="text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 font-medium">
                                                {{ $relatedService->title }}
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    <!-- Call to Action -->
                    <div class="bg-blue-600 dark:bg-blue-800 p-6 rounded-lg text-white">
                        <h3 class="text-xl font-semibold mb-4">Ready to get started?</h3>
                        <p class="mb-6 text-blue-100">
                            Contact us today to discuss your project requirements and how we can help you achieve your goals.
                        </p>
                        <a href="{{ route('contact') }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-blue-700 bg-white hover:bg-blue-50 focus:outline-none">
                            Contact Us Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Testimonials Section -->
    @if(isset($testimonials) && $testimonials->count() > 0)
        <section class="bg-gray-50 dark:bg-gray-800 py-16">
            <div class="container mx-auto px-4">
                <div class="max-w-3xl mx-auto text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">What Our Clients Say</h2>
                    <p class="text-lg text-gray-600 dark:text-gray-300">
                        Don't just take our word for it. Here's what our clients have to say about our services.
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($testimonials as $testimonial)
                        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center mb-4">
                                @if($testimonial->image)
                                    <img src="{{ asset('storage/' . $testimonial->image) }}" alt="{{ $testimonial->client_name }}" class="h-12 w-12 rounded-full object-cover mr-4">
                                @else
                                    <div class="h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center mr-4">
                                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-white">{{ $testimonial->client_name }}</h4>
                                    @if($testimonial->client_position && $testimonial->client_company)
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $testimonial->client_position }}, {{ $testimonial->client_company }}
                                        </p>
                                    @elseif($testimonial->client_company)
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $testimonial->client_company }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            
                            @if($testimonial->rating)
                                <div class="flex items-center mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $testimonial->rating)
                                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @else
                                            <svg class="h-5 w-5 text-gray-300 dark:text-gray-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @endif
                                    @endfor
                                </div>
                            @endif
                            
                            <blockquote class="text-gray-600 dark:text-gray-300">
                                "{{ $testimonial->content }}"
                            </blockquote>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
    
    <!-- Other Services Section -->
    @if(isset($otherServices) && $otherServices->count() > 0)
        <section class="bg-white dark:bg-gray-900 py-16">
            <div class="container mx-auto px-4">
                <div class="max-w-3xl mx-auto text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Explore Our Other Services</h2>
                    <p class="text-lg text-gray-600 dark:text-gray-300">
                        Discover our full range of services designed to meet your business needs.
                    </p>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($otherServices as $otherService)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden border border-gray-200 dark:border-gray-700 transition hover:shadow-md">
                            <a href="{{ route('services.show', $otherService->slug) }}">
                                @if($otherService->image)
                                    <img src="{{ asset('storage/' . $otherService->image) }}" alt="{{ $otherService->title }}" class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                        @if($otherService->icon)
                                            <img src="{{ asset('storage/' . $otherService->icon) }}" alt="{{ $otherService->title }}" class="h-16 w-16">
                                        @else
                                            <svg class="h-16 w-16 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0 1 12 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2m4 6h.01M5 20h14a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2z" />
                                            </svg>
                                        @endif
                                    </div>
                                @endif
                            </a>
                            
                            <div class="p-5">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                    <a href="{{ route('services.show', $otherService->slug) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                        {{ $otherService->title }}
                                    </a>
                                </h3>
                                
                                @if($otherService->short_description)
                                    <p class="text-gray-600 dark:text-gray-300 mb-4 line-clamp-2">
                                        {{ $otherService->short_description }}
                                    </p>
                                @endif
                                
                                <a href="{{ route('services.show', $otherService->slug) }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                    Learn more
                                    <svg class="ml-1 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layouts.app>