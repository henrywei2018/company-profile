<x-layouts.client>
    <x-slot name="title">Penawaran Saya</x-slot>
    <x-slot name="description">View and manage your quotation requests and their status.</x-slot>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Quotations -->
        <x-admin.stat-card 
            title="Total Penawaran" 
            :value="$quotations->total()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />'
            iconColor="text-blue-600 dark:text-blue-400" 
            iconBg="bg-blue-100 dark:bg-blue-900/30"
        />
        
        <!-- Pending -->
        <x-admin.stat-card 
            title="Tertunda Review" 
            :value="$quotations->where('status', 'pending')->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />'
            iconColor="text-yellow-600 dark:text-yellow-400" 
            iconBg="bg-yellow-100 dark:bg-yellow-900/30"
            :href="route('client.quotations.index', ['status' => 'pending'])"
        />
        
        <!-- Approved -->
        <x-admin.stat-card 
            title="Disetujui" 
            :value="$quotations->where('status', 'approved')->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />'
            iconColor="text-green-600 dark:text-green-400" 
            iconBg="bg-green-100 dark:bg-green-900/30"
            :href="route('client.quotations.index', ['status' => 'approved'])"
        />
        
        <!-- This Month -->
        <x-admin.stat-card 
            title="Bulan Ini" 
            :value="$quotations->where('created_at', '>=', now()->startOfMonth())->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />'
            iconColor="text-indigo-600 dark:text-indigo-400" 
            iconBg="bg-indigo-100 dark:bg-indigo-900/30"
        />
    </div>

    <!-- Filter Section -->
    <x-admin.filter-section  
        :action="route('client.quotations.index')"
        :searchValue="request('search')"
        searchPlaceholder="Search quotations by project type, location, or requirements..."
        :hasActiveFilters="request()->hasAny(['search', 'status', 'service', 'priority'])"
        :clearFiltersRoute="route('client.quotations.index')"
        :filters="[
            [
                'name' => 'status',
                'label' => 'Status',
                'allLabel' => 'Semua Status',
                'options' => [
                    'pending' => 'Tunda',
                    'reviewed' => 'Direviu',
                    'approved' => 'Disetujui',
                    'rejected' => 'Ditolak'
                ]
            ],
            [
                'name' => 'priority',
                'label' => 'Prioritas',
                'allLabel' => 'Semua Prioritas',
                'options' => [
                    'low' => 'Rendah',
                    'normal' => 'Normal',
                    'high' => 'Tinggi',
                    'urgent' => 'Mendesak'
                ]
            ],
            [
                'name' => 'service',
                'label' => 'Layanan',
                'allLabel' => 'Semua Layanan',
                'options' => $services->pluck('name', 'id')->toArray()
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
            <!-- Table Header Aksi -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        Quotations List
                    </h3>
                    
                    <div class="flex items-center gap-3">
                        <!-- Create New button -->
                        <x-admin.button href="{{ route('client.quotations.create') }}" color="primary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Penawaran Baru
                        </x-admin.button>

                        <!-- Pagination Info -->
                        <div class="text-sm text-gray-700 dark:text-neutral-400">
                            Menampilkan <span class="font-medium text-gray-900 dark:text-white">{{ $quotations->firstItem() }}</span> 
                            sampai <span class="font-medium text-gray-900 dark:text-white">{{ $quotations->lastItem() }}</span> 
                            dari <span class="font-medium text-gray-900 dark:text-white">{{ $quotations->total() }}</span> penawaran
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                    <thead class="bg-gray-50 dark:bg-neutral-800">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                Informasi Penawaran
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                Layanan
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                Prioritas
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                dibuat
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                Aksi
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
                                            <div class="text-sm text-gray-500 dark:text-neutral-400">
                                                #{{ $quotation->quotation_number }}
                                            </div>
                                            @if($quotation->location)
                                                <div class="text-xs text-gray-400 dark:text-neutral-500">
                                                    📍 {{ $quotation->location }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Service -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($quotation->service)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                            {{ $quotation->service->name }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-500 dark:text-neutral-400">General</span>
                                    @endif
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-admin.status-badge :status="$quotation->status" />
                                </td>

                                <!-- Priority -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-admin.badge :priority="$quotation->priority" />
                                </td>

                                <!-- Created -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-400">
                                    <div>{{ $quotation->created_at->format('M j, Y') }}</div>
                                    <div class="text-xs">{{ $quotation->created_at->diffForHumans() }}</div>
                                </td>

                                <!-- Aksi -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <!-- View -->
                                        <x-admin.button 
                                            href="{{ route('client.quotations.show', $quotation) }}" 
                                            size="sm" 
                                            color="light"
                                            title="Lihat Detail"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </x-admin.button>

                                        <!-- Edit (if editable) -->
                                        @if(in_array($quotation->status, ['pending', 'reviewed']))
                                            <x-admin.button 
                                                href="{{ route('client.quotations.edit', $quotation) }}" 
                                                size="sm" 
                                                color="gray"
                                                title="Edit Penawaran"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </x-admin.button>
                                        @endif

                                        <!-- Dropdown menu -->
                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                </svg>
                                            </button>

                                            <div x-show="open" @click.away="open = false" 
                                                 class="absolute right-0 mt-2 w-48 bg-white dark:bg-neutral-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-10">
                                                <div class="py-1">
                                                    <!-- Duplicate -->
                                                    <form method="POST" action="{{ route('client.quotations.duplicate', $quotation) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="block w-full px-4 py-2 text-sm text-gray-700 dark:text-neutral-300 hover:bg-gray-100 dark:hover:bg-neutral-700 text-left">
                                                            Duplicate
                                                        </button>
                                                    </form>

                                                    <!-- Cetak -->
                                                    <a href="{{ route('client.quotations.print', $quotation) }}" 
                                                       target="_blank"
                                                       class="block px-4 py-2 text-sm text-gray-700 dark:text-neutral-300 hover:bg-gray-100 dark:hover:bg-neutral-700">
                                                        Cetak
                                                    </a>

                                                    @if($quotation->status === 'pending')
                                                        <!-- Batal -->
                                                        <form method="POST" action="{{ route('client.quotations.cancel', $quotation) }}" 
                                                              class="inline"
                                                              onsubmit="return confirm('Apakah Anda yakin ingin membatalkan penawaran ini?')">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="block w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-neutral-700 text-left">
                                                                Batal
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-neutral-700">
                {{ $quotations->appends(request()->query())->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No quotations found</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">
                    @if(request()->hasAny(['search', 'status', 'service', 'priority']))
                        Try adjusting your search or filter criteria.
                    @else
                        Get started by creating your first quotation request.
                    @endif
                </p>
                <div class="mt-6">
                    @if(request()->hasAny(['search', 'status', 'service', 'priority']))
                        <x-admin.button href="{{ route('client.quotations.index') }}" color="light">
                            Clear Filters
                        </x-admin.button>
                    @else
                        <x-admin.button href="{{ route('client.quotations.create') }}" color="primary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Create First Quotation
                        </x-admin.button>
                    @endif
                </div>
            </div>
        @endif
    </x-admin.card>
</x-layouts.client>