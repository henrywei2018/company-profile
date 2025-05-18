<!-- resources/views/components/layouts/admin.blade.php -->
@props(['title' => config('app.name'), 'enableCharts' => false])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }} - Admin Panel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Custom Styles -->
    @stack('styles')
    
    <!-- Theme Check and Update -->
    <script>
    const html = document.querySelector('html');
    const isLightOrAuto = localStorage.getItem('hs_theme') === 'light' || (localStorage.getItem('hs_theme') === 'auto' && !window.matchMedia('(prefers-color-scheme: dark)').matches);
    const isDarkOrAuto = localStorage.getItem('hs_theme') === 'dark' || (localStorage.getItem('hs_theme') === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);

    if (isLightOrAuto && html.classList.contains('dark')) html.classList.remove('dark');
    else if (isDarkOrAuto && html.classList.contains('light')) html.classList.remove('light');
    else if (isDarkOrAuto && !html.classList.contains('dark')) html.classList.add('dark');
    else if (isLightOrAuto && !html.classList.contains('light')) html.classList.add('light');
  </script>

    @if($enableCharts)
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
    <x-admin.admin-header :unreadMessagesCount="$unreadMessages ?? 0" />
    <!-- ========== END HEADER ========== -->

    <!-- ========== MAIN CONTENT ========== -->
    <!-- Mobile breadcrumb -->
    <x-admin.breadcrumb-mobile />

    <!-- Sidebar -->
    <x-admin.admin-sidebar 
        :unreadMessagesCount="$unreadMessages ?? 0" 
        :pendingQuotationsCount="$pendingQuotations ?? 0"
    />

    <!-- Content -->    
    <div class="w-full lg:ps-64">
        <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
            <!-- Flash Messages -->
            @if(session('success'))
                <x-admin.alert type="success" class="mb-4" dismissible>
                    {{ session('success') }}
                </x-admin.alert>
            @endif
            
            @if(session('error'))
                <x-admin.alert type="error" class="mb-4" dismissible>
                    {{ session('error') }}
                </x-admin.alert>
            @endif
            
            @if(session('info'))
                <x-admin.alert type="info" class="mb-4" dismissible>
                    {{ session('info') }}
                </x-admin.alert>
            @endif
            
            @if(session('warning'))
                <x-admin.alert type="warning" class="mb-4" dismissible>
                    {{ session('warning') }}
                </x-admin.alert>
            @endif
            
            <!-- Page Content -->
            {{ $slot }}
        </div>
    </div>
    <!-- ========== END MAIN CONTENT ========== -->

    <!-- ========== FOOTER ========== -->
    <x-admin.admin-footer />
    <!-- ========== END FOOTER ========== -->

    <!-- JS Implementing Plugins -->
    <script src="https://cdn.jsdelivr.net/npm/preline/dist/index.js"></script>
    
    @if($enableCharts)
    <!-- Charts Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/lodash/lodash.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts/dist/apexcharts.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/preline/dist/helper-apexcharts.js"></script>
    @endif
    
    <!-- Theme toggle script -->
    <script>
        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                // Switch to light mode
                document.documentElement.classList.remove('dark');
                localStorage.setItem('hs_theme', 'light');
            } else {
                // Switch to dark mode
                document.documentElement.classList.add('dark');
                localStorage.setItem('hs_theme', 'dark');
            }
        }
    </script>
    
    @stack('scripts')
</body>
</html>