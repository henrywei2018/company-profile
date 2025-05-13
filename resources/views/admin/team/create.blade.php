<!-- resources/views/admin/team/create.blade.php -->
<x-admin-layout :title="isset($teamMember) ? 'Edit Team Member: ' . $teamMember->name : 'Add New Team Member'">
    <div class="mb-6">
        <a href="{{ route('admin.team.index') }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900">
            <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Team Members
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
                {{ isset($teamMember) ? 'Edit Team Member: ' . $teamMember->name : 'Add New Team Member' }}
            </h2>

            <form action="{{ isset($teamMember) ? route('admin.team.update', $teamMember->id) : route('admin.team.store') }}" 
                  method="POST" 
                  enctype="multipart/form-data">
                @csrf
                @if(isset($teamMember))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                               value="{{ old('name', isset($teamMember) ? $teamMember->name : '') }}" 
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Slug -->
                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
                        <input type="text" 
                               name="slug" 
                               id="slug" 
                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                               value="{{ old('slug', isset($teamMember) ? $teamMember->slug : '') }}">
                        <p class="mt-1 text-sm text-gray-500">Leave blank to auto-generate from name.</p>
                        @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Position -->
                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-700">Position</label>
                        <input type="text" 
                               name="position" 
                               id="position" 
                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                               value="{{ old('position', isset($teamMember) ? $teamMember->position : '') }}" 
                               required>
                        @error('position')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Department -->
                    <div>
                        <label for="department" class="block text-sm font-medium text-gray-700">Department</label>
                        <input type="text" 
                               name="department" 
                               id="department" 
                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                               value="{{ old('department', isset($teamMember) ? $teamMember->department : '') }}">
                        @error('department')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                               value="{{ old('email', isset($teamMember) ? $teamMember->email : '') }}">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="text" 
                               name="phone" 
                               id="phone" 
                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                               value="{{ old('phone', isset($teamMember) ? $teamMember->phone : '') }}">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status Toggles -->
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" 
                                       name="is_active" 
                                       id="is_active" 
                                       value="1"
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                       {{ old('is_active', isset($teamMember) && $teamMember->is_active ? true : false) ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_active" class="font-medium text-gray-700">Active</label>
                                <p class="text-gray-500">Only active team members are displayed on the website.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" 
                                       name="is_featured" 
                                       id="is_featured" 
                                       value="1"
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                       {{ old('is_featured', isset($teamMember) && $teamMember->is_featured ? true : false) ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_featured" class="font-medium text-gray-700">Featured</label>
                                <p class="text-gray-500">Featured team members are highlighted on the homepage.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sort Order -->
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700">Display Order</label>
                        <input type="number" 
                               name="sort_order" 
                               id="sort_order" 
                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                               value="{{ old('sort_order', isset($teamMember) ? $teamMember->sort_order : 0) }}"
                               min="0">
                        <p class="mt-1 text-sm text-gray-500">Lower numbers appear first.</p>
                        @error('sort_order')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Bio -->
                <div class="mb-6">
                    <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                    <textarea name="bio" 
                              id="bio" 
                              rows="5" 
                              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('bio', isset($teamMember) ? $teamMember->bio : '') }}</textarea>
                    @error('bio')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Profile Image -->
                <div class="mb-6">
                    <label for="image" class="block text-sm font-medium text-gray-700">Profile Image</label>
                    @if(isset($teamMember) && $teamMember->image)
                        <div class="mt-2 mb-4">
                            <img src="{{ asset('storage/' . $teamMember->image) }}" alt="{{ $teamMember->name }}" class="h-40 w-40 object-cover rounded-full">
                        </div>
                    @endif
                    <input type="file" 
                           name="image" 
                           id="image" 
                           class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300"
                           accept="image/*">
                    <p class="mt-1 text-sm text-gray-500">Recommended size: 400×400 pixels (square).</p>
                    @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Social Media -->
                <div class="bg-gray-50 p-4 rounded-md mb-6">
                    <h3 class="text-md font-medium text-gray-900 mb-3">Social Media Links</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- LinkedIn -->
                        <div>
                            <label for="linkedin" class="block text-sm font-medium text-gray-700">LinkedIn Profile</label>
                            <input type="url" 
                                   name="linkedin" 
                                   id="linkedin" 
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                   value="{{ old('linkedin', isset($teamMember) ? $teamMember->linkedin : '') }}"
                                   placeholder="https://www.linkedin.com/in/username">
                            @error('linkedin')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Facebook -->
                        <div>
                            <label for="social_facebook" class="block text-sm font-medium text-gray-700">Facebook Profile</label>
                            <input type="url" 
                                   name="social_facebook" 
                                   id="social_facebook" 
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                   value="{{ old('social_facebook', isset($teamMember) ? $teamMember->social_facebook : '') }}"
                                   placeholder="https://www.facebook.com/username">
                            @error('social_facebook')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Twitter -->
                        <div>
                            <label for="social_twitter" class="block text-sm font-medium text-gray-700">Twitter Profile</label>
                            <input type="url" 
                                   name="social_twitter" 
                                   id="social_twitter" 
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                   value="{{ old('social_twitter', isset($teamMember) ? $teamMember->social_twitter : '') }}"
                                   placeholder="https://twitter.com/username">
                            @error('social_twitter')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Instagram -->
                        <div>
                            <label for="social_instagram" class="block text-sm font-medium text-gray-700">Instagram Profile</label>
                            <input type="url" 
                                   name="social_instagram" 
                                   id="social_instagram" 
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                   value="{{ old('social_instagram', isset($teamMember) ? $teamMember->social_instagram : '') }}"
                                   placeholder="https://www.instagram.com/username">
                            @error('social_instagram')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Buttons -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.team.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ isset($teamMember) ? 'Update Team Member' : 'Create Team Member' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>