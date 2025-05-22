<!-- resources/views/admin/messages/index.blade.php -->
<x-layouts.admin title="Messages Management" :unreadMessages="$unreadMessages" :pendingQuotations="$pendingQuotations">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'Messages' => route('admin.messages.index'),
        ]" />
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Urgent Messages (Unreplied & Unread) -->
        <x-admin.stat-card 
            title="Urgent Messages" 
            :value="$statusCounts['unread_unreplied'] ?? 0"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />'
            iconColor="text-red-600 dark:text-red-400" 
            iconBg="bg-red-100 dark:bg-red-900/30"
            :href="route('admin.messages.index', ['status' => 'unread_unreplied'])"
        />
        
        <!-- Unreplied Messages -->
        <x-admin.stat-card 
            title="Needs Reply" 
            :value="$statusCounts['unreplied'] ?? 0"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />'
            iconColor="text-amber-600 dark:text-amber-400" 
            iconBg="bg-amber-100 dark:bg-amber-900/30"
            :href="route('admin.messages.index', ['status' => 'unreplied'])"
        />
        
        <!-- Unread Messages -->
        <x-admin.stat-card 
            title="Unread Messages" 
            :value="$statusCounts['unread'] ?? 0"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />'
            iconColor="text-blue-600 dark:text-blue-400" 
            iconBg="bg-blue-100 dark:bg-blue-900/30"
            :href="route('admin.messages.index', ['status' => 'unread'])"
        />
        
        <!-- Total Messages -->
        <x-admin.stat-card 
            title="Total Messages" 
            :value="$statusCounts['total'] ?? 0"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />'
            iconColor="text-gray-600 dark:text-gray-400" 
            iconBg="bg-gray-100 dark:bg-gray-700"
            :href="route('admin.messages.index')"
        />
    </div>

    <!-- Filters -->
    <x-admin.filter action="{{ route('admin.messages.index') }}" method="GET" :resetRoute="route('admin.messages.index')">
        <x-admin.input name="search" label="Search" placeholder="Search by name, email or subject"
            value="{{ request('search') }}" />

        <x-admin.select name="status" label="Status" :options="[
            'unread_unreplied' => 'Urgent (Unreplied & Unread)',
            'unreplied' => 'Needs Reply',
            'unread' => 'Unread',
            'read' => 'Read',
            'replied' => 'Replied'
        ]" placeholder="All Statuses" value="{{ request('status') }}" />

        <x-admin.select name="type" label="Type" :options="[
            'contact_form' => 'Contact Form',
            'client_to_admin' => 'Client Message',
        ]" placeholder="All Types"
            value="{{ request('type') }}" />

        <x-admin.date-range-picker name="date_range" label="Date Range" startName="created_from" endName="created_to"
            :startDate="request('created_from')" :endDate="request('created_to')" placeholder="Select date range" />
    </x-admin.filter>

    <!-- Messages List -->
    <x-admin.card>
    
        <x-slot name="headerActions">
        <div class="flex items-center justify-between w-full px-4 py-2">
            <!-- Left side: Action buttons -->
            <div class="flex items-center space-x-3">
                @if($messages->count() > 0)
                <form action="{{ route('admin.messages.mark-read') }}" method="POST" class="inline">
                    @csrf
                    <x-admin.button
                        type="submit"
                        color="success"
                        size="sm"
                        class="flex items-center justify-center"
                    >
                    <div class="flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Mark All as Read
                    </div>
                    </x-admin.button>
                </form>
                
                <form action="{{ route('admin.messages.destroy-multiple') }}" method="POST" class="inline" id="delete-selected-form">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="ids" id="selected-ids" value="">
                    <x-admin.button
                        type="button"
                        color="danger"
                        size="sm"
                        onclick="confirmDeleteSelected()"
                        class="flex items-center justify-center"
                    >
                    <div class="flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete Selected
                    </div>
                    </x-admin.button>
                </form>
                @endif
            </div>
            
            <!-- Right side: Pagination-style message count -->
            <div class="flex items-center space-x-4">
                @if($messages->count() > 0)
                    <!-- Message count info (pagination style) -->
                    <div class="text-sm text-gray-700 dark:text-neutral-400">
                        Showing
                        <span class="font-medium text-gray-900 dark:text-white">{{ $messages->firstItem() }}</span>
                        to
                        <span class="font-medium text-gray-900 dark:text-white">{{ $messages->lastItem() }}</span>
                        of
                        <span class="font-medium text-gray-900 dark:text-white">{{ $messages->total() }}</span>
                        messages
                    </div>
                    
                    <!-- Optional: Add a divider -->
                    <div class="h-5 w-px bg-gray-300 dark:bg-neutral-600"></div>
                    
                    <!-- Optional: Quick filter status -->
                    @if(request()->hasAny(['search', 'status', 'type', 'created_from', 'created_to']))
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            <span class="text-sm text-blue-600 dark:text-blue-400 font-medium">Filtered</span>
                        </div>
                    @endif
                @else
                    <span class="text-sm text-gray-500 dark:text-neutral-500">No messages found</span>
                @endif
            </div>
        </div>
    </x-slot>

        @if ($messages->count() > 0)
            <x-admin.data-table checkbox="true" class="space-y-1">
                <x-slot name="columns">
                    <x-admin.table-column>Sender</x-admin.table-column>
                    <x-admin.table-column>Subject</x-admin.table-column>
                    <x-admin.table-column>Type</x-admin.table-column>
                    <x-admin.table-column sortable="true" field="created_at"
                        direction="{{ request('sort') === 'created_at' ? request('direction', 'asc') : null }}">Date</x-admin.table-column>
                    <x-admin.table-column>Status</x-admin.table-column>
                    <x-admin.table-column>Actions</x-admin.table-column>
                </x-slot>

                @foreach ($messages as $message)
                    @php
                        // Determine priority level and styling
                        $priorityLevel = '';
                        $priorityColor = '';
                        $priorityBadge = '';
                        $rowBg = '';
                        
                        if (!$message->is_replied && !$message->is_read) {
                            $priorityLevel = 'urgent';
                            $priorityColor = 'bg-red-500';
                            $priorityBadge = 'Urgent';
                            $rowBg = 'bg-red-50 dark:bg-red-900/10';
                        } elseif (!$message->is_replied) {
                            $priorityLevel = 'needs-reply';
                            $priorityColor = 'bg-amber-500';
                            $priorityBadge = 'Needs Reply';
                            $rowBg = 'bg-amber-50 dark:bg-amber-900/10';
                        } elseif (!$message->is_read) {
                            $priorityLevel = 'unread';
                            $priorityColor = 'bg-blue-500';
                            $priorityBadge = 'Unread';
                            $rowBg = 'bg-blue-50 dark:bg-blue-900/10';
                        }
                    @endphp
                    
                    <tr class="{{ $rowBg }} hover:bg-gray-50 dark:hover:bg-neutral-700 cursor-pointer relative"
                        onclick="window.location='{{ route('admin.messages.show', $message) }}'">
                        
                        <!-- Priority Indicator (Left Border) -->
                        @if($priorityLevel)
                            <td class="absolute left-0 top-0 bottom-0 w-1 {{ $priorityColor }}"></td>
                        @endif
                        
                        <td class="whitespace-nowrap">
                            <input type="checkbox" name="message_ids[]" value="{{ $message->id }}"
                                class="message-checkbox shrink-0 border-gray-300 rounded-sm text-blue-600 focus:ring-blue-500 checked:border-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-neutral-800"
                                onclick="event.stopPropagation()">
                        </td>

                        <x-admin.table-cell class="max-w-xs truncate" :highlight="!$message->is_read">
                            <div class="flex items-center">
                                <div
                                    class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-full {{ !$message->is_read ? 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-gray-100 text-gray-600 dark:bg-neutral-800 dark:text-neutral-400' }}">
                                    @if ($message->type === 'client_to_admin')
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="flex items-center">
                                        <span class="text-sm font-medium {{ !$message->is_read ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-neutral-400' }}">
                                            {{ $message->name }}
                                        </span>
                                        @if($priorityBadge)
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ 
                                                $priorityLevel === 'urgent' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : 
                                                ($priorityLevel === 'needs-reply' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400') 
                                            }}">
                                                {{ $priorityBadge }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-sm {{ !$message->is_read ? 'text-gray-700 dark:text-neutral-300' : 'text-gray-500 dark:text-neutral-500' }}">
                                        {{ $message->email }}
                                    </div>
                                </div>
                            </div>
                        </x-admin.table-cell>

                        <x-admin.table-cell class="max-w-xs truncate">
                            <span class="text-sm {{ !$message->is_read ? 'font-semibold text-gray-900 dark:text-white' : 'text-gray-600 dark:text-neutral-400' }}">
                                {{ $message->subject }}
                            </span>
                            <div class="text-xs text-gray-500 dark:text-neutral-500 truncate max-w-xs">
                                {{ Str::limit(strip_tags($message->message), 80) }}
                            </div>
                        </x-admin.table-cell>

                        <x-admin.table-cell>
                            @if ($message->type === 'contact_form')
                                <x-admin.badge type="info">Contact Form</x-admin.badge>
                            @elseif($message->type === 'client_to_admin')
                                <x-admin.badge type="primary">Client Message</x-admin.badge>
                            @else
                                <x-admin.badge>{{ $message->type }}</x-admin.badge>
                            @endif
                        </x-admin.table-cell>

                        <x-admin.table-cell>
                            <span class="text-sm text-gray-600 dark:text-neutral-400"
                                title="{{ $message->created_at }}">
                                {{ $message->created_at->diffForHumans() }}
                            </span>
                            <div class="text-xs text-gray-500 dark:text-neutral-500">
                                {{ $message->created_at->format('M d, Y H:i') }}
                            </div>
                        </x-admin.table-cell>

                        <x-admin.table-cell>
                            <div class="flex flex-col space-y-1">
                                <!-- Read Status -->
                                @if ($message->is_read)
                                    <x-admin.badge type="success" dot="true" size="sm">Read</x-admin.badge>
                                @else
                                    <x-admin.badge type="warning" dot="true" size="sm">Unread</x-admin.badge>
                                @endif
                                
                                <!-- Reply Status -->
                                @if ($message->is_replied)
                                    <x-admin.badge type="info" dot="true" size="sm">
                                        Replied
                                        @if($message->repliedBy)
                                            <span class="text-xs opacity-75">by {{ $message->repliedBy->name }}</span>
                                        @endif
                                    </x-admin.badge>
                                @else
                                    <x-admin.badge type="danger" dot="true" size="sm">Unreplied</x-admin.badge>
                                @endif
                            </div>
                        </x-admin.table-cell>

                        <x-admin.table-cell>
                            <div class="flex items-center space-x-2" onclick="event.stopPropagation()">
                                <a href="{{ route('admin.messages.show', $message) }}"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                    title="View Message">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>

                                <form action="{{ route('admin.messages.toggle-read', $message) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="{{ $message->is_read ? 'text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300' : 'text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300' }}"
                                        title="{{ $message->is_read ? 'Mark as Unread' : 'Mark as Read' }}">
                                        @if ($message->is_read)
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                        @else
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76" />
                                            </svg>
                                        @endif
                                    </button>
                                </form>

                                <form action="{{ route('admin.messages.destroy', $message) }}" method="POST"
                                    class="inline"
                                    onsubmit="return confirm('Are you sure you want to delete this message?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                        title="Delete Message">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </x-admin.table-cell>
                    </tr>
                @endforeach
            </x-admin.data-table>

            <div class="px-6 py-4">
                {{ $messages->withQueryString()->links() }}
            </div>
        @else
            <x-admin.empty-state title="No messages found" description="There are no messages matching your criteria."
                icon='<svg class="w-10 h-10 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>' />
        @endif
    </x-admin.card>
</x-layouts.admin>

<script>
    // Handle checkbox selection for bulk actions
    document.addEventListener('DOMContentLoaded', function() {
        const mainCheckbox = document.getElementById('hs-at-with-checkboxes-main');
        const messageCheckboxes = document.querySelectorAll('.message-checkbox');

        if (mainCheckbox) {
            mainCheckbox.addEventListener('change', function() {
                messageCheckboxes.forEach(checkbox => {
                    checkbox.checked = mainCheckbox.checked;
                });
            });
        }

        // Update main checkbox state based on individual checkboxes
        messageCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allChecked = Array.from(messageCheckboxes).every(c => c.checked);
                const someChecked = Array.from(messageCheckboxes).some(c => c.checked);

                if (mainCheckbox) {
                    mainCheckbox.checked = allChecked;
                    mainCheckbox.indeterminate = someChecked && !allChecked;
                }
            });
        });
    });

    // Function to confirm and submit bulk delete
    function confirmDeleteSelected() {
        const selectedCheckboxes = document.querySelectorAll('.message-checkbox:checked');
        const selectedIds = Array.from(selectedCheckboxes).map(checkbox => checkbox.value);

        if (selectedIds.length === 0) {
            alert('Please select at least one message to delete.');
            return;
        }

        if (confirm(`Are you sure you want to delete ${selectedIds.length} selected message(s)?`)) {
            document.getElementById('selected-ids').value = selectedIds.join(',');
            document.getElementById('delete-selected-form').submit();
        }
    }
</script>