{{-- resources/views/admin/roles/create.blade.php --}}
<x-layouts.admin title="Create New Role">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Roles Management' => route('admin.roles.index'),
        'Create Role' => ''
    ]" />

    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Role</h1>
            <p class="text-sm text-gray-600 dark:text-neutral-400">Define a new role with specific permissions</p>
        </div>
        <x-admin.button 
            href="{{ route('admin.roles.index') }}" 
            color="light"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>'
        >
            Back to Roles
        </x-admin.button>
    </div>

    <form action="{{ route('admin.roles.store') }}" method="POST" x-data="{ selectedPermissions: [] }">
        @csrf
        
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
                            :value="old('name')"
                            required
                            placeholder="Enter role name (e.g., Content Manager)"
                            helper="Role name should be descriptive and unique"
                        />

                        <x-admin.select
                            label="Guard"
                            name="guard_name"
                            :value="old('guard_name', 'web')"
                            required
                            :options="$guards->mapWithKeys(fn($guard) => [$guard => ucfirst($guard)])->toArray()"
                            helper="Select the authentication guard for this role"
                        />

                        <x-admin.textarea
                            label="Description"
                            name="description"
                            :value="old('description')"
                            rows="3"
                            placeholder="Describe what this role is for and what access it provides..."
                            helper="Optional description to help identify the role's purpose"
                        />

                        <x-admin.select
                            label="Role Color"
                            name="color"
                            :value="old('color')"
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
                                Create Role
                            </x-admin.button>
                        </div>
                    </x-slot>
                </x-admin.form-section>

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
                                            {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}
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
            const moduleCheckboxes = document.querySelectorAll(`input[data-module="${module}"].permission-checkbox`);
            
            moduleCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });

            updateSelectedPermissions();
        }

        function updateSelectedPermissions() {
            const checkedBoxes = document.querySelectorAll('.permission-checkbox:checked');
            const selectedPermissions = Array.from(checkedBoxes).map(cb => cb.value);
            
            // Update Alpine.js data
            window.dispatchEvent(new CustomEvent('permissions-updated', {
                detail: { selected: selectedPermissions }
            }));

            // Update module toggles
            document.querySelectorAll('.module-toggle').forEach(toggle => {
                const module = toggle.dataset.module;
                const moduleCheckboxes = document.querySelectorAll(`input[data-module="${module}"].permission-checkbox`);
                const checkedModuleBoxes = document.querySelectorAll(`input[data-module="${module}"].permission-checkbox:checked`);
                
                if (checkedModuleBoxes.length === moduleCheckboxes.length) {
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