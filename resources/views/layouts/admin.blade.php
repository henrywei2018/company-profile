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
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom Styles -->
    @stack('styles')
    
    <!-- Dark mode detection script - must be in head to avoid FOUC -->
    <script>
        // On page load or when changing themes, check if the user has a preference
        if (localStorage.getItem('hs_theme') === 'dark' || 
            (!('hs_theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
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
                    @yield('content')
                </div>
            </main>
            
            <!-- Footer -->
            <x-admin-footer />
        </div>
    </div>
    <!-- ========== END MAIN CONTENT ========== -->
    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
    </script>
    @stack('scripts')
</body>
</html>