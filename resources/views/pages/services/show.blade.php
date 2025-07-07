{{-- resources/views/pages/services/show.blade.php --}}
<x-layouts.public
    :title="$service->title . ' - ' . $siteConfig['site_title']"
    :description="$service->short_description ?: 'Professional ' . $service->title . ' services with quality and expertise.'"
    :keywords="$service->title . ', construction, engineering, professional services'"
    type="article"
>

{{-- Hero Section --}}
<section class="relative pt-32 pb-20 bg-gradient-to-br from-orange-50 via-white to-amber-50 overflow-hidden">
    {{-- Background Pattern --}}
    <div class="absolute inset-0 bg-[url('/images/grid.svg')] bg-center [mask-image:linear-gradient(180deg,white,rgba(255,255,255,0))]"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10">
        {{-- Breadcrumbs --}}
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-orange-600 inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('services.index') }}" class="ml-1 text-gray-700 hover:text-orange-600 md:ml-2">Services</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-orange-600 md:ml-2 font-medium">{{ $service->title }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            {{-- Service Content --}}
            <div>
                @if($service->category)
                <div class="flex items-center mb-4">
                    <span class="bg-orange-100 text-orange-600 px-3 py-1 rounded-full text-sm font-medium">
                        {{ $service->category->name }}
                    </span>
                    @if($service->featured)
                    <span class="bg-amber-100 text-amber-600 px-3 py-1 rounded-full text-sm font-medium ml-2">
                        Featured Service
                    </span>
                    @endif
                </div>
                @endif
                
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                    {{ $service->title }}
                </h1>
                
                @if($service->short_description)
                <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                    {{ $service->short_description }}
                </p>
                @endif
                
                {{-- Service Stats --}}
                <div class="flex flex-wrap gap-6 mb-8">
                    @if(isset($serviceProjects) && $serviceProjects->count() > 0)
                    <div class="flex items-center text-gray-600">
                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">{{ $serviceProjects->count() }}+ Projects</div>
                            <div class="text-sm text-gray-500">Successfully Completed</div>
                        </div>
                    </div>
                    @endif
                    
                    @if($service->base_price)
                    <div class="flex items-center text-gray-600">
                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">From ${{ number_format($service->base_price) }}</div>
                            <div class="text-sm text-gray-500">Starting Price</div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="flex items-center text-gray-600">
                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">Quality Guaranteed</div>
                            <div class="text-sm text-gray-500">Professional Standards</div>
                        </div>
                    </div>
                </div>
                
                {{-- CTA Buttons --}}
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('contact.index', ['service' => $service->slug]) }}" 
                       class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-orange-600 to-amber-600 text-white font-semibold rounded-xl hover:from-orange-700 hover:to-amber-700 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.959 8.959 0 01-4.906-1.405L3 21l2.595-5.094A8.959 8.959 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"/>
                        </svg>
                        Get Free Quote
                    </a>
                    @if($contactInfo['phone'])
                    <a href="tel:{{ $contactInfo['phone'] }}" 
                       class="inline-flex items-center justify-center px-8 py-4 border-2 border-orange-600 text-orange-600 font-semibold rounded-xl hover:bg-orange-600 hover:text-white transition-all duration-300">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        Call Now
                    </a>
                    @endif
                </div>
            </div>
            
            {{-- Service Image --}}
            <div class="relative">
                <div class="relative rounded-2xl overflow-hidden shadow-2xl transform hover:scale-105 transition-transform duration-500">
                    @if($service->image)
                        <img src="{{ asset('storage/' . $service->image) }}" 
                             alt="{{ $service->title }}" 
                             class="w-full h-96 object-cover">
                    @else
                        <div class="w-full h-96 bg-gradient-to-br from-orange-400 to-amber-500 flex items-center justify-center">
                            <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Service Details Section --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            {{-- Main Content --}}
            <div class="lg:col-span-2">
                {{-- Service Description --}}
                @if($service->description)
                <div class="prose prose-lg max-w-none mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">Service Overview</h2>
                    <div class="text-gray-700 leading-relaxed">
                        {!! nl2br(e($service->description)) !!}
                    </div>
                </div>
                @endif
                
                {{-- Service Features --}}
                @if($features && count($features) > 0)
                <div class="mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-8">Key Features & Benefits</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($features as $feature)
                        <div class="flex items-start p-6 bg-gray-50 rounded-xl hover:bg-orange-300 transition-colors duration-300">
                            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @switch($feature['icon'] ?? 'check-circle')
                                        @case('clock')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            @break
                                        @case('shield-check')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                            @break
                                        @case('users')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                            @break
                                        @case('tools')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                            @break
                                        @case('dollar-sign')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                            @break
                                        @default
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    @endswitch
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">{{ $feature['title'] }}</h3>
                                <p class="text-gray-600">{{ $feature['description'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                
            </div>
            
            {{-- Sidebar --}}
            <div class="lg:col-span-1">
                {{-- Contact Information --}}
                <div class="bg-white border border-gray-200 rounded-2xl p-6 mb-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Contact Information</h3>
                    <div class="space-y-4">
                        @if($contactInfo['phone'])
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <a href="tel:{{ $contactInfo['phone'] }}" class="text-gray-700 hover:text-orange-600">
                                {{ $contactInfo['phone'] }}
                            </a>
                        </div>
                        @endif
                        
                        @if($contactInfo['email'])
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 7.89a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <a href="mailto:{{ $contactInfo['email'] }}" class="text-gray-700 hover:text-orange-600">
                                {{ $contactInfo['email'] }}
                            </a>
                        </div>
                        @endif
                        
                        @if($contactInfo['address'])
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-orange-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="text-gray-700">{{ $contactInfo['address'] }}</span>
                        </div>
                        @endif
                    </div>
                    
                </div>
                {{-- Service Process --}}
                @if($processSteps && count($processSteps) > 0)
                <div class="bg-white border border-gray-200 rounded-2xl p-4 mb-4">
                    <h2 class="text-lg font-bold text-gray-900 mb-2">Our Process</h2>
                    <div class="space-y-2">
                        @foreach($processSteps as $index => $step)
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-amber-500 text-white rounded-full flex items-center justify-center font-bold text-m mr-2 flex-shrink-0">
                                {{ $step['step'] }}
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $step['title'] }}</h3>
                                <p class="text-gray-600 leading-relaxed">{{ $step['description'] }}</p>
                            </div>
                        </div>
                        @if(!$loop->last)
                        <div class="ml-6 w-0.5 h-8 bg-gradient-to-b from-orange-300 to-transparent"></div>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endif
                {{-- Service Benefits --}}
                <div class="bg-gray-100 rounded-2xl p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Why Choose Us?</h3>
                    <ul class="space-y-3">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700">Licensed & Insured</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700">Free Consultations</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700">24/7 Emergency Support</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700">Quality Guarantee</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700">Competitive Pricing</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- FAQ Section --}}
@if($faqs && count($faqs) > 0)
<section class="py-20 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
            <p class="text-gray-600">Get answers to common questions about our {{ $service->title }} service.</p>
        </div>
        
        <div class="space-y-4">
            @foreach($faqs as $index => $faq)
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <button class="w-full px-6 py-4 text-left flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-orange-500 faq-toggle"
                        data-target="faq-{{ $index }}">
                    <span class="font-semibold text-gray-900">{{ $faq['question'] }}</span>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="faq-content hidden px-6 pb-4" id="faq-{{ $index }}">
                    <p class="text-gray-600 leading-relaxed">{{ $faq['answer'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Related Projects Section --}}
@if(isset($serviceProjects) && $serviceProjects->count() > 0)
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Our {{ $service->title }} Projects</h2>
            <p class="text-gray-600">See how we've successfully delivered {{ $service->title }} for our clients.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($serviceProjects->take(6) as $project)
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden transform hover:-translate-y-2">
                {{-- Project Image --}}
                <div class="relative h-48 overflow-hidden">
                    @if($project->image)
                        <img src="{{ asset('storage/' . $project->image) }}" 
                             alt="{{ $project->title }}" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-orange-400 to-amber-500 flex items-center justify-center">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                    @if($project->category)
                    <div class="absolute top-3 left-3">
                        <span class="bg-orange-500 text-white px-2 py-1 rounded-lg text-xs font-medium">
                            {{ $project->category->name }}
                        </span>
                    </div>
                    @endif
                    <div class="absolute bottom-3 right-3">
                        <span class="bg-green-500 text-white px-2 py-1 rounded-lg text-xs font-medium">
                            Completed
                        </span>
                    </div>
                </div>
                
                {{-- Project Content --}}
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-orange-600 transition-colors">
                        {{ $project->title }}
                    </h3>
                    @if($project->client)
                    <p class="text-orange-600 text-sm font-medium mb-2">
                        {{ $project->client->name }}
                    </p>
                    @endif
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                        {{ $project->short_description ?: Str::limit($project->description, 80) }}
                    </p>
                    
                    <div class="flex items-center justify-between">
                        @if($project->completed_at)
                        <span class="text-gray-500 text-xs">
                            {{ $project->completed_at->format('M Y') }}
                        </span>
                        @endif
                        <a href="{{ route('portfolio.show', $project->slug) }}" 
                           class="inline-flex items-center text-orange-600 font-medium text-sm hover:text-orange-700 transition-colors">
                            View Project
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        @if($serviceProjects->count() > 6)
        <div class="text-center mt-8">
            <a href="{{ route('portfolio.index', ['service' => $service->slug]) }}" 
               class="inline-flex items-center px-6 py-3 bg-orange-600 text-white font-semibold rounded-xl hover:bg-orange-700 transition-colors">
                View All {{ $service->title }} Projects
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>
        @endif
    </div>
</section>
@endif

{{-- Related Services Section --}}
@if($relatedServices && $relatedServices->count() > 0)
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Related Services</h2>
            <p class="text-gray-600">Explore other services that might interest you.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($relatedServices as $relatedService)
            <div class="group bg-white rounded-2xl border border-gray-200 hover:border-orange-300 shadow-md hover:shadow-xl transition-all duration-500 overflow-hidden transform hover:-translate-y-1">
                {{-- Service Image --}}
                <div class="relative h-48 overflow-hidden">
                    @if($relatedService->image)
                        <img src="{{ asset('storage/' . $relatedService->image) }}" 
                             alt="{{ $relatedService->title }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                    
                    @if($relatedService->featured)
                    <div class="absolute top-3 left-3">
                        <span class="bg-orange-500 text-white px-2 py-1 rounded-lg text-xs font-medium">
                            Featured
                        </span>
                    </div>
                    @endif
                </div>
                
                {{-- Service Content --}}
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-3 group-hover:text-orange-600 transition-colors">
                        {{ $relatedService->title }}
                    </h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                        {{ $relatedService->short_description ?: Str::limit($relatedService->description, 80) }}
                    </p>
                    
                    <div class="flex items-center justify-between">
                        @if($relatedService->category)
                        <span class="text-orange-600 text-xs font-medium">
                            {{ $relatedService->category->name }}
                        </span>
                        @endif
                        <a href="{{ route('services.show', $relatedService->slug) }}" 
                           class="inline-flex items-center text-orange-600 font-medium text-sm hover:text-orange-700 transition-colors">
                            Learn More
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="text-center mt-8">
            <a href="{{ route('services.index') }}" 
               class="inline-flex items-center px-6 py-3 border border-orange-600 text-orange-600 font-semibold rounded-xl hover:bg-orange-600 hover:text-white transition-all duration-300">
                View All Services
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>
    </div>
</section>
@endif

{{-- CTA Section --}}
<section class="py-20 bg-gradient-to-r from-orange-600 via-amber-600 to-orange-700">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
            Ready to Start Your {{ $service->title }} Project?
        </h2>
        <p class="text-xl text-orange-100 mb-8">
            Get in touch with our experts today for a free consultation and detailed quote.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('contact.index', ['service' => $service->slug]) }}" 
               class="inline-flex items-center px-8 py-4 bg-white text-orange-600 font-semibold rounded-xl hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.959 8.959 0 01-4.906-1.405L3 21l2.595-5.094A8.959 8.959 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"/>
                </svg>
                Get Free Consultation
            </a>
            @if($contactInfo['phone'])
            <a href="tel:{{ $contactInfo['phone'] }}" 
               class="inline-flex items-center px-8 py-4 border-2 border-white text-white font-semibold rounded-xl hover:bg-white hover:text-orange-600 transition-all duration-300">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                Call {{ $contactInfo['phone'] }}
            </a>
            @endif
        </div>
    </div>
</section>

{{-- JavaScript for Interactive Elements --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // FAQ Toggle Functionality
    const faqToggles = document.querySelectorAll('.faq-toggle');
    
    faqToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const content = document.getElementById(targetId);
            const icon = this.querySelector('.faq-icon');
            
            // Close all other FAQs
            faqToggles.forEach(otherToggle => {
                if (otherToggle !== this) {
                    const otherTargetId = otherToggle.getAttribute('data-target');
                    const otherContent = document.getElementById(otherTargetId);
                    const otherIcon = otherToggle.querySelector('.faq-icon');
                    
                    otherContent.classList.add('hidden');
                    otherIcon.classList.remove('rotate-180');
                }
            });
            
            // Toggle current FAQ
            content.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        });
    });
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Form enhancement
    const quoteForm = document.querySelector('form[action*="contact"]');
    if (quoteForm) {
        const submitButton = quoteForm.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        quoteForm.addEventListener('submit', function() {
            submitButton.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Sending...
            `;
            submitButton.disabled = true;
        });
    }
});
</script>
@endpush

{{-- Additional CSS for line-clamp utilities --}}
@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Enhanced transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

/* Custom gradient backgrounds */
.bg-gradient-orange {
    background: linear-gradient(135deg, #f97316 0%, #fb923c 25%, #fbbf24 50%, #f59e0b 75%, #ea580c 100%);
}

/* Hover effects */
.hover-lift:hover {
    transform: translateY(-4px);
}

/* Focus styles */
.focus-ring:focus {
    outline: 2px solid #f97316;
    outline-offset: 2px;
}
</style>
@endpush

</x-layouts.public>