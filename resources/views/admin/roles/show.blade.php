{{-- resources/views/admin/roles/show.blade.php --}}
<x-layouts.admin title="Role Details: {{ $role->name }}">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Roles Management' => route('admin.roles.index'),
        $role->name => ''
    ]" />

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $role->formatted_name }}</h1>
                <x-admin.badge :type="$role->color ?? 'primary'">{{ $role->name }}</x-admin.badge>
                @if($role->is_system)
                    <x-admin.badge type="warning">System Role</x-admin.badge>
                @endif
            </div>
            @if($role->description)
            <p class="text-sm text-gray-600 dark:text-neutral-400 mt-1">{{ $role->description }}</p>
            @endif
        </div>
        <div class="flex items-center space-x-3">
            @can('edit roles')
            <x-admin.button 
                href="{{ route('admin.roles.edit', $role) }}" 
                color="primary"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>'
            >
                Edit Role
            </x-admin.button>
            @endcan
            
            <x-admin.button 
                href="{{ route('admin.roles.index') }}" 
                color="light"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>'
            >
                Back to Roles
            </x-admin.button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Role Information -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Basic Information -->
            <x-admin.card title="Role Information">
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Role Name</label>
                        <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $role->name }}</p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Display Name</label>
                        <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $role->formatted_name }}</p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Guard</label>
                        <p class="text-sm text-gray-900 dark:text-white mt-1">
                            <x-admin.badge type="info" size="sm">{{ $role->guard_name }}</x-admin.badge>
                        </p>
                    </div>
                    
                    @if($role->description)
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Description</label>
                        <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $role->description }}</p>
                    </div>
                    @endif
                    
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Role Type</label>
                        <p class="text-sm text-gray-900 dark:text-white mt-1">
                            @if($role->is_system)
                                <x-admin.badge type="warning" size="sm">System Role</x-admin.badge>
                            @else
                                <x-admin.badge type="light" size="sm">Custom Role</x-admin.badge>
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Created</label>
                        <p class="text-sm text-gray-900 dark:text-white mt-1">
                            {{ $role->created_at->format('F j, Y \a\t g:i A') }}
                        </p>
                    </div>
                </div>
            </x-admin.card>

            <!-- Statistics -->
            <x-admin.card title="Statistics">
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-neutral-400">Users with this role</span>
                        <x-admin.badge type="info">{{ $usersCount }}</x-admin.badge>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-neutral-400">Total permissions</span>
                        <x-admin.badge type="success">{{ $role->permissions_count }}</x-admin.badge>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-neutral-400">Permission modules</span>
                        <x-admin.badge type="primary">{{ count($permissions) }}</x-admin.badge>
                    </div>
                </div>
            </x-admin.card>

            <!-- Actions -->
            <x-admin.card title="Actions">
                <div class="space-y-3">
                    @can('edit roles')
                    <x-admin.button 
                        href="{{ route('admin.roles.permissions', $role) }}" 
                        color="primary" 
                        class="w-full"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>'
                    >
                        Manage Permissions
                    </x-admin.button>

                    <form action="{{ route('admin.roles.duplicate', $role) }}" method="POST" class="w-full">
                        @csrf
                        <x-admin.button 
                            type="submit" 
                            color="light" 
                            class="w-full"
                            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>'
                        >
                            Duplicate Role
                        </x-admin.button>
                    </form>
                    @endcan

                    @if($usersCount > 0)
                    <x-admin.button 
                        onclick="loadRoleUsers({{ $role->id }})" 
                        color="info" 
                        class="w-full"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>'
                    >
                        View Users ({{ $usersCount }})
                    </x-admin.button>
                    @endif

                    @can('delete roles')
                    @if(!$role->is_system && $usersCount == 0)
                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="w-full" 
                          onsubmit="return confirm('Are you sure you want to delete this role? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <x-admin.button 
                            type="submit" 
                            color="danger" 
                            class="w-full"
                            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>'
                        >
                            Delete Role
                        </x-admin.button>
                    </form>
                    @endif
                    @endcan
                </div>
            </x-admin.card>
        </div>

        <!-- Permissions -->
        <div class="lg:col-span-2">
            <x-admin.card title="Role Permissions" subtitle="Permissions granted to users with this role">
                @if($permissions->count() > 0)
                <div class="space-y-6">
                    @foreach($permissions as $module => $modulePermissions)
                    <div class="border border-gray-200 dark:border-neutral-700 rounded-lg">
                        <div class="bg-gray-50 dark:bg-neutral-800 px-4 py-3 border-b border-gray-200 dark:border-neutral-700">
                            <div class="flex items-center justify-between">
                                <h4 class="font-medium text-gray-900 dark:text-white capitalize">
                                    {{ str_replace('-', ' ', $module) }}
                                </h4>
                                <x-admin.badge type="info" size="sm">
                                    {{ $modulePermissions->count() }} permissions
                                </x-admin.badge>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($modulePermissions as $permission)
                                <div class="flex items-center space-x-3 p-2 bg-green-50 dark:bg-green-900/20 rounded-md">
                                    <div class="flex-shrink-0">
                                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ ucfirst(explode(' ', $permission->name)[0]) }}
                                        </div>
                                        @if($permission->description)
                                        <div class="text-xs text-gray-500 dark:text-neutral-500">
                                            {{ $permission->description }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <x-admin.empty-state
                    title="No Permissions"
                    description="This role has no permissions assigned. Users with this role will have very limited access."
                    :actionText="auth()->user()->can('edit roles') ? 'Assign Permissions' : null"
                    :actionUrl="auth()->user()->can('edit roles') ? route('admin.roles.permissions', $role) : null"
                />
                @endif
            </x-admin.card>
        </div>
    </div>

    <!-- Users Modal -->
    <x-admin.modal id="users-modal" title="Users with {{ $role->name }} Role" size="lg">
        <div id="users-content">
            <x-admin.loading text="Loading users..." />
        </div>
        <x-slot name="footer">
            <x-admin.button type="button" color="light" data-hs-overlay="#users-modal">
                Close
            </x-admin.button>
        </x-slot>
    </x-admin.modal>

    @push('scripts')
    <script>
        function loadRoleUsers(roleId) {
            const content = document.getElementById('users-content');
            content.innerHTML = '<div class="flex justify-center py-4"><div class="animate-spin h-8 w-8 border-b-2 border-blue-600 rounded-full"></div></div>';
            
            fetch(`{{ route('admin.roles.users', '') }}/${roleId}`)
                .then(response => response.json())
                .then(users => {
                    if (users.length > 0) {
                        let html = '<div class="space-y-3">';
                        users.forEach(user => {
                            html += `
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-neutral-700 rounded-lg">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">${user.name}</div>
                                        <div class="text-sm text-gray-500 dark:text-neutral-400">${user.email}</div>
                                    </div>
                                    <div>
                                        ${user.is_active ? 
                                            '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Active</span>' : 
                                            '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Inactive</span>'
                                        }
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                        content.innerHTML = html;
                    } else {
                        content.innerHTML = '<p class="text-center text-gray-500 dark:text-neutral-400 py-4">No users found with this role.</p>';
                    }
                })
                .catch(error => {
                    content.innerHTML = '<p class="text-center text-red-500 py-4">Error loading users.</p>';
                });
        }
    </script>
    @endpush
</x-layouts.admin>