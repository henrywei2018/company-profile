{{-- resources/views/components/public/footer.blade.php --}}
<footer class="bg-gray-900 text-white">
    {{-- Main Footer --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {{-- Company Info --}}
            <div class="lg:col-span-1">
                <div class="mb-6">
                    @if($globalSiteConfig['site_logo'])
                        <img src="{{ asset($globalSiteConfig['site_logo']) }}" 
                             alt="{{ $globalSiteConfig['site_name'] }}" 
                             class="h-10 w-auto mb-4">
                    @else
                        <h3 class="text-xl font-bold text-white mb-4">
                            {{ $globalSiteConfig['site_name'] }}
                        </h3>
                    @endif
                    
                    @if($globalCompanyProfile?->tagline)
                        <p class="text-gray-300 mb-4">
                            {{ $globalCompanyProfile->tagline }}
                        </p>
                    @endif
                    
                    @if($globalCompanyProfile?->description)
                        <p class="text-gray-400 text-sm leading-relaxed">
                            {{ Str::limit($globalCompanyProfile->description, 150) }}
                        </p>
                    @endif
                </div>
                
                {{-- Social Media --}}
                <div class="flex space-x-4">
                    @foreach($globalSocialMedia as $platform => $url)
                        @if($url)
                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                               class="text-gray-400 hover:text-white transition-colors">
                                @switch($platform)
                                    @case('facebook')
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                        </svg>
                                        @break
                                    @case('instagram')
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987 6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.349-1.051-2.349-2.348 0-1.297 1.052-2.349 2.349-2.349 1.297 0 2.348 1.052 2.348 2.349 0 1.297-1.051 2.348-2.348 2.348zm7.718 0c-1.297 0-2.349-1.051-2.349-2.348 0-1.297 1.052-2.349 2.349-2.349 1.297 0 2.348 1.052 2.348 2.349 0 1.297-1.051 2.348-2.348 2.348z"/>
                                        </svg>
                                        @break
                                    @case('twitter')
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                        </svg>
                                        @break
                                    @case('linkedin')
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                        </svg>
                                        @break
                                    @case('youtube')
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                        </svg>
                                        @break
                                    @default
                                        <span class="text-sm font-medium">{{ strtoupper(substr($platform, 0, 2)) }}</span>
                                @endswitch
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Quick Links --}}
            <div>
                <h3 class="text-lg font-semibold text-white mb-4">Menu Utama</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition-colors">Beranda</a></li>
                    <li><a href="{{ route('about') }}" class="text-gray-400 hover:text-white transition-colors">Tentang Kami</a></li>
                    <li><a href="{{ route('services.index') }}" class="text-gray-400 hover:text-white transition-colors">Layanan</a></li>
                    <li><a href="{{ route('portfolio.index') }}" class="text-gray-400 hover:text-white transition-colors">Portfolio</a></li>
                    <li><a href="{{ route('blog.index') }}" class="text-gray-400 hover:text-white transition-colors">Blog</a></li>
                    <li><a href="{{ route('contact') }}" class="text-gray-400 hover:text-white transition-colors">Kontak</a></li>
                </ul>
            </div>

            {{-- Services --}}
            <div>
                <h3 class="text-lg font-semibold text-white mb-4">Layanan Kami</h3>
                <ul class="space-y-2">
                    @foreach($globalServices->take(6) as $service)
                        <li>
                            <a href="{{ route('services.show', $service->slug) }}" 
                               class="text-gray-400 hover:text-white transition-colors">
                                {{ $service->title }}
                            </a>
                        </li>
                    @endforeach
                    @if($globalServices->count() > 6)
                        <li>
                            <a href="{{ route('services.index') }}" 
                               class="text-blue-400 hover:text-blue-300 transition-colors">
                                Lihat Semua →
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            {{-- Contact Info --}}
            <div>
                <h3 class="text-lg font-semibold text-white mb-4">Kontak Kami</h3>
                <ul class="space-y-3">
                    @if($globalContactInfo['address'])
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="text-gray-400 text-sm">{{ $globalContactInfo['address'] }}</span>
                        </li>
                    @endif

                    @if($globalContactInfo['phone'])
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <a href="tel:{{ $globalContactInfo['phone'] }}" 
                               class="text-gray-400 hover:text-white transition-colors text-sm">
                                {{ $globalContactInfo['phone'] }}
                            </a>
                        </li>
                    @endif

                    @if($globalContactInfo['email'])
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <a href="mailto:{{ $globalContactInfo['email'] }}" 
                               class="text-gray-400 hover:text-white transition-colors text-sm">
                                {{ $globalContactInfo['email'] }}
                            </a>
                        </li>
                    @endif

                    @if($globalContactInfo['working_hours'])
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-gray-400 text-sm">{{ $globalContactInfo['working_hours'] }}</span>
                        </li>
                    @endif
                </ul>

                {{-- Call to Action --}}
                <div class="mt-6">
                    <a href="{{ route('quotation.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Minta Penawaran
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Footer --}}
    <div class="border-t border-gray-800">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-gray-400 text-sm mb-4 md:mb-0">
                    © {{ date('Y') }} {{ $globalSiteConfig['site_name'] }}. All rights reserved.
                </div>
                
                <div class="flex items-center space-x-6 text-sm">
                    <a href="{{ route('privacy-policy') }}" class="text-gray-400 hover:text-white transition-colors">
                        Privacy Policy
                    </a>
                    <a href="{{ route('terms-of-service') }}" class="text-gray-400 hover:text-white transition-colors">
                        Terms of Service
                    </a>
                    <a href="{{ route('sitemap') }}" class="text-gray-400 hover:text-white transition-colors">
                        Sitemap
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>