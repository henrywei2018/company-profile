<x-layouts.admin title="Banners Management">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Banners' => '']" />

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Banners Management</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Create and manage your website banners</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <!-- Statistics Button -->
            <button type="button" onclick="showStatistics()"
                class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Statistics
            </button>

            <!-- Export Button -->
            <button type="button" onclick="exportBanners()"
                class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export
            </button>

            <!-- Create Button -->
            <a href="{{ route('admin.banners.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create New Banner
            </a>
        </div>
    </div>

    <!-- Filters -->
    <x-admin.card class="mb-6">
        <form method="GET" action="{{ route('admin.banners.index') }}" class="space-y-4 sm:space-y-0 sm:flex sm:items-end sm:gap-4">
            <!-- Search -->
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                    Search
                </label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       placeholder="Search by title, subtitle, or description..."
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
            </div>

            <!-- Category Filter -->
            <div class="w-full sm:w-48">
                <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                    Category
                </label>
                <select name="category" id="category"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="w-full sm:w-48">
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                    Status
                </label>
                <select name="status" id="status"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </div>

            <!-- Filter Actions -->
            <div class="flex gap-2">
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Filter
                </button>

                @if(request()->hasAny(['search', 'category', 'status']))
                    <a href="{{ route('admin.banners.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </x-admin.card>

    <!-- Bulk Actions Form -->
    <form id="bulk-form" method="POST" action="{{ route('admin.banners.bulk-action') }}" class="hidden">
        @csrf
        <input type="hidden" name="action" id="bulk-action">
    </form>

    <!-- Banners Table -->
    <x-admin.card>
        @if($banners->count() > 0)
            <!-- Bulk Actions Bar -->
            <div id="bulk-actions" class="hidden mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <div class="flex items-center justify-between">
                    <span id="selected-count" class="text-sm font-medium text-blue-900 dark:text-blue-100">
                        0 banners selected
                    </span>
                    <div class="flex gap-2">
                        <button type="button" onclick="bulkAction('activate')" 
                                class="px-3 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-md hover:bg-green-200 dark:bg-green-800 dark:text-green-200">
                            Activate
                        </button>
                        <button type="button" onclick="bulkAction('deactivate')" 
                                class="px-3 py-1 text-xs font-medium text-yellow-700 bg-yellow-100 rounded-md hover:bg-yellow-200 dark:bg-yellow-800 dark:text-yellow-200">
                            Deactivate
                        </button>
                        <button type="button" onclick="bulkAction('delete')" 
                                class="px-3 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-md hover:bg-red-200 dark:bg-red-800 dark:text-red-200">
                            Delete
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th scope="col" class="w-8 px-6 py-3">
                                <input type="checkbox" id="select-all" 
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Banner
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Category
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Schedule
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Order
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($banners as $banner)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-6 py-4">
                                    <input type="checkbox" name="banner_ids[]" value="{{ $banner->id }}" 
                                           class="banner-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-start space-x-3">
                                        @if($banner->image)
                                            <img src="{{ $banner->imageUrl }}" alt="{{ $banner->title }}" 
                                                 class="w-20 h-12 object-cover rounded-lg flex-shrink-0">
                                        @else
                                            <div class="w-20 h-12 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('admin.banners.edit', $banner) }}" 
                                                   class="text-sm font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 truncate">
                                                    {{ $banner->title }}
                                                </a>
                                            </div>
                                            @if($banner->subtitle)
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                    {{ $banner->subtitle }}
                                                </p>
                                            @endif
                                            @if($banner->description)
                                                <p class="text-sm text-gray-500 dark:text-gray-500 mt-1 line-clamp-2">
                                                    {{ Str::limit($banner->description, 100) }}
                                                </p>
                                            @endif
                                            @if($banner->button_text && $banner->button_link)
                                                <div class="flex items-center gap-2 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                                        {{ $banner->button_text }}
                                                    </span>
                                                    @if($banner->open_in_new_tab)
                                                        <span class="text-xs">↗</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100">
                                        {{ $banner->category->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $now = now();
                                        $isActive = $banner->is_active;
                                        $isScheduled = $banner->start_date && $banner->start_date > $now;
                                        $isExpired = $banner->end_date && $banner->end_date < $now;
                                        $isLive = $isActive && !$isScheduled && !$isExpired;
                                    @endphp
                                    
                                    @if($isLive)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                            Live
                                        </span>
                                    @elseif($isScheduled)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                            Scheduled
                                        </span>
                                    @elseif($isExpired)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                            Expired
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
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
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                        {{ $banner->display_order }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Quick Actions -->
                                        <form method="POST" action="{{ route('admin.banners.toggle-status', $banner) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="text-{{ $banner->is_active ? 'red' : 'green' }}-600 hover:text-{{ $banner->is_active ? 'red' : 'green' }}-900"
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
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($banners->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $banners->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No banners found</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    @if(request()->hasAny(['search', 'category', 'status']))
                        Try adjusting your search criteria or filters.
                    @else
                        Get started by creating your first banner.
                    @endif
                </p>
                <div class="mt-6">
                    @if(request()->hasAny(['search', 'category', 'status']))
                        <a href="{{ route('admin.banners.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                            Clear filters
                        </a>
                    @else
                        <a href="{{ route('admin.banners.create') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Create your first banner
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </x-admin.card>

    <!-- Statistics Modal -->
    <div id="statistics-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Banner Statistics</h3>
                <button onclick="closeStatistics()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="statistics-content">
                <!-- Statistics content will be loaded here -->
                <div class="flex items-center justify-center py-8">
                    <svg class="animate-spin h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Bulk selection functionality
            const selectAllCheckbox = document.getElementById('select-all');
            const bannerCheckboxes = document.querySelectorAll('.banner-checkbox');
            const bulkActions = document.getElementById('bulk-actions');
            const selectedCount = document.getElementById('selected-count');
            
            // Select all functionality
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    const isChecked = this.checked;
                    bannerCheckboxes.forEach(checkbox => {
                        checkbox.checked = isChecked;
                    });
                    updateBulkActions();
                });
            }
            
            // Individual checkbox change
            bannerCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const checkedBoxes = document.querySelectorAll('.banner-checkbox:checked');
                    if (selectAllCheckbox) {
                        selectAllCheckbox.checked = checkedBoxes.length === bannerCheckboxes.length;
                        selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < bannerCheckboxes.length;
                    }
                    updateBulkActions();
                });
            });
            
            function updateBulkActions() {
                const checkedBoxes = document.querySelectorAll('.banner-checkbox:checked');
                const count = checkedBoxes.length;
                
                if (count > 0) {
                    bulkActions.classList.remove('hidden');
                    selectedCount.textContent = `${count} banner${count === 1 ? '' : 's'} selected`;
                } else {
                    bulkActions.classList.add('hidden');
                }
            }
        });
        
        // Bulk actions
        function bulkAction(action) {
    const checkedBoxes = document.querySelectorAll('.banner-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Please select at least one banner.');
        return;
    }

    const bannerIds = Array.from(checkedBoxes).map(cb => cb.value);

    let confirmMessage = '';
    switch(action) {
        case 'delete':
            confirmMessage = `Are you sure you want to delete ${bannerIds.length} banner(s)?`;
            break;
        case 'activate':
            confirmMessage = `Are you sure you want to activate ${bannerIds.length} banner(s)?`;
            break;
        case 'deactivate':
            confirmMessage = `Are you sure you want to deactivate ${bannerIds.length} banner(s)?`;
            break;
        default:
            confirmMessage = `Are you sure you want to ${action} ${bannerIds.length} banner(s)?`;
    }

    if (!confirm(confirmMessage)) {
        return;
    }

    // Set action input
    document.getElementById('bulk-action').value = action;

    // Clear existing banner_ids inputs
    const bulkForm = document.getElementById('bulk-form');
    const existingInputs = bulkForm.querySelectorAll('input[name="banner_ids[]"]');
    existingInputs.forEach(el => el.remove());

    // Add fresh banner_ids[] inputs
    bannerIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'banner_ids[]';
        input.value = id;
        bulkForm.appendChild(input);
    });

    bulkForm.submit();
}

        // Export functionality
        function exportBanners() {
            const params = new URLSearchParams(window.location.search);
            const exportUrl = '{{ route("admin.banners.export") }}?' + params.toString();
            window.open(exportUrl, '_blank');
        }
        
        // Statistics functionality
        function showStatistics() {
            document.getElementById('statistics-modal').classList.remove('hidden');
            loadStatistics();
        }
        
        function closeStatistics() {
            document.getElementById('statistics-modal').classList.add('hidden');
        }
        
        function loadStatistics() {
            fetch('{{ route("admin.banners.statistics") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderStatistics(data.data);
                    } else {
                        document.getElementById('statistics-content').innerHTML = 
                            '<div class="text-center py-8 text-red-600">Failed to load statistics</div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading statistics:', error);
                    document.getElementById('statistics-content').innerHTML = 
                        '<div class="text-center py-8 text-red-600">Failed to load statistics</div>';
                });
        }
        
        function renderStatistics(stats) {
            const content = `
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">${stats.total_banners}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Total Banners</div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">${stats.active_banners}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Active</div>
                    </div>
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">${stats.scheduled_banners}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Scheduled</div>
                    </div>
                    <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">${stats.expired_banners}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Expired</div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Recent Banners</h4>
                        <div class="space-y-2">
                            ${stats.recent_banners ? stats.recent_banners.map(banner => `
                                <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                    <div>
                                        <div class="font-medium text-sm">${banner.title}</div>
                                        <div class="text-xs text-gray-500">${banner.category} • ${banner.created_at}</div>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded ${banner.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">${banner.status}</span>
                                </div>
                            `).join('') : '<div class="text-sm text-gray-500">No recent banners</div>'}
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Categories</h4>
                        <div class="space-y-2">
                            ${stats.popular_categories ? stats.popular_categories.map(category => `
                                <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                    <div class="font-medium text-sm">${category.name}</div>
                                    <span class="text-xs text-gray-500">${category.banners_count} banners</span>
                                </div>
                            `).join('') : '<div class="text-sm text-gray-500">No categories</div>'}
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded">
                        <span class="font-medium">Active Categories:</span> ${stats.active_categories_count || 0}
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded">
                        <span class="font-medium">Inactive Banners:</span> ${stats.inactive_banners || 0}
                    </div>
                </div>
            `;
            
            document.getElementById('statistics-content').innerHTML = content;
        }
        
        // Close modal when clicking outside
        document.getElementById('statistics-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeStatistics();
            }
        });

        // Show notification function
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full shadow-lg rounded-lg p-4 ${getNotificationClasses(type)} transform transition-all duration-300 ease-in-out`;
            notification.innerHTML = `
                <div class="flex">
                    <div class="flex-shrink-0">
                        ${getNotificationIcon(type)}
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium">${message}</p>
                    </div>
                    <div class="ml-auto pl-3">
                        <button onclick="this.closest('.fixed').remove()" class="inline-flex text-current hover:opacity-75">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            `;

            document.body.appendChild(notification);
            setTimeout(() => notification?.remove(), 5000);
        }

        function getNotificationClasses(type) {
            const classes = {
                success: 'bg-green-50 border border-green-200 text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-400',
                error: 'bg-red-50 border border-red-200 text-red-800 dark:bg-red-900/20 dark:border-red-800 dark:text-red-400',
                warning: 'bg-yellow-50 border border-yellow-200 text-yellow-800 dark:bg-yellow-900/20 dark:border-yellow-800 dark:text-yellow-400',
                info: 'bg-blue-50 border border-blue-200 text-blue-800 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-400'
            };
            return classes[type] || classes.info;
        }

        function getNotificationIcon(type) {
            const icons = {
                success: '<svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
                error: '<svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
                warning: '<svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>',
                info: '<svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>'
            };
            return icons[type] || icons.info;
        }
    </script>
    @endpush
</x-layouts.admin>