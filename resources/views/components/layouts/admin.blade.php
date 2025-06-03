{{-- resources/views/components/layouts/admin.blade.php - FIXED --}}
@props([
    'title' => 'Dashboard',
    'enableCharts' => false,
    'unreadMessages' => 0,
    'pendingQuotations' => 0,
    'recentNotifications' => null,
    'unreadNotificationsCount' => 0,
    'unreadMessagesCount' => 0,
    'pendingQuotationsCount' => 0,
    'waitingChatsCount' => 0,
    'urgentItemsCount' => 0
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

    <title>{{ $title }} - Admin Panel</title>

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
    <x-admin.admin-header 
            :unreadMessagesCount="$unreadMessagesCount ?? $unreadMessages ?? 0" 
            :pendingQuotationsCount="$pendingQuotationsCount ?? $pendingQuotations ?? 0"
            :recentNotifications="$recentNotifications ?? collect()"
            :unreadNotificationsCount="$unreadNotificationsCount ?? 0"
            :waitingChatsCount="$waitingChatsCount ?? 0"
            :urgentItemsCount="$urgentItemsCount ?? 0"
    />
    <!-- ========== END HEADER ========== -->

    <!-- ========== MAIN CONTENT ========== -->
    <!-- Mobile breadcrumb -->
    <x-admin.breadcrumb-mobile />

    <!-- Sidebar -->
    <x-admin.admin-sidebar />

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

            <!-- Error Display (if any from controller) -->
            @if(isset($error))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Dashboard Error</h3>
                            <div class="mt-2 text-sm text-red-700">{{ $error }}</div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Page Content -->
            {{ $slot }}
        </div>
    </div>
    
    <!-- Chat Widget for Admins -->
    <x-chat-widget 
        size="compact"
        theme="admin"
        :show-online-status="true"
        welcome-message="Admin Chat System"
        operator-name="System Admin" />
    
    <!-- ========== END MAIN CONTENT ========== -->

    <!-- Global JavaScript -->
    <script>
        // Hide loading screen when page is ready
        document.addEventListener('DOMContentLoaded', function() {
            const loadingScreen = document.getElementById('loading-screen');
            if (loadingScreen) {
                loadingScreen.style.display = 'none';
            }
        });

        // Dark mode toggle
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('theme-toggle');
            if (themeToggle) {
                themeToggle.addEventListener('click', function() {
                    if (document.documentElement.classList.contains('dark')) {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('color-theme', 'light');
                    } else {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('color-theme', 'dark');
                    }
                });
            }
        });

        // Global error handler for admin
        window.addEventListener('error', function(e) {
            console.error('Admin global error:', e.error);
        });

        // Global unhandled promise rejection handler
        window.addEventListener('unhandledrejection', function(e) {
            console.error('Admin unhandled promise rejection:', e.reason);
        });

        // Auto-refresh admin stats every minute
        document.addEventListener('DOMContentLoaded', function() {
            setInterval(function() {
                updateAdminStats();
            }, 60000); // Every minute for admin
        });

        function updateAdminStats() {
            fetch('{{ route("admin.dashboard.stats") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update various admin counters
                    updateAdminCounters(data.data);
                }
            })
            .catch(error => console.error('Error updating admin stats:', error));
        }

        function updateAdminCounters(data) {
            // Update notification badge
            if (data.notifications && typeof updateAdminNotificationBadge === 'function') {
                updateAdminNotificationBadge(data.notifications.unread);
            }
            
            // Update other counters if elements exist
            const elements = {
                'pending-quotations-count': data.quotations?.pending,
                'waiting-chats-count': data.chat?.waiting,
                'unread-messages-count': data.messages?.unread,
                'urgent-items-count': data.urgent_items
            };

            Object.entries(elements).forEach(([elementId, value]) => {
                const element = document.getElementById(elementId);
                if (element && value !== undefined) {
                    element.textContent = value;
                    
                    // Show/hide based on value
                    if (value > 0) {
                        element.style.display = 'inline-flex';
                    } else {
                        element.style.display = 'none';
                    }
                }
            });
        }

        // Admin-specific helper functions
        function handleAdminError(error, context = 'admin') {
            console.error(`Admin error in ${context}:`, error);
            
            // Show user-friendly error message
            const errorContainer = document.getElementById('admin-error-container');
            if (errorContainer) {
                errorContainer.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Admin System Notice</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    Some admin features may be temporarily unavailable. Please refresh the page or contact system administrator.
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
        }

        // System health check for admin
        function checkSystemHealth() {
            fetch('{{ route("admin.dashboard.system-health") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateSystemHealthIndicator(data.overall_status);
                }
            })
            .catch(error => {
                console.error('System health check failed:', error);
                updateSystemHealthIndicator('error');
            });
        }

        function updateSystemHealthIndicator(status) {
            const indicator = document.getElementById('system-health-indicator');
            if (indicator) {
                indicator.className = `system-health-${status}`;
                indicator.title = `System Status: ${status}`;
            }
        }

        // Run system health check every 5 minutes for admin
        document.addEventListener('DOMContentLoaded', function() {
            checkSystemHealth();
            setInterval(checkSystemHealth, 300000); // 5 minutes
        });
    </script>

    @stack('scripts')
</body>
</html>