<x-layouts.client>
    <x-slot name="title">Messages</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Messages') }}
        </h2>
    </x-slot>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white dark:bg-neutral-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Messages</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $statistics['total'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-neutral-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-5 5v-5zm-11 4h7l-7-7v7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Unread</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $statistics['unread'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-neutral-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Replies</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                        {{ $statistics['pending_replies'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-neutral-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L3.316 16.5c-.77.833.192 2.5 1.732 2.5z">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Urgent</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $statistics['urgent'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <x-admin.filter-section :action="route('client.messages.index')" :searchValue="request('search')" searchPlaceholder="Search messages..." :hasActiveFilters="request()->hasAny(['search', 'type', 'priority', 'read'])"
        :clearFiltersRoute="route('client.messages.index')" :filters="[
            [
                'name' => 'type',
                'label' => 'Type',
                'allLabel' => 'All Types',
                'options' => $filterOptions['types'] ?? [
                    'general' => 'General',
                    'support' => 'Support',
                    'project_inquiry' => 'Project Inquiry',
                    'complaint' => 'Complaint',
                    'feedback' => 'Feedback',
                    'client_reply' => 'Client Reply',
                    'admin_to_client' => 'Admin to Client',
                ],
            ],
            [
                'name' => 'priority',
                'label' => 'Priority',
                'allLabel' => 'All Priorities',
                'options' => $filterOptions['priorities'] ?? [
                    'low' => 'Low',
                    'normal' => 'Normal',
                    'high' => 'High',
                    'urgent' => 'Urgent',
                ],
            ],
            [
                'name' => 'read',
                'label' => 'Read Status',
                'allLabel' => 'All Status',
                'options' => [
                    'unread' => 'Unread',
                    'read' => 'Read',
                ],
            ],
        ]" :sortOptions="[
            'created_at' => 'Date Created',
            'updated_at' => 'Last Updated',
            'subject' => 'Subject',
            'is_read' => 'Read Status',
        ]" />

    <!-- Messages List -->
    <div class="grid grid-cols-1 lg:grid-cols-7 gap-6">

        <!-- Left Column - Modern Actions Panel -->
        <div class="lg:col-span-1">
            <div
                class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

                <!-- Header -->
                <div class="px-2 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg flex justify-center font-semibold text-gray-900 dark:text-white">Menu</h3>
                </div>

                <div class="p-4 space-y-3">

                    <!-- Primary Actions -->
                    <div class="space-y-3">
                        <!-- New Message -->
                        <a href="{{ route('client.messages.create') }}"
                            class="group flex flex-col items-center justify-center p-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 rounded-lg transition-all duration-200 hover:scale-105 shadow-sm hover:shadow-md">
                            <svg class="w-6 h-6 text-white mb-1" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span class="text-xs text-center text-white opacity-90 group-hover:opacity-100">
                                New Message
                            </span>
                        </a>
                        <!-- Mark All Read -->
                        <button onclick="markAllAsRead()"
                            class="group flex flex-col items-center justify-center p-3 bg-gray-50 dark:bg-neutral-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition-all duration-200 hover:scale-105">
                            <svg class="w-6 h-6 text-gray-600 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-gray-200 mb-1"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span
                                class="text-xs text-gray-600 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-gray-200">
                                Mark All Read
                            </span>
                        </button>
                    </div>


                    <!-- Divider -->
                    <div class="border-t border-gray-100 dark:border-gray-700"></div>

                    <!-- Utility Actions -->
                    <div class="space-y-3">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Tools</h4>

                        <div class="grid grid-cols-2 gap-2">
                            <!-- Refresh -->
                            <button onclick="window.location.reload()"
                                class="group flex flex-col items-center justify-center p-3 bg-gray-50 dark:bg-neutral-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition-all duration-200 hover:scale-105">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-gray-200 mb-1"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                                <span
                                    class="text-xs text-gray-600 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-gray-200">Refresh</span>
                            </button>

                            <!-- Export -->
                            <button onclick="exportMessages()"
                                class="group flex flex-col items-center justify-center p-3 bg-gray-50 dark:bg-neutral-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition-all duration-200 hover:scale-105">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-gray-200 mb-1"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                <span
                                    class="text-xs text-gray-600 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-gray-200">Export</span>
                            </button>
                        </div>
                    </div>

                    <!-- Bulk Actions (shown when messages selected) -->
                    <div id="bulk-actions" class="hidden space-y-4">
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-4">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Bulk Actions</h4>
                            <div class="grid grid-cols-2 gap-2">
                                <!-- Bulk Mark Read -->
                                <button onclick="bulkMarkAsRead()"
                                    class="relative group flex items-center justify-center w-9 h-9 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/40 text-green-700 dark:text-green-300 rounded-lg transition-all duration-200"
                                    aria-label="Mark as Read">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4"></path>
                                    </svg>
                                    <span
                                        class="pointer-events-none absolute z-10 left-1/2 top-0 -translate-x-1/2 -translate-y-full mb-2 whitespace-nowrap rounded bg-green-700 px-2 py-1 text-xs text-white opacity-0 group-hover:opacity-100 transition-all duration-150">
                                        Mark as Read
                                    </span>
                                </button>
                                <!-- Bulk Mark Unread -->
                                <button onclick="bulkMarkAsUnread()"
                                    class="relative group flex items-center justify-center w-9 h-9 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/40 text-blue-700 dark:text-blue-300 rounded-lg transition-all duration-200"
                                    aria-label="Mark as Unread">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 12h.01M12 12h.01M16 12h.01"></path>
                                    </svg>
                                    <span
                                        class="pointer-events-none absolute z-10 left-1/2 top-0 -translate-x-1/2 -translate-y-full mb-2 whitespace-nowrap rounded bg-blue-700 px-2 py-1 text-xs text-white opacity-0 group-hover:opacity-100 transition-all duration-150">
                                        Mark as Unread
                                    </span>
                                </button>
                                <!-- Bulk Mark Urgent -->
                                <button onclick="bulkMarkUrgent()"
                                    class="relative group flex items-center justify-center w-9 h-9 bg-orange-50 dark:bg-orange-900/20 hover:bg-orange-100 dark:hover:bg-orange-900/40 text-orange-700 dark:text-orange-300 rounded-lg transition-all duration-200"
                                    aria-label="Mark as Urgent">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01"></path>
                                    </svg>
                                    <span
                                        class="pointer-events-none absolute z-10 left-1/2 top-0 -translate-x-1/2 -translate-y-full mb-2 whitespace-nowrap rounded bg-orange-700 px-2 py-1 text-xs text-white opacity-0 group-hover:opacity-100 transition-all duration-150">
                                        Mark as Urgent
                                    </span>
                                </button>
                                <!-- Bulk Delete -->
                                <button onclick="bulkDelete()"
                                    class="relative group flex items-center justify-center w-9 h-9 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 text-red-700 dark:text-red-300 rounded-lg transition-all duration-200"
                                    aria-label="Delete Selected">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                    <span
                                        class="pointer-events-none absolute z-10 left-1/2 top-0 -translate-x-1/2 -translate-y-full mb-2 whitespace-nowrap rounded bg-red-700 px-2 py-1 text-xs text-white opacity-0 group-hover:opacity-100 transition-all duration-150">
                                        Delete Selected
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Right Column - Messages Table Card -->
        <div class="lg:col-span-6">
            <x-admin.card>
                @if ($messages->count() > 0)
                    <!-- Table Header -->
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                Message List
                            </h3>

                            <div class="text-sm text-gray-700 dark:text-neutral-400">
                                Showing
                                <span class="font-medium">{{ $messages->firstItem() }}</span>
                                to
                                <span class="font-medium">{{ $messages->lastItem() }}</span>
                                of
                                <span class="font-medium">{{ $messages->total() }}</span>
                                results
                            </div>
                        </div>
                    </div>

                    <!-- Messages Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-neutral-800">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        <input type="checkbox"
                                            class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                            onchange="toggleAllCheckboxes(this)">
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Subject & Message
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Priority
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Date
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-neutral-900 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($messages as $message)
                                    <tr
                                        class="hover:bg-gray-50 dark:hover:bg-gray-800 {{ !$message->is_read ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                                        <td class="px-6 py-4">
                                            <input type="checkbox"
                                                class="message-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                                value="{{ $message->id }}" onchange="updateBulkActions()">
                                        </td>

                                        <!-- Subject & Preview -->
                                        <td class="px-6 py-4 max-w-xs">
                                            <div class="flex items-center">
                                                @if ($message->priority === 'urgent')
                                                    <span
                                                        class="inline-flex items-center mr-2 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                        Urgent
                                                    </span>
                                                @endif

                                                <a href="{{ route('client.messages.show', $message) }}"
                                                    class="font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 truncate">
                                                    {{ $message->subject }}
                                                </a>

                                                @if (!$message->is_read)
                                                    <span
                                                        class="ml-2 w-2 h-2 bg-blue-500 rounded-full flex-shrink-0"></span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">
                                                {{ Str::limit(strip_tags($message->message), 100) }}
                                            </div>

                                            @if ($message->attachments->count() > 0)
                                                <div class="flex items-center mt-1">
                                                    <svg class="w-3 h-3 text-gray-400 mr-1" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13">
                                                        </path>
                                                    </svg>
                                                    <span
                                                        class="text-xs text-gray-500">{{ $message->attachments->count() }}
                                                        attachment(s)</span>
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Type -->
                                        <td class="px-6 py-4 whitespace-nowrap text-xs">
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                                        {{ $message->type === 'admin_to_client'
                                                            ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                                                            : ($message->type === 'support'
                                                                ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
                                                                : ($message->type === 'complaint'
                                                                    ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                                                    : 'bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-gray-200')) }}">
                                                {{ ucfirst(str_replace('_', ' ', $message->type)) }}
                                            </span>
                                        </td>

                                        <!-- Priority -->
                                        <td class="px-6 py-4 whitespace-nowrap text-xs">
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                        {{ $message->priority === 'urgent'
                                                            ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                                            : ($message->priority === 'high'
                                                                ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200'
                                                                : ($message->priority === 'low'
                                                                    ? 'bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-gray-200'
                                                                    : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200')) }}">
                                                {{ ucfirst($message->priority) }}
                                            </span>
                                        </td>

                                        <!-- Status -->
                                        <td class="px-6 py-4 whitespace-nowrap text-xs">
                                            <div class="flex flex-col gap-1">
                                                @if ($message->is_read)
                                                    <span
                                                        class="text-green-600 dark:text-green-400 font-medium">Read</span>
                                                @else
                                                    <span
                                                        class="text-blue-600 dark:text-blue-400 font-medium">Unread</span>
                                                @endif

                                                @if ($message->is_replied)
                                                    <span
                                                        class="text-xs text-green-600 dark:text-green-400">Replied</span>
                                                @elseif($message->type !== 'admin_to_client' && $message->requires_response)
                                                    <span
                                                        class="text-xs text-orange-600 dark:text-orange-400">Pending</span>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Date -->
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <div class="flex flex-col">
                                                <span>{{ $message->created_at->format('M d, Y') }}</span>
                                                <span class="text-xs">{{ $message->created_at->format('H:i') }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $messages->withQueryString()->links() }}
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                            </path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No messages found</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">
                            @if (!empty(array_filter($filters ?? [])))
                                No messages match your current filters.
                            @else
                                You haven't sent any messages yet.
                            @endif
                        </p>

                        <div class="flex justify-center gap-3">
                            @if (!empty(array_filter($filters ?? [])))
                                <x-admin.button href="{{ route('client.messages.index') }}" color="light">
                                    Clear Filters
                                </x-admin.button>
                            @endif

                            <x-admin.button href="{{ route('client.messages.create') }}" color="primary">
                                Send Your First Message
                            </x-admin.button>
                        </div>
                    </div>
                @endif
            </x-admin.card>
        </div>
    </div>

    <!-- JavaScript for interactive features -->
    <script>
        // Toggle all checkboxes
        function toggleAllCheckboxes(masterCheckbox) {
            const checkboxes = document.querySelectorAll('.message-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = masterCheckbox.checked;
            });
            updateBulkActions();
        }

        // Update bulk actions visibility
        function updateBulkActions() {
            const selectedBoxes = document.querySelectorAll('.message-checkbox:checked');
            const bulkActions = document.getElementById('bulk-actions');

            if (selectedBoxes.length > 0) {
                bulkActions.classList.remove('hidden');
            } else {
                bulkActions.classList.add('hidden');
            }
        }

        // Bulk mark selected as read
        function bulkMarkAsRead() {
            const selectedBoxes = document.querySelectorAll('.message-checkbox:checked');
            const messageIds = Array.from(selectedBoxes).map(box => box.value);

            if (messageIds.length === 0) {
                alert('Please select messages to mark as read.');
                return;
            }

            fetch('/client/messages/bulk-mark-read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        message_ids: messageIds
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to mark messages as read');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
        }

        // Bulk mark selected as unread
        function bulkMarkAsUnread() {
            const selectedBoxes = document.querySelectorAll('.message-checkbox:checked');
            const messageIds = Array.from(selectedBoxes).map(box => box.value);

            if (messageIds.length === 0) {
                alert('Please select messages to mark as unread.');
                return;
            }

            fetch('/client/messages/bulk-mark-unread', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        message_ids: messageIds
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to mark messages as unread');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
        }

        // Bulk mark selected as urgent
        function bulkMarkUrgent() {
            const selectedBoxes = document.querySelectorAll('.message-checkbox:checked');
            const messageIds = Array.from(selectedBoxes).map(box => box.value);

            if (messageIds.length === 0) {
                alert('Please select messages to mark as urgent.');
                return;
            }

            if (confirm('Are you sure you want to mark selected messages as urgent?')) {
                fetch('/client/messages/bulk-mark-urgent', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            message_ids: messageIds
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Failed to mark messages as urgent');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    });
            }
        }

        // Bulk delete selected messages
        function bulkDelete() {
            const selectedBoxes = document.querySelectorAll('.message-checkbox:checked');
            const messageIds = Array.from(selectedBoxes).map(box => box.value);

            if (messageIds.length === 0) {
                alert('Please select messages to delete.');
                return;
            }

            if (confirm(
                    `Are you sure you want to delete ${messageIds.length} selected message(s)? This action cannot be undone.`
                )) {
                fetch('/client/messages/bulk-delete', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            message_ids: messageIds
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Failed to delete messages');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    });
            }
        }

        // Export messages
        function exportMessages() {
            const url = new URL('/client/messages/export', window.location.origin);
            // Add current filters to export
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.forEach((value, key) => {
                url.searchParams.set(key, value);
            });

            window.open(url.toString(), '_blank');
        }

        // Toggle archive mode
        function toggleArchiveMode() {
            const btn = document.getElementById('archive-btn');
            const isArchiveMode = btn.classList.contains('archive-active');

            if (isArchiveMode) {
                // Exit archive mode
                btn.classList.remove('archive-active');
                window.location.href = '/client/messages';
            } else {
                // Enter archive mode
                btn.classList.add('archive-active');
                window.location.href = '/client/messages?archived=true';
            }
        }

        // Toggle read status
        function toggleReadStatus(messageId) {
            fetch(`/client/messages/${messageId}/toggle-read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload(); // Simple reload to update UI
                    } else {
                        alert(data.message || 'Failed to update message status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
        }

        // Mark all as read
        function markAllAsRead() {
            if (confirm('Are you sure you want to mark all messages as read?')) {
                fetch('/client/messages/mark-all-read', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload(); // Simple reload to update UI
                        } else {
                            alert(data.message || 'Failed to mark messages as read');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    });
            }
        }
    </script>
</x-layouts.client>
