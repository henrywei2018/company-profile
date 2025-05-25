{{-- resources/views/admin/users/show.blade.php --}}
<x-layouts.admin title="User Profile: {{ $user->name }}">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Users Management' => route('admin.users.index'),
        $user->name => ''
    ]" />

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex items-center space-x-4">
            <x-admin.avatar 
                :src="$user->avatar_url" 
                :alt="$user->name"
                size="lg"
            />
            <div>
                <div class="flex items-center space-x-3">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h1>
                    @if($user->id === auth()->id())
                        <x-admin.badge type="info">You</x-admin.badge>
                    @endif
                    @if($user->is_active)
                        <x-admin.badge type="success">Active</x-admin.badge>
                    @else
                        <x-admin.badge type="danger">Inactive</x-admin.badge>
                    @endif
                </div>
                <p class="text-sm text-gray-600 dark:text-neutral-400">{{ $user->email }}</p>
                @if($user->company)
                <p class="text-sm text-gray-500 dark:text-neutral-500">{{ $user->company }}</p>
                @endif
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            @can('edit users')
            <x-admin.button 
                href="{{ route('admin.users.edit', $user) }}" 
                color="primary"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>'
            >
                Edit User
            </x-admin.button>
            @endcan
            
            <x-admin.button 
                href="{{ route('admin.users.index') }}" 
                color="light"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>'
            >
                Back to Users
            </x-admin.button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- User Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <x-admin.card title="User Information">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Full Name</label>
                        <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $user->name }}</p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Email Address</label>
                        <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $user->email }}</p>
                    </div>
                    
                    @if($user->phone)
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Phone Number</label>
                        <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $user->phone }}</p>
                    </div>
                    @endif
                    
                    @if($user->company)
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Company</label>
                        <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $user->company }}</p>
                    </div>
                    @endif
                    
                    @if($user->address)
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Address</label>
                        <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $user->address }}</p>
                    </div>
                    @endif
                </div>
            </x-admin.card>

            <!-- Account Status -->
            <x-admin.card title="Account Status">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Account Status</label>
                        <div class="mt-1">
                            @if($user->is_active)
                                <x-admin.badge type="success">Active</x-admin.badge>
                            @else
                                <x-admin.badge type="danger">Inactive</x-admin.badge>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Email Verification</label>
                        <div class="mt-1">
                            @if($user->email_verified_at)
                                <x-admin.badge type="success">
                                    Verified {{ $user->email_verified_at->format('M d, Y') }}
                                </x-admin.badge>
                            @else
                                <x-admin.badge type="warning">Not Verified</x-admin.badge>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Last Login</label>
                        <p class="text-sm text-gray-900 dark:text-white mt-1">
                            {{ $user->last_login_at ? $user->last_login_at->format('M d, Y g:i A') : 'Never logged in' }}
                        </p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Login Count</label>
                        <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $user->login_count ?? 0 }} times</p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Account Created</label>
                        <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $user->created_at->format('F j, Y \a\t g:i A') }}</p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Last Updated</label>
                        <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $user->updated_at->format('F j, Y \a\t g:i A') }}</p>
                    </div>
                </div>
            </x-admin.card>

            <!-- User Permissions -->
            <x-admin.card title="User Permissions" subtitle="All permissions granted to this user through roles">
                @if($userPermissions->count() > 0)
                <div class="space-y-6">
                    @foreach($userPermissions as $module => $modulePermissions)
                    <div class="border border-gray-200 dark:border-neutral-700 rounded-lg">
                        <div class="bg-gray-50 dark:bg-neutral-800 px-4 py-3 border-b border-gray-200 dark:border-neutral-700">
                            <div class="flex items-center justify-between">
                                <h4 class="font-medium text-gray-900 dark:text-white capitalize">
                                    {{ str_replace('-', ' ', $module) }}
                                </h4>
                                <x-admin.badge type="info" size="sm">
                                    {{ count($modulePermissions) }} permissions
                                </x-admin.badge>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($modulePermissions as $permissionId => $permissionName)
                                <div class="flex items-center space-x-3 p-2 bg-green-50 dark:bg-green-900/20 rounded-md">
                                    <div class="flex-shrink-0">
                                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ ucfirst(explode(' ', $permissionName)[0]) }}
                                        </div>
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
                    description="This user has no permissions assigned. Consider assigning roles to grant access."
                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>'
                />
                @endif
            </x-admin.card>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- User Roles -->
            <x-admin.card title="User Roles">
                @if($user->roles->count() > 0)
                <div class="space-y-3">
                    @foreach($user->roles as $role)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-neutral-700 rounded-lg">
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">{{ $role->formatted_name }}</div>
                            @if($role->description)
                            <div class="text-xs text-gray-500 dark:text-neutral-400">{{ $role->description }}</div>
                            @endif
                        </div>
                        <x-admin.badge :type="$role->badge_color ?? 'primary'" size="sm">
                            {{ $role->name }}
                        </x-admin.badge>
                    </div>
                    @endforeach
                </div>
                @else
                <x-admin.empty-state
                    title="No Roles Assigned"
                    description="This user has no roles assigned."
                />
                @endif
            </x-admin.card>

            <!-- User Statistics -->
            <x-admin.card title="Statistics">
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-neutral-400">Active Roles</span>
                        <x-admin.badge type="info">{{ $user->roles->count() }}</x-admin.badge>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-neutral-400">Total Permissions</span>
                        <x-admin.badge type="success">{{ $user->getAllPermissions()->count() }}</x-admin.badge>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-neutral-400">Projects</span>
                        <x-admin.badge type="primary">{{ $user->projects()->count() }}</x-admin.badge>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-neutral-400">Quotations</span>
                        <x-admin.badge type="warning">{{ $user->quotations()->count() }}</x-admin.badge>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-neutral-400">Messages</span>
                        <x-admin.badge type="info">{{ $user->messages()->count() }}</x-admin.badge>
                    </div>
                </div>
            </x-admin.card>

            <!-- Quick Actions -->
            <x-admin.card title="Quick Actions">
                <div class="space-y-3">
                    @can('edit users')
                    <x-admin.button 
                        href="{{ route('admin.users.edit', $user) }}" 
                        color="primary" 
                        class="w-full"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>'
                    >
                        Edit User
                    </x-admin.button>

                    <x-admin.button 
                        href="{{ route('admin.users.roles', $user) }}" 
                        color="info" 
                        class="w-full"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>'
                    >
                        Manage Roles
                    </x-admin.button>

                    <x-admin.button 
                        href="{{ route('admin.users.password.form', $user) }}" 
                        color="warning" 
                        class="w-full"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>'
                    >
                        Change Password
                    </x-admin.button>
                    @endcan

                    @if($user->id !== auth()->id())
                    <form action="{{ route('admin.users.toggle-active', $user) }}" method="POST" class="w-full">
                        @csrf
                        <x-admin.button 
                            type="submit" 
                            :color="$user->is_active ? 'danger' : 'success'" 
                            class="w-full"
                            :icon="$user->is_active ? '<path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728\"/>' : '<path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z\"/>'"
                        >
                            {{ $user->is_active ? 'Deactivate User' : 'Activate User' }}
                        </x-admin.button>
                    </form>

                    @if($user->hasRole('client') && !$user->email_verified_at)
                    <form action="{{ route('admin.users.verify', $user) }}" method="POST" class="w-full">
                        @csrf
                        <x-admin.button 
                            type="submit" 
                            color="success" 
                            class="w-full"
                            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
                        >
                            Verify Email
                        </x-admin.button>
                    </form>
                    @endif
                    @endif

                    @can('delete users')
                    @if($user->id !== auth()->id())
                    <form action="{{ route('admin.users.destroy', $user) }}" 
                          method="POST" 
                          class="w-full" 
                          onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone and will remove all associated data.')">
                        @csrf
                        @method('DELETE')
                        <x-admin.button 
                            type="submit" 
                            color="danger" 
                            class="w-full"
                            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>'
                        >
                            Delete User
                        </x-admin.button>
                    </form>
                    @endif
                    @endcan
                </div>
            </x-admin.card>

            <!-- Recent Activity -->
            <x-admin.card title="Recent Activity">
                <div class="space-y-3">
                    @if($user->last_login_at)
                    <div class="flex items-center space-x-3 p-2 bg-green-50 dark:bg-green-900/20 rounded-md">
                        <div class="flex-shrink-0">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">Last Login</div>
                            <div class="text-xs text-gray-500 dark:text-neutral-500">{{ $user->last_login_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    @endif

                    @if($user->updated_at > $user->created_at)
                    <div class="flex items-center space-x-3 p-2 bg-blue-50 dark:bg-blue-900/20 rounded-md">
                        <div class="flex-shrink-0">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">Profile Updated</div>
                            <div class="text-xs text-gray-500 dark:text-neutral-500">{{ $user->updated_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    @endif

                    <div class="flex items-center space-x-3 p-2 bg-gray-50 dark:bg-neutral-700 rounded-md">
                        <div class="flex-shrink-0">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">Account Created</div>
                            <div class="text-xs text-gray-500 dark:text-neutral-500">{{ $user->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                </div>
            </x-admin.card>
        </div>
    </div>
</x-layouts.admin>