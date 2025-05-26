{{-- resources/views/admin/certifications/edit.blade.php --}}
<x-layouts.admin title="Edit Certification">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Certifications' => route('admin.certifications.index'), 
        $certification->name => route('admin.certifications.show', $certification),
        'Edit' => ''
    ]" />

    <form action="{{ route('admin.certifications.update', $certification) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Main Content -->
            <div class="flex-1 space-y-6">
                <!-- Basic Information -->
                <x-admin.form-section title="Basic Information" description="Update the main details for the certification">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-admin.input
                                name="name"
                                label="Certification Name"
                                placeholder="Enter certification name..."
                                :value="old('name', $certification->name)"
                                required
                                helper="The full name of the certification or credential"
                            />
                        </div>
                        
                        <div class="md:col-span-2">
                            <x-admin.input
                                name="issuer"
                                label="Issuing Organization"
                                placeholder="Enter issuing organization..."
                                :value="old('issuer', $certification->issuer)"
                                required
                                helper="The organization or authority that issued this certification"
                            />
                        </div>
                        
                        <div class="md:col-span-2">
                            <x-admin.textarea
                                name="description"
                                label="Description"
                                placeholder="Describe the certification..."
                                :value="old('description', $certification->description)"
                                rows="4"
                                helper="Optional description of what this certification covers or represents"
                            />
                        </div>
                    </div>
                </x-admin.form-section>

                <!-- Dates -->
                <x-admin.form-section title="Certification Dates" description="Update the validity period of the certification">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-admin.input
                            type="date"
                            name="issue_date"
                            label="Issue Date"
                            :value="old('issue_date', $certification->issue_date?->format('Y-m-d'))"
                            helper="When was this certification issued?"
                        />
                        
                        <x-admin.input
                            type="date"
                            name="expiry_date"
                            label="Expiry Date"
                            :value="old('expiry_date', $certification->expiry_date?->format('Y-m-d'))"
                            helper="When does this certification expire? Leave blank if it doesn't expire"
                        />
                    </div>
                    
                    @if($certification->expiry_date && $certification->expiry_date->isPast())
                        <x-admin.alert type="warning" class="mt-4">
                            <strong>Notice:</strong> This certification has expired on {{ $certification->expiry_date->format('M j, Y') }}. 
                            Consider updating the expiry date or marking it as inactive.
                        </x-admin.alert>
                    @elseif($certification->expiry_date && $certification->expiry_date->diffInDays() <= 30)
                        <x-admin.alert type="info" class="mt-4">
                            <strong>Reminder:</strong> This certification will expire on {{ $certification->expiry_date->format('M j, Y') }} 
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
                        <x-admin.checkbox
                            name="is_active"
                            label="Active Certification"
                            :checked="old('is_active', $certification->is_active)"
                            helper="Show this certification on the website"
                        />
                        
                        <x-admin.input
                            type="number"
                            name="sort_order"
                            label="Sort Order"
                            :value="old('sort_order', $certification->sort_order)"
                            min="0"
                            helper="Order in which this certification appears (lower numbers appear first)"
                        />
                    </div>
                </x-admin.card>

                <!-- Certificate Image -->
                <x-admin.card title="Certificate Image">
                    @if($certification->image)
                        <div class="mb-4">
                            <img src="{{ asset('storage/' . $certification->image) }}" 
                                 alt="{{ $certification->name }}" 
                                 class="w-full h-32 object-cover rounded-lg border">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Current certificate image</p>
                        </div>
                    @endif
                    
                    <x-admin.file-upload
                        name="image"
                        label=""
                        accept="image/*"
                        helper="Upload a new image to replace the current one (if any). Recommended size: 800x600px"
                    >
                        {{ $certification->image ? 'Replace certificate image (max 2MB)' : 'Upload certificate image (max 2MB)' }}
                    </x-admin.file-upload>
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
                        
                        @if($certification->issue_date)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Age:</span>
                            <span class="font-medium">{{ $certification->issue_date->diffForHumans() }}</span>
                        </div>
                        @endif
                        
                        @if($certification->expiry_date)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">{{ $certification->expiry_date->isPast() ? 'Expired:' : 'Expires:' }}</span>
                            <span class="font-medium {{ $certification->expiry_date->isPast() ? 'text-red-600' : 'text-green-600' }}">
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to View
                </x-admin.button>
                
                <x-admin.button 
                    color="danger" 
                    type="button"
                    onclick="confirmDelete()"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update Certification
                </x-admin.button>
            </div>
        </div>
    </form>

    <!-- Hidden Delete Form -->
    <form id="delete-form" action="{{ route('admin.certifications.destroy', $certification) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
    <script>
        // Validate expiry date is after issue date
        const issueDateInput = document.querySelector('input[name="issue_date"]');
        const expiryDateInput = document.querySelector('input[name="expiry_date"]');
        
        function validateDates() {
            if (issueDateInput.value && expiryDateInput.value) {
                const issueDate = new Date(issueDateInput.value);
                const expiryDate = new Date(expiryDateInput.value);
                
                if (expiryDate <= issueDate) {
                    expiryDateInput.setCustomValidity('Expiry date must be after issue date');
                } else {
                    expiryDateInput.setCustomValidity('');
                }
            } else {
                expiryDateInput.setCustomValidity('');
            }
        }
        
        issueDateInput.addEventListener('change', validateDates);
        expiryDateInput.addEventListener('change', validateDates);
        
        // File upload preview
        const fileInput = document.querySelector('input[name="image"]');
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // Find existing image or create preview
                        let existingImg = document.querySelector('img[alt="{{ $certification->name }}"]');
                        if (existingImg) {
                            existingImg.src = e.target.result;
                            existingImg.nextElementSibling.textContent = 'New image preview';
                        } else {
                            // Create new preview
                            let preview = document.getElementById('image-preview');
                            if (!preview) {
                                preview = document.createElement('div');
                                preview.id = 'image-preview';
                                preview.className = 'mb-4';
                                fileInput.parentNode.insertBefore(preview, fileInput);
                            }
                            
                            preview.innerHTML = `
                                <img src="${e.target.result}" alt="Certificate preview" 
                                     class="w-full h-32 object-cover rounded-lg border">
                                <p class="text-xs text-gray-500 mt-2">New image preview</p>
                            `;
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
        
        // Delete confirmation
        function confirmDelete() {
            if (confirm('Are you sure you want to delete this certification? This action cannot be undone.')) {
                document.getElementById('delete-form').submit();
            }
        }
        
        // Auto-save draft functionality (optional)
        let autoSaveTimeout;
        const formInputs = document.querySelectorAll('input, textarea, select');
        
        formInputs.forEach(input => {
            input.addEventListener('input', function() {
                clearTimeout(autoSaveTimeout);
                autoSaveTimeout = setTimeout(function() {
                    // Could implement auto-save to localStorage here
                    console.log('Auto-saving draft...');
                }, 2000);
            });
        });
    </script>
    @endpush
</x-layouts.admin>