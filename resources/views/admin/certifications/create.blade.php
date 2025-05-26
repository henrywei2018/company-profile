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
                <x-admin.form-section title="Basic Information" description="Enter the main details for the certification">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-admin.input
                                name="name"
                                label="Certification Name"
                                placeholder="Enter certification name..."
                                :value="old('name')"
                                required
                                helper="The full name of the certification or credential"
                            />
                        </div>
                        
                        <div class="md:col-span-2">
                            <x-admin.input
                                name="issuer"
                                label="Issuing Organization"
                                placeholder="Enter issuing organization..."
                                :value="old('issuer')"
                                required
                                helper="The organization or authority that issued this certification"
                            />
                        </div>
                        
                        <div class="md:col-span-2">
                            <x-admin.textarea
                                name="description"
                                label="Description"
                                placeholder="Describe the certification..."
                                :value="old('description')"
                                rows="4"
                                helper="Optional description of what this certification covers or represents"
                            />
                        </div>
                    </div>
                </x-admin.form-section>

                <!-- Dates -->
                <x-admin.form-section title="Certification Dates" description="Specify the validity period of the certification">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-admin.input
                            type="date"
                            name="issue_date"
                            label="Issue Date"
                            :value="old('issue_date')"
                            helper="When was this certification issued?"
                        />
                        
                        <x-admin.input
                            type="date"
                            name="expiry_date"
                            label="Expiry Date"
                            :value="old('expiry_date')"
                            helper="When does this certification expire? Leave blank if it doesn't expire"
                        />
                    </div>
                </x-admin.form-section>
            </div>

            <!-- Sidebar -->
            <div class="w-full lg:w-80 space-y-6">
                <!-- Status Options -->
                <x-admin.card title="Status">
                    <div class="">
                        <x-admin.checkbox
                            name="is_active"
                            label="Active Certification"
                            :checked="old('is_active', true)"
                            helper="Show this certification on the website"
                        />
                        
                        <x-admin.input
                            type="number"
                            name="sort_order"
                            label="Sort Order"
                            :value="old('sort_order', 0)"
                            min="0"
                            helper="Order in which this certification appears (lower numbers appear first)"
                        />
                    </div>
                </x-admin.card>

                <!-- Certificate Image -->
                <x-admin.card title="Certificate Image">
                    <x-admin.file-upload
                        name="image"
                        label=""
                        accept="image/*"
                        helper="Upload an image of the certificate. Recommended size: 800x600px"
                    >
                        Upload certificate image (max 2MB)
                    </x-admin.file-upload>
                </x-admin.card>

                <!-- Additional Information -->
                <x-admin.card title="Tips">
                    <div class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                        <p><strong>Certification Name:</strong> Use the official name as it appears on the certificate.</p>
                        <p><strong>Issuer:</strong> Include the full organization name for credibility.</p>
                        <p><strong>Image:</strong> A clear photo or scan of the certificate helps build trust.</p>
                        <p><strong>Expiry Date:</strong> Keep track of renewal dates to maintain valid certifications.</p>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Create Certification
                </x-admin.button>
            </div>
        </div>
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
                        // Create preview if it doesn't exist
                        let preview = document.getElementById('image-preview');
                        if (!preview) {
                            preview = document.createElement('div');
                            preview.id = 'image-preview';
                            preview.className = 'mt-4';
                            fileInput.parentNode.appendChild(preview);
                        }
                        
                        preview.innerHTML = `
                            <img src="${e.target.result}" alt="Certificate preview" 
                                 class="w-full h-32 object-cover rounded-lg border">
                            <p class="text-xs text-gray-500 mt-2">Preview</p>
                        `;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    </script>
    @endpush
</x-layouts.admin>