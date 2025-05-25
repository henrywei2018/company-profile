{{-- resources/views/admin/permissions/index.blade.php --}}
<x-layouts.admin title="Permissions Management">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Permissions Management' => '']" />

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Permissions Management</h1>
            <p class="text-sm text-gray-600 dark:text-neutral-400">Manage system permissions and access control</p>
        </div>
        <div class="flex items-center gap-3">
            @can('create permissions')
            <x-admin.button 
                href="{{ route('admin.permissions.bulk-create') }}" 
                color="info"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>'
            >
                Bulk Create
            </x-admin.button>
            
            <x-admin.button 
                href="{{ route('admin.permissions.create') }}" 
                color="primary"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />'
            >
                Add Permission
            </x-admin.button>
            @endcan
        </div>
    </div>

    <!-- Filters -->
    <x-admin.filter 
        action="{{ route('admin.permissions.index') }}" 
        method="GET"
        :resetRoute="route('admin.permissions.index')"
    >
        <div>
            <x-admin.input
                label="Search Permissions"
                name="search"
                :value="request('search')"
                placeholder="Search by permission name..."
            />
        </div>
        
        <div>
            <x-admin.select
                label="Module"
                name="module"
                :value="request('module')"
                placeholder="All Modules"
                :options="$modules->mapWithKeys(fn($module) => [$module => ucwords(str_replace('-', ' ', $module))])->toArray()"
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

    <!-- Permissions by Module -->
    <div class="space-y-6">
        @foreach($groupedPermissions as $module => $modulePermissions)
        <x-admin.card>
            <x-slot name="headerActions">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center space-x-3">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white capitalize">
                            {{ str_replace('-', ' ', $module) }} Module
                        </h3>
                        <x-admin.badge type="info">{{ $modulePermissions->count() }} Permissions</x-admin.badge>
                    </div>
                    <div class="flex items-center space-x-2">
                        @can('create permissions')
                        <x-admin.button 
                            href="{{ route('admin.permissions.bulk-create') }}?module={{ $module }}" 
                            color="light" 
                            size="sm"
                            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />'
                        >
                            Add to Module
                        </x-admin.button>
                        @endcan
                    </div>
                </div>
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($modulePermissions as $permission)
                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-4 border border-gray-200 dark:border-neutral-700">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ ucfirst(explode(' ', $permission->name)[0]) }}
                            </h4>
                            <p class="text-xs text-gray-500 dark:text-neutral-500 mt-1">
                                {{ $permission->name }}
                            </p>
                            @if($permission->description)
                            <p class="text-xs text-gray-600 dark:text-neutral-400 mt-2">
                                {{ Str::limit($permission->description, 60) }}
                            </p>
                            @endif
                            
                            <div class="flex items-center space-x-2 mt-3">
                                <x-admin.badge type="light" size="sm">{{ $permission->guard_name }}</x-admin.badge>
                                @if($permission->roles_count > 0)
                                <x-admin.badge type="success" size="sm">{{ $permission->roles_count }} roles</x-admin.badge>
                                @else
                                <x-admin.badge type="warning" size="sm">Unused</x-admin.badge>
                                @endif
                            </div>
                        </div>
                        
                        <x-admin.dropdown>
                            <x-slot name="trigger">
                                <x-admin.icon-button color="light" size="sm">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                    </svg>
                                </x-admin.icon-button>
                            </x-slot>

                            @can('view permissions')
                            <x-admin.dropdown-item 
                                href="{{ route('admin.permissions.show', $permission) }}"
                                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>'
                            >
                                View Details
                            </x-admin.dropdown-item>
                            @endcan

                            @can('edit permissions')
                            <x-admin.dropdown-item 
                                href="{{ route('admin.permissions.edit', $permission) }}"
                                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>'
                            >
                                Edit Permission
                            </x-admin.dropdown-item>
                            @endcan

                            @if($permission->roles_count > 0)
                            <x-admin.dropdown-item 
                                type="button"
                                onclick="loadPermissionRoles({{ $permission->id }}, '{{ $permission->name }}')"
                                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>'
                            >
                                View Roles ({{ $permission->roles_count }})
                            </x-admin.dropdown-item>
                            @endif

                            @can('delete permissions')
                            @if($permission->roles_count == 0)
                            <x-admin.dropdown-item 
                                type="form"
                                action="{{ route('admin.permissions.destroy', $permission) }}"
                                method="DELETE"
                                :confirm="true"
                                confirmMessage="Are you sure you want to delete this permission? This action cannot be undone."
                                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>'
                            >
                                Delete Permission
                            </x-admin.dropdown-item>
                            @endif
                            @endcan
                        </x-admin.dropdown>
                    </div>
                </div>
                @endforeach
            </div>
        </x-admin.card>
        @endforeach

        @if($groupedPermissions->isEmpty())
        <x-admin.card>
            <x-admin.empty-state
                title="No permissions found"
                description="No permissions match your search criteria. Try adjusting your filters or create new permissions."
                :actionText="auth()->user()->can('create permissions') ? 'Create Permission' : null"
                :actionUrl="auth()->user()->can('create permissions') ? route('admin.permissions.create') : null"
            />
        </x-admin.card>
        @endif
    </div>

    <!-- Pagination -->
    @if($permissions->hasPages())
    <div class="mt-6">
        <x-admin.pagination :paginator="$permissions" :appends="request()->query()" />
    </div>
    @endif

    <!-- Permission Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
        <x-admin.stat-card
            title="Total Permissions"
            :value="$permissions->total()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>'
            iconColor="text-blue-500"
            iconBg="bg-blue-100 dark:bg-blue-800/30"
        />

        <x-admin.stat-card
            title="Active Modules"
            :value="$groupedPermissions->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>'
            iconColor="text-green-500"
            iconBg="bg-green-100 dark:bg-green-800/30"
        />

        <x-admin.stat-card
            title="Assigned Permissions"
            :value="$permissions->where('roles_count', '>', 0)->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
            iconColor="text-purple-500"
            iconBg="bg-purple-100 dark:bg-purple-800/30"
        />

        <x-admin.stat-card
            title="Unused Permissions"
            :value="$permissions->where('roles_count', 0)->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>'
            iconColor="text-amber-500"
            iconBg="bg-amber-100 dark:bg-amber-800/30"
        />
    </div>

    <!-- Roles Modal -->
    <x-admin.modal id="roles-modal" title="Roles with Permission" size="lg">
        <div id="roles-content">
            <x-admin.loading text="Loading roles..." />
        </div>
        <x-slot name="footer">
            <x-admin.button type="button" color="light" data-hs-overlay="#roles-modal">
                Close
            </x-admin.button>
        </x-slot>
    </x-admin.modal>

    @push('scripts')
    <script>
        function loadPermissionRoles(permissionId, permissionName) {
            const content = document.getElementById('roles-content');
            const modal = document.querySelector('#roles-modal [data-hs-overlay-title]');
            
            if (modal) {
                modal.textContent = `Roles with "${permissionName}" Permission`;
            }
            
            content.innerHTML = '<div class="flex justify-center py-4"><div class="animate-spin h-8 w-8 border-b-2 border-blue-600 rounded-full"></div></div>';
            
            fetch(`{{ route('admin.permissions.roles', '') }}/${permissionId}`)
                .then(response => response.json())
                .then(roles => {
                    if (roles.length > 0) {
                        let html = '<div class="space-y-3">';
                        roles.forEach(role => {
                            html += `
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-neutral-700 rounded-lg">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">${role.name}</div>
                                        <div class="text-sm text-gray-500 dark:text-neutral-400">Guard: ${role.guard_name}</div>
                                    </div>
                                    <div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                            Role
                                        </span>
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                        content.innerHTML = html;
                    } else {
                        content.innerHTML = '<p class="text-center text-gray-500 dark:text-neutral-400 py-4">No roles found with this permission.</p>';
                    }
                })
                .catch(error => {
                    content.innerHTML = '<p class="text-center text-red-500 py-4">Error loading roles.</p>';
                });
        }
    </script>
    @endpush
</x-layouts.admin>