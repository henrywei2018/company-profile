@if($banners->count() > 0)
@php
    $isSlider = in_array($displayType, ['slider', 'ads']);
    $isGrid = $displayType === 'grid';
    $isSingle = $displayType === 'single';
    $isDashboardAds = $displayType === 'dashboard-ads';
    $carouselId = 'banner-' . uniqid();
@endphp

<div class="{{ $isDashboardAds ? 'p-4' : 'px-4 py-8 sm:px-6 lg:px-8' }}">
    @if($isGrid)
        {{-- Grid Layout for Multiple Banners --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($banners as $banner)
                <div class="relative overflow-hidden rounded-2xl {{ $height }} group">
                    <div class="h-full flex flex-col bg-[url('{{ $banner->imageUrl }}')] bg-cover bg-center bg-no-repeat">
                        <div class="absolute inset-0 bg-black bg-opacity-30 group-hover:bg-opacity-20 transition-all duration-300"></div>
                        <div class="relative mt-auto w-full p-6">
                            @if($banner->subtitle)
                                <span class="block text-white text-sm opacity-90">{{ $banner->subtitle }}</span>
                            @endif
                            <span class="block text-white text-lg md:text-xl font-semibold">{{ $banner->title }}</span>
                            @if($banner->description)
                                <p class="mt-2 text-white text-sm opacity-90 line-clamp-2">{{ $banner->description }}</p>
                            @endif
                            @if($banner->button_text && $banner->button_link)
                                <div class="mt-4">
                                    <a class="inline-flex items-center gap-x-2 text-sm font-medium px-4 py-2 rounded-xl bg-white bg-opacity-90 text-black hover:bg-opacity-100 transition-all duration-300 transform hover:scale-105" 
                                       href="{{ $banner->button_link }}"
                                       @if($banner->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                                       onclick="trackBannerClick({{ $banner->id }})">
                                        {{ $banner->button_text }}
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @elseif($isSingle || $isDashboardAds)
        {{-- Single Banner or Dashboard Ads Layout --}}
        @foreach($banners as $banner)
            <div class="relative overflow-hidden rounded-2xl {{ $isDashboardAds ? 'h-32 mb-4' : $height }} group">
                <div class="h-full flex flex-col bg-[url('{{ $banner->imageUrl }}')] bg-cover bg-center bg-no-repeat">
                    <div class="absolute inset-0 bg-gradient-to-r from-black/50 to-transparent"></div>
                    <div class="relative mt-auto {{ $isDashboardAds ? 'w-full p-4' : 'w-2/3 md:max-w-lg ps-5 pb-5 md:ps-10 md:pb-10' }}">
                        @if($banner->subtitle && !$isDashboardAds)
                            <span class="block text-white text-sm opacity-90">{{ $banner->subtitle }}</span>
                        @endif
                        <span class="block text-white {{ $isDashboardAds ? 'text-lg' : 'text-xl md:text-3xl' }} font-bold">{{ $banner->title }}</span>
                        @if($banner->description && !$isDashboardAds)
                            <p class="mt-2 text-white opacity-90">{{ $banner->description }}</p>
                        @endif
                        @if($banner->button_text && $banner->button_link)
                            <div class="mt-{{ $isDashboardAds ? '3' : '5' }}">
                                <a class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-xl bg-white border border-transparent text-black hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 transition-all duration-300 transform hover:scale-105" 
                                   href="{{ $banner->button_link }}"
                                   @if($banner->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                                   onclick="trackBannerClick({{ $banner->id }})">
                                    {{ $banner->button_text }}
                                    @if($isDashboardAds)
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    @endif
                                </a>
                            </div>
                        @endif
                    </div>
                    @if($isDashboardAds)
                        <div class="absolute top-2 right-2">
                            <span class="bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">Sponsored</span>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        {{-- Slider Layout (Default) --}}
        <div data-hs-carousel='{
            "loadingClasses": "opacity-0"{{ $autoPlay ? ',"autoPlay": true, "autoPlayInterval": 5000' : '' }}
        }' class="relative" id="{{ $carouselId }}">
            <div class="hs-carousel relative overflow-hidden w-full {{ $height }} bg-gray-100 rounded-2xl dark:bg-neutral-800">
                <div class="hs-carousel-body absolute top-0 bottom-0 start-0 flex flex-nowrap transition-transform duration-700 opacity-0">
                    @foreach($banners as $index => $banner)
                    <!-- Slide {{ $index + 1 }} -->
                    <div class="hs-carousel-slide">
                        <div class="{{ $height }} flex flex-col bg-[url('{{ $banner->imageUrl }}')] bg-cover bg-center bg-no-repeat">
                            <div class="absolute inset-0 bg-gradient-to-r from-black/40 to-transparent"></div>
                            <div class="relative mt-auto w-2/3 md:max-w-lg ps-5 pb-5 md:ps-10 md:pb-10">
                                @if($banner->subtitle)
                                    <span class="block text-white opacity-90 animate-fade-in-up" style="animation-delay: 0.2s">{{ $banner->subtitle }}</span>
                                @endif
                                <span class="block text-white text-xl md:text-3xl font-bold animate-fade-in-up" style="animation-delay: 0.4s">{{ $banner->title }}</span>
                                @if($banner->description)
                                    <p class="mt-2 text-white opacity-90 animate-fade-in-up" style="animation-delay: 0.6s">{{ $banner->description }}</p>
                                @endif
                                @if($banner->button_text && $banner->button_link)
                                    <div class="mt-5 animate-fade-in-up" style="animation-delay: 0.8s">
                                        <a class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-xl bg-white border border-transparent text-black hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none transition-all duration-300 transform hover:scale-105" 
                                           href="{{ $banner->button_link }}"
                                           @if($banner->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                                           onclick="trackBannerClick({{ $banner->id }})">
                                            {{ $banner->button_text }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- End Slide {{ $index + 1 }} -->
                    @endforeach
                </div>
            </div>

            @if($banners->count() > 1 && $showArrows)
                <!-- Navigation Arrows -->
                <button type="button" class="hs-carousel-prev hs-carousel-disabled:opacity-50 disabled:pointer-events-none absolute inset-y-0 start-0 inline-flex justify-center items-center w-12 h-full text-white hover:bg-white/20 rounded-s-2xl focus:outline-hidden focus:bg-white/20 transition-all duration-300">
                    <span class="text-2xl" aria-hidden="true">
                        <svg class="shrink-0 size-3.5 md:size-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"></path>
                        </svg>
                    </span>
                    <span class="sr-only">Previous</span>
                </button>

                <button type="button" class="hs-carousel-next hs-carousel-disabled:opacity-50 disabled:pointer-events-none absolute inset-y-0 end-0 inline-flex justify-center items-center w-12 h-full text-white hover:bg-white/20 rounded-e-2xl focus:outline-hidden focus:bg-white/20 transition-all duration-300">
                    <span class="sr-only">Next</span>
                    <span class="text-2xl" aria-hidden="true">
                        <svg class="shrink-0 size-3.5 md:size-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"></path>
                        </svg>
                    </span>
                </button>
                <!-- End Navigation Arrows -->
            @endif

            @if($banners->count() > 1 && $showDots)
                <!-- Dots Indicator -->
                <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                    @foreach($banners as $index => $banner)
                        <button class="w-3 h-3 rounded-full bg-white/50 hover:bg-white/80 focus:bg-white transition-all duration-300 hs-carousel-pagination" data-hs-carousel-goto="{{ $index }}"></button>
                    @endforeach
                </div>
                <!-- End Dots Indicator -->
            @endif
        </div>
    @endif
</div>

@push('styles')
<style>
@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fade-in-up 0.6s ease-out forwards;
    opacity: 0;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush

@push('scripts')
<script>
function trackBannerClick(bannerId) {
    // Track banner clicks for analytics
    if (typeof fetch !== 'undefined') {
        fetch('/api/banner-track', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            },
            body: JSON.stringify({
                banner_id: bannerId,
                action: 'click'
            })
        }).catch(console.error);
    }
}

// Track banner impressions
document.addEventListener('DOMContentLoaded', function() {
    const banners = document.querySelectorAll('[data-banner-id]');
    
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const bannerId = entry.target.dataset.bannerId;
                    if (bannerId && typeof fetch !== 'undefined') {
                        fetch('/api/banner-track', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                            },
                            body: JSON.stringify({
                                banner_id: bannerId,
                                action: 'impression'
                            })
                        }).catch(console.error);
                    }
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        banners.forEach(banner => observer.observe(banner));
    }
});
</script>
@endpush
@endif