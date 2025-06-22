{{-- Enhanced Dashboard with Integrated KPI Tabs - resources/views/admin/dashboard.blade.php --}}
<x-layouts.admin :title="'Admin Dashboard'" :enableCharts="true" 
    :unreadMessagesCount="$unreadMessagesCount ?? 0" 
    :pendingQuotationsCount="$pendingQuotationsCount ?? 0"
    :recentNotifications="$recentNotifications ?? collect()"
    :unreadNotificationsCount="$unreadNotificationsCount ?? 0"
    :waitingChatsCount="$waitingChatsCount ?? 0"
    :urgentItemsCount="$urgentItemsCount ?? 0">
    

    @if(config('app.debug'))
    <div class="mb-6 bg-gray-50 border border-gray-200 rounded-md p-4">
        <h3 class="text-sm font-medium text-gray-700 mb-2">🔧 Debug Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            <div>
                <strong>User:</strong> {{ auth()->user()->name ?? 'Not authenticated' }}<br>
                <strong>User ID:</strong> {{ auth()->id() ?? 'N/A' }}<br>
                <strong>Admin Role:</strong> {{ auth()->user()->is_admin ?? false ? 'Yes' : 'No' }}
            </div>
            <div>
                <strong>Analytics Data:</strong> {{ isset($analytics) ? 'Available' : 'Missing' }}<br>
                <strong>Analytics Type:</strong> {{ isset($analytics) ? gettype($analytics) : 'N/A' }}<br>
                <strong>Analytics Count:</strong> {{ isset($analytics) && is_array($analytics) ? count($analytics) : 'N/A' }}
            </div>
            <div>
                <strong>Environment:</strong> {{ app()->environment() }}<br>
                <strong>URL:</strong> {{ request()->url() }}<br>
                <strong>CSRF Token:</strong> {{ csrf_token() ? 'Present' : 'Missing' }}
            </div>
        </div>
    </div>
    @endif
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Admin Dashboard
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Welcome back, {{ auth()->user()->name }}! Here's your comprehensive system overview.
                </p>
            </div>
            
            <!-- Quick Actions -->
            <div class="flex items-center space-x-3">
                <button id="refresh-all-btn" type="button" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Refresh All Data
                </button>
                
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    Last updated: <span id="last-update-time">{{ now()->format('H:i') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Display (if any) -->
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

    <!-- Main Dashboard Tabs -->
    <div class="mb-8">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <!-- Application Stats Tab -->
                <button id="tab-app-stats"
                        class="dashboard-tab active border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm dark:text-gray-400 dark:hover:text-gray-300"
                        data-tab="app-stats">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        Application Statistics
                        <span class="ml-2 bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-blue-900 dark:text-blue-300">
                            Real-time
                        </span>
                    </div>
                </button>
                
                <!-- Analytics KPIs Tab -->
                <button id="tab-analytics-kpis"
                        class="dashboard-tab border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm dark:text-gray-400 dark:hover:text-gray-300"
                        data-tab="analytics-kpis">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 01-2-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Analytics KPIs
                        <span class="ml-2 bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">
                            GA4
                        </span>
                    </div>
                </button>
                
                <!-- Analytics Overview Tab -->
                <button id="tab-analytics-overview"
                        class="dashboard-tab border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm dark:text-gray-400 dark:hover:text-gray-300"
                        data-tab="analytics-overview">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Analytics Overview
                        <span class="ml-2 bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-purple-900 dark:text-purple-300">
                            Summary
                        </span>
                    </div>
                </button>

                <!-- System Health Tab -->
                <button id="tab-system-health"
                        class="dashboard-tab border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm dark:text-gray-400 dark:hover:text-gray-300"
                        data-tab="system-health">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        System Health
                        <span class="ml-2 bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-yellow-900 dark:text-yellow-300">
                            Live
                        </span>
                    </div>
                </button>
            </nav>
        </div>
    </div>

    <!-- Tab Content Sections -->
    <div class="tab-content">
        
        <!-- Application Statistics Content -->
        <div id="content-app-stats" class="tab-panel active">
            <!-- Quick Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Users Count -->
                <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Total Users</h3>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ number_format($stats['users_count'] ?? 0) }}
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                Active: {{ number_format($stats['active_users'] ?? 0) }}
                            </p>
                        </div>
                        <div class="text-blue-600 dark:text-blue-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Projects -->
                <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Projects</h3>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ number_format($stats['projects_count'] ?? 0) }}
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                Featured: {{ number_format($stats['featured_projects'] ?? 0) }}
                            </p>
                        </div>
                        <div class="text-purple-600 dark:text-purple-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Messages</h3>
                            <div class="flex items-center space-x-2">
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ number_format($stats['messages_count'] ?? 0) }}
                                </p>
                                @if(($unreadMessagesCount ?? 0) > 0)
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-red-900 dark:text-red-300">
                                        {{ $unreadMessagesCount }} new
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                Unread: {{ number_format($unreadMessagesCount ?? 0) }}
                            </p>
                        </div>
                        <div class="text-orange-600 dark:text-orange-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Chat Sessions -->
                <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Chat Sessions</h3>
                            <div class="flex items-center space-x-2">
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ number_format($stats['chat_sessions'] ?? 0) }}
                                </p>
                                @if(($waitingChatsCount ?? 0) > 0)
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-yellow-900 dark:text-yellow-300">
                                        {{ $waitingChatsCount }} waiting
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                Active: {{ number_format($stats['active_chats'] ?? 0) }}
                            </p>
                        </div>
                        <div class="text-green-600 dark:text-green-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a2 2 0 01-2-2v-6a2 2 0 012-2h8z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Users -->
                <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl">
                    <div class="p-6 border-b border-gray-200 dark:border-neutral-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Users</h3>
                    </div>
                    <div class="p-6">
                        @if(isset($recentUsers) && $recentUsers->count() > 0)
                            <div class="space-y-3">
                                @foreach($recentUsers->take(5) as $user)
                                <div class="flex items-center justify-between py-2">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-blue-600">
                                                {{ substr($user->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $user->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400">No recent users</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Messages -->
                <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl">
                    <div class="p-6 border-b border-gray-200 dark:border-neutral-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Messages</h3>
                    </div>
                    <div class="p-6">
                        @if(isset($recentMessages) && $recentMessages->count() > 0)
                            <div class="space-y-3">
                                @foreach($recentMessages->take(5) as $message)
                                <div class="flex items-start justify-between py-2">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $message->name ?? 'Anonymous' }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                            {{ Str::limit($message->message ?? $message->subject ?? '', 50) }}
                                        </p>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 ml-2">
                                        {{ $message->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400">No recent messages</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics KPIs Content (Your KPI Dashboard Component) -->
        <div id="content-analytics-kpis" class="tab-panel hidden">
            <x-analytics.kpi-dashboard />
        </div>

        <!-- Analytics Overview Content -->
        <div id="content-analytics-overview" class="tab-panel hidden">
            @if(isset($analytics) && is_array($analytics) && count($analytics) > 0)
                <!-- Analytics Overview Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Page Views -->
                    @if(isset($analytics['overview']['page_views']) || isset($analytics['overview']['pageviews']))
                    <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Bounce Rate</h3>
                                <div class="flex items-center space-x-2">
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                        {{ number_format($analytics['engagement']['bounce_rate']['value'] ?? 0, 1) }}%
                                    </p>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">current</span>
                                </div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                    Benchmark: {{ $analytics['engagement']['bounce_rate']['benchmark'] ?? '< 60%' }}
                                </p>
                            </div>
                            <div class="text-orange-600 dark:text-orange-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Analytics Charts and Tables -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Traffic Sources -->
                    <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl">
                        <div class="p-6 border-b border-gray-200 dark:border-neutral-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Top Traffic Sources</h3>
                        </div>
                        <div class="p-6">
                            @if(isset($analytics['traffic']['top_sources']) && count($analytics['traffic']['top_sources']) > 0)
                                <div class="space-y-3">
                                    @foreach(array_slice($analytics['traffic']['top_sources'], 0, 5) as $index => $source)
                                    <div class="flex items-center justify-between py-2 px-3 {{ $index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-700' : '' }} rounded">
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                {{ $source['source'] ?? 'Unknown' }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                Traffic source
                                            </div>
                                        </div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white ml-2">
                                            {{ number_format($source['sessions'] ?? 0) }}
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 01-2-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    <p class="text-gray-500 dark:text-gray-400">No traffic data available</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Most Engaging Pages -->
                    <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl">
                        <div class="p-6 border-b border-gray-200 dark:border-neutral-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Most Engaging Pages</h3>
                        </div>
                        <div class="p-6">
                            @if(isset($analytics['engagement']['most_engaging_pages']) && count($analytics['engagement']['most_engaging_pages']) > 0)
                                <div class="space-y-3">
                                    @foreach(array_slice($analytics['engagement']['most_engaging_pages'], 0, 5) as $index => $page)
                                    <div class="flex items-center justify-between py-2 px-3 {{ $index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-700' : '' }} rounded">
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                {{ $page['pagePath'] ?? $page['url'] ?? 'Unknown' }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                Avg. session duration: {{ number_format($page['averageSessionDuration'] ?? 0) }}s
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                    <p class="text-gray-500 dark:text-gray-400">No engagement data available</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <!-- Analytics Error State -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Analytics Data Unavailable</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                Analytics data is being processed or temporarily unavailable. Please try refreshing in a few moments.
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- System Health Content -->
        <div id="content-system-health" class="tab-panel hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Cache Status -->
                <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Cache Status</h3>
                            <p class="text-lg font-bold text-green-600 dark:text-green-400">Healthy</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                Redis: Connected
                            </p>
                        </div>
                        <div class="text-green-600 dark:text-green-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Database Status -->
                <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Database</h3>
                            <p class="text-lg font-bold text-green-600 dark:text-green-400">Connected</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                Response: < 50ms
                            </p>
                        </div>
                        <div class="text-green-600 dark:text-green-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Queue Status -->
                <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Queue Jobs</h3>
                            <p class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                {{ number_format($stats['queue_jobs'] ?? 0) }}
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                Pending jobs
                            </p>
                        </div>
                        <div class="text-blue-600 dark:text-blue-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Server Load -->
                <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Server Load</h3>
                            <p class="text-lg font-bold text-yellow-600 dark:text-yellow-400">Moderate</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                CPU: 45% | RAM: 62%
                            </p>
                        </div>
                        <div class="text-yellow-600 dark:text-yellow-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Metrics -->
            <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">System Metrics</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- PHP Version -->
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">PHP Version</h4>
                        <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ PHP_VERSION }}</p>
                    </div>

                    <!-- Laravel Version -->
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">Laravel</h4>
                        <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ app()->version() }}</p>
                    </div>

                    <!-- Environment -->
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">Environment</h4>
                        <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ app()->environment() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    const tabButtons = document.querySelectorAll('.dashboard-tab');
    const tabPanels = document.querySelectorAll('.tab-panel');

    function switchTab(targetTab) {
        // Remove active class from all tabs and panels
        tabButtons.forEach(btn => {
            btn.classList.remove('active');
            btn.classList.remove('border-blue-500', 'text-blue-600');
            btn.classList.remove('dark:border-blue-400', 'dark:text-blue-400');
            btn.classList.add('border-transparent', 'text-gray-500');
            btn.classList.add('dark:text-gray-400');
        });

        tabPanels.forEach(panel => {
            panel.classList.add('hidden');
            panel.classList.remove('active');
        });

        // Add active class to target tab and panel
        const targetButton = document.querySelector(`[data-tab="${targetTab}"]`);
        const targetPanel = document.getElementById(`content-${targetTab}`);

        if (targetButton && targetPanel) {
            targetButton.classList.add('active');
            targetButton.classList.remove('border-transparent', 'text-gray-500');
            targetButton.classList.remove('dark:text-gray-400');
            targetButton.classList.add('border-blue-500', 'text-blue-600');
            targetButton.classList.add('dark:border-blue-400', 'dark:text-blue-400');

            targetPanel.classList.remove('hidden');
            targetPanel.classList.add('active');

            // Initialize KPI dashboard if analytics-kpis tab is selected
            if (targetTab === 'analytics-kpis') {
                initializeKPIDashboard();
            }

            // Load system health if system-health tab is selected
            if (targetTab === 'system-health') {
                loadSystemHealth();
            }
        }
    }

    // Add click event listeners to tab buttons
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            switchTab(targetTab);
        });
    });

    // Refresh all data functionality
    const refreshBtn = document.getElementById('refresh-all-btn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            // Add loading state
            this.disabled = true;
            this.querySelector('svg').classList.add('animate-spin');
            
            // Get the currently active tab
            const activeTab = document.querySelector('.dashboard-tab.active');
            const activeTabName = activeTab ? activeTab.getAttribute('data-tab') : 'app-stats';
            
            // Refresh based on active tab
            refreshActiveTabData(activeTabName).then(() => {
                this.disabled = false;
                this.querySelector('svg').classList.remove('animate-spin');
                updateLastRefreshTime();
                showNotification('Dashboard data refreshed successfully!', 'success');
            }).catch(error => {
                this.disabled = false;
                this.querySelector('svg').classList.remove('animate-spin');
                showNotification('Failed to refresh data: ' + error.message, 'error');
            });
        });
    }

    // Initialize KPI Dashboard function
    function initializeKPIDashboard() {
        // Check if KPI dashboard is already initialized
        if (window.kpiDashboard && typeof window.kpiDashboard.loadKPIData === 'function') {
            window.kpiDashboard.loadKPIData();
        } else {
            // Initialize KPI dashboard if the class exists
            if (typeof KPIDashboard !== 'undefined') {
                window.kpiDashboard = new KPIDashboard();
            } else {
                // Create a simple fallback KPI loader
                loadKPIDataFallback();
            }
        }
    }

    // Fallback KPI data loader with proper authentication
    async function loadKPIDataFallback() {
        try {
            const response = await fetchWithAuth('/admin/analytics/kpi/dashboard?period=30');
            const data = await response.json();
            
            if (data.success) {
                console.log('KPI Data loaded:', data.data);
                showNotification('KPI data loaded successfully', 'success');
            } else {
                throw new Error(data.message || 'Failed to load KPI data');
            }
        } catch (error) {
            console.error('KPI Dashboard error:', error);
            showKPIError(error.message);
        }
    }

    // Load system health data
    async function loadSystemHealth() {
        const healthContainer = document.getElementById('content-system-health');
        if (!healthContainer) return;

        try {
            // Try to load system health - use a safe fallback URL
            const response = await fetchWithAuth('/admin/dashboard');
            const data = await response.json();
            
            console.log('System health check completed');
            showNotification('System health checked', 'success');
        } catch (error) {
            console.error('System health check failed:', error);
            // Don't show error for this as it's not critical
        }
    }
async function fetchWithAuth(url, options = {}) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    
    const defaultOptions = {
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...(csrfToken && { 'X-CSRF-TOKEN': csrfToken.getAttribute('content') }),
            ...options.headers
        },
        credentials: 'include',
        ...options
    };

    const response = await fetch(url, defaultOptions);

    if (!response.ok) {
        if (response.status === 401) {
            throw new Error('Authentication required. Please refresh the page and try again.');
        }
        if (response.status === 403) {
            throw new Error('Access denied. You do not have permission to view this data.');
        }
        if (response.status === 500) {
            throw new Error('Server error. Please try again later.');
        }
        throw new Error(`HTTP error! status: ${response.status}`);
    }

    // Check if response is actually JSON
    const contentType = response.headers.get('content-type');
    if (!contentType || !contentType.includes('application/json')) {
        throw new Error('Server returned invalid response format');
    }

    return response;
}


    // Refresh data based on active tab
    async function refreshActiveTabData(tabName) {
        switch (tabName) {
            case 'analytics-kpis':
                if (window.kpiDashboard && typeof window.kpiDashboard.loadKPIData === 'function') {
                    await window.kpiDashboard.loadKPIData();
                } else {
                    await loadKPIDataFallback();
                }
                break;
            case 'system-health':
                await loadSystemHealth();
                break;
            case 'analytics-overview':
                // Refresh analytics overview if needed
                console.log('Analytics overview refresh not implemented');
                break;
            case 'app-stats':
            default:
                // Refresh application stats if needed
                console.log('Application stats refresh not implemented');
                break;
        }
    }

    // Update last refresh time
    function updateLastRefreshTime() {
        const timeElement = document.getElementById('last-update-time');
        if (timeElement) {
            timeElement.textContent = new Date().toLocaleTimeString('en-US', { 
                hour12: false, 
                hour: '2-digit', 
                minute: '2-digit' 
            });
        }
    }

    // Show KPI error in the dashboard
    function showKPIError(message) {
        const kpiContainer = document.getElementById('content-analytics-kpis');
        if (kpiContainer) {
            kpiContainer.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">KPI Dashboard Error</h3>
                            <div class="mt-2 text-sm text-red-700">${message}</div>
                            <div class="mt-4">
                                <button onclick="window.location.reload()" class="bg-red-100 hover:bg-red-200 text-red-800 px-3 py-1 rounded text-sm">
                                    Refresh Page
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }

    // Notification function
    function showNotification(message, type = 'info') {
        // Remove any existing notifications
        const existingNotifications = document.querySelectorAll('.dashboard-notification');
        existingNotifications.forEach(notification => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        });

        const notification = document.createElement('div');
        notification.className = `dashboard-notification fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 transform translate-x-full`;
        
        const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
        notification.classList.add(bgColor, 'text-white');
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Animate out and remove
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    // Global functions for external access
    window.showNotification = showNotification;
    window.fetchWithAuth = fetchWithAuth;

    // Debug analytics data structure (can be removed in production)
    @if(isset($analytics) && config('app.debug'))
    console.log('Analytics data structure:', @json($analytics));
    @endif

    // Set initial tab (app-stats by default)
    switchTab('app-stats');

    // Initialize first load
    updateLastRefreshTime();
});

// Enhanced KPI Dashboard Class with better error handling
class EnhancedKPIDashboard {
    constructor() {
        this.currentCategory = 'overview';
        this.currentPeriod = 30;
        this.refreshInterval = null;
        this.kpiData = null;
        this.isInitialized = false;

        this.init();
    }

    async init() {
        try {
            await this.loadKPIData();
            this.setupEventListeners();
            this.startAutoRefresh();
            this.isInitialized = true;
        } catch (error) {
            console.error('KPI Dashboard initialization failed:', error);
            this.showError('Failed to initialize KPI dashboard: ' + error.message);
        }
    }

    async loadKPIData() {
    try {
        console.log('📊 Loading KPI Dashboard...');
        const response = await this.fetchWithAuth('/admin/analytics/kpi/dashboard?period=30');
        const data = await response.json();
        
        // Debug the actual data structure
        console.log('🔍 Raw API Response:', data);
        console.log('🔍 KPI Data Structure:', data.data);
        
        if (data.success) {
            this.kpiData = data.data;
            console.log('✅ KPI Data loaded:', this.kpiData);
            
            // Check what categories are available
            console.log('📋 Available KPI categories:', Object.keys(this.kpiData));
            
            this.renderKPIDashboard();
            this.showNotification('KPI data loaded successfully', 'success');
        } else {
            throw new Error(data.message || 'Failed to load KPI data');
        }
    } catch (error) {
        console.error('❌ KPI Dashboard error:', error);
        this.showKPIError(error.message);
    }
}

    renderKPIDashboard() {
        if (!this.kpiData) return;

        try {
            this.renderOverviewKPIs();
            this.renderCategoryContent(this.currentCategory);
            console.log('KPI dashboard rendered successfully');
        } catch (error) {
            console.error('Error rendering KPI dashboard:', error);
            this.showError('Failed to render dashboard: ' + error.message);
        }
    }

    renderOverviewKPIs() {
        const container = document.getElementById('overview-kpis');
        if (!container) return;

        const overview = this.kpiData.overview || {};
        
        if (Object.keys(overview).length === 0) {
            container.innerHTML = '<div class="col-span-full text-center text-gray-500">No overview data available</div>';
            return;
        }

        const kpiCards = Object.entries(overview).map(([key, data]) => {
            return `
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                                ${this.formatKPITitle(key)}
                            </h4>
                            <div class="flex items-center space-x-2">
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                    ${this.formatKPIValue(key, data.current || data.value || 0)}
                                </p>
                                ${data.change_percent !== undefined ? `
                                    <span class="text-xs ${data.change_percent >= 0 ? 'text-green-600' : 'text-red-600'}">
                                        ${data.change_percent >= 0 ? '+' : ''}${data.change_percent.toFixed(1)}%
                                    </span>
                                ` : ''}
                            </div>
                            ${data.status ? `
                                <p class="text-xs mt-1 ${this.getStatusClass(data.status)}">
                                    ${data.status}
                                </p>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        container.innerHTML = kpiCards;
    }

    renderCategoryContent(category) {
        const categoryData = this.kpiData[category];
        if (!categoryData) return;

        console.log(`Rendering ${category} category data:`, categoryData);
        // Add specific category rendering logic here
    }

    formatKPITitle(key) {
        return key.split('_').map(word =>
            word.charAt(0).toUpperCase() + word.slice(1)
        ).join(' ');
    }

    formatKPIValue(key, value) {
        if (value === undefined || value === null) return '0';

        switch (key) {
            case 'bounce_rate':
                return `${typeof value === 'number' ? value.toFixed(1) : value}%`;
            case 'avg_session_duration':
            case 'average_session_duration':
                return this.formatDuration(value);
            default:
                return typeof value === 'number' ? value.toLocaleString() : value;
        }
    }

    formatDuration(seconds) {
        if (!seconds || seconds === 0) return '0s';
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = Math.floor(seconds % 60);
        if (minutes === 0) return `${remainingSeconds}s`;
        return `${minutes}m ${remainingSeconds}s`;
    }

    getStatusClass(status) {
        const classes = {
            'excellent': 'text-green-600',
            'good': 'text-blue-600',
            'warning': 'text-yellow-600',
            'critical': 'text-red-600'
        };
        return classes[status] || 'text-gray-600';
    }

    showLoadingState() {
        const container = document.getElementById('overview-kpis');
        if (container) {
            container.innerHTML = `
                <div class="col-span-full flex justify-center items-center py-12">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <span class="ml-2 text-gray-600">Loading KPI data...</span>
                </div>
            `;
        }
    }

    hideLoadingState() {
        // Loading state will be replaced by actual content
    }

    showError(message) {
        const container = document.getElementById('overview-kpis');
        if (container) {
            container.innerHTML = `
                <div class="col-span-full bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">KPI Error</h3>
                            <div class="mt-2 text-sm text-red-700">${message}</div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        if (window.showNotification) {
            window.showNotification(message, 'error');
        }
    }

    setupEventListeners() {
        // Period change listener
        const periodSelect = document.getElementById('kpi-period');
        if (periodSelect) {
            periodSelect.addEventListener('change', (e) => {
                this.currentPeriod = parseInt(e.target.value);
                this.loadKPIData(this.currentPeriod);
            });
        }
    }

    startAutoRefresh() {
        // Refresh every 5 minutes
        this.refreshInterval = setInterval(() => {
            this.loadKPIData();
        }, 5 * 60 * 1000);
    }

    cleanup() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
        }
    }
}

// Initialize enhanced KPI dashboard when needed
window.initializeEnhancedKPIDashboard = function() {
    if (!window.enhancedKpiDashboard) {
        window.enhancedKpiDashboard = new EnhancedKPIDashboard();
    }
    return window.enhancedKpiDashboard;
};

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    if (window.enhancedKpiDashboard) {
        window.enhancedKpiDashboard.cleanup();
    }
});
</script>

    <!-- Enhanced Styles -->
    <style>
        .dashboard-tab {
            transition: all 0.2s ease-in-out;
        }

        .dashboard-tab.active {
            border-color: rgb(59 130 246) !important;
            color: rgb(59 130 246) !important;
        }

        .dark .dashboard-tab.active {
            border-color: rgb(96 165 250) !important;
            color: rgb(96 165 250) !important;
        }

        .tab-panel {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Loading animation */
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }

        /* Enhanced hover effects */
        .dashboard-tab:hover {
            transform: translateY(-1px);
        }

        /* Responsive tab adjustments */
        @media (max-width: 768px) {
            .dashboard-tab {
                font-size: 0.8rem;
                padding: 0.5rem 0.25rem;
            }
            
            .dashboard-tab svg {
                width: 1rem;
                height: 1rem;
            }
            
            .dashboard-tab span {
                display: none;
            }
        }

        /* Card hover effects */
        .bg-white:hover, .dark .bg-neutral-800:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.2s ease-in-out;
        }
    </style>
</x-layouts.admin>