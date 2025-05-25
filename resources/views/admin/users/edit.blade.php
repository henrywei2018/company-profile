{{-- resources/views/admin/users/edit.blade.php --}}
<x-layouts.admin title="Edit User: {{ $user->name }}">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Users Management' => route('admin.users.index'),
        $user->name => route('admin.users.show', $user),
        'Edit' => ''
    ]" />

    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit User: {{ $user->name }}</h1>
            <p class="text-sm text-gray-600 dark:text-neutral-400">
                Update user information and role assignments
                @if($user->id === auth()->id())
                    <x-admin.badge type="info" size="sm" class="ml-2">You</x-admin.badge>
                @endif
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <x-admin.button 
                href="{{ route('admin.users.show', $user) }}" 
                color="light"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>'
            >
                View Profile
            </x-admin.button>
            <x-admin.button 
                href="{{ route('admin.users.index') }}" 
                color="light"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>'
            >
                Back to Users
            </x-admin.button>
        </div>
    </div>

    <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- User Information -->
            <div class="lg:col-span-2">
                <x-admin.form-section 
                    title="User Information"
                    description="Basic account information"
                >
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-admin.input
                                label="Full Name"
                                name="name"
                                :value="old('name', $user->name)"
                                required
                                placeholder="Enter user's full name"
                                helper="The display name for this user"
                            />
                        </div>

                        <x-admin.input
                            label="Email Address"
                            name="email"
                            type="email"
                            :value="old('email', $user->email)"
                            required
                            placeholder="user@example.com"
                            helper="This will be used for login and notifications"
                        />

                        <x-admin.input
                            label="Phone Number"
                            name="phone"
                            type="tel"
                            :value="old('phone', $user->phone)"
                            placeholder="+1 (555) 123-4567"
                            helper="Optional contact number"
                        />

                        <div class="md:col-span-2">
                            <x-admin.input
                                label="Company"
                                name="company"
                                :value="old('company', $user->company)"
                                placeholder="Company or organization name"
                                helper="Optional company information"
                            />
                        </div>

                        <div class="md:col-span-2">
                            <x-admin.textarea
                                label="Address"
                                name="address"
                                :value="old('address', $user->address)"
                                rows="3"
                                placeholder="Street address, city, state, postal code, country"
                                helper="Optional physical address"
                            />
                        </div>
                    </div>
                </x-admin.form-section>

                <x-admin.form-section 
                    title="Account Status"
                    description="Manage account accessibility and verification"
                    class="mt-6"
                >
                    <div class="space-y-4">
                        @if($user->id !== auth()->id())
                        <x-admin.checkbox
                            label="Account Active"
                            name="is_active"
                            :checked="old('is_active', $user->is_active)"
                            helper="Active users can log in and access the system"
                        />
                        @else
                        <x-admin.alert type="info">
                            <strong>Note:</strong> You cannot deactivate your own account. Contact another administrator if needed.
                        </x-admin.alert>
                        <input type="hidden" name="is_active" value="1">
                        @endif

                        <div class="border-t border-gray-200 dark:border-neutral-700 pt-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Email Verification</label>
                                    <p class="text-xs text-gray-500 dark:text-neutral-500">Current verification status</p>
                                </div>
                                <div>
                                    @if($user->email_verified_at)
                                        <x-admin.badge type="success">
                                            Verified {{ $user->email_verified_at->format('M d, Y') }}
                                        </x-admin.badge>
                                    @else
                                        <x-admin.badge type="warning">Not Verified</x-admin.badge>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 dark:border-neutral-700 pt-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600 dark:text-neutral-400">Last Login:</span>
                                    <span class="text-gray-900 dark:text-white ml-2">
                                        {{ $user->last_login_at ? $user->last_login_at->format('M d, Y g:i A') : 'Never' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-neutral-400">Login Count:</span>
                                    <span class="text-gray-900 dark:text-white ml-2">{{ $user->login_count ?? 0 }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-neutral-400">Account Created:</span>
                                    <span class="text-gray-900 dark:text-white ml-2">{{ $user->created_at->format('M d, Y') }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-neutral-400">Last Updated:</span>
                                    <span class="text-gray-900 dark:text-white ml-2">{{ $user->updated_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-admin.form-section>
            </div>

            <!-- Avatar & Roles -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Current Avatar -->
                <x-admin.card title="Profile Picture">
                    <div class="text-center">
                        <x-admin.avatar 
                            :src="$user->avatar_url" 
                            :alt="$user->name"
                            size="xl"
                            class="mx-auto mb-4"
                        />
                        <p class="text-sm text-gray-600 dark:text-neutral-400 mb-4">Current avatar</p>
                        
                        <x-admin.image-uploader
                            name="avatar"
                            label="Upload New Avatar"
                            accept=".jpg,.jpeg,.png,.gif"
                            helper="Recommended size: 200x200px. Max file size: 2MB"
                            aspectRatio="1:1"
                            :showRemoveButton="false"
                            :showFeaturedToggle="false"
                            :showAltTextField="false"
                        >
                            JPG, PNG, or GIF (Max 2MB)
                        </x-admin.image-uploader>
                    </div>
                </x-admin.card>

                <!-- Role Assignment -->
                <x-admin.form-section 
                    title="Role Assignment"
                    description="Current user roles and permissions"
                >
                    @if($user->id === auth()->id() && $user->hasRole('super-admin'))
                        <x-admin.alert type="warning" class="mb-4">
                            <strong>Restricted:</strong> You cannot modify your own super-admin role.
                        </x-admin.alert>
                    @endif

                    <div class="space-y-3">
                        @foreach($roles as $role)
                        <label class="flex items-start space-x-3 p-3 border border-gray-200 dark:border-neutral-700 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-700 {{ in_array($role->id, $userRoles) ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800' : '' }}">
                            <input 
                                type="checkbox" 
                                name="roles[]" 
                                value="{{ $role->id }}"
                                class="mt-0.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                {{ in_array($role->id, old('roles', $userRoles)) ? 'checked' : '' }}
                                {{ ($user->id === auth()->id() && $role->name === 'super-admin') ? 'disabled' : '' }}
                            >
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $role->formatted_name }}
                                    </span>
                                    <x-admin.badge 
                                        :type="$role->badge_color ?? 'primary'" 
                                        size="sm"
                                    >
                                        {{ $role->name }}
                                    </x-admin.badge>
                                    @if(in_array($role->id, $userRoles))
                                        <x-admin.badge type="success" size="sm">Current</x-admin.badge>
                                    @endif
                                </div>
                                @if($role->description)
                                <p class="text-xs text-gray-500 dark:text-neutral-500 mt-1">
                                    {{ $role->description }}
                                </p>
                                @endif
                                <div class="text-xs text-gray-400 dark:text-neutral-600 mt-1">
                                    {{ $role->permissions()->count() }} permissions
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    @error('roles')
                        <div class="mt-2">
                            <span class="text-xs text-red-600 dark:text-red-500">{{ $message }}</span>
                        </div>
                    @enderror

                    @if($user->id === auth()->id() && $user->hasRole('super-admin'))
                        <!-- Add hidden input to maintain super-admin role -->
                        <input type="hidden" name="roles[]" value="{{ $roles->where('name', 'super-admin')->first()->id }}">
                    @endif
                </x-admin.form-section>

                <!-- User Statistics -->
                <x-admin.card title="User Statistics">
                    <div class="space-y-3">
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
                            <span class="text-sm text-gray-600 dark:text-neutral-400">Messages</span>
                            <x-admin.badge type="warning">{{ $user->messages()->count() }}</x-admin.badge>
                        </div>
                    </div>
                </x-admin.card>

                <!-- Form Actions -->
                <x-admin.card>
                    <div class="flex flex-col space-y-3">
                        <x-admin.button 
                            type="submit" 
                            color="primary" 
                            class="w-full"
                            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>'
                        >
                            Update User
                        </x-admin.button>
                        
                        <x-admin.button 
                            href="{{ route('admin.users.password.form', $user) }}" 
                            color="warning" 
                            class="w-full"
                            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>'
                        >
                            Change Password
                        </x-admin.button>
                        
                        <x-admin.button 
                            type="button" 
                            color="light" 
                            class="w-full"
                            onclick="history.back()"
                            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>'
                        >
                            Cancel
                        </x-admin.button>
                    </div>
                </x-admin.card>
            </div>
        </div>
    </form>
</x-layouts.admin>