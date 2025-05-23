<x-layouts.admin title="Quotations Management" :unreadMessages="$unreadMessages" :pendingQuotations="$pendingQuotations">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'Quotations' => route('admin.quotations.index'),
        ]" />
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 mb-6">
        <!-- Urgent/High Priority Quotations -->
        <x-admin.stat-card 
            title="Needs Attention" 
            :value="$statusCounts['needs_attention'] ?? 0"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />'
            iconColor="text-red-600 dark:text-red-400" 
            iconBg="bg-red-100 dark:bg-red-900/30"
            :href="route('admin.quotations.index', ['status' => 'pending', 'priority' => 'high'])"
        />
        
        <!-- Pending Quotations -->
        <x-admin.stat-card 
            title="Pending Review" 
            :value="$statusCounts['pending'] ?? 0"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />'
            iconColor="text-amber-600 dark:text-amber-400" 
            iconBg="bg-amber-100 dark:bg-amber-900/30"
            :href="route('admin.quotations.index', ['status' => 'pending'])"
        />
        
        <!-- Approved Quotations -->
        <x-admin.stat-card 
            title="Approved" 
            :value="$statusCounts['approved'] ?? 0"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'
            iconColor="text-green-600 dark:text-green-400" 
            iconBg="bg-green-100 dark:bg-green-900/30"
            :href="route('admin.quotations.index', ['status' => 'approved'])"
        />
        
        <!-- This Month -->
        <x-admin.stat-card 
            title="This Month" 
            :value="$statusCounts['this_month'] ?? 0"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />'
            iconColor="text-blue-600 dark:text-blue-400" 
            iconBg="bg-blue-100 dark:bg-blue-900/30"
            :href="route('admin.quotations.index', ['date_range' => 'month'])"
        />
        
        <!-- Total Quotations -->
        <x-admin.stat-card 
            title="Total Quotations" 
            :value="$statusCounts['total'] ?? 0"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />'
            iconColor="text-gray-600 dark:text-gray-400" 
            iconBg="bg-gray-100 dark:bg-gray-700"
            :href="route('admin.quotations.index')"
        />
    </div>

    <!-- Filters -->
    <x-admin.filter action="{{ route('admin.quotations.index') }}" method="GET" :resetRoute="route('admin.quotations.index')">
        <x-admin.input name="search" label="Search" placeholder="Search by name, email, company or project type"
            value="{{ request('search') }}" />        

        <x-admin.select name="status" label="Status" value="{{ request('status') }}"
            :options="[
                '' => 'All Statuses',
                'pending' => 'Pending Review',
                'reviewed' => 'Under Review',
                'approved' => 'Approved',
                'rejected' => 'Rejected'
            ]" />

        <x-admin.select name="service" label="Service" value="{{ request('service') }}"
            :options="['' => 'All Services'] + $services->pluck('title', 'id')->toArray()" />

        <x-admin.date-range-picker name="date_range" label="Date Range" startName="created_from" endName="created_to"
            :startDate="request('created_from')" :endDate="request('created_to')" placeholder="Select date range" />
    </x-admin.filter>

    <!-- Quotations List -->
    <x-admin.card>
        <x-slot name="headerActions">
            <div class="flex items-center justify-between w-full px-4 py-2">
                <!-- Left side: Action buttons -->
                <div class="flex items-center space-x-3">
                    @if ($quotations->count() > 0)
                        <form action="{{ route('admin.quotations.bulk-action') }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="action" value="approve">
                            <input type="hidden" name="quotation_ids" id="approve-selected-ids" value="">
                            <x-admin.button type="button" color="success" size="sm" onclick="confirmBulkAction('approve')" class="flex items-center justify-center">
                                <div class="flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Approve Selected
                                </div>
                            </x-admin.button>
                        </form>
                        
                        <form action="{{ route('admin.quotations.bulk-action') }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="action" value="reject">
                            <input type="hidden" name="quotation_ids" id="reject-selected-ids" value="">
                            <x-admin.button type="button" color="warning" size="sm" onclick="confirmBulkAction('reject')" class="flex items-center justify-center">
                                <div class="flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Reject Selected
                                </div>
                            </x-admin.button>
                        </form>

                        <form action="{{ route('admin.quotations.bulk-action') }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="quotation_ids" id="delete-selected-ids" value="">
                            <x-admin.button type="button" color="danger" size="sm" onclick="confirmBulkAction('delete')" class="flex items-center justify-center">
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
                
                <!-- Right side: Pagination-style quotation count -->
                <div class="flex items-center space-x-4">
                    @if ($quotations->count() > 0)
                        <!-- Quotation count info (pagination style) -->
                        <div class="text-sm text-gray-700 dark:text-neutral-400">
                            Showing <span class="font-medium text-gray-900 dark:text-white">{{ $quotations->firstItem() }}</span> to <span class="font-medium text-gray-900 dark:text-white">{{ $quotations->lastItem() }}</span> of <span class="font-medium text-gray-900 dark:text-white">{{ $quotations->total() }}</span> quotations
                        </div>
                        
                        <!-- Optional: Add a divider -->
                        <div class="h-5 w-px bg-gray-300 dark:bg-neutral-600"></div>
                        
                        <!-- Optional: Quick filter status -->
                        @if (request()->hasAny(['search', 'status', 'service', 'created_from', 'created_to', 'date_range']))
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                <span class="text-sm text-blue-600 dark:text-blue-400 font-medium">Filtered</span>
                            </div>
                        @endif

                        <!-- Export Button -->
                        <div class="h-5 w-px bg-gray-300 dark:bg-neutral-600"></div>
                        <a href="{{ route('admin.quotations.export', request()->query()) }}" class="inline-flex items-center text-sm text-gray-600 dark:text-neutral-400 hover:text-gray-900 dark:hover:text-white">
                            <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export CSV
                        </a>
                    @else
                        <span class="text-sm text-gray-500 dark:text-neutral-500">No quotations found</span>
                    @endif
                </div>
            </div>
        </x-slot>

        @if ($quotations->count() > 0)
            <x-admin.data-table checkbox="true">
                <x-slot name="columns">
                    <x-admin.table-column width="w-80">Client</x-admin.table-column>
                    <x-admin.table-column width="w-96">Project Details</x-admin.table-column>
                    <x-admin.table-column width="w-32">Status</x-admin.table-column>
                    <x-admin.table-column sortable="true" field="created_at" width="w-40"
                        direction="{{ request('sort') === 'created_at' ? request('direction', 'desc') : 'desc' }}">Received</x-admin.table-column>
                    <x-admin.table-column width="w-32">Actions</x-admin.table-column>
                </x-slot>

                @foreach ($quotations as $quotation)
                    @php
                        // Determine priority level for minimal indicators
                        $priorityDot = '';
                        $priorityBadge = '';
                        
                        if ($quotation->priority === 'urgent' && $quotation->status === 'pending') {
                            $priorityDot = 'bg-red-500';
                            $priorityBadge = 'Urgent';
                        } elseif ($quotation->priority === 'high' && $quotation->status === 'pending') {
                            $priorityDot = 'bg-orange-500';
                            $priorityBadge = 'High Priority';
                        } elseif ($quotation->status === 'pending' && $quotation->created_at->diffInDays() > 3) {
                            $priorityDot = 'bg-amber-500';
                            $priorityBadge = 'Overdue';
                        }
                    @endphp
                    
                    <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700 cursor-pointer"
                        onclick="window.location='{{ route('admin.quotations.show', $quotation) }}'">
                        
                        <x-admin.table-cell>
                            <input type="checkbox" name="quotation_ids[]" value="{{ $quotation->id }}"
                                class="quotation-checkbox shrink-0 border-gray-300 rounded text-blue-600 focus:ring-blue-500 checked:border-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-neutral-800"
                                onclick="event.stopPropagation()">
                        </x-admin.table-cell>

                        <x-admin.table-cell :highlight="$quotation->status === 'pending'">
                            <div class="flex items-center">
                                <!-- Minimal Priority Dot Indicator -->
                                @if($priorityDot)
                                    <div class="w-2 h-2 rounded-full {{ $priorityDot }} mr-3 flex-shrink-0"></div>
                                @endif
                                
                                <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-full {{ $quotation->status === 'pending' ? 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-gray-100 text-gray-600 dark:bg-neutral-700 dark:text-neutral-400' }}">
                                    @if ($quotation->service)
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="ml-4 min-w-0 flex-1">
                                    <div class="flex items-center">
                                        <span class="text-sm font-medium {{ $quotation->status === 'pending' ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-neutral-400' }} truncate">
                                            {{ $quotation->name }}
                                        </span>
                                        @if($priorityBadge)
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ 
                                                $priorityDot === 'bg-red-500' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : 
                                                ($priorityDot === 'bg-orange-500' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400') 
                                            }}">
                                                {{ $priorityBadge }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-sm {{ $quotation->status === 'pending' ? 'text-gray-700 dark:text-neutral-300' : 'text-gray-500 dark:text-neutral-500' }} truncate">
                                        {{ $quotation->email }}
                                    </div>
                                    @if($quotation->company)
                                        <div class="text-xs text-gray-400 dark:text-neutral-500 truncate">
                                            {{ $quotation->company }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </x-admin.table-cell>

                        <x-admin.table-cell>
                            <div class="min-w-0">
                                <span class="text-sm {{ $quotation->status === 'pending' ? 'font-semibold text-gray-900 dark:text-white' : 'text-gray-600 dark:text-neutral-400' }} block truncate">
                                    {{ $quotation->project_type ?: 'General Inquiry' }}
                                </span>
                                @if($quotation->service)
                                    <div class="text-xs text-gray-500 dark:text-neutral-500 truncate mt-1">
                                        Service: {{ $quotation->service->title }}
                                    </div>
                                @endif
                                @if($quotation->budget_range)
                                    <div class="text-xs text-gray-500 dark:text-neutral-500 truncate">
                                        Budget: {{ $quotation->budget_range }}
                                    </div>
                                @endif
                            </div>
                        </x-admin.table-cell>

                        <x-admin.table-cell>
                            <div class="flex flex-col space-y-1">
                                <x-admin.badge 
                                    :type="match($quotation->status) {
                                        'pending' => 'warning',
                                        'reviewed' => 'info',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        default => 'default'
                                    }"
                                    size="sm"
                                >
                                    {{ ucfirst($quotation->status) }}
                                </x-admin.badge>
                                
                                @if($quotation->client_approved === true)
                                    <x-admin.badge type="success" size="sm">
                                        Client Approved
                                    </x-admin.badge>
                                @elseif($quotation->client_approved === false)
                                    <x-admin.badge type="danger" size="sm">
                                        Client Declined
                                    </x-admin.badge>
                                @endif

                                @if($quotation->priority && $quotation->priority !== 'normal')
                                    <x-admin.badge 
                                        :type="match($quotation->priority) {
                                            'urgent' => 'danger',
                                            'high' => 'warning',
                                            'low' => 'gray',
                                            default => 'default'
                                        }"
                                        size="sm"
                                    >
                                        {{ ucfirst($quotation->priority) }}
                                    </x-admin.badge>
                                @endif
                            </div>
                        </x-admin.table-cell>

                        <x-admin.table-cell>
                            <div class="text-sm text-gray-600 dark:text-neutral-400" title="{{ $quotation->created_at }}">
                                {{ $quotation->created_at->diffForHumans() }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-neutral-500">
                                {{ $quotation->created_at->format('M d, H:i') }}
                            </div>
                        </x-admin.table-cell>

                        <x-admin.table-cell>
                            <div class="flex items-center space-x-2" onclick="event.stopPropagation()">
                                <x-admin.icon-button 
                                    href="{{ route('admin.quotations.show', $quotation) }}"
                                    tooltip="View Details"
                                    color="primary"
                                    size="sm"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </x-admin.icon-button>

                                @if($quotation->status === 'pending')
                                    <form action="{{ route('admin.quotations.update-status', $quotation) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <x-admin.icon-button 
                                            type="submit"
                                            tooltip="Quick Approve"
                                            color="success"
                                            size="sm"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </x-admin.icon-button>
                                    </form>

                                    <form action="{{ route('admin.quotations.update-status', $quotation) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <x-admin.icon-button 
                                            type="submit"
                                            tooltip="Quick Reject"
                                            color="danger"
                                            size="sm"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </x-admin.icon-button>
                                    </form>
                                @endif

                                <form action="{{ route('admin.quotations.destroy', $quotation) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Are you sure you want to delete this quotation?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-admin.icon-button 
                                        type="submit"
                                        tooltip="Delete Quotation"
                                        color="danger"
                                        size="sm"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </x-admin.icon-button>
                                </form>
                            </div>
                        </x-admin.table-cell>
                    </tr>
                @endforeach
            </x-admin.data-table>

            <div class="px-6 py-4 border-t border-gray-200 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-800/50">
                {{ $quotations->withQueryString()->links() }}
            </div>
        @else
            <x-admin.empty-state title="No quotations found" description="There are no quotation requests matching your criteria."
                icon='<svg class="w-10 h-10 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>' />
        @endif
    </x-admin.card>
</x-layouts.admin>

<script>
    // Handle checkbox selection for bulk actions
    document.addEventListener('DOMContentLoaded', function() {
        const mainCheckbox = document.getElementById('hs-at-with-checkboxes-main');
        const quotationCheckboxes = document.querySelectorAll('.quotation-checkbox');

        if (mainCheckbox) {
            mainCheckbox.addEventListener('change', function() {
                quotationCheckboxes.forEach(checkbox => {
                    checkbox.checked = mainCheckbox.checked;
                });
            });
        }

        // Update main checkbox state based on individual checkboxes
        quotationCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allChecked = Array.from(quotationCheckboxes).every(c => c.checked);
                const someChecked = Array.from(quotationCheckboxes).some(c => c.checked);

                if (mainCheckbox) {
                    mainCheckbox.checked = allChecked;
                    mainCheckbox.indeterminate = someChecked && !allChecked;
                }
            });
        });
    });

    // Function to confirm and submit bulk actions
    function confirmBulkAction(action) {
        const selectedCheckboxes = document.querySelectorAll('.quotation-checkbox:checked');
        const selectedIds = Array.from(selectedCheckboxes).map(checkbox => checkbox.value);

        if (selectedIds.length === 0) {
            alert('Please select at least one quotation.');
            return;
        }

        let confirmMessage = '';
        let form = null;

        switch(action) {
            case 'approve':
                confirmMessage = `Are you sure you want to approve ${selectedIds.length} selected quotation(s)?`;
                form = document.querySelector('form input[value="approve"]').closest('form');
                document.getElementById('approve-selected-ids').value = selectedIds.join(',');
                break;
            case 'reject':
                confirmMessage = `Are you sure you want to reject ${selectedIds.length} selected quotation(s)?`;
                form = document.querySelector('form input[value="reject"]').closest('form');
                document.getElementById('reject-selected-ids').value = selectedIds.join(',');
                break;
            case 'delete':
                confirmMessage = `Are you sure you want to delete ${selectedIds.length} selected quotation(s)? This action cannot be undone.`;
                form = document.querySelector('form input[value="delete"]').closest('form');
                document.getElementById('delete-selected-ids').value = selectedIds.join(',');
                break;
        }

        if (confirm(confirmMessage)) {
            form.submit();
        }
    }
</script>