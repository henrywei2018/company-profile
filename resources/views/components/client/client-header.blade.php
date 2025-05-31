@props([
    'unreadMessagesCount' => 0, 
    'pendingQuotationsCount' => 0,
    'recentNotifications' => collect(),
    'unreadNotificationsCount' => 0,
    'pendingApprovalsCount' => 0,
    'overdueProjectsCount' => 0
])

<header class="sticky top-0 inset-x-0 z-50 w-full bg-white border-b border-gray-200 text-sm dark:bg-gray-800 dark:border-gray-700 lg:ps-64">
    <nav class="w-full mx-auto px-4 py-2.5 sm:flex sm:items-center sm:justify-between sm:px-6 lg:px-8" aria-label="Global">
        <!-- Left: Logo and Mobile Menu Toggle -->
        <div class="flex items-center lg:hidden">
            <!-- Mobile Menu Toggle -->
            <button type="button" 
                class="size-8 flex justify-center items-center text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-neutral-700 rounded-full mr-2"
                data-hs-overlay="#hs-client-sidebar" 
                aria-controls="hs-client-sidebar" 
                aria-label="Toggle navigation">
                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            
            <!-- Mobile Logo -->
            <a href="{{ route('client.dashboard') }}" aria-label="{{ config('app.name') }}"
                class="text-xl font-bold text-blue-600 dark:text-white">
                @if(isset($companyProfile) && $companyProfile->logo)
                    <img src="{{ $companyProfile->logoUrl }}" alt="{{ config('app.name') }}" class="h-8 md:h-10">
                @else
                    {{ config('app.name') }}
                @endif
            </a>
        </div>

        <!-- Right: Actions -->
        <div class="flex items-center justify-end w-full gap-x-2">
            <!-- Quick Actions Dropdown -->
            <div class="hs-dropdown relative inline-block" data-hs-dropdown data-hs-dropdown-placement="bottom-end">
                <button type="button"
                    class="size-8 flex justify-center items-center text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-neutral-700 rounded-full"
                    data-hs-dropdown-toggle>
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span class="sr-only">Quick Actions</span>
                </button>

                <div class="hs-dropdown-menu hidden z-50 mt-2 min-w-60 bg-white shadow-lg rounded-lg border dark:bg-neutral-800 dark:border-neutral-700">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-neutral-700">
                        <p class="text-sm text-gray-800 dark:text-white font-medium">Quick Actions</p>
                    </div>
                    <div class="p-1.5 space-y-0.5">
                        @can('create quotations')
                        <a href="{{ route('quotation.create') }}"
                            class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                            <div class="size-8 flex items-center justify-center bg-blue-100 rounded-lg dark:bg-blue-900/30">
                                <svg class="size-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium">Request Quotation</p>
                                <p class="text-xs text-gray-500 dark:text-neutral-400">Get project estimate</p>
                            </div>
                        </a>
                        @endcan

                        @can('send messages')
                        <a href="{{ route('client.messages.create') }}"
                            class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                            <div class="size-8 flex items-center justify-center bg-green-100 rounded-lg dark:bg-green-900/30">
                                <svg class="size-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium">Send Message</p>
                                <p class="text-xs text-gray-500 dark:text-neutral-400">Contact support</p>
                            </div>
                        </a>
                        @endcan

                        <a href="{{ route('client.projects.index') }}"
                            class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                            <div class="size-8 flex items-center justify-center bg-purple-100 rounded-lg dark:bg-purple-900/30">
                                <svg class="size-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium">View Projects</p>
                                <p class="text-xs text-gray-500 dark:text-neutral-400">Track progress</p>
                            </div>
                        </a>

                        <a href="{{ route('client.profile.edit') }}"
                            class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                            <div class="size-8 flex items-center justify-center bg-amber-100 rounded-lg dark:bg-amber-900/30">
                                <svg class="size-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium">Update Profile</p>
                                <p class="text-xs text-gray-500 dark:text-neutral-400">Manage account</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Theme Toggle -->
            <button id="theme-toggle"
                class="size-8 flex justify-center items-center text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-neutral-700 rounded-full">
                <!-- Dark mode: Sun icon -->
                <svg class="hidden dark:block size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="5" />
                    <line x1="12" y1="1" x2="12" y2="3" />
                    <line x1="12" y1="21" x2="12" y2="23" />
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" />
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" />
                    <line x1="1" y1="12" x2="3" y2="12" />
                    <line x1="21" y1="12" x2="23" y2="12" />
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36" />
                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22" />
                </svg>
                <!-- Light mode: Moon icon -->
                <svg class="block dark:hidden size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
                </svg>
                <span class="sr-only">Toggle dark mode</span>
            </button>

            <!-- Urgent Alerts Dropdown -->
            @if($overdueProjectsCount > 0 || $pendingApprovalsCount > 0)
            <div class="hs-dropdown relative inline-block" data-hs-dropdown data-hs-dropdown-placement="bottom-end">
                <button type="button"
                    class="size-8 flex justify-center items-center text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20 rounded-full relative animate-pulse"
                    data-hs-dropdown-toggle>
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                        <line x1="12" y1="9" x2="12" y2="13" />
                        <line x1="12" y1="17" x2="12.01" y2="17" />
                    </svg>
                    <span class="absolute -top-1 -end-1 inline-flex items-center justify-center size-4 text-xs font-bold text-white bg-red-500 rounded-full">
                        {{ $overdueProjectsCount + $pendingApprovalsCount }}
                    </span>
                    <span class="sr-only">Urgent Alerts</span>
                </button>

                <div class="hs-dropdown-menu hidden z-50 mt-2 w-80 bg-white shadow-lg rounded-lg border border-red-200 dark:bg-neutral-800 dark:border-red-700">
                    <div class="px-4 py-3 border-b border-red-200 dark:border-red-700 bg-red-50 dark:bg-red-900/20">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200 flex items-center gap-2">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.928-.833-2.598 0L3.216 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            Urgent Attention Required
                        </h3>
                    </div>

                    <div class="p-4 space-y-3">
                        @if($overdueProjectsCount > 0)
                        <div class="flex items-start gap-3 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                            <div class="flex-shrink-0">
                                <div class="size-8 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                                    <svg class="size-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-red-800 dark:text-red-200">Overdue Projects</h4>
                                <p class="text-sm text-red-600 dark:text-red-300">
                                    {{ $overdueProjectsCount }} {{ Str::plural('project', $overdueProjectsCount) }} {{ $overdueProjectsCount === 1 ? 'is' : 'are' }} past deadline
                                </p>
                                <a href="{{ route('client.projects.index', ['status' => 'overdue']) }}" 
                                   class="text-xs text-red-700 dark:text-red-300 hover:underline">
                                    View overdue projects →
                                </a>
                            </div>
                        </div>
                        @endif

                        @if($pendingApprovalsCount > 0)
                        <div class="flex items-start gap-3 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                            <div class="flex-shrink-0">
                                <div class="size-8 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                                    <svg class="size-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-amber-800 dark:text-amber-200">Pending Approvals</h4>
                                <p class="text-sm text-amber-600 dark:text-amber-300">
                                    {{ $pendingApprovalsCount }} {{ Str::plural('quotation', $pendingApprovalsCount) }} waiting for your approval
                                </p>
                                <a href="{{ route('client.quotations.index', ['status' => 'pending_approval']) }}" 
                                   class="text-xs text-amber-700 dark:text-amber-300 hover:underline">
                                    Review quotations →
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Notification Dropdown -->
            <div class="hs-dropdown relative inline-block" data-hs-dropdown data-hs-dropdown-placement="bottom-end">
                <button type="button"
                    class="size-8 flex justify-center items-center text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-neutral-700 rounded-full relative"
                    data-hs-dropdown-toggle id="notification-dropdown-toggle">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9" />
                        <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0" />
                    </svg>
                    @if($unreadNotificationsCount > 0)
                        <span class="absolute -top-1 -end-1 inline-flex items-center justify-center size-4 text-xs font-bold text-white bg-blue-500 rounded-full" id="notification-badge">
                            {{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}
                        </span>
                    @endif
                    <span class="sr-only">Notifications</span>
                </button>

                <!-- Dropdown Panel -->
                <div class="hs-dropdown-menu hidden z-50 mt-2 w-80 bg-white shadow-lg rounded-lg border dark:bg-neutral-800 dark:border-neutral-700"
                    aria-labelledby="notification-dropdown-toggle">
                    <!-- Header -->
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-neutral-700 flex items-center justify-between">
                        <h3 class="text-sm font-medium text-gray-800 dark:text-white">Notifications</h3>
                        <div class="flex items-center gap-2">
                            @if($unreadNotificationsCount > 0)
                            <button type="button" 
                                onclick="markAllNotificationsAsRead()"
                                class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                Mark all read
                            </button>
                            @endif
                            <a href="{{ route('client.notifications.index') }}" 
                               class="text-xs text-gray-600 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                View all
                            </a>
                        </div>
                    </div>

                    <!-- Notification List -->
                    <div class="max-h-80 overflow-y-auto" id="notification-list">
                        @forelse($recentNotifications as $notification)
                            <div class="notification-item {{ is_null($notification['read_at']) ? 'bg-blue-50 dark:bg-blue-900/10' : '' }} border-b border-gray-100 dark:border-neutral-700 last:border-0" 
                                 data-notification-id="{{ $notification['id'] }}">
                                <a href="{{ $notification['url'] }}" 
                                   onclick="markNotificationAsRead('{{ $notification['id'] }}')"
                                   class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-neutral-700 transition-colors">
                                    <div class="flex items-start gap-3">
                                        <!-- Notification Icon -->
                                        <div class="flex-shrink-0 mt-1">
                                            @php
                                                $iconClass = match($notification['color']) {
                                                    'green' => 'text-green-600 bg-green-100 dark:bg-green-900/30',
                                                    'red' => 'text-red-600 bg-red-100 dark:bg-red-900/30',
                                                    'yellow' => 'text-yellow-600 bg-yellow-100 dark:bg-yellow-900/30',
                                                    'blue' => 'text-blue-600 bg-blue-100 dark:bg-blue-900/30',
                                                    default => 'text-gray-600 bg-gray-100 dark:bg-gray-900/30'
                                                };
                                            @endphp
                                            <div class="size-8 flex items-center justify-center rounded-lg {{ $iconClass }}">
                                                @switch($notification['icon'])
                                                    @case('folder')
                                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                                        </svg>
                                                        @break
                                                    @case('document-text')
                                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        @break
                                                    @case('mail')
                                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                        </svg>
                                                        @break
                                                    @case('exclamation-triangle')
                                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.928-.833-2.598 0L3.216 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                                        </svg>
                                                        @break
                                                    @case('user')
                                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                        </svg>
                                                        @break
                                                    @case('star')
                                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                                        </svg>
                                                        @break
                                                    @default
                                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 17v5l5-5H4zM20 7V2l-5 5h5zM4 7V2l5 5H4z" />
                                                        </svg>
                                                @endswitch
                                            </div>
                                        </div>

                                        <!-- Notification Content -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between">
                                                <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                    {{ $notification['title'] }}
                                                </h4>
                                                @if(!$notification['is_read'])
                                                    <div class="size-2 bg-blue-600 rounded-full ml-2 mt-2 flex-shrink-0"></div>
                                                @endif
                                            </div>
                                            
                                            <p class="text-sm text-gray-600 dark:text-neutral-400 mt-1 line-clamp-2">
                                                {{ $notification['message'] }}
                                            </p>
                                            
                                            <div class="flex items-center justify-between mt-2">
                                                <span class="text-xs text-gray-500 dark:text-neutral-500">
                                                    {{ $notification['formatted_time'] }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <div class="px-4 py-8 text-center">
                                <svg class="mx-auto size-12 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 7h6m-6 4h6m-6 4h6M3 7h3m-3 4h3m-3 4h3" />
                                </svg>
                                <p class="text-sm text-gray-500 dark:text-neutral-400 mt-2">No new notifications</p>
                                <p class="text-xs text-gray-400 dark:text-neutral-500">You're all caught up!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Messages Quick Access -->
            <div class="hs-dropdown relative inline-block" data-hs-dropdown data-hs-dropdown-placement="bottom-end">
                <button type="button"
                    class="size-8 flex justify-center items-center text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-neutral-700 rounded-full relative"
                    data-hs-dropdown-toggle>
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    @if($unreadMessagesCount > 0)
                        <span class="absolute -top-1 -end-1 inline-flex items-center justify-center size-4 text-xs font-bold text-white bg-green-500 rounded-full">
                            {{ $unreadMessagesCount > 99 ? '99+' : $unreadMessagesCount }}
                        </span>
                    @endif
                    <span class="sr-only">Messages</span>
                </button>

                <div class="hs-dropdown-menu hidden z-50 mt-2 w-72 bg-white shadow-lg rounded-lg border dark:bg-neutral-800 dark:border-neutral-700">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-neutral-700 flex items-center justify-between">
                        <h3 class="text-sm font-medium text-gray-800 dark:text-white">Recent Messages</h3>
                        <a href="{{ route('client.messages.index') }}" class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400">
                            View all
                        </a>
                    </div>

                    <div class="max-h-64 overflow-y-auto">
                        @php
                            $clientAccessService = app(\App\Services\ClientAccessService::class);
                            $recentMessages = $clientAccessService->getClientMessages(auth()->user())
                                ->with('project')
                                ->latest()
                                ->limit(5)
                                ->get();
                        @endphp
                        
                        @forelse($recentMessages as $message)
                            <a href="{{ route('client.messages.show', $message) }}" 
                               class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-neutral-700 border-b border-gray-100 dark:border-neutral-700 last:border-0">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="size-8 flex items-center justify-center rounded-full {{ $message->is_read ? 'bg-gray-100 dark:bg-neutral-700' : 'bg-blue-100 dark:bg-blue-900/30' }}">
                                            @if($message->type === 'admin_to_client')
                                                <svg class="size-4 {{ $message->is_read ? 'text-gray-600 dark:text-neutral-400' : 'text-blue-600 dark:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                </svg>
                                            @else
                                                <svg class="size-4 {{ $message->is_read ? 'text-gray-600 dark:text-neutral-400' : 'text-green-600 dark:text-green-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                {{ $message->subject ?? 'No Subject' }}
                                            </p>
                                            @if(!$message->is_read)
                                                <div class="size-2 bg-blue-600 rounded-full flex-shrink-0"></div>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600 dark:text-neutral-400 truncate">
                                            {{ $message->type === 'admin_to_client' ? 'From Support Team' : 'Your message' }}
                                        </p>
                                        <div class="flex items-center justify-between mt-1">
                                            <p class="text-xs text-gray-500 dark:text-neutral-500">
                                                {{ $message->created_at->diffForHumans() }}
                                            </p>
                                            @if($message->priority === 'urgent')
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                                    Urgent
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="px-4 py-6 text-center">
                                <svg class="mx-auto size-8 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p class="text-sm text-gray-500 dark:text-neutral-400 mt-2">No recent messages</p>
                                <a href="{{ route('client.messages.create') }}" class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                    Send a message
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- User Dropdown -->
            <div class="hs-dropdown relative inline-block" data-hs-dropdown data-hs-dropdown-placement="bottom-end">
                <button type="button"
                    class="size-8 inline-flex justify-center items-center rounded-full text-sm font-semibold text-gray-800 dark:text-white"
                    data-hs-dropdown-toggle>
                    @if(auth()->user()->avatar)
                        <img class="size-8 rounded-full object-cover" src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}">
                    @else
                        <div class="size-8 bg-blue-600 rounded-full flex items-center justify-center">
                            <span class="text-xs font-medium text-white">
                                {{ substr(auth()->user()->name, 0, 2) }}
                            </span>
                        </div>
                    @endif
                </button>

                <div class="hs-dropdown-menu hidden z-50 min-w-60 mt-2 bg-white shadow-lg rounded-lg border dark:bg-neutral-800 dark:border-neutral-700"
                    aria-labelledby="hs-dropdown-toggle">
                    <div class="px-5 py-3 bg-gray-50 dark:bg-neutral-700 rounded-t-lg">
                        <p class="text-sm text-gray-500 dark:text-neutral-400">Signed in as</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-neutral-400 truncate">{{ auth()->user()->email }}</p>
                        @if(auth()->user()->company)
                        <p class="text-xs text-gray-500 dark:text-neutral-400 mt-1">{{ auth()->user()->company }}</p>
                        @endif
                    </div>
                    <div class="p-1.5 space-y-0.5">
                        <a href="{{ route('client.dashboard') }}"
                            class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                <polyline points="9,22 9,12 15,12 15,22" />
                            </svg>
                            Dashboard
                        </a>
                        <a href="{{ route('client.profile.edit') }}"
                            class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                            My Profile
                        </a>
                        <a href="{{ route('client.projects.index') }}"
                            class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
                            </svg>
                            My Projects
                        </a>
                        <a href="{{ route('client.quotations.index') }}"
                            class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                <polyline points="14,2 14,8 20,8" />
                                <line x1="16" y1="13" x2="8" y2="13" />
                                <line x1="16" y1="17" x2="8" y2="17" />
                                <polyline points="10,9 9,9 8,9" />
                            </svg>
                            My Quotations
                        </a>
                        <a href="{{ route('client.messages.index') }}"
                            class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                <polyline points="22,6 12,13 2,6" />
                            </svg>
                            Messages
                        </a>
                        
                        <div class="border-t border-gray-200 dark:border-neutral-700 my-1"></div>
                        
                        <a href="{{ route('client.notifications.preferences') }}"
                            class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            Preferences
                        </a>
                        
                        <button type="button" onclick="testClientNotification()"
                            class="w-full flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9" />
                                <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0" />
                            </svg>
                            Test Notifications
                        </button>
                        
                        <a href="{{ route('home') }}" target="_blank"
                            class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" />
                                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />
                            </svg>
                            View Website
                        </a>
                        
                        <div class="border-t border-gray-200 dark:border-neutral-700 my-1"></div>
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" viewBox="0 0 24 24">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                    <polyline points="16 17 21 12 16 7" />
                                    <line x1="21" y1="12" x2="9" y2="12" />
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>

@push('scripts')
<script>
    // Client Notification management functions
    function markNotificationAsRead(notificationId) {
        fetch(`{{ route('client.dashboard.mark-notification-read') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                notification_id: notificationId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update UI
                const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
                if (notificationItem) {
                    notificationItem.classList.remove('bg-blue-50', 'dark:bg-blue-900/10');
                    const unreadDot = notificationItem.querySelector('.size-2.bg-blue-600');
                    if (unreadDot) {
                        unreadDot.remove();
                    }
                }
                
                updateNotificationCounts();
            }
        })
        .catch(error => {
            console.error('Failed to mark notification as read:', error);
        });
    }

    function markAllNotificationsAsRead() {
        fetch(`{{ route('client.dashboard.mark-notification-read') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                notification_id: 'all'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update UI - remove all unread indicators
                const notificationItems = document.querySelectorAll('.notification-item');
                notificationItems.forEach(item => {
                    item.classList.remove('bg-blue-50', 'dark:bg-blue-900/10');
                    const unreadDot = item.querySelector('.size-2.bg-blue-600');
                    if (unreadDot) {
                        unreadDot.remove();
                    }
                });
                
                updateNotificationCounts();
                
                // Hide mark all button
                const markAllBtn = document.querySelector('button[onclick="markAllNotificationsAsRead()"]');
                if (markAllBtn) {
                    markAllBtn.style.display = 'none';
                }
            }
        })
        .catch(error => {
            console.error('Failed to mark all notifications as read:', error);
        });
    }

    function updateNotificationCounts() {
        fetch('{{ route("client.dashboard.realtime-stats") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update notification badge
                const notificationBadge = document.getElementById('notification-badge');
                const unreadCount = data.data.notifications.unread;
                
                if (unreadCount > 0) {
                    if (notificationBadge) {
                        notificationBadge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                    } else {
                        // Create badge if it doesn't exist
                        const toggleBtn = document.getElementById('notification-dropdown-toggle');
                        if (toggleBtn) {
                            const badge = document.createElement('span');
                            badge.id = 'notification-badge';
                            badge.className = 'absolute -top-1 -end-1 inline-flex items-center justify-center size-4 text-xs font-bold text-white bg-blue-500 rounded-full';
                            badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                            toggleBtn.appendChild(badge);
                        }
                    }
                } else {
                    if (notificationBadge) {
                        notificationBadge.remove();
                    }
                }
            }
        })
        .catch(error => {
            console.error('Failed to update notification counts:', error);
        });
    }

    function testClientNotification() {
        fetch('{{ route("client.dashboard.test-notification") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showToast('success', data.message);
                
                // Refresh notifications after a short delay
                setTimeout(() => {
                    updateNotificationCounts();
                }, 2000);
            } else {
                showToast('error', data.message);
            }
        })
        .catch(error => {
            console.error('Failed to send test notification:', error);
            showToast('error', 'Failed to send test notification');
        });
    }

    function showToast(type, message) {
        // Simple toast implementation
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-white ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        }`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    // Theme toggle functionality
    document.addEventListener('DOMContentLoaded', function() {
        const themeToggle = document.getElementById('theme-toggle');
        
        themeToggle.addEventListener('click', function() {
            const html = document.documentElement;
            
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('hs_theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('hs_theme', 'dark');
            }
        });

        // Auto-update notification counts every 30 seconds
        setInterval(updateNotificationCounts, 30000);

        // Auto-refresh urgent alerts every minute
        setInterval(() => {
            fetch('{{ route("client.dashboard.realtime-stats") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update urgent alerts visibility based on current counts
                    const urgentDropdown = document.querySelector('[data-hs-dropdown-toggle]:has(svg path[d*="M10.29"])');
                    if (urgentDropdown) {
                        const overdueCount = data.data.projects.overdue || 0;
                        const pendingApprovals = data.data.quotations.awaiting_approval || 0;
                        const totalUrgent = overdueCount + pendingApprovals;
                        
                        if (totalUrgent > 0) {
                            urgentDropdown.style.display = 'flex';
                            const badge = urgentDropdown.querySelector('.bg-red-500');
                            if (badge) {
                                badge.textContent = totalUrgent;
                            }
                        } else {
                            urgentDropdown.style.display = 'none';
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Failed to update urgent alerts:', error);
            });
        }, 60000);
    });
</script>
@endpush