{{-- resources/views/admin/team-member-departments/create.blade.php --}}
<x-layouts.admin title="Create New Department">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Team Departments' => route('admin.team-member-departments.index'), 'Create New Department' => '']" />

    <form action="{{ route('admin.team-member-departments.store') }}" method="POST" class="space-y-6" id="department-form">
        @csrf

        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Main Content -->
            <div class="flex-1 space-y-6">
                <!-- Basic Information -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Department Information</h3>
                    </x-slot>

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Department Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name"
                                   value="{{ old('name') }}"
                                   placeholder="Enter department name..."
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('name') border-red-500 @enderror"
                                   required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Slug
                            </label>
                            <input type="text" 
                                   name="slug" 
                                   id="slug"
                                   value="{{ old('slug') }}"
                                   placeholder="auto-generated-from-name"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('slug') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave blank to auto-generate from name. Only lowercase letters, numbers, and hyphens allowed.</p>
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Description
                            </label>
                            <textarea name="description" 
                                      id="description"
                                      rows="4"
                                      placeholder="Enter department description..."
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm resize-y focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>
            </div>

            <!-- Sidebar -->
            <div class="w-full lg:w-80 space-y-6">
                <!-- Publishing Options -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Settings</h3>
                    </x-slot>

                    <div class="space-y-4">
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                            </label>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Make this department active immediately</p>
                        </div>

                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Sort Order
                            </label>
                            <input type="number" 
                                   name="sort_order" 
                                   id="sort_order"
                                   value="{{ old('sort_order') }}"
                                   placeholder="Auto-assigned if empty"
                                   min="0"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('sort_order') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Lower numbers appear first</p>
                            @error('sort_order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>

                <!-- Guidelines -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Guidelines</h3>
                    </x-slot>

                    <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                        <div class="flex items-start">
                            <svg class="w-4 h-4 text-blue-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Use clear, descriptive names for departments</span>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-4 h-4 text-blue-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Department names should be unique</span>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-4 h-4 text-blue-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Descriptions help organize team members</span>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-4 h-4 text-blue-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Sort order controls display sequence</span>
                        </div>
                    </div>
                </x-admin.card>

                <!-- Department Preview -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Preview</h3>
                    </x-slot>

                    <div id="department-preview" class="space-y-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div>
                                <h4 id="preview-name" class="text-sm font-medium text-gray-900 dark:text-white">
                                    Department Name
                                </h4>
                                <p id="preview-slug" class="text-xs text-gray-500 dark:text-gray-400">
                                    department-slug
                                </p>
                            </div>
                        </div>
                        
                        <p id="preview-description" class="text-sm text-gray-600 dark:text-gray-400">
                            Department description will appear here
                        </p>

                        <div class="flex items-center justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                            <span id="preview-status" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                Active
                            </span>
                            <span id="preview-order" class="text-xs text-gray-500 dark:text-gray-400">
                                Order: Auto
                            </span>
                        </div>
                    </div>
                </x-admin.card>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('admin.team-member-departments.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Cancel
            </a>

            <button type="submit" 
                    class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Create Department
            </button>
        </div>
    </form>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-generate slug from name
            const nameInput = document.getElementById('name');
            const slugInput = document.getElementById('slug');
            
            nameInput.addEventListener('input', function() {
                if (!slugInput.value || slugInput.dataset.autoGenerated) {
                    const slug = generateSlug(this.value);
                    slugInput.value = slug;
                    slugInput.dataset.autoGenerated = 'true';
                    updatePreview();
                }
            });

            // Remove auto-generated flag when user manually edits slug
            slugInput.addEventListener('input', function() {
                if (this.value !== generateSlug(nameInput.value)) {
                    delete this.dataset.autoGenerated;
                }
                updatePreview();
            });

            // Update preview on input changes
            ['name', 'description', 'sort_order'].forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.addEventListener('input', updatePreview);
                }
            });

            // Update preview on checkbox change
            document.querySelector('input[name="is_active"]').addEventListener('change', updatePreview);

            // Character counters
            addCharCounter('name', 255);
            addCharCounter('description', 1000);

            // Form validation
            document.getElementById('department-form').addEventListener('submit', function(e) {
                const name = document.getElementById('name').value.trim();

                if (!name) {
                    e.preventDefault();
                    showNotification('Please enter a department name.', 'error');
                    document.getElementById('name').focus();
                    return;
                }

                // Validate slug format if provided
                const slug = document.getElementById('slug').value.trim();
                if (slug && !isValidSlug(slug)) {
                    e.preventDefault();
                    showNotification('Slug must only contain lowercase letters, numbers, and hyphens.', 'error');
                    document.getElementById('slug').focus();
                    return;
                }
            });

            // Initialize preview
            updatePreview();
        });

        function generateSlug(text) {
            return text
                .toLowerCase()
                .trim()
                .replace(/[^\w\s-]/g, '') // Remove special characters
                .replace(/[\s_-]+/g, '-') // Replace spaces and underscores with hyphens
                .replace(/^-+|-+$/g, ''); // Remove leading/trailing hyphens
        }

        function isValidSlug(slug) {
            return /^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(slug);
        }

        function updatePreview() {
            const name = document.getElementById('name').value || 'Department Name';
            const slug = document.getElementById('slug').value || 'department-slug';
            const description = document.getElementById('description').value || 'Department description will appear here';
            const sortOrder = document.getElementById('sort_order').value || 'Auto';
            const isActive = document.querySelector('input[name="is_active"]').checked;

            document.getElementById('preview-name').textContent = name;
            document.getElementById('preview-slug').textContent = slug;
            document.getElementById('preview-description').textContent = description;
            document.getElementById('preview-order').textContent = `Order: ${sortOrder}`;

            const statusElement = document.getElementById('preview-status');
            if (isActive) {
                statusElement.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100';
                statusElement.textContent = 'Active';
            } else {
                statusElement.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
                statusElement.textContent = 'Inactive';
            }
        }

        function addCharCounter(inputId, maxLength) {
            const input = document.getElementById(inputId);
            if (!input) return;

            const counter = document.createElement('div');
            counter.className = 'text-xs text-gray-500 dark:text-gray-400 mt-1';
            counter.id = inputId + '_counter';

            input.parentNode.appendChild(counter);

            function updateCounter() {
                const remaining = maxLength - input.value.length;
                counter.textContent = `${input.value.length}/${maxLength} characters`;

                if (remaining < 10) {
                    counter.className = 'text-xs text-red-500 mt-1';
                } else {
                    counter.className = 'text-xs text-gray-500 dark:text-gray-400 mt-1';
                }
            }

            input.addEventListener('input', updateCounter);
            updateCounter();
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full shadow-lg rounded-lg p-4 ${getNotificationClasses(type)} transform transition-all duration-300 ease-in-out`;
            notification.innerHTML = `
                <div class="flex">
                    <div class="flex-shrink-0">
                        ${getNotificationIcon(type)}
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium">${message}</p>
                    </div>
                    <div class="ml-auto pl-3">
                        <button onclick="this.closest('.fixed').remove()" class="inline-flex text-current hover:opacity-75">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            `;

            document.body.appendChild(notification);
            setTimeout(() => notification?.remove(), 5000);
        }

        function getNotificationClasses(type) {
            const classes = {
                success: 'bg-green-50 border border-green-200 text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-400',
                error: 'bg-red-50 border border-red-200 text-red-800 dark:bg-red-900/20 dark:border-red-800 dark:text-red-400',
                warning: 'bg-yellow-50 border border-yellow-200 text-yellow-800 dark:bg-yellow-900/20 dark:border-yellow-800 dark:text-yellow-400',
                info: 'bg-blue-50 border border-blue-200 text-blue-800 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-400'
            };
            return classes[type] || classes.info;
        }

        function getNotificationIcon(type) {
            const icons = {
                success: '<svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
                error: '<svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
                warning: '<svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>',
                info: '<svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>'
            };
            return icons[type] || icons.info;
        }
    </script>
    @endpush
</x-layouts.admin>