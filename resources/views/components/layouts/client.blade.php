{{-- resources/views/components/layouts/client.blade.php --}}
@props([
    'title' => config('app.name', 'Client Portal'), 
    'enableCharts' => false,
    'unreadMessages' => 0,
    'pendingApprovals' => 0
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }} - Client Portal</title>

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
        const html = document.querySelector('html');
        const isLightOrAuto = localStorage.getItem('hs_theme') === 'light' || (localStorage.getItem('hs_theme') === 'auto' && !window.matchMedia('(prefers-color-scheme: dark)').matches);
        const isDarkOrAuto = localStorage.getItem('hs_theme') === 'dark' || (localStorage.getItem('hs_theme') === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);

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
    <x-client.client-header :unreadMessagesCount="$unreadMessages" :pendingApprovalsCount="$pendingApprovals" />
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

    <!-- Quick Actions for Client -->
    @php
    $clientQuickActions = [
        [
            'title' => 'Request Quote',
            'description' => 'Submit new quotation request',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
            'href' => route('client.quotations.create'),
            'color_classes' => 'bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800'
        ],
        [
            'title' => 'Send Message',
            'description' => 'Contact support team',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />',
            'href' => route('client.messages.create'),
            'color_classes' => 'bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800'
        ],
        [
            'title' => 'View Projects',
            'description' => 'Check your projects',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />',
            'href' => route('client.projects.index'),
            'color_classes' => 'bg-purple-600 hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-800'
        ],
        [
            'title' => 'Leave Review',
            'description' => 'Share your experience',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />',
            'href' => route('client.testimonials.create'),
            'color_classes' => 'bg-amber-600 hover:bg-amber-700 dark:bg-amber-700 dark:hover:bg-amber-800'
        ]
    ];
    @endphp

    <x-admin.floating-action-button :actions="$clientQuickActions" class="pr-4" />
    
    <!-- Chat Widget -->
    <x-chat-widget 
        size="normal"
        theme="client"
        :show-online-status="true"
        welcome-message="Halo! Ada yang bisa kami bantu?"
        operator-name="Customer Support" />
    
    <!-- ========== END MAIN CONTENT ========== -->

    <script src="https://cdn.jsdelivr.net/npm/preline/dist/index.js"></script>
    @if ($enableCharts)
        <!-- Charts Libraries -->
        <script src="https://cdn.jsdelivr.net/npm/lodash/lodash.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts/dist/apexcharts.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/preline/dist/helper-apexcharts.js"></script>
    @endif

    @stack('scripts')
</body>

<style>
    /* Client-specific table styles */
    .client-table {
        @apply w-full border-collapse;
    }

    .client-table th,
    .client-table td {
        @apply px-6 py-3 text-left;
    }

    .client-table thead th {
        @apply bg-gray-50 dark:bg-neutral-800 font-medium text-xs text-gray-500 dark:text-neutral-400 uppercase tracking-wider border-b border-gray-200 dark:border-neutral-700;
    }

    .client-table tbody td {
        @apply bg-white dark:bg-neutral-800 border-b border-gray-200 dark:border-neutral-700 text-sm;
    }

    .client-table tbody tr:hover td {
        @apply bg-gray-50 dark:bg-neutral-700;
    }

    /* Client card styles */
    .client-card {
        @apply bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl overflow-hidden shadow-sm;
    }

    .client-card-header {
        @apply px-6 py-4 bg-gray-50 dark:bg-neutral-800/50 border-b border-gray-200 dark:border-neutral-700;
    }

    .client-card-body {
        @apply p-6;
    }

    .client-card-footer {
        @apply px-6 py-4 bg-gray-50 dark:bg-neutral-800/50 border-t border-gray-200 dark:border-neutral-700;
    }

    /* Status indicators for client */
    .status-active {
        @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400;
    }

    .status-pending {
        @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400;
    }

    .status-completed {
        @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400;
    }

    .status-cancelled {
        @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400;
    }

    /* Priority indicators */
    .priority-high {
        @apply border-l-4 border-red-500 bg-red-50 dark:bg-red-900/10;
    }

    .priority-normal {
        @apply border-l-4 border-blue-500 bg-blue-50 dark:bg-blue-900/10;
    }

    .priority-low {
        @apply border-l-4 border-gray-500 bg-gray-50 dark:bg-gray-900/10;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .client-table th,
        .client-table td {
            @apply px-3 py-2;
        }
        
        .client-card-header,
        .client-card-body,
        .client-card-footer {
            @apply px-4 py-3;
        }
    }
</style>

</html>