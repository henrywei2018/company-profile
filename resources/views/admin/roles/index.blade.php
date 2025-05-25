{{-- resources/views/admin/roles/index.blade.php --}}
<x-layouts.admin title="Roles Management">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Roles Management' => '']" />

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Roles Management</h1>
            <p class="text-sm text-gray-600 dark:text-neutral-400">Manage user roles and their permissions</p>
        </div>
        <div class="flex items-center gap-3">
            @can('create roles')
            <x-admin.button 
                href="{{ route('admin.roles.create') }}" 
                color="primary"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />'
            >
                Add New Role
            </x-admin.button>
            @endcan
        </div>
    </div>

    <!-- Filters -->
    <x-admin.filter 
        action="{{ route('admin.roles.index') }}" 
        method="GET"
        :resetRoute="route('admin.roles.index')"
    >
        <div>
            <x-admin.input
                label="Search Roles"
                name="search"
                :value="request('search')"
                placeholder="Search by role name..."
            />
        </div>
        
        <div>
            <x-admin.select
                label="Guard"
                name="guard"
                :value="request('guard')"
                placeholder="All Guards"
                :options="$guards->mapWithKeys(fn($guard) => [$guard => ucfirst($guard)])->toArray()"
            />
        </div>
    </x-admin.filter>

    <!-- Roles Table -->
    <x-admin.card>
        <x-slot name="headerActions">
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center space-x-3">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">All Roles</h3>
                    <x-admin.badge type="info">{{ $roles->total() }} Total</x-admin.badge>
                </div>
                <div class="text-sm text-gray-600 dark:text-neutral-400">
                    Showing {{ $roles->firstItem() }} to {{ $roles->lastItem() }} of {{ $roles->total() }} roles
                </div>
            </div>
        </x-slot>

        <x-admin.data-table>
            <x-slot name="columns">
                <x-admin.table-column>Role Name</x-admin.table-column>
                <x-admin.table-column>Description</x-admin.table-column>
                <x-admin.table-column>Users</x-admin.table-column>
                <x-admin.table-column>Permissions</x-admin.table-column>
                <x-admin.table-column>Guard</x-admin.table-column>
                <x-admin.table-column>Type</x-admin.table-column>
                <x-admin.table-column width="w-32">Actions</x-admin.table-column>
            </x-slot>

            @forelse($roles as $role)
            <x-admin.table-row>
                <x-admin.table-cell highlight>
                    <div class="flex items-center space-x-3">
                        <x-admin.badge 
                            :type="$role->badge_color ?? 'primary'" 
                            size="sm"
                        >
                            {{ $role->name }}
                        </x-admin.badge>
                    </div>
                </x-admin.table-cell>

                <x-admin.table-cell>
                    <span class="text-sm text-gray-600 dark:text-neutral-400">
                        {{ Str::limit($role->description ?? 'No description', 50) }}
                    </span>
                </x-admin.table-cell>

                <x-admin.table-cell>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium">{{ $role->users()->count() }}</span>
                        @if($role->users()->count() > 0)
                        <x-admin.badge type="success" size="sm">Active</x-admin.badge>
                        @endif
                    </div>
                </x-admin.table-cell>

                <x-admin.table-cell>
                    <span class="text-sm font-medium">{{ $role->permissions()->count() }}</span>
                    <span class="text-xs text-gray-500">permissions</span>
                </x-admin.table-cell>

                <x-admin.table-cell>
                    <x-admin.badge type="info" size="sm">{{ $role->guard_name }}</x-admin.badge>
                </x-admin.table-cell>

                <x-admin.table-cell>
                    @if($role->is_system ?? false)
                        <x-admin.badge type="warning" size="sm">System</x-admin.badge>
                    @else
                        <x-admin.badge type="light" size="sm">Custom</x-admin.badge>
                    @endif
                </x-admin.table-cell>

                <x-admin.table-cell>
                    <div class="relative inline-block text-left">
                        <div>
                            <button type="button" 
                                    class="inline-flex items-center justify-center w-8 h-8 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:hover:bg-neutral-700"
                                    onclick="toggleDropdown('dropdown-{{ $role->id }}')">
                                <svg class="w-4 h-4 text-gray-600 dark:text-neutral-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                </svg>
                            </button>
                        </div>

                        <div id="dropdown-{{ $role->id }}" 
                             class="hidden absolute right-0 z-10 mt-2 w-48 origin-top-right bg-white border border-gray-200 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-neutral-800 dark:border-neutral-700">
                            <div class="py-1">
                                @can('view roles')
                                <a href="{{ route('admin.roles.show', $role) }}" 
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    View Details
                                </a>
                                @endcan

                                @can('edit roles')
                                <a href="{{ route('admin.roles.edit', $role) }}" 
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit Role
                                </a>

                                <a href="{{ route('admin.roles.permissions', $role) }}" 
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    Manage Permissions
                                </a>

                                <form action="{{ route('admin.roles.duplicate', $role) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        Duplicate Role
                                    </button>
                                </form>
                                @endcan

                                @can('delete roles')
                                @if(!($role->is_system ?? false) && $role->users()->count() == 0)
                                <form action="{{ route('admin.roles.destroy', $role) }}" 
                                      method="POST" 
                                      class="inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this role? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="flex items-center w-full px-4 py-2 text-sm text-red-700 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Delete Role
                                    </button>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </div>
                    </div>
                </x-admin.table-cell>
            </x-admin.table-row>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-12">
                    <x-admin.empty-state
                        title="No roles found"
                        description="No roles match your search criteria. Try adjusting your filters or create a new role."
                        :actionText="auth()->user()->can('create roles') ? 'Create New Role' : null"
                        :actionUrl="auth()->user()->can('create roles') ? route('admin.roles.create') : null"
                    />
                </td>
            </tr>
            @endforelse
        </x-admin.data-table>

        @if($roles->hasPages())
        <x-slot name="footer">
            <x-admin.pagination :paginator="$roles" :appends="request()->query()" />
        </x-slot>
        @endif
    </x-admin.card>

    <!-- Role Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
        <x-admin.stat-card
            title="Total Roles"
            :value="$roles->total()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>'
            iconColor="text-blue-500"
            iconBg="bg-blue-100 dark:bg-blue-800/30"
        />

        <x-admin.stat-card
            title="System Roles"
            :value="$roles->where('is_system', true)->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>'
            iconColor="text-amber-500"
            iconBg="bg-amber-100 dark:bg-amber-800/30"
        />

        <x-admin.stat-card
            title="Custom Roles"
            :value="$roles->where('is_system', false)->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>'
            iconColor="text-green-500"
            iconBg="bg-green-100 dark:bg-green-800/30"
        />

        <x-admin.stat-card
            title="Active Users"
            :value="$roles->sum(function($role) { return $role->users()->count(); })"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'
            iconColor="text-purple-500"
            iconBg="bg-purple-100 dark:bg-purple-800/30"
        />
    </div>

    @push('scripts')
    <script>
        function toggleDropdown(dropdownId) {
            // Close all other dropdowns first
            document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
                if (dropdown.id !== dropdownId) {
                    dropdown.classList.add('hidden');
                }
            });
            
            // Toggle the clicked dropdown
            const dropdown = document.getElementById(dropdownId);
            dropdown.classList.toggle('hidden');
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.relative')) {
                document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
                    dropdown.classList.add('hidden');
                });
            }
        });
    </script>
    @endpush
</x-layouts.admin>