{{-- resources/views/components/banner-slider.blade.php --}}
@if($hasContent())
@php
    $swiperId = $getSwiperId();
    $swiperConfig = json_encode($getSwiperConfig());
@endphp

<section class="relative {{ $containerClass }}">
    <div class="swiper {{ $swiperId }}" data-config="{{ htmlspecialchars($swiperConfig) }}">
        <div class="swiper-wrapper">
            @foreach($banners as $banner)
            <div class="swiper-slide">
                <div class="relative {{ $height }} flex items-center" style="{{ $getBannerStyles($banner) }}">
                    {{-- Dark Overlay --}}
                    <div class="absolute inset-0 bg-black/40 z-10"></div>
                    
                    {{-- Mobile Image (if different) --}}
                    @if($banner->mobile_image)
                    <div class="absolute inset-0 z-0 md:hidden">
                        <img src="{{ Storage::url($banner->mobile_image) }}" 
                             alt="{{ $banner->title }}"
                             class="w-full h-full object-cover">
                    </div>
                    @endif
                    
                    {{-- Content --}}
                    <div class="relative z-20 container mx-auto px-4">
                        <div class="max-w-4xl">
                            @if($banner->subtitle)
                            <div class="mb-4 animate-fade-in-up" style="animation-delay: 0.2s">
                                <span class="inline-block px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-white/90 font-medium text-sm md:text-base">
                                    {{ $banner->subtitle }}
                                </span>
                            </div>
                            @endif
                            
                            <h1 class="text-3xl md:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight animate-fade-in-up" style="animation-delay: 0.4s">
                                {{ $banner->title }}
                            </h1>
                            
                            @if($banner->description)
                            <p class="text-lg md:text-xl text-white/90 mb-8 max-w-2xl leading-relaxed animate-fade-in-up" style="animation-delay: 0.6s">
                                {{ $banner->description }}
                            </p>
                            @endif
                            
                            @if($banner->button_text && $banner->button_link)
                            <div class="animate-fade-in-up" style="animation-delay: 0.8s">
                                <a {!! collect($banner->link_attributes)->map(fn($value, $key) => "$key=\"$value\"")->implode(' ') !!}
                                   class="inline-flex items-center px-8 py-4 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-lg transition-all duration-300 hover:scale-105 hover:shadow-xl"
                                   @if($banner->shouldOpenInNewTab()) onclick="trackBannerClick({{ $banner->id }})" @endif>
                                    {{ $banner->button_text }}
                                    <svg class="ml-2 w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                    </svg>
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        {{-- Navigation --}}
        @if($showNavigation && $banners->count() > 1)
        <div class="swiper-button-next !text-white hover:!text-amber-400 transition-colors"></div>
        <div class="swiper-button-prev !text-white hover:!text-amber-400 transition-colors"></div>
        @endif
        
        {{-- Pagination --}}
        @if($showPagination && $banners->count() > 1)
        <div class="swiper-pagination"></div>
        @endif
    </div>
</section>

@once
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
/* Custom animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    opacity: 0;
    animation: fadeInUp 0.8s ease-out forwards;
}

/* Custom swiper styles */
.swiper-pagination-bullet {
    background: rgba(255, 255, 255, 0.5);
    opacity: 1;
}

.swiper-pagination-bullet-active {
    background: #f59e0b;
}

.swiper-button-next,
.swiper-button-prev {
    background: rgba(0, 0, 0, 0.3);
    width: 50px;
    height: 50px;
    border-radius: 50%;
    backdrop-filter: blur(10px);
}

.swiper-button-next:after,
.swiper-button-prev:after {
    font-size: 20px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .swiper-button-next,
    .swiper-button-prev {
        width: 40px;
        height: 40px;
    }
    
    .swiper-button-next:after,
    .swiper-button-prev:after {
        font-size: 16px;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize banner slider
    const swiperElement = document.querySelector('.{{ $swiperId }}');
    if (swiperElement) {
        const config = JSON.parse(swiperElement.dataset.config);
        
        // Add navigation and pagination selectors
        if (document.querySelector('.{{ $swiperId }} .swiper-pagination')) {
            config.pagination = {
                el: '.{{ $swiperId }} .swiper-pagination',
                clickable: true,
            };
        }
        
        if (document.querySelector('.{{ $swiperId }} .swiper-button-next')) {
            config.navigation = {
                nextEl: '.{{ $swiperId }} .swiper-button-next',
                prevEl: '.{{ $swiperId }} .swiper-button-prev',
            };
        }
        
        new Swiper('.{{ $swiperId }}', config);
    }
});

// Track banner clicks
function trackBannerClick(bannerId) {
    fetch(`/api/banners/${bannerId}/track`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ action: 'click' })
    }).catch(error => console.log('Banner tracking error:', error));
}
</script>
@endpush
@endonce

@else
{{-- Fallback content when no banners --}}
<section class="relative {{ $height }} bg-gradient-to-br from-blue-600 to-purple-700 flex items-center {{ $containerClass }}">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
            {{ config('app.name') }}
        </h1>
        <p class="text-xl text-white/90 max-w-2xl mx-auto">
            Solusi Konstruksi Terpercaya untuk Masa Depan yang Lebih Baik
        </p>
    </div>
</section>
@endif