<!-- resources/views/admin/testimonials/create.blade.php -->
<x-admin-layout :title="isset($testimonial) ? 'Edit Testimonial: ' . $testimonial->client_name : 'Add New Testimonial'">
    <div class="mb-6">
        <a href="{{ route('admin.testimonials.index') }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900">
            <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Testimonials
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
                {{ isset($testimonial) ? 'Edit Testimonial: ' . $testimonial->client_name : 'Add New Testimonial' }}
            </h2>

            <form action="{{ isset($testimonial) ? route('admin.testimonials.update', $testimonial->id) : route('admin.testimonials.store') }}" 
                  method="POST" 
                  enctype="multipart/form-data">
                @csrf
                @if(isset($testimonial))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Client Name -->
                    <div>
                        <label for="client_name" class="block text-sm font-medium text-gray-700">Client Name</label>
                        <input type="text" 
                               name="client_name" 
                               id="client_name" 
                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                               value="{{ old('client_name', isset($testimonial) ? $testimonial->client_name : '') }}" 
                               required>
                        @error('client_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Client Position -->
                    <div>
                        <label for="client_position" class="block text-sm font-medium text-gray-700">Position</label>
                        <input type="text" 
                               name="client_position" 
                               id="client_position" 
                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                               value="{{ old('client_position', isset($testimonial) ? $testimonial->client_position : '') }}">
                        @error('client_position')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Client Company -->
                    <div>
                        <label for="client_company" class="block text-sm font-medium text-gray-700">Company</label>
                        <input type="text" 
                               name="client_company" 
                               id="client_company" 
                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                               value="{{ old('client_company', isset($testimonial) ? $testimonial->client_company : '') }}">
                        @error('client_company')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Rating -->
                    <div>
                        <label for="rating" class="block text-sm font-medium text-gray-700">Rating (1-5)</label>
                        <div class="mt-1 flex items-center space-x-2">
                            <input type="range" 
                                   name="rating" 
                                   id="rating" 
                                   min="1" 
                                   max="5" 
                                   step="1" 
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                   value="{{ old('rating', isset($testimonial) ? $testimonial->rating : 5) }}">
                            <span id="rating-display" class="text-sm font-medium text-gray-700">
                                {{ old('rating', isset($testimonial) ? $testimonial->rating : 5) }}
                            </span>
                        </div>
                        @error('rating')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Related Project -->
                    <div>
                        <label for="project_id" class="block text-sm font-medium text-gray-700">Related Project (Optional)</label>
                        <select name="project_id" 
                                id="project_id" 
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <option value="">No related project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id', isset($testimonial) ? $testimonial->project_id : '') == $project->id ? 'selected' : '' }}>
                                    {{ $project->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
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
                                       {{ old('is_active', isset($testimonial) && $testimonial->is_active ? true : false) ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_active" class="font-medium text-gray-700">Active</label>
                                <p class="text-gray-500">Only active testimonials are displayed on the website.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" 
                                       name="featured" 
                                       id="featured" 
                                       value="1"
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                       {{ old('featured', isset($testimonial) && $testimonial->featured ? true : false) ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="featured" class="font-medium text-gray-700">Featured</label>
                                <p class="text-gray-500">Featured testimonials are highlighted on the homepage.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Client Image -->
                <div class="mb-6">
                    <label for="image" class="block text-sm font-medium text-gray-700">Client Photo (Optional)</label>
                    @if(isset($testimonial) && $testimonial->image)
                        <div class="mt-2 mb-4">
                            <img src="{{ asset('storage/' . $testimonial->image) }}" alt="{{ $testimonial->client_name }}" class="h-32 w-32 object-cover rounded-full">
                        </div>
                    @endif
                    <input type="file" 
                           name="image" 
                           id="image" 
                           class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300"
                           accept="image/*">
                    <p class="mt-1 text-sm text-gray-500">Recommended size: 300×300 pixels (square).</p>
                    @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Testimonial Content -->
                <div class="mb-6">
                    <label for="content" class="block text-sm font-medium text-gray-700">Testimonial Content</label>
                    <textarea name="content" 
                              id="content" 
                              rows="5" 
                              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                              required>{{ old('content', isset($testimonial) ? $testimonial->content : '') }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">The client's testimonial text.</p>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Client Contact Info (For Internal Use) -->
                <div class="bg-gray-50 p-4 rounded-md mb-6">
                    <h3 class="text-md font-medium text-gray-900 mb-3">Client Contact Information (For Internal Use Only)</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Client Email -->
                        <div>
                            <label for="client_email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" 
                                   name="client_email" 
                                   id="client_email" 
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                   value="{{ old('client_email', isset($testimonial) ? $testimonial->client_email : '') }}">
                            @error('client_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Client Phone -->
                        <div>
                            <label for="client_phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="text" 
                                   name="client_phone" 
                                   id="client_phone" 
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                   value="{{ old('client_phone', isset($testimonial) ? $testimonial->client_phone : '') }}">
                            @error('client_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">This information is for internal records only and will not be displayed publicly.</p>
                </div>

                <!-- Admin Notes -->
                <div class="mb-6">
                    <label for="admin_notes" class="block text-sm font-medium text-gray-700">Admin Notes (Internal Only)</label>
                    <textarea name="admin_notes" 
                              id="admin_notes" 
                              rows="3" 
                              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('admin_notes', isset($testimonial) ? $testimonial->admin_notes : '') }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">Notes visible only to administrators.</p>
                    @error('admin_notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Buttons -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.testimonials.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ isset($testimonial) ? 'Update Testimonial' : 'Create Testimonial' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>

@push('scripts')
<script>
    // Update the rating display when the range input changes
    document.addEventListener('DOMContentLoaded', function() {
        const ratingInput = document.getElementById('rating');
        const ratingDisplay = document.getElementById('rating-display');
        
        if (ratingInput && ratingDisplay) {
            ratingInput.addEventListener('input', function() {
                ratingDisplay.textContent = this.value;
            });
        }
    });
</script>
@endpush