{{-- resources/views/components/layouts/public.blade.php - UPDATED --}}
@props([
    'title' => null,
    'description' => null,
    'keywords' => null,
    'image' => null,
    'type' => 'website',
    'model' => null,
    'noindex' => false,
    'breadcrumbs' => null,
    'headerVariant' => 'default',
    'footerVariant' => 'default',
    'showNewsletter' => true,
    'announcementBanner' => null,
    'bodyClass' => ''
])

@php
    // Auto SEO data - fallback values
    $autoSeo = [
        'title' => config('app.name'),
        'description' => 'Professional web development and digital solutions',
        'type' => 'website'
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- SEO Meta Tags Component --}}
    <x-seo.meta-tags 
        :title="$title ?? $autoSeo['title'] ?? null"
        :description="$description ?? $autoSeo['description'] ?? null"
        :keywords="$keywords"
        :image="$image"
        :type="$type ?? $autoSeo['type'] ?? 'website'"
        :model="$model"
        :noindex="$noindex"
    />
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    {{-- Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite('resources/css/orange-theme.css')

    
    {{-- Additional head content --}}
    @stack('styles')
    
    {{-- Analytics & Tracking - Head --}}
    @if(class_exists('App\View\Components\Seo\Analytics'))
        <x-seo.analytics position="head" />
    @endif
    
    {{-- Theme Detection Script --}}
    <script>
        const html = document.querySelector('html');
        const isLightOrAuto = localStorage.getItem('hs_theme') === 'light' || (localStorage.getItem('hs_theme') === 'auto' && !window.matchMedia('(prefers-color-scheme: dark)').matches);
        const isDarkOrAuto = localStorage.getItem('hs_theme') === 'dark' || (localStorage.getItem('hs_theme') === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);

        if (isLightOrAuto && html.classList.contains('dark')) html.classList.remove('dark');
        else if (isDarkOrAuto && html.classList.contains('light')) html.classList.remove('light');
        else if (isDarkOrAuto && !html.classList.contains('dark')) html.classList.add('dark');
        else if (isLightOrAuto && !html.classList.contains('light')) html.classList.add('light');
    </script>
</head>

<body class="bg-white text-gray-900 dark:bg-gray-900 dark:text-gray-100 {{ $bodyClass }}">
    {{-- Analytics & Tracking - Body --}}
    @if(class_exists('App\View\Components\Seo\Analytics'))
        <x-seo.analytics position="body" />
    @endif
    
    {{-- Page Loading Indicator --}}
    <div id="page-loader" class="fixed inset-0 z-50 bg-white dark:bg-gray-900 flex items-center justify-center transition-opacity duration-300">
        <div class="text-center">
            <div class="loading-spinner mb-4">
                <div class="w-8 h-8 border-4 border-orange-200 border-t-orange-500 rounded-full animate-spin"></div>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Loading...</p>
        </div>
    </div>
    
    {{-- Skip to Content Link --}}
    <a href="#main-content" 
       class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 btn btn-primary z-50">
        Skip to main content
    </a>
    
    {{-- Header --}}
    <x-public.header 
        :variant="$headerVariant"
        :announcement-banner="$announcementBanner"
    />
    
    {{-- Main Content --}}
    <main id="main-content" class="page-enter min-h-screen">
        {{ $slot }}
    </main>
    
    {{-- Footer --}}
    @if(class_exists('App\View\Components\Public\Footer'))
        <x-public.footer 
            :variant="$footerVariant"
            :show-newsletter="$showNewsletter"
        />
    @else
        {{-- Fallback Footer --}}
        <footer class="bg-gray-900 text-white py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <p class="text-sm text-gray-400">
                        © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>
    @endif
    
    {{-- Schema.org JSON-LD --}}
    @if(class_exists('App\View\Components\Seo\Schema'))
        <x-seo.schema 
            type="company"
            :breadcrumbs="$breadcrumbs ?? []"
        />
    @endif
    
    {{-- JavaScript --}}
    @stack('scripts')
    
    {{-- Page Transition and Loading Scripts --}}
    <script>
        // Hide loading screen when page is ready
        document.addEventListener('DOMContentLoaded', function() {
            const pageLoader = document.getElementById('page-loader');
            if (pageLoader) {
                setTimeout(() => {
                    pageLoader.style.opacity = '0';
                    setTimeout(() => {
                        pageLoader.style.display = 'none';
                    }, 300);
                }, 100);
            }
            
            // Add page enter animation
            const mainContent = document.getElementById('main-content');
            if (mainContent) {
                mainContent.classList.add('opacity-100', 'translate-y-0');
            }
        });

        // Page transition effects
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a[href]');
            if (link && !link.hasAttribute('target') && !link.hasAttribute('download')) {
                const href = link.getAttribute('href');
                if (href && href.startsWith('/') && !href.startsWith('//')) {
                    e.preventDefault();
                    
                    // Add exit animation
                    const mainContent = document.getElementById('main-content');
                    if (mainContent) {
                        mainContent.classList.add('page-exit');
                        setTimeout(() => {
                            window.location.href = href;
                        }, 150);
                    } else {
                        window.location.href = href;
                    }
                }
            }
        });

        // Smooth scroll for anchor links
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a[href^="#"]');
            if (link) {
                e.preventDefault();
                const targetId = link.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });

        // Handle back/forward browser navigation
        window.addEventListener('popstate', function() {
            const pageLoader = document.getElementById('page-loader');
            if (pageLoader) {
                pageLoader.style.display = 'flex';
                pageLoader.style.opacity = '1';
            }
            
            setTimeout(() => {
                location.reload();
            }, 100);
        });

        // Global error handler
        window.addEventListener('error', function(e) {
            console.error('Public page error:', e.error);
        });

        // Global unhandled promise rejection handler
        window.addEventListener('unhandledrejection', function(e) {
            console.error('Public page promise rejection:', e.reason);
        });
    </script>
    
    {{-- Custom CSS for page transitions --}}
    <style>
        .page-enter {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.3s ease-out, transform 0.3s ease-out;
        }
        
        .page-exit {
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity 0.15s ease-in, transform 0.15s ease-in;
        }
        
        .loading-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        /* Ensure smooth transitions */
        * {
            transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }
        
        /* Reduce motion for users who prefer it */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
            
            .page-enter,
            .page-exit {
                transition: none !important;
            }
        }
    </style>
</body>
</html>