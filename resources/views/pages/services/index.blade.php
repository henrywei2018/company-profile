<x-layouts.public 
    :title="$seoData['title']"
    :description="$seoData['description']"
    :keywords="$seoData['keywords']"
    :breadcrumbs="$seoData['breadcrumbs']"
>

    <!-- Hero Section -->
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <div class="max-w-2xl mx-auto text-center mb-10 lg:mb-14">
            <h1 class="text-3xl font-bold md:text-4xl md:leading-tight dark:text-white">Our Services</h1>
            <p class="mt-3 text-gray-800 dark:text-gray-200">
                Comprehensive construction services and solutions for all your building needs
            </p>
        </div>

        <div class="grid lg:grid-cols-4 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-3">
                <!-- Filters & Search -->
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6 mb-8">
                    <form method="GET" action="{{ route('services.index') }}" class="grid md:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div>
                            <input 
                                type="text" 
                                name="search" 
                                placeholder="Search services..."
                                value="{{ request('search') }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white"
                            >
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <select name="category" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                        {{ $category->name }} ({{ $category->services_count }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sort -->
                        <div>
                            <select name="sort" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white">
                                <option value="default" {{ request('sort') == 'default' ? 'selected' : '' }}>Default Order</option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                            </select>
                        </div>

                        <!-- Submit & Featured Filter -->
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                Filter
                            </button>
                            <label class="flex items-center">
                                <input type="checkbox" name="featured" value="1" {{ request('featured') == '1' ? 'checked' : '' }} class="mr-2">
                                <span class="text-sm dark:text-gray-300">Featured</span>
                            </label>
                        </div>
                    </form>

                    <!-- Active Filters -->
                    @if(request()->hasAny(['search', 'category', 'featured', 'sort']))
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-slate-600">
                            <div class="flex flex-wrap gap-2 items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Active filters:</span>
                                
                                @if(request('search'))
                                    <span class="inline-flex items-center px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-sm rounded-full">
                                        Search: "{{ request('search') }}"
                                        <a href="{{ request()->fullUrlWithoutQuery('search') }}" class="ml-2 hover:text-blue-600">×</a>
                                    </span>
                                @endif

                                @if(request('category'))
                                    <span class="inline-flex items-center px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-sm rounded-full">
                                        Category: {{ $categories->where('slug', request('category'))->first()?->name }}
                                        <a href="{{ request()->fullUrlWithoutQuery('category') }}" class="ml-2 hover:text-green-600">×</a>
                                    </span>
                                @endif

                                @if(request('featured'))
                                    <span class="inline-flex items-center px-3 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 text-sm rounded-full">
                                        Featured Only
                                        <a href="{{ request()->fullUrlWithoutQuery('featured') }}" class="ml-2 hover:text-yellow-600">×</a>
                                    </span>
                                @endif

                                <a href="{{ route('services.index') }}" class="text-sm text-red-600 dark:text-red-400 hover:underline">Clear all</a>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Services Results -->
                @if($services->isNotEmpty())
                    <!-- Results Info -->
                    <div class="flex justify-between items-center mb-6">
                        <p class="text-gray-600 dark:text-gray-400">
                            Showing {{ $services->firstItem() }}-{{ $services->lastItem() }} of {{ $services->total() }} services
                        </p>
                    </div>

                    <!-- Services Grid -->
                    <div class="grid md:grid-cols-2 gap-6 mb-8">
                        @foreach($services as $service)
                            <article class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden hover:shadow-md transition-all duration-300 group">
                                @if($service->image_url)
                                    <div class="aspect-video overflow-hidden">
                                        <img 
                                            src="{{ $service->image_url }}" 
                                            alt="{{ $service->title }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                        >
                                    </div>
                                @endif
                                
                                <div class="p-6">
                                    <!-- Service Header -->
                                    <div class="flex items-start gap-4 mb-4">
                                        @if($service->icon_url)
                                            <div class="flex-shrink-0">
                                                <img src="{{ $service->icon_url }}" alt="{{ $service->title }}" class="w-12 h-12 object-contain">
                                            </div>
                                        @endif
                                        <div class="flex-1">
                                            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                <a href="{{ route('services.show', $service->slug) }}">{{ $service->title }}</a>
                                            </h2>
                                            @if($service->category)
                                                <span class="inline-block bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs px-2 py-1 rounded-full">
                                                    {{ $service->category->name }}
                                                </span>
                                            @endif
                                            @if($service->featured)
                                                <span class="inline-block bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 text-xs px-2 py-1 rounded-full ml-2">
                                                    <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                    Featured
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    @if($service->short_description)
                                        <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 line-clamp-3">{{ $service->short_description }}</p>
                                    @endif

                                    <!-- Actions -->
                                    <div class="flex items-center justify-between">
                                        <a href="{{ route('services.show', $service->slug) }}" 
                                           class="inline-flex items-center gap-x-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium">
                                            Learn more
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('quotation.create', ['service' => $service->slug]) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-amber-600 text-white text-sm rounded-lg hover:bg-amber-700 transition-colors">
                                            Get Quote
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($services->hasPages())
                        <div class="flex justify-center">
                            {{ $services->links() }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-16">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No services found</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-6">
                            @if(request()->hasAny(['search', 'category', 'featured']))
                                Try adjusting your filters or search terms.
                            @else
                                We're currently updating our services. Please check back soon.
                            @endif
                        </p>
                        @if(request()->hasAny(['search', 'category', 'featured']))
                            <a href="{{ route('services.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Clear Filters
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <aside class="lg:col-span-1 space-y-6">
                <!-- Featured Services -->
                @if($featuredServices->isNotEmpty())
                    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Featured Services</h3>
                        <div class="space-y-4">
                            @foreach($featuredServices as $featured)
                                <article class="group">
                                    <div class="flex items-start gap-3">
                                        @if($featured->icon_url)
                                            <img src="{{ $featured->icon_url }}" alt="{{ $featured->title }}" class="w-8 h-8 object-contain flex-shrink-0">
                                        @endif
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors mb-1">
                                                <a href="{{ route('services.show', $featured->slug) }}">{{ Str::limit($featured->title, 50) }}</a>
                                            </h4>
                                            @if($featured->category)
                                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $featured->category->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Categories -->
                @if($categories->isNotEmpty())
                    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Service Categories</h3>
                        <div class="space-y-2">
                            @foreach($categories as $category)
                                <a href="{{ route('services.index', ['category' => $category->slug]) }}" 
                                   class="flex items-center justify-between py-2 px-3 rounded-lg text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors {{ request('category') == $category->slug ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                                    <div class="flex items-center">
                                        @if($category->icon)
                                            <img src="{{ asset('storage/' . $category->icon) }}" alt="{{ $category->name }}" class="w-5 h-5 object-contain mr-2">
                                        @endif
                                        <span>{{ $category->name }}</span>
                                    </div>
                                    <span class="bg-gray-200 dark:bg-slate-600 text-gray-600 dark:text-gray-300 text-xs px-2 py-1 rounded-full">
                                        {{ $category->services_count }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Recent Services -->
                @if($recentServices->isNotEmpty())
                    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Latest Services</h3>
                        <div class="space-y-4">
                            @foreach($recentServices as $recent)
                                <article class="group">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors mb-1">
                                        <a href="{{ route('services.show', $recent->slug) }}">{{ Str::limit($recent->title, 60) }}</a>
                                    </h4>
                                    @if($recent->category)
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $recent->category->name }}</span>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- CTA -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white">
                    <h3 class="text-lg font-bold mb-2">Need a Custom Solution?</h3>
                    <p class="text-blue-100 text-sm mb-4">Contact us for personalized service recommendations and quotes.</p>
                    <a href="{{ route('contact.index') }}" class="inline-flex items-center px-4 py-2 bg-white text-blue-600 rounded-lg hover:bg-gray-100 transition-colors text-sm font-medium">
                        Contact Us
                    </a>
                </div>
            </aside>
        </div>
    </div>
</x-layouts.public>