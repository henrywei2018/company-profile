{{-- resources/views/components/public/header.blade.php - CLEAN VERSION --}}
@props([
    'variant' => 'default', // default, transparent, dark, gradient
    'sticky' => true,
    'announcementBanner' => null,
    'companyProfile' => null
])

@php
    $headerClasses = match($variant) {
        'transparent' => 'absolute top-0 inset-x-0 z-50 bg-transparent backdrop-blur-sm',
        'gradient' => 'bg-gradient-to-r from-orange-500 via-amber-500 to-yellow-500',
        'dark' => 'bg-gradient-to-r from-orange-900 via-amber-900 to-orange-800',
        default => 'bg-white/95 backdrop-blur-sm border-b border-orange-100/50 shadow-sm'
    };
    
    if ($sticky) {
        $headerClasses .= ' sticky top-0';
    }
    
    // Get company profile if not provided
    if (!$companyProfile) {
        $companyProfile = \App\Models\CompanyProfile::getInstance();
    }
@endphp

{{-- Announcement Banner --}}
@if($announcementBanner)
<div class="py-2 bg-gradient-to-r from-orange-600 via-amber-600 to-orange-500 text-center relative overflow-hidden">
    <div class="absolute inset-0 bg-black/10"></div>
    <div class="max-w-7xl px-4 sm:px-6 lg:px-8 mx-auto relative z-10">
        <p class="text-sm text-white font-medium">
            {{ $announcementBanner }}
        </p>
    </div>
</div>
@endif

{{-- Main Header --}}
<header class="flex flex-wrap lg:justify-start lg:flex-nowrap z-50 w-full py-4 lg:py-6 {{ $headerClasses }}">
    <nav class="relative max-w-7xl w-full flex flex-wrap lg:grid lg:grid-cols-12 basis-full items-center px-4 md:px-6 lg:px-8 mx-auto">
        
        {{-- Logo Section --}}
        <div class="lg:col-span-3 flex items-center">
            <a class="flex-none rounded-xl text-xl inline-block font-bold focus:outline-none focus:opacity-80 group" 
               href="{{ route('home') }}" 
               aria-label="{{ $companyProfile->company_name ?? config('app.name') }}">
                
                @if($companyProfile->logo_url)
                    <img src="{{ $companyProfile->logo_url }}" 
                         alt="{{ $companyProfile->company_name ?? config('app.name') }}"
                         class="h-8 lg:h-10 w-auto group-hover:scale-105 transition-transform duration-300">
                @else
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl group-hover:scale-105 transition-all duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold gradient-text">
                            {{ $companyProfile->company_name ?? config('app.name') }}
                        </span>
                    </div>
                @endif
            </a>
        </div>

        {{-- Navigation Menu --}}
        <div class="lg:col-span-6 hidden lg:block">
            <div class="flex justify-center">
                <div class="flex items-center gap-x-1">
                    {{-- Home Link --}}
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Home
                    </a>

                    {{-- About Dropdown --}}
                    <div class="hs-dropdown relative inline-flex">
                        <button type="button" 
                                class="nav-link hs-dropdown-toggle {{ request()->routeIs('about.*') ? 'active' : '' }}"
                                aria-haspopup="menu" 
                                aria-expanded="false" 
                                aria-label="About Dropdown">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            About
                            <svg class="hs-dropdown-open:rotate-180 ms-1 shrink-0 size-4 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m6 9 6 6 6-6"/>
                            </svg>
                        </button>

                        <div class="hs-dropdown-menu transition-[opacity,margin] duration-300 hs-dropdown-open:opacity-100 opacity-0 min-w-60 z-10 bg-white shadow-lg border border-orange-100 rounded-xl p-2 mt-2 hidden"
                             role="menu" 
                             aria-orientation="vertical">
                            <a class="dropdown-item {{ request()->routeIs('about.index') ? 'active' : '' }}" 
                               href="{{ route('about.index') }}" 
                               role="menuitem">
                                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Company Profile
                            </a>
                            
                            @if(Route::has('about.team'))
                            <a class="dropdown-item {{ request()->routeIs('about.team') ? 'active' : '' }}" 
                               href="{{ route('about.team') }}" 
                               role="menuitem">
                                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Our Team
                            </a>
                            @endif
                            
                            @if(Route::has('about.history'))
                            <a class="dropdown-item {{ request()->routeIs('about.history') ? 'active' : '' }}" 
                               href="{{ route('about.history') }}" 
                               role="menuitem">
                                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Our History
                            </a>
                            @endif
                        </div>
                    </div>

                    {{-- Services Link --}}
                    @if(Route::has('services.index'))
                    <a class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}" href="{{ route('services.index') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                        Services
                    </a>
                    @endif

                    {{-- Portfolio Link --}}
                    @if(Route::has('portfolio.index'))
                    <a class="nav-link {{ request()->routeIs('portfolio.*') ? 'active' : '' }}" href="{{ route('portfolio.index') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        Portfolio
                    </a>
                    @endif

                    {{-- Blog Link --}}
                    @if(Route::has('blog.index'))
                    <a class="nav-link {{ request()->routeIs('blog.*') ? 'active' : '' }}" href="{{ route('blog.index') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                        Blog
                    </a>
                    @endif

                    {{-- Contact Link --}}
                    @if(Route::has('contact.index'))
                    <a class="nav-link {{ request()->routeIs('contact.*') ? 'active' : '' }}" href="{{ route('contact.index') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Contact
                    </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Side Actions --}}
        <div class="lg:col-span-3 flex justify-end items-center gap-x-3">
            {{-- Theme Toggle --}}
            <button id="theme-toggle"
                    class="size-9 flex justify-center items-center rounded-xl transition-all duration-300"
                    aria-label="Toggle dark mode">
                {{-- Sun Icon (Light Mode) --}}
                <svg class="hidden dark:block size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
                {{-- Moon Icon (Dark Mode) --}}
                <svg class="block dark:hidden size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
                </svg>
            </button>

            {{-- Mobile Menu Toggle --}}
            <div class="lg:hidden">
                <button type="button"
                        class="hs-collapse-toggle size-9 flex justify-center items-center rounded-xl transition-all duration-300"
                        data-hs-collapse="#hs-navbar-collapse-with-animation"
                        aria-controls="hs-navbar-collapse-with-animation"
                        aria-label="Toggle navigation">
                    <svg class="hs-collapse-open:hidden shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" x2="21" y1="6" y2="6"/>
                        <line x1="3" x2="21" y1="12" y2="12"/>
                        <line x1="3" x2="21" y1="18" y2="18"/>
                    </svg>
                    <svg class="hs-collapse-open:block hidden shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m18 6-12 12"/>
                        <path d="m6 6 12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div id="hs-navbar-collapse-with-animation" 
             class="hs-collapse hidden overflow-hidden transition-all duration-300 basis-full grow lg:hidden">
            <div class="flex flex-col gap-y-4 gap-x-0 mt-5 divide-y divide-orange-100">
                {{-- Mobile Navigation Links --}}
                <div class="space-y-2">
                    <a class="mobile-nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Home
                    </a>
                    
                    <a class="mobile-nav-link {{ request()->routeIs('about.*') ? 'active' : '' }}" href="{{ route('about.index') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        About
                    </a>
                    
                    @if(Route::has('services.index'))
                    <a class="mobile-nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}" href="{{ route('services.index') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                        Services
                    </a>
                    @endif
                    
                    @if(Route::has('portfolio.index'))
                    <a class="mobile-nav-link {{ request()->routeIs('portfolio.*') ? 'active' : '' }}" href="{{ route('portfolio.index') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        Portfolio
                    </a>
                    @endif
                    
                    @if(Route::has('blog.index'))
                    <a class="mobile-nav-link {{ request()->routeIs('blog.*') ? 'active' : '' }}" href="{{ route('blog.index') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                        Blog
                    </a>
                    @endif
                    
                    @if(Route::has('contact.index'))
                    <a class="mobile-nav-link {{ request()->routeIs('contact.*') ? 'active' : '' }}" href="{{ route('contact.index') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Contact
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>
</header>