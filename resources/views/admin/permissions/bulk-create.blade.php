{{-- resources/views/admin/permissions/bulk-create.blade.php --}}
<x-layouts.admin title="Bulk Create Permissions">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Permissions Management' => route('admin.permissions.index'),
        'Bulk Create' => ''
    ]" />

    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Bulk Create Permissions</h1>
            <p class="text-sm text-gray-600 dark:text-neutral-400">Create multiple permissions for a module at once</p>
        </div>
        <x-admin.button 
            href="{{ route('admin.permissions.index') }}" 
            color="light"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>'
        >
            Back to Permissions
        </x-admin.button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Form -->
        <div>
            <x-admin.form-section 
                title="Permission Generator"
                description="Select a module and actions to generate multiple permissions automatically"
            >
                <form action="{{ route('admin.permissions.bulk-store') }}" method="POST" x-data="permissionGenerator()">
                    @csrf
                    
                    <div class="space-y-6">
                        <!-- Module Selection -->
                        <div>
                            <x-admin.select
                                label="Module"
                                name="module"
                                :value="old('module', request('module'))"
                                required
                                :options="$modules->mapWithKeys(fn($module) => [$module => ucwords(str_replace('-', ' ', $module))])->toArray()"
                                placeholder="Select a module"
                                helper="Choose the module for which you want to create permissions"
                                x-model="selectedModule"
                                @change="updatePreview()"
                            />
                        </div>

                        <!-- Guard Selection -->
                        <div>
                            <x-admin.select
                                label="Guard"
                                name="guard_name"
                                :value="old('guard_name', 'web')"
                                required
                                :options="$guards->mapWithKeys(fn($guard) => [$guard => ucfirst($guard)])->toArray()"
                                helper="Select the authentication guard for these permissions"
                            />
                        </div>

                        <!-- Actions Selection -->
                        <div>
                            <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-neutral-300">
                                Actions <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-2">
                                @foreach($actions as $action => $label)
                                <label class="flex items-center space-x-3 p-3 border border-gray-200 dark:border-neutral-700 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-700 cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        name="actions[]" 
                                        value="{{ $action }}"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        {{ in_array($action, old('actions', [])) ? 'checked' : '' }}
                                        x-model="selectedActions"
                                        @change="updatePreview()"
                                    >
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $label }}</div>
                                        <div class="text-xs text-gray-500 dark:text-neutral-500">{{ $action }}</div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-500 dark:text-neutral-400">Select the actions you want to create permissions for</p>
                            @error('actions')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Quick Select Buttons -->
                        <div class="flex flex-wrap gap-2">
                            <x-admin.button 
                                type="button" 
                                color="light" 
                                size="sm"
                                @click="selectActions(['view', 'create', 'edit', 'delete'])"
                            >
                                CRUD Actions
                            </x-admin.button>
                            <x-admin.button 
                                type="button" 
                                color="light" 
                                size="sm"
                                @click="selectActions(['view', 'create', 'edit', 'delete', 'manage'])"
                            >
                                All Common
                            </x-admin.button>
                            <x-admin.button 
                                type="button" 
                                color="light" 
                                size="sm"
                                @click="selectAllActions()"
                            >
                                Select All
                            </x-admin.button>
                            <x-admin.button 
                                type="button" 
                                color="light" 
                                size="sm"
                                @click="clearActions()"
                            >
                                Clear All
                            </x-admin.button>
                        </div>
                    </div>

                    <x-slot name="footer">
                        <div class="flex items-center justify-end space-x-3">
                            <x-admin.button type="button" color="light" onclick="history.back()">
                                Cancel
                            </x-admin.button>
                            <x-admin.button 
                                type="submit" 
                                color="primary"
                                x-bind:disabled="!selectedModule || selectedActions.length === 0"
                            >
                                Create Permissions
                            </x-admin.button>
                        </div>
                    </x-slot>
                </form>
            </x-admin.form-section>
        </div>

        <!-- Preview -->
        <div>
            <x-admin.card title="Permission Preview" subtitle="Preview of permissions that will be created">
                <div x-show="!selectedModule || selectedActions.length === 0" class="text-center py-8">
                    <div class="text-gray-400 dark:text-neutral-500 mb-2">
                        <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-neutral-400">Select a module and actions to preview permissions</p>
                </div>

                <div x-show="selectedModule && selectedActions.length > 0" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-600 dark:text-neutral-400">
                            <span x-text="selectedActions.length"></span> permissions will be created for 
                            <span class="font-medium" x-text="selectedModule ? selectedModule.replace('-', ' ') : ''"></span> module
                        </p>
                        <x-admin.badge type="info" x-text="selectedActions.length + ' permissions'"></x-admin.badge>
                    </div>

                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        <template x-for="action in selectedActions" :key="action">
                            <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-md border border-green-200 dark:border-green-800">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white" x-text="action + ' ' + (selectedModule || '')"></div>
                                        <div class="text-xs text-gray-500 dark:text-neutral-500">Permission Name</div>
                                    </div>
                                </div>
                                <x-admin.badge type="success" size="sm">New</x-admin.badge>
                            </div>
                        </template>
                    </div>
                </div>
            </x-admin.card>

            <!-- Module Info -->
            <x-admin.card title="Module Information" class="mt-6">
                <div x-show="!selectedModule" class="text-center py-4">
                    <p class="text-sm text-gray-500 dark:text-neutral-400">Select a module to see information</p>
                </div>

                <div x-show="selectedModule" class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-neutral-400">Module Name</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="selectedModule ? selectedModule.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase()) : ''"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-neutral-400">Selected Actions</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="selectedActions.length"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-neutral-400">Guard</span>
                        <x-admin.badge type="info" size="sm">web</x-admin.badge>
                    </div>
                </div>
            </x-admin.card>

            <!-- Help -->
            <x-admin.help-text type="info" title="Bulk Permission Creation" class="mt-6">
                <ul class="text-sm space-y-1">
                    <li>• Select a module to organize related permissions</li>
                    <li>• Choose actions that make sense for your module</li>
                    <li>• Permissions will be named as "action module" (e.g., "view users")</li>
                    <li>• Existing permissions will be skipped automatically</li>
                </ul>
            </x-admin.help-text>
        </div>
    </div>

    @push('scripts')
    <script>
        function permissionGenerator() {
            return {
                selectedModule: @js(old('module', request('module')) ?? ''),
                selectedActions: @js(old('actions', [])),
                
                init() {
                    this.updatePreview();
                },
                
                updatePreview() {
                    // This method is called whenever module or actions change
                    // The preview is handled by Alpine.js reactive data
                },
                
                selectActions(actions) {
                    this.selectedActions = actions;
                    // Update checkboxes
                    document.querySelectorAll('input[name="actions[]"]').forEach(checkbox => {
                        checkbox.checked = actions.includes(checkbox.value);
                    });
                    this.updatePreview();
                },
                
                selectAllActions() {
                    const allActions = @js($actions->keys()->toArray());
                    this.selectActions(allActions);
                },
                
                clearActions() {
                    this.selectedActions = [];
                    document.querySelectorAll('input[name="actions[]"]').forEach(checkbox => {
                        checkbox.checked = false;
                    });
                    this.updatePreview();
                }
            }
        }
    </script>
    @endpush
</x-layouts.admin>