{{-- resources/views/components/layouts/public.blade.php --}}
@props([
    'title' => null,
    'description' => null,
    'keywords' => null,
    'image' => null,
    'type' => 'website',
    'breadcrumbs' => null,
    'bodyClass' => '',
    'showHeader' => true,
    'showFooter' => true,
    'noindex' => false
]) 

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO Meta Tags --}}
    <title>{{ $title ?? ($autoSeo['title'] ?? $siteConfig['site_title']) }}</title>
    <meta name="description" content="{{ $description ?? ($autoSeo['description'] ?? $siteConfig['site_description']) }}">
    <meta name="keywords" content="{{ $keywords ?? ($autoSeo['keywords'] ?? $siteConfig['site_keywords']) }}">
    <meta name="author" content="{{ $companyProfile->company_name ?? config('app.name') }}">
    
    {{-- Robots Meta --}}
    @if($noindex)
    <meta name="robots" content="noindex, nofollow">
    @else
    <meta name="robots" content="index, follow">
    @endif

    {{-- Open Graph Meta Tags --}}
    <meta property="og:title" content="{{ $title ?? ($autoSeo['title'] ?? $siteConfig['site_title']) }}">
    <meta property="og:description" content="{{ $description ?? ($autoSeo['description'] ?? $siteConfig['site_description']) }}">
    <meta property="og:image" content="{{ $image ?? ($autoSeo['image'] ?? asset($siteConfig['site_logo'])) }}">
    <meta property="og:url" content="{{ $autoSeo['url'] ?? request()->url() }}">
    <meta property="og:type" content="{{ $type ?? ($autoSeo['type'] ?? 'website') }}">
    <meta property="og:site_name" content="{{ $companyProfile->company_name ?? config('app.name') }}">

    {{-- Twitter Meta Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?? ($autoSeo['title'] ?? $siteConfig['site_title']) }}">
    <meta name="twitter:description" content="{{ $description ?? ($autoSeo['description'] ?? $siteConfig['site_description']) }}">
    <meta name="twitter:image" content="{{ $image ?? ($autoSeo['image'] ?? asset($siteConfig['site_logo'])) }}">

    {{-- Canonical URL --}}
    <link rel="canonical" href="{{ $autoSeo['url'] ?? request()->url() }}">

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset($siteConfig['site_favicon'] ?? 'favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset($siteConfig['site_logo'] ?? 'images/logo.png') }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    {{-- Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite('resources/css/orange-theme.css')
    
    {{-- Additional Styles --}}
    @stack('styles')

    {{-- Analytics --}}
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-VYHSLQXJE5"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
    
      gtag('config', 'G-VYHSLQXJE5');
    </script>

    @if($siteConfig['facebook_pixel'] ?? false)
        <!-- Facebook Pixel -->
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ $siteConfig['facebook_pixel'] }}');
            fbq('track', 'PageView');
        </script>
        <noscript>
            <img height="1" width="1" style="display:none" 
                 src="https://www.facebook.com/tr?id={{ $siteConfig['facebook_pixel'] }}&ev=PageView&noscript=1"/>
        </noscript>
    @endif

    {{-- Global JavaScript Variables --}}
    <script>
        window.App = {
            name: '{{ config("app.name") }}',
            url: '{{ config("app.url") }}',
            locale: '{{ app()->getLocale() }}',
            csrf_token: '{{ csrf_token() }}',
            company: {
                name: '{{ $companyProfile->company_name ?? config("app.name") }}',
                phone: '{{ $contactInfo["phone"] ?? "" }}',
                email: '{{ $contactInfo["email"] ?? "" }}',
                whatsapp: '{{ $socialMedia["whatsapp"] ?? "" }}'
            },
            routes: {
                home: '{{ route("home") }}',
                contact: '{{ route("contact.index") }}',
                services: '{{ route("services.index") }}',
                portfolio: '{{ route("portfolio.index") }}',
                quotation: '{{ route("quotation.create") }}'
            }
        };
    </script>
    
</head>

<body class="bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 {{ $bodyClass }}">
    {{-- Site Announcement Banner --}}
    @if($announcementBanner && $announcementBanner['message'])
        <div id="announcement-banner" class="bg-blue-600 text-white py-2 px-4 text-center text-sm">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <div class="flex-1 text-center">
                    {!! $announcementBanner['message'] !!}
                </div>
                @if($announcementBanner['dismissible'] ?? true)
                <button onclick="closeAnnouncement()" class="ml-4 text-white hover:text-gray-200 transition-colors duration-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                @endif
            </div>
        </div>
    @endif

    {{-- Header --}}
    @if($showHeader)
        <x-public.header />
    @endif

    {{-- Breadcrumb --}}
    @if($breadcrumbs && count($breadcrumbs) > 1)
        <x-public.breadcrumb :items="$breadcrumbs" />
    @endif

    {{-- Main Content --}}
    <main class="min-h-screen">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    @if($showFooter)
        <x-public.footer />
    @endif

    {{-- WhatsApp Floating Button --}}
    @if($socialMedia['whatsapp'] ?? $contactInfo['whatsapp'] ?? false)
        <x-public.whatsapp-button 
            :number="$socialMedia['whatsapp'] ?? $contactInfo['whatsapp']"
            :message="'Hello! I would like to inquire about services from ' . ($companyProfile->company_name ?? config('app.name')) . '.'" />
    @endif

    {{-- Scroll to Top Button --}}
    <x-public.scroll-to-top />

    {{-- Scripts --}}
    @stack('scripts')

    {{-- Common Scripts --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Close announcement banner
            window.closeAnnouncement = function() {
                const banner = document.getElementById('announcement-banner');
                if (banner) {
                    banner.style.display = 'none';
                    localStorage.setItem('announcement_closed_{{ date("Y-m-d") }}', 'true');
                }
            };

            // Check if announcement was closed today
            const announcementKey = 'announcement_closed_{{ date("Y-m-d") }}';
            if (localStorage.getItem(announcementKey)) {
                const banner = document.getElementById('announcement-banner');
                if (banner) banner.style.display = 'none';
            }

            // Smooth scroll for anchor links
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


            // Add loading class removal for images
            const images = document.querySelectorAll('img');
            images.forEach(img => {
                if (img.complete) {
                    img.classList.add('loaded');
                } else {
                    img.addEventListener('load', function() {
                        this.classList.add('loaded');
                    });
                }
            });

            // Add error handling for missing images
            images.forEach(img => {
                img.addEventListener('error', function() {
                    this.style.display = 'none';
                });
            });

            // Performance optimization: Preload critical resources
            if ('requestIdleCallback' in window) {
                requestIdleCallback(() => {
                    // Preload important routes
                    const criticalRoutes = [
                        '{{ route("services.index") }}',
                        '{{ route("portfolio.index") }}',
                        '{{ route("contact.index") }}'
                    ];
                    
                    criticalRoutes.forEach(url => {
                        const link = document.createElement('link');
                        link.rel = 'prefetch';
                        link.href = url;
                        document.head.appendChild(link);
                    });
                });
            }

            // Add intersection observer for animations
            if ('IntersectionObserver' in window) {
                const animationObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('animate-in');
                            animationObserver.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.1 });

                // Observe elements with animation classes
                document.querySelectorAll('[class*="animate-"]').forEach(el => {
                    animationObserver.observe(el);
                });
            }

            // Add scroll direction detection
            let lastScrollY = window.scrollY;
            window.addEventListener('scroll', () => {
                const currentScrollY = window.scrollY;
                const direction = currentScrollY > lastScrollY ? 'down' : 'up';
                
                document.body.setAttribute('data-scroll-direction', direction);
                lastScrollY = currentScrollY;
            }, { passive: true });

            // Add click outside handler utility
            window.clickOutside = function(element, callback) {
                document.addEventListener('click', function(event) {
                    if (!element.contains(event.target)) {
                        callback();
                    }
                });
            };

            // Add debounce utility function
            window.debounce = function(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            };
        });

        // Handle online/offline status
        window.addEventListener('online', () => {
            document.body.classList.remove('offline');
            console.log('✅ Back online');
        });

        window.addEventListener('offline', () => {
            document.body.classList.add('offline');
            console.log('📵 You are offline');
        });

        // Add resize handler for responsive adjustments
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                window.dispatchEvent(new CustomEvent('debouncedResize'));
            }, 150);
        });
    </script>

    {{-- Additional CSS for loading states and offline mode --}}
    <style>
        /* Loading states */
        img {
            transition: opacity 0.3s ease;
        }
        
        img:not(.loaded) {
            opacity: 0.7;
        }
        
        img.loaded {
            opacity: 1;
        }

        /* Offline indicator */
        body.offline::before {
            content: '📵 You are currently offline';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #ef4444;
            color: white;
            text-align: center;
            padding: 0.5rem;
            font-size: 0.875rem;
            z-index: 9999;
        }

        /* Scroll direction classes */
        body[data-scroll-direction="down"] .scroll-hide {
            transform: translateY(-100%);
        }
        
        body[data-scroll-direction="up"] .scroll-show {
            transform: translateY(0);
        }

        /* Print styles */
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                background: white !important;
                color: black !important;
            }
        }

        /* High contrast mode support */
        @media (prefers-contrast: high) {
            * {
                text-shadow: none !important;
                box-shadow: none !important;
            }
        }

        /* Reduced motion support */
        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
</body>
</html>