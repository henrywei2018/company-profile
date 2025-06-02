{{-- resources/views/admin/chat/templates/index.blade.php --}}
<x-layouts.admin title="Chat Templates">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Live Chat' => route('admin.chat.index'), 
        'Chat Templates' => ''
    ]" />

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Chat Templates</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Manage quick response templates for chat operators
            </p>
        </div>
        <div class="flex items-center gap-3">
            <x-admin.button color="primary" href="{{ route('admin.chat.templates.create') }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add Template
            </x-admin.button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <x-admin.stat-card 
            title="Total Templates" 
            :value="$templates->total()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2v0a2 2 0 01-2-2v-5a2 2 0 00-2-2H8z"/>'
            iconColor="text-blue-500" 
            iconBg="bg-blue-100 dark:bg-blue-800/30" />

        <x-admin.stat-card 
            title="Quick Replies" 
            :value="$templates->where('type', 'quick_reply')->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>'
            iconColor="text-green-500" 
            iconBg="bg-green-100 dark:bg-green-800/30" />

        <x-admin.stat-card 
            title="Greetings" 
            :value="$templates->where('type', 'greeting')->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
            iconColor="text-yellow-500" 
            iconBg="bg-yellow-100 dark:bg-yellow-800/30" />

        <x-admin.stat-card 
            title="Auto Responses" 
            :value="$templates->where('type', 'auto_response')->count()"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>'
            iconColor="text-purple-500" 
            iconBg="bg-purple-100 dark:bg-purple-800/30" />
    </div>

    <!-- Templates Table -->
    <x-admin.card>
        <x-slot name="headerActions">
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center space-x-3">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Templates</h3>
                    <x-admin.badge type="info">{{ $templates->total() }} Total</x-admin.badge>
                </div>
                <!-- Filters -->
                <div class="flex items-center space-x-3">
                    <select id="type-filter" class="text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                        <option value="">All Types</option>
                        <option value="greeting">Greetings</option>
                        <option value="quick_reply">Quick Replies</option>
                        <option value="auto_response">Auto Responses</option>
                        <option value="offline">Offline Messages</option>
                    </select>
                    <select id="status-filter" class="text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
        </x-slot>

        <x-admin.data-table>
            <x-slot name="columns">
                <x-admin.table-column>Template</x-admin.table-column>
                <x-admin.table-column>Type</x-admin.table-column>
                <x-admin.table-column>Trigger</x-admin.table-column>
                <x-admin.table-column>Usage Count</x-admin.table-column>
                <x-admin.table-column>Status</x-admin.table-column>
                <x-admin.table-column width="w-32">Actions</x-admin.table-column>
            </x-slot>

            @forelse($templates as $template)
                <x-admin.table-row>
                    <x-admin.table-cell highlight>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $template->name }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-neutral-400">
                                {{ Str::limit($template->message, 80) }}
                            </div>
                        </div>
                    </x-admin.table-cell>

                    <x-admin.table-cell>
                        @php
                            $typeColors = [
                                'greeting' => 'yellow',
                                'quick_reply' => 'green', 
                                'auto_response' => 'purple',
                                'offline' => 'gray'
                            ];
                            $color = $typeColors[$template->type] ?? 'gray';
                        @endphp
                        <x-admin.badge :type="$color" size="sm">
                            {{ ucfirst(str_replace('_', ' ', $template->type)) }}
                        </x-admin.badge>
                    </x-admin.table-cell>

                    <x-admin.table-cell>
                        @if($template->trigger)
                            <code class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 rounded">
                                {{ $template->trigger }}
                            </code>
                        @else
                            <span class="text-gray-400 text-sm">-</span>
                        @endif
                    </x-admin.table-cell>

                    <x-admin.table-cell>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $template->usage_count }}
                            </span>
                            @if($template->usage_count > 0)
                                <span class="text-xs text-gray-500">uses</span>
                            @endif
                        </div>
                    </x-admin.table-cell>

                    <x-admin.table-cell>
                        <button onclick="toggleTemplateStatus({{ $template->id }})"
                                class="inline-flex items-center">
                            @if($template->is_active)
                                <x-admin.badge type="success" size="sm">Active</x-admin.badge>
                            @else
                                <x-admin.badge type="danger" size="sm">Inactive</x-admin.badge>
                            @endif
                        </button>
                    </x-admin.table-cell>

                    <x-admin.table-cell>
                        <div class="relative inline-block text-left">
                            <button type="button" 
                                    class="inline-flex items-center justify-center w-8 h-8 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:hover:bg-neutral-700"
                                    onclick="toggleDropdown('dropdown-template-{{ $template->id }}')">
                                <svg class="w-4 h-4 text-gray-600 dark:text-neutral-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                </svg>
                            </button>

                            <div id="dropdown-template-{{ $template->id }}" 
                                 class="hidden absolute right-0 z-10 mt-2 w-48 origin-top-right bg-white border border-gray-200 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-neutral-800 dark:border-neutral-700">
                                <div class="py-1">
                                    <a href="{{ route('admin.chat.templates.show', $template) }}" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        View Template
                                    </a>
                                    
                                    <a href="{{ route('admin.chat.templates.edit', $template) }}" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit Template
                                    </a>

                                    <button onclick="duplicateTemplate({{ $template->id }})" 
                                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        Duplicate
                                    </button>

                                    <div class="border-t border-gray-100 dark:border-neutral-600"></div>
                                    
                                    <button onclick="deleteTemplate({{ $template->id }})" 
                                            class="flex items-center w-full px-4 py-2 text-sm text-red-700 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </x-admin.table-cell>
                </x-admin.table-row>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12">
                        <x-admin.empty-state title="No templates found"
                            description="No chat templates match your criteria. Create your first template to get started."
                            actionText="Create Template"
                            :actionUrl="route('admin.chat.templates.create')" />
                    </td>
                </tr>
            @endforelse
        </x-admin.data-table>

        @if($templates->hasPages())
            <x-slot name="footer">
                <x-admin.pagination :paginator="$templates" />
            </x-slot>
        @endif
    </x-admin.card>

    @push('scripts')
    <script>
        // Toggle template status
        async function toggleTemplateStatus(templateId) {
            try {
                const response = await fetch(`/admin/chat/templates/${templateId}/toggle-active`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Reload page to show updated status
                    window.location.reload();
                } else {
                    alert('Failed to update template status');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while updating template status');
            }
        }

        // Delete template
        async function deleteTemplate(templateId) {
            if (!confirm('Are you sure you want to delete this template? This action cannot be undone.')) {
                return;
            }

            try {
                const response = await fetch(`/admin/chat/templates/${templateId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Remove row from table or reload page
                    window.location.reload();
                } else {
                    alert('Failed to delete template');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while deleting template');
            }
        }

        // Duplicate template
        function duplicateTemplate(templateId) {
            window.location.href = `/admin/chat/templates/${templateId}/duplicate`;
        }

        // Dropdown toggle
        function toggleDropdown(dropdownId) {
            document.querySelectorAll('[id^="dropdown-template-"]').forEach(dropdown => {
                if (dropdown.id !== dropdownId) {
                    dropdown.classList.add('hidden');
                }
            });

            const dropdown = document.getElementById(dropdownId);
            dropdown.classList.toggle('hidden');
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.relative')) {
                document.querySelectorAll('[id^="dropdown-template-"]').forEach(dropdown => {
                    dropdown.classList.add('hidden');
                });
            }
        });

        // Filters
        document.getElementById('type-filter').addEventListener('change', function() {
            filterTemplates();
        });

        document.getElementById('status-filter').addEventListener('change', function() {
            filterTemplates();
        });

        function filterTemplates() {
            const typeFilter = document.getElementById('type-filter').value;
            const statusFilter = document.getElementById('status-filter').value;
            
            const urlParams = new URLSearchParams(window.location.search);
            
            if (typeFilter) {
                urlParams.set('type', typeFilter);
            } else {
                urlParams.delete('type');
            }
            
            if (statusFilter) {
                urlParams.set('status', statusFilter);
            } else {
                urlParams.delete('status');
            }
            
            window.location.search = urlParams.toString();
        }
    </script>
    @endpush
</x-layouts.admin>