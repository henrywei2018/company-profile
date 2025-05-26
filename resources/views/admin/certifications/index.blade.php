{{-- resources/views/admin/certifications/index.blade.php --}}
<x-layouts.admin title="Certifications Management">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Certifications Management</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage company certifications and credentials</p>
        </div>
        <div class="flex gap-3">
            <x-admin.button color="primary" href="{{ route('admin.certifications.create') }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add New Certification
            </x-admin.button>
        </div>
    </div>

    <!-- Filters -->
    <x-admin.filter action="{{ route('admin.certifications.index') }}" resetRoute="{{ route('admin.certifications.index') }}">
        <x-admin.select name="status" label="Status" 
            :options="['active' => 'Active', 'inactive' => 'Inactive']" 
            :selected="request('status')" placeholder="All Statuses" />

        <x-admin.select name="valid" label="Validity" 
            :options="['valid' => 'Valid', 'expired' => 'Expired']" 
            :selected="request('valid')" placeholder="All Certifications" />

        <x-admin.input name="search" label="Search" placeholder="Search certifications..." :value="request('search')" />
    </x-admin.filter>

    <!-- Certifications Table -->
    <x-admin.card noPadding>
        <x-slot name="title">
            <div class="flex items-center justify-between w-full">
                <span>Certifications ({{ $certifications->total() }})</span>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Showing {{ $certifications->firstItem() ?? 0 }} to {{ $certifications->lastItem() ?? 0 }} of {{ $certifications->total() }}
                    results
                </div>
            </div>
        </x-slot>

        <x-admin.data-table>
            <x-slot name="columns">
                <x-admin.table-column sortable field="name" :direction="request('sort') === 'name' ? request('direction') : null">
                    Certification
                </x-admin.table-column>
                <x-admin.table-column>Issuer</x-admin.table-column>
                <x-admin.table-column sortable field="issue_date" :direction="request('sort') === 'issue_date' ? request('direction') : null">
                    Issue Date
                </x-admin.table-column>
                <x-admin.table-column sortable field="expiry_date" :direction="request('sort') === 'expiry_date' ? request('direction') : null">
                    Expiry Date
                </x-admin.table-column>
                <x-admin.table-column>Status</x-admin.table-column>
                <x-admin.table-column class="text-center">Actions</x-admin.table-column>
            </x-slot>

            @forelse($certifications as $certification)
                <x-admin.table-row>
                    <x-admin.table-cell highlight>
                        <div class="flex items-center gap-3">
                            @if ($certification->image)
                                <img src="{{ asset('storage/' . $certification->image) }}" alt="{{ $certification->name }}"
                                    class="w-12 h-12 rounded-lg object-cover">
                            @else
                                <div
                                    class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-white line-clamp-1">
                                    {{ $certification->name }}
                                </h3>
                                @if ($certification->description)
                                    <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-1 mt-1">
                                        {{ \Illuminate\Support\Str::limit($certification->description, 50) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </x-admin.table-cell>

                    <x-admin.table-cell>
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $certification->issuer }}
                        </div>
                    </x-admin.table-cell>

                    <x-admin.table-cell>
                        @if ($certification->issue_date)
                            <div class="text-sm">
                                <div class="text-gray-900 dark:text-white">
                                    {{ $certification->issue_date->format('M j, Y') }}
                                </div>
                            </div>
                        @else
                            <span class="text-sm text-gray-400">Not specified</span>
                        @endif
                    </x-admin.table-cell>

                    <x-admin.table-cell>
                        @if ($certification->expiry_date)
                            <div class="text-sm">
                                <div class="text-gray-900 dark:text-white">
                                    {{ $certification->expiry_date->format('M j, Y') }}
                                </div>
                                @if ($certification->expiry_date->isPast())
                                    <x-admin.badge type="danger" size="sm">Expired</x-admin.badge>
                                @elseif ($certification->expiry_date->diffInDays() <= 30)
                                    <x-admin.badge type="warning" size="sm">Expiring Soon</x-admin.badge>
                                @endif
                            </div>
                        @else
                            <span class="text-sm text-gray-400">No Expiry</span>
                        @endif
                    </x-admin.table-cell>

                    <x-admin.table-cell>
                        <div class="flex flex-col gap-1">
                            <x-admin.badge :type="$certification->is_active ? 'success' : 'danger'" size="sm">
                                {{ $certification->is_active ? 'Active' : 'Inactive' }}
                            </x-admin.badge>
                        </div>
                    </x-admin.table-cell>

                    <x-admin.table-cell>
                        <div class="flex items-center space-x-2">
                            <!-- View Button -->
                            <div class="relative group">
                                <a href="{{ route('admin.certifications.show', $certification) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors dark:text-neutral-400 dark:hover:text-blue-400 dark:hover:bg-blue-900/30">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <!-- Tooltip -->
                                <div
                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 whitespace-nowrap z-50 dark:bg-gray-700">
                                    View Certification
                                    <div
                                        class="absolute top-full left-1/2 transform -translate-x-1/2 w-2 h-2 bg-gray-900 rotate-45 dark:bg-gray-700">
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Button -->
                            <div class="relative group">
                                <a href="{{ route('admin.certifications.edit', $certification) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-md transition-colors dark:text-neutral-400 dark:hover:text-green-400 dark:hover:bg-green-900/30">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <!-- Tooltip -->
                                <div
                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 whitespace-nowrap z-50 dark:bg-gray-700">
                                    Edit Certification
                                    <div
                                        class="absolute top-full left-1/2 transform -translate-x-1/2 w-2 h-2 bg-gray-900 rotate-45 dark:bg-gray-700">
                                    </div>
                                </div>
                            </div>

                            <!-- Toggle Status Button -->
                            <div class="relative group">
                                <form action="{{ route('admin.certifications.toggle-active', $certification) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center justify-center w-8 h-8 text-gray-600 hover:text-amber-600 hover:bg-amber-50 rounded-md transition-colors dark:text-neutral-400 dark:hover:text-amber-400 dark:hover:bg-amber-900/30">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="{{ $certification->is_active ? 'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21' : 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' }}" />
                                        </svg>
                                    </button>
                                </form>
                                <!-- Tooltip -->
                                <div
                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 whitespace-nowrap z-50 dark:bg-gray-700">
                                    {{ $certification->is_active ? 'Deactivate' : 'Activate' }}
                                    <div
                                        class="absolute top-full left-1/2 transform -translate-x-1/2 w-2 h-2 bg-gray-900 rotate-45 dark:bg-gray-700">
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Button -->
                            <div class="relative group">
                                <form action="{{ route('admin.certifications.destroy', $certification) }}" method="POST"
                                    class="inline"
                                    onsubmit="return confirm('Are you sure you want to delete this certification? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center justify-center w-8 h-8 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-md transition-colors dark:text-neutral-400 dark:hover:text-red-400 dark:hover:bg-red-900/30">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                                <!-- Tooltip -->
                                <div
                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 whitespace-nowrap z-50 dark:bg-gray-700">
                                    Delete Certification
                                    <div
                                        class="absolute top-full left-1/2 transform -translate-x-1/2 w-2 h-2 bg-gray-900 rotate-45 dark:bg-gray-700">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-admin.table-cell>
                </x-admin.table-row>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12">
                        <x-admin.empty-state title="No certifications found"
                            description="You haven't added any certifications yet or no certifications match your search criteria."
                            :actionText="request()->hasAny(['search', 'status', 'valid'])
                                ? null
                                : 'Add Your First Certification'" :actionUrl="request()->hasAny(['search', 'status', 'valid'])
                                ? null
                                : route('admin.certifications.create')"
                            icon='<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>' />
                    </td>
                </tr>
            @endforelse
        </x-admin.data-table>

        @if ($certifications->hasPages())
            <x-slot name="footer">
                <x-admin.pagination :paginator="$certifications" :appends="request()->query()" />
            </x-slot>
        @endif
    </x-admin.card>
</x-layouts.admin>