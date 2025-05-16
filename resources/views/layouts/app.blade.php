<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'CV Usaha Prima Lestari'))</title>
    <meta name="description" content="@yield('meta_description', 'CV Usaha Prima Lestari - Professional construction and general supplier company')">
    <meta name="keywords" content="@yield('meta_keywords', 'construction, supplier, building, renovation, Indonesia')">
    
    <!-- Open Graph / Social Media Meta Tags -->
    <meta property="og:title" content="@yield('og_title', config('app.name', 'CV Usaha Prima Lestari'))">
    <meta property="og:description" content="@yield('og_description', 'Professional construction and general supplier company')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.jpg'))">
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Preline CSS (via CDN) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/preline/dist/preline.min.css" />
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Custom Styles -->
    @stack('styles')
</head>
<body class="bg-gray-50 dark:bg-slate-900 min-h-screen flex flex-col">
    <div id="app" class="flex flex-col min-h-screen">
        <!-- Header -->
        @include('partials.header')

        <!-- Main Content -->
        <main class="flex-grow">
            @yield('content')
        </main>

        <!-- Footer -->
        @include('partials.footer')
    </div>

    <!-- Back to top button -->
    <button type="button" id="back-to-top" class="hs-back-to-top hs-btn-up hidden fixed z-10 right-5 bottom-5 p-3 rounded-full bg-amber-600 text-white shadow-sm">
        <svg class="w-3 h-3" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M8 12.6666L8 3.33325" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <path d="M3.5 7.83325L8 3.33325L12.5 7.83325" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>

    <!-- Flash Messages -->
    @if(session('success'))
    <div id="flash-success" class="fixed bottom-4 right-4 max-w-xs bg-green-50 border border-green-200 text-sm text-green-800 rounded-lg p-4 dark:bg-green-800/10 dark:border-green-900 dark:text-green-500 z-50" role="alert">
        <div class="flex items-center gap-3">
            <span class="flex-shrink-0 inline-flex justify-center items-center w-8 h-8 rounded-full border-2 border-green-500 bg-green-100 text-green-500 dark:bg-green-800/20">
                <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </span>
            <div class="flex-1">{{ session('success') }}</div>
            <button type="button" class="inline-flex justify-center items-center w-6 h-6 text-green-600 rounded-md hover:bg-green-50" data-dismiss-target="#flash-success">
                <span class="sr-only">Dismiss</span>
                <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div id="flash-error" class="fixed bottom-4 right-4 max-w-xs bg-red-50 border border-red-200 text-sm text-red-800 rounded-lg p-4 dark:bg-red-800/10 dark:border-red-900 dark:text-red-500 z-50" role="alert">
        <div class="flex items-center gap-3">
            <span class="flex-shrink-0 inline-flex justify-center items-center w-8 h-8 rounded-full border-2 border-red-500 bg-red-100 text-red-500 dark:bg-red-800/20">
                <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </span>
            <div class="flex-1">{{ session('error') }}</div>
            <button type="button" class="inline-flex justify-center items-center w-6 h-6 text-red-600 rounded-md hover:bg-red-50" data-dismiss-target="#flash-error">
                <span class="sr-only">Dismiss</span>
                <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>
    </div>
    @endif

    <!-- Preline JS (via CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/preline/dist/preline.min.js"></script>
    
    <!-- Scripts -->
    <script>

        const HSThemeAppearance = {
        init() {
            const defaultTheme = 'light';
            let theme = localStorage.getItem('hs_theme') || defaultTheme;
            
            if (theme === 'auto') {
            theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            
            this.setAppearance(theme);
        },
        _resetStylesOnLoad() {
            const $resetStyles = document.createElement('style');
            $resetStyles.innerText = `*{transition: unset !important;}`;
            $resetStyles.setAttribute('data-hs-appearance-onload-styles', '');
            document.head.appendChild($resetStyles);
            return $resetStyles;
        },
        setAppearance(theme, saveInStore = true, dispatchEvent = true) {
            const $resetStylesEl = this._resetStylesOnLoad();

            if (saveInStore) {
            localStorage.setItem('hs_theme', theme);
            }

            if (theme === 'auto') {
            theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }

            document.documentElement.classList.remove('dark', 'light');
            document.documentElement.classList.add(theme);
            
            setTimeout(() => {
            $resetStylesEl.remove();
            });

            if (dispatchEvent) {
            window.dispatchEvent(new CustomEvent('on-hs-appearance-change', {detail: theme}));
            }
        },
        getAppearance() {
            let theme = this.getOriginalAppearance();
            if (theme === 'auto') {
            theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            return theme;
        },
        getOriginalAppearance() {
            return localStorage.getItem('hs_theme') || 'light';
        }
        };
        HSThemeAppearance.init();

        // Initialize Preline
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize all Preline components 
            HSStaticMethods.autoInit();
            
            // Auto-hide flash messages after 5 seconds
            setTimeout(function() {
                const flashSuccess = document.getElementById('flash-success');
                const flashError = document.getElementById('flash-error');
                
                if (flashSuccess) {
                    flashSuccess.style.opacity = '0';
                    setTimeout(() => flashSuccess.remove(), 300);
                }
                
                if (flashError) {
                    flashError.style.opacity = '0';
                    setTimeout(() => flashError.remove(), 300);
                }
            }, 5000);
        });
        document.addEventListener('DOMContentLoaded', () => {
        const $themeTogglers = document.querySelectorAll('[data-hs-theme-click-value]');
        
        $themeTogglers.forEach(($toggler) => {
            $toggler.addEventListener('click', () => {
            const theme = $toggler.getAttribute('data-hs-theme-click-value');
            HSThemeAppearance.setAppearance(theme);
            });
        });
        });

        
    </script>
    
    @stack('scripts')
</body>
</html>