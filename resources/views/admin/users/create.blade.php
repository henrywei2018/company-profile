{{-- resources/views/admin/users/create.blade.php --}}
<x-layouts.admin title="Create New User">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Users Management' => route('admin.users.index'),
        'Create User' => ''
    ]" />

    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create New User</h1>
            <p class="text-sm text-gray-600 dark:text-neutral-400">Add a new user account and assign roles</p>
        </div>
        <x-admin.button 
            href="{{ route('admin.users.index') }}" 
            color="light"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>'
        >
            Back to Users
        </x-admin.button>
    </div>

    <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- User Information -->
            <div class="lg:col-span-2">
                <x-admin.form-section 
                    title="User Information"
                    description="Basic account information for the new user"
                >
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-admin.input
                                label="Full Name"
                                name="name"
                                :value="old('name')"
                                required
                                placeholder="Enter user's full name"
                                helper="The display name for this user"
                            />
                        </div>

                        <x-admin.input
                            label="Email Address"
                            name="email"
                            type="email"
                            :value="old('email')"
                            required
                            placeholder="user@example.com"
                            helper="This will be used for login and notifications"
                        />

                        <x-admin.input
                            label="Phone Number"
                            name="phone"
                            type="tel"
                            :value="old('phone')"
                            placeholder="+1 (555) 123-4567"
                            helper="Optional contact number"
                        />

                        <div class="md:col-span-2">
                            <x-admin.input
                                label="Company"
                                name="company"
                                :value="old('company')"
                                placeholder="Company or organization name"
                                helper="Optional company information"
                            />
                        </div>

                        <div class="md:col-span-2">
                            <x-admin.textarea
                                label="Address"
                                name="address"
                                :value="old('address')"
                                rows="3"
                                placeholder="Street address, city, state, postal code, country"
                                helper="Optional physical address"
                            />
                        </div>
                    </div>
                </x-admin.form-section>

                <x-admin.form-section 
                    title="Account Security"
                    description="Set up login credentials for the user"
                    class="mt-6"
                >
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-admin.input
                            label="Password"
                            name="password"
                            type="password"
                            required
                            placeholder="Enter secure password"
                            helper="Minimum 8 characters with letters and numbers"
                        />

                        <x-admin.input
                            label="Confirm Password"
                            name="password_confirmation"
                            type="password"
                            required
                            placeholder="Confirm the password"
                            helper="Must match the password above"
                        />
                    </div>

                    <div class="mt-4">
                        <x-admin.checkbox
                            label="Account Active"
                            name="is_active"
                            :checked="old('is_active', true)"
                            helper="Active users can log in and access the system"
                        />
                    </div>
                </x-admin.form-section>
            </div>

            <!-- Avatar & Roles -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Avatar Upload -->
                <x-admin.form-section 
                    title="Profile Picture"
                    description="Upload an avatar for the user"
                >
                    <x-admin.image-uploader
                        name="avatar"
                        label="Avatar Image"
                        accept=".jpg,.jpeg,.png,.gif"
                        helper="Recommended size: 200x200px. Max file size: 2MB"
                        aspectRatio="1:1"
                        :showRemoveButton="false"
                        :showFeaturedToggle="false"
                        :showAltTextField="false"
                    >
                        JPG, PNG, or GIF (Max 2MB)
                    </x-admin.image-uploader>
                </x-admin.form-section>

                <!-- Role Assignment -->
                <x-admin.form-section 
                    title="Role Assignment"
                    description="Assign roles to determine user permissions"
                >
                    <div class="space-y-3">
                        @foreach($roles as $role)
                        <label class="flex items-start space-x-3 p-3 border border-gray-200 dark:border-neutral-700 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-700">
                            <input 
                                type="checkbox" 
                                name="roles[]" 
                                value="{{ $role->id }}"
                                class="mt-0.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}
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

                    <x-admin.help-text type="info" class="mt-4">
                        <strong>Note:</strong> Users must have at least one role assigned to access the system. 
                        Multiple roles can be assigned to grant combined permissions.
                    </x-admin.help-text>
                </x-admin.form-section>

                <!-- Form Actions -->
                <x-admin.card>
                    <div class="flex flex-col space-y-3">
                        <x-admin.button 
                            type="submit" 
                            color="primary" 
                            class="w-full"
                            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />'
                        >
                            Create User
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

    @push('scripts')
    <script>
        // Password strength indicator
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.querySelector('input[name="password"]');
            const confirmInput = document.querySelector('input[name="password_confirmation"]');
            
            if (passwordInput && confirmInput) {
                confirmInput.addEventListener('input', function() {
                    if (this.value && this.value !== passwordInput.value) {
                        this.setCustomValidity('Passwords do not match');
                    } else {
                        this.setCustomValidity('');
                    }
                });
                
                passwordInput.addEventListener('input', function() {
                    if (confirmInput.value && confirmInput.value !== this.value) {
                        confirmInput.setCustomValidity('Passwords do not match');
                    } else {
                        confirmInput.setCustomValidity('');
                    }
                });
            }
        });
    </script>
    @endpush
</x-layouts.admin>