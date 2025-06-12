{{-- resources/views/admin/team/index.blade.php --}}
<x-layouts.admin title="Team Management">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Team Management' => '']" />

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Team Management</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage your organization's team members</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
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
                    onclick="exportTeamMembers()"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export
            </button>

            <!-- Create Button -->
            <a href="{{ route('admin.team.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Team Member
            </a>
        </div>
    </div>

    <!-- Filters -->
    <x-admin.card class="mb-6">
        <form method="GET" action="{{ route('admin.team.index') }}" class="space-y-4 sm:space-y-0 sm:flex sm:items-end sm:gap-4">
            <!-- Search -->
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                    Search
                </label>
                <input type="text" 
                       name="search" 
                       id="search"
                       value="{{ request('search') }}"
                       placeholder="Search by name, position, or department..."
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
            </div>

            <!-- Department Filter -->
            <div class="w-full sm:w-48">
                <label for="department" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                    Department
                </label>
                <select name="department" 
                        id="department"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->name }}" {{ request('department') === $dept->name ? 'selected' : '' }}>
                            {{ $dept->name }}
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
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <!-- Featured Filter -->
            <div class="w-full sm:w-48">
                <label for="featured" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                    Featured
                </label>
                <select name="featured" 
                        id="featured"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                    <option value="">All</option>
                    <option value="1" {{ request('featured') === '1' ? 'selected' : '' }}>Featured</option>
                    <option value="0" {{ request('featured') === '0' ? 'selected' : '' }}>Not Featured</option>
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

                @if(request()->hasAny(['search', 'department', 'status', 'featured']))
                    <a href="{{ route('admin.team.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </x-admin.card>

    <!-- Bulk Actions Form -->
    <form id="bulk-form" method="POST" action="{{ route('admin.team.bulk-action') }}" class="hidden">
        @csrf
        <input type="hidden" name="action" id="bulk-action">
        <input type="hidden" name="team_member_ids" id="bulk-team-member-ids">
    </form>

    <!-- Team Members Table -->
    <x-admin.card>
        @if($teamMembers->count() > 0)
            <!-- Bulk Actions Bar -->
            <div id="bulk-actions" class="hidden mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <div class="flex items-center justify-between">
                    <span id="selected-count" class="text-sm font-medium text-blue-900 dark:text-blue-100">
                        0 team members selected
                    </span>
                    <div class="flex gap-2">
                        <button type="button" onclick="bulkAction('activate')" class="px-3 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-md hover:bg-green-200 dark:bg-green-800 dark:text-green-200">
                            Activate
                        </button>
                        <button type="button" onclick="bulkAction('deactivate')" class="px-3 py-1 text-xs font-medium text-yellow-700 bg-yellow-100 rounded-md hover:bg-yellow-200 dark:bg-yellow-800 dark:text-yellow-200">
                            Deactivate
                        </button>
                        <button type="button" onclick="bulkAction('feature')" class="px-3 py-1 text-xs font-medium text-purple-700 bg-purple-100 rounded-md hover:bg-purple-200 dark:bg-purple-800 dark:text-purple-200">
                            Feature
                        </button>
                        <button type="button" onclick="bulkAction('unfeature')" class="px-3 py-1 text-xs font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200">
                            Unfeature
                        </button>
                        <button type="button" onclick="bulkAction('delete')" class="px-3 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-md hover:bg-red-200 dark:bg-red-800 dark:text-red-200">
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
                                <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Team Member
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Department
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Contact
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($teamMembers as $teamMember)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-6 py-4">
                                    <input type="checkbox" name="team_member_ids[]" value="{{ $teamMember->id }}" 
                                           class="team-member-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </td>
                                
                                <!-- Team Member Info -->
                                <td class="px-6 py-4">
                                    <div class="flex items-start space-x-3">
                                        @if($teamMember->photo)
                                            <img src="{{ $teamMember->photo_url }}" 
                                                 alt="{{ $teamMember->name }}" 
                                                 class="w-16 h-16 object-cover rounded-full flex-shrink-0">
                                        @else
                                            <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center flex-shrink-0">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('admin.team.edit', $teamMember) }}" 
                                                   class="text-sm font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 truncate">
                                                    {{ $teamMember->name }}
                                                </a>
                                                @if($teamMember->featured)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                        </svg>
                                                        Featured
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $teamMember->position }}</p>
                                            @if($teamMember->bio)
                                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">
                                                    {{ Str::limit(strip_tags($teamMember->bio), 100) }}
                                                </p>
                                            @endif
                                            <div class="flex items-center gap-4 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                <span>Added {{ $teamMember->created_at->format('M d, Y') }}</span>
                                                @if($teamMember->hasSocialLinks())
                                                    <span class="inline-flex items-center">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                                        </svg>
                                                        {{ count($teamMember->social_links) }} social links
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Department -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($teamMember->department)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100">
                                            {{ $teamMember->department->name }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400 italic">No department</span>
                                    @endif
                                </td>
                                
                                <!-- Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                                        @if($teamMember->is_active) bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                        @else bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100 @endif">
                                        {{ $teamMember->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                
                                <!-- Contact -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    @if($teamMember->email)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                            <a href="mailto:{{ $teamMember->email }}" class="hover:text-blue-600 dark:hover:text-blue-400 truncate">
                                                {{ $teamMember->email }}
                                            </a>
                                        </div>
                                    @endif
                                    @if($teamMember->phone)
                                        <div class="flex items-center mt-1">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                            <a href="tel:{{ $teamMember->phone }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                {{ $teamMember->phone }}
                                            </a>
                                        </div>
                                    @endif
                                    @if(!$teamMember->email && !$teamMember->phone)
                                        <span class="text-gray-400 italic">No contact info</span>
                                    @endif
                                </td>
                                
                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Quick Actions -->
                                        <form method="POST" action="{{ route('admin.team.toggle-active', $teamMember) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="{{ $teamMember->is_active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' }}"
                                                    title="{{ $teamMember->is_active ? 'Deactivate' : 'Activate' }}">
                                                @if($teamMember->is_active)
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

                                        <form method="POST" action="{{ route('admin.team.toggle-featured', $teamMember) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
                                                    title="{{ $teamMember->featured ? 'Remove from featured' : 'Make featured' }}">
                                                <svg class="w-4 h-4" fill="{{ $teamMember->featured ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                                </svg>
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
                                                    <a href="{{ route('admin.team.edit', $teamMember) }}" 
                                                       class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                        Edit
                                                    </a>
                                                    <a href="{{ route('admin.team.show', $teamMember) }}" 
                                                       class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                        View Details
                                                    </a>
                                                    <div class="border-t border-gray-100 dark:border-gray-700"></div>
                                                    <form method="POST" action="{{ route('admin.team.destroy', $teamMember) }}" 
                                                          onsubmit="return confirm('Are you sure you want to delete this team member?')" class="inline w-full">
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
            @if($teamMembers->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $teamMembers->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No team members found</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    @if(request()->hasAny(['search', 'department', 'status', 'featured']))
                        Try adjusting your search criteria or filters.
                    @else
                        Get started by adding your first team member.
                    @endif
                </p>
                <div class="mt-6">
                    @if(request()->hasAny(['search', 'department', 'status', 'featured']))
                        <a href="{{ route('admin.team.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                            Clear filters
                        </a>
                    @else
                        <a href="{{ route('admin.team.create') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add your first team member
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
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Team Statistics</h3>
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
            const teamMemberCheckboxes = document.querySelectorAll('.team-member-checkbox');
            const bulkActions = document.getElementById('bulk-actions');
            const selectedCount = document.getElementById('selected-count');
            
            // Select all functionality
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    const isChecked = this.checked;
                    teamMemberCheckboxes.forEach(checkbox => {
                        checkbox.checked = isChecked;
                    });
                    updateBulkActions();
                });
            }
            
            // Individual checkbox change
            teamMemberCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const checkedBoxes = document.querySelectorAll('.team-member-checkbox:checked');
                    if (selectAllCheckbox) {
                        selectAllCheckbox.checked = checkedBoxes.length === teamMemberCheckboxes.length;
                        selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < teamMemberCheckboxes.length;
                    }
                    updateBulkActions();
                });
            });
            
            function updateBulkActions() {
                const checkedBoxes = document.querySelectorAll('.team-member-checkbox:checked');
                const count = checkedBoxes.length;
                
                if (count > 0) {
                    bulkActions.classList.remove('hidden');
                    selectedCount.textContent = `${count} team member${count === 1 ? '' : 's'} selected`;
                } else {
                    bulkActions.classList.add('hidden');
                }
            }
        });
        
        // Bulk actions
        function bulkAction(action) {
            const checkedBoxes = document.querySelectorAll('.team-member-checkbox:checked');
            if (checkedBoxes.length === 0) {
                alert('Please select at least one team member.');
                return;
            }
            
            const teamMemberIds = Array.from(checkedBoxes).map(cb => cb.value);
            
            let confirmMessage = '';
            switch(action) {
                case 'delete':
                    confirmMessage = `Are you sure you want to delete ${teamMemberIds.length} team member(s)?`;
                    break;
                case 'activate':
                    confirmMessage = `Are you sure you want to activate ${teamMemberIds.length} team member(s)?`;
                    break;
                case 'deactivate':
                    confirmMessage = `Are you sure you want to deactivate ${teamMemberIds.length} team member(s)?`;
                    break;
                case 'feature':
                    confirmMessage = `Are you sure you want to feature ${teamMemberIds.length} team member(s)?`;
                    break;
                case 'unfeature':
                    confirmMessage = `Are you sure you want to unfeature ${teamMemberIds.length} team member(s)?`;
                    break;
                default:
                    confirmMessage = `Are you sure you want to ${action} ${teamMemberIds.length} team member(s)?`;
            }
            
            if (confirm(confirmMessage)) {
                document.getElementById('bulk-action').value = action;
                document.getElementById('bulk-team-member-ids').value = JSON.stringify(teamMemberIds);
                document.getElementById('bulk-form').submit();
            }
        }
        
        // Export functionality
        function exportTeamMembers() {
            const params = new URLSearchParams(window.location.search);
            const exportUrl = '{{ route("admin.team.export") }}?' + params.toString();
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
            fetch('{{ route("admin.team.statistics") }}')
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
                    ${Object.entries(stats.overview || {}).map(([key, value]) => `
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">${value.count || value}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">${value.label || key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</div>
                        </div>
                    `).join('')}
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Recent Team Members</h4>
                        <div class="space-y-2">
                            ${(stats.recent_items || []).map(item => `
                                <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                    <div>
                                        <div class="font-medium text-sm">${item.title || item.name}</div>
                                        <div class="text-xs text-gray-500">${item.category || ''} • ${item.created_at || item.date}</div>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded ${item.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">${item.status || 'unknown'}</span>
                                </div>
                            `).join('') || '<div class="text-sm text-gray-500">No recent team members</div>'}
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Departments</h4>
                        <div class="space-y-2">
                            ${(stats.popular_categories || []).map(category => `
                                <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                    <div class="font-medium text-sm">${category.name}</div>
                                    <span class="text-xs text-gray-500">${category.count || category.team_members_count} members</span>
                                </div>
                            `).join('') || '<div class="text-sm text-gray-500">No departments</div>'}
                        </div>
                    </div>
                </div>
                
                ${stats.additional_metrics ? `
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        ${Object.entries(stats.additional_metrics).map(([key, value]) => `
                            <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded">
                                <span class="font-medium">${key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}:</span> ${value}
                            </div>
                        `).join('')}
                    </div>
                ` : ''}
            `;
            
            document.getElementById('statistics-content').innerHTML = content;
        }
        
        // Close modal when clicking outside
        document.getElementById('statistics-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeStatistics();
            }
        });
    </script>
    @endpush
</x-layouts.admin>