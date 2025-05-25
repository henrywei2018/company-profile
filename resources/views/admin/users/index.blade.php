{{-- resources/views/admin/users/index.blade.php --}}
<x-layouts.admin title="Users Management">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Users Management' => '']" />

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Users Management</h1>
            <p class="text-sm text-gray-600 dark:text-neutral-400">Manage user accounts and role assignments</p>
        </div>
        <div class="flex items-center gap-3">
            @can('create users')
                <x-admin.button href="{{ route('admin.users.create') }}" color="primary"
                    icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />'>
                    Add New User
                </x-admin.button>
            @endcan
        </div>
    </div>

    <!-- Filters -->
    <x-admin.filter action="{{ route('admin.users.index') }}" method="GET" :resetRoute="route('admin.users.index')">
        <div>
            <x-admin.input label="Search Users" name="search" :value="request('search')"
                placeholder="Search by name or email..." />
        </div>

        <div>
            <x-admin.select label="Role" name="role" :value="request('role')" placeholder="All Roles" :options="$roles" />
        </div>

        <div>
            <x-admin.select label="Status" name="status" :value="request('status')" placeholder="All Status"
                :options="[
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                    'verified' => 'Verified',
                    'unverified' => 'Unverified',
                ]" />
        </div>
    </x-admin.filter>

    <!-- Users Table -->
    <x-admin.card>
        <x-slot name="headerActions">
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center space-x-3">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">All Users</h3>
                    <x-admin.badge type="info">{{ $users->total() }} Total</x-admin.badge>
                </div>
                <div class="text-sm text-gray-600 dark:text-neutral-400">
                    Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                </div>
            </div>
        </x-slot>

        <x-admin.data-table>
            <x-slot name="columns">
                <x-admin.table-column>User</x-admin.table-column>
                <x-admin.table-column>Roles</x-admin.table-column>
                <x-admin.table-column>Status</x-admin.table-column>
                <x-admin.table-column>Last Login</x-admin.table-column>
                <x-admin.table-column>Joined</x-admin.table-column>
                <x-admin.table-column width="w-32">Actions</x-admin.table-column>
            </x-slot>

            @forelse($users as $user)
                <x-admin.table-row>
                    <x-admin.table-cell highlight>
                        <div class="flex items-center space-x-3">
                            <x-admin.avatar :src="$user->avatar_url" :alt="$user->name" size="sm" />
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $user->name }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-neutral-400">
                                    {{ $user->email }}
                                </div>
                                @if ($user->company)
                                    <div class="text-xs text-gray-400 dark:text-neutral-500">
                                        {{ $user->company }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </x-admin.table-cell>

                    <x-admin.table-cell>
                        <div class="flex flex-wrap gap-1">
                            @forelse($user->roles as $role)
                                <x-admin.badge :type="$role->badge_color ?? 'primary'" size="sm">
                                    {{ $role->name }}
                                </x-admin.badge>
                            @empty
                                <span class="text-xs text-gray-500 dark:text-neutral-500">No roles</span>
                            @endforelse
                        </div>
                    </x-admin.table-cell>

                    <x-admin.table-cell>
                        <div class="flex flex-col space-y-1">
                            <x-admin.badge :type="$user->is_active ? 'success' : 'danger'" size="sm">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </x-admin.badge>

                            <x-admin.badge :type="$user->email_verified_at ? 'info' : 'warning'" size="sm">
                                {{ $user->email_verified_at ? 'Verified' : 'Unverified' }}
                            </x-admin.badge>
                        </div>
                    </x-admin.table-cell>

                    <x-admin.table-cell>
                        <span class="text-sm {{ $user->last_login_at ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-neutral-500' }}">
                            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                        </span>
                    </x-admin.table-cell>

                    <x-admin.table-cell>
                        <span class="text-sm text-gray-900 dark:text-white">
                            {{ $user->created_at->format('M d, Y') }}
                        </span>
                    </x-admin.table-cell>

                    <x-admin.table-cell>
    <div class="relative inline-block text-left">
        <div>
            <button type="button" 
                    class="inline-flex items-center justify-center w-8 h-8 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:hover:bg-neutral-700"
                    onclick="toggleDropdown('dropdown-user-{{ $user->id }}')">
                <svg class="w-4 h-4 text-gray-600 dark:text-neutral-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                </svg>
            </button>
        </div>

        <div id="dropdown-user-{{ $user->id }}" 
             class="hidden absolute right-0 z-10 mt-2 w-56 origin-top-right bg-white border border-gray-200 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-neutral-800 dark:border-neutral-700">
            <div class="py-1">
                @can('view users')
                <a href="{{ route('admin.users.show', $user) }}" 
                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    View Profile
                </a>
                @endcan

                @can('edit users')
                <a href="{{ route('admin.users.edit', $user) }}" 
                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit User
                </a>
                <a href="{{ route('admin.users.password.form', $user) }}" 
                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    Change Password
                </a>

                @if ($user->id !== auth()->id())
                <form action="{{ route('admin.users.toggle-active', $user) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                        @if ($user->is_active)
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
                        </svg>
                        Deactivate User
                        @else
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Activate User
                        @endif
                    </button>
                </form>

                @if ($user->hasRole('client') && !$user->email_verified_at)
                <form action="{{ route('admin.users.verify', $user) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Verify Email
                    </button>
                </form>
                @endif
                @endif
                @endcan

                @can('delete users')
                @if ($user->id !== auth()->id())
                <div class="border-t border-gray-100 dark:border-neutral-600"></div>
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-700 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete User
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
                    <td colspan="6" class="px-6 py-12">
                        <x-admin.empty-state title="No users found"
                            description="No users match your search criteria. Try adjusting your filters or create a new user."
                            :actionText="auth()->user()->can('create users') ? 'Create New User' : null"
                            :actionUrl="auth()->user()->can('create users') ? route('admin.users.create') : null" />
                    </td>
                </tr>
            @endforelse
        </x-admin.data-table>

        @if ($users->hasPages())
            <x-slot name="footer">
                <x-admin.pagination :paginator="$users" :appends="request()->query()" />
            </x-slot>
        @endif
    </x-admin.card>

    <!-- User Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
        <x-admin.stat-card title="Total Users" :value="$users->total()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'
            iconColor="text-blue-500" iconBg="bg-blue-100 dark:bg-blue-800/30" />

        <x-admin.stat-card title="Active Users" :value="$users->where('is_active', true)->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
            iconColor="text-green-500" iconBg="bg-green-100 dark:bg-green-800/30" />

        <x-admin.stat-card title="Verified Users" :value="$users->whereNotNull('email_verified_at')->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>'
            iconColor="text-purple-500" iconBg="bg-purple-100 dark:bg-purple-800/30" />

        <x-admin.stat-card title="New This Month" :value="$users->where('created_at', '>=', now()->startOfMonth())->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />'
            iconColor="text-amber-500" iconBg="bg-amber-100 dark:bg-amber-800/30" />
    </div>

    @push('scripts')
        <script>
            function toggleDropdown(dropdownId) {
                document.querySelectorAll('[id^="dropdown-user-"]').forEach(dropdown => {
                    if (dropdown.id !== dropdownId) {
                        dropdown.classList.add('hidden');
                    }
                });

                const dropdown = document.getElementById(dropdownId);
                dropdown.classList.toggle('hidden');
            }

            document.addEventListener('click', function(event) {
                if (!event.target.closest('.relative')) {
                    document.querySelectorAll('[id^="dropdown-user-"]').forEach(dropdown => {
                        dropdown.classList.add('hidden');
                    });
                }
            });
        </script>
    @endpush
</x-layouts.admin>
