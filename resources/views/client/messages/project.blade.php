<!-- resources/views/client/messages/project.blade.php -->
<x-layouts.admin title="Project Messages" :unreadMessages="$unreadMessages ?? 0" :pendingQuotations="$pendingQuotations ?? 0">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'Messages' => route('client.messages.index'),
            'Projects' => route('client.projects.index'),
            $project->title => route('client.projects.show', $project),
            'Messages' => '#'
        ]" />
        
        <div class="mt-4 md:mt-0 flex flex-wrap gap-2">
            <x-admin.button
                href="{{ route('client.messages.create', ['project_id' => $project->id]) }}"
                color="primary"
                size="sm"
            >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Pesan Baru
            </x-admin.button>
        </div>
    </div>

    <!-- Project Info Card -->
    <x-admin.card class="mb-6">
        <div class="flex items-center space-x-4">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $project->title }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $messages->total() }} messages • Status: 
                    <span class="px-2 py-1 text-xs font-medium rounded-full
                        @if($project->status === 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                        @elseif($project->status === 'completed') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                        @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                        @endif">
                        {{ ucfirst($project->status) }}
                    </span>
                </p>
            </div>
        </div>
    </x-admin.card>

    <!-- Filters -->
    <x-admin.card class="mb-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-0">
                <x-admin.input
                    name="search"
                    placeholder="Search messages..."
                    value="{{ $filters['search'] ?? '' }}"
                    class="w-full"
                />
            </div>
            
            <div class="w-40">
                <select name="type" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">All Types</option>
                    <option value="general" {{ ($filters['type'] ?? '') === 'general' ? 'selected' : '' }}>General</option>
                    <option value="support" {{ ($filters['type'] ?? '') === 'support' ? 'selected' : '' }}>Support</option>
                    <option value="project_inquiry" {{ ($filters['type'] ?? '') === 'project_inquiry' ? 'selected' : '' }}>Project Related</option>
                    <option value="client_reply" {{ ($filters['type'] ?? '') === 'client_reply' ? 'selected' : '' }}>My Replies</option>
                </select>
            </div>
            
            <div class="w-32">
                <select name="read" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">All Status</option>
                    <option value="unread" {{ ($filters['read'] ?? '') === 'unread' ? 'selected' : '' }}>Unread</option>
                    <option value="read" {{ ($filters['read'] ?? '') === 'read' ? 'selected' : '' }}>Read</option>
                </select>
            </div>
            
            <x-admin.button type="submit" color="light" size="sm">
                Filter
            </x-admin.button>
            
            @if(!empty(array_filter($filters ?? [])))
                <x-admin.button 
                    href="{{ route('client.messages.project', $project) }}" 
                    color="light" 
                    size="sm"
                >
                    Clear
                </x-admin.button>
            @endif
        </form>
    </x-admin.card>

    <!-- Messages List -->
    <x-admin.card>
        <x-slot name="title">Project Messages</x-slot>
        
        @if($messages->count() > 0)
            <div class="space-y-4">
                @foreach($messages as $message)
                    <div class="flex items-start space-x-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg
                        {{ $message->is_read ? 'bg-white dark:bg-gray-800' : 'bg-blue-50 dark:bg-blue-900/20' }}">
                        
                        <!-- Message Icon -->
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center
                                @if($message->type === 'admin_to_client') bg-green-100 dark:bg-green-900
                                @elseif($message->priority === 'urgent') bg-red-100 dark:bg-red-900
                                @else bg-gray-100 dark:bg-gray-700
                                @endif">
                                @if($message->type === 'admin_to_client')
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @elseif($message->priority === 'urgent')
                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L3.316 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Message Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    <a href="{{ route('client.messages.show', $message) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                        {{ $message->subject }}
                                    </a>
                                </h4>
                                <div class="flex items-center space-x-2 ml-2">
                                    @if($message->priority === 'urgent')
                                        <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-full">
                                            Urgent
                                        </span>
                                    @endif
                                    
                                    @if(!$message->is_read)
                                        <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                    @endif
                                </div>
                            </div>
                            
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 line-clamp-2">
                                {{ Str::limit(strip_tags($message->message), 120) }}
                            </p>
                            
                            <div class="flex items-center justify-between mt-2">
                                <div class="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                                    <span>{{ ucfirst(str_replace('_', ' ', $message->type)) }}</span>
                                    <span>{{ $message->created_at->diffForHumans() }}</span>
                                    @if($message->attachments_count > 0)
                                        <span class="flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                            </svg>
                                            {{ $message->attachments_count }}
                                        </span>
                                    @endif
                                </div>
                                
                                @if($message->is_replied)
                                    <span class="text-xs text-green-600 dark:text-green-400 font-medium">Replied</span>
                                @elseif($message->type !== 'admin_to_client')
                                    <span class="text-xs text-orange-600 dark:text-orange-400">Pending</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-6">
                {{ $messages->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No messages found</h3>
                <p class="text-gray-500 dark:text-gray-400">
                    @if(!empty(array_filter($filters ?? [])))
                        No messages match your current filters.
                    @else
                        No messages have been sent for this project yet.
                    @endif
                </p>
                @if(!empty(array_filter($filters ?? [])))
                    <div class="mt-4">
                        <x-admin.button 
                            href="{{ route('client.messages.project', $project) }}" 
                            color="light"
                        >
                            Clear Filters
                        </x-admin.button>
                    </div>
                @endif
            </div>
        @endif
    </x-admin.card>
</x-layouts.admin>