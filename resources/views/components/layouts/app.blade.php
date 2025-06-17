@props([
    'title' => null,
    'description' => null,
    'keywords' => null,
    'breadcrumbs' => null,
    'bodyClass' => '',
    'showHeader' => true,
    'showFooter' => true,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO Meta Tags --}}
    <title>{{ $title ?? $pageTitle ?? $globalSiteConfig['site_title'] }}</title>
    <meta name="description" content="{{ $description ?? $pageDescription ?? $globalSiteConfig['site_description'] }}">
    <meta name="keywords" content="{{ $keywords ?? $pageKeywords ?? $globalSiteConfig['site_keywords'] }}">
    <meta name="author" content="{{ $globalSiteConfig['site_name'] }}">

    {{-- Open Graph Meta Tags --}}
    <meta property="og:title" content="{{ $title ?? $pageTitle ?? $globalSiteConfig['site_title'] }}">
    <meta property="og:description" content="{{ $description ?? $pageDescription ?? $globalSiteConfig['site_description'] }}">
    <meta property="og:image" content="{{ $pageImage ?? asset($globalSiteConfig['site_logo']) }}">
    <meta property="og:url" content="{{ $pageUrl ?? request()->url() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $globalSiteConfig['site_name'] }}">

    {{-- Twitter Meta Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?? $pageTitle ?? $globalSiteConfig['site_title'] }}">
    <meta name="twitter:description" content="{{ $description ?? $pageDescription ?? $globalSiteConfig['site_description'] }}">
    <meta name="twitter:image" content="{{ $pageImage ?? asset($globalSiteConfig['site_logo']) }}">

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset($globalSiteConfig['site_favicon']) }}">
    <link rel="apple-touch-icon" href="{{ asset($globalSiteConfig['site_logo']) }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:400,500,600,700&display=swap" rel="stylesheet" />

    {{-- Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite('resources/css/orange-theme.css')
    {{-- Additional Styles --}}
    @stack('styles')

    {{-- Analytics --}}
    @if($globalSiteConfig['google_analytics'])
        <!-- Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $globalSiteConfig['google_analytics'] }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $globalSiteConfig['google_analytics'] }}');
        </script>
    @endif

    @if($globalSiteConfig['facebook_pixel'])
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
            fbq('init', '{{ $globalSiteConfig['facebook_pixel'] }}');
            fbq('track', 'PageView');
        </script>
        <noscript>
            <img height="1" width="1" style="display:none" 
                 src="https://www.facebook.com/tr?id={{ $globalSiteConfig['facebook_pixel'] }}&ev=PageView&noscript=1"/>
        </noscript>
    @endif

    {{-- Global JavaScript Variables --}}
    <script>
        window.App = {!! json_encode($globalJsVars ?? []) !!};
    </script>
</head>

<body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 {{ $bodyClass }}">
    {{-- Site Announcement Banner --}}
    @if($globalSiteConfig['show_announcement'] && $globalSiteConfig['site_announcement'])
        <div id="announcement-banner" class="bg-blue-600 text-white py-2 px-4 text-center text-sm">
            <div class="container mx-auto flex items-center justify-between">
                <div class="flex-1 text-center">
                    {!! $globalSiteConfig['site_announcement'] !!}
                </div>
                <button onclick="closeAnnouncement()" class="ml-4 text-white hover:text-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    {{-- Header --}}
    @if($showHeader)
        <x-public.header />
    @endif

    {{-- Breadcrumb --}}
    @if($breadcrumbs ?? $breadcrumb ?? false)
        <x-public.breadcrumb :items="$breadcrumbs ?? $breadcrumb" />
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
    @if($globalContactInfo['whatsapp_number'])
        <x-public.whatsapp-button 
            :number="$globalContactInfo['whatsapp_number']"
            :message="$globalContactInfo['whatsapp_message']" />
    @endif

    {{-- Scroll to Top Button --}}
    <x-public.scroll-to-top />

    {{-- Chat Widget --}}
    @if($globalSiteConfig['chat_widget'])
        {!! $globalSiteConfig['chat_widget'] !!}
    @endif

    {{-- Scripts --}}
    @stack('scripts')

    {{-- Common Scripts --}}
    <script>
        // Close announcement banner
        function closeAnnouncement() {
            document.getElementById('announcement-banner').style.display = 'none';
            localStorage.setItem('announcement_closed', 'true');
        }

        // Check if announcement was closed
        if (localStorage.getItem('announcement_closed')) {
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

        // Dark mode toggle (if implemented)
        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'));
        }

        // Load dark mode preference
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>
</body>
</html>