<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Meta Tags -->
    <title>{{ isset($seo_title) ? $seo_title : config('app.name') }}</title>
    <meta name="description" content="{{ isset($seo_description) ? $seo_description : config('app.description', '') }}">
    <meta name="keywords" content="{{ isset($seo_keywords) ? $seo_keywords : '' }}">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="{{ isset($seo_title) ? $seo_title : config('app.name') }}">
    <meta property="og:description"
        content="{{ isset($seo_description) ? $seo_description : config('app.description', '') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ isset($og_image) ? $og_image : asset('images/og-default.jpg') }}">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ isset($seo_title) ? $seo_title : config('app.name') }}">
    <meta name="twitter:description"
        content="{{ isset($seo_description) ? $seo_description : config('app.description', '') }}">
    <meta name="twitter:image" content="{{ isset($og_image) ? $og_image : asset('images/og-default.jpg') }}">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap"
        rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom Styles -->
    @stack('styles')

    <!-- Google Analytics -->
    @if (config('services.google_analytics.tracking_id'))
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_analytics.tracking_id') }}">
        </script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());
            gtag('config', '{{ config('services.google_analytics.tracking_id') }}');
        </script>
    @endif
</head>

<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <div id="app" class="min-h-screen flex flex-col">
        <!-- Header -->
        <x-header />

        <!-- Page Content -->
        <main class="flex-grow">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <x-footer />
    </div>

    <!-- Scripts -->
    @stack('scripts')
</body>

</html>
