{{-- resources/views/components/layouts/client.blade.php --}}
@props([
    'title' => 'Dashboard',
    'enableCharts' => false,
    'unreadMessages' => 0,
    'pendingApprovals' => 0,
    'recentNotifications' => null,
    'unreadNotificationsCount' => 0,
    'unreadMessagesCount' => 0,
    'pendingApprovalsCount' => 0,
    'overdueProjectsCount' => 0
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="auth-user-id" content="{{ auth()->id() }}">
        <meta name="is-admin" content="{{ auth()->user()->hasRole(['admin', 'super-admin']) ? 'true' : 'false' }}">
    @endauth

    <title>{{ $title }} - Client Panel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom Styles -->
    @stack('styles')

    <!-- Initial theme check -->
    <script>
        // Quick theme check before page load to prevent flashing
        const html = document.querySelector('html');
        const isLightOrAuto = localStorage.getItem('hs_theme') === 'light' || (localStorage.getItem('hs_theme') ===
            'auto' && !window.matchMedia('(prefers-color-scheme: dark)').matches);
        const isDarkOrAuto = localStorage.getItem('hs_theme') === 'dark' || (localStorage.getItem('hs_theme') === 'auto' &&
            window.matchMedia('(prefers-color-scheme: dark)').matches);

        if (isLightOrAuto && html.classList.contains('dark')) html.classList.remove('dark');
        else if (isDarkOrAuto && html.classList.contains('light')) html.classList.remove('light');
        else if (isDarkOrAuto && !html.classList.contains('dark')) html.classList.add('dark');
        else if (isLightOrAuto && !html.classList.contains('light')) html.classList.add('light');
    </script>

    @if ($enableCharts)
        <!-- Apexcharts -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts/dist/apexcharts.css">
        <style type="text/css">
            .apexcharts-tooltip.apexcharts-theme-light {
                background-color: transparent !important;
                border: none !important;
                box-shadow: none !important;
            }
        </style>
    @endif
</head>

<body class="bg-gray-50 dark:bg-neutral-900">
    <!-- ========== HEADER ========== -->
    <x-client.client-header 
            :unreadMessagesCount="$unreadMessagesCount ?? $unreadMessages ?? 0" 
            :pendingQuotationsCount="$pendingApprovalsCount ?? $pendingApprovals ?? 0"
            :recentNotifications="$recentNotifications ?? collect()"
            :unreadNotificationsCount="$unreadNotificationsCount ?? 0"
            :pendingApprovalsCount="$pendingApprovalsCount ?? $pendingApprovals ?? 0"
            :overdueProjectsCount="$overdueProjectsCount ?? 0"
    />
    <!-- ========== END HEADER ========== -->

    <!-- ========== MAIN CONTENT ========== -->
    <!-- Mobile breadcrumb -->
    <x-admin.breadcrumb-mobile />

    <!-- Sidebar -->
    <x-client.client-sidebar :unreadMessagesCount="$unreadMessages" :pendingApprovalsCount="$pendingApprovals" />

    <!-- Content -->
    <div class="w-full lg:ps-64">
        <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
            <!-- Flash Messages -->
            @if (session('success'))
                <x-admin.alert type="success" class="mb-4" dismissible>
                    {{ session('success') }}
                </x-admin.alert>
            @endif

            @if (session('error'))
                <x-admin.alert type="error" class="mb-4" dismissible>
                    {{ session('error') }}
                </x-admin.alert>
            @endif

            @if (session('info'))
                <x-admin.alert type="info" class="mb-4" dismissible>
                    {{ session('info') }}
                </x-admin.alert>
            @endif

            @if (session('warning'))
                <x-admin.alert type="warning" class="mb-4" dismissible>
                    {{ session('warning') }}
                </x-admin.alert>
            @endif

            <!-- Page Content -->
            {{ $slot }}
        </div>
    </div>
    @auth
    <!-- Include chat widget only for authenticated users -->
    <x-chat-widget 
            :auto-open="false"
            theme="primary"
            position="bottom-right"
            :enable-sound="true"
            :show-online-status="true"
            :polling-interval="2000"
        />
    @endauth
    
    <!-- ========== END MAIN CONTENT ========== -->
<script>
        // Hide loading screen when page is ready
        document.addEventListener('DOMContentLoaded', function() {
            const loadingScreen = document.getElementById('loading-screen');
            if (loadingScreen) {
                loadingScreen.style.display = 'none';
            }
        });

        // Global error handler
        window.addEventListener('error', function(e) {
            console.error('Global error:', e.error);
        });

        // Global unhandled promise rejection handler
        window.addEventListener('unhandledrejection', function(e) {
            console.error('Unhandled promise rejection:', e.reason);
        });
    </script>

    @stack('scripts')
</body>
</html>