<!-- resources/views/layouts/admin.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }} - Admin Panel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/preline.css') }}">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/preline.js') }}" defer></script>
    
    @stack('styles')
</head>
<body class="bg-gray-50 dark:bg-slate-900 h-full">
    <!-- ========== MAIN CONTENT ========== -->
    <div class="flex h-full">
        <!-- Sidebar -->
        <x-admin-sidebar :unreadMessagesCount="$unreadMessagesCount ?? 0" :pendingQuotationsCount="$pendingQuotationsCount ?? 0" />

        <!-- Content -->
        <div class="w-full min-h-screen flex flex-col">
            <!-- Top Navigation -->
            <x-admin-header :title="$title ?? 'Dashboard'">
                @if(isset($breadcrumbs))
                    <x-slot name="breadcrumbs">
                        {{ $breadcrumbs }}
                    </x-slot>
                @endif
            </x-admin-header>

            <!-- Main Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto">
                <!-- Alert messages -->
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
                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-xl p-4 sm:p-6">
                    {{ $slot }}
                </div>
            </main>
            
            <!-- Footer -->
            <x-admin-footer />
        </div>
    </div>
    <!-- ========== END MAIN CONTENT ========== -->
    
    @stack('scripts')
</body>
</html>