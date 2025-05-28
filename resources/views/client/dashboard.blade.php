{{-- resources/views/client/dashboard.blade.php --}}
<x-layouts.client 
    :title="'Dashboard'" 
    :enableCharts="true" 
    :unreadMessages="$statistics['messages']['unread'] ?? 0" 
    :pendingApprovals="$statistics['quotations']['awaiting_approval'] ?? 0">

    <!-- Page Header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Dashboard</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Welcome back, {{ auth()->user()->name }}! Here's what's happening with your projects.</p>
        </div>
        
        <!-- Quick Actions -->
        <div class="flex items-center space-x-2 sm:space-x-3">
            <a href="{{ route('client.quotations.create') }}" 
               class="inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Request Quote
            </a>
            <a href="{{ route('client.messages.create') }}" 
               class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                Send Message
            </a>
        </div>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
        <!-- Projects Card -->
        <div class="flex flex-col bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl shadow-sm">
            <div class="p-4 md:p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex justify-center items-center size-[46px] bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <svg class="flex-shrink-0 size-5 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="grow ms-5">
                        <div class="flex items-center gap-x-2">
                            <h3 class="text-xs uppercase tracking-wide text-gray-500 dark:text-neutral-500">
                                Total Projects
                            </h3>
                        </div>
                        <div class="mt-1 flex items-center gap-x-2">
                            <h3 class="text-xl sm:text-2xl font-medium text-gray-800 dark:text-neutral-200">
                                {{ $statistics['projects']['total'] ?? 0 }}
                            </h3>
                            @if(($statistics['projects']['total'] ?? 0) > 0)
                                <span class="flex items-center gap-x-1 text-green-600">
                                    <svg class="inline-block size-4 self-center" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="22,7 13.5,15.5 8.5,10.5 2,17"/>
                                        <polyline points="16,7 22,7 22,13"/>
                                    </svg>
                                    <span class="inline-block text-xs">Active</span>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <a class="py-3 px-4 md:px-5 inline-flex justify-between items-center text-sm text-gray-600 dark:text-neutral-400 border-t border-gray-200 dark:border-neutral-700 hover:bg-gray-50 dark:hover:bg-neutral-750 rounded-b-xl" href="{{ route('client.projects.index') }}">
                View projects
                <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m9 18 6-6-6-6"/>
                </svg>
            </a>
        </div>

        <!-- Quotations Card -->
        <div class="flex flex-col bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl shadow-sm">
            <div class="p-4 md:p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex justify-center items-center size-[46px] bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                            <svg class="flex-shrink-0 size-5 text-amber-600 dark:text-amber-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14,2 14,8 20,8"/>
                                <line x1="16" y1="13" x2="8" y2="13"/>
                                <line x1="16" y1="17" x2="8" y2="17"/>
                                <polyline points="10,9 9,9 8,9"/>
                            </svg>
                        </div>
                    </div>
                    <div class="grow ms-5">
                        <div class="flex items-center gap-x-2">
                            <h3 class="text-xs uppercase tracking-wide text-gray-500 dark:text-neutral-500">
                                Quotations
                            </h3>
                            @if(($statistics['quotations']['awaiting_approval'] ?? 0) > 0)
                                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-lg text-xs font-medium bg-red-100 text-red-800 dark:bg-red-800/30 dark:text-red-500">
                                    {{ $statistics['quotations']['awaiting_approval'] }} pending
                                </span>
                            @endif
                        </div>
                        <div class="mt-1 flex items-center gap-x-2">
                            <h3 class="text-xl sm:text-2xl font-medium text-gray-800 dark:text-neutral-200">
                                {{ $statistics['quotations']['total'] ?? 0 }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
            <a class="py-3 px-4 md:px-5 inline-flex justify-between items-center text-sm text-gray-600 dark:text-neutral-400 border-t border-gray-200 dark:border-neutral-700 hover:bg-gray-50 dark:hover:bg-neutral-750 rounded-b-xl" href="{{ route('client.quotations.index') }}">
                View quotations
                <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m9 18 6-6-6-6"/>
                </svg>
            </a>
        </div>

        <!-- Messages Card -->
        <div class="flex flex-col bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl shadow-sm">
            <div class="p-4 md:p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex justify-center items-center size-[46px] bg-green-100 dark:bg-green-900/30 rounded-lg">
                            <svg class="flex-shrink-0 size-5 text-green-600 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                        </div>
                    </div>
                    <div class="grow ms-5">
                        <div class="flex items-center gap-x-2">
                            <h3 class="text-xs uppercase tracking-wide text-gray-500 dark:text-neutral-500">
                                Messages
                            </h3>
                            @if(($statistics['messages']['unread'] ?? 0) > 0)
                                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-lg text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800/30 dark:text-blue-500">
                                    {{ $statistics['messages']['unread'] }} unread
                                </span>
                            @endif
                        </div>
                        <div class="mt-1 flex items-center gap-x-2">
                            <h3 class="text-xl sm:text-2xl font-medium text-gray-800 dark:text-neutral-200">
                                {{ $statistics['messages']['total'] ?? 0 }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
            <a class="py-3 px-4 md:px-5 inline-flex justify-between items-center text-sm text-gray-600 dark:text-neutral-400 border-t border-gray-200 dark:border-neutral-700 hover:bg-gray-50 dark:hover:bg-neutral-750 rounded-b-xl" href="{{ route('client.messages.index') }}">
                View messages
                <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m9 18 6-6-6-6"/>
                </svg>
            </a>
        </div>

        <!-- Project Completion Card -->
        <div class="flex flex-col bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl shadow-sm">
            <div class="p-4 md:p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex justify-center items-center size-[46px] bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                            <svg class="flex-shrink-0 size-5 text-purple-600 dark:text-purple-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22,4 12,14.01 9,11.01"/>
                            </svg>
                        </div>
                    </div>
                    <div class="grow ms-5">
                        <div class="flex items-center gap-x-2">
                            <h3 class="text-xs uppercase tracking-wide text-gray-500 dark:text-neutral-500">
                                Completion Rate
                            </h3>
                        </div>
                        <div class="mt-1 flex items-center gap-x-2">
                            <h3 class="text-xl sm:text-2xl font-medium text-gray-800 dark:text-neutral-200">
                                {{ $statistics['summary']['completion_rate'] ?? 0 }}%
                            </h3>
                            <span class="flex items-center gap-x-1 text-green-600">
                                <svg class="inline-block size-4 self-center" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="22,7 13.5,15.5 8.5,10.5 2,17"/>
                                    <polyline points="16,7 22,7 22,13"/>
                                </svg>
                                <span class="inline-block text-xs">{{ $statistics['projects']['completed'] ?? 0 }} completed</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid lg:grid-cols-2 gap-4 sm:gap-6 mb-8">
        <!-- Recent Activities -->
        <div class="flex flex-col bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-200">Recent Activities</h2>
                <p class="text-sm text-gray-600 dark:text-neutral-400">Latest updates on your projects and quotations</p>
            </div>
            <div class="p-6">
                @if(isset($recentActivities) && count($recentActivities) > 0)
                    <div class="space-y-4">
                        @foreach(array_slice($recentActivities, 0, 5) as $activity)
                            <div class="flex items-start gap-x-3">
                                <div class="flex-shrink-0">
                                    <div class="flex justify-center items-center size-8 rounded-full 
                                        @if($activity['type'] === 'project') bg-blue-100 dark:bg-blue-900/30
                                        @elseif($activity['type'] === 'quotation') bg-amber-100 dark:bg-amber-900/30
                                        @else bg-green-100 dark:bg-green-900/30 @endif">
                                        <svg class="flex-shrink-0 size-4 
                                            @if($activity['type'] === 'project') text-blue-600 dark:text-blue-400
                                            @elseif($activity['type'] === 'quotation') text-amber-600 dark:text-amber-400
                                            @else text-green-600 dark:text-green-400 @endif" 
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            @if($activity['icon'] === 'folder')
                                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                                                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                                            @elseif($activity['icon'] === 'document-text')
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                                <polyline points="14,2 14,8 20,8"/>
                                            @else
                                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                                <polyline points="22,6 12,13 2,6"/>
                                            @endif
                                        </svg>
                                    </div>
                                </div>
                                <div class="grow">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-medium text-gray-800 dark:text-neutral-200">
                                            {{ $activity['title'] }}
                                        </h3>
                                        <span class="text-xs text-gray-500 dark:text-neutral-500">
                                            {{ $activity['date']->diffForHumans() }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-neutral-400">
                                        {{ $activity['description'] }}
                                    </p>
                                    @if(isset($activity['url']))
                                        <a href="{{ $activity['url'] }}" class="mt-2 inline-flex items-center text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                            View details
                                            <svg class="flex-shrink-0 size-3 ms-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="m9 18 6-6-6-6"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-neutral-200">No recent activity</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">Your recent project and quotation updates will appear here.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Upcoming Deadlines -->
        <div class="flex flex-col bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-200">Upcoming Deadlines</h2>
                <p class="text-sm text-gray-600 dark:text-neutral-400">Projects with deadlines in the next 30 days</p>
            </div>
            <div class="p-6">
                @if(isset($upcomingDeadlines) && count($upcomingDeadlines) > 0)
                    <div class="space-y-4">
                        @foreach($upcomingDeadlines as $deadline)
                            <div class="flex items-center justify-between p-3 rounded-lg 
                                @if($deadline['urgency'] === 'critical') bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800/30
                                @elseif($deadline['urgency'] === 'high') bg-orange-50 dark:bg-orange-900/10 border border-orange-200 dark:border-orange-800/30
                                @elseif($deadline['urgency'] === 'medium') bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-200 dark:border-yellow-800/30
                                @else bg-gray-50 dark:bg-neutral-700/50 border border-gray-200 dark:border-neutral-600 @endif">
                                <div class="flex-1">
                                    <h3 class="text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        {{ $deadline['title'] }}
                                    </h3>
                                    <p class="text-xs text-gray-600 dark:text-neutral-400 mt-1">
                                        Due: {{ $deadline['date']->format('M d, Y') }}
                                        @if(isset($deadline['location']))
                                            • {{ $deadline['location'] }}
                                        @endif
                                    </p>
                                </div>
                                <div class="flex-shrink-0 text-right">
                                    <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-lg text-xs font-medium
                                        @if($deadline['urgency'] === 'critical') bg-red-100 text-red-800 dark:bg-red-800/30 dark:text-red-500
                                        @elseif($deadline['urgency'] === 'high') bg-orange-100 text-orange-800 dark:bg-orange-800/30 dark:text-orange-500
                                        @elseif($deadline['urgency'] === 'medium') bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-500
                                        @else bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-neutral-300 @endif">
                                        {{ $deadline['days_until'] }} days
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-neutral-200">No upcoming deadlines</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">You're all caught up! No project deadlines in the next 30 days.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions & Notifications -->
    <div class="grid lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Quick Actions -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-200">Quick Actions</h2>
                    <p class="text-sm text-gray-600 dark:text-neutral-400">Common tasks you can perform</p>
                </div>
                <div class="p-6">
                    @if(isset($quickActions) && count($quickActions) > 0)
                        <div class="grid sm:grid-cols-2 gap-4">
                            @foreach($quickActions as $action)
                                <a href="{{ $action['url'] }}" 
                                   class="group flex items-center p-4 border border-gray-200 dark:border-neutral-700 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-700/50 transition-colors">
                                    <div class="flex-shrink-0">
                                        <div class="flex justify-center items-center size-10 rounded-lg bg-{{ $action['color'] }}-100 dark:bg-{{ $action['color'] }}-900/30">
                                            <svg class="flex-shrink-0 size-5 text-{{ $action['color'] }}-600 dark:text-{{ $action['color'] }}-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                @if($action['icon'] === 'document-add')
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                                    <polyline points="14,2 14,8 20,8"/>
                                                    <line x1="12" y1="18" x2="12" y2="12"/>
                                                    <line x1="9" y1="15" x2="15" y2="15"/>
                                                @elseif($action['icon'] === 'mail')
                                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                                    <polyline points="22,6 12,13 2,6"/>
                                                @elseif($action['icon'] === 'star')
                                                    <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/>
                                                @else
                                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                                    <circle cx="9" cy="9" r="2"/>
                                                    <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                                                    </div>

        <!-- Notifications Placeholder -->
        <div class="flex flex-col bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-200">Notifications</h2>
                <p class="text-sm text-gray-600 dark:text-neutral-400">Latest updates from the system</p>
            </div>
            <div class="p-6">
                @if(isset($notifications) && count($notifications) > 0)
                    <ul class="space-y-4">
                        @foreach($notifications as $note)
                            <li class="flex items-start gap-x-3">
                                <div class="flex-shrink-0">
                                    <div class="flex justify-center items-center size-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-full">
                                        <svg class="size-4 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12A9 9 0 113 12a9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="grow">
                                    <h3 class="text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        {{ $note['title'] }}
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-neutral-400">
                                        {{ $note['message'] }}
                                    </p>
                                    <span class="text-xs text-gray-400 dark:text-neutral-500">{{ $note['timestamp']->diffForHumans() }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-6">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12A9 9 0 113 12a9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-neutral-200">No new notifications</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">You're all caught up for now.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

</x-layouts.client>