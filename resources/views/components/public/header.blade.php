{{-- resources/views/components/public/header.blade.php --}}
<header class="bg-white dark:bg-gray-900 shadow-sm border-b border-gray-200 dark:border-gray-700 sticky top-0 z-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Top Bar --}}
        <div class="hidden lg:flex items-center justify-between py-2 text-sm border-b border-gray-100 dark:border-gray-800">
            {{-- Contact Info --}}
            <div class="flex items-center space-x-6 text-gray-600 dark:text-gray-400">
                @if($globalContactInfo['email'])
                    <a href="mailto:{{ $globalContactInfo['email'] }}" 
                       class="flex items-center hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ $globalContactInfo['email'] }}
                    </a>
                @endif

                @if($globalContactInfo['phone'])
                    <a href="tel:{{ $globalContactInfo['phone'] }}" 
                       class="flex items-center hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        {{ $globalContactInfo['phone'] }}
                    </a>
                @endif

                @if($globalContactInfo['working_hours'])
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $globalContactInfo['working_hours'] }}
                    </span>
                @endif
            </div>

            {{-- Social Media & Language --}}
            <div class="flex items-center space-x-4">
                {{-- Social Media Links --}}
                <div class="flex items-center space-x-2">
                    @foreach($globalSocialMedia as $platform => $url)
                        @if($url)
                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                               class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                @switch($platform)
                                    @case('facebook')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                        </svg>
                                        @break
                                    @case('instagram')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987 6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.349-1.051-2.349-2.348 0-1.297 1.052-2.349 2.349-2.349 1.297 0 2.348 1.052 2.348 2.349 0 1.297-1.051 2.348-2.348 2.348zm7.718 0c-1.297 0-2.349-1.051-2.349-2.348 0-1.297 1.052-2.349 2.349-2.349 1.297 0 2.348 1.052 2.348 2.349 0 1.297-1.051 2.348-2.348 2.348z"/>
                                        </svg>
                                        @break
                                    @case('linkedin')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                        </svg>
                                        @break
                                    @default
                                        <span>{{ strtoupper(substr($platform, 0, 1)) }}</span>
                                @endswitch
                            </a>
                        @endif
                    @endforeach
                </div>

                {{-- Dark Mode Toggle --}}
                <button onclick="toggleDarkMode()" 
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-4 h-4 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <svg class="w-4 h-4 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Main Navigation --}}
        <div class="flex items-center justify-between py-4">
            {{-- Logo --}}
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center">
                    @if($globalSiteConfig['site_logo'])
                        <img src="{{ asset($globalSiteConfig['site_logo']) }}" 
                             alt="{{ $globalSiteConfig['site_name'] }}" 
                             class="h-8 w-auto sm:h-10">
                    @else
                        <span class="text-xl font-bold text-blue-600 dark:text-blue-400">
                            {{ $globalSiteConfig['site_name'] }}
                        </span>
                    @endif
                </a>
            </div>

            {{-- Desktop Navigation --}}
            <nav class="hidden lg:flex items-center space-x-8">
                <a href="{{ route('home') }}" 
                   class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors {{ request()->routeIs('home') ? 'text-blue-600 dark:text-blue-400 font-medium' : '' }}">
                    Beranda
                </a>

                <a href="{{ route('about') }}" 
                   class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors {{ request()->routeIs('about*') ? 'text-blue-600 dark:text-blue-400 font-medium' : '' }}">
                    Tentang
                </a>

                {{-- Services Dropdown --}}
                <div class="relative group">
                    <button class="flex items-center text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors {{ request()->routeIs('services*') ? 'text-blue-600 dark:text-blue-400 font-medium' : '' }}">
                        Layanan
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <div class="absolute left-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <div class="py-2">
                            <a href="{{ route('services.index') }}" 
                               class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                Semua Layanan
                            </a>
                            @foreach($globalServiceCategories->take(5) as $category)
                                <a href="{{ route('services.category', $category->slug) }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <a href="{{ route('portfolio.index') }}" 
                   class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors {{ request()->routeIs('portfolio*') ? 'text-blue-600 dark:text-blue-400 font-medium' : '' }}">
                    Portfolio
                </a>

                <a href="{{ route('blog.index') }}" 
                   class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors {{ request()->routeIs('blog*') ? 'text-blue-600 dark:text-blue-400 font-medium' : '' }}">
                    Blog
                </a>

                <a href="{{ route('contact') }}" 
                   class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors {{ request()->routeIs('contact*') ? 'text-blue-600 dark:text-blue-400 font-medium' : '' }}">
                    Kontak
                </a>
            </nav>

            {{-- CTA Buttons --}}
            <div class="hidden lg:flex items-center space-x-4">
                <a href="{{ route('quotation.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors font-medium">
                    Minta Penawaran
                </a>
                
                @auth
                    <a href="{{ route('dashboard') }}" 
                       class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        Masuk
                    </a>
                @endauth
            </div>

            {{-- Mobile Menu Button --}}
            <button id="mobile-menu-button" 
                    class="lg:hidden text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile Navigation --}}
    <div id="mobile-menu" class="lg:hidden hidden bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
        <div class="px-4 py-2 space-y-1">
            <a href="{{ route('home') }}" 
               class="block px-3 py-2 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 {{ request()->routeIs('home') ? 'bg-blue-50 dark:bg-blue-900 text-blue-600 dark:text-blue-400' : '' }}">
                Beranda
            </a>
            
            <a href="{{ route('about') }}" 
               class="block px-3 py-2 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 {{ request()->routeIs('about*') ? 'bg-blue-50 dark:bg-blue-900 text-blue-600 dark:text-blue-400' : '' }}">
                Tentang
            </a>
            
            <a href="{{ route('services.index') }}" 
               class="block px-3 py-2 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 {{ request()->routeIs('services*') ? 'bg-blue-50 dark:bg-blue-900 text-blue-600 dark:text-blue-400' : '' }}">
                Layanan
            </a>
            
            <a href="{{ route('portfolio.index') }}" 
               class="block px-3 py-2 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 {{ request()->routeIs('portfolio*') ? 'bg-blue-50 dark:bg-blue-900 text-blue-600 dark:text-blue-400' : '' }}">
                Portfolio
            </a>
            
            <a href="{{ route('blog.index') }}" 
               class="block px-3 py-2 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 {{ request()->routeIs('blog*') ? 'bg-blue-50 dark:bg-blue-900 text-blue-600 dark:text-blue-400' : '' }}">
                Blog
            </a>
            
            <a href="{{ route('contact') }}" 
               class="block px-3 py-2 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 {{ request()->routeIs('contact*') ? 'bg-blue-50 dark:bg-blue-900 text-blue-600 dark:text-blue-400' : '' }}">
                Kontak
            </a>
            
            {{-- Mobile CTA Buttons --}}
            <div class="pt-4 pb-2 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('quotation.create') }}" 
                   class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors font-medium mb-2">
                    Minta Penawaran
                </a>
                
                @auth
                    <a href="{{ route('dashboard') }}" 
                       class="block text-center text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="block text-center text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        Masuk
                    </a>
                @endauth
            </div>
        </div>
    </div>
</header>

<script>
    // Mobile menu toggle
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
        const mobileMenu = document.getElementById('mobile-menu');
        mobileMenu.classList.toggle('hidden');
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        
        if (!mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
            mobileMenu.classList.add('hidden');
        }
    });
</script>