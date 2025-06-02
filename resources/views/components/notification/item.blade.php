{{-- resources/views/components/admin/notification/item.blade.php --}}
@props([
    'notification',
    'variant' => 'admin'
])

@php
    $data = is_array($notification) ? $notification : ($notification->data ?? []);
    $notificationId = is_array($notification) ? $notification['id'] : $notification->id;
    $isRead = is_array($notification) ? $notification['is_read'] : !is_null($notification->read_at);
    $createdAt = is_array($notification) ? $notification['created_at'] : $notification->created_at;
    $formattedTime = is_array($notification) ? ($notification['formatted_time'] ?? $createdAt->diffForHumans()) : $createdAt->diffForHumans();
    
    $title = $data['title'] ?? 'Notification';
    $message = $data['message'] ?? '';
    $type = $data['type'] ?? 'notification';
    $url = $data['action_url'] ?? '#';
    
    // Get notification styling
    $iconColor = $this->getNotificationColor($type);
    $icon = $this->getNotificationIcon($type);
    
    $unreadClass = !$isRead ? 'bg-blue-50 dark:bg-blue-900/10' : '';
@endphp

<div class="px-4 py-3 border-b border-gray-100 dark:border-neutral-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-neutral-700 cursor-pointer transition-colors duration-150 {{ $unreadClass }}"
     onclick="handleNotificationClick('{{ $notificationId }}', '{{ $url }}')"
     data-notification-id="{{ $notificationId }}"
     role="button"
     tabindex="0"
     aria-label="Notification: {{ $title }}">
    
    <div class="flex items-start space-x-3">
        <!-- Notification Icon -->
        <div class="flex-shrink-0">
            <div class="size-8 bg-{{ $iconColor }}-100 dark:bg-{{ $iconColor }}-900/30 rounded-lg flex items-center justify-center">
                <svg class="size-4 text-{{ $iconColor }}-600 dark:text-{{ $iconColor }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" />
                </svg>
            </div>
        </div>
        
        <!-- Notification Content -->
        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate pr-2">
                    {{ $title }}
                </p>
                <div class="flex items-center space-x-1 flex-shrink-0">
                    @if(!$isRead)
                        <span class="notification-unread-dot w-2 h-2 bg-blue-600 rounded-full"></span>
                    @endif
                    <span class="text-xs text-gray-500 dark:text-neutral-400 whitespace-nowrap">
                        {{ $formattedTime }}
                    </span>
                </div>
            </div>
            
            @if(!empty($message))
                <p class="mt-1 text-xs text-gray-600 dark:text-neutral-300 line-clamp-2 leading-relaxed">
                    {{ $message }}
                </p>
            @endif
            
            <!-- Notification Type Badge -->
            @if($type !== 'notification')
                <div class="mt-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $iconColor }}-100 text-{{ $iconColor }}-800 dark:bg-{{ $iconColor }}-900/30 dark:text-{{ $iconColor }}-400">
                        {{ $this->formatNotificationType($type) }}
                    </span>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Action Indicator -->
    @if($url !== '#')
        <div class="mt-2 flex justify-end">
            <svg class="size-3 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </div>
    @endif
</div>

@php
    if (!function_exists('getNotificationColor')) {
        function getNotificationColor($type) {
            return match(true) {
                str_contains($type, 'completed') => 'green',
                str_contains($type, 'overdue') || str_contains($type, 'urgent') => 'red',
                str_contains($type, 'deadline') => 'yellow',
                str_contains($type, 'approved') => 'green',
                str_contains($type, 'rejected') => 'red',
                str_contains($type, 'created') || str_contains($type, 'pending') => 'blue',
                str_contains($type, 'message') => 'green',
                str_contains($type, 'user') => 'purple',
                str_contains($type, 'chat') => 'indigo',
                str_contains($type, 'system') => 'orange',
                default => 'gray',
            };
        }
    }
    
    if (!function_exists('getNotificationIcon')) {
        function getNotificationIcon($type) {
            $iconMap = [
                'project.created' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                'project.updated' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                'project.completed' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                'project.overdue' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                'project.deadline_approaching' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                'quotation.created' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
                'quotation.approved' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                'quotation.rejected' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                'message.created' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                'message.reply' => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6',
                'message.urgent' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.928-.833-2.598 0L3.216 16.5c-.77.833.192 2.5 1.732 2.5z',
                'user.welcome' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                'user.email_verified' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                'chat.session_started' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
                'chat.message_received' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
                'system.maintenance' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                'system.alert' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.928-.833-2.598 0L3.216 16.5c-.77.833.192 2.5 1.732 2.5z',
                'testimonial.created' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
            ];
            
            return $iconMap[$type] ?? 'M15 17h5l-5 5v-5zM9 7h6m-6 4h6m-6 4h6M3 7h3m-3 4h3m-3 4h3';
        }
    }
    
    if (!function_exists('formatNotificationType')) {
        function formatNotificationType($type) {
            $typeMap = [
                'project.created' => 'New Project',
                'project.updated' => 'Project Update',
                'project.completed' => 'Project Completed',
                'project.overdue' => 'Overdue Project',
                'project.deadline_approaching' => 'Deadline Alert',
                'quotation.created' => 'New Quotation',
                'quotation.approved' => 'Quote Approved',
                'quotation.rejected' => 'Quote Rejected',
                'quotation.status_updated' => 'Quote Updated',
                'message.created' => 'New Message',
                'message.reply' => 'Message Reply',
                'message.urgent' => 'Urgent Message',
                'user.welcome' => 'Welcome',
                'user.email_verified' => 'Email Verified',
                'chat.session_started' => 'Chat Started',
                'chat.message_received' => 'Chat Message',
                'system.maintenance' => 'Maintenance',
                'system.alert' => 'System Alert',
                'testimonial.created' => 'New Review',
            ];
            
            return $typeMap[$type] ?? ucwords(str_replace(['.', '_'], ' ', $type));
        }
    }
@endphp