<x-layouts.client title="New Quotation Request">
    <div class="max-w-8xl mx-auto py-4 px-4 sm:px-6 lg:px-4">
        {{-- Header Section --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Request New Quotation</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Fill out the form below to request a quotation for your project. All fields marked with * are required.
            </p>
        </div>

        {{-- Enhanced Form with File Upload Support --}}
        <form id="quotation-form" action="{{ route('client.quotations.store') }}" method="POST">
            @csrf
            
            <div class="space-y-8">
                {{-- Project Information Section --}}
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                            Project Information
                        </h3>
                        
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            {{-- Project Type --}}
                            <div class="sm:col-span-2">
                                <label for="project_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Project Type *
                                </label>
                                <input type="text" 
                                       name="project_type" 
                                       id="project_type" 
                                       value="{{ old('project_type') }}"
                                       placeholder="e.g., Website Development, Mobile App, E-commerce Platform"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                       required>
                                @error('project_type')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Service Selection --}}
                            <div>
                                <label for="service_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Related Service
                                </label>
                                <select name="service_id" 
                                        id="service_id" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select a service (optional)</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                            {{ $service->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('service_id')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Priority --}}
                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Priority Level
                                </label>
                                <select name="priority" 
                                        id="priority"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium Priority</option>
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low Priority</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High Priority</option>
                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                                @error('priority')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Location --}}
                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Project Location
                                </label>
                                <input type="text" 
                                       name="location" 
                                       id="location" 
                                       value="{{ old('location') }}"
                                       placeholder="e.g., New York, Remote, Multiple Locations"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('location')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Budget Range --}}
                            <div>
                                <label for="budget_range" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Budget Range
                                </label>
                                <select name="budget_range" 
                                        id="budget_range"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select budget range</option>
                                    <option value="under_5k" {{ old('budget_range') == 'under_5k' ? 'selected' : '' }}>Under $5,000</option>
                                    <option value="5k_10k" {{ old('budget_range') == '5k_10k' ? 'selected' : '' }}>$5,000 - $10,000</option>
                                    <option value="10k_25k" {{ old('budget_range') == '10k_25k' ? 'selected' : '' }}>$10,000 - $25,000</option>
                                    <option value="25k_50k" {{ old('budget_range') == '25k_50k' ? 'selected' : '' }}>$25,000 - $50,000</option>
                                    <option value="50k_100k" {{ old('budget_range') == '50k_100k' ? 'selected' : '' }}>$50,000 - $100,000</option>
                                    <option value="over_100k" {{ old('budget_range') == 'over_100k' ? 'selected' : '' }}>Over $100,000</option>
                                    <option value="tbd" {{ old('budget_range') == 'tbd' ? 'selected' : '' }}>To Be Determined</option>
                                </select>
                                @error('budget_range')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Start Date --}}
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Preferred Start Date
                                </label>
                                <input type="date" 
                                       name="start_date" 
                                       id="start_date" 
                                       value="{{ old('start_date') }}"
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('start_date')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Preferred Contact Method --}}
                            <div>
                                <label for="preferred_contact_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Preferred Contact Method
                                </label>
                                <select name="preferred_contact_method" 
                                        id="preferred_contact_method"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="both" {{ old('preferred_contact_method', 'both') == 'both' ? 'selected' : '' }}>Email & Phone</option>
                                    <option value="email" {{ old('preferred_contact_method') == 'email' ? 'selected' : '' }}>Email Only</option>
                                    <option value="phone" {{ old('preferred_contact_method') == 'phone' ? 'selected' : '' }}>Phone Only</option>
                                </select>
                                @error('preferred_contact_method')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Requirements Section --}}
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                            Project Requirements
                        </h3>
                        
                        <div class="space-y-6">
                            {{-- Main Requirements --}}
                            <div>
                                <label for="requirements" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Project Requirements *
                                </label>
                                <textarea name="requirements" 
                                          id="requirements" 
                                          rows="6" 
                                          placeholder="Please describe your project requirements, goals, target audience, features needed, and any specific preferences or constraints..."
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                          required>{{ old('requirements') }}</textarea>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    Minimum 10 characters. Be as detailed as possible to help us provide an accurate quote.
                                </p>
                                @error('requirements')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Additional Notes --}}
                            <div>
                                <label for="additional_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Additional Notes
                                </label>
                                <textarea name="additional_notes" 
                                          id="additional_notes" 
                                          rows="3" 
                                          placeholder="Any additional information, special requirements, or questions..."
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('additional_notes') }}</textarea>
                                @error('additional_notes')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- File Attachments Section - ENHANCED WITH UNIVERSAL FILE UPLOADER --}}
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
            Supporting Documents
        </h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            Upload any relevant documents, images, or specifications to help us understand your project better (Optional)
        </p>

        {{-- Universal File Uploader Integration - CORRECTED --}}
        <x-universal-file-uploader 
    id="quotation-attachments-uploader"
    name="files[]"
    :multiple="false"
    :maxFiles="3"
    maxFileSize="10MB"
    :acceptedFileTypes="[
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp', 'image/bmp',
        'text/plain', 'text/csv', 'application/zip', 'application/rtf'
    ]"
    :uploadEndpoint="route('client.quotations.temp-upload')"
    :deleteEndpoint="route('client.quotations.temp-delete')"
    dropDescription="Drop a file here or click to browse"
    :enableCategories="true"
    :categories="[
        ['value' => 'document', 'label' => 'Project Document'],
        ['value' => 'image', 'label' => 'Reference Image'],
        ['value' => 'requirement', 'label' => 'Requirement Spec'],
        ['value' => 'specification', 'label' => 'Technical Specification'],
        ['value' => 'other', 'label' => 'Other']
    ]"
    :enableDescription="true"
    :instantUpload="true"
    :autoUpload="true"
    :singleMode="true"
    theme="modern"
    containerClass="mb-4"
/>
        
        {{-- Hidden inputs for temp file data --}}
        <div id="temp-files-data"></div>

        {{-- Helpful Information --}}
        <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
            <div class="flex">
                <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div class="text-sm text-blue-700 dark:text-blue-300">
                    <p class="font-medium">Helpful files to include:</p>
                    <ul class="mt-1 space-y-1 list-disc list-inside">
                        <li>Project briefs or specifications</li>
                        <li>Reference images or design mockups</li>
                        <li>Technical requirements documents</li>
                        <li>Brand guidelines or style guides</li>
                        <li>Existing website or system documentation</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('client.quotations.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Cancel
                    </a>
                    
                    <div class="flex space-x-3">
                        {{-- Save as Draft Button --}}
                        <button type="submit" 
                                name="action" 
                                value="save_as_draft" 
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a.997.997 0 01-.707.293H7a4 4 0 01-4-4V7a4 4 0 014-4z"/>
                            </svg>
                            Save as Draft
                        </button>
                        
                        {{-- Submit Button --}}
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            Submit Quotation Request
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

   @push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    window.tempFiles = [];

    // Show notification helper
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-md ${
            type === 'error' ? 'bg-red-600 text-white' : 
            type === 'success' ? 'bg-green-600 text-white' : 
            'bg-blue-600 text-white'
        }`;
        notification.innerHTML = `
            <div class="flex items-center">
                <span class="flex-1">${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        `;
        document.body.appendChild(notification);
        setTimeout(() => { if (notification.parentNode) notification.remove(); }, 5000);
    }

    // Utility: sinkronkan temp files ke hidden input
    function addTempFilesToForm() {
        const container = document.getElementById('temp-files-data');
        if (!container) return;
        container.innerHTML = '';
        window.tempFiles.forEach((file, index) => {
            const tempIdInput = document.createElement('input');
            tempIdInput.type = 'hidden';
            tempIdInput.name = `temp_files[${index}][temp_id]`;
            tempIdInput.value = file.temp_id;
            container.appendChild(tempIdInput);

            if (file.category) {
                const categoryInput = document.createElement('input');
                categoryInput.type = 'hidden';
                categoryInput.name = `temp_files[${index}][category]`;
                categoryInput.value = file.category;
                container.appendChild(categoryInput);
            }

            if (file.description) {
                const descInput = document.createElement('input');
                descInput.type = 'hidden';
                descInput.name = `temp_files[${index}][description]`;
                descInput.value = file.description;
                container.appendChild(descInput);
            }
        });
    }

    // Handler sukses upload file universal-uploader
    function handleUniversalUploadEvent(event) {
        // Debug: tampilkan detail event
        console.log('[DEBUG] files-uploaded event:', event.detail);
        let files = [];
        if (event.detail.files && Array.isArray(event.detail.files)) {
            files = event.detail.files;
        } else if (event.detail.file) {
            files = [event.detail.file];
        } else if (event.detail.result) {
            files = [event.detail.result];
        }

        // Update tempFiles hanya dengan file yang temp_id
        files.forEach(file => {
            if (file.temp_id) {
                // Hindari duplikat temp_id
                if (!window.tempFiles.some(f => f.temp_id === file.temp_id)) {
                    window.tempFiles.push({
                        temp_id: file.temp_id,
                        category: file.category || 'document',
                        description: file.description || ''
                    });
                }
            }
        });
        addTempFilesToForm();
    }

    // Handler file deleted (hapus dari array & form)
    function handleTempFileDelete(event) {
        let tempId = event.detail?.temp_id || event.temp_id;
        if (tempId) {
            window.tempFiles = window.tempFiles.filter(file => file.temp_id !== tempId);
            addTempFilesToForm();
        }
    }

    // Listen event universal-uploader (window & document agar robust)
    window.addEventListener('files-uploaded', handleUniversalUploadEvent);
    document.addEventListener('files-uploaded', handleUniversalUploadEvent);
    window.addEventListener('file-deleted', handleTempFileDelete);
    document.addEventListener('file-deleted', handleTempFileDelete);

    // --- Validasi sebelum submit ---
    const form = document.getElementById('quotation-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            addTempFilesToForm();

            // Validasi field wajib (customisasi sesuai kebutuhan)
            const projectType = document.getElementById('project_type')?.value?.trim() || '';
            const requirements = document.getElementById('requirements')?.value?.trim() || '';
            // Cek apakah minimal 1 file harus diupload
            const mustUploadFile = true; // Ubah ke false jika tidak wajib file

            if (!projectType) {
                e.preventDefault();
                showNotification('Please enter a project type.', 'error');
                document.getElementById('project_type').focus();
                return;
            }
            if (!requirements || requirements.length < 10) {
                e.preventDefault();
                showNotification('Please provide at least 10 characters in the requirements field.', 'error');
                document.getElementById('requirements').focus();
                return;
            }
            if (mustUploadFile && window.tempFiles.length < 1) {
                e.preventDefault();
                showNotification('Please upload at least one attachment.', 'error');
                // Scroll ke uploader jika perlu:
                document.getElementById('quotation-attachments-uploader')?.scrollIntoView({behavior: "smooth", block: "center"});
                return;
            }
            // Tambahkan validasi lain jika perlu...
        });
    }
});
</script>
@endpush

</x-layouts.client>