{{-- resources/views/components/public/service-card.blade.php --}}
@props(['service'])

<div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden transform hover:-translate-y-2">
    @if($service->featured_image)
    <div class="relative h-48 overflow-hidden">
        <img src="{{ asset('storage/' . $service->featured_image) }}" 
             alt="{{ $service->title }}" 
             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
        
        @if($service->icon)
        <div class="absolute top-4 left-4 w-12 h-12 bg-white/90 rounded-lg flex items-center justify-center">
            {!! $service->icon !!}
        </div>
        @endif
    </div>
    @endif
    
    <div class="p-6">
        <h3 class="text-xl font-semibold text-gray-900 mb-3 group-hover:text-orange-600 transition-colors duration-300">
            {{ $service->title }}
        </h3>
        <p class="text-gray-600 mb-4 line-clamp-3">
            {{ $service->short_description }}
        </p>
        
        @if($service->price_range)
        <div class="text-sm text-orange-600 font-semibold mb-4">
            Starting from {{ $service->price_range }}
        </div>
        @endif
        
        <a href="{{ route('services.show', $service->slug) }}" 
           class="inline-flex items-center text-orange-600 font-semibold hover:text-orange-700 transition-colors duration-300">
            Learn More
            <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</div>