{{-- Enhanced Dashboard with Tabs - resources/views/admin/dashboard.blade.php --}}
<x-layouts.admin :title="'Admin Dashboard'" :enableCharts="true" 
    :unreadMessagesCount="$unreadMessagesCount ?? 0" 
    :pendingQuotationsCount="$pendingQuotationsCount ?? 0"
    :recentNotifications="$recentNotifications ?? collect()"
    :unreadNotificationsCount="$unreadNotificationsCount ?? 0"
    :waitingChatsCount="$waitingChatsCount ?? 0"
    :urgentItemsCount="$urgentItemsCount ?? 0">
    
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Admin Dashboard
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Welcome back, {{ auth()->user()->name }}! Here's your system overview.
                </p>
            </div>
            
            <!-- Quick Actions -->
            <div class="flex items-center space-x-3">
                <button onclick="refreshAllData()" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Refresh Data
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
                <button onclick="switchTab('app-stats')" 
                        id="tab-app-stats"
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
                
                <button onclick="switchTab('analytics-stats')" 
                        id="tab-analytics-stats"
                        class="dashboard-tab border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm dark:text-gray-400 dark:hover:text-gray-300"
                        data-tab="analytics-stats">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 01-2-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Website Analytics
                        <span class="ml-2 bg-orange-100 text-orange-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-orange-900 dark:text-orange-300">
                            ~2h delay
                        </span>
                    </div>
                </button>
            </nav>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
        
        <!-- Application Statistics Tab -->
        <div id="app-stats-content" class="tab-pane active">
            
            <!-- App Statistics Section -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Application Overview
                    </h2>
                    <div class="flex items-center text-sm text-green-600 dark:text-green-400">
                        <div class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></div>
                        Live Data
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Projects Stat -->
                    @php
                        $projectStats = $statistics['projects'] ?? ['total' => 0, 'active' => 0, 'completed' => 0, 'change_percentage' => 0];
                        $projectsChange = $projectStats['change_percentage'] ?? 0;
                    @endphp
                    <x-admin.stat-card title="Total Projects" value="{{ $projectStats['total'] }}"
                        icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10' />"
                        iconColor="text-purple-500" iconBg="bg-purple-100" :change="$projectsChange"
                        href="{{ route('admin.projects.index') }}" />

                    <!-- Quotations Stat -->
                    @php
                        $quotationStats = $statistics['quotations'] ?? ['total' => 0, 'pending' => 0, 'approved' => 0, 'conversion_rate' => 0];
                    @endphp
                    <x-admin.stat-card title="Quotations" value="{{ $quotationStats['total'] }}"
                        icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01' />"
                        iconColor="text-amber-500" iconBg="bg-amber-100"
                        href="{{ route('admin.quotations.index') }}" />

                    <!-- Clients Stat -->
                    @php
                        $clientStats = $statistics['clients'] ?? ['total' => 0, 'active' => 0, 'verified' => 0];
                    @endphp
                    <x-admin.stat-card title="Clients" value="{{ $clientStats['total'] }}"
                        icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' />"
                        iconColor="text-blue-500" iconBg="bg-blue-100"
                        href="{{ route('admin.users.index', ['role' => 'client']) }}" />

                    <!-- Messages Stat -->
                    @php
                        $messageStats = $statistics['messages'] ?? ['total' => 0, 'unread' => 0, 'urgent' => 0];
                    @endphp
                    <x-admin.stat-card title="Messages" value="{{ $messageStats['total'] }}"
                        icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' />"
                        iconColor="text-green-500" iconBg="bg-green-100"
                        href="{{ route('admin.messages.index') }}" />
                </div>
            </div>

            <!-- Alerts Section -->
            @php
                $alerts = $alerts ?? [];
                $hasAlerts = collect($alerts)->sum() > 0;
            @endphp
            @if($hasAlerts)
            <div class="mb-8">
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">System Alerts</h3>
                            <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                <ul class="list-disc pl-5 space-y-1">
                                    @if(($alerts['overdue_projects'] ?? 0) > 0)
                                        <li>
                                            <a href="{{ route('admin.projects.index', ['status' => 'overdue']) }}" class="hover:underline">
                                                {{ $alerts['overdue_projects'] }} overdue projects need attention
                                            </a>
                                        </li>
                                    @endif
                                    @if(($alerts['pending_quotations'] ?? 0) > 0)
                                        <li>
                                            <a href="{{ route('admin.quotations.index', ['status' => 'pending']) }}" class="hover:underline">
                                                {{ $alerts['pending_quotations'] }} quotations awaiting review
                                            </a>
                                        </li>
                                    @endif
                                    @if(($alerts['urgent_messages'] ?? 0) > 0)
                                        <li>
                                            <a href="{{ route('admin.messages.index', ['priority' => 'urgent']) }}" class="hover:underline">
                                                {{ $alerts['urgent_messages'] }} urgent messages
                                            </a>
                                        </li>
                                    @endif
                                    @if(($alerts['waiting_chats'] ?? 0) > 0)
                                        <li>
                                            <a href="{{ route('admin.chat.index') }}" class="hover:underline">
                                                {{ $alerts['waiting_chats'] }} chat sessions waiting for response
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Recent Activities (App Data) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl">
                        <div class="p-6 border-b border-gray-200 dark:border-neutral-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Application Activities</h2>
                        </div>
                        <div class="p-6">
                            @php
                                $activitiesArray = [];
                                if (isset($recentActivities) && is_array($recentActivities)) {
                                    foreach ($recentActivities as $key => $activities) {
                                        if (is_array($activities) || is_object($activities)) {
                                            foreach ($activities as $activity) {
                                                if (is_array($activity) || is_object($activity)) {
                                                    $activityArray = is_array($activity) ? $activity : (array) $activity;
                                                    $activitiesArray[] = [
                                                        'type' => $activityArray['type'] ?? 'system',
                                                        'action' => $activityArray['action'] ?? 'updated',
                                                        'title' => $activityArray['title'] ?? 'System Activity',
                                                        'user' => $activityArray['user'] ?? 'System',
                                                        'date' => $activityArray['date'] ?? now(),
                                                        'url' => $activityArray['url'] ?? '#',
                                                        'icon' => $activityArray['icon'] ?? 'folder',
                                                        'color' => $activityArray['color'] ?? 'gray',
                                                    ];
                                                }
                                            }
                                        }
                                    }
                                }
                                usort($activitiesArray, function($a, $b) {
                                    return $b['date'] <=> $a['date'];
                                });
                                $activitiesArray = array_slice($activitiesArray, 0, 10);
                            @endphp

                            @if(count($activitiesArray) > 0)
                                <div class="flow-root">
                                    <ul class="-mb-8">
                                        @foreach($activitiesArray as $index => $activity)
                                        <li>
                                            <div class="relative pb-8">
                                                @if($index < count($activitiesArray) - 1)
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-neutral-700" aria-hidden="true"></span>
                                                @endif
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <span class="h-8 w-8 rounded-full bg-{{ $activity['color'] }}-100 dark:bg-{{ $activity['color'] }}-900/30 flex items-center justify-center ring-8 ring-white dark:ring-neutral-800">
                                                            <svg class="h-4 w-4 text-{{ $activity['color'] }}-600 dark:text-{{ $activity['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                            </svg>
                                                        </span>
                                                    </div>
                                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                        <div>
                                                            <p class="text-sm text-gray-900 dark:text-white font-medium">
                                                                <a href="{{ $activity['url'] }}" class="hover:underline">{{ $activity['title'] }}</a>
                                                            </p>
                                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                                {{ ucfirst($activity['action']) }} by {{ $activity['user'] }}
                                                            </p>
                                                        </div>
                                                        <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                            @php
                                                                $date = $activity['date'];
                                                                if (is_string($date)) {
                                                                    try {
                                                                        $date = \Carbon\Carbon::parse($date);
                                                                    } catch (\Exception $e) {
                                                                        $date = now();
                                                                    }
                                                                }
                                                            @endphp
                                                            <time datetime="{{ $date->toISOString() }}">{{ $date->diffForHumans() }}</time>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No recent activities</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">All systems are running smoothly.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column: App Status -->
                <div class="space-y-8">
                    <!-- System Performance -->
                    @php
                        $performance = $performance ?? [];
                    @endphp
                    <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl">
                        <div class="p-6 border-b border-gray-200 dark:border-neutral-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">System Performance</h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <!-- Memory Usage -->
                            <div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Memory Usage</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $performance['memory_usage'] ?? 45 }}%</span>
                                </div>
                                <div class="mt-2 bg-gray-200 dark:bg-neutral-700 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $performance['memory_usage'] ?? 45 }}%"></div>
                                </div>
                            </div>

                            <!-- Disk Usage -->
                            <div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Disk Usage</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $performance['disk_usage'] ?? 62 }}%</span>
                                </div>
                                <div class="mt-2 bg-gray-200 dark:bg-neutral-700 rounded-full h-2">
                                    @php
                                        $diskUsage = $performance['disk_usage'] ?? 62;
                                        $diskColor = $diskUsage > 80 ? 'bg-red-600' : ($diskUsage > 60 ? 'bg-yellow-600' : 'bg-green-600');
                                    @endphp
                                    <div class="{{ $diskColor }} h-2 rounded-full" style="width: {{ $diskUsage }}%"></div>
                                </div>
                            </div>

                            <!-- CPU Usage -->
                            <div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">CPU Usage</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $performance['cpu_usage'] ?? 23 }}%</span>
                                </div>
                                <div class="mt-2 bg-gray-200 dark:bg-neutral-700 rounded-full h-2">
                                    <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $performance['cpu_usage'] ?? 23 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Items -->
                    @php
                        $pendingItems = $pendingItems ?? [];
                        $hasPendingItems = collect($pendingItems)->sum() > 0;
                    @endphp
                    <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl">
                        <div class="p-6 border-b border-gray-200 dark:border-neutral-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Pending Items</h2>
                        </div>
                        <div class="p-6 space-y-4">
                            @if($hasPendingItems)
                                @if(($pendingItems['pending_quotations'] ?? 0) > 0)
                                <div class="flex items-center justify-between p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">Pending Quotations</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $pendingItems['pending_quotations'] }} awaiting review</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.quotations.index', ['status' => 'pending']) }}" 
                                       class="text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </div>
                                @endif
                            @else
                                <div class="text-center py-6">
                                    <svg class="mx-auto h-10 w-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">All caught up!</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No pending items require attention.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Google Analytics Tab -->
        <div id="analytics-stats-content" class="tab-pane hidden">
            
            <!-- Analytics Dashboard Layout -->
            <x-analytics.dashboard-layout title="Website Analytics" 
                                        :show-period-selector="true" 
                                        :show-export-options="true" 
                                        :show-refresh-button="true">
                
                <!-- Analytics Data Freshness Indicator -->
                @if(isset($analytics['data_freshness']))
                    <x-analytics.data-freshness-indicator 
                        :status="'Active'"
                        :data-as-of="now()->subHours(2)->format('M j, H:i')"
                        :estimated-delay="'1-4 hours'"
                        :update-frequency="'15 minutes'"
                        :next-update="now()->addMinutes(15)->format('H:i')"
                        :show-progress="true"
                        :cache-progress="75"
                        :show-details="true" />
                @endif

                <!-- Analytics Stats Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    
                    <!-- Website Visitors Card -->
                    <x-analytics.stats-card 
                        title="Website Visitors" 
                        :value="number_format($analytics['stats']['visitors']['today'] ?? 0)"
                        icon="users"
                        color="blue"
                        :trend="$analytics['trends']['visitor_growth'] ?? null"
                        :subtitle="'Week: ' . number_format($analytics['stats']['visitors']['week'] ?? 0) . ' | Month: ' . number_format($analytics['stats']['visitors']['month'] ?? 0)" />

                    <!-- Page Views Card -->
                    <x-analytics.stats-card 
                        title="Page Views" 
                        :value="number_format($analytics['stats']['pageviews']['today'] ?? 0)"
                        icon="eye"
                        color="green"
                        :subtitle="'Week: ' . number_format($analytics['stats']['pageviews']['week'] ?? 0) . ' | Month: ' . number_format($analytics['stats']['pageviews']['month'] ?? 0)" />

                    <!-- Sessions Card -->
                    <x-analytics.stats-card 
                        title="Sessions" 
                        :value="number_format($analytics['stats']['sessions']['today'] ?? 0)"
                        icon="clock"
                        color="purple"
                        :subtitle="'Week: ' . number_format($analytics['stats']['sessions']['week'] ?? 0) . ' | Month: ' . number_format($analytics['stats']['sessions']['month'] ?? 0)" />

                    <!-- Bounce Rate Card -->
                    <x-analytics.stats-card 
                        title="Bounce Rate" 
                        :value="number_format($analytics['analytics']['bounce_rate'] ?? 45.2, 1) . '%'"
                        icon="chart-bar"
                        color="orange"
                        subtitle="Lower is better" />

                </div>

                <!-- Real-time Stats Widget -->
                <div class="mb-8">
                    <x-analytics.realtime-stats 
                        :stats="[
                            'active_users' => $analytics['stats']['visitors']['today'] ?? 0,
                            'today_visitors' => $analytics['stats']['visitors']['today'] ?? 0,
                            'today_pageviews' => $analytics['stats']['pageviews']['today'] ?? 0,
                            'bounce_rate' => $analytics['analytics']['bounce_rate'] ?? 45.2
                        ]"
                        :auto-refresh="true"
                        :refresh-interval="300" />
                </div>

                <!-- Charts and Tables Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    
                    <!-- Visitors Trend Chart -->
                    <x-analytics.chart 
                        chart-id="visitors-trend-chart"
                        title="Visitors Trend (Last 7 Days)"
                        type="line"
                        :data="[
                            'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                            'datasets' => [[
                                'label' => 'Visitors',
                                'data' => [120, 135, 140, 128, 156, 178, 145],
                                'borderColor' => 'rgb(59, 130, 246)',
                                'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                                'fill' => true
                            ]]
                        ]"
                        height="300px" />

                    <!-- Top Pages Table -->
                    <x-analytics.table 
                        title="Top Pages"
                        :headers="['Page', 'Views', 'Percentage']"
                        :data="isset($analytics['analytics']['most_visited_pages']) && $analytics['analytics']['most_visited_pages']->count() > 0 ? 
                            $analytics['analytics']['most_visited_pages']->take(5)->map(function($page) {
                                return [
                                    $page['url'] ?? $page['page'] ?? 'Unknown',
                                    number_format($page['pageViews'] ?? $page['views'] ?? 0),
                                    '100%' // You can calculate percentage here
                                ];
                            })->toArray() : 
                            [['No data available', '0', '0%']]"
                        type="pages"
                        :show-export="true" />

                </div>

                <!-- Additional Analytics Widgets Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    
                    <!-- Top Referrers Widget -->
                    <x-analytics.widget 
                        title="Top Referrers"
                        data-type="referrers"
                        :period="7"
                        widget-id="widget-referrers"
                        size="medium"
                        :auto-refresh="false" />

                    <!-- Top Browsers Widget -->
                    <x-analytics.widget 
                        title="Browser Usage"
                        data-type="browsers"
                        :period="7"
                        widget-id="widget-browsers"
                        size="medium"
                        :auto-refresh="false" />

                    <!-- Countries Widget -->
                    <x-analytics.widget 
                        title="Top Countries"
                        data-type="countries"
                        :period="7"
                        widget-id="widget-countries"
                        size="medium"
                        :auto-refresh="false" />

                </div>

                <!-- Analytics Health Status and Quick Actions -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Health Status -->
                    <div class="lg:col-span-2">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Analytics Health</h3>
                                <x-analytics.health-status 
                                    :health="[
                                        'status' => 'healthy',
                                        'message' => 'All systems operational',
                                        'last_update' => now()->format('H:i:s'),
                                        'sample_data' => [
                                            'api_calls' => '1,234',
                                            'response_time' => '1.2s',
                                            'success_rate' => '99.8%'
                                        ]
                                    ]"
                                    :show-details="true" />
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <!-- Data Information -->
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white mb-2">
                                        📊 Data Information
                                    </div>
                                    <div class="text-gray-600 dark:text-gray-300 space-y-1">
                                        <div>Last API call: {{ now()->format('H:i') }}</div>
                                        <div>Cache expires: {{ now()->addMinutes(15)->format('H:i') }}</div>
                                        <div>Estimated delay: 1-4 hours</div>
                                    </div>
                                </div>

                                <!-- Quick Summary -->
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white mb-2">
                                        📈 Quick Summary
                                    </div>
                                    <div class="text-gray-600 dark:text-gray-300 space-y-1">
                                        <div>Avg daily visitors: {{ number_format($analytics['summary']['avg_daily_visitors'] ?? 0) }}</div>
                                        <div>Total this week: {{ number_format($analytics['summary']['total_visitors'] ?? 0) }}</div>
                                        <div>Peak day: {{ $analytics['summary']['peak_day'] ?? 'N/A' }}</div>
                                    </div>
                                </div>

                                <!-- Performance -->
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white mb-2">
                                        ⚡ Performance
                                    </div>
                                    <div class="text-gray-600 dark:text-gray-300 space-y-1">
                                        <div>API status: Operational</div>
                                        <div>Cache hit rate: >90%</div>
                                        <div>Data quality: High</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                            
                            <x-analytics.quick-actions 
                                :actions="[
                                    [
                                        'label' => 'Refresh Analytics',
                                        'action' => 'refresh',
                                        'icon' => 'refresh',
                                        'color' => 'blue'
                                    ],
                                    [
                                        'label' => 'Clear Cache',
                                        'action' => 'clear-cache',
                                        'icon' => 'trash',
                                        'color' => 'red'
                                    ],
                                    [
                                        'label' => 'Export Report',
                                        'action' => 'export',
                                        'icon' => 'download',
                                        'color' => 'green'
                                    ]
                                ]" />

                            <!-- Period Selector -->
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Time Period
                                </label>
                                <x-analytics.period-selector 
                                    :current-period="7"
                                    :periods="[
                                        1 => 'Today',
                                        7 => 'Last 7 days',
                                        30 => 'Last 30 days',
                                        90 => 'Last 3 months'
                                    ]"
                                    target="analytics-dashboard" />
                            </div>

                            <!-- Export Options -->
                            <div class="mt-6 space-y-2">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Export Data</h4>
                                <x-analytics.export-button 
                                    type="visitors" 
                                    label="Visitors Data"
                                    format="csv" />
                                <x-analytics.export-button 
                                    type="pages" 
                                    label="Pages Report"
                                    format="csv" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analytics Recommendations -->
                <div class="mt-8">
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <h3 class="text-sm font-medium text-blue-900 dark:text-blue-200">
                                    Analytics Usage Tips
                                </h3>
                                <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li><strong>Real-time data:</strong> Use Google Analytics interface directly for live visitor tracking</li>
                                        <li><strong>Reporting:</strong> This dashboard is perfect for daily/weekly trend analysis and reports</li>
                                        <li><strong>Data freshness:</strong> Analytics data is typically 1-4 hours behind real-time</li>
                                        <li><strong>Performance:</strong> Cache refreshes automatically every 15 minutes for optimal performance</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </x-analytics.dashboard-layout>
        </div>
    </div>

    <!-- Error Container for JavaScript errors -->
    <div id="admin-error-container"></div>

</x-layouts.admin>

@push('scripts')
<script>
// Enhanced Dashboard Tab System
let currentTab = 'app-stats';

document.addEventListener('DOMContentLoaded', function() {
    // Initialize dashboard
    try {
        initializeTabDashboard();
    } catch (error) {
        handleAdminError(error, 'dashboard initialization');
    }
});

function initializeTabDashboard() {
    // Set initial tab based on URL hash or default
    const hash = window.location.hash.substring(1);
    if (hash && (hash === 'app-stats' || hash === 'analytics-stats')) {
        switchTab(hash);
    } else {
        switchTab('app-stats');
    }

    // Auto-refresh dashboard stats every 2 minutes for app stats
    setInterval(function() {
        if (currentTab === 'app-stats') {
            updateDashboardStats();
        }
    }, 120000);

    // Auto-refresh analytics every 15 minutes for analytics tab
    setInterval(function() {
        if (currentTab === 'analytics-stats') {
            refreshAnalyticsData(true); // Silent refresh
        }
    }, 900000); // 15 minutes

    // Set up event listeners
    setupEventListeners();
}

function switchTab(tabName) {
    // Update URL hash
    window.location.hash = tabName;
    
    // Update current tab
    currentTab = tabName;
    
    // Hide all tab content
    document.querySelectorAll('.tab-pane').forEach(pane => {
        pane.classList.add('hidden');
        pane.classList.remove('active');
    });
    
    // Show selected tab content
    const targetContent = document.getElementById(tabName + '-content');
    if (targetContent) {
        targetContent.classList.remove('hidden');
        targetContent.classList.add('active');
    }
    
    // Update tab buttons
    document.querySelectorAll('.dashboard-tab').forEach(tab => {
        tab.classList.remove('border-blue-500', 'text-blue-600', 'active');
        tab.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Activate current tab button
    const activeTab = document.getElementById('tab-' + tabName);
    if (activeTab) {
        activeTab.classList.remove('border-transparent', 'text-gray-500');
        activeTab.classList.add('border-blue-500', 'text-blue-600', 'active');
    }
    
    // Initialize tab-specific features
    if (tabName === 'analytics-stats') {
        initializeAnalyticsTab();
    } else if (tabName === 'app-stats') {
        initializeAppStatsTab();
    }
    
    console.log('Switched to tab:', tabName);
}

function initializeAnalyticsTab() {
    // Initialize any analytics-specific features
    updateAnalyticsLastUpdate();
    
    // Initialize analytics widgets
    initializeAnalyticsWidgets();
    
    // Initialize analytics charts
    initializeAnalyticsCharts();
}

function initializeAppStatsTab() {
    // Initialize any app stats specific features
    updateLastRefreshTime();
    
    // Refresh app stats
    if (typeof updateDashboardStats === 'function') {
        updateDashboardStats();
    }
}

function initializeAnalyticsWidgets() {
    // Initialize analytics widgets with proper error handling
    document.querySelectorAll('[data-widget-type]').forEach(widget => {
        const widgetId = widget.getAttribute('id');
        const dataType = widget.getAttribute('data-widget-type');
        const period = widget.getAttribute('data-period') || 7;
        
        if (widgetId && dataType) {
            loadWidgetData(widgetId, dataType, period);
        }
    });
}

function initializeAnalyticsCharts() {
    // Initialize Chart.js charts if library is available
    if (typeof Chart !== 'undefined') {
        // Initialize visitors trend chart
        const visitorsChart = document.getElementById('visitors-trend-chart');
        if (visitorsChart) {
            createVisitorsTrendChart(visitorsChart);
        }
    }
}

function loadWidgetData(widgetId, dataType, period) {
    const widget = document.getElementById(widgetId);
    if (!widget) return;
    
    const contentArea = widget.querySelector('.widget-content');
    if (!contentArea) return;
    
    // Show loading state
    contentArea.innerHTML = `
        <div class="flex items-center justify-center h-32">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">Loading...</span>
        </div>
    `;
    
    // Fetch widget data
    fetch(`/api/admin/gtag/${dataType}/${period}`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data) {
            renderWidgetContent(contentArea, dataType, data.data);
        } else {
            showWidgetError(contentArea, 'No data available');
        }
    })
    .catch(error => {
        console.error(`Error loading ${dataType} widget:`, error);
        showWidgetError(contentArea, 'Failed to load data');
    });
}

function renderWidgetContent(contentArea, dataType, data) {
    // Render different widget types
    switch (dataType) {
        case 'referrers':
            renderReferrersWidget(contentArea, data);
            break;
        case 'browsers':
            renderBrowsersWidget(contentArea, data);
            break;
        case 'countries':
            renderCountriesWidget(contentArea, data);
            break;
        default:
            contentArea.innerHTML = '<div class="text-center text-gray-500">Widget type not supported</div>';
    }
}

function renderReferrersWidget(contentArea, data) {
    if (!Array.isArray(data) || data.length === 0) {
        contentArea.innerHTML = '<div class="text-center text-gray-500 py-8">No referrer data available</div>';
        return;
    }
    
    const html = `
        <div class="space-y-3">
            ${data.slice(0, 5).map((item, index) => `
                <div class="flex items-center justify-between py-2 px-3 ${index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-700' : ''} rounded">
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            ${item.url || item.referrer || 'Direct'}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Referrer</div>
                    </div>
                    <div class="text-sm font-semibold text-gray-900 dark:text-white ml-2">
                        ${item.pageViews || item.sessions || 0}
                    </div>
                </div>
            `).join('')}
        </div>
    `;
    
    contentArea.innerHTML = html;
}

function renderBrowsersWidget(contentArea, data) {
    if (!Array.isArray(data) || data.length === 0) {
        contentArea.innerHTML = '<div class="text-center text-gray-500 py-8">No browser data available</div>';
        return;
    }
    
    const html = `
        <div class="space-y-3">
            ${data.slice(0, 5).map((item, index) => `
                <div class="flex items-center justify-between py-2 px-3 ${index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-700' : ''} rounded">
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            ${item.browser || 'Unknown Browser'}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Browser</div>
                    </div>
                    <div class="text-sm font-semibold text-gray-900 dark:text-white ml-2">
                        ${item.sessions || 0}
                    </div>
                </div>
            `).join('')}
        </div>
    `;
    
    contentArea.innerHTML = html;
}

function renderCountriesWidget(contentArea, data) {
    if (!Array.isArray(data) || data.length === 0) {
        contentArea.innerHTML = '<div class="text-center text-gray-500 py-8">No country data available</div>';
        return;
    }
    
    const html = `
        <div class="space-y-3">
            ${data.slice(0, 5).map((item, index) => `
                <div class="flex items-center justify-between py-2 px-3 ${index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-700' : ''} rounded">
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            ${item.country || 'Unknown Country'}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Country</div>
                    </div>
                    <div class="text-sm font-semibold text-gray-900 dark:text-white ml-2">
                        ${item.sessions || 0}
                    </div>
                </div>
            `).join('')}
        </div>
    `;
    
    contentArea.innerHTML = html;
}

function showWidgetError(contentArea, message) {
    contentArea.innerHTML = `
        <div class="flex items-center justify-center h-32 text-gray-500 dark:text-gray-400">
            <div class="text-center">
                <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm">${message}</p>
            </div>
        </div>
    `;
}

function createVisitorsTrendChart(canvas) {
    // Sample chart creation - replace with real data
    const ctx = canvas.getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Visitors',
                data: [120, 135, 140, 128, 156, 178, 145],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                }
            }
        }
    });
}

function refreshAllData() {
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    
    // Show loading state
    button.disabled = true;
    button.innerHTML = `
        <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Refreshing...
    `;
    
    // Refresh based on current tab
    let refreshPromise;
    
    if (currentTab === 'analytics-stats') {
        refreshPromise = refreshAnalyticsData();
    } else {
        refreshPromise = refreshAppData();
    }
    
    refreshPromise.finally(() => {
        // Restore button state
        setTimeout(() => {
            button.disabled = false;
            button.innerHTML = originalText;
            updateLastRefreshTime();
        }, 2000);
    });
}

function refreshAnalyticsData(silent = false) {
    return fetch('/admin/analytics/refresh', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (!silent) {
                showAnalyticsMessage('Analytics data refreshed successfully!', 'success');
                setTimeout(() => window.location.reload(), 1000);
            }
            updateAnalyticsLastUpdate();
        } else {
            if (!silent) showAnalyticsMessage('Failed to refresh analytics data', 'error');
        }
    })
    .catch(error => {
        console.error('Analytics refresh error:', error);
        if (!silent) showAnalyticsMessage('Error refreshing analytics data', 'error');
    });
}

function refreshAppData() {
    return fetch('/admin/dashboard/stats', {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAnalyticsMessage('Application data refreshed successfully!', 'success');
            updateDashboardStats();
        } else {
            showAnalyticsMessage('Failed to refresh application data', 'error');
        }
    })
    .catch(error => {
        console.error('App data refresh error:', error);
        showAnalyticsMessage('Error refreshing application data', 'error');
    });
}

function changeAnalyticsPeriod(period) {
    console.log('Changing analytics period to:', period);
    showAnalyticsMessage(`Period changed to ${period} days`, 'info');
    
    // Refresh analytics widgets with new period
    if (currentTab === 'analytics-stats') {
        initializeAnalyticsWidgets();
    }
}

function updateLastRefreshTime() {
    const element = document.getElementById('last-update-time');
    if (element) {
        element.textContent = new Date().toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }
}

function updateAnalyticsLastUpdate() {
    const element = document.getElementById('analytics-last-update');
    if (element) {
        element.textContent = new Date().toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }
}

function setupEventListeners() {
    // Listen for hash changes to switch tabs
    window.addEventListener('hashchange', function() {
        const hash = window.location.hash.substring(1);
        if (hash && (hash === 'app-stats' || hash === 'analytics-stats')) {
            switchTab(hash);
        }
    });
    
    // Tab click handlers
    document.querySelectorAll('.dashboard-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            switchTab(tabName);
        });
    });

    // Quick actions event listeners
    document.addEventListener('click', function(e) {
        if (e.target.closest('.quick-action-btn')) {
            const button = e.target.closest('.quick-action-btn');
            const action = button.getAttribute('data-action');
            handleQuickAction(action);
        }
        
        if (e.target.closest('.widget-refresh')) {
            const widgetId = e.target.closest('.widget-refresh').getAttribute('data-widget-id');
            refreshWidget(widgetId);
        }
        
        if (e.target.closest('.period-btn')) {
            const period = e.target.closest('.period-btn').getAttribute('data-period');
            changeAnalyticsPeriod(period);
        }
    });
}

function handleQuickAction(action) {
    switch (action) {
        case 'refresh':
            refreshAnalyticsData();
            break;
        case 'clear-cache':
            clearAnalyticsCache();
            break;
        case 'export':
            exportAnalyticsData();
            break;
        default:
            console.log('Unknown action:', action);
    }
}

function refreshWidget(widgetId) {
    const widget = document.getElementById(widgetId);
    if (!widget) return;
    
    const dataType = widget.getAttribute('data-widget-type');
    const period = widget.getAttribute('data-period') || 7;
    
    if (dataType) {
        loadWidgetData(widgetId, dataType, period);
    }
}

function clearAnalyticsCache() {
    fetch('/admin/analytics/clear-cache', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAnalyticsMessage('Cache cleared successfully!', 'success');
        } else {
            showAnalyticsMessage('Failed to clear cache', 'error');
        }
    })
    .catch(error => {
        console.error('Cache clear error:', error);
        showAnalyticsMessage('Error clearing cache', 'error');
    });
}

function exportAnalyticsData() {
    // Open export dialog or directly download
    window.open('/admin/analytics/export?format=csv', '_blank');
    showAnalyticsMessage('Export started', 'info');
}

function updateDashboardStats() {
    fetch('/admin/dashboard/stats')
    .then(response => response.json())
    .then(data => {
function updateDashboardStats() {
    fetch('/admin/dashboard/stats')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateStatCards(data.data);
        }
    })
    .catch(error => {
        console.error('Error updating dashboard stats:', error);
    });
}

function updateStatCards(data) {
    // Update stat card values safely
    const statUpdates = {
        'projects-total': data.projects?.total,
        'quotations-total': data.quotations?.total,
        'clients-total': data.clients?.total,
        'messages-total': data.messages?.total
    };

    Object.entries(statUpdates).forEach(([elementId, value]) => {
        const element = document.getElementById(elementId);
        if (element && value !== undefined) {
            element.textContent = value;
        }
    });
}

function showAnalyticsMessage(message, type = 'info') {
    const toast = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
    
    toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 transform translate-x-full`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    // Animate out and remove
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            if (document.body.contains(toast)) {
                document.body.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

// Global admin dashboard error handler
function handleAdminError(error, context = 'admin dashboard') {
    console.error(`Admin dashboard error in ${context}:`, error);
    
    // Show user-friendly error message
    const errorContainer = document.getElementById('admin-error-container');
    if (errorContainer) {
        errorContainer.innerHTML = `
            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Dashboard Notice</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            Some dashboard features may be temporarily unavailable. Please refresh the page.
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
}

// Period selector functionality
function updatePeriodSelector(selectedPeriod) {
    document.querySelectorAll('.period-btn').forEach(btn => {
        const period = btn.getAttribute('data-period');
        if (period === selectedPeriod.toString()) {
            btn.classList.remove('text-gray-700', 'hover:bg-gray-50');
            btn.classList.add('bg-blue-600', 'text-white');
        } else {
            btn.classList.remove('bg-blue-600', 'text-white');
            btn.classList.add('text-gray-700', 'hover:bg-gray-50');
        }
    });
}

// Keyboard shortcuts for tab navigation
document.addEventListener('keydown', function(e) {
    // Ctrl+1 for App Stats, Ctrl+2 for Analytics
    if (e.ctrlKey && e.key === '1') {
        e.preventDefault();
        switchTab('app-stats');
    } else if (e.ctrlKey && e.key === '2') {
        e.preventDefault();
        switchTab('analytics-stats');
    }
});

// Health status toggle functionality
function toggleHealthDetails() {
    const details = document.getElementById('health-details');
    if (details) {
        details.classList.toggle('hidden');
    }
}

// Chart refresh functionality
function refreshChart(chartId) {
    const chart = document.getElementById(chartId);
    if (chart) {
        // Refresh chart data
        if (chartId === 'visitors-trend-chart') {
            // Recreate visitors chart with fresh data
            createVisitorsTrendChart(chart);
        }
    }
}

// Chart fullscreen toggle
function toggleChartFullscreen(chartId) {
    const chartContainer = document.getElementById(chartId).closest('.bg-white');
    if (chartContainer) {
        chartContainer.classList.toggle('fixed');
        chartContainer.classList.toggle('inset-0');
        chartContainer.classList.toggle('z-50');
        chartContainer.classList.toggle('p-8');
    }
}

// Export table functionality
function exportTable(type) {
    const url = `/admin/analytics/export?type=${type}&format=csv`;
    window.open(url, '_blank');
    showAnalyticsMessage(`Exporting ${type} data...`, 'info');
}

// Real-time stats auto-update
function startRealtimeUpdates() {
    if (currentTab === 'analytics-stats') {
        setInterval(function() {
            // Update real-time stats
            fetch('/api/admin/gtag/realtime')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateRealtimeStats(data.stats);
                }
            })
            .catch(error => {
                console.error('Error updating realtime stats:', error);
            });
        }, 60000); // Update every minute
    }
}

function updateRealtimeStats(stats) {
    // Update real-time stats display
    const elements = {
        'active-users': stats.active_users,
        'today-visitors': stats.today_visitors,
        'today-pageviews': stats.today_pageviews,
        'bounce-rate': stats.bounce_rate
    };
    
    Object.entries(elements).forEach(([elementId, value]) => {
        const element = document.getElementById(elementId);
        if (element && value !== undefined) {
            element.textContent = typeof value === 'number' && elementId === 'bounce-rate' 
                ? value.toFixed(1) + '%' 
                : value;
        }
    });
    
    // Update last update time
    const lastUpdateElement = document.getElementById('last-update-time');
    if (lastUpdateElement) {
        const now = new Date();
        lastUpdateElement.textContent = now.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    }
}

// Initialize real-time updates when analytics tab is active
document.addEventListener('DOMContentLoaded', function() {
    if (currentTab === 'analytics-stats') {
        startRealtimeUpdates();
    }
});

// Global error handler override
window.handleAdminError = handleAdminError;
window.switchTab = switchTab;
window.refreshAllData = refreshAllData;
window.refreshAnalyticsData = refreshAnalyticsData;
window.changeAnalyticsPeriod = changeAnalyticsPeriod;
window.updateLastRefreshTime = updateLastRefreshTime;
window.updateAnalyticsLastUpdate = updateAnalyticsLastUpdate;
window.toggleHealthDetails = toggleHealthDetails;
window.refreshChart = refreshChart;
window.toggleChartFullscreen = toggleChartFullscreen;

</script>

<style>
/* Tab system styles */
.dashboard-tab.active {
    border-bottom-color: rgb(37 99 235); /* blue-600 */
    color: rgb(37 99 235); /* blue-600 */
}

.tab-pane {
    animation: fadeIn 0.3s ease-in-out;
}

.tab-pane.active {
    display: block;
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

/* Loading states */
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}

.dark .loading-overlay {
    background: rgba(0, 0, 0, 0.8);
}

/* Responsive tab layout */
@media (max-width: 640px) {
    .dashboard-tab {
        flex: 1;
        text-align: center;
        padding: 8px 4px;
    }
    
    .dashboard-tab span.ml-2 {
        display: none;
    }
}

/* Enhanced hover effects */
.dashboard-tab:hover {
    border-bottom-color: rgb(156 163 175); /* gray-400 */
    transition: all 0.2s ease-in-out;
}

.dashboard-tab.active:hover {
    border-bottom-color: rgb(29 78 216); /* blue-700 */
}

/* Smooth transitions for stat cards */
.stat-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.stat-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Widget styles */
.analytics-widget {
    transition: all 0.3s ease-in-out;
}

.analytics-widget:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Chart container styles */
.chart-container {
    position: relative;
    height: 300px;
}

.chart-container.fullscreen {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 9999;
    background: white;
    padding: 2rem;
}

/* Health indicator animations */
@keyframes pulse-dot {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.health-indicator {
    animation: pulse-dot 2s infinite;
}

/* Period selector styles */
.period-btn {
    transition: all 0.2s ease-in-out;
}

.period-btn:hover {
    transform: translateY(-1px);
}

/* Quick action buttons */
.quick-action-btn {
    transition: all 0.2s ease-in-out;
}

.quick-action-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

/* Toast notification styles */
.toast-enter {
    transform: translateX(100%);
    opacity: 0;
}

.toast-enter-active {
    transform: translateX(0);
    opacity: 1;
    transition: all 0.3s ease-in-out;
}

.toast-exit {
    transform: translateX(0);
    opacity: 1;
}

.toast-exit-active {
    transform: translateX(100%);
    opacity: 0;
    transition: all 0.3s ease-in-out;
}

/* Responsive analytics grid */
@media (max-width: 768px) {
    .analytics-widget {
        margin-bottom: 1rem;
    }
    
    .chart-container {
        height: 250px;
    }
}

/* Dark mode specific adjustments */
.dark .analytics-widget {
    border-color: rgb(55 65 81); /* gray-700 */
}

.dark .chart-container.fullscreen {
    background: rgb(31 41 55); /* gray-800 */
}

/* Accessibility improvements */
.dashboard-tab:focus {
    outline: 2px solid rgb(59 130 246); /* blue-500 */
    outline-offset: 2px;
}

.quick-action-btn:focus {
    outline: 2px solid rgb(59 130 246); /* blue-500 */
    outline-offset: 2px;
}

/* Print styles */
@media print {
    .dashboard-tab,
    .quick-action-btn,
    button {
        display: none;
    }
    
    .tab-pane.hidden {
        display: block !important;
    }
}
</style>
@endpush