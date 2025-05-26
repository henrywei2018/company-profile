{{-- resources/views/admin/certifications/index.blade.php --}}
<x-layouts.admin title="Certifications Management">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Certifications' => '']" />

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Certifications</h1>
            <p class="text-sm text-gray-600 dark:text-neutral-400">Manage company certifications and credentials</p>
        </div>
        <div class="flex items-center gap-3">
            <x-admin.button href="{{ route('admin.certifications.create') }}" color="primary"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />'>
                Add Certification
            </x-admin.button>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl shadow-sm mb-6">
        <div class="p-4 border-b border-gray-200 dark:border-neutral-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Filters</h3>
        </div>
        <div class="p-4">
            <form method="GET" action="{{ route('admin.certifications.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Search by name or issuer..."
                        class="w-full px-3 py-2 border border-gray-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-800 dark:text-white">
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-800 dark:text-white">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Validity Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">Validity</label>
                    <select name="valid" class="w-full px-3 py-2 border border-gray-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-neutral-800 dark:text-white">
                        <option value="">All Certifications</option>
                        <option value="valid" {{ request('valid') === 'valid' ? 'selected' : '' }}>Valid</option>
                        <option value="expired" {{ request('valid') === 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex items-end gap-2">
                    <x-admin.button type="submit" color="info" size="sm"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />'>
                        Filter
                    </x-admin.button>
                    <x-admin.button type="button" color="light" size="sm" onclick="clearFilters()"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />'>
                        Clear
                    </x-admin.button>
                </div>
            </form>
        </div>
    </div>

    <!-- Certifications Table -->
    <div class="card-with-table">
        <!-- Table Header -->
        <div class="table-header-actions">
            <div class="left-actions">
                <span class="text-sm font-medium text-gray-700 dark:text-neutral-300">
                    {{ $certifications->total() }} certification(s) found
                </span>
            </div>
            <div class="right-info">
                <span class="text-sm text-gray-600 dark:text-neutral-400">
                    Page {{ $certifications->currentPage() }} of {{ $certifications->lastPage() }}
                </span>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th class="w-8">
                            <input type="checkbox" id="select-all" onchange="toggleSelectAll(this)"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:bg-neutral-700 dark:border-neutral-600">
                        </th>
                        <th>Certification</th>
                        <th>Issuer</th>
                        <th>Validity Period</th>
                        <th>Status</th>
                        <th>Sort Order</th>
                        <th class="w-32">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($certifications as $certification)
                        <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700">
                            <td>
                                <input type="checkbox" name="selected_certifications[]" value="{{ $certification->id }}"
                                    class="certification-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:bg-neutral-700 dark:border-neutral-600">
                            </td>
                            <td>
                                <div class="flex items-center space-x-3">
                                    @if($certification->image)
                                        <img src="{{ Storage::url($certification->image) }}" alt="{{ $certification->name }}" 
                                            class="w-10 h-10 rounded-lg object-cover">
                                    @else
                                        <div class="w-10 h-10 bg-gray-100 dark:bg-neutral-700 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $certification->name }}</p>
                                        @if($certification->description)
                                            <p class="text-sm text-gray-500 dark:text-neutral-400">{{ Str::limit($certification->description, 50) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-sm text-gray-900 dark:text-white">{{ $certification->issuer }}</span>
                            </td>
                            <td>
                                <div class="text-sm">
                                    @if($certification->issue_date)
                                        <div class="text-gray-900 dark:text-white">
                                            <strong>Issued:</strong> {{ $certification->issue_date->format('M d, Y') }}
                                        </div>
                                    @endif
                                    @if($certification->expiry_date)
                                        <div class="text-gray-600 dark:text-neutral-400">
                                            <strong>Expires:</strong> {{ $certification->expiry_date->format('M d, Y') }}
                                            @if($certification->expiry_date->isPast())
                                                <span class="text-red-600 font-medium">(Expired)</span>
                                            @elseif($certification->expiry_date->diffInDays() <= 30)
                                                <span class="text-amber-600 font-medium">(Expires Soon)</span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-green-600 dark:text-green-400">
                                            <strong>Validity:</strong> Permanent
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="flex flex-col space-y-1">
                                    @if($certification->is_active)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                            Inactive
                                        </span>
                                    @endif
                                    
                                    @if($certification->expiry_date && $certification->expiry_date->isPast())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400">
                                            Expired
                                        </span>
                                    @elseif($certification->expiry_date && $certification->expiry_date->diffInDays() <= 30)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/20 dark:text-amber-400">
                                            Expires Soon
                                        </span>
                                    @elseif(!$certification->expiry_date)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                            Permanent
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                            Valid
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="text-sm text-gray-600 dark:text-neutral-400">{{ $certification->sort_order }}</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.certifications.show', $certification) }}" 
                                        class="btn-icon" title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.certifications.edit', $certification) }}" 
                                        class="btn-icon" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.certifications.toggle-active', $certification) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn-icon" title="{{ $certification->is_active ? 'Deactivate' : 'Activate' }}">
                                            @if($certification->is_active)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.certifications.destroy', $certification) }}" method="POST" 
                                        class="inline" onsubmit="return confirm('Are you sure you want to delete this certification?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon text-red-600 hover:text-red-700" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-gray-500 dark:text-neutral-400">No certifications found</p>
                                    <x-admin.button href="{{ route('admin.certifications.create') }}" color="primary" size="sm">
                                        Add First Certification
                                    </x-admin.button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($certifications->hasPages())
            <div class="table-pagination">
                {{ $certifications->links() }}
            </div>
        @endif
    </div>

    <!-- Bulk Actions (Hidden by default) -->
    <div id="bulk-actions" class="fixed bottom-4 left-1/2 transform -translate-x-1/2 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-lg shadow-lg p-4 hidden">
        <div class="flex items-center space-x-4">
            <span id="selected-count" class="text-sm font-medium text-gray-700 dark:text-neutral-300">0 selected</span>
            <div class="flex space-x-2">
                <x-admin.button type="button" color="success" size="sm" onclick="bulkActivate()">
                    Activate
                </x-admin.button>
                <x-admin.button type="button" color="warning" size="sm" onclick="bulkDeactivate()">
                    Deactivate
                </x-admin.button>
                <x-admin.button type="button" color="danger" size="sm" onclick="bulkDelete()">
                    Delete
                </x-admin.button>
                <x-admin.button type="button" color="light" size="sm" onclick="clearSelection()">
                    Cancel
                </x-admin.button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Clear filters
        function clearFilters() {
            window.location.href = '{{ route("admin.certifications.index") }}';
        }

        // Select all functionality
        function toggleSelectAll(checkbox) {
            const checkboxes = document.querySelectorAll('.certification-checkbox');
            checkboxes.forEach(cb => cb.checked = checkbox.checked);
            updateBulkActions();
        }

        // Update bulk actions visibility
        function updateBulkActions() {
            const selected = document.querySelectorAll('.certification-checkbox:checked');
            const bulkActions = document.getElementById('bulk-actions');
            const selectedCount = document.getElementById('selected-count');
            
            if (selected.length > 0) {
                bulkActions.classList.remove('hidden');
                selectedCount.textContent = `${selected.length} selected`;
            } else {
                bulkActions.classList.add('hidden');
            }
            
            // Update select all checkbox
            const selectAll = document.getElementById('select-all');
            const allCheckboxes = document.querySelectorAll('.certification-checkbox');
            selectAll.checked = selected.length === allCheckboxes.length && allCheckboxes.length > 0;
            selectAll.indeterminate = selected.length > 0 && selected.length < allCheckboxes.length;
        }

        // Add event listeners to checkboxes
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.certification-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateBulkActions);
            });
        });

        // Clear selection
        function clearSelection() {
            const checkboxes = document.querySelectorAll('.certification-checkbox');
            checkboxes.forEach(cb => cb.checked = false);
            document.getElementById('select-all').checked = false;
            updateBulkActions();
        }

        // Bulk actions
        function bulkActivate() {
            const selected = Array.from(document.querySelectorAll('.certification-checkbox:checked')).map(cb => cb.value);
            if (selected.length === 0) return;
            
            if (confirm(`Activate ${selected.length} certification(s)?`)) {
                // Implementation would go here
                console.log('Bulk activate:', selected);
            }
        }

        function bulkDeactivate() {
            const selected = Array.from(document.querySelectorAll('.certification-checkbox:checked')).map(cb => cb.value);
            if (selected.length === 0) return;
            
            if (confirm(`Deactivate ${selected.length} certification(s)?`)) {
                // Implementation would go here
                console.log('Bulk deactivate:', selected);
            }
        }

        function bulkDelete() {
            const selected = Array.from(document.querySelectorAll('.certification-checkbox:checked')).map(cb => cb.value);
            if (selected.length === 0) return;
            
            if (confirm(`Delete ${selected.length} certification(s)? This action cannot be undone.`)) {
                // Implementation would go here
                console.log('Bulk delete:', selected);
            }
        }
    </script>
    @endpush
</x-layouts.admin>