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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                        <select name="project_id" id="project_id" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                            <option value="">Select project...</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Client Photo -->
                    <div class="md:col-span-2">
                        <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Client Photo (Optional)
                        </label>
                        <!-- Universal File Uploader for Client Photo -->
                        <x-universal-file-uploader 
                            :id="'testimonial-photo-uploader-' . uniqid()" 
                            name="image" 
                            :multiple="false" 
                            :maxFiles="1"
                            maxFileSize="2MB" 
                            :acceptedFileTypes="['image/jpeg', 'image/png', 'image/jpg', 'image/gif']" 
                            dropDescription="Drop client photo here or click to browse" 
                            :singleMode="true"
                            :showFileList="true"
                            :galleryMode="false"
                            containerClass="mb-2" 
                            theme="minimal" />
                        <p class="mt-1 text-sm text-gray-500">PNG, JPG, GIF up to 2MB</p>
                        @error('image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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

            // Client selection auto-fill
            const clientSelect = document.getElementById('client_id');
            const clientNameInput = document.getElementById('client_name');
            const clientCompanyInput = document.getElementById('client_company');
            
            clientSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    if (!clientNameInput.value) {
                        clientNameInput.value = selectedOption.dataset.name || '';
                    }
                    if (!clientCompanyInput.value) {
                        clientCompanyInput.value = selectedOption.dataset.company || '';
                    }
                }
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
            
            // Featured checkbox handling
            if (featuredCheckbox) {
                featuredCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        statusSelect.value = 'featured';
                        isActiveCheckbox.checked = true;
                    }
                });
            }
        });
    </script>
</x-layouts.admin>