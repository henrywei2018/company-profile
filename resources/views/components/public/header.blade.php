{{-- resources/views/components/public/header.blade.php --}}
<header class="premium-header glass-effect luxury-shadow border-b premium-border sticky top-0 z-50">
    {{-- Premium Glass Background with Gradients --}}
    <div class="absolute inset-0 bg-gradient-to-r from-white/98 via-white/95 to-white/98 backdrop-blur-xl"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-orange-500/8 via-amber-500/5 to-orange-500/8"></div>
    
    {{-- Animated Border Glow --}}
    <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-transparent via-orange-400/60 via-amber-400/60 to-transparent animate-luxury-shimmer"></div>

    <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Top Bar - Contact Info --}}
        <div class="hidden lg:flex items-center justify-between py-2.5 text-sm border-b border-orange-100/30">
            {{-- Contact Info - Left Side --}}
            <div class="flex items-center space-x-6">
                @if ($contactInfo['email'] ?? false)
                    <a href="mailto:{{ $contactInfo['email'] }}"
                        class="group flex items-center text-gray-600 hover:text-orange-600 transition-all duration-300">
                        <div class="w-7 h-7 bg-gradient-to-br from-orange-400 to-amber-400 rounded-lg flex items-center justify-center mr-2.5 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="font-medium">{{ $contactInfo['email'] }}</span>
                    </a>
                @endif

                @if ($contactInfo['phone'] ?? false)
                    <a href="tel:{{ $contactInfo['phone'] }}"
                        class="group flex items-center text-gray-600 hover:text-orange-600 transition-all duration-300">
                        <div class="w-7 h-7 bg-gradient-to-br from-orange-500 to-amber-500 rounded-lg flex items-center justify-center mr-2.5 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <span class="font-medium">{{ $contactInfo['phone'] }}</span>
                    </a>
                @endif
            </div>

            {{-- Social Media & Theme Toggle - Right Side --}}
            <div class="flex items-center space-x-4">
                {{-- Social Media Links --}}
                <div class="flex items-center space-x-2">
                    @foreach ($socialMedia as $platform => $url)
                        @if ($url)
                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                                class="w-7 h-7 bg-gray-100 hover:bg-gradient-to-br hover:from-orange-400 hover:to-amber-400 rounded-lg flex items-center justify-center text-gray-500 hover:text-white transition-all duration-300 transform hover:scale-110">
                                @switch($platform)
                                    @case('facebook')
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                                        </svg>
                                    @break
                                    @case('instagram')
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                                        </svg>
                                    @break
                                    @case('linkedin')
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                                        </svg>
                                    @break
                                    @case('whatsapp')
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488" />
                                        </svg>
                                    @break
                                    @default
                                        <span class="text-xs font-bold">{{ strtoupper(substr($platform, 0, 2)) }}</span>
                                @endswitch
                            </a>
                        @endif
                    @endforeach
                </div>

                {{-- Dark Mode Toggle --}}
                <button id="theme-toggle"
                    class="w-7 h-7 bg-gray-100 hover:bg-gradient-to-br hover:from-orange-400 hover:to-amber-400 rounded-lg flex justify-center items-center text-gray-600 hover:text-white transition-all duration-300 transform hover:scale-110 focus-luxury">
                    <svg class="hidden dark:block w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="5" />
                        <line x1="12" y1="1" x2="12" y2="3" />
                        <line x1="12" y1="21" x2="12" y2="23" />
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" />
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" />
                        <line x1="1" y1="12" x2="3" y2="12" />
                        <line x1="21" y1="12" x2="23" y2="12" />
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36" />
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22" />
                    </svg>
                    <svg class="block dark:hidden w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
                    </svg>
                    <span class="sr-only">Toggle dark mode</span>
                </button>               
            </div>
        </div>

        {{-- Main Navigation Bar --}}
        <div class="flex items-center justify-between py-4">
            {{-- Logo Section - Fixed Width --}}
            <div class="flex items-center w-64">
                <a href="{{ route('home') }}" class="group flex items-center">
                    <div class="relative">
                        {{-- Logo Container --}}
                        <div class="w-32 h-18 transition-all duration-300">
                            <img src="{{$companyProfile->logoUrl ?? asset('images/logo.png')}}"
                                alt="{{ $companyProfile->company_name ?? config('app.name') }}"
                                class="w-full h-full">
                        </div>
                    </div>                    
                </a>
            </div>

            {{-- Navigation Menu - Center --}}
            <nav class="hidden lg:flex items-center flex-1 justify-center">
                <div class="flex items-center space-x-1">
                    @foreach ($navLinks as $link)
                        @if (isset($link['dropdown']) && count($link['dropdown']) > 0)
                            {{-- Dropdown Menu with Bridge --}}
                            <div class="relative group">
                                <button class="nav-link px-4 py-2 text-sm font-medium text-gray-700 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-all duration-300 flex items-center {{ collect($link['active_routes'] ?? [])->contains(fn($route) => request()->routeIs($route)) ? 'text-orange-600 bg-orange-50' : '' }}">
                                    {{ $link['label'] }}
                                    <svg class="w-4 h-4 ml-1 transition-transform duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                
                                {{-- Invisible Bridge to prevent dropdown from closing --}}
                                <div class="absolute left-0 right-0 h-2 -bottom-0 bg-transparent group-hover:block"></div>
                                
                                {{-- Dropdown Content --}}
                                <div class="absolute left-0 mt-0 w-64 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 bg-white rounded-xl shadow-xl border border-orange-100/50 overflow-hidden">
                                    @foreach ($link['dropdown'] as $item)
                                        <a href="{{ isset($item['params']) ? route($item['route'], $item['params']) : route($item['route']) }}"
                                            class="block px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-colors duration-300 border-b border-gray-100 last:border-b-0">
                                            <div class="font-medium">{{ $item['label'] }}</div>
                                            @if (isset($item['description']))
                                                <div class="text-xs text-gray-500 mt-1">{{ $item['description'] }}</div>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            {{-- Single Link --}}
                            <a href="{{ route($link['route']) }}"
                                class="nav-link px-4 py-2 text-sm font-medium text-gray-700 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-all duration-300 {{ collect($link['active_routes'] ?? [])->contains(fn($route) => request()->routeIs($route)) ? 'text-orange-600 bg-orange-50' : '' }}">
                                {{ $link['label'] }}
                            </a>
                        @endif
                    @endforeach
                </div>
            </nav>

            {{-- Mobile Navigation Toggle - Show on MD and below --}}
            <nav class="flex lg:hidden items-center flex-1 justify-center">
                <button id="mobile-menu-button" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-all duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    Menu
                </button>
            </nav>

            {{-- Right Section - CTA & User Menu --}}
            <div class="flex items-center justify-end w-64 space-x-3">
                {{-- CTA Button --}}
                <a href="{{ route('quotation.create') }}" 
                   class="hidden md:inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-orange-500 to-amber-500 text-white text-sm font-semibold rounded-lg hover:from-orange-600 hover:to-amber-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span class="hidden lg:inline">Get Quote</span>
                    <span class="lg:hidden">Quote</span>
                </a>

                {{-- User Menu --}}
                @auth
                    <div class="relative group">
                        <button class="flex items-center space-x-2 text-gray-700 hover:text-orange-600 transition-all duration-300">
                            <div class="w-9 h-9 bg-gradient-to-br from-orange-500 to-amber-500 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-sm">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </span>
                            </div>
                            <span class="hidden xl:block font-medium text-sm">{{ Str::limit(auth()->user()->name, 12) }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        
                        {{-- Invisible Bridge for User Dropdown --}}
                        <div class="absolute right-0 left-0 h-2 -bottom-0 bg-transparent group-hover:block"></div>
                        
                        {{-- User Dropdown --}}
                        <div class="absolute right-0 mt-0 w-48 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 bg-white rounded-xl shadow-xl border border-orange-100/50 overflow-hidden">
                            <div class="px-4 py-3 bg-gradient-to-r from-orange-50 to-amber-50 border-b border-orange-100">
                                <p class="font-semibold text-gray-900 text-sm">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-600">{{ auth()->user()->email }}</p>
                            </div>
                            <div class="py-2">
                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-colors duration-300">
                                    Dashboard
                                </a>
                                <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-colors duration-300">
                                    Profile
                                </a>
                                <div class="border-t border-gray-200 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-300">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-orange-600 transition-colors duration-300">
                        Login
                    </a>
                @endauth
            </div>
        </div>
    </div>

    {{-- Mobile Navigation --}}
    <div id="mobile-menu" class="lg:hidden hidden bg-white border-t border-orange-100/50">
        <div class="w-full max-w-7xl mx-auto px-4 py-4">
            <nav class="space-y-2">
                @foreach ($navLinks as $link)
                    @if (isset($link['dropdown']) && count($link['dropdown']) > 0)
                        {{-- Mobile Dropdown --}}
                        <div class="border-b border-gray-100 pb-2 mb-2">
                            <div class="font-semibold text-gray-900 px-3 py-2 text-sm">
                                {{ $link['label'] }}
                            </div>
                            @foreach ($link['dropdown'] as $item)
                                <a href="{{ isset($item['params']) ? route($item['route'], $item['params']) : route($item['route']) }}"
                                    class="block px-6 py-2 text-sm text-gray-600 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-all duration-300">
                                    {{ $item['label'] }}
                                </a>
                            @endforeach
                        </div>
                    @else
                        {{-- Mobile Single Link --}}
                        <a href="{{ route($link['route']) }}"
                            class="block px-4 py-3 text-sm font-medium text-gray-700 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-all duration-300 {{ collect($link['active_routes'] ?? [])->contains(fn($route) => request()->routeIs($route)) ? 'text-orange-600 bg-orange-50' : '' }}">
                            {{ $link['label'] }}
                        </a>
                    @endif
                @endforeach

                {{-- Mobile CTA --}}
                <div class="pt-4 border-t border-gray-200">
                    <a href="{{ route('quotation.create') }}" 
                       class="block w-full text-center px-4 py-3 bg-gradient-to-r from-orange-500 to-amber-500 text-white text-sm font-semibold rounded-lg hover:from-orange-600 hover:to-amber-600 transition-all duration-300">
                        Get Free Quote
                    </a>
                </div>
            </nav>
        </div>
    </div>
</header>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function(e) {
            e.stopPropagation();
            mobileMenu.classList.toggle('hidden');
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                mobileMenu.classList.add('hidden');
            }
        });

        // Close mobile menu when window is resized to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                mobileMenu.classList.add('hidden');
            }
        });
    }

    // Smart header scroll behavior
    let lastScrollY = window.scrollY;
    const header = document.querySelector('.premium-header');

    window.addEventListener('scroll', () => {
        const currentScrollY = window.scrollY;

        if (currentScrollY > 100) {
            if (currentScrollY > lastScrollY && currentScrollY > 200) {
                // Scrolling down - hide header
                header.style.transform = 'translateY(-100%)';
            } else {
                // Scrolling up - show header
                header.style.transform = 'translateY(0)';
            }
        } else {
            // At top - show header
            header.style.transform = 'translateY(0)';
        }

        lastScrollY = currentScrollY;
    }, { passive: true });

    // Add smooth transition
    header.style.transition = 'transform 0.3s ease-in-out';
});
</script>
@endpush