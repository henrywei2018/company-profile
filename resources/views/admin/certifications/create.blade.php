{{-- resources/views/admin/certifications/create.blade.php --}}
<x-layouts.admin title="Create New Certification">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Certifications' => route('admin.certifications.index'), 'Create New Certification' => '']" />

    <form action="{{ route('admin.certifications.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Main Content -->
            <div class="flex-1 space-y-6">
                <!-- Basic Information -->
                <x-admin.form-section title="Basic Information"
                    description="Enter the main details for the certification">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-admin.input name="name" label="Certification Name"
                                placeholder="Enter certification name..." :value="old('name')" required
                                helper="The full name of the certification or credential" />
                        </div>

                        <div class="md:col-span-2">
                            <x-admin.input name="issuer" label="Issuing Organization"
                                placeholder="Enter issuing organization..." :value="old('issuer')" required
                                helper="The organization or authority that issued this certification" />
                        </div>

                        <div class="md:col-span-2">
                            <x-admin.textarea name="description" label="Description"
                                placeholder="Describe the certification..." :value="old('description')" rows="4"
                                helper="Optional description of what this certification covers or represents" />
                        </div>
                    </div>
                </x-admin.form-section>

                <!-- Dates -->
                <x-admin.form-section title="Certification Dates"
                    description="Specify the validity period of the certification">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-admin.input type="date" name="issue_date" label="Issue Date" :value="old('issue_date')"
                            helper="When was this certification issued?" />

                        <x-admin.input type="date" name="expiry_date" label="Expiry Date" :value="old('expiry_date')"
                            helper="When does this certification expire? Leave blank if it doesn't expire" />
                    </div>
                </x-admin.form-section>
            </div>

            <!-- Sidebar -->
            <div class="w-full lg:w-80 space-y-6">
                <!-- Status Options -->
                <x-admin.card title="Status">
                    <div class="">
                        <x-admin.checkbox name="is_active" label="Active Certification" :checked="old('is_active', true)"
                            helper="Show this certification on the website" />

                        <x-admin.input type="number" name="sort_order" label="Sort Order" :value="old('sort_order', 0)"
                            min="0"
                            helper="Order in which this certification appears (lower numbers appear first)" />
                    </div>
                </x-admin.card>

                <!-- Certificate Image -->
                <x-admin.card title="Certificate File (Image or PDF)">
                    <div>
                        <!-- Universal File Uploader -->
                        <x-universal-file-uploader id="certification-temp-uploader" name="certification_files"
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
                            dropDescription="Drop certification file here or click to browse" :existingFiles="[]"
                            :instantUpload="true" :autoUpload="true" :singleMode="true" containerClass="mb-4"
                            theme="modern" />
                    </div>
                    <x-temp-files-display sessionKey="certification_temp_files_{{ session()->getId() }}"
                        title="Upload Status" emptyMessage="No certificate file uploaded yet" :showPreview="true"
                        :allowDelete="true" deleteEndpoint="{{ route('admin.certifications.temp-delete') }}"
                        gridCols="grid-cols-1" componentId="cert-temp-display" />
                </x-admin.card>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
            <x-admin.button color="light" href="{{ route('admin.certifications.index') }}">
                Cancel
            </x-admin.button>

            <div class="flex gap-3">
                <x-admin.button type="submit" name="action" value="save_and_continue" color="light">
                    Save & Add Another
                </x-admin.button>

                <x-admin.button type="submit" name="action" value="save" color="primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Create Certification
                </x-admin.button>
            </div>
        </div>
    </form>
        @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const uploadSummaryCard = document.getElementById('upload-summary-card');
            const submitBtn = document.getElementById('submit-btn');
            const traditionalUpload = document.getElementById('traditional-upload-fallback');

            let hasUploadedFiles = false;
            let tempDisplayComponent = null;

            // Get reference to temp display component
            setTimeout(() => {
                const tempDisplayElement = document.getElementById('cert-temp-display');
                if (tempDisplayElement && tempDisplayElement.__x) {
                    tempDisplayComponent = tempDisplayElement.__x.$data;
                    
                    // Override the addFiles method to prevent automatic addition
                    const originalAddFiles = tempDisplayComponent.addFiles;
                    tempDisplayComponent.addFiles = function(newFiles) {
                        console.log('addFiles called, but blocked to prevent duplicates');
                        // Don't call the original addFiles to prevent duplicates
                        // Instead, we'll refresh from server
                        setTimeout(() => {
                            refreshTempFilesFromServer();
                        }, 100);
                    };
                }
            }, 100);

            // Listen for file uploads from universal uploader
            document.addEventListener('files-uploaded', function(event) {
                if (event.detail.component && event.detail.component.includes('certification-uploader')) {
                    const files = event.detail.files || [];
                    console.log('Files uploaded event received:', files);
                    
                    if (files.length > 0) {
                        hasUploadedFiles = true;
                        showUploadSummary(true);
                        
                        // Hide traditional upload
                        if (traditionalUpload) {
                            traditionalUpload.style.display = 'none';
                        }

                        // Refresh temp display from server instead of letting it auto-add
                        setTimeout(() => {
                            refreshTempFilesFromServer();
                        }, 200);
                    }
                }
            });

            // Listen for file deletions
            document.addEventListener('files-deleted', function(event) {
                if (event.detail.component && (
                    event.detail.component.includes('certification-uploader') || 
                    event.detail.component.includes('cert-temp-display')
                )) {
                    console.log('File deleted event received');
                    hasUploadedFiles = false;
                    showUploadSummary(false);
                    
                    // Show traditional upload
                    if (traditionalUpload) {
                        traditionalUpload.style.display = 'block';
                    }

                    // Refresh temp display
                    setTimeout(() => {
                        refreshTempFilesFromServer();
                    }, 100);
                }
            });

            // Refresh temp files display from server (authoritative source)
            function refreshTempFilesFromServer() {
                fetch('{{ route('admin.certifications.temp-files') }}', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Server response:', data);
                    
                    if (data.success && tempDisplayComponent) {
                        // Clear existing files first to prevent any accumulation
                        tempDisplayComponent.files = [];
                        
                        // Set files directly from server response
                        if (data.files && data.files.length > 0) {
                            tempDisplayComponent.files = [...data.files]; // Create new array to trigger reactivity
                        }

                        // Update UI state
                        if (data.files && data.files.length > 0) {
                            hasUploadedFiles = true;
                            showUploadSummary(true);
                            if (traditionalUpload) {
                                traditionalUpload.style.display = 'none';
                            }
                        } else {
                            hasUploadedFiles = false;
                            showUploadSummary(false);
                            if (traditionalUpload) {
                                traditionalUpload.style.display = 'block';
                            }
                        }
                    }
                })
                .catch(error => {
                    console.error('Could not refresh temp files:', error);
                });
            }

            // Show/hide upload summary
            function showUploadSummary(show) {
                if (show && uploadSummaryCard) {
                    uploadSummaryCard.classList.remove('hidden');
                    if (submitBtn) {
                        submitBtn.textContent = `{{ isset($certification) ? 'Update' : 'Create' }} Certification (1 file ready)`;
                    }
                } else {
                    if (uploadSummaryCard) {
                        uploadSummaryCard.classList.add('hidden');
                    }
                    if (submitBtn) {
                        submitBtn.textContent = `{{ isset($certification) ? 'Update Certification' : 'Create Certification' }}`;
                    }
                }
            }

            // Form validation
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
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

                    // Show loading state
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = `
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        `;
                    }
                });
            }

            // Initial check for existing files
            setTimeout(() => {
                refreshTempFilesFromServer();
            }, 300);
        });
    </script>
@endpush
    </x-layouts.admin>
