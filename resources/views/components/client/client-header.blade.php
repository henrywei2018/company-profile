@props([
    'unreadMessagesCount' => 0, 
    'pendingQuotationsCount' => 0,
    'recentNotifications' => collect(),
    'unreadNotificationsCount' => 0
])

<header class="sticky top-0 inset-x-0 z-50 w-full bg-white border-b border-gray-200 text-sm dark:bg-gray-800 dark:border-gray-700 lg:ps-64">
    <nav class="w-full mx-auto px-4 py-2.5 sm:flex sm:items-center sm:justify-between sm:px-6 lg:px-8" aria-label="Global">
        <!-- Left: Logo and Mobile Menu Toggle -->
        <div class="flex items-center lg:hidden">
            <!-- Mobile Menu Toggle -->
            <button type="button" 
                class="size-8 flex justify-center items-center text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-neutral-700 rounded-full mr-2"
                data-hs-overlay="#hs-application-sidebar" 
                aria-controls="hs-application-sidebar" 
                aria-label="Toggle navigation">
                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            
            <!-- Mobile Logo -->
            <a href="{{ route('admin.dashboard') }}" aria-label="{{ config('app.name') }}"
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
                        @can('create projects')
                        <a href="{{ route('admin.projects.create') }}"
                            class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                            <div class="size-8 flex items-center justify-center bg-blue-100 rounded-lg dark:bg-blue-900/30">
                                <svg class="size-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium">New Project</p>
                                <p class="text-xs text-gray-500 dark:text-neutral-400">Create client project</p>
                            </div>
                        </a>
                        @endcan

                        @can('create quotations')
                        <a href="{{ route('admin.quotations.create') }}"
                            class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                            <div class="size-8 flex items-center justify-center bg-amber-100 rounded-lg dark:bg-amber-900/30">
                                <svg class="size-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium">New Quotation</p>
                                <p class="text-xs text-gray-500 dark:text-neutral-400">Create quotation</p>
                            </div>
                        </a>
                        @endcan

                        @can('create posts')
                        <a href="{{ route('admin.posts.create') }}"
                            class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                            <div class="size-8 flex items-center justify-center bg-purple-100 rounded-lg dark:bg-purple-900/30">
                                <svg class="size-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium">New Post</p>
                                <p class="text-xs text-gray-500 dark:text-neutral-400">Write article</p>
                            </div>
                        </a>
                        @endcan
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
                        <span class="absolute -top-1 -end-1 inline-flex items-center justify-center size-4 text-xs font-bold text-white bg-red-500 rounded-full" id="notification-badge">
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
                        @if($unreadNotificationsCount > 0)
                        <button type="button" 
                            onclick="markAllAsRead()"
                            class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                            Mark all read
                        </button>
                        @endif
                    </div>

                    <!-- Notification List -->
                    <div class="max-h-80 overflow-y-auto" id="notification-list">
                        @forelse($recentNotifications as $notification)
                            <div class="notification-item {{ is_null($notification->read_at) ? 'bg-blue-50 dark:bg-blue-900/10' : '' }} border-b border-gray-100 dark:border-neutral-700 last:border-0" 
                                 data-notification-id="{{ $notification->id }}">
                                <a href="{{ $notification->data['action_url'] ?? '#' }}" 
                                   onclick="markAsRead('{{ $notification->id }}')"
                                   class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-neutral-700 transition-colors">
                                    <div class="flex items-start gap-3">
                                        <!-- Notification Icon -->
                                        <div class="flex-shrink-0 mt-1">
                                            @php
                                                $type = $notification->data['type'] ?? 'info';
                                                $iconClass = match($type) {
                                                    'project' => 'text-blue-600 bg-blue-100 dark:bg-blue-900/30',
                                                    'quotation' => 'text-amber-600 bg-amber-100 dark:bg-amber-900/30',
                                                    'message' => 'text-green-600 bg-green-100 dark:bg-green-900/30',
                                                    'user' => 'text-purple-600 bg-purple-100 dark:bg-purple-900/30',
                                                    'system' => 'text-red-600 bg-red-100 dark:bg-red-900/30',
                                                    default => 'text-gray-600 bg-gray-100 dark:bg-gray-900/30'
                                                };
                                            @endphp
                                            <div class="size-8 flex items-center justify-center rounded-lg {{ $iconClass }}">
                                                @switch($type)
                                                    @case('project')
                                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                        </svg>
                                                        @break
                                                    @case('quotation')
                                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                        </svg>
                                                        @break
                                                    @case('message')
                                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                        </svg>
                                                        @break
                                                    @case('user')
                                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                        </svg>
                                                        @break
                                                    @default
                                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                @endswitch
                                            </div>
                                        </div>

                                        <!-- Notification Content -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between">
                                                <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                    {{ $notification->data['title'] ?? 'Notification' }}
                                                </h4>
                                                @if(is_null($notification->read_at))
                                                    <div class="size-2 bg-blue-600 rounded-full ml-2 mt-2 flex-shrink-0"></div>
                                                @endif
                                            </div>
                                            
                                            <p class="text-sm text-gray-600 dark:text-neutral-400 mt-1 line-clamp-2">
                                                {{ $notification->data['message'] ?? '' }}
                                            </p>
                                            
                                            <div class="flex items-center justify-between mt-2">
                                                <span class="text-xs text-gray-500 dark:text-neutral-500">
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </span>
                                                @if($notification->data['action_text'] ?? false)
                                                    <span class="text-xs text-blue-600 dark:text-blue-400 font-medium">
                                                        {{ $notification->data['action_text'] }}
                                                    </span>
                                                @endif
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

                    <!-- Footer -->
                    @if($recentNotifications->count() > 0)
                    <div class="px-4 py-3 border-t border-gray-200 dark:border-neutral-700">
                        <a href="{{ route('admin.notifications.index') }}" 
                           class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                            View all notifications
                        </a>
                    </div>
                    @endif
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
                        <span class="absolute -top-1 -end-1 inline-flex items-center justify-center size-4 text-xs font-bold text-white bg-red-500 rounded-full">
                            {{ $unreadMessagesCount > 99 ? '99+' : $unreadMessagesCount }}
                        </span>
                    @endif
                    <span class="sr-only">Messages</span>
                </button>

                <div class="hs-dropdown-menu hidden z-50 mt-2 w-72 bg-white shadow-lg rounded-lg border dark:bg-neutral-800 dark:border-neutral-700">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-neutral-700 flex items-center justify-between">
                        <h3 class="text-sm font-medium text-gray-800 dark:text-white">Recent Messages</h3>
                        <a href="{{ route('admin.messages.index') }}" class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400">
                            View all
                        </a>
                    </div>

                    <div class="max-h-64 overflow-y-auto">
                        @php
                            $recentMessages = \App\Models\Message::with('user')
                                ->latest()
                                ->limit(5)
                                ->get();
                        @endphp
                        
                        @forelse($recentMessages as $message)
                            <a href="{{ route('admin.messages.show', $message) }}" 
                               class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-neutral-700 border-b border-gray-100 dark:border-neutral-700 last:border-0">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="size-8 bg-gray-100 dark:bg-neutral-700 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-medium text-gray-600 dark:text-neutral-300">
                                                {{ substr($message->name, 0, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                {{ $message->name }}
                                            </p>
                                            @if(!$message->is_read)
                                                <div class="size-2 bg-blue-600 rounded-full"></div>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600 dark:text-neutral-400 truncate">
                                            {{ $message->subject }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-neutral-500 mt-1">
                                            {{ $message->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="px-4 py-6 text-center">
                                <p class="text-sm text-gray-500 dark:text-neutral-400">No recent messages</p>
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
                        <p class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ auth()->user()->email }}</p>
                        <p class="text-xs text-gray-500 dark:text-neutral-400 mt-1">
                            {{ auth()->user()->getRoleNames()->map('ucfirst')->join(', ') }}
                        </p>
                    </div>
                    <div class="p-1.5 space-y-0.5">
                        <a href="{{ route('admin.profile.edit') }}"
                            class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                            My Profile
                        </a>
                        <a href="{{ route('admin.settings.index') }}"
                            class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            Settings
                        </a>
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
                                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                                    <polyline points="10 17 15 12 10 7" />
                                    <line x1="15" y1="12" x2="3" y2="12" />
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
    // Notification management functions
    function markAsRead(notificationId) {
        fetch(`/admin/notifications/${notificationId}/mark-as-read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
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

    function markAllAsRead() {
        fetch('/admin/notifications/mark-all-as-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
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
                const markAllBtn = document.querySelector('button[onclick="markAllAsRead()"]');
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
        fetch('/admin/notifications/counts')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update notification badge
                const notificationBadge = document.getElementById('notification-badge');
                if (data.counts.unread_notifications > 0) {
                    if (notificationBadge) {
                        notificationBadge.textContent = data.counts.unread_notifications > 99 ? '99+' : data.counts.unread_notifications;
                    } else {
                        // Create badge if it doesn't exist
                        const toggleBtn = document.getElementById('notification-dropdown-toggle');
                        if (toggleBtn) {
                            const badge = document.createElement('span');
                            badge.id = 'notification-badge';
                            badge.className = 'absolute -top-1 -end-1 inline-flex items-center justify-center size-4 text-xs font-bold text-white bg-red-500 rounded-full';
                            badge.textContent = data.counts.unread_notifications > 99 ? '99+' : data.counts.unread_notifications;
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
    });
</script>
@endpush