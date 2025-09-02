{{-- resources/views/components/public/footer.blade.php --}}
<footer class="bg-gray-900 text-white">
    {{-- Main Footer --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {{-- Company Info --}}
            <div class="lg:col-span-1">
                <div class="mb-6">
                    @if($siteConfig['site_logo'])
                        <img src="{{ asset($siteConfig['site_logo']) }}" 
                             alt="{{ $companyProfile->company_name ?? config('app.name') }}" 
                             class="h-10 w-auto mb-4">
                    @else
                        <h3 class="text-xl font-bold text-white mb-4">
                            {{ $companyProfile->company_name ?? config('app.name') }}
                        </h3>
                    @endif
                    
                    @if($companyProfile?->tagline)
                        <p class="text-gray-300 mb-4">
                            {{ $companyProfile->tagline }}
                        </p>
                    @endif
                    
                    @if($companyProfile?->about)
                        <p class="text-gray-400 text-sm leading-relaxed">
                            {{ Str::limit($companyProfile->about, 150) }}
                        </p>
                    @endif
                </div>
                
                {{-- Social Media --}}
                <div class="flex space-x-4">
                    @foreach($socialMedia as $platform => $url)
                        @if($url)
                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                               class="text-gray-400 hover:text-white transition-colors duration-300">
                                @switch($platform)
                                    @case('facebook')
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-label="Facebook">
                                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                        </svg>
                                        @break
                                    @case('instagram')
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-label="Instagram">
                                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                        </svg>
                                        @break
                                    @case('twitter')
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-label="Twitter">
                                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                        </svg>
                                        @break
                                    @case('linkedin')
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-label="LinkedIn">
                                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                        </svg>
                                        @break
                                    @case('youtube')
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-label="YouTube">
                                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                        </svg>
                                        @break
                                    @case('whatsapp')
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-label="WhatsApp">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
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
                <h3 class="text-lg font-semibold text-white mb-6">Tautan Cepat</h3>
                <ul class="space-y-3">
                    @foreach($navLinks as $link)
                        <li>
                            <a href="{{ route($link['route']) }}" 
                               class="text-gray-400 hover:text-white transition-colors duration-300 flex items-center group">
                                <svg class="w-4 h-4 mr-2 text-orange-500 group-hover:translate-x-1 transition-transform duration-300" 
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Services --}}
            <div>
                <h3 class="text-lg font-semibold text-white mb-6">Layanan Kami</h3>
                <ul class="space-y-3">
                    @if($globalServices && $globalServices->count() > 0)
                        @foreach($globalServices->take(6) as $service)
                            <li>
                                <a href="{{ route('services.show', $service->slug) }}" 
                                   class="text-gray-400 hover:text-white transition-colors duration-300 flex items-center group">
                                    <svg class="w-4 h-4 mr-2 text-orange-500 group-hover:translate-x-1 transition-transform duration-300" 
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    {{ $service->title }}
                                </a>
                            </li>
                        @endforeach
                        @if($globalServices->count() > 6)
                            <li>
                                <a href="{{ route('services.index') }}" 
                                   class="text-orange-400 hover:text-orange-300 transition-colors duration-300 flex items-center group font-medium">
                                    <svg class="w-4 h-4 mr-2 group-hover:translate-x-1 transition-transform duration-300" 
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                    Lihat Semua Layanan
                                </a>
                            </li>
                        @endif
                    @else
                        <li class="text-gray-500 text-sm">No services available</li>
                    @endif
                </ul>
            </div>

            {{-- Contact Info --}}
            <div>
                <h3 class="text-lg font-semibold text-white mb-6">Informasi Kontak</h3>
                <ul class="space-y-4">
                    @if($contactInfo['address'])
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-orange-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="text-gray-400 text-sm leading-relaxed">{{ $contactInfo['address'] }}</span>
                        </li>
                    @endif

                    @if($contactInfo['phone'])
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-orange-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <a href="tel:{{ $contactInfo['phone'] }}" 
                               class="text-gray-400 hover:text-white transition-colors duration-300 text-sm">
                                {{ $contactInfo['phone'] }}
                            </a>
                        </li>
                    @endif

                    @if($contactInfo['email'])
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-orange-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <a href="mailto:{{ $contactInfo['email'] }}" 
                               class="text-gray-400 hover:text-white transition-colors duration-300 text-sm">
                                {{ $contactInfo['email'] }}
                            </a>
                        </li>
                    @endif

                    @if($contactInfo['business_hours'])
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-orange-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-gray-400 text-sm">{{ $contactInfo['business_hours'] }}</span>
                        </li>
                    @endif
                </ul>

            </div>
        </div>
    </div>

    {{-- Newsletter Section (Optional) --}}
    @if($settings['newsletter_enabled'] ?? false)
    <div class="border-t border-gray-800 bg-gray-800">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="max-w-2xl mx-auto text-center">
                <h3 class="text-lg font-semibold text-white mb-2">Stay Updated</h3>
                <p class="text-gray-400 mb-4">Subscribe to our newsletter for latest updates and news.</p>
                
                <form class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                    <input type="email" 
                           placeholder="Enter your email" 
                           class="flex-1 px-4 py-2 bg-gray-700 text-white rounded-lg border border-gray-600 focus:outline-none focus:border-orange-500 transition-colors duration-300">
                    <button type="submit" 
                            class="px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition-colors duration-300 font-medium">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Bottom Footer --}}
    <div class="border-t border-gray-800">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-gray-400 text-sm mb-4 md:mb-0">
                    © {{ date('Y') }} {{ $companyProfile->company_name ?? config('app.name') }}. Hak cipta dilindungi.
                </div>
                
                <div class="flex items-center space-x-6 text-sm">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">
                        Kebijakan Privasi
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">
                        Syarat Layanan
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">
                        Sitemap
                    </a>
                    @if($settings['cookie_policy_enabled'] ?? false)
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">
                        Kebijakan Cookie
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</footer>