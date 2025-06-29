{{-- resources/views/admin/testimonials/create.blade.php --}}
<x-layouts.admin title="Create Testimonial">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="[
        'Testimonials' => route('admin.testimonials.index'),
        'Create' => ''
    ]" />

    <!-- Header -->
    <x-admin.header-section 
        title="Create New Testimonial" 
        description="Add a new client testimonial to showcase your work" />

    <form action="{{ route('admin.testimonials.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="space-y-6">
            <!-- Client Information -->
            <x-admin.card>
                <x-slot name="header">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Client Information</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Basic information about the client</p>
                </x-slot>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Link to User -->
                    <div class="md:col-span-2">
                        <label for="client_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Link to User Account (Optional)
                        </label>
                        <select name="client_id" id="client_id" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                            <option value="">Select existing user...</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" 
                                        {{ old('client_id') == $client->id ? 'selected' : '' }}
                                        data-name="{{ $client->name }}" 
                                        data-email="{{ $client->email }}" 
                                        data-company="{{ $client->company ?? '' }}">
                                    {{ $client->name }} ({{ $client->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Client Name -->
                    <div>
                        <label for="client_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Client Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="client_name" id="client_name" value="{{ old('client_name') }}"
                               placeholder="Enter client name..."
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('client_name') border-red-500 @enderror"
                               required>
                        @error('client_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Client Position -->
                    <div>
                        <label for="client_position" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Position/Title
                        </label>
                        <input type="text" name="client_position" id="client_position" value="{{ old('client_position') }}"
                               placeholder="e.g. CEO, Marketing Manager..."
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                        @error('client_position')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Client Company -->
                    <div>
                        <label for="client_company" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Company/Organization
                        </label>
                        <input type="text" name="client_company" id="client_company" value="{{ old('client_company') }}"
                               placeholder="Enter company name..."
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                        @error('client_company')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Project Association -->
                    <div>
                        <label for="project_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Related Project (Optional)
                        </label>
                        
                        <div class="relative">
                            <select name="project_id" id="project_id" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 disabled:opacity-50 disabled:cursor-not-allowed"
                                    data-client-id="{{ old('client_id') }}">
                                <option value="">Select project...</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" 
                                            data-client-id="{{ $project->client_id ?? '' }}"
                                            {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->title }}
                                        @if($project->client && $project->client->name)
                                            <span class="text-gray-500">- {{ $project->client->name }}</span>
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            
                            <!-- Loading spinner -->
                            <div id="project-loading" class="hidden absolute right-3 top-1/2 transform -translate-y-1/2">
                                <svg class="animate-spin h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Helper text -->
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" id="project-helper-text">
                            Select a client above to filter projects, or choose from all available projects
                        </p>
                        
                        @error('project_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Client Photo -->
                    
                        <div class="lg:col-span-2 space-y-6">
                                <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    Client Photo (Optional)
                                </label>
                                
                                <!-- Universal File Uploader for Client Photo -->
                                <x-universal-file-uploader 
                                    :id="'testimonial-photo-uploader-create'"
                                    name="testimonial_images" 
                                    :multiple="false"
                                    :maxFiles="1" 
                                    maxFileSize="2MB" 
                                    :acceptedFileTypes="['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']" 
                                    :uploadEndpoint="route('admin.testimonials.temp-upload')" 
                                    :deleteEndpoint="route('admin.testimonials.temp-delete')"
                                    dropDescription="Drop client photo here or click to browse (Max 2MB)"
                                    :enableCategories="false"
                                    :enableDescription="false"
                                    :enablePublicToggle="false"
                                    :instantUpload="true" 
                                    :galleryMode="false"
                                    :replaceMode="false"
                                    :singleMode="true"
                                    containerClass="mb-4" 
                                    theme="modern"
                                    :showFileList="false"
                                    :showProgress="true"
                                    :dragOverlay="true" />

                                <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                    <p>• Recommended: Square images (1:1 ratio) work best for client photos</p>
                                    <p>• Supported formats: JPEG, PNG, JPG, GIF, WebP</p>
                                    <p>• Maximum file size: 2MB</p>
                                    <p>• Images will be automatically optimized and resized</p>
                                </div>

                                @error('image')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                        </div>
                        <div class="lg:col-span-1 space-y-6">
                            <x-temp-files-display 
                                :sessionKey="'temp_testimonial_images_' . session()->getId()"
                                title="Uploaded Client Photo"
                                emptyMessage="No client photo uploaded yet"
                                :showPreview="true"
                                :allowDelete="true"
                                :deleteEndpoint="route('admin.testimonials.temp-delete')"
                                gridCols="grid-cols-1"
                                :componentId="'temp-display-testimonial'" />
                        </div>
                    

                </div>
            </x-admin.card>

            <!-- Testimonial Content -->
            <x-admin.card>
                <x-slot name="header">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Testimonial Content</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">The testimonial content and rating</p>
                </x-slot>

                <div class="space-y-6">
                    <!-- Content -->
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Testimonial Content <span class="text-red-500">*</span>
                        </label>
                        <textarea name="content" id="content" rows="5" 
                                  placeholder="Enter the testimonial content..."
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('content') border-red-500 @enderror"
                                  required>{{ old('content') }}</textarea>
                        <div class="flex justify-between mt-1">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Provide the client's testimonial text.</p>
                            <span id="content-count" class="text-xs text-gray-400">0 characters</span>
                        </div>
                        @error('content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Rating -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Rating <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center space-x-1">
                            @for($i = 1; $i <= 5; $i++)
                                <label class="cursor-pointer">
                                    <input type="radio" name="rating" value="{{ $i }}" 
                                           class="sr-only rating-input"
                                           {{ old('rating', '5') == $i ? 'checked' : '' }}
                                           required>
                                    <svg class="h-8 w-8 rating-star {{ old('rating', '5') >= $i ? 'text-yellow-400' : 'text-gray-300' }} hover:text-yellow-400 transition-colors" 
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </label>
                            @endfor
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400" id="rating-text">5 stars</span>
                        </div>
                        @error('rating')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </x-admin.card>

            <!-- Status & Settings -->
            <x-admin.card>
                <x-slot name="header">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Status & Settings</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Control testimonial visibility and status</p>
                </x-slot>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                            <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>
                                Pending Review
                            </option>
                            <option value="approved" {{ old('status') === 'approved' ? 'selected' : '' }}>
                                Approved
                            </option>
                            <option value="featured" {{ old('status') === 'featured' ? 'selected' : '' }}>
                                Featured
                            </option>
                            <option value="rejected" {{ old('status') === 'rejected' ? 'selected' : '' }}>
                                Rejected
                            </option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div></div> <!-- Empty div for grid alignment -->

                    <!-- Active Toggle -->
                    <div class="flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" 
                               name="is_active" 
                               id="is_active" 
                               value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                            Active - Whether this testimonial is active and visible
                        </label>
                    </div>

                    <!-- Featured Toggle -->
                    <div class="flex items-center">
                        <input type="hidden" name="featured" value="0">
                        <input type="checkbox" 
                               name="featured" 
                               id="featured" 
                               value="1"
                               {{ old('featured', false) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="featured" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                            Featured - Mark this testimonial as featured
                        </label>
                    </div>
                </div>
            </x-admin.card>

            <!-- Admin Notes -->
            <x-admin.card>
                <x-slot name="header">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Admin Notes</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Internal notes for administrative use</p>
                </x-slot>

                <div>
                    <label for="admin_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        Internal Notes
                    </label>
                    <textarea name="admin_notes" id="admin_notes" rows="3" 
                              placeholder="Internal notes for admin use only..."
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">{{ old('admin_notes') }}</textarea>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">These notes are only visible to administrators and are not displayed publicly.</p>
                    @error('admin_notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </x-admin.card>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3 pt-8 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('admin.testimonials.index') }}" 
               class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                Cancel
            </a>
            <button type="submit" 
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Create Testimonial
            </button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get CSRF token for AJAX requests
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // DOM elements
            const clientSelect = document.getElementById('client_id');
            const clientNameInput = document.getElementById('client_name');
            const clientPositionInput = document.getElementById('client_position');
            const clientCompanyInput = document.getElementById('client_company');
            const projectSelect = document.getElementById('project_id');
            const clientLoading = document.getElementById('client-loading');
            const projectLoading = document.getElementById('project-loading');
            const projectHelperText = document.getElementById('project-helper-text');
            
            // Character count for content
            const contentTextarea = document.getElementById('content');
            const contentCount = document.getElementById('content-count');
            
            function updateContentCount() {
                contentCount.textContent = contentTextarea.value.length + ' characters';
            }
            
            contentTextarea.addEventListener('input', updateContentCount);
            updateContentCount();

            // Rating star interaction
            const ratingInputs = document.querySelectorAll('.rating-input');
            const ratingStars = document.querySelectorAll('.rating-star');
            const ratingText = document.getElementById('rating-text');
            
            function updateStars(rating) {
                ratingStars.forEach((star, index) => {
                    if (index < rating) {
                        star.classList.remove('text-gray-300');
                        star.classList.add('text-yellow-400');
                    } else {
                        star.classList.remove('text-yellow-400');
                        star.classList.add('text-gray-300');
                    }
                });
                
                const ratingTexts = ['', '1 star', '2 stars', '3 stars', '4 stars', '5 stars'];
                ratingText.textContent = ratingTexts[rating] || '';
            }
            
            ratingInputs.forEach((input, index) => {
                input.addEventListener('change', function() {
                    updateStars(parseInt(this.value));
                });
            });

            // Status change handling
            const statusSelect = document.getElementById('status');
            const isActiveCheckbox = document.getElementById('is_active');
            const featuredCheckbox = document.getElementById('featured');
            
            statusSelect.addEventListener('change', function() {
                if (this.value === 'featured') {
                    featuredCheckbox.checked = true;
                    isActiveCheckbox.checked = true;
                } else if (this.value === 'approved') {
                    isActiveCheckbox.checked = true;
                } else if (this.value === 'rejected') {
                    isActiveCheckbox.checked = false;
                    featuredCheckbox.checked = false;
                }
            });
            
            if (featuredCheckbox) {
                featuredCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        statusSelect.value = 'featured';
                        isActiveCheckbox.checked = true;
                    }
                });
            }

            // Enhanced client selection with AJAX
            clientSelect.addEventListener('change', async function() {
                const selectedClientId = this.value;
                
                if (!selectedClientId) {
                    // Clear all fields if no client selected
                    clearClientFields();
                    clearProjectFields();
                    return;
                }

                try {
                    // Show loading state
                    showClientLoading(true);
                    
                    // Get client details
                    const clientResponse = await fetchWithErrorHandling(
                        `/admin/testimonials/ajax/client/${selectedClientId}/details`
                    );
                    
                    if (clientResponse.success) {
                        fillClientFields(clientResponse.client);
                    }
                    
                    // Get client's completed projects
                    const projectsResponse = await fetchWithErrorHandling(
                        `/admin/testimonials/ajax/client/${selectedClientId}/projects`
                    );
                    
                    if (projectsResponse.success) {
                        populateProjectSelect(projectsResponse.projects, projectsResponse.count);
                    }
                    
                } catch (error) {
                    console.error('Error fetching client data:', error);
                    showNotification('Error loading client data. Please try again.', 'error');
                    clearClientFields();
                    clearProjectFields();
                } finally {
                    showClientLoading(false);
                }
            });

            // Helper functions
            function showClientLoading(show) {
                if (show) {
                    clientLoading.classList.remove('hidden');
                    clientSelect.disabled = true;
                } else {
                    clientLoading.classList.add('hidden');
                    clientSelect.disabled = false;
                }
            }

            function showProjectLoading(show) {
                if (show) {
                    projectLoading.classList.remove('hidden');
                    projectSelect.disabled = true;
                } else {
                    projectLoading.classList.add('hidden');
                    projectSelect.disabled = false;
                }
            }

            function clearClientFields() {
                clientNameInput.value = '';
                clientPositionInput.value = '';
                clientCompanyInput.value = '';
                
                // Make fields editable again
                clientNameInput.readOnly = false;
                clientPositionInput.readOnly = false;
                clientCompanyInput.readOnly = false;
                
                // Update placeholders
                clientNameInput.placeholder = 'Enter client name manually...';
                clientPositionInput.placeholder = 'Enter position manually...';
                clientCompanyInput.placeholder = 'Enter company manually...';
            }

            function fillClientFields(client) {
                clientNameInput.value = client.name || '';
                clientPositionInput.value = client.position || '';
                clientCompanyInput.value = client.company || '';
                
                // Make fields read-only but allow manual editing
                clientNameInput.readOnly = false;
                clientPositionInput.readOnly = false;
                clientCompanyInput.readOnly = false;
                
                // Update placeholders
                clientNameInput.placeholder = 'Auto-filled from client profile';
                clientPositionInput.placeholder = 'Auto-filled from client profile';
                clientCompanyInput.placeholder = 'Auto-filled from client profile';
            }

            function clearProjectFields() {
                projectSelect.innerHTML = '<option value="">Select a client first...</option>';
                projectSelect.disabled = true;
                updateProjectHelperText('Select a client above to view their completed projects');
            }

            function populateProjectSelect(projects, count) {
                showProjectLoading(true);
                
                // Clear existing options
                projectSelect.innerHTML = '<option value="">Select a project (optional)...</option>';
                
                if (projects && projects.length > 0) {
                    projects.forEach(project => {
                        const option = document.createElement('option');
                        option.value = project.id;
                        option.textContent = project.title;
                        if (project.completed_at) {
                            option.textContent += ` (Completed: ${project.completed_at})`;
                        }
                        option.setAttribute('data-client-id', project.client_id);
                        option.setAttribute('data-description', project.description || '');
                        projectSelect.appendChild(option);
                    });
                    
                    updateProjectHelperText(`Found ${count} completed project(s) for this client`);
                    projectSelect.disabled = false;
                } else {
                    updateProjectHelperText('No completed projects found for this client');
                    projectSelect.disabled = true;
                }
                
                showProjectLoading(false);
            }

            function updateProjectHelperText(text) {
                projectHelperText.textContent = text;
            }

            // Fetch with error handling
            async function fetchWithErrorHandling(url) {
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                
                if (!data.success) {
                    throw new Error(data.message || 'Request failed');
                }

                return data;
            }

            // Show notification helper function
            function showNotification(message, type = 'info') {
                if (typeof window.showToast === 'function') {
                    window.showToast(message, type);
                } else if (typeof window.showNotification === 'function') {
                    window.showNotification(message, type);
                } else {
                    createToastNotification(message, type);
                }
            }

            // Simple toast notification fallback
            function createToastNotification(message, type = 'info') {
                const toast = document.createElement('div');
                toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 ${getToastColor(type)} transition-opacity duration-300 opacity-0`;
                toast.textContent = message;
                
                document.body.appendChild(toast);
                
                // Animate in
                setTimeout(() => {
                    toast.classList.remove('opacity-0');
                    toast.classList.add('opacity-100');
                }, 100);
                
                // Remove after 4 seconds
                setTimeout(() => {
                    toast.classList.remove('opacity-100');
                    toast.classList.add('opacity-0');
                    setTimeout(() => {
                        if (document.body.contains(toast)) {
                            document.body.removeChild(toast);
                        }
                    }, 300);
                }, 4000);
            }

            function getToastColor(type) {
                switch(type) {
                    case 'success': return 'bg-green-500';
                    case 'error': return 'bg-red-500';
                    case 'warning': return 'bg-yellow-500';
                    default: return 'bg-blue-500';
                }
            }

            // Initialize form state
            const initialClientId = clientSelect.value;
            if (initialClientId) {
                // Trigger change event for pre-selected client (from old input)
                clientSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
</x-layouts.admin>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploaderId = 'testimonial-photo-uploader-create-{{ uniqid() }}';
    
    // Listen for universal uploader events
    document.addEventListener('files-uploaded', function(event) {
        if (event.detail.component.startsWith('testimonial-photo-uploader-create-')) {
            handleTempImageUploadSuccess(event.detail);
        }
    });

    document.addEventListener('file-deleted', function(event) {
        if (event.detail.component.startsWith('testimonial-photo-uploader-create-')) {
            handleTempImageDelete(event.detail);
        }
    });

    // Handle temporary image upload success
    function handleTempImageUploadSuccess(detail) {
        showNotification(detail.message || 'Client photo uploaded successfully!', 'success');
        
        // You can add any additional logic here if needed
        console.log('Temporary image uploaded:', detail);
    }

    // Handle temporary image deletion
    function handleTempImageDelete(detail) {
        showNotification(detail.message || 'Client photo removed!', 'info');
        
        // You can add any additional logic here if needed
        console.log('Temporary image deleted:', detail);
    }

    // Show notification helper function
    function showNotification(message, type = 'info') {
        // Check if you have a notification system, otherwise use a simple alert
        if (typeof window.showToast === 'function') {
            window.showToast(message, type);
        } else if (typeof window.showNotification === 'function') {
            window.showNotification(message, type);
        } else {
            // Create a simple toast notification
            createToastNotification(message, type);
        }
    }

    // Simple toast notification fallback
    function createToastNotification(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 ${getToastColor(type)}`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.classList.add('opacity-100');
        }, 100);
        
        // Remove after 3 seconds
        setTimeout(() => {
            toast.classList.add('opacity-0');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }

    function getToastColor(type) {
        switch(type) {
            case 'success': return 'bg-green-500';
            case 'error': return 'bg-red-500';
            case 'warning': return 'bg-yellow-500';
            default: return 'bg-blue-500';
        }
    }
});
</script>
@endpush