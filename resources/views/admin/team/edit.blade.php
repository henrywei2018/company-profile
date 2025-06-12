{{-- resources/views/admin/team/edit.blade.php --}}
<x-layouts.admin title="Edit Team Member: {{ $teamMember->name }}">
    <!-- Breadcrumb -->
    <x-admin.breadcrumb :items="['Team Management' => route('admin.team.index'), 'Edit Team Member' => '']" />

    <form action="{{ route('admin.team.update', $teamMember) }}" method="POST" class="space-y-6" id="team-form">
        @csrf
        @method('PUT')

        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Main Content -->
            <div class="flex-1 space-y-6">
                <!-- Basic Information -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Basic Information</h3>
                    </x-slot>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name"
                                   value="{{ old('name', $teamMember->name) }}"
                                   placeholder="Enter full name..."
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('name') border-red-500 @enderror"
                                   required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Position <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="position" 
                                   id="position"
                                   value="{{ old('position', $teamMember->position) }}"
                                   placeholder="Enter job position..."
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('position') border-red-500 @enderror"
                                   required>
                            @error('position')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Department
                            </label>
                            <select name="department_id" 
                                    id="department_id"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('department_id') border-red-500 @enderror">
                                <option value="">Select a department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id', $teamMember->department_id) == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Email Address
                            </label>
                            <input type="email" 
                                   name="email" 
                                   id="email"
                                   value="{{ old('email', $teamMember->email) }}"
                                   placeholder="Enter email address..."
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Phone Number
                            </label>
                            <input type="text" 
                                   name="phone" 
                                   id="phone"
                                   value="{{ old('phone', $teamMember->phone) }}"
                                   placeholder="Enter phone number..."
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('phone') border-red-500 @enderror">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Sort Order
                            </label>
                            <input type="number" 
                                   name="sort_order" 
                                   id="sort_order"
                                   value="{{ old('sort_order', $teamMember->sort_order) }}"
                                   placeholder="0"
                                   min="0"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('sort_order') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Lower numbers appear first</p>
                            @error('sort_order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>

                <!-- Biography -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Biography</h3>
                    </x-slot>

                    <div>
                        <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Bio
                        </label>
                        <textarea name="bio" 
                                  id="bio"
                                  rows="6"
                                  placeholder="Enter team member biography..."
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm resize-y focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('bio') border-red-500 @enderror">{{ old('bio', $teamMember->bio) }}</textarea>
                        @error('bio')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </x-admin.card>

                <!-- Social Media Links -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Social Media Links</h3>
                    </x-slot>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="linkedin" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                LinkedIn Profile
                            </label>
                            <input type="url" 
                                   name="linkedin" 
                                   id="linkedin"
                                   value="{{ old('linkedin', $teamMember->linkedin) }}"
                                   placeholder="https://linkedin.com/in/username"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('linkedin') border-red-500 @enderror">
                            @error('linkedin')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="twitter" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Twitter/X Profile
                            </label>
                            <input type="url" 
                                   name="twitter" 
                                   id="twitter"
                                   value="{{ old('twitter', $teamMember->twitter) }}"
                                   placeholder="https://twitter.com/username"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('twitter') border-red-500 @enderror">
                            @error('twitter')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="facebook" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Facebook Profile
                            </label>
                            <input type="url" 
                                   name="facebook" 
                                   id="facebook"
                                   value="{{ old('facebook', $teamMember->facebook) }}"
                                   placeholder="https://facebook.com/username"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('facebook') border-red-500 @enderror">
                            @error('facebook')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="instagram" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Instagram Profile
                            </label>
                            <input type="url" 
                                   name="instagram" 
                                   id="instagram"
                                   value="{{ old('instagram', $teamMember->instagram) }}"
                                   placeholder="https://instagram.com/username"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('instagram') border-red-500 @enderror">
                            @error('instagram')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>

                <!-- Profile Photo Management -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Profile Photo</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage profile photo for the team member</p>
                    </x-slot>

                    <!-- Current Photo Display -->
                    @if($teamMember->photo)
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Current Photo</h4>
                            <div class="relative group inline-block">
                                <div class="w-32 h-32 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700">
                                    <img src="{{ $teamMember->photo_url }}" alt="{{ $teamMember->name }}" 
                                         class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 rounded-full"></div>
                                </div>
                                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button type="button" onclick="removeExistingPhoto()"
                                            class="bg-red-600 hover:bg-red-700 text-white p-1 rounded-full">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="mt-2 text-xs text-gray-600 dark:text-gray-400 text-center">
                                    {{ $teamMember->getPhotoFileSize() }}
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Upload New Photo -->
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">
                            {{ $teamMember->photo ? 'Replace Photo' : 'Upload Photo' }}
                        </h4>

                        <!-- Universal File Uploader for Photo Upload -->
                        <x-universal-file-uploader 
                            :id="'team-photo-uploader-' . $teamMember->id" 
                            name="team_photo" 
                            :multiple="false" 
                            :maxFiles="1"
                            maxFileSize="5MB" 
                            :acceptedFileTypes="['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']" 
                            :uploadEndpoint="route('admin.team.upload-temp')" 
                            :deleteEndpoint="route('admin.team.delete-temp')"
                            dropDescription="Drop new profile photo here or click to browse" 
                            :enableCategories="true"
                            :categories="[['value' => 'photo', 'label' => 'Profile Photo']]" 
                            :instantUpload="true" 
                            :galleryMode="false" 
                            :replaceMode="true"
                            containerClass="mb-4" 
                            theme="modern" 
                            :singleMode="true" />
                    </div>
                </x-admin.card>
            </div>

            <!-- Sidebar -->
            <div class="w-full lg:w-80 space-y-6">
                <!-- Settings -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Settings</h3>
                    </x-slot>

                    <div class="space-y-4">
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', $teamMember->is_active) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                            </label>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Make this team member active</p>
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="featured" 
                                       value="1"
                                       {{ old('featured', $teamMember->featured) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Featured Member</span>
                            </label>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Display this member prominently on the website</p>
                        </div>
                    </div>
                </x-admin.card>

                <!-- SEO Settings -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">SEO Settings</h3>
                    </x-slot>

                    <div class="space-y-4">
                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Meta Title
                            </label>
                            <input type="text" 
                                   name="meta_title" 
                                   id="meta_title"
                                   value="{{ old('meta_title', $teamMember->seo->title ?? '') }}"
                                   placeholder="SEO optimized title"
                                   maxlength="60"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('meta_title') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave blank to use member name (max 60 characters)</p>
                            @error('meta_title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Meta Description
                            </label>
                            <textarea name="meta_description" 
                                      id="meta_description"
                                      rows="3"
                                      placeholder="Brief description for search engines..."
                                      maxlength="160"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('meta_description') border-red-500 @enderror">{{ old('meta_description', $teamMember->seo->description ?? '') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Recommended length: 150-160 characters</p>
                            @error('meta_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="meta_keywords" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Meta Keywords
                            </label>
                            <input type="text" 
                                   name="meta_keywords" 
                                   id="meta_keywords"
                                   value="{{ old('meta_keywords', $teamMember->seo->keywords ?? '') }}"
                                   placeholder="keyword1, keyword2, keyword3"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 @error('meta_keywords') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Comma-separated keywords</p>
                            @error('meta_keywords')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-admin.card>

                <!-- Team Member Status -->
                <x-admin.card>
                    <x-slot name="header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Status Information</h3>
                    </x-slot>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Current Status:</span>
                            <x-admin.status-badge :status="$teamMember->is_active ? 'active' : 'inactive'" />
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Featured:</span>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $teamMember->featured ? 'Yes' : 'No' }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Department:</span>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $teamMember->department->name ?? 'Not assigned' }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Created:</span>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $teamMember->created_at->format('M j, Y g:i A') }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Last Updated:</span>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $teamMember->updated_at->format('M j, Y g:i A') }}</span>
                        </div>
                    </div>
                </x-admin.card>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('admin.team.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Team
            </a>

            <div class="flex gap-3">
                <button type="submit" 
                        class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    Update Team Member
                </button>
            </div>
        </div>
    </form>

    <!-- Delete Form -->
    <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700 mt-6">
        <div class="text-sm text-gray-500 dark:text-gray-400">
            Danger Zone: Permanently delete this team member
        </div>
        <form method="POST" action="{{ route('admin.team.destroy', $teamMember) }}" class="inline"
              onsubmit="return confirm('Are you sure you want to delete this team member? This action cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800 dark:hover:bg-red-900/30">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Delete Team Member
            </button>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Character counters for SEO fields
            function addCharCounter(inputId, maxLength) {
                const input = document.getElementById(inputId);
                if (!input) return;

                const counter = document.createElement('div');
                counter.className = 'text-xs text-gray-500 dark:text-gray-400 mt-1';
                counter.id = inputId + '_counter';

                input.parentNode.appendChild(counter);

                function updateCounter() {
                    const remaining = maxLength - input.value.length;
                    counter.textContent = `${input.value.length}/${maxLength} characters`;

                    if (remaining < 10) {
                        counter.className = 'text-xs text-red-500 mt-1';
                    } else {
                        counter.className = 'text-xs text-gray-500 dark:text-gray-400 mt-1';
                    }
                }

                input.addEventListener('input', updateCounter);
                updateCounter();
            }

            // Add character counters
            addCharCounter('meta_title', 60);
            addCharCounter('meta_description', 160);

            // Remove existing photo function
            window.removeExistingPhoto = function() {
                if (confirm('Are you sure you want to remove the current photo?')) {
                    fetch('{{ route('admin.team.delete-photo', $teamMember) }}', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification(data.message, 'success');
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            showNotification(data.message || 'Failed to remove photo', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('An error occurred while removing the photo', 'error');
                    });
                }
            };

            // Listen for universal uploader events
            document.addEventListener('files-uploaded', function(event) {
                if (event.detail.component.includes('team-photo-uploader')) {
                    showNotification(event.detail.message || 'Photo uploaded successfully!', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }
            });

            // Form validation
            document.getElementById('team-form').addEventListener('submit', function(e) {
                const name = document.getElementById('name').value.trim();
                const position = document.getElementById('position').value.trim();

                if (!name) {
                    e.preventDefault();
                    showNotification('Please enter the team member name.', 'error');
                    document.getElementById('name').focus();
                    return;
                }

                if (!position) {
                    e.preventDefault();
                    showNotification('Please enter the team member position.', 'error');
                    document.getElementById('position').focus();
                    return;
                }
            });
        });

        // Utility notification function
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full shadow-lg rounded-lg p-4 ${getNotificationClasses(type)} transform transition-all duration-300 ease-in-out`;
            notification.innerHTML = `
                <div class="flex">
                    <div class="flex-shrink-0">
                        ${getNotificationIcon(type)}
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium">${message}</p>
                    </div>
                    <div class="ml-auto pl-3">
                        <button onclick="this.closest('.fixed').remove()" class="inline-flex text-current hover:opacity-75">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            `;

            document.body.appendChild(notification);
            setTimeout(() => notification?.remove(), 5000);
        }

        function getNotificationClasses(type) {
            const classes = {
                success: 'bg-green-50 border border-green-200 text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-400',
                error: 'bg-red-50 border border-red-200 text-red-800 dark:bg-red-900/20 dark:border-red-800 dark:text-red-400',
                warning: 'bg-yellow-50 border border-yellow-200 text-yellow-800 dark:bg-yellow-900/20 dark:border-yellow-800 dark:text-yellow-400',
                info: 'bg-blue-50 border border-blue-200 text-blue-800 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-400'
            };
            return classes[type] || classes.info;
        }

        function getNotificationIcon(type) {
            const icons = {
                success: '<svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
                error: '<svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
                warning: '<svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>',
                info: '<svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>'
            };
            return icons[type] || icons.info;
        }
    </script>
    @endpush
</x-layouts.admin>