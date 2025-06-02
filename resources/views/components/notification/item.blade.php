@props([
    'notification',
    'variant' => 'default',
    'showActions' => false,
    'clickable' => true
])

@php
    $isRead = !empty($notification['read_at']) || !empty($notification['is_read']);
    $iconClass = 'flex-shrink-0 size-4 mt-0.5 text-gray-600 dark:text-neutral-400';
    $bgClass = $isRead ? 'hover:bg-gray-50 dark:hover:bg-neutral-700' : 'bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-900/30';
    
    $notificationUrl = $notification['action_url'] ?? '#';
    $notificationIcon = $notification['icon'] ?? 'bell';
    $notificationColor = $notification['color'] ?? 'gray';
@endphp

<div 
    {{ $attributes->merge(['class' => "px-4 py-3 {$bgClass} transition-colors duration-150"]) }}
    @if($clickable && $notificationUrl !== '#')
        role="button"
        onclick="handleNotificationClick('{{ $notification['id'] }}', '{{ $notificationUrl }}')"
    @endif
>
    <div class="flex space-x-3">
        <!-- Icon -->
        <div class="flex-shrink-0">
            <x-admin.notification.icon :type="$notificationIcon" :color="$notificationColor" />
        </div>

        <!-- Content -->
        <div class="flex-1 min-w-0">
            <!-- Title and Timestamp -->
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                    {{ $notification['title'] ?? 'Notification' }}
                </p>
                <div class="flex items-center space-x-1">
                    @if(!$isRead)
                        <span class="flex-shrink-0 w-2 h-2 bg-blue-600 rounded-full"></span>
                    @endif
                    <span class="text-xs text-gray-500 dark:text-neutral-400 whitespace-nowrap">
                        {{ $notification['time_ago'] ?? $notification['created_at']?->diffForHumans() }}
                    </span>
                </div>
            </div>

            <!-- Message -->
            @if(!empty($notification['message']))
                <p class="mt-1 text-xs text-gray-600 dark:text-neutral-300 line-clamp-2">
                    {{ $notification['message'] }}
                </p>
            @endif

            <!-- Actions -->
            @if($showActions)
                <div class="mt-2 flex items-center space-x-2">
                    @if($notification['action_url'] && $notification['action_text'])
                        <a 
                            href="{{ $notification['action_url'] }}" 
                            class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 font-medium"
                        >
                            {{ $notification['action_text'] }}
                        </a>
                    @endif
                    
                    @if(!$isRead)
                        <button 
                            type="button"
                            onclick="markNotificationAsRead('{{ $notification['id'] }}')"
                            class="text-xs text-gray-500 hover:text-gray-700 dark:text-neutral-400 dark:hover:text-neutral-300"
                        >
                            Mark as read
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>