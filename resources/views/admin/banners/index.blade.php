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
                <input type="text" 
                       name="search" 
                       id="search"
                       value="{{ request('search') }}"
                       placeholder="Search by title, subtitle, or description..."
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
            </div>

            <!-- Category Filter -->
            <div class="w-full sm:w-48">
                <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                    Category
                </label>
                <select name="category" 
                        id="category"
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
                <select name="status" 
                        id="status"
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

    <!-- Banners Table -->
    <x-admin.card>
        @if($banners->count() > 0)
            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
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
                                    <div class="flex items-start space-x-4">
                                        @if($banner->image)
                                            <img src="{{ $banner->imageUrl }}" 
                                                 alt="{{ $banner->title }}" 
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
</x-layouts.admin>