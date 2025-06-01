@props(['session'])
<div class="p-4 border border-gray-200 dark:border-neutral-700 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-700 transition-colors" 
     data-session-id="{{ $session->session_id }}">
    <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
            <div class="flex items-center space-x-2">
                <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                    {{ $session->getVisitorName() }}
                </h4>
                @if($session->getDuration())
                    <x-admin.badge type="info" size="sm">
                        {{ $session->getDuration() }}m
                    </x-admin.badge>
                @endif
                <div class="w-2 h-2 bg-gray-400 rounded-full" title="Closed session"></div>
            </div>
            <div class="flex items-center space-x-2 mt-1">
                <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    handled by {{ $session->operator->name ?? 'System' }}
                </p>
            </div>
            <div class="flex items-center space-x-4 mt-2">
                <p class="text-xs text-gray-600 dark:text-gray-300">
                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Ended {{ $session->ended_at->diffForHumans() }}
                </p>
                <p class="text-xs text-gray-600 dark:text-gray-300">
                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    {{ $session->messages->count() }} messages
                </p>
            </div>
            @if($session->summary)
                <p class="text-xs text-gray-600 dark:text-gray-300 mt-2 bg-gray-50 dark:bg-neutral-800 p-2 rounded">
                    <span class="font-medium">Summary:</span> {{ Str::limit($session->summary, 80) }}
                </p>
            @endif
        </div>
        <div class="flex flex-col space-y-2 ml-4">
            <x-admin.button 
                href="{{ route('admin.chat.show', $session) }}" 
                color="light" 
                size="sm"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>'>
                View History
            </x-admin.button>
            @if($session->getVisitorEmail())
                <x-admin.button 
                    href="{{ route('admin.messages.create', ['to' => $session->getVisitorEmail()]) }}" 
                    color="info" 
                    size="sm"
                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>'>
                    Send Email
                </x-admin.button>
            @endif
        </div>
    </div>
</div>