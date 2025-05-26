{{-- resources/views/admin/certifications/show.blade.php --}}
<x-layouts.admin title="View Certification">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Certifications' => route('admin.certifications.index'), 
        $certification->name => ''
    ]" />

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $certification->name }}</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Issued by {{ $certification->issuer }}
                @if($certification->issue_date)
                    on {{ $certification->issue_date->format('M j, Y') }}
                @endif
            </p>
        </div>
        <div class="flex gap-3">
            <x-admin.button color="light" href="{{ route('admin.certifications.index') }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to List
            </x-admin.button>
            
            <x-admin.button color="primary" href="{{ route('admin.certifications.edit', $certification) }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Certification
            </x-admin.button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Certificate Image -->
            @if($certification->image)
                <x-admin.card title="Certificate Image">
                    <div class="text-center">
                        <img src="{{ asset('storage/' . $certification->image) }}" 
                             alt="{{ $certification->name }}" 
                             class="w-full max-w-2xl mx-auto rounded-lg shadow-lg border">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">
                            Click image to view full size
                        </p>
                    </div>
                </x-admin.card>
            @endif

            <!-- Description -->
            @if($certification->description)
                <x-admin.card title="Description">
                    <div class="prose dark:prose-invert max-w-none">
                        {!! nl2br(e($certification->description)) !!}
                    </div>
                </x-admin.card>
            @endif

            <!-- Certification Details -->
            <x-admin.card title="Certification Details">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Certification Name</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $certification->name }}</p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Issuing Organization</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $certification->issuer }}</p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Issue Date</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            @if($certification->issue_date)
                                {{ $certification->issue_date->format('F j, Y') }}
                                <span class="text-gray-500">({{ $certification->issue_date->diffForHumans() }})</span>
                            @else
                                <span class="text-gray-400">Not specified</span>
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Expiry Date</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            @if($certification->expiry_date)
                                {{ $certification->expiry_date->format('F j, Y') }}
                                @if($certification->expiry_date->isPast())
                                    <x-admin.badge type="danger" size="sm" class="ml-2">Expired</x-admin.badge>
                                @elseif($certification->expiry_date->diffInDays() <= 30)
                                    <x-admin.badge type="warning" size="sm" class="ml-2">Expiring Soon</x-admin.badge>
                                @else
                                    <x-admin.badge type="success" size="sm" class="ml-2">Valid</x-admin.badge>
                                @endif
                            @else
                                <span class="text-gray-400">No expiry date</span>
                                <x-admin.badge type="info" size="sm" class="ml-2">Permanent</x-admin.badge>
                            @endif
                        </p>
                    </div>
                </div>
            </x-admin.card>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Information -->
            <x-admin.card title="Status">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Status</span>
                        <x-admin.badge :type="$certification->is_active ? 'success' : 'danger'">
                            {{ $certification->is_active ? 'Active' : 'Inactive' }}
                        </x-admin.badge>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Sort Order</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $certification->sort_order }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Validity</span>
                        @if($certification->expiry_date)
                            @if($certification->expiry_date->isPast())
                                <x-admin.badge type="danger">Expired</x-admin.badge>
                            @else
                                <x-admin.badge type="success">Valid</x-admin.badge>
                            @endif
                        @else
                            <x-admin.badge type="info">Permanent</x-admin.badge>
                        @endif
                    </div>
                </div>
            </x-admin.card>

            <!-- Quick Actions -->
            <x-admin.card title="Quick Actions">
                <div class="space-y-3">
                    <form action="{{ route('admin.certifications.toggle-active', $certification) }}" method="POST" class="w-full">
                        @csrf
                        <x-admin.button type="submit" :color="$certification->is_active ? 'warning' : 'success'" class="w-full">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="{{ $certification->is_active ? 'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21' : 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' }}" />
                            </svg>
                            {{ $certification->is_active ? 'Deactivate' : 'Activate' }}
                        </x-admin.button>
                    </form>

                    <x-admin.button color="danger" type="button" onclick="confirmDelete()" class="w-full">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete Certification
                    </x-admin.button>
                </div>
            </x-admin.card>

            <!-- Timeline -->
            <x-admin.card title="Timeline">
                <div class="space-y-4">
                    @if($certification->issue_date)
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Issued</p>
                                <p class="text-xs text-gray-500">{{ $certification->issue_date->format('M j, Y') }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Added to System</p>
                            <p class="text-xs text-gray-500">{{ $certification->created_at->format('M j, Y') }}</p>
                        </div>
                    </div>

                    @if($certification->updated_at != $certification->created_at)
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-2 h-2 bg-yellow-500 rounded-full mt-2"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Last Updated</p>
                                <p class="text-xs text-gray-500">{{ $certification->updated_at->format('M j, Y') }}</p>
                            </div>
                        </div>
                    @endif

                    @if($certification->expiry_date)
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-2 h-2 {{ $certification->expiry_date->isPast() ? 'bg-red-500' : 'bg-orange-500' }} rounded-full mt-2"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $certification->expiry_date->isPast() ? 'Expired' : 'Expires' }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $certification->expiry_date->format('M j, Y') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </x-admin.card>
        </div>
    </div>

    <!-- Hidden Delete Form -->
    <form id="delete-form" action="{{ route('admin.certifications.destroy', $certification) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    @if($certification->image)
        <!-- Image Modal for full size view -->
        <div id="image-modal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden items-center justify-center p-4" onclick="closeImageModal()">
            <div class="max-w-4xl max-h-full">
                <img src="{{ asset('storage/' . $certification->image) }}" 
                     alt="{{ $certification->name }}" 
                     class="max-w-full max-h-full object-contain rounded-lg">
            </div>
            <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white text-2xl hover:text-gray-300">
                ×
            </button>
        </div>
    @endif

    @push('scripts')
    <script>
        // Delete confirmation
        function confirmDelete() {
            if (confirm('Are you sure you want to delete this certification? This action cannot be undone.')) {
                document.getElementById('delete-form').submit();
            }
        }

        @if($certification->image)
        // Image modal functionality
        document.querySelector('img[alt="{{ $certification->name }}"]').addEventListener('click', function() {
            document.getElementById('image-modal').classList.remove('hidden');
            document.getElementById('image-modal').classList.add('flex');
        });

        function closeImageModal() {
            document.getElementById('image-modal').classList.add('hidden');
            document.getElementById('image-modal').classList.remove('flex');
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
        @endif
    </script>
    @endpush
</x-layouts.admin>