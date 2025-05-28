<!-- resources/views/components/layouts/client.blade.php -->
@props([
    'title' => config('app.name', 'Client Portal'),
    'enableCharts' => false,
    'unreadMessages' => 0,
    'pendingQuotations' => 0
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
    <x-client.client-header :unreadMessagesCount="$unreadMessages" />
    <!-- ========== END HEADER ========== -->

    <!-- ========== MAIN CONTENT ========== -->
    <!-- Mobile breadcrumb -->
    <x-client.breadcrumb-mobile />

    <!-- Sidebar -->
    <x-client.client-sidebar :unreadMessagesCount="$unreadMessages" :pendingQuotationsCount="$pendingQuotations" />

    <!-- Content -->
    <div class="w-full lg:ps-64">
        <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
            <!-- Flash Messages -->
            @if (session('success'))
                <x-client.alert type="success" class="mb-4" dismissible>
                    {{ session('success') }}
                </x-client.alert>
            @endif

            @if (session('error'))
                <x-client.alert type="error" class="mb-4" dismissible>
                    {{ session('error') }}
                </x-client.alert>
            @endif

            @if (session('info'))
                <x-client.alert type="info" class="mb-4" dismissible>
                    {{ session('info') }}
                </x-client.alert>
            @endif

            @if (session('warning'))
                <x-client.alert type="warning" class="mb-4" dismissible>
                    {{ session('warning') }}
                </x-client.alert>
            @endif

            <!-- Admin Support Banner (for admin users viewing client area) -->
            @adminViewing
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                Admin Support Mode
                            </h3>
                            <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                <p>You are viewing the client portal as an administrator. You have access to all client data for support purposes.</p>
                            </div>
                            <div class="mt-4">
                                <div class="-mx-2 -my-1.5 flex">
                                    <a href="{{ route('admin.dashboard') }}" class="bg-blue-50 dark:bg-blue-900/50 px-2 py-1.5 rounded-md text-sm font-medium text-blue-800 dark:text-blue-200 hover:bg-blue-100 dark:hover:bg-blue-900/70 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Return to Admin Panel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endadminViewing

            <!-- Page Content -->
            {{ $slot }}
        </div>
    </div>

    <!-- Quick Actions for Client -->
    @php
    $quickActions = [
        [
            'title' => 'Request Quote',
            'description' => 'Submit a new quotation request',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
            'href' => route('client.quotations.create'),
            'color_classes' => 'bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800'
        ],
        [
            'title' => 'Send Message',
            'description' => 'Contact our support team',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />',
            'href' => route('client.messages.create'),
            'color_classes' => 'bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800'
        ],
        [
            'title' => 'View Projects',
            'description' => 'Check your project status',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />',
            'href' => route('client.projects.index'),
            'color_classes' => 'bg-purple-600 hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-800'
        ],
        [
            'title' => 'Browse Portfolio',
            'description' => 'View our completed work',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />',
            'href' => route('portfolio.index'),
            'color_classes' => 'bg-amber-600 hover:bg-amber-700 dark:bg-amber-700 dark:hover:bg-amber-800'
        ]
    ];
    @endphp

    <x-client.floating-action-button :actions="$quickActions" class="pr-4" />
    
    <!-- Chat Widget for Client -->
    <x-chat-widget 
        size="normal"
        theme="client"
        :show-online-status="true"
        welcome-message="Hi! How can we help you today?"
        operator-name="Support Team" />
    
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

    /* Client-specific styles */
    .status-badge {
        @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
    }

    .status-badge.pending {
        @apply bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400;
    }

    .status-badge.approved {
        @apply bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400;
    }

    .status-badge.in-progress {
        @apply bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400;
    }

    .status-badge.completed {
        @apply bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400;
    }

    .status-badge.rejected {
        @apply bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400;
    }

    /* Priority indicators */
    .priority-urgent {
        @apply border-l-4 border-red-500 bg-red-50 dark:bg-red-900/10;
    }

    .priority-high {
        @apply border-l-4 border-orange-500 bg-orange-50 dark:bg-orange-900/10;
    }

    .priority-normal {
        @apply border-l-4 border-blue-500 bg-blue-50 dark:bg-blue-900/10;
    }

    /* Card components */
    .client-card {
        @apply bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl shadow-sm;
    }

    .client-card-header {
        @apply px-6 py-4 border-b border-gray-200 dark:border-neutral-700;
    }

    .client-card-body {
        @apply p-6;
    }

    .client-card-footer {
        @apply px-6 py-4 border-t border-gray-200 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-800/50;
    }

    /* Progress bars */
    .progress-bar {
        @apply w-full bg-gray-200 rounded-full dark:bg-neutral-700;
    }

    .progress-fill {
        @apply bg-blue-600 text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full;
    }

    /* Timeline styles */
    .timeline-item {
        @apply relative pb-8;
    }

    .timeline-item:last-child {
        @apply pb-0;
    }

    .timeline-connector {
        @apply absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-neutral-700;
    }

    .timeline-item:last-child .timeline-connector {
        @apply hidden;
    }

    .timeline-marker {
        @apply relative flex h-8 w-8 items-center justify-center rounded-full;
    }

    .timeline-marker.completed {
        @apply bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400;
    }

    .timeline-marker.pending {
        @apply bg-gray-100 text-gray-400 dark:bg-neutral-800 dark:text-neutral-500;
    }
</style>

</html>