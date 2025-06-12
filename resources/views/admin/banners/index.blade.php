{{-- resources/views/admin/banners/index.blade.php --}}
<x-layouts.admin title="Banners Management">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Banners' => '']" />

    <!-- Header Section -->
    <x-admin.header-section 
        title="Banners Management" 
        description="Create and manage your website banners"
        :createRoute="route('admin.banners.create')"
        createText="Create New Banner">
        
        <x-slot name="additionalActions">
            <!-- Statistics Button -->
            <button type="button" 
                    onclick="showStatistics()"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Statistics
            </button>

            <!-- Export Button -->
            <button type="button" 
                    onclick="exportBanners()"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export
            </button>
        </x-slot>
    </x-admin.header-section>

    <!-- Filter Section -->
    <x-admin.filter-section 
        :action="route('admin.banners.index')"
        :searchValue="request('search')"
        searchPlaceholder="Search by title, subtitle, or description..."
        :hasActiveFilters="request()->hasAny(['search', 'category', 'status'])"
        :clearFiltersRoute="route('admin.banners.index')"
        :filters="[
            [
                'name' => 'category',
                'label' => 'Category',
                'allLabel' => 'All Categories',
                'options' => $categories->pluck('name', 'id')->toArray()
            ],
            [
                'name' => 'status', 
                'label' => 'Status',
                'allLabel' => 'All Status',
                'options' => [
                    'active' => 'Active',
                    'inactive' => 'Inactive', 
                    'scheduled' => 'Scheduled',
                    'expired' => 'Expired'
                ]
            ]
        ]" />

    <!-- Bulk Actions -->
    <x-admin.bulk-actions 
        formId="bulk-form"
        :actionRoute="route('admin.banners.bulk-action')"
        selectedCountText="banners selected"
        :actions="[
            [
                'value' => 'activate',
                'label' => 'Activate',
                'bgColor' => 'bg-green-100',
                'textColor' => 'text-green-700',
                'hoverColor' => 'bg-green-200'
            ],
            [
                'value' => 'deactivate', 
                'label' => 'Deactivate',
                'bgColor' => 'bg-yellow-100',
                'textColor' => 'text-yellow-700',
                'hoverColor' => 'bg-yellow-200'
            ],
            [
                'value' => 'delete',
                'label' => 'Delete', 
                'bgColor' => 'bg-red-100',
                'textColor' => 'text-red-700',
                'hoverColor' => 'bg-red-200'
            ]
        ]" />

    <!-- Data Table -->
    <x-admin.new.data-table 
        :items="$banners"
        emptyTitle="No banners found"
        emptyDescription="Get started by creating your first banner."
        emptyActionText="Create your first banner"
        :emptyActionRoute="route('admin.banners.create')"
        :hasActiveFilters="request()->hasAny(['search', 'category', 'status'])"
        :clearFiltersRoute="route('admin.banners.index')"
        :headers="[
            ['label' => 'Banner'],
            ['label' => 'Category'], 
            ['label' => 'Status'],
            ['label' => 'Schedule'],
            ['label' => 'Order']
        ]">

        <x-slot name="emptyIcon">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </x-slot>

        @foreach($banners as $banner)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                <td class="px-6 py-4">
                    <input type="checkbox" name="banner_ids[]" value="{{ $banner->id }}" 
                           class="item-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </td>
                
                <!-- Banner Info -->
                <td class="px-6 py-4">
                    <div class="flex items-start space-x-3">
                        <x-admin.media-preview 
                            :src="$banner->image ? $banner->imageUrl : null"
                            :alt="$banner->title"
                            width="w-20"
                            height="h-12" />
                        
                        <x-admin.content-summary 
                            :title="$banner->title"
                            :subtitle="$banner->subtitle"
                            :description="$banner->description"
                            :link="route('admin.banners.edit', $banner)"
                            :linkText="$banner->button_text && $banner->button_link ? $banner->button_text : null"
                            :meta="[$banner->created_at->format('M d, Y')]" />
                    </div>
                </td>
                
                <!-- Category -->
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100">
                        {{ $banner->category->name }}
                    </span>
                </td>
                
                <!-- Status -->
                <td class="px-6 py-4 whitespace-nowrap">
                    @php
                        $now = now();
                        $isActive = $banner->is_active;
                        $isScheduled = $banner->start_date && $banner->start_date > $now;
                        $isExpired = $banner->end_date && $banner->end_date < $now;
                        $isLive = $isActive && !$isScheduled && !$isExpired;
                        
                        if ($isLive) {
                            $status = 'live';
                        } elseif ($isScheduled) {
                            $status = 'scheduled';
                        } elseif ($isExpired) {
                            $status = 'expired';
                        } else {
                            $status = 'inactive';
                        }
                    @endphp
                    
                    <x-admin.status-badge :status="$status" />
                </td>
                
                <!-- Schedule -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    @if($banner->start_date || $banner->end_date)
                        <div>
                            @if($banner->start_date)
                                <div>Start: {{ $banner->start_date->format('M d, Y') }}</div>
                            @endif
                            @if($banner->end_date)
                                <div>End: {{ $banner->end_date->format('M d, Y') }}</div>
                            @endif
                        </div>
                    @else
                        <span class="text-gray-400 italic">No schedule</span>
                    @endif
                </td>
                
                <!-- Order -->
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                        {{ $banner->display_order }}
                    </span>
                </td>
                
                <!-- Actions -->
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex items-center justify-end gap-2">
                        <!-- Quick Actions -->
                        <form method="POST" action="{{ route('admin.banners.toggle-status', $banner) }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="{{ $banner->is_active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' }}"
                                    title="{{ $banner->is_active ? 'Deactivate' : 'Activate' }}">
                                @if($banner->is_active)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @endif
                            </button>
                        </form>

                        <!-- Dropdown Menu -->
                        <div class="relative inline-block text-left" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false" 
                                 class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
                                <div class="py-1">
                                    <a href="{{ route('admin.banners.edit', $banner) }}" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.banners.duplicate', $banner) }}" class="inline w-full">
                                        @csrf
                                        <button type="submit" 
                                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-left">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                            Duplicate
                                        </button>
                                    </form>
                                    <div class="border-t border-gray-100 dark:border-gray-700"></div>
                                    <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" 
                                          onsubmit="return confirm('Are you sure you want to delete this banner?')" class="inline w-full">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 text-left">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-admin.new.data-table>

    <!-- Statistics Modal -->
    <x-admin.statistics-modal 
        modalId="statistics-modal"
        title="Banner Statistics"
        :statsEndpoint="route('admin.banners.statistics')" />

    @push('scripts')
    <script>
        // Override the default input name for banner bulk actions
        function getInputName(action) {
            return 'banner_ids[]';
        }

        // Export functionality
        function exportBanners() {
            const params = new URLSearchParams(window.location.search);
            const exportUrl = '{{ route("admin.banners.export") }}?' + params.toString();
            window.open(exportUrl, '_blank');
        }
    </script>
    @endpush
</x-layouts.admin>