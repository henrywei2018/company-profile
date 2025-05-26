<!-- resources/views/components/layouts/admin.blade.php -->
@props([
    'title' => config('app.name', 'Admin Panel'), 
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
    <x-admin.admin-header :unreadMessagesCount="$unreadMessages" />
    <!-- ========== END HEADER ========== -->

    <!-- ========== MAIN CONTENT ========== -->
    <!-- Mobile breadcrumb -->
    <x-admin.breadcrumb-mobile />

    <!-- Sidebar -->
    <x-admin.admin-sidebar :unreadMessagesCount="$unreadMessages" :pendingQuotationsCount="$pendingQuotations" />

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

    <!-- Quick Actions Implementation -->
    @php
    $quickActions = [
        [
            'title' => 'Add New Project',
            'description' => 'Create a new client project',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
            'href' => route('admin.projects.create'),
            'color_classes' => 'bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800'
        ],
        [
            'title' => 'Add New Service',
            'description' => 'Create a new service offering',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />',
            'href' => route('admin.services.create'),
            'color_classes' => 'bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800'
        ],
        [
            'title' => 'New Blog Post',
            'description' => 'Write a new article',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />',
            'href' => route('admin.posts.create'),
            'color_classes' => 'bg-purple-600 hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-800'
        ],
        [
            'title' => 'Update Company',
            'description' => 'Manage company information',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />',
            'href' => route('admin.company.edit'),
            'color_classes' => 'bg-amber-600 hover:bg-amber-700 dark:bg-amber-700 dark:hover:bg-amber-800'
        ]
    ];
    @endphp

    <x-admin.floating-action-button :actions="$quickActions" />
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
    .admin-table {
        @apply w-full border-collapse;
    }

    .admin-table th,
    .admin-table td {
        @apply px-6 py-3 text-left;
    }

    .admin-table thead th {
        @apply bg-gray-50 dark:bg-neutral-800 font-medium text-xs text-gray-500 dark:text-neutral-400 uppercase tracking-wider border-b border-gray-200 dark:border-neutral-700;
    }

    .admin-table tbody td {
        @apply bg-white dark:bg-neutral-800 border-b border-gray-200 dark:border-neutral-700 text-sm;
    }

    .admin-table tbody tr:hover td {
        @apply bg-gray-50 dark:bg-neutral-700;
    }

    /* Header Actions Alignment */
    .table-header-actions {
        @apply flex items-center justify-between w-full px-6 py-4 bg-gray-50 dark:bg-neutral-800/50 border-b border-gray-200 dark:border-neutral-700;
    }

    .table-header-actions .left-actions {
        @apply flex items-center space-x-3;
    }

    .table-header-actions .right-info {
        @apply flex items-center space-x-4 text-sm text-gray-600 dark:text-neutral-400;
    }

    /* Pagination Spacing */
    .table-pagination {
        @apply px-6 py-4 bg-gray-50 dark:bg-neutral-800/50 border-t border-gray-200 dark:border-neutral-700;
    }

    /* Priority Indicators */
    .message-row-urgent {
        @apply border-l-4 border-red-500 bg-red-50 dark:bg-red-900/10;
    }

    .message-row-needs-reply {
        @apply border-l-4 border-amber-500 bg-amber-50 dark:bg-amber-900/10;
    }

    .message-row-unread {
        @apply border-l-4 border-blue-500 bg-blue-50 dark:bg-blue-900/10;
    }

    /* Compact Badge Styles */
    .status-badges {
        @apply flex flex-col space-y-1;
    }

    .status-badges .badge {
        @apply inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium;
    }

    /* Action Buttons Spacing */
    .action-buttons {
        @apply flex items-center space-x-1;
    }

    .action-buttons .btn-icon {
        @apply p-1.5 rounded text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors;
    }

    /* Responsive Table Adjustments */
    @media (max-width: 768px) {
        .admin-table th,
        .admin-table td {
            @apply px-3 py-2;
        }
        
        .table-header-actions {
            @apply px-3 py-3 flex-col space-y-3;
        }
        
        .table-header-actions .left-actions,
        .table-header-actions .right-info {
            @apply w-full justify-center;
        }
    }

    /* Fix for cards with tables */
    .card-with-table {
        @apply bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl overflow-hidden shadow-sm;
    }

    .card-with-table .card-header {
        @apply px-6 py-4 bg-gray-50 dark:bg-neutral-800/50 border-b border-gray-200 dark:border-neutral-700;
    }

    .card-with-table .card-body {
        @apply p-0; /* Remove padding for tables */
    }

    .card-with-table .card-footer {
        @apply px-6 py-4 bg-gray-50 dark:bg-neutral-800/50 border-t border-gray-200 dark:border-neutral-700;
    }
</style>

</html>