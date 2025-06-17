<x-layouts.public 
    :title="$seoData['title']"
    :description="$seoData['description']"
    :keywords="$seoData['keywords']"
    :breadcrumbs="$seoData['breadcrumbs']"
>

    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('services.index') }}" class="inline-flex items-center gap-x-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Services
            </a>
        </div>

        <div class="grid lg:grid-cols-4 gap-8">
            <!-- Main Content -->
            <article class="lg:col-span-3">
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <!-- Service Image -->
                    @if($service->image_url)
                        <div class="aspect-video overflow-hidden">
                            <img src="{{ $service->image_url }}" alt="{{ $service->title }}" class="w-full h-full object-cover">
                        </div>
                    @endif

                    <div class="p-8">
                        <!-- Header -->
                        <header class="mb-8">
                            <!-- Category & Featured Badge -->
                            <div class="flex items-center gap-2 mb-4">
                                @if($service->category)
                                    <a href="{{ route('services.index', ['category' => $service->category->slug]) }}" 
                                       class="inline-block bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-sm px-3 py-1 rounded-full hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors">
                                        {{ $service->category->name }}
                                    </a>
                                @endif
                                @if($service->featured)
                                    <span class="inline-flex items-center px-3 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 text-sm rounded-full">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        Featured Service
                                    </span>
                                @endif
                            </div>

                            <!-- Title & Icon -->
                            <div class="flex items-start gap-6 mb-6">
                                @if($service->icon_url)
                                    <div class="flex-shrink-0">
                                        <img src="{{ $service->icon_url }}" alt="{{ $service->title }}" class="w-16 h-16 object-contain">
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">{{ $service->title }}</h1>
                                    @if($service->short_description)
                                        <p class="text-xl text-gray-600 dark:text-gray-300 leading-relaxed">{{ $service->short_description }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- CTA Buttons -->
                            <div class="flex flex-wrap gap-4">
                                <a href="{{ route('quotation.create', ['service' => $service->slug]) }}" 
                                   class="inline-flex items-center px-6 py-3 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors font-medium">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Get Quote
                                </a>
                                <a href="{{ route('contact.index') }}" 
                                   class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors font-medium">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                    Contact Us
                                </a>
                            </div>
                        </header>

                        <!-- Service Description -->
                        <div class="prose prose-lg dark:prose-invert max-w-none">
                            {!! $service->description !!}
                        </div>

                        <!-- Service Features/Benefits -->
                        @if($service->category)
                            <div class="mt-12 p-6 bg-gray-50 dark:bg-slate-700 rounded-xl">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Why Choose Our {{ $service->category->name }}?</h3>
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-green-600 mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">Professional Quality</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">Expert craftsmanship and attention to detail</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-green-600 mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">Competitive Pricing</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">Fair and transparent pricing structure</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-green-600 mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">Timely Delivery</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">Projects completed on schedule</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-green-600 mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">Customer Support</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">Ongoing support and maintenance</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Related Services -->
                @if($relatedServices->isNotEmpty())
                    <section class="mt-12">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-8">Related Services</h2>
                        <div class="grid md:grid-cols-2 gap-6">
                            @foreach($relatedServices as $related)
                                <article class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden hover:shadow-md transition-all duration-300 group">
                                    @if($related->image_url)
                                        <div class="aspect-video overflow-hidden">
                                            <img src="{{ $related->image_url }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        </div>
                                    @endif
                                    <div class="p-6">
                                        <div class="flex items-start gap-3 mb-4">
                                            @if($related->icon_url)
                                                <img src="{{ $related->icon_url }}" alt="{{ $related->title }}" class="w-8 h-8 object-contain flex-shrink-0">
                                            @endif
                                            <div class="flex-1">
                                                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                    <a href="{{ route('services.show', $related->slug) }}">{{ $related->title }}</a>
                                                </h3>
                                                @if($related->category)
                                                    <span class="inline-block bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 text-xs px-2 py-1 rounded-full">
                                                        {{ $related->category->name }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        @if($related->short_description)
                                            <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">{{ Str::limit($related->short_description, 100) }}</p>
                                        @endif

                                        <div class="flex items-center justify-between">
                                            <a href="{{ route('services.show', $related->slug) }}" 
                                               class="inline-flex items-center gap-x-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium">
                                                Learn more
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </a>
                                            <a href="{{ route('quotation.create', ['service' => $related->slug]) }}" 
                                               class="inline-flex items-center px-3 py-1 bg-amber-600 text-white text-sm rounded-lg hover:bg-amber-700 transition-colors">
                                                Get Quote
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif
            </article>

            <!-- Sidebar -->
            <aside class="lg:col-span-1 space-y-6">
                <!-- Quick Contact -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white">
                    <h3 class="text-lg font-bold mb-2">Get Started Today</h3>
                    <p class="text-blue-100 text-sm mb-4">Ready to discuss your project? Contact us for a free consultation.</p>
                    <div class="space-y-2">
                        <a href="{{ route('quotation.create', ['service' => $service->slug]) }}" 
                           class="block w-full text-center px-4 py-2 bg-white text-blue-600 rounded-lg hover:bg-gray-100 transition-colors text-sm font-medium">
                            Request Quote
                        </a>
                        <a href="{{ route('contact.index') }}" 
                           class="block w-full text-center px-4 py-2 border border-blue-300 text-white rounded-lg hover:bg-blue-400 transition-colors text-sm font-medium">
                            Contact Us
                        </a>
                    </div>
                </div>

                <!-- Service Categories -->
                @if($categories->isNotEmpty())
                    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Other Services</h3>
                        <div class="space-y-2">
                            @foreach($categories as $category)
                                <a href="{{ route('services.index', ['category' => $category->slug]) }}" 
                                   class="flex items-center justify-between py-2 px-3 rounded-lg text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors {{ $service->category_id == $category->id ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                                    <div class="flex items-center">
                                        @if($category->icon)
                                            <img src="{{ asset('storage/' . $category->icon) }}" alt="{{ $category->name }}" class="w-4 h-4 object-contain mr-2">
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
                                        <a href="{{ route('services.show', $recent->slug) }}">{{ Str::limit($recent->title, 50) }}</a>
                                    </h4>
                                    @if($recent->category)
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $recent->category->name }}</span>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif
            </aside>
        </div>
    </div>
</x-layouts.public>