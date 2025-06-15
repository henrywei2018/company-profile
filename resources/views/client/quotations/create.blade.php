<x-layouts.client>
    <x-slot name="title">Submit Quotation Request</x-slot>
    <x-slot name="description">Tell us about your project and we'll get back to you with a detailed quote.</x-slot>

    <form id="quotation-form" 
          action="{{ route('client.quotations.store') }}" 
          method="POST" 
          enctype="multipart/form-data"
          x-data="quotationFormHandler()">
        @csrf

        <!-- Personal Information -->
        <x-admin.card>
            
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Personal Information</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Please provide your contact details.</p>
            

            <div class="px-6 py-4 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-admin.input 
                        label="Full Name" 
                        name="name" 
                        :value="old('name', auth()->user()->name)" 
                        required 
                    />
                    
                    <x-admin.input 
                        label="Email Address" 
                        name="email" 
                        type="email" 
                        :value="old('email', auth()->user()->email)" 
                        required 
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-admin.input 
                        label="Phone Number" 
                        name="phone" 
                        :value="old('phone', auth()->user()->phone)" 
                    />
                    
                    <x-admin.input 
                        label="Company Name" 
                        name="company" 
                        :value="old('company', auth()->user()->company)" 
                    />
                </div>
            </div>
        </x-admin.card>

        <!-- Project Information -->
        <x-admin.card>
            
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Project Information</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tell us about your project requirements.</p>
            

            <div class="px-6 py-4 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-admin.select 
                        label="Service Type" 
                        name="service_id"
                        :options="$services->pluck('name', 'id')"
                        :value="old('service_id')"
                        placeholder="Select a service (optional)"
                    />
                    
                    <x-admin.input 
                        label="Project Type" 
                        name="project_type" 
                        :value="old('project_type')" 
                        placeholder="e.g., Website Development, Mobile App, etc."
                        required 
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-admin.input 
                        label="Location" 
                        name="location" 
                        :value="old('location')" 
                        placeholder="Project location (if applicable)"
                    />
                    
                    <x-admin.input 
                        label="Expected Start Date" 
                        name="start_date" 
                        type="date" 
                        :value="old('start_date')"
                    />
                </div>

                <x-admin.textarea 
                    label="Project Requirements" 
                    name="requirements" 
                    :value="old('requirements')" 
                    rows="4"
                    placeholder="Please describe your project in detail. Include features, functionality, design preferences, technical requirements, etc."
                    required 
                />

                <x-admin.select 
                    label="Budget Range" 
                    name="budget_range"
                    :options="[
                        'under_5k' => 'Under $5,000',
                        '5k_15k' => '$5,000 - $15,000',
                        '15k_50k' => '$15,000 - $50,000',
                        '50k_100k' => '$50,000 - $100,000',
                        'over_100k' => 'Over $100,000',
                        'discuss' => 'Prefer to discuss'
                    ]"
                    :value="old('budget_range')"
                    placeholder="Select budget range (optional)"
                />
            </div>
        </x-admin.card>

        <!-- File Attachments -->
        <x-admin.card>
            
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Project Files</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Upload any relevant documents, designs, specifications, or reference materials (max 5 files, 10MB each).
                </p>
           

            <div class="px-6 py-4">
                <x-universal-file-uploader
                    name="files"
                    :multiple="true"
                    :max-files="5"
                    max-file-size="10MB"
                    :accepted-file-types="[
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                        'application/zip',
                        'application/x-rar-compressed',
                        'text/plain',
                        'text/csv'
                    ]"
                    upload-endpoint="{{ route('client.quotations.upload-attachment') }}"
                    delete-endpoint="{{ route('client.quotations.delete-temp-file') }}"
                    drop-description="Drop project files here or click to browse"
                    :auto-upload="true"
                    :upload-on-drop="true"
                    :show-progress="true"
                    theme="modern"
                    id="quotation-attachments-uploader"
                    container-class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg"
                    :existing-files="[]"
                />

                @error('attachments')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                @error('attachments.*')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </x-admin.card>

        <!-- Action Buttons -->
        <x-admin.card>
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <x-admin.button href="{{ route('client.quotations.index') }}" color="light">
                            Cancel
                        </x-admin.button>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <button 
                            type="button" 
                            class="inline-flex items-center px-4 py-2 bg-gray-300 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            @click="saveDraft()"
                            x-show="!isDraft"
                        >
                            Save as Draft
                        </button>
                        
                        <button 
                            type="button" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="submitting"
                            @click="submitQuotation()"
                        >
                            <!-- Loading spinner -->
                            <template x-if="submitting">
                                <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            
                            <!-- Normal icon -->
                            <template x-if="!submitting">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                            </template>
                            
                            <span x-text="submitting ? 'Submitting...' : 'Submit Quotation Request'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </x-admin.card>
    </form>

    @push('scripts')
    <script>
        // Debug logging
        console.log('=== QUOTATION FORM DEBUG ===');
        console.log('Session ID:', '{{ session()->getId() }}');
        console.log('Upload endpoint:', '{{ route("client.quotations.upload-attachment") }}');
        
        function quotationFormHandler() {
            return {
                submitting: false,
                isDraft: false,
                uploadedFiles: [],
                tempSession: '{{ session()->getId() }}',
                
                init() {
                    console.log('Quotation form handler initialized');
                    
                    // Load existing temp files on page load
                    this.loadExistingTempFiles();
                    
                    // Listen for file upload events from universal uploader
                    document.addEventListener('files-uploaded', (event) => {
                        console.log('Files uploaded event received:', event.detail);
                        if (event.detail.component === 'quotation-attachments-uploader') {
                            this.handleFilesUploaded(event.detail);
                        }
                    });
                    
                    // Listen for file removal events
                    document.addEventListener('file-removed', (event) => {
                        console.log('File removed event received:', event.detail);
                        if (event.detail.component === 'quotation-attachments-uploader') {
                            this.handleFileRemoved(event.detail);
                        }
                    });

                    // Listen for upload errors
                    document.addEventListener('upload-error', (event) => {
                        console.log('Upload error event received:', event.detail);
                        this.showNotification('Upload failed: ' + event.detail.error, 'error');
                    });

                    // Prevent default form submission and use our custom handler
                    document.getElementById('quotation-form').addEventListener('submit', (e) => {
                        e.preventDefault();
                        if (!this.submitting) {
                            this.submitQuotation();
                        }
                    });
                },
                
                async loadExistingTempFiles() {
                    console.log('Loading existing temp files...');
                    try {
                        const response = await fetch('{{ route('client.quotations.get-temp-files') }}', {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const result = await response.json();
                        console.log('Get temp files response:', result);
                        
                        if (result.success && result.files.length > 0) {
                            this.uploadedFiles = result.files;
                            console.log('Loaded temp files:', this.uploadedFiles);
                            
                            // Dispatch event to update uploader component
                            this.$dispatch('load-existing-files', {
                                files: result.files,
                                component: 'quotation-attachments-uploader'
                            });
                        }
                    } catch (error) {
                        console.warn('Could not load existing temp files:', error);
                    }
                },
                
                handleFilesUploaded(detail) {
                    console.log('Handling files uploaded:', detail);
                    
                    if (detail.files && Array.isArray(detail.files)) {
                        this.uploadedFiles = [...this.uploadedFiles, ...detail.files];
                        console.log('Updated uploaded files array:', this.uploadedFiles);
                        
                        this.showNotification(detail.result?.message || 'Files uploaded successfully!', 'success');
                    }
                },
                
                handleFileRemoved(detail) {
                    console.log('Handling file removed:', detail);
                    // Remove from our tracking array
                    this.uploadedFiles = this.uploadedFiles.filter((file, index) => index !== detail.index);
                    console.log('Updated uploaded files after removal:', this.uploadedFiles);
                },
                
                async submitQuotation() {
                    console.log('Submit quotation called');
                    
                    if (this.submitting) {
                        console.log('Already submitting, ignoring...');
                        return;
                    }
                    
                    this.submitting = true;
                    console.log('Starting quotation submission...');
                    
                    try {
                        // Get the form element
                        const form = document.getElementById('quotation-form');
                        
                        // Basic client-side validation
                        const requiredFields = ['name', 'email', 'project_type', 'requirements'];
                        for (const field of requiredFields) {
                            const input = form.querySelector(`[name="${field}"]`);
                            if (!input || !input.value.trim()) {
                                this.showNotification(`Please fill in the ${field.replace('_', ' ')} field.`, 'error');
                                this.submitting = false;
                                input?.focus();
                                return;
                            }
                        }

                        // Create form data
                        const formData = new FormData(form);
                        
                        // Log uploaded files before submission
                        console.log('Uploaded files before submission:', this.uploadedFiles);
                        
                        // Add uploaded file references
                        this.uploadedFiles.forEach((file, index) => {
                            if (file.temp_id) {
                                formData.append(`temp_files[${index}]`, file.temp_id);
                                console.log(`Added temp file ${index}:`, file.temp_id);
                            }
                        });

                        // Log FormData contents
                        console.log('FormData contents:');
                        for (let pair of formData.entries()) {
                            console.log(pair[0], pair[1]);
                        }

                        // Submit via fetch for better control
                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        console.log('Form submission response status:', response.status);
                        
                        const result = await response.json();
                        console.log('Form submission response:', result);

                        if (response.ok && result.success) {
                            this.showNotification(result.message, 'success');
                            
                            // Redirect to show page
                            if (result.data && result.data.redirect) {
                                console.log('Redirecting to:', result.data.redirect);
                                setTimeout(() => {
                                    window.location.href = result.data.redirect;
                                }, 1500);
                            }
                        } else {
                            throw new Error(result.message || 'Form submission failed');
                        }
                        
                    } catch (error) {
                        console.error('Quotation submission error:', error);
                        this.showNotification(error.message || 'Failed to submit quotation. Please try again.', 'error');
                    } finally {
                        this.submitting = false;
                    }
                },
                
                async saveDraft() {
                    // Implement draft saving if needed
                    this.showNotification('Draft functionality coming soon!', 'info');
                },
                
                showNotification(message, type = 'info') {
                    console.log('Showing notification:', type, message);
                    
                    // Use your existing notification system
                    if (window.showNotification) {
                        window.showNotification(message, type);
                    } else {
                        // Fallback
                        alert(`${type.toUpperCase()}: ${message}`);
                    }
                }
            }
        }

    </script>
    @endpush
</x-layouts.client>