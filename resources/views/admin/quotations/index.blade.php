<x-layouts.admin title="Quotations Management" enableCharts="true">
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Quotation Requests</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Manage and track all quotation requests from potential clients
                </p>
            </div>
            <div class="mt-4 md:mt-0 flex items-center space-x-3">
                <x-admin.button href="{{ route('admin.quotations.export') }}" color="light" size="sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export CSV
                </x-admin.button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-admin.stat-card 
                title="Total Quotations" 
                :value="$stats['total']"
                icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' />"
                iconColor="text-blue-600"
                iconBg="bg-blue-100 dark:bg-blue-900/30"
            />
            
            <x-admin.stat-card 
                title="Pending Review" 
                :value="$stats['pending']"
                icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' />"
                iconColor="text-amber-600"
                iconBg="bg-amber-100 dark:bg-amber-900/30"
            />
            
            <x-admin.stat-card 
                title="Approved" 
                :value="$stats['approved']"
                icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' />"
                iconColor="text-green-600"
                iconBg="bg-green-100 dark:bg-green-900/30"
            />
            
            <x-admin.stat-card 
                title="This Month" 
                :value="$stats['this_month']"
                icon="<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' />"
                iconColor="text-purple-600"
                iconBg="bg-purple-100 dark:bg-purple-900/30"
            />
        </div>

        <!-- Filters -->
        <x-admin.filter action="{{ route('admin.quotations.index') }}" resetRoute="{{ route('admin.quotations.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-admin.input 
                    label="Search" 
                    name="search" 
                    :value="request('search')"
                    placeholder="Name, email, company..."
                />
                
                <x-admin.select 
                    label="Status" 
                    name="status" 
                    :value="request('status')"
                    :options="[
                        '' => 'All Statuses',
                        'pending' => 'Pending',
                        'reviewed' => 'Reviewed', 
                        'approved' => 'Approved',
                        'rejected' => 'Rejected'
                    ]"
                />
                
                <x-admin.select 
                    label="Service" 
                    name="service" 
                    :value="request('service')"
                    :options="['' => 'All Services'] + $services->pluck('title', 'id')->toArray()"
                />
                
                <x-admin.select 
                    label="Date Range" 
                    name="date_range" 
                    :value="request('date_range')"
                    :options="[
                        '' => 'All Time',
                        'today' => 'Today',
                        'week' => 'This Week',
                        'month' => 'This Month',
                        'quarter' => 'This Quarter',
                        'year' => 'This Year'
                    ]"
                />
            </div>
        </x-admin.filter>

        <!-- Quotations Table -->
        <x-admin.card>
            <x-slot name="title">Quotation Requests</x-slot>
            
            @if($quotations->count() > 0)
                <!-- Bulk Actions -->
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="select-all" class="text-sm text-gray-700 dark:text-gray-300">Select All</label>
                        
                        <select id="bulk-action" class="rounded-md border-gray-300 text-sm">
                            <option value="">Bulk Actions</option>
                            <option value="approve">Approve Selected</option>
                            <option value="reject">Reject Selected</option>
                            <option value="delete">Delete Selected</option>
                        </select>
                        
                        <x-admin.button id="apply-bulk" size="sm" color="light">Apply</x-admin.button>
                    </div>
                    
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Showing {{ $quotations->firstItem() }} to {{ $quotations->lastItem() }} of {{ $quotations->total() }} results
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    <input type="checkbox" id="select-all-header" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center space-x-1 hover:text-gray-700 dark:hover:text-gray-300">
                                        <span>Client</span>
                                        @if(request('sort') === 'name')
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                @if(request('direction') === 'asc')
                                                    <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                                                @else
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                @endif
                                            </svg>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Project Details
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center space-x-1 hover:text-gray-700 dark:hover:text-gray-300">
                                        <span>Received</span>
                                        @if(request('sort') === 'created_at' || !request('sort'))
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                @if(request('direction') === 'asc')
                                                    <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                                                @else
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                @endif
                                            </svg>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($quotations as $quotation)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 {{ $quotation->status === 'pending' ? 'bg-yellow-50 dark:bg-yellow-900/10' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" name="quotation_ids[]" value="{{ $quotation->id }}" class="quotation-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-start">
                                            <div class="ml-0">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $quotation->name }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $quotation->email }}
                                                </div>
                                                @if($quotation->company)
                                                    <div class="text-xs text-gray-400 dark:text-gray-500">
                                                        {{ $quotation->company }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $quotation->project_type ?: 'General Inquiry' }}
                                        </div>
                                        @if($quotation->service)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                Service: {{ $quotation->service->title }}
                                            </div>
                                        @endif
                                        @if($quotation->budget_range)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                Budget: {{ $quotation->budget_range }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col space-y-1">
                                            <x-admin.badge 
                                                :type="match($quotation->status) {
                                                    'pending' => 'warning',
                                                    'reviewed' => 'info',
                                                    'approved' => 'success',
                                                    'rejected' => 'danger',
                                                    default => 'default'
                                                }"
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
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <div>{{ $quotation->created_at->format('M d, Y') }}</div>
                                        <div class="text-xs">{{ $quotation->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <x-admin.dropdown>
                                                <x-slot name="trigger">
                                                    <x-admin.icon-button size="sm" color="light">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                        </svg>
                                                    </x-admin.icon-button>
                                                </x-slot>
                                                
                                                <x-admin.dropdown-item 
                                                    href="{{ route('admin.quotations.show', $quotation) }}"
                                                    icon="<svg class='w-4 h-4' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 12a3 3 0 11-6 0 3 3 0 016 0z' /><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' /></svg>"
                                                >
                                                    View Details
                                                </x-admin.dropdown-item>
                                                
                                                <x-admin.dropdown-item 
                                                    href="{{ route('admin.quotations.edit', $quotation) }}"
                                                    icon="<svg class='w-4 h-4' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' /></svg>"
                                                >
                                                    Edit
                                                </x-admin.dropdown-item>
                                                
                                                @if($quotation->status === 'pending')
                                                    <x-admin.dropdown-item 
                                                        type="form"
                                                        action="{{ route('admin.quotations.update-status', $quotation) }}"
                                                        method="POST"
                                                        icon="<svg class='w-4 h-4' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' /></svg>"
                                                    >
                                                        Approve
                                                        <input type="hidden" name="status" value="approved">
                                                    </x-admin.dropdown-item>
                                                    
                                                    <x-admin.dropdown-item 
                                                        type="form"
                                                        action="{{ route('admin.quotations.update-status', $quotation) }}"
                                                        method="POST"
                                                        icon="<svg class='w-4 h-4' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z' /></svg>"
                                                    >
                                                        Reject
                                                        <input type="hidden" name="status" value="rejected">
                                                    </x-admin.dropdown-item>
                                                @endif
                                                
                                                @if($quotation->status === 'approved')
                                                    <x-admin.dropdown-item 
                                                        href="{{ route('admin.quotations.create-project', $quotation) }}"
                                                        icon="<svg class='w-4 h-4' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 6v6m0 0v6m0-6h6m-6 0H6' /></svg>"
                                                    >
                                                        Create Project
                                                    </x-admin.dropdown-item>
                                                @endif
                                                
                                                <x-admin.dropdown-item 
                                                    href="{{ route('admin.quotations.duplicate', $quotation) }}"
                                                    icon="<svg class='w-4 h-4' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z' /></svg>"
                                                >
                                                    Duplicate
                                                </x-admin.dropdown-item>
                                                
                                                <x-admin.dropdown-item 
                                                    type="form"
                                                    action="{{ route('admin.quotations.destroy', $quotation) }}"
                                                    method="DELETE"
                                                    confirm="true"
                                                    confirmMessage="Are you sure you want to delete this quotation? This action cannot be undone."
                                                    icon="<svg class='w-4 h-4' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16' /></svg>"
                                                >
                                                    Delete
                                                </x-admin.dropdown-item>
                                            </x-admin.dropdown>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="mt-4">
                    {{ $quotations->appends(request()->query())->links('components.admin.pagination') }}
                </div>
            @else
                <x-admin.empty-state 
                    title="No quotations found"
                    description="No quotation requests match your current filters. Try adjusting your search criteria."
                    icon="<svg class='h-8 w-8 text-gray-400' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' /></svg>"
                />
            @endif
        </x-admin.card>
    </div>

    @push('scripts')
    <script>
        // Bulk actions functionality
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select-all');
            const selectAllHeader = document.getElementById('select-all-header');
            const checkboxes = document.querySelectorAll('.quotation-checkbox');
            const bulkActionSelect = document.getElementById('bulk-action');
            const applyBulkButton = document.getElementById('apply-bulk');
            
            // Select all functionality
            [selectAll, selectAllHeader].forEach(checkbox => {
                if (checkbox) {
                    checkbox.addEventListener('change', function() {
                        checkboxes.forEach(cb => cb.checked = this.checked);
                        [selectAll, selectAllHeader].forEach(cb => {
                            if (cb !== this) cb.checked = this.checked;
                        });
                    });
                }
            });
            
            // Apply bulk actions
            if (applyBulkButton) {
                applyBulkButton.addEventListener('click', function() {
                    const selectedIds = Array.from(document.querySelectorAll('.quotation-checkbox:checked')).map(cb => cb.value);
                    const action = bulkActionSelect.value;
                    
                    if (!action) {
                        alert('Please select an action');
                        return;
                    }
                    
                    if (selectedIds.length === 0) {
                        alert('Please select at least one quotation');
                        return;
                    }
                    
                    if (action === 'delete' && !confirm('Are you sure you want to delete the selected quotations?')) {
                        return;
                    }
                    
                    // Create form and submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("admin.quotations.bulk-action") }}';
                    
                    // CSRF token
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = '{{ csrf_token() }}';
                    form.appendChild(csrfInput);
                    
                    // Action
                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = action;
                    form.appendChild(actionInput);
                    
                    // Selected IDs
                    selectedIds.forEach(id => {
                        const idInput = document.createElement('input');
                        idInput.type = 'hidden';
                        idInput.name = 'quotation_ids[]';
                        idInput.value = id;
                        form.appendChild(idInput);
                    });
                    
                    document.body.appendChild(form);
                    form.submit();
                });
            }
        });
    </script>
    @endpush
</x-layouts.admin>