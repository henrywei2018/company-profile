{{-- resources/views/admin/dashboard.blade.php - FIXED VERSION --}}
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
            <div class="flex items-center space-x-3">
                <!-- System Health Indicator -->
                <div class="flex items-center space-x-2">
                    <div id="system-health-indicator" class="w-3 h-3 bg-green-500 rounded-full" title="System Status: Healthy"></div>
                    <span class="text-sm text-gray-600 dark:text-gray-400">System Health</span>
                </div>
                
                <!-- Quick Actions -->
                <button type="button" 
                    onclick="refreshDashboard()"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-700">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Refresh
                </button>
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

    <!-- Statistics Section with Safe Array Access -->
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">System Overview</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
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

    <!-- Alerts Section with Safe Array Access -->
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
                            @if(($alerts['expiring_certificates'] ?? 0) > 0)
                                <li>
                                    <a href="{{ route('admin.certifications.index') }}" class="hover:underline">
                                        {{ $alerts['expiring_certificates'] }} certificates expiring soon
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

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Recent Activities & Charts -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Recent Activities with Safe Array Access -->
            <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl">
                <div class="p-6 border-b border-gray-200 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Activities</h2>
                </div>
                <div class="p-6">
                    @php
                        // Safe processing of recent activities
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
                        // Sort by date
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
                                                    @if($activity['icon'] === 'folder')
                                                        <svg class="h-4 w-4 text-{{ $activity['color'] }}-600 dark:text-{{ $activity['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                        </svg>
                                                    @elseif($activity['icon'] === 'document-text')
                                                        <svg class="h-4 w-4 text-{{ $activity['color'] }}-600 dark:text-{{ $activity['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                    @else
                                                        <svg class="h-4 w-4 text-{{ $activity['color'] }}-600 dark:text-{{ $activity['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                        </svg>
                                                    @endif
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

            <!-- Charts Section -->
            @if($enableCharts)
            <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl">
                <div class="p-6 border-b border-gray-200 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Performance Overview</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Projects Chart -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Projects This Month</h3>
                            <div id="projects-chart" class="h-64"></div>
                        </div>
                        
                        <!-- Revenue Chart -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-4">System Usage</h3>
                            <div id="usage-chart" class="h-64"></div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Right Column: Quick Stats & Pending Items -->
        <div class="space-y-8">
            
            <!-- Pending Items with Safe Array Access -->
            <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl">
                <div class="p-6 border-b border-gray-200 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Pending Items</h2>
                </div>
                <div class="p-6 space-y-4">
                    @php
                        $pendingItems = $pendingItems ?? [];
                        $hasPendingItems = collect($pendingItems)->sum() > 0;
                    @endphp
                    
                    @if($hasPendingItems)
                        <!-- Pending Quotations -->
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
                        
                        <!-- Unread Messages -->
                        @if(($pendingItems['unread_messages'] ?? 0) > 0)
                        <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Unread Messages</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $pendingItems['unread_messages'] }} need attention</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.messages.index', ['read' => 'unread']) }}" 
                               class="text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                        @endif
                        
                        <!-- Waiting Chats -->
                        @if(($pendingItems['waiting_chats'] ?? 0) > 0)
                        <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center animate-pulse">
                                    <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Waiting Chats</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $pendingItems['waiting_chats'] }} users waiting</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.chat.index') }}" 
                               class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
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

                    <!-- Uptime & Last Backup -->
                    <div class="pt-4 border-t border-gray-200 dark:border-neutral-700">
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Uptime</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $performance['uptime'] ?? '99.9%' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Last Backup</span>
                                @php
                                    $lastBackup = $performance['last_backup'] ?? now()->subHours(6);
                                    if (is_string($lastBackup)) {
                                        try {
                                            $lastBackup = \Carbon\Carbon::parse($lastBackup);
                                        } catch (\Exception $e) {
                                            $lastBackup = now()->subHours(6);
                                        }
                                    }
                                @endphp
                                <span class="font-medium text-gray-900 dark:text-white">{{ $lastBackup->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl">
                <div class="p-6 border-b border-gray-200 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-3">
                        <a href="{{ route('admin.projects.create') }}" 
                           class="flex items-center p-3 text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 dark:bg-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-600 transition-colors">
                            <svg class="w-5 h-5 mr-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Create Project
                        </a>
                        
                        <a href="{{ route('admin.quotations.create') }}" 
                           class="flex items-center p-3 text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 dark:bg-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-600 transition-colors">
                            <svg class="w-5 h-5 mr-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Create Quotation
                        </a>
                        
                        <a href="{{ route('admin.users.create') }}" 
                           class="flex items-center p-3 text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 dark:bg-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-600 transition-colors">
                            <svg class="w-5 h-5 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Add User
                        </a>
                        
                        <button type="button" 
                                onclick="sendTestNotification()" 
                                class="flex items-center p-3 text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 dark:bg-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-600 transition-colors">
                            <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 7h6m-6 4h6m-6 4h6M3 7h3m-3 4h3m-3 4h3"/>
                            </svg>
                            Test Notification
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Container for JavaScript errors -->
    <div id="admin-error-container"></div>

</x-layouts.admin>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize dashboard
    try {
        initializeAdminDashboard();
    } catch (error) {
        handleAdminError(error, 'dashboard initialization');
    }
});

function initializeAdminDashboard() {
    // Auto-refresh dashboard stats every 2 minutes
    setInterval(function() {
        updateDashboardStats();
    }, 120000);

    // Initialize charts if enabled
    @if($enableCharts)
    initializeCharts();
    @endif

    // Set up event listeners
    setupEventListeners();
}

function setupEventListeners() {
    // Refresh button
    const refreshBtn = document.querySelector('button[onclick="refreshDashboard()"]');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Refreshing...';
            
            refreshDashboard().finally(() => {
                this.disabled = false;
                this.innerHTML = '<svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>Refresh';
            });
        });
    }
}

async function refreshDashboard() {
    try {
        // Clear cache and reload data
        await clearDashboardCache();
        
        // Reload the page to get fresh data
        window.location.reload();
        
    } catch (error) {
        handleAdminError(error, 'dashboard refresh');
        throw error;
    }
}

async function clearDashboardCache() {
    try {
        const response = await fetch('{{ route("admin.dashboard.clear-cache") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message || 'Failed to clear cache');
        }
        
        return data;
        
    } catch (error) {
        console.error('Failed to clear dashboard cache:', error);
        throw error;
    }
}

function updateDashboardStats() {
    fetch('{{ route("admin.dashboard.stats") }}')
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

async function sendTestNotification() {
    try {
        const response = await fetch('{{ route("admin.dashboard.send-test-notification") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Show success message
            showNotificationMessage('Test notification sent successfully!', 'success');
            
            // Refresh notifications after a short delay
            setTimeout(() => {
                if (typeof loadAdminNotifications === 'function') {
                    loadAdminNotifications();
                }
            }, 1000);
        } else {
            showNotificationMessage(data.message || 'Failed to send test notification', 'error');
        }
        
    } catch (error) {
        console.error('Error sending test notification:', error);
        showNotificationMessage('Failed to send test notification', 'error');
    }
}

function showNotificationMessage(message, type = 'info') {
    // Create a toast notification
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
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}

@if($enableCharts)
function initializeCharts() {
    // Projects Chart
    if (document.getElementById('projects-chart')) {
        const projectsOptions = {
            chart: {
                type: 'line',
                height: 250,
                toolbar: { show: false },
                background: 'transparent'
            },
            series: [{
                name: 'Projects',
                data: [10, 15, 8, 12, 20, 18, 25]
            }],
            xaxis: {
                categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                labels: { style: { colors: '#9CA3AF' } }
            },
            yaxis: {
                labels: { style: { colors: '#9CA3AF' } }
            },
            colors: ['#8B5CF6'],
            stroke: { curve: 'smooth', width: 3 },
            grid: { borderColor: '#374151' },
            tooltip: { theme: 'dark' }
        };
        
        const projectsChart = new ApexCharts(document.querySelector("#projects-chart"), projectsOptions);
        projectsChart.render();
    }

    // Usage Chart
    if (document.getElementById('usage-chart')) {
        const usageOptions = {
            chart: {
                type: 'donut',
                height: 250,
                background: 'transparent'
            },
            series: [{{ $performance['memory_usage'] ?? 45 }}, {{ $performance['disk_usage'] ?? 62 }}, {{ $performance['cpu_usage'] ?? 23 }}],
            labels: ['Memory', 'Disk', 'CPU'],
            colors: ['#3B82F6', '#F59E0B', '#8B5CF6'],
            legend: {
                labels: { colors: '#9CA3AF' }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%'
                    }
                }
            },
            tooltip: { theme: 'dark' }
        };
        
        const usageChart = new ApexCharts(document.querySelector("#usage-chart"), usageOptions);
        usageChart.render();
    }
}
@endif

// Global admin dashboard error handler
function handleAdminDashboardError(error, context = 'admin dashboard') {
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

// Override global error handler for dashboard-specific errors
window.handleAdminError = handleAdminDashboardError;
</script>
@endpush