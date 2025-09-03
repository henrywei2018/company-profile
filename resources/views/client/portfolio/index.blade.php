<!-- resources/views/client/portfolio/index.blade.php -->
<x-app-layout>
    <!-- Hero Section -->
    <div class="relative bg-amber-600 overflow-hidden">
        <div class="absolute inset-0">
            <img 
                src="{{ asset('images/portfolio-hero-bg.jpg') }}" 
                alt="Proyek Kami" 
                class="w-full h-full object-cover opacity-30"
            >
            <div class="absolute inset-0 bg-amber-600 mix-blend-multiply"></div>
        </div>
        <div class="relative max-w-7xl mx-auto py-24 px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
                Our Projects
            </h1>
            <p class="mt-6 max-w-3xl text-xl text-amber-100">
                Explore our diverse portfolio of construction and general supplier projects. 
                From residential developments to commercial buildings and infrastructure projects, 
                discover how CV Usaha Prima Lestari delivers excellence in every endeavor.
            </p>
        </div>
    </div>

    <!-- Saring Section -->
    <div class="bg-white">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <form action="{{ route('portfolio.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Kategori</label>
                    <select id="category" name="category" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm rounded-md">
                        <option value="">Semua Kategori</option>
                        <option value="residential" {{ request('category') == 'residential' ? 'selected' : '' }}>Residential</option>
                        <option value="commercial" {{ request('category') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                        <option value="industrial" {{ request('category') == 'industrial' ? 'selected' : '' }}>Industrial</option>
                        <option value="infrastructure" {{ request('category') == 'infrastructure' ? 'selected' : '' }}>Infrastructure</option>
                    </select>
                </div>
                <div>
                    <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                    <select id="year" name="year" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm rounded-md">
                        <option value="">All Years</option>
                        @for ($i = date('Y'); $i >= date('Y') - 10; $i--)
                            <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                    <select id="location" name="location" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm rounded-md">
                        <option value="">All Locations</option>
                        @foreach($locations as $location)
                            <option value="{{ $location }}" {{ request('location') == $location ? 'selected' : '' }}>{{ $location }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                        Filter
                    </button>
                    @if(request()->anyFilled(['category', 'year', 'location']))
                        <a href="{{ route('portfolio.index') }}" class="ml-2 text-amber-600 hover:text-amber-800 font-medium py-2 px-4">
                            Hapus
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Project Listing -->
    <div class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($featuredProjects->count() > 0)
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Featured Projects</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($featuredProjects as $project)
                            <x-project-card :project="$project" :featured="$loop->first" class="{{ $loop->first ? 'md:col-span-2' : '' }}" />
                        @endforeach
                    </div>
                </div>
            @endif

            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">All Projects</h2>
                @if($projects->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($projects as $project)
                            <x-project-card :project="$project" />
                        @endforeach
                    </div>
                    <div class="mt-8">
                        {{ $projects->links('components.pagination') }}
                    </div>
                @else
                    <div class="bg-white p-6 rounded-lg shadow-md text-center">
                        <svg class="h-12 w-12 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900">No projects found</h3>
                        <p class="mt-1 text-gray-500">
                            @if(request()->anyFilled(['category', 'year', 'location']))
                                No projects match your filter criteria. Try adjusting your filters or <a href="{{ route('portfolio.index') }}" class="text-amber-600 hover:text-amber-800">view all projects</a>.
                            @else
                                Check back soon for new projects!
                            @endif
                        </p>
                    </div>
                @endif
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
</x-app-layout>