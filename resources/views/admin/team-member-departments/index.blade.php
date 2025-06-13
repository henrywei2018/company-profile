{{-- resources/views/admin/team-member-departments/index.blade.php --}}
<x-layouts.admin title="Team Departments Management">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Team Departments' => '']" />

    <!-- Header Section -->
    <x-admin.header-section 
        title="Team Departments Management" 
        description="Organize your team members into departments"
        :createRoute="route('admin.team-member-departments.create')"
        createText="Create New Department">
        
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
                    onclick="exportDepartments()"
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
        :action="route('admin.team-member-departments.index')"
        :searchValue="request('search')"
        searchPlaceholder="Search by department name or description..."
        :hasActiveFilters="request()->hasAny(['search', 'status'])"
        :clearFiltersRoute="route('admin.team-member-departments.index')"
        :filters="[
            [
                'name' => 'status', 
                'label' => 'Status',
                'allLabel' => 'All Status',
                'options' => [
                    'active' => 'Active',
                    'inactive' => 'Inactive'
                ]
            ]
        ]" />

    <!-- Bulk Actions -->
    <x-admin.bulk-actions 
        formId="bulk-form"
        :actionRoute="route('admin.team-member-departments.bulk-action')"
        selectedCountText="departments selected"
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
        :items="$departments"
        emptyTitle="No departments found"
        emptyDescription="Get started by creating your first department."
        emptyActionText="Create your first department"
        :emptyActionRoute="route('admin.team-member-departments.create')"
        :hasActiveFilters="request()->hasAny(['search', 'status'])"
        :clearFiltersRoute="route('admin.team-member-departments.index')"
        :headers="[
            ['label' => 'Department'],
            ['label' => 'Members'],
            ['label' => 'Status'],
            ['label' => 'Sort Order']
        ]">

        <x-slot name="emptyIcon">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
        </x-slot>

        @foreach($departments as $department)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                <td class="px-6 py-4">
                    <input type="checkbox" name="department_ids[]" value="{{ $department->id }}" 
                           class="item-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </td>
                
                <!-- Department Info -->
                <td class="px-6 py-4">
                    <div class="flex items-start space-x-3">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        
                        <x-admin.content-summary 
                            :title="$department->name"
                            :description="$department->description"
                            :link="route('admin.team-member-departments.edit', $department)"
                            :meta="['Created ' . $department->created_at->format('M d, Y')]" />
                    </div>
                </td>
                
                <!-- Members Count -->
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $department->team_members_count }}
                            </span>
                            <span class="text-sm text-gray-500 dark:text-gray-400 ml-1">
                                {{ Str::plural('member', $department->team_members_count) }}
                            </span>
                        </div>
                        @if($department->team_members_count > 0)
                            <a href="{{ route('admin.team.index', ['department' => $department->name]) }}" 
                               class="ml-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                </td>
                
                <!-- Status -->
                <td class="px-6 py-4 whitespace-nowrap">
                    <x-admin.status-badge :status="$department->is_active ? 'active' : 'inactive'" />
                </td>
                
                <!-- Sort Order -->
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                        {{ $department->sort_order }}
                    </span>
                </td>
                
                <!-- Actions -->
                <!-- Actions -->
<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
    <div class="flex items-center justify-end gap-2">
        <!-- Quick Action: Activate/Deactivate -->
        <form method="POST" action="{{ route('admin.team-member-departments.toggle-active', $department) }}" class="inline">
            @csrf
            @method('PATCH')
            <button type="submit"
                class="{{ $department->is_active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' }}"
                title="{{ $department->is_active ? 'Deactivate' : 'Activate' }}">
                @if($department->is_active)
                    <!-- Deactivate Icon -->
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                    </svg>
                @else
                    <!-- Activate Icon -->
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
                 class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
                <div class="py-1">
                    <!-- View Details -->
                    <a href="{{ route('admin.team-member-departments.show', $department) }}"
                       class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        View Details
                    </a>
                    <!-- Edit -->
                    <a href="{{ route('admin.team-member-departments.edit', $department) }}"
                       class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                    <!-- View Members -->
                    <a href="{{ route('admin.team.index', ['department' => $department->name]) }}"
                       class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        View Members ({{ $department->team_members_count }})
                    </a>
                    <div class="border-t border-gray-100 dark:border-gray-700"></div>
                    <!-- Delete -->
                    <form method="POST" action="{{ route('admin.team-member-departments.destroy', $department) }}"
                          onsubmit="return confirm('Are you sure you want to delete this department?{{ $department->team_members_count > 0 ? ' This department has ' . $department->team_members_count . ' members.' : '' }}')" class="inline w-full">
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
        title="Department Statistics"
        :statsEndpoint="route('admin.team-member-departments.statistics')" />

    @push('scripts')
    <script>
        // Override the default input name for department bulk actions
        function getInputName(action) {
            return 'department_ids[]';
        }

        // Export functionality
        function exportDepartments() {
            const params = new URLSearchParams(window.location.search);
            const exportUrl = '{{ route("admin.team-member-departments.export") }}?' + params.toString();
            window.open(exportUrl, '_blank');
        }

        // Department reordering (if you want to add drag & drop)
        document.addEventListener('DOMContentLoaded', function() {
            // You can add Sortable.js integration here for drag & drop reordering
            // Similar to how you might have it in other admin pages
        });
    </script>
    @endpush
</x-layouts.admin>