
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
    'announcementBanner' => null
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
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
    @vite(['resources/css/app.css', 'resources/js/app.js',])
    @vite('resources/css/orange-theme.css')

    
    {{-- Additional head content --}}
    @stack('styles')
    
    {{-- Analytics & Tracking - Head --}}
    <x-seo.analytics position="head" />
    
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

<body class="bg-white text-gray-900 {{ $bodyClass ?? '' }}">
    {{-- Analytics & Tracking - Body --}}
    <x-seo.analytics position="body" />
    
    {{-- Page Loading Indicator --}}
    <div id="page-loader" class="fixed inset-0 z-50 bg-white flex items-center justify-center">
        <div class="text-center">
            <div class="loading-spinner mb-4"></div>
            <p class="text-sm text-gray-600">Loading...</p>
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
    <main id="main-content" class="page-enter">
        {{ $slot }}
    </main>
    
    {{-- Footer --}}
    <x-public.footer 
        :variant="$footerVariant"
        :show-newsletter="$showNewsletter"
    />
    
    {{-- Schema.org JSON-LD --}}
    <x-seo.schema 
        type="company"
        :breadcrumbs="$breadcrumbs ?? null"
    />
    
    {{-- Page-specific schema --}}
    @if(isset($model))
        @if(isset($model->title) || isset($model->name))
            <x-seo.schema 
                type="article"
                :data="$model"
            />
        @endif
    @endif
    
    {{-- Scripts --}}
    @stack('scripts')
    
    {{-- Page Load Complete Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Hide loading indicator
            const loader = document.getElementById('page-loader');
            if (loader) {
                loader.style.opacity = '0';
                setTimeout(() => {
                    loader.style.display = 'none';
                }, 300);
            }
            
            // Initialize page animations
            const elements = document.querySelectorAll('[data-animate]');
            if (elements.length > 0 && 'IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const animationType = entry.target.dataset.animate;
                            entry.target.classList.add(`animate-${animationType}`);
                            observer.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                });
                
                elements.forEach(el => observer.observe(el));
            }
            
            // Initialize smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
            
            // Initialize lazy loading for images
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            if (img.dataset.src) {
                                img.src = img.dataset.src;
                                img.classList.remove('opacity-0');
                                img.classList.add('opacity-100');
                                imageObserver.unobserve(img);
                            }
                        }
                    });
                });
                
                document.querySelectorAll('img[data-src]').forEach(img => {
                    img.classList.add('opacity-0', 'transition-opacity', 'duration-300');
                    imageObserver.observe(img);
                });
            }
            
            // Performance optimization: Preload critical resources
            const criticalResources = [
                '/images/hero-bg.jpg',
                '/images/logo.png'
            ];
            
            criticalResources.forEach(resource => {
                const link = document.createElement('link');
                link.rel = 'preload';
                link.as = 'image';
                link.href = resource;
                document.head.appendChild(link);
            });
        });
        
        // Error handling for images
        document.addEventListener('error', function(e) {
            if (e.target.tagName === 'IMG') {
                e.target.src = '/images/placeholder.jpg'; // Fallback image
                e.target.classList.add('opacity-50');
            }
        }, true);
        
        // Service Worker Registration (optional)
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('SW registered: ', registration);
                    })
                    .catch(function(registrationError) {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }
    </script>
</body>
</html>