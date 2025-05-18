<!-- resources/views/services/index.blade.php -->
<x-layouts.app>
    @section('title', 'Our Services - ' . config('app.name'))
    @section('meta_description', 'Explore our comprehensive range of professional services designed to meet your needs.')
    
    <!-- Hero Section -->
    <section class="bg-blue-600 dark:bg-blue-900 py-16 md:py-24">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-6">Our Services</h1>
                <p class="text-lg md:text-xl text-blue-100 mb-8">
                    We offer a comprehensive range of professional services designed to help your business thrive.
                </p>
            </div>
        </div>
    </section>
    
    <!-- Categories Filter -->
    <section class="bg-white dark:bg-gray-900 py-6 border-b border-gray-200 dark:border-gray-800 sticky top-0 z-10">
        <div class="container mx-auto px-4">
            <div class="flex flex-wrap items-center justify-center gap-2 md:gap-4">
                <a href="{{ route('services.index') }}" class="px-4 py-2 rounded-full text-sm font-medium {{ !request('category') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-800 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                    All Services
                </a>
                
                @foreach($categories as $category)
                    <a href="{{ route('services.index', ['category' => $category->slug]) }}" class="px-4 py-2 rounded-full text-sm font-medium {{ request('category') === $category->slug ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-800 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    
    <!-- Services List -->
    <section class="bg-gray-50 dark:bg-gray-900 py-16">
        <div class="container mx-auto px-4">
            @if($services->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 md:gap-8">
                    @foreach($services as $service)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden border border-gray-200 dark:border-gray-700 transition hover:shadow-md">
                            <a href="{{ route('services.show', $service->slug) }}">
                                @if($service->image)
                                    <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->title }}" class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                        @if($service->icon)
                                            <img src="{{ asset('storage/' . $service->icon) }}" alt="{{ $service->title }}" class="h-16 w-16">
                                        @else
                                            <svg class="h-16 w-16 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0 1 12 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2m4 6h.01M5 20h14a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2z" />
                                            </svg>
                                        @endif
                                    </div>
                                @endif
                            </a>
                            
                            <div class="p-5">
                                @if($service->featured)
                                    <span class="inline-block mb-2 px-2.5 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 rounded-full dark:bg-blue-900/30 dark:text-blue-400">
                                        Featured
                                    </span>
                                @endif
                                
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                    <a href="{{ route('services.show', $service->slug) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                        {{ $service->title }}
                                    </a>
                                </h3>
                                
                                @if($service->short_description)
                                    <p class="text-gray-600 dark:text-gray-300 mb-4 line-clamp-3">
                                        {{ $service->short_description }}
                                    </p>
                                @endif
                                
                                <a href="{{ route('services.show', $service->slug) }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                    Learn more
                                    <svg class="ml-1 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            
                <!-- Pagination -->
                @if($services->hasPages())
                    <div class="mt-12">
                        {{ $services->withQueryString()->links('vendor.pagination.tailwind') }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0 1 12 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2m4 6h.01M5 20h14a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2z" />
                    </svg>
                    <h3 class="mt-4 text-xl font-medium text-gray-900 dark:text-white">No services found</h3>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">We couldn't find any services matching your criteria.</p>
                    <div class="mt-6">
                        <a href="{{ route('services.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none">
                            View all services
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="bg-blue-600 dark:bg-blue-900 py-12 md:py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-2xl md:text-3xl font-bold text-white mb-4">Ready to get started?</h2>
                <p class="text-blue-100 mb-8 text-lg">
                    Contact us today to discuss how our services can benefit your business.
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="{{ route('contact') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-blue-700 bg-white hover:bg-blue-50 focus:outline-none">
                        Contact Us
                    </a>
                    <a href="{{ route('quotation.create') }}" class="inline-flex items-center px-6 py-3 border border-white text-base font-medium rounded-md shadow-sm text-white hover:bg-blue-700 focus:outline-none">
                        Request a Quote
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>