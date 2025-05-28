{{-- resources/views/client/dashboard.blade.php --}}
<x-layouts.client :title="'Client Dashboard'" :enableCharts="true" :unreadMessages="$notifications['unread_messages'] ?? 0" :pendingApprovals="$notifications['pending_approvals'] ?? 0">
    
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            Welcome back, {{ auth()->user()->name }}!
        </h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Here's what's happening with your projects and quotations.
        </p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Projects Card -->
        <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl p-6 shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Projects</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $statistics['projects']['total'] ?? 0 }}</p>
                    <p class="text-xs text-green-600 dark:text-green-400">
                        {{ $statistics['projects']['active'] ?? 0 }} active
                    </p>
                </div>
            </div>
        </div>

        <!-- Quotations Card -->
        <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl p-6 shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Quotations</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $statistics['quotations']['total'] ?? 0 }}</p>
                    <p class="text-xs text-yellow-600 dark:text-yellow-400">
                        {{ $statistics['quotations']['pending'] ?? 0 }} pending
                    </p>
                </div>
            </div>
        </div>

        <!-- Messages Card -->
        <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl p-6 shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Messages</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $statistics['messages']['total'] ?? 0 }}</p>
                    <p class="text-xs text-red-600 dark:text-red-400">
                        {{ $statistics['messages']['unread'] ?? 0 }} unread
                    </p>
                </div>
            </div>
        </div>

        <!-- Completion Rate Card -->
        <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl p-6 shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00-2-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Completion Rate</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $statistics['summary']['completion_rate'] ?? 0 }}%</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $statistics['projects']['completed'] ?? 0 }} completed
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications Panel -->
    @if(array_sum($notifications ?? []) > 0)
    <div class="mb-8">
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Attention Required</h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        <ul class="list-disc pl-5 space-y-1">
                            @if(($notifications['unread_messages'] ?? 0) > 0)
                                <li>You have {{ $notifications['unread_messages'] }} unread messages</li>
                            @endif
                            @if(($notifications['pending_approvals'] ?? 0) > 0)
                                <li>{{ $notifications['pending_approvals'] }} quotations awaiting your approval</li>
                            @endif
                            @if(($notifications['overdue_projects'] ?? 0) > 0)
                                <li>{{ $notifications['overdue_projects'] }} projects are overdue</li>
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
        
        <!-- Left Column: Recent Activities & Quick Actions -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Recent Activities -->
            <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl">
                <div class="p-6 border-b border-gray-200 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Activities</h2>
                </div>
                <div class="p-6">
                    @if(count($recentActivities ?? []) > 0)
                        <div class="flow-root">
                            <ul class="-mb-8">
                                @foreach(array_slice($recentActivities, 0, 8) as $index => $activity)
                                <li>
                                    <div class="relative pb-8">
                                        @if($index < count($recentActivities) - 1)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-neutral-700" aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-{{ $activity['color'] }}-100 dark:bg-{{ $activity['color'] }}-900/30 flex items-center justify-center ring-8 ring-white dark:ring-neutral-800">
                                                    <svg class="h-4 w-4 text-{{ $activity['color'] }}-600 dark:text-{{ $activity['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        @if($activity['icon'] === 'folder')
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                                        @elseif($activity['icon'] === 'document-text')
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        @else
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                        @endif
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-900 dark:text-white font-medium">{{ $activity['title'] }}</p>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $activity['description'] }}</p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                    <time datetime="{{ $activity['date']->toISOString() }}">{{ $activity['date']->diffForHumans() }}</time>
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
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Your recent project and quotation activities will appear here.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl">
                <div class="p-6 border-b border-gray-200 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($quickActions ?? [] as $action)
                        <a href="{{ $action['url'] }}" class="block p-4 border border-gray-200 dark:border-neutral-700 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-700 transition-colors">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-{{ $action['color'] }}-100 dark:bg-{{ $action['color'] }}-900/30 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-{{ $action['color'] }}-600 dark:text-{{ $action['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($action['icon'] === 'document-add')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            @elseif($action['icon'] === 'mail')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            @elseif($action['icon'] === 'star')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            @endif
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ $action['title'] }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $action['description'] }}</p>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Upcoming Deadlines & Summary -->
        <div class="space-y-8">
            
            <!-- Upcoming Deadlines -->
            <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl">
                <div class="p-6 border-b border-gray-200 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Upcoming Deadlines</h2>
                </div>
                <div class="p-6">
                    @if(count($upcomingDeadlines ?? []) > 0)
                        <div class="space-y-4">
                            @foreach(array_slice($upcomingDeadlines, 0, 5) as $deadline)
                            <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-neutral-700 rounded-lg
                                {{ $deadline['urgency'] === 'critical' ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800' : 
                                   ($deadline['urgency'] === 'high' ? 'bg-orange-50 dark:bg-orange-900/20 border-orange-200 dark:border-orange-800' : 
                                   'bg-gray-50 dark:bg-neutral-700') }}">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $deadline['title'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $deadline['location'] ?? 'No location' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $deadline['days_until'] }} days</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $deadline['date']->format('M d') }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No upcoming deadlines</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">All caught up! No project deadlines in the next 30 days.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Performance Summary -->
            <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl">
                <div class="p-6 border-b border-gray-200 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Performance Summary</h2>
                </div>
                <div class="p-6 space-y-4">
                    <!-- Completion Rate -->
                    <div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Project Completion Rate</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $statistics['summary']['completion_rate'] ?? 0 }}%</span>
                        </div>
                        <div class="mt-2 bg-gray-200 dark:bg-neutral-700 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $statistics['summary']['completion_rate'] ?? 0 }}%"></div>
                        </div>
                    </div>

                    <!-- Response Rate -->
                    <div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Message Response Rate</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $statistics['summary']['response_rate'] ?? 0 }}%</span>
                        </div>
                        <div class="mt-2 bg-gray-200 dark:bg-neutral-700 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: {{ $statistics['summary']['response_rate'] ?? 0 }}%"></div>
                        </div>
                    </div>

                    <!-- This Week Activity -->
                    <div class="pt-4 border-t border-gray-200 dark:border-neutral-700">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">This Week</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">New Messages</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $statistics['messages']['this_week'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Project Updates</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ count(array_filter($recentActivities ?? [], fn($activity) => $activity['type'] === 'project' && $activity['date']->isCurrentWeek())) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-layouts.client>