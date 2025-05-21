<!-- resources/views/client/messages/index.blade.php -->
<x-layouts.admin title="My Messages" :unreadMessages="$unreadMessages" :pendingQuotations="$pendingQuotations">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'My Messages' => route('client.messages.index')
        ]" />
        
        <div class="mt-4 md:mt-0">
            <x-admin.button 
                href="{{ route('client.messages.create') }}" 
                color="primary"
                icon='<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>'
            >
                New Message
            </x-admin.button>
        </div>
    </div>
    
    <!-- Filters -->
    <x-admin.filter action="{{ route('client.messages.index') }}" method="GET" :resetRoute="route('client.messages.index')">
        <x-admin.input
            name="search"
            label="Search"
            placeholder="Search messages"
            value="{{ request('search') }}"
        />
        
        <x-admin.select
            name="read"
            label="Status"
            :options="['read' => 'Read', 'unread' => 'Unread']"
            placeholder="All"
            value="{{ request('read') }}"
        />
        
        <x-admin.select
            name="type"
            label="Type"
            :options="['client_to_admin' => 'My Messages', 'admin_to_client' => 'Admin Replies']"
            placeholder="All"
            value="{{ request('type') }}"
        />
    </x-admin.filter>
    
    <!-- Messages List -->
    <x-admin.card>
        <x-slot name="title">My Messages</x-slot>
        <x-slot name="headerActions">
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $messages->total() }} messages found</span>
        </x-slot>
        
        @if($messages->count() > 0)
            <div class="divide-y divide-gray-200 dark:divide-neutral-700">
                @foreach($messages as $message)
                    <a href="{{ route('client.messages.show', $message) }}" class="block hover:bg-gray-50 dark:hover:bg-neutral-700 transition-colors {{ !$message->is_read && $message->type === 'admin_to_client' ? 'bg-blue-50 dark:bg-blue-900/10' : '' }}">
                        <div class="px-6 py-5">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="size-10 flex items-center justify-center rounded-full {{ $message->type === 'admin_to_client' ? 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-gray-100 text-gray-600 dark:bg-neutral-800 dark:text-neutral-400' }}">
                                            @if($message->type === 'admin_to_client')
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                                </svg>
                                            @else
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between mb-1">
                                            <h3 class="text-base font-medium {{ !$message->is_read && $message->type === 'admin_to_client' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-900 dark:text-white' }} truncate pr-4 max-w-md">
                                                {{ $message->subject }}
                                            </h3>
                                            <div class="ml-2 flex-shrink-0 flex">
                                                @if(!$message->is_read && $message->type === 'admin_to_client')
                                                    <x-admin.badge type="primary">New</x-admin.badge>
                                                @endif
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-2xl">{{ Str::limit(strip_tags($message->message), 120) }}</p>
                                        <div class="mt-2 flex items-center text-xs text-gray-500 dark:text-gray-400 space-x-2">
                                            <span>{{ $message->created_at->format('M d, Y H:i') }}</span>
                                            <span>•</span>
                                            <span>{{ $message->type === 'admin_to_client' ? 'From: Admin' : 'To: Admin' }}</span>
                                            @if($message->attachments->count() > 0)
                                                <span>•</span>
                                                <span class="flex items-center">
                                                    <svg class="h-3 w-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                    </svg>
                                                    {{ $message->attachments->count() }} attachment(s)
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 self-center ml-2">
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200 dark:border-neutral-700">
                {{ $messages->withQueryString()->links() }}
            </div>
        @else
            <x-admin.empty-state 
                title="No messages found" 
                description="You don't have any messages matching your criteria."
                icon='<svg class="w-10 h-10 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>'
                actionText="New Message"
                :actionUrl="route('client.messages.create')"
            />
        @endif
    </x-admin.card>
</x-layouts.admin>