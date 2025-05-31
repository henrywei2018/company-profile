{{-- resources/views/components/admin/message-item.blade.php --}}
@props(['message'])

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