{{-- resources/views/admin/messages/index.blade.php --}}
<x-layouts.admin 
    title="Messages Management" 
    :unreadMessages="$statistics['unread_messages'] ?? 0"
    :pendingApprovals="0"
>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <!-- Total Messages -->
    <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-md dark:bg-blue-900">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Messages</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ number_format($statistics['total_messages'] ?? 0) }}
                </p>
            </div>
        </div>
    </div>

    <!-- Unread Messages -->
    <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center w-8 h-8 bg-red-100 rounded-md dark:bg-red-900">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Unread</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ number_format($statistics['unread_messages'] ?? 0) }}
                </p>
            </div>
        </div>
    </div>

    <!-- Unreplied Messages -->
    <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center w-8 h-8 bg-yellow-100 rounded-md dark:bg-yellow-900">
                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Replies</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ number_format($statistics['unreplied_messages'] ?? 0) }}
                </p>
            </div>
        </div>
    </div>

    <!-- Today's Messages -->
    <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center w-8 h-8 bg-green-100 rounded-md dark:bg-green-900">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Today</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ number_format($statistics['today_messages'] ?? 0) }}
                </p>
            </div>
        </div>
    </div>

    <!-- Urgent Messages -->
    <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center w-8 h-8 bg-orange-100 rounded-md dark:bg-orange-900">
                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Urgent</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ number_format($statistics['urgent_messages'] ?? 0) }}
                </p>
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
                    'unreplied' => 'Unreplied',
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
    <x-admin.bulk-actions.bulk-actions 
            :route="route('admin.messages.bulk-action')"
            :previewRoute="route('admin.messages.preview-bulk-action')"
            :statisticsRoute="route('admin.messages.statistics')"
            :canForceDelete="true"
            :showPriorityActions="true"
            :showAssignActions="false"
            type="admin"
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
                    </div>

                </div>
            </div>
        </div>

        <!-- Main Content - Messages Table -->
        <div class="lg:col-span-6">
            @if ($messages->count() > 0)
                <!-- Bulk Actions Bar -->
                

                <!-- Messages Table -->
                <x-admin.card>
                    <x-admin.messages-table 
        :messages="$messages"
        :showBulkActions="true"
        :showProjectColumn="true"
        :showClientColumn="true"
        :actionRoutes="[
            'show' => 'admin.messages.show',
            'reply' => 'admin.messages.reply',
            'toggle_read' => 'admin.messages.toggle-read',
            'destroy' => 'admin.messages.destroy'
        ]"
    />
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


</x-layouts.admin>
