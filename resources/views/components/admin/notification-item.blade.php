{{-- resources/views/components/admin/notification-item.blade.php --}}
@props(['notification'])

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