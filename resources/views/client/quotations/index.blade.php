{{-- resources/views/client/quotations/index.blade.php --}}
<x-layouts.client title="My Quotations" 
    :unreadMessagesCount="$unreadMessagesCount ?? 0" 
    :pendingApprovalsCount="$pendingApprovalsCount ?? 0">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Quotations</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">
                Track and manage your quotation requests
            </p>
        </div>
        
        <div class="flex items-center gap-3 mt-4 md:mt-0">
            <x-admin.button href="{{ route('client.quotations.create') }}" color="primary" size="sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                New Quotation Request
            </x-admin.button>
        </div>
    </div>

    <!-- Alerts Section -->
    @if(!empty($alerts))
        <div class="mb-6 space-y-4">
            @foreach($alerts as $alert)
                <x-admin.alert 
                    :type="$alert['type']" 
                    :title="$alert['title']" 
                    :message="$alert['message']"
                    :action="$alert['action'] ?? null"
                />
            @endforeach
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 mb-6">
        <!-- Total Quotations -->
        <x-admin.stat-card 
            title="Total Quotations" 
            :value="$statistics['total'] ?? 0"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z M4 5a2 2 0 012-2v1a1 1 0 002 0V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm2.5 5a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />'
            iconColor="text-blue-600 dark:text-blue-400" 
            iconBg="bg-blue-100 dark:bg-blue-900/30"
            :href="route('client.quotations.index')"
        />
        
        <!-- Pending Review -->
        <x-admin.stat-card 
            title="Pending Review" 
            :value="$statistics['pending'] ?? 0"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />'
            iconColor="text-amber-600 dark:text-amber-400" 
            iconBg="bg-amber-100 dark:bg-amber-900/30"
            :href="route('client.quotations.index', ['status' => 'pending'])"
        />
        
        <!-- Approved -->
        <x-admin.stat-card 
            title="Approved" 
            :value="$statistics['approved'] ?? 0"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'
            iconColor="text-green-600 dark:text-green-400" 
            iconBg="bg-green-100 dark:bg-green-900/30"
            :href="route('client.quotations.index', ['status' => 'approved'])"
        />
        
        <!-- Projects Created -->
        <x-admin.stat-card 
            title="Projects Created" 
            :value="$statistics['projects_created'] ?? 0"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />'
            iconColor="text-purple-600 dark:text-purple-400" 
            iconBg="bg-purple-100 dark:bg-purple-900/30"
            :href="route('client.quotations.index', ['project_created' => '1'])"
        />
        
        <!-- This Month -->
        <x-admin.stat-card 
            title="This Month" 
            :value="$statistics['this_month'] ?? 0"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />'
            iconColor="text-indigo-600 dark:text-indigo-400" 
            iconBg="bg-indigo-100 dark:bg-indigo-900/30"
            :href="route('client.quotations.index', ['date_range' => 'month'])"
        />
    </div>

    <!-- Filter Section -->
    <x-admin.filter-section 
        :action="route('client.quotations.index')"
        :searchValue="request('search')"
        searchPlaceholder="Search quotations by project type, location, or requirements..."
        :hasActiveFilters="request()->hasAny(['search', 'status', 'service', 'priority', 'project_created', 'client_approved'])"
        :clearFiltersRoute="route('client.quotations.index')"
        :filters="[
            [
                'name' => 'status',
                'label' => 'Status',
                'allLabel' => 'All Statuses',
                'options' => $statuses
            ],
            [
                'name' => 'priority',
                'label' => 'Priority',
                'allLabel' => 'All Priorities',
                'options' => $priorities
            ],
            [
                'name' => 'client_approved',
                'label' => 'Client Response',
                'allLabel' => 'All Responses',
                'options' => [
                    '1' => 'Approved by Me',
                    '0' => 'Declined by Me'
                ]
            ]
        ]"
        :sortOptions="[
            'created_at' => 'Date Created',
            'updated_at' => 'Last Updated',
            'status' => 'Status',
            'priority' => 'Priority',
            'project_type' => 'Project Type',
            'start_date' => 'Start Date'
        ]"
    />

    <!-- Quotations Table -->
    <x-admin.card>
        @if($quotations->count() > 0)
            <!-- Table Header Actions -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        Quotations List
                    </h3>
                    
                    <!-- Pagination Info -->
                    <div class="text-sm text-gray-700 dark:text-neutral-400">
                        Showing <span class="font-medium text-gray-900 dark:text-white">{{ $quotations->firstItem() }}</span> 
                        to <span class="font-medium text-gray-900 dark:text-white">{{ $quotations->lastItem() }}</span> 
                        of <span class="font-medium text-gray-900 dark:text-white">{{ $quotations->total() }}</span> quotations
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                    <thead class="bg-gray-50 dark:bg-neutral-800">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                Project Details
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                Service
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                Priority
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                Created
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-neutral-800 divide-y divide-gray-200 dark:divide-neutral-700">
                        @foreach($quotations as $quotation)
                            <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700">
                                <!-- Project Details -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                <a href="{{ route('client.quotations.show', $quotation) }}" 
                                                   class="hover:text-blue-600 dark:hover:text-blue-400">
                                                    {{ $quotation->project_type }}
                                                </a>
                                            </div>
                                            @if($quotation->location)
                                                <div class="text-sm text-gray-500 dark:text-neutral-400 flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"></path>
                                                    </svg>
                                                    {{ $quotation->location }}
                                                </div>
                                            @endif
                                            @if($quotation->quotation_number)
                                                <div class="text-xs text-gray-400 dark:text-neutral-500">
                                                    #{{ $quotation->quotation_number }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Service -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $quotation->service ? $quotation->service->title : 'General' }}
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-admin.badge 
                                        :type="match($quotation->status) {
                                            'pending' => 'warning',
                                            'reviewed' => 'info',
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            default => 'default'
                                        }"
                                    >
                                        {{ $statuses[$quotation->status] ?? ucfirst($quotation->status) }}
                                    </x-admin.badge>
                                    
                                    @if($quotation->status === 'approved' && $quotation->client_approved === true)
                                        <div class="mt-1">
                                            <x-admin.badge type="success" size="sm">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path>
                                                </svg>
                                                Accepted
                                            </x-admin.badge>
                                        </div>
                                    @elseif($quotation->status === 'approved' && $quotation->client_approved === false)
                                        <div class="mt-1">
                                            <x-admin.badge type="danger" size="sm">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"></path>
                                                </svg>
                                                Declined
                                            </x-admin.badge>
                                        </div>
                                    @endif
                                </td>

                                <!-- Priority -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-admin.badge 
                                        :type="match($quotation->priority) {
                                            'low' => 'default',
                                            'normal' => 'info',
                                            'high' => 'warning',
                                            'urgent' => 'danger',
                                            default => 'default'
                                        }"
                                    >
                                        {{ $priorities[$quotation->priority] ?? ucfirst($quotation->priority) }}
                                    </x-admin.badge>
                                </td>

                                <!-- Created -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-400">
                                    <div>{{ $quotation->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs">{{ $quotation->created_at->format('g:i A') }}</div>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <!-- View Button -->
                                        <x-admin.button href="{{ route('client.quotations.show', $quotation) }}" color="light" size="xs">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </x-admin.button>

                                        <!-- Edit Button (if editable) -->
                                        @if(in_array($quotation->status, ['pending', 'reviewed']))
                                            <x-admin.button href="{{ route('client.quotations.edit', $quotation) }}" color="info" size="xs">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </x-admin.button>
                                        @endif

                                        <!-- Approve Button (if approved and not yet accepted) -->
                                        @if($quotation->status === 'approved' && $quotation->client_approved === null)
                                            <x-admin.button 
                                                href="{{ route('client.quotations.approve', $quotation) }}" 
                                                color="success" 
                                                size="xs"
                                                onclick="return confirm('Are you sure you want to accept this quotation?')"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </x-admin.button>
                                        @endif

                                        <!-- Duplicate Button -->
                                        <form action="{{ route('client.quotations.duplicate', $quotation) }}" method="POST" class="inline">
                                            @csrf
                                            <x-admin.button 
                                                type="submit" 
                                                color="light" 
                                                size="xs"
                                                onclick="return confirm('Create a copy of this quotation?')"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                </svg>
                                            </x-admin.button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-neutral-700">
                {{ $quotations->withQueryString()->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No quotations found</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">
                    @if(request()->hasAny(['search', 'status', 'service', 'priority']))
                        No quotations match your current filters.
                    @else
                        Get started by creating your first quotation request.
                    @endif
                </p>
                <div class="mt-6">
                    @if(request()->hasAny(['search', 'status', 'service', 'priority']))
                        <x-admin.button href="{{ route('client.quotations.index') }}" color="primary">
                            Clear filters
                        </x-admin.button>
                    @else
                        <x-admin.button href="{{ route('client.quotations.create') }}" color="primary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Create quotation request
                        </x-admin.button>
                    @endif
                </div>
            </div>
        @endif
    </x-admin.card>

    <!-- Recent Activities (if available) -->
    @if(isset($recentActivities) && $recentActivities->count() > 0)
        <x-admin.card class="mt-8">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Activity</h3>
            </div>
            <div class="px-6 py-4">
                <div class="flow-root">
                    <ul class="-mb-8">
                        @foreach($recentActivities as $activity)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                        <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200 dark:bg-neutral-600" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex items-start space-x-3">
                                        <div class="relative">
                                            <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white dark:ring-neutral-800">
                                                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 002 0V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm2.5 5a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div>
                                                <div class="text-sm">
                                                    <span class="font-medium text-gray-900 dark:text-white">{{ $activity['description'] ?? 'Activity' }}</span>
                                                </div>
                                                <p class="mt-0.5 text-sm text-gray-500 dark:text-neutral-400">
                                                    {{ $activity['created_at'] ? \Carbon\Carbon::parse($activity['created_at'])->diffForHumans() : '' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </x-admin.card>
    @endif
</x-layouts.client>