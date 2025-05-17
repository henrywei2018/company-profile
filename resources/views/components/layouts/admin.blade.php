<!-- resources/views/components/layouts/admin.blade.php -->
@props(['title' => config('app.name')])

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
        // On page load or when changing themes, check if the user has a preference
        const html = document.querySelector('html');
        const isLightOrAuto = localStorage.getItem('hs_theme') === 'light' || (localStorage.getItem('hs_theme') === 'auto' && !window.matchMedia('(prefers-color-scheme: dark)').matches);
        const isDarkOrAuto = localStorage.getItem('hs_theme') === 'dark' || (localStorage.getItem('hs_theme') === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);

        if (isLightOrAuto && html.classList.contains('dark')) html.classList.remove('dark');
        else if (isDarkOrAuto && html.classList.contains('light')) html.classList.remove('light');
        else if (isDarkOrAuto && !html.classList.contains('dark')) html.classList.add('dark');
        else if (isLightOrAuto && !html.classList.contains('light')) html.classList.add('light');
    </script>
</head>
<body class="bg-gray-50 dark:bg-neutral-900">
    <!-- ========== HEADER ========== -->
    <x-admin.admin-header :unreadMessagesCount="$unreadMessages ?? 0"></x-admin.admin-header>
    <!-- ========== END HEADER ========== -->

    <!-- ========== MAIN CONTENT ========== -->
    <!-- Breadcrumb - Mobile Only -->
    <x-admin.breadcrumb-mobile></x-admin.breadcrumb-mobile>

    <!-- Sidebar -->
    <x-admin.admin-sidebar 
        :unreadMessagesCount="$unreadMessages ?? 0" 
        :pendingQuotationsCount="$pendingQuotations ?? 0">
    </x-admin.admin-sidebar>

    <!-- Content -->    
    <div class="w-full lg:ps-64">
        <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
            <!-- Flash Messages -->
            @if(session('success'))
                <x-alert type="success" class="mb-4" dismissible>
                    {{ session('success') }}
                </x-alert>
            @endif
            
            @if(session('error'))
                <x-alert type="error" class="mb-4" dismissible>
                    {{ session('error') }}
                </x-alert>
            @endif
            
            @if(session('info'))
                <x-alert type="info" class="mb-4" dismissible>
                    {{ session('info') }}
                </x-alert>
            @endif
            
            @if(session('warning'))
                <x-alert type="warning" class="mb-4" dismissible>
                    {{ session('warning') }}
                </x-alert>
            @endif
            
            <!-- Page Content -->
            {{ $slot }}
        </div>
    </div>
    <!-- ========== END MAIN CONTENT ========== -->

    <!-- JS Implementing Plugins -->
    <script src="https://cdn.jsdelivr.net/npm/preline/dist/index.js"></script>
    
    @if($enableCharts ?? false)
    <!-- Charts Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/lodash/lodash.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts/dist/apexcharts.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/preline/dist/helper-apexcharts.js"></script>
    @endif
    
    @stack('scripts')
</body>
</html>