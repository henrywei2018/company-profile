{{-- resources/views/components/public/header.blade.php --}}
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
            {{-- Logo --}}
            <a class="flex-none rounded-xl text-xl inline-block font-bold focus:outline-none focus:opacity-80 group" 
               href="{{ route('home') }}" 
               aria-label="{{ $companyProfile->company_name ?? config('app.name') }}">
                
                @if($companyProfile->logo_url)
                    <img src="{{ $companyProfile->logo_url }}" 
                         alt="{{ $companyProfile->company_name ?? config('app.name') }}"
                         class="h-8 lg:h-10 w-auto group-hover:scale-105 transition-transform duration-300">
                @else
                    {{-- Default Logo with Orange Gradient --}}
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl group-hover:scale-105 transition-all duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold bg-gradient-to-r from-orange-600 to-amber-600 bg-clip-text text-transparent">
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
                        Home
                    </a>

                    {{-- About Dropdown --}}
                    <div class="hs-dropdown relative inline-flex">
                        <button type="button" 
                                class="nav-link hs-dropdown-toggle {{ request()->routeIs('about*') ? 'active' : '' }}" 
                                id="hs-dropdown-about"
                                aria-haspopup="menu" 
                                aria-expanded="false" 
                                aria-label="About Menu">
                            About
                            <svg class="hs-dropdown-open:rotate-180 size-4 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m6 9 6 6 6-6"/>
                            </svg>
                        </button>

                        <div class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden min-w-60 bg-white/95 backdrop-blur-sm shadow-xl rounded-2xl border border-orange-100/50 p-2 mt-2" role="menu" aria-orientation="vertical" aria-labelledby="hs-dropdown-about">
                            <a class="dropdown-item {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-orange-100 to-amber-100 rounded-xl flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">About Us</div>
                                        <div class="text-xs text-gray-500">Learn about our company</div>
                                    </div>
                                </div>
                            </a>
                            
                            @if(Route::has('about.team'))
                            <a class="dropdown-item {{ request()->routeIs('about.team') ? 'active' : '' }}" href="{{ route('about.team') }}">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-orange-100 to-amber-100 rounded-xl flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Our Team</div>
                                        <div class="text-xs text-gray-500">Meet our professionals</div>
                                    </div>
                                </div>
                            </a>
                            @endif
                        </div>
                    </div>

                    {{-- Services Dropdown --}}
                    @if(Route::has('services'))
                    <div class="hs-dropdown relative inline-flex">
                        <button type="button" 
                                class="nav-link hs-dropdown-toggle {{ request()->routeIs('services*') ? 'active' : '' }}" 
                                id="hs-dropdown-services"
                                aria-haspopup="menu" 
                                aria-expanded="false" 
                                aria-label="Services Menu">
                            Services
                            <svg class="hs-dropdown-open:rotate-180 size-4 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m6 9 6 6 6-6"/>
                            </svg>
                        </button>

                        <div class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden min-w-80 bg-white/95 backdrop-blur-sm shadow-xl rounded-2xl border border-orange-100/50 p-2 mt-2" role="menu" aria-orientation="vertical" aria-labelledby="hs-dropdown-services">
                            {{-- Featured Services Grid --}}
                            <div class="grid grid-cols-2 gap-2">
                                <a class="dropdown-item" href="{{ route('services') }}">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-orange-100 to-amber-100 rounded-xl flex items-center justify-center mr-3">
                                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">Web Development</div>
                                            <div class="text-xs text-gray-500">Custom web solutions</div>
                                        </div>
                                    </div>
                                </a>
                                
                                <a class="dropdown-item" href="{{ route('services') }}">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-orange-100 to-amber-100 rounded-xl flex items-center justify-center mr-3">
                                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a1 1 0 001-1V4a1 1 0 00-1-1H8a1 1 0 00-1 1v16a1 1 0 001 1z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">Mobile Apps</div>
                                            <div class="text-xs text-gray-500">iOS & Android apps</div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            
                            <div class="border-t border-orange-100 mt-3 pt-3">
                                <a class="w-full text-center py-2 px-4 bg-gradient-to-r from-orange-500 to-amber-500 text-white rounded-xl font-medium hover:from-orange-600 hover:to-amber-600 transition-all duration-300 inline-block" href="{{ route('services') }}">
                                    View All Services
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Portfolio Link --}}
                    @if(Route::has('portfolio'))
                    <a class="nav-link {{ request()->routeIs('portfolio*') ? 'active' : '' }}" href="{{ route('portfolio') }}">
                        Portfolio
                    </a>
                    @endif

                    {{-- Blog Link --}}
                    @if(Route::has('blog'))
                    <a class="nav-link {{ request()->routeIs('blog*') ? 'active' : '' }}" href="{{ route('blog') }}">
                        Blog
                    </a>
                    @endif

                    {{-- Contact Link --}}
                    @if(Route::has('contact'))
                    <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">
                        Contact
                    </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Section - CTA & Mobile Menu --}}
        <div class="lg:col-span-3 flex justify-end items-center gap-x-2">
            {{-- Theme Toggle --}}
            <button type="button" 
                    class="hs-dark-mode hs-dark-mode-active:hidden block size-9 flex justify-center items-center text-gray-600 hover:text-orange-600 rounded-xl hover:bg-orange-50 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2"
                    data-hs-theme-click-value="dark">
                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>
                </svg>
            </button>
            
            <button type="button" 
                    class="hs-dark-mode hs-dark-mode-active:block hidden size-9 flex justify-center items-center text-gray-600 hover:text-orange-600 rounded-xl hover:bg-orange-50 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2"
                    data-hs-theme-click-value="light">
                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="4"/>
                    <path d="m12 2 2 2-2 2-2-2 2-2zM12 22l2-2-2-2-2 2 2 2zM22 12l-2 2-2-2 2-2 2 2zM2 12l2-2 2 2-2 2-2-2z"/>
                </svg>
            </button>

            {{-- CTA Button --}}
            @if(Route::has('contact'))
            <div class="hidden lg:block">
                <a class="cta-button" href="{{ route('contact') }}">
                    Get Started
                    <svg class="shrink-0 size-4 group-hover:translate-x-0.5 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14"/>
                        <path d="m12 5 7 7-7 7"/>
                    </svg>
                </a>
            </div>
            @endif

            {{-- Mobile Menu Toggle --}}
            <div class="lg:hidden">
                <button type="button" 
                        class="hs-collapse-toggle size-9 flex justify-center items-center text-gray-600 hover:text-orange-600 rounded-xl hover:bg-orange-50 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2"
                        id="hs-navbar-collapse"
                        aria-expanded="false"
                        aria-controls="hs-navbar-collapse-with-animation"
                        aria-label="Toggle navigation"
                        data-hs-collapse="#hs-navbar-collapse-with-animation">
                    <svg class="hs-collapse-open:hidden shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" x2="21" y1="6" y2="6"/>
                        <line x1="3" x2="21" y1="12" y2="12"/>
                        <line x1="3" x2="21" y1="18" y2="18"/>
                    </svg>
                    <svg class="hs-collapse-open:block hidden shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                        Home
                    </a>
                    
                    <a class="mobile-nav-link {{ request()->routeIs('about*') ? 'active' : '' }}" href="{{ route('about') }}">
                        About
                    </a>
                    
                    @if(Route::has('about.team'))
                    <a class="mobile-nav-link {{ request()->routeIs('about.team') ? 'active' : '' }}" href="{{ route('about.team') }}">
                        Team
                    </a>
                    @endif
                    
                    @if(Route::has('services'))
                    <a class="mobile-nav-link {{ request()->routeIs('services*') ? 'active' : '' }}" href="{{ route('services') }}">
                        Services
                    </a>
                    @endif
                    
                    @if(Route::has('portfolio'))
                    <a class="mobile-nav-link {{ request()->routeIs('portfolio*') ? 'active' : '' }}" href="{{ route('portfolio') }}">
                        Portfolio
                    </a>
                    @endif
                    
                    @if(Route::has('blog'))
                    <a class="mobile-nav-link {{ request()->routeIs('blog*') ? 'active' : '' }}" href="{{ route('blog') }}">
                        Blog
                    </a>
                    @endif
                    
                    @if(Route::has('contact'))
                    <a class="mobile-nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">
                        Contact
                    </a>
                    @endif
                </div>

                {{-- Mobile CTA --}}
                @if(Route::has('contact'))
                <div class="pt-4">
                    <a class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 text-white hover:from-orange-600 hover:to-amber-600 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2" 
                       href="{{ route('contact') }}">
                        Get Started
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14"/>
                            <path d="m12 5 7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </nav>
</header>

@pushOnce('styles')

<style>
    /* Navigation Styles with Orange Theme */
    .nav-link {
        @apply relative px-4 py-2 text-sm font-medium text-gray-700 hover:text-orange-600 rounded-xl transition-all duration-300 flex items-center gap-x-1 group;
    }
    
    .nav-link:hover {
        @apply bg-orange-50/80 backdrop-blur-sm;
    }
    
    .nav-link.active {
        @apply text-orange-600 bg-orange-50/80 backdrop-blur-sm;
    }
    
    .nav-link.active::after {
        content: '';
        @apply absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-orange-500 rounded-full;
    }
    
    /* Dropdown Styles */
    .dropdown-item {
        @apply flex items-center w-full px-3 py-3 text-sm text-gray-700 rounded-xl hover:bg-orange-50/80 hover:text-orange-600 transition-all duration-300 group;
    }
    
    .dropdown-item.active {
        @apply text-orange-600 bg-orange-50/80;
    }
    
    /* CTA Button */
    .cta-button {
        @apply group inline-flex items-center gap-x-2 py-2.5 px-6 bg-gradient-to-r from-orange-500 to-amber-500 text-white text-sm font-semibold rounded-xl shadow-lg hover:shadow-xl hover:from-orange-600 hover:to-amber-600 transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2;
    }
    
    /* Mobile Navigation */
    .mobile-nav-link {
        @apply block px-4 py-3 text-sm font-medium text-gray-700 hover:text-orange-600 hover:bg-orange-50/80 rounded-xl transition-all duration-300;
    }
    
    .mobile-nav-link.active {
        @apply text-orange-600 bg-orange-50/80;
    }
    
    /* Backdrop Blur Support */
    @supports (backdrop-filter: blur(0)) {
    .backdrop-blur-sm {
        backdrop-filter: blur(4px);
    }
    }
    
    /* Custom Scrollbar for Dropdowns */
    .hs-dropdown-menu {
        scrollbar-width: thin;
        scrollbar-color: rgb(251 146 60) rgb(255 247 237);
    }
    
    .hs-dropdown-menu::-webkit-scrollbar {
        width: 6px;
    }
    
    .hs-dropdown-menu::-webkit-scrollbar-track {
        background: rgb(255 247 237);
        border-radius: 3px;
    }
    
    .hs-dropdown-menu::-webkit-scrollbar-thumb {
        background: rgb(251 146 60);
        border-radius: 3px;
    }
    
    .hs-dropdown-menu::-webkit-scrollbar-thumb:hover {
        background: rgb(234 88 12);
    }
</style>

@endPushOnce