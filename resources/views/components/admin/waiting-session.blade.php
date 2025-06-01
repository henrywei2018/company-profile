@props(['session'])
<div class="p-4 border border-gray-200 dark:border-neutral-700 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-700 transition-colors" 
     data-session-id="{{ $session->session_id }}">
    <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
            <div class="flex items-center space-x-2">
                <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                    {{ $session->getVisitorName() }}
                </h4>
                <x-admin.badge 
                    :type="match($session->priority) {
                        'urgent' => 'danger',
                        'high' => 'warning', 
                        'low' => 'info',
                        default => 'primary'
                    }" 
                    size="sm">
                    {{ ucfirst($session->priority) }}
                </x-admin.badge>
                @if($session->getVisitorEmail())
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                        </svg>
                        Registered
                    </span>
                @endif
            </div>
            @if($session->getVisitorEmail())
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ $session->getVisitorEmail() }}
                </p>
            @endif
            <div class="flex items-center space-x-4 mt-2">
                <p class="text-xs text-gray-600 dark:text-gray-300">
                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Waiting {{ $session->started_at->diffInMinutes() }} minutes
                </p>
                @if($session->messages->count() > 0)
                    <p class="text-xs text-gray-600 dark:text-gray-300">
                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        {{ $session->messages->count() }} messages
                    </p>
                @endif
            </div>
            @if($session->latestMessage)
                <p class="text-xs text-gray-600 dark:text-gray-300 mt-2 last-message bg-gray-50 dark:bg-neutral-800 p-2 rounded">
                    "{{ Str::limit($session->latestMessage->message, 60) }}"
                </p>
            @endif
        </div>
        <div class="flex flex-col space-y-2 ml-4">
            <x-admin.button 
                onclick="assignSessionToMe('{{ $session->session_id }}')" 
                color="primary" 
                size="sm"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>'>
                Assign to Me
            </x-admin.button>
            <x-admin.button 
                href="{{ route('admin.chat.show', $session) }}" 
                color="light" 
                size="sm"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>'>
                View
            </x-admin.button>
        </div>
    </div>
</div>