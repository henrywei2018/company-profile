{{-- resources/views/admin/messages/index.blade.php --}}
<x-layouts.admin 
    title="Messages Management" 
    :unreadMessages="$statistics['unread_messages'] ?? 0"
    :pendingApprovals="0"
>
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Unread Messages -->
        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">New Messages</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $statistics['new_messages'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Today's Messages -->
        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Today's Messages</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $statistics['today_messages'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Total Messages -->
        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Messages</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $statistics['total_messages'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Average Response Time -->
        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Avg Response</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $statistics['avg_response_time'] ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <x-admin.filter-section 
        :action="route('admin.messages.index')" 
        :searchValue="request('search')" 
        searchPlaceholder="Search by client name, email or subject..." 
        :hasActiveFilters="request()->hasAny(['search', 'status', 'priority', 'created_from', 'created_to'])"
        :clearFiltersRoute="route('admin.messages.index')" 
        :filters="[
            [
                'name' => 'status',
                'label' => 'Status',
                'allLabel' => 'All Status',
                'options' => [
                    'unread' => 'Unread',
                    'read' => 'Read',
                    'replied' => 'Replied',
                ],
            ],
            [
                'name' => 'priority',
                'label' => 'Priority',
                'allLabel' => 'All Priorities',
                'options' => [
                    'low' => 'Low',
                    'normal' => 'Normal',
                    'high' => 'High',
                    'urgent' => 'Urgent',
                ],
            ],
        ]" 
        :sortOptions="[
            'created_at' => 'Date Created',
            'updated_at' => 'Last Updated',
            'is_read' => 'Read Status',
            'priority' => 'Priority',
        ]" 
    />

    <!-- Messages List -->
    <div class="grid grid-cols-1 lg:grid-cols-7 gap-6">

        <!-- Left Column - Admin Actions Panel -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

                <!-- Header -->
                <div class="px-2 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg flex justify-center font-semibold text-gray-900 dark:text-white">Actions</h3>
                </div>

                <div class="p-4 space-y-3">

                    <!-- Primary Actions -->
                    <div class="space-y-3">
                        <!-- Send Message -->
                        <a href="{{ route('admin.messages.create') }}"
                            class="group flex flex-col items-center justify-center p-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 rounded-lg transition-all duration-200 hover:scale-105 shadow-sm hover:shadow-md">
                            <svg class="w-6 h-6 text-white mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span class="text-xs text-center text-white opacity-90 group-hover:opacity-100">Send Message</span>
                        </a>

                        <!-- Statistics -->
                        <a href="{{ route('admin.messages.statistics') }}"
                            class="group flex flex-col items-center justify-center p-3 bg-gray-50 dark:bg-neutral-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition-all duration-200 hover:scale-105">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-gray-200 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <span class="text-xs text-gray-600 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-gray-200">Statistics</span>
                        </a>

                        <!-- Export -->
                        <button onclick="exportMessages()"
                            class="group flex flex-col items-center justify-center p-3 bg-gray-50 dark:bg-neutral-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition-all duration-200 hover:scale-105">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-gray-200 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="text-xs text-gray-600 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-gray-200">Export</span>
                        </button>

                        <!-- Refresh -->
                        <button onclick="window.location.reload()"
                            class="group flex flex-col items-center justify-center p-3 bg-gray-50 dark:bg-neutral-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition-all duration-200 hover:scale-105">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-gray-200 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span class="text-xs text-gray-600 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-gray-200">Refresh</span>
                        </button>
                    </div>

                    <!-- Bulk Actions (shown when messages selected) -->
                    <div id="bulk-actions" class="hidden space-y-4">
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-4">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Bulk Actions</h4>
                            <div class="grid grid-cols-1 gap-2">
                                <!-- Bulk Mark Read -->
                                <button onclick="bulkAction('mark_read')" 
                                    class="flex flex-col items-center justify-center p-2 bg-green-50 dark:bg-green-900/30 hover:bg-green-100 dark:hover:bg-green-900/50 rounded-lg transition-all duration-200">
                                    <svg class="w-4 h-4 text-green-600 dark:text-green-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-xs text-green-600 dark:text-green-400">Mark Read</span>
                                </button>

                                <!-- Bulk Mark Unread -->
                                <button onclick="bulkAction('mark_unread')" 
                                    class="flex flex-col items-center justify-center p-2 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-lg transition-all duration-200">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-xs text-blue-600 dark:text-blue-400">Mark Unread</span>
                                </button>

                                <!-- Bulk Delete -->
                                <button onclick="bulkAction('delete')" 
                                    class="flex flex-col items-center justify-center p-2 bg-red-50 dark:bg-red-900/30 hover:bg-red-100 dark:hover:bg-red-900/50 rounded-lg transition-all duration-200">
                                    <svg class="w-4 h-4 text-red-600 dark:text-red-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    <span class="text-xs text-red-600 dark:text-red-400">Delete</span>
                                </button>

                                <!-- Bulk Archive -->
                                <button onclick="bulkAction('archive')" 
                                    class="flex flex-col items-center justify-center p-2 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition-all duration-200">
                                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8l6 6 6-6"></path>
                                    </svg>
                                    <span class="text-xs text-gray-600 dark:text-gray-400">Archive</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Filter Buttons -->
                    <div class="border-t border-gray-100 dark:border-gray-700 pt-4">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Quick Filters</h4>
                        <div class="space-y-2">
                            <a href="{{ route('admin.messages.index', ['status' => 'unread']) }}" 
                                class="flex items-center justify-between px-3 py-2 text-sm rounded-lg {{ request('status') === 'unread' ? 'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                <span>Unread</span>
                                <span class="text-xs">{{ $statistics['new_messages'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('admin.messages.index', ['priority' => 'urgent']) }}" 
                                class="flex items-center justify-between px-3 py-2 text-sm rounded-lg {{ request('priority') === 'urgent' ? 'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                <span>Urgent</span>
                                <span class="text-xs">{{ $statistics['urgent_messages'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('admin.messages.index', ['type' => 'support']) }}" 
                                class="flex items-center justify-between px-3 py-2 text-sm rounded-lg {{ request('type') === 'support' ? 'bg-yellow-50 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                <span>Support</span>
                                <span class="text-xs">{{ $statistics['support_messages'] ?? 0 }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content - Messages Table -->
        <div class="lg:col-span-6">
            @if ($messages->count() > 0)
                <!-- Bulk Actions Bar -->
                <div id="bulk-actions-bar" class="hidden mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <div class="flex items-center justify-between">
                        <span id="selected-count" class="text-sm font-medium text-blue-900 dark:text-blue-100">
                            0 messages selected
                        </span>
                        <div class="flex gap-2">
                            <button onclick="bulkAction('mark_read')" 
                                class="px-3 py-1 text-xs font-medium rounded-md bg-green-100 text-green-800 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-300 dark:hover:bg-green-900/50">
                                Mark Read
                            </button>
                            <button onclick="bulkAction('mark_unread')" 
                                class="px-3 py-1 text-xs font-medium rounded-md bg-blue-100 text-blue-800 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:hover:bg-blue-900/50">
                                Mark Unread
                            </button>
                            <button onclick="bulkAction('delete')" 
                                class="px-3 py-1 text-xs font-medium rounded-md bg-red-100 text-red-800 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300 dark:hover:bg-red-900/50">
                                Delete
                            </button>
                            <button onclick="clearSelection()" 
                                class="px-3 py-1 text-xs font-medium rounded-md bg-gray-100 text-gray-800 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                                Clear
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Messages Table -->
                <x-admin.card>
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="w-4 px-6 py-3">
                                        <input type="checkbox" id="select-all" onchange="selectAll()" 
                                            class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Client
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Subject
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Priority
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($messages as $message)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 {{ !$message->is_read ? 'bg-blue-50 dark:bg-blue-900/10' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" 
                                                class="message-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" 
                                                value="{{ $message->id }}" 
                                                onchange="updateBulkActions()">
                                        </td>
                                        
                                        <!-- Client Info -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                            {{ substr($message->name, 0, 2) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $message->name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $message->email }}
                                                    </div>
                                                    @if($message->user)
                                                        <div class="text-xs text-blue-600 dark:text-blue-400">
                                                            Client ID: {{ $message->user->id }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Subject -->
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 dark:text-white font-medium">
                                                <a href="{{ route('admin.messages.show', $message) }}" 
                                                    class="hover:text-blue-600 dark:hover:text-blue-400">
                                                    {{ Str::limit($message->subject, 50) }}
                                                </a>
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ Str::limit($message->message, 100) }}
                                            </div>
                                            @if($message->attachments && $message->attachments->count() > 0)
                                                <div class="flex items-center mt-2 text-xs text-gray-500">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                    </svg>
                                                    {{ $message->attachments->count() }} attachment(s)
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Type -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                {{ $message->type === 'support' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                                                   ($message->type === 'complaint' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                                                   ($message->type === 'project_inquiry' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 
                                                   'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200')) }}">
                                                {{ ucfirst(str_replace('_', ' ', $message->type)) }}
                                            </span>
                                        </td>

                                        <!-- Priority -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                {{ $message->priority === 'urgent' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                                                   ($message->priority === 'high' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : 
                                                   ($message->priority === 'normal' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 
                                                   'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200')) }}">
                                                {{ ucfirst($message->priority ?? 'normal') }}
                                            </span>
                                        </td>

                                        <!-- Date -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <div>{{ $message->created_at->format('M j, Y') }}</div>
                                            <div class="text-xs">{{ $message->created_at->format('H:i') }}</div>
                                        </td>

                                        <!-- Status -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if(!$message->is_read)
                                                    <div class="flex items-center">
                                                        <div class="w-2 h-2 bg-red-400 rounded-full mr-2"></div>
                                                        <span class="text-sm text-red-600 dark:text-red-400 font-medium">Unread</span>
                                                    </div>
                                                @else
                                                    <div class="flex items-center">
                                                        <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                                                        <span class="text-sm text-green-600 dark:text-green-400">Read</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Actions -->
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-2">
                                                <a href="{{ route('admin.messages.show', $message) }}" 
                                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </a>
                                                
                                                <a href="{{ route('admin.messages.reply', $message) }}" 
                                                    class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                                    </svg>
                                                </a>

                                                <form action="{{ route('admin.messages.toggle-read', $message) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                        class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300">
                                                        @if($message->is_read)
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                            </svg>
                                                        @else
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                        @endif
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" class="inline" 
                                                    onsubmit="return confirm('Are you sure you want to delete this message?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($messages->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                            {{ $messages->links() }}
                        </div>
                    @endif
                </x-admin.card>
            @else
                <!-- Empty State -->
                <x-admin.card>
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No messages found</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            @if(request()->hasAny(['search', 'status', 'type', 'priority', 'client_id', 'created_from', 'created_to']))
                                No messages match your current filters.
                            @else
                                No messages have been received yet.
                            @endif
                        </p>

                        <div class="mt-6 flex justify-center gap-3">
                            @if(request()->hasAny(['search', 'status', 'type', 'priority', 'client_id', 'created_from', 'created_to']))
                                <a href="{{ route('admin.messages.index') }}" 
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                                    Clear Filters
                                </a>
                            @endif

                            <a href="{{ route('admin.messages.create') }}" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 shadow-sm">
                                Send Message
                            </a>
                        </div>
                    </div>
                </x-admin.card>
            @endif
        </div>
    </div>

    <!-- Bulk Actions Script -->
    @include('admin.messages.partials.bulk-actions-script')

</x-layouts.admin>

<script>
// Export Messages Function
function exportMessages() {
    const searchParams = new URLSearchParams(window.location.search);
    const exportUrl = new URL('{{ route("admin.messages.export") }}', window.location.origin);
    
    // Add current filters to export URL
    for (const [key, value] of searchParams) {
        exportUrl.searchParams.append(key, value);
    }
    
    window.location.href = exportUrl.toString();
}

// Message Management Functions
function quickReply(messageId) {
    window.location.href = `{{ route('admin.messages.index') }}/${messageId}/reply`;
}

function viewMessage(messageId) {
    window.location.href = `{{ route('admin.messages.index') }}/${messageId}`;
}

function toggleMessageRead(messageId) {
    fetch(`{{ route('admin.messages.index') }}/${messageId}/toggle-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the message.');
    });
}
</script>