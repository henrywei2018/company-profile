{{-- resources/views/admin/certifications/edit.blade.php --}}
<x-layouts.admin title="Edit Certification">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Certifications' => route('admin.certifications.index'),
        $certification->name => route('admin.certifications.show', $certification),
        'Edit' => '',
    ]" />

    <form action="{{ route('admin.certifications.update', $certification) }}" method="POST" enctype="multipart/form-data"
        class="space-y-6">
        @csrf
        @method('PUT')

        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Main Content -->
            <div class="flex-1 space-y-6">
                <!-- Basic Information -->
                <x-admin.form-section title="Basic Information"
                    description="Update the main details for the certification">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-admin.input name="name" label="Certification Name"
                                placeholder="Enter certification name..." :value="old('name', $certification->name)" required
                                helper="The full name of the certification or credential" />
                        </div>

                        <div class="md:col-span-2">
                            <x-admin.input name="issuer" label="Issuing Organization"
                                placeholder="Enter issuing organization..." :value="old('issuer', $certification->issuer)" required
                                helper="The organization or authority that issued this certification" />
                        </div>

                        <div class="md:col-span-2">
                            <x-admin.textarea name="description" label="Description"
                                placeholder="Describe the certification..." :value="old('description', $certification->description)" rows="4"
                                helper="Optional description of what this certification covers or represents" />
                        </div>
                    </div>
                </x-admin.form-section>

                <!-- Dates -->
                <x-admin.form-section title="Certification Dates"
                    description="Update the validity period of the certification">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-admin.input type="date" name="issue_date" label="Issue Date" :value="old('issue_date', $certification->issue_date?->format('Y-m-d'))"
                            helper="When was this certification issued?" />

                        <x-admin.input type="date" name="expiry_date" label="Expiry Date" :value="old('expiry_date', $certification->expiry_date?->format('Y-m-d'))"
                            helper="When does this certification expire? Leave blank if it doesn't expire" />
                    </div>

                    @if ($certification->expiry_date && $certification->expiry_date->isPast())
                        <x-admin.alert type="warning" class="mt-4">
                            <strong>Notice:</strong> This certification has expired on
                            {{ $certification->expiry_date->format('M j, Y') }}.
                            Consider updating the expiry date or marking it as inactive.
                        </x-admin.alert>
                    @elseif($certification->expiry_date && $certification->expiry_date->diffInDays() <= 30)
                        <x-admin.alert type="info" class="mt-4">
                            <strong>Reminder:</strong> This certification will expire on
                            {{ $certification->expiry_date->format('M j, Y') }}
                            ({{ $certification->expiry_date->diffForHumans() }}).
                        </x-admin.alert>
                    @endif
                </x-admin.form-section>
            </div>

            <!-- Sidebar -->
            <div class="w-full lg:w-80 space-y-6">
                <!-- Status Options -->
                <x-admin.card title="Status">
                    <div class="space-y-4">
                        <x-admin.checkbox name="is_active" label="Active Certification" :checked="old('is_active', $certification->is_active)"
                            helper="Show this certification on the website" />

                        <x-admin.input type="number" name="sort_order" label="Sort Order" :value="old('sort_order', $certification->sort_order)"
                            min="0"
                            helper="Order in which this certification appears (lower numbers appear first)" />
                    </div>
                </x-admin.card>

                <!-- Certificate Image -->
                <x-admin.card title="Certificate Image">
                    <div>
                        @if ($certification->image)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Current File
                                </label>
                                <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded border">
                                    @if (str_ends_with(strtolower($certification->image), '.pdf'))
                                        {{-- PDF Display --}}
                                        <div class="flex items-center">
                                            <svg class="w-8 h-8 text-red-600 mr-3" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" />
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium">PDF Certificate</p>
                                                <a href="{{ asset('storage/' . $certification->image) }}"
                                                    target="_blank" class="text-xs text-blue-600 hover:underline">View
                                                    PDF</a>
                                            </div>
                                        </div>
                                    @else
                                        {{-- Image Display --}}
                                        <div class="flex items-center">
                                            <img src="{{ asset('storage/' . $certification->image) }}"
                                                alt="{{ $certification->name }}"
                                                class="w-16 h-16 object-cover rounded mr-3">
                                            <div>
                                                <p class="text-sm font-medium">Current Image</p>
                                                <p class="text-xs text-gray-500">{{ basename($certification->image) }}
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Replace File Upload Section --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ $certification->image ? 'Replace Certificate File' : 'Certificate File (Image or PDF)' }}
                            </label>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                Upload new certification image or PDF document (Max: 10MB)
                            </p>

                            <!-- Universal File Uploader (following banner pattern) -->
                            <x-universal-file-uploader id="certification-edit-uploader" name="certification_files"
                                :multiple="false" :maxFiles="1" maxFileSize="10MB" :acceptedFileTypes="[
                                    'image/jpeg',
                                    'image/png',
                                    'image/jpg',
                                    'image/gif',
                                    'image/webp',
                                    'application/pdf',
                                ]"
                                uploadEndpoint="{{ route('admin.certifications.temp-upload') }}"
                                deleteEndpoint="{{ route('admin.certifications.temp-delete') }}"
                                dropDescription="Drop new certification file here or click to browse" :instantUpload="true"
                                :singleMode="true" :replaceMode="true" containerClass="mb-4" theme="modern" />

                            <!-- Traditional Upload Fallback -->
                            <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-800 rounded border">
                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">
                                    Or use traditional upload:
                                </p>
                                <input type="file" name="image" accept="image/*,application/pdf"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                            </div>
                        </div>
                    </div>
                </x-admin.card>

                <!-- Certification Statistics -->
                <x-admin.card title="Statistics">
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Created:</span>
                            <span class="font-medium">{{ $certification->created_at->format('M j, Y') }}</span>
                        </div>

                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Last Updated:</span>
                            <span class="font-medium">{{ $certification->updated_at->diffForHumans() }}</span>
                        </div>

                        @if ($certification->issue_date)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Age:</span>
                                <span class="font-medium">{{ $certification->issue_date->diffForHumans() }}</span>
                            </div>
                        @endif

                        @if ($certification->expiry_date)
                            <div class="flex justify-between text-sm">
                                <span
                                    class="text-gray-500 dark:text-gray-400">{{ $certification->expiry_date->isPast() ? 'Expired:' : 'Expires:' }}</span>
                                <span
                                    class="font-medium {{ $certification->expiry_date->isPast() ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $certification->expiry_date->diffForHumans() }}
                                </span>
                            </div>
                        @endif
                    </div>
                </x-admin.card>

                <!-- Tips -->
                <x-admin.card title="Tips">
                    <div class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                        <p><strong>Expiry Tracking:</strong> Set reminder notifications before certificates expire.</p>
                        <p><strong>Image Quality:</strong> Use high-resolution scans for better presentation.</p>
                        <p><strong>Sorting:</strong> Lower sort numbers appear first in listings.</p>
                        <p><strong>Status:</strong> Inactive certifications are hidden from public view.</p>
                    </div>
                </x-admin.card>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
            <div class="flex gap-3">
                <x-admin.button color="light" href="{{ route('admin.certifications.show', $certification) }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to View
                </x-admin.button>

                <x-admin.button color="danger" type="button" onclick="confirmDelete()">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete Certification
                </x-admin.button>
            </div>

            <div class="flex gap-3">
                <x-admin.button type="submit" name="action" value="save_and_continue" color="light">
                    Save & Continue Editing
                </x-admin.button>

                <x-admin.button type="submit" name="action" value="update" color="primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Update Certification
                </x-admin.button>
            </div>
        </div>
    </form>

    <!-- Hidden Delete Form -->
    <form id="delete-form" action="{{ route('admin.certifications.destroy', $certification) }}" method="POST"
        class="hidden">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Load existing temp files on page load (following banner pattern)
                loadExistingTempFiles();

                // Listen for universal uploader events (following banner pattern)
                document.addEventListener('files-uploaded', function(event) {
                    if (event.detail.component && event.detail.component.includes('certification')) {
                        console.log('Certification file uploaded:', event.detail.files);
                        // Optional: Hide traditional upload
                        const traditionalUpload = document.querySelector('input[name="image"]');
                        if (traditionalUpload) {
                            traditionalUpload.closest('div').style.display = 'none';
                        }
                    }
                });

                document.addEventListener('files-deleted', function(event) {
                    if (event.detail.component && event.detail.component.includes('certification')) {
                        console.log('Certification file deleted');
                        // Optional: Show traditional upload
                        const traditionalUpload = document.querySelector('input[name="image"]');
                        if (traditionalUpload) {
                            traditionalUpload.closest('div').style.display = 'block';
                        }
                    }
                });

                // Load existing temp files function (following banner pattern)
                function loadExistingTempFiles() {
                    fetch('{{ route('admin.certifications.temp-files') }}', {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.files.length > 0) {
                                console.log('Found existing temp files:', data.files);

                                // Find the universal uploader and set existing files
                                const uploader = document.querySelector('[id*="certification"]');
                                if (uploader && uploader.__x) {
                                    // Set existing files in Alpine.js component
                                    uploader.__x.$data.existingFiles = data.files;

                                    // Hide traditional upload if temp files exist
                                    const traditionalUpload = document.querySelector('input[name="image"]');
                                    if (traditionalUpload) {
                                        traditionalUpload.closest('div').style.display = 'none';
                                    }
                                }
                            } else {
                                console.log('No existing temp files found');
                            }
                        })
                        .catch(error => {
                            console.warn('Could not load existing temp files:', error);
                        });
                }

                // Form submission handler (following banner pattern)
                const form = document.querySelector('form');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        console.log('Form submitting with certification data...');

                        // Basic validation
                        const name = document.querySelector('input[name="name"]')?.value?.trim();
                        const issuer = document.querySelector('input[name="issuer"]')?.value?.trim();

                        if (!name) {
                            e.preventDefault();
                            alert('Please enter a certification name.');
                            document.querySelector('input[name="name"]')?.focus();
                            return;
                        }

                        if (!issuer) {
                            e.preventDefault();
                            alert('Please enter the issuing organization.');
                            document.querySelector('input[name="issuer"]')?.focus();
                            return;
                        }
                    });
                }
            });
        </script>
    @endpush
</x-layouts.admin>
