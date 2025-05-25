{{-- resources/views/admin/roles/edit.blade.php --}}
<x-layouts.admin title="Edit Role: {{ $role->name }}">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Roles Management' => route('admin.roles.index'),
        $role->name => route('admin.roles.show', $role),
        'Edit' => ''
    ]" />

    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Role: {{ $role->name }}</h1>
            <p class="text-sm text-gray-600 dark:text-neutral-400">
                Modify role settings and permissions
                @if($role->is_system)
                    <x-admin.badge type="warning" size="sm" class="ml-2">System Role</x-admin.badge>
                @endif
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <x-admin.button 
                href="{{ route('admin.roles.show', $role) }}" 
                color="light"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>'
            >
                View Details
            </x-admin.button>
            <x-admin.button 
                href="{{ route('admin.roles.index') }}" 
                color="light"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>'
            >
                Back to Roles
            </x-admin.button>
        </div>
    </div>

    <form action="{{ route('admin.roles.update', $role) }}" method="POST" x-data="{ selectedPermissions: @js($rolePermissions) }">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Role Information -->
            <div class="lg:col-span-1">
                <x-admin.form-section 
                    title="Role Information"
                    description="Basic information about the role"
                >
                    <div class="space-y-4">
                        <x-admin.input
                            label="Role Name"
                            name="name"
                            :value="old('name', $role->name)"
                            required
                            :disabled="$role->is_system && !auth()->user()->hasRole('super-admin')"
                            placeholder="Enter role name (e.g., Content Manager)"
                            helper="Role name should be descriptive and unique"
                        />

                        <x-admin.select
                            label="Guard"
                            name="guard_name"
                            :value="old('guard_name', $role->guard_name)"
                            required
                            :disabled="$role->is_system"
                            :options="$guards->mapWithKeys(fn($guard) => [$guard => ucfirst($guard)])->toArray()"
                            helper="Select the authentication guard for this role"
                        />

                        <x-admin.textarea
                            label="Description"
                            name="description"
                            :value="old('description', $role->description)"
                            rows="3"
                            placeholder="Describe what this role is for and what access it provides..."
                            helper="Optional description to help identify the role's purpose"
                        />

                        <x-admin.select
                            label="Role Color"
                            name="color"
                            :value="old('color', $role->color)"
                            placeholder="Select a color"
                            :options="[
                                'blue' => 'Blue',
                                'green' => 'Green', 
                                'red' => 'Red',
                                'yellow' => 'Yellow',
                                'purple' => 'Purple',
                                'pink' => 'Pink',
                                'indigo' => 'Indigo',
                                'gray' => 'Gray',
                                'orange' => 'Orange',
                                'teal' => 'Teal'
                            ]"
                            helper="Color coding helps identify roles visually"
                        />
                    </div>

                    <x-slot name="footer">
                        <div class="flex items-center justify-end space-x-3">
                            <x-admin.button type="button" color="light" onclick="history.back()">
                                Cancel
                            </x-admin.button>
                            <x-admin.button type="submit" color="primary">
                                Update Role
                            </x-admin.button>
                        </div>
                    </x-slot>
                </x-admin.form-section>

                <!-- Role Statistics -->
                <x-admin.card class="mt-6">
                    <x-slot name="title">Role Statistics</x-slot>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-neutral-400">Users with this role</span>
                            <x-admin.badge type="info">{{ $role->users_count }}</x-admin.badge>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-neutral-400">Total permissions</span>
                            <x-admin.badge type="success" x-text="selectedPermissions.length"></x-admin.badge>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-neutral-400">Role type</span>
                            @if($role->is_system)
                                <x-admin.badge type="warning">System</x-admin.badge>
                            @else
                                <x-admin.badge type="light">Custom</x-admin.badge>
                            @endif
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-neutral-400">Created</span>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $role->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </x-admin.card>

                <!-- Permission Summary -->
                <x-admin.card class="mt-6">
                    <x-slot name="title">Permission Summary</x-slot>
                    <div x-show="selectedPermissions.length > 0">
                        <p class="text-sm text-gray-600 dark:text-neutral-400 mb-3">
                            Selected <span x-text="selectedPermissions.length" class="font-medium"></span> permissions
                        </p>
                        <div class="max-h-32 overflow-y-auto">
                            <template x-for="permissionId in selectedPermissions" :key="permissionId">
                                <div class="text-xs text-gray-500 dark:text-neutral-500 mb-1" 
                                     x-text="document.querySelector(`input[value='${permissionId}']`)?.getAttribute('data-name')">
                                </div>
                            </template>
                        </div>
                    </div>
                    <div x-show="selectedPermissions.length === 0" class="text-center py-4">
                        <p class="text-sm text-gray-500 dark:text-neutral-500">No permissions selected</p>
                    </div>
                </x-admin.card>
            </div>

            <!-- Permissions -->
            <div class="lg:col-span-2">
                <x-admin.form-section 
                    title="Role Permissions"
                    description="Select permissions for this role. Permissions are grouped by module."
                >
                    @if($role->name === 'super-admin' && !auth()->user()->hasRole('super-admin'))
                        <x-admin.alert type="warning">
                            <strong>Limited Access:</strong> Super-admin role permissions can only be modified by other super-administrators.
                        </x-admin.alert>
                    @endif

                    <div class="space-y-6">
                        @foreach($permissions as $module => $modulePermissions)
                        <div class="border border-gray-200 dark:border-neutral-700 rounded-lg">
                            <div class="bg-gray-50 dark:bg-neutral-800 px-4 py-3 border-b border-gray-200 dark:border-neutral-700">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-medium text-gray-900 dark:text-white capitalize">
                                        {{ str_replace('-', ' ', $module) }} 
                                        <span class="text-sm text-gray-500 dark:text-neutral-400 font-normal">
                                            ({{ $modulePermissions->count() }} permissions)
                                        </span>
                                    </h4>
                                    <label class="flex items-center space-x-2">
                                        <input 
                                            type="checkbox" 
                                            class="module-toggle rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                            data-module="{{ $module }}"
                                            @change="toggleModulePermissions($event, '{{ $module }}')"
                                            {{ $role->name === 'super-admin' && !auth()->user()->hasRole('super-admin') ? 'disabled' : '' }}
                                        >
                                        <span class="text-sm text-gray-600 dark:text-neutral-400">Select All</span>
                                    </label>
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($modulePermissions as $permission)
                                    <label class="flex items-center space-x-3 p-2 rounded hover:bg-gray-50 dark:hover:bg-neutral-700">
                                        <input 
                                            type="checkbox" 
                                            name="permissions[]" 
                                            value="{{ $permission->id }}"
                                            data-name="{{ $permission->name }}"
                                            data-module="{{ $module }}"
                                            class="permission-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                            @change="updateSelectedPermissions()"
                                            {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}
                                            {{ $role->name === 'super-admin' && !auth()->user()->hasRole('super-admin') ? 'disabled' : '' }}
                                        >
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
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </x-admin.form-section>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        function toggleModulePermissions(event, module) {
            const isChecked = event.target.checked;
            const moduleCheckboxes = document.querySelectorAll(`input[data-module="${module}"].permission-checkbox:not([disabled])`);
            
            moduleCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });

            updateSelectedPermissions();
        }

        function updateSelectedPermissions() {
            const checkedBoxes = document.querySelectorAll('.permission-checkbox:checked');
            const selectedPermissions = Array.from(checkedBoxes).map(cb => parseInt(cb.value));
            
            // Update Alpine.js data
            window.dispatchEvent(new CustomEvent('permissions-updated', {
                detail: { selected: selectedPermissions }
            }));

            // Update module toggles
            document.querySelectorAll('.module-toggle').forEach(toggle => {
                const module = toggle.dataset.module;
                const moduleCheckboxes = document.querySelectorAll(`input[data-module="${module}"].permission-checkbox:not([disabled])`);
                const checkedModuleBoxes = document.querySelectorAll(`input[data-module="${module}"].permission-checkbox:checked`);
                
                if (checkedModuleBoxes.length === moduleCheckboxes.length && moduleCheckboxes.length > 0) {
                    toggle.checked = true;
                    toggle.indeterminate = false;
                } else if (checkedModuleBoxes.length > 0) {
                    toggle.checked = false;
                    toggle.indeterminate = true;
                } else {
                    toggle.checked = false;
                    toggle.indeterminate = false;
                }
            });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateSelectedPermissions();
            
            // Listen for permission updates
            window.addEventListener('permissions-updated', function(e) {
                const component = Alpine.$data(document.querySelector('[x-data]'));
                if (component) {
                    component.selectedPermissions = e.detail.selected;
                }
            });
        });
    </script>
    @endpush
</x-layouts.admin>