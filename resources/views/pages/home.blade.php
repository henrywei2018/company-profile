<!-- resources/views/pages/home.blade.php -->
<x-app-layout>
    <!-- Hero Section -->
    <div class="relative bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">CV Usaha Prima Lestari</h1>
            <p class="text-xl md:text-2xl max-w-3xl mb-8">{{ $companyProfile->tagline }}</p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('services.index') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 transition">
                    Our Services
                </a>
                <a href="{{ route('contact.index') }}" class="inline-flex items-center px-6 py-3 border border-white text-base font-medium rounded-md text-white hover:bg-white hover:text-gray-900 transition">
                    Contact Us
                </a>
            </div>
        </div>
    </div>

    <!-- Featured Projects -->
    <div class="bg-gray-100 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900">Featured Projects</h2>
                <p class="mt-4 text-lg text-gray-600">Explore some of our latest construction and supplier projects</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($featuredProjects as $project)
                    <x-project-card :project="$project" />
                @endforeach
            </div>
            
            <div class="mt-12 text-center">
                <a href="{{ route('portfolio.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    View All Projects
                </a>
            </div>
        </div>
    </div>

    <!-- Our Services -->
    <div class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900">Our Services</h2>
                <p class="mt-4 text-lg text-gray-600">Professional solutions for construction and general supplier needs</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($services as $service)
                    <x-service-card :service="$service" />
                @endforeach
            </div>
            
            <div class="mt-12 text-center">
                <a href="{{ route('services.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    View All Services
                </a>
            </div>
        </div>
    </div>

    <!-- Testimonials -->
    <div class="bg-gray-100 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900">What Our Clients Say</h2>
                <p class="mt-4 text-lg text-gray-600">Trusted by businesses across Indonesia</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach($testimonials as $testimonial)
                    <x-testimonial-card :testimonial="$testimonial" />
                @endforeach
            </div>
        </div>
    </div>

    <!-- Latest News -->
    <div class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900">Latest News</h2>
                <p class="mt-4 text-lg text-gray-600">Stay updated with our latest articles and insights</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($latestPosts as $post)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        @if($post->featured_image)
                            <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover">
                        @endif
                        <div class="p-6">
                            <p class="text-sm text-gray-500 mb-2">{{ $post->published_at->format('M d, Y') }}</p>
                            <h3 class="text-xl font-semibold mb-3">{{ $post->title }}</h3>
                            <p class="text-gray-600 mb-4">{{ Str::limit(strip_tags($post->excerpt ?? $post->content), 120) }}</p>
                            <a href="{{ route('blog.show', $post->slug) }}" class="text-blue-600 hover:text-blue-800 font-medium">Read More →</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="bg-blue-700 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold mb-6">Ready to Start Your Project?</h2>
            <p class="text-xl max-w-3xl mx-auto mb-8">Contact us today for a free consultation and estimate for your construction or supplier needs.</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('quotation.index') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-gray-900 bg-white hover:bg-gray-100 transition">
                    Request a Quote
                </a>
                <a href="{{ route('contact.index') }}" class="inline-flex items-center px-6 py-3 border border-white text-base font-medium rounded-md text-white hover:bg-white hover:text-gray-900 transition">
                    Contact Us
                </a>
            </div>
        </div>
    </div>
</x-app-layout>