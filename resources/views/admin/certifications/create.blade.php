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
                console.log('Certification create page loaded');
                
                // Initialize by checking for existing temp files
                loadExistingTempFiles();

                // Listen for universal uploader events
                document.addEventListener('files-uploaded', function(event) {
                    console.log('Files uploaded event:', event.detail);
                    
                    if (event.detail.component && event.detail.component.includes('certification-create-uploader')) {
                        console.log('Certification file uploaded successfully');
                        
                        // Hide traditional upload when temp file is uploaded
                        toggleTraditionalUpload(false);
                        
                        // Show success feedback
                        showUploadFeedback('File uploaded successfully!', 'success');
                    }
                });

                document.addEventListener('files-deleted', function(event) {
                    console.log('Files deleted event:', event.detail);
                    
                    if (event.detail.component && event.detail.component.includes('certification-create-uploader')) {
                        console.log('Certification file deleted');
                        
                        // Show traditional upload when temp file is deleted
                        toggleTraditionalUpload(true);
                        
                        // Show delete feedback
                        showUploadFeedback('File deleted successfully!', 'info');
                    }
                });

                document.addEventListener('upload-error', function(event) {
                    console.log('Upload error event:', event.detail);
                    
                    if (event.detail.component && event.detail.component.includes('certification-create-uploader')) {
                        const errorMessage = event.detail.error || 'Upload failed';
                        console.error('Upload error:', errorMessage);
                        
                        // Show traditional upload on error
                        toggleTraditionalUpload(true);
                        
                        // Show error feedback
                        showUploadFeedback('Upload failed: ' + errorMessage, 'error');
                    }
                });

                // Form submission handler
                const form = document.querySelector('form');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        console.log('Form submitting...');
                        
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

            /**
             * Load existing temporary files
             */
            function loadExistingTempFiles() {
                console.log('Loading existing temp files...');
                
                fetch('{{ route('admin.certifications.temp-files') }}', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Temp files response:', data);
                    
                    if (data.success && data.files && data.files.length > 0) {
                        console.log('Found existing temp files:', data.files);
                        
                        // Find the uploader component and set existing files
                        const uploaderElement = document.querySelector('#certification-create-uploader');
                        if (uploaderElement && uploaderElement.__x) {
                            // Set existing files in Alpine.js component
                            uploaderElement.__x.$data.existingFiles = data.files;
                            
                            // Hide traditional upload since temp files exist
                            toggleTraditionalUpload(false);
                        }
                    } else {
                        console.log('No existing temp files found');
                        // Show traditional upload if no temp files
                        toggleTraditionalUpload(true);
                    }
                })
                .catch(error => {
                    console.warn('Could not load existing temp files:', error);
                    // Show traditional upload on error
                    toggleTraditionalUpload(true);
                });
            }

            /**
             * Toggle traditional upload visibility
             */
            function toggleTraditionalUpload(show) {
                const traditionalUpload = document.querySelector('#traditional-upload');
                if (traditionalUpload) {
                    traditionalUpload.style.display = show ? 'block' : 'none';
                }
            }

            /**
             * Show upload feedback messages
             */
            function showUploadFeedback(message, type = 'info') {
                // Simple notification - you can enhance this based on your notification system
                console.log(`${type.toUpperCase()}: ${message}`);
                
                // If you have a notification system, use it here
                // Example: window.showNotification && window.showNotification(message, type);
            }
        </script>
    @endpush
    </x-layouts.admin>
