{{-- resources/views/client/testimonials/edit.blade.php --}}
<x-layouts.client title="Edit Testimonial">
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Testimonial</h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Update your testimonial. Changes will reset the status to pending for review.
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Current Status -->
                    @switch($testimonial->status)
                        @case('pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                                Pending Review
                            </span>
                            @break
                        @case('rejected')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                Rejected
                            </span>
                            @break
                    @endswitch
                    
                    <a href="{{ route('client.testimonials.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Testimonials
                    </a>
                </div>
            </div>
        </div>

        <!-- Admin Feedback (if rejected) -->
        @if($testimonial->status === 'rejected' && $testimonial->admin_notes)
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                            Feedback from Review Team
                        </h3>
                        <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                            <p>{{ $testimonial->admin_notes }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <form action="{{ route('client.testimonials.update', $testimonial) }}" method="POST" enctype="multipart/form-data" class="space-y-6 p-6">
                @csrf
                @method('PUT')

                <!-- Project Selection -->
                <div>
                    <label for="project_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        Related Project (Optional)
                    </label>
                    <select name="project_id" id="project_id" 
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                        <option value="">Select a project...</option>
                        @foreach($userProjects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id', $testimonial->project_id) == $project->id ? 'selected' : '' }}>
                                {{ $project->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('project_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Rating -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        Your Rating <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center space-x-1">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" 
                                    class="rating-star text-2xl {{ $i <= old('rating', $testimonial->rating) ? 'text-yellow-400' : 'text-gray-300' }} hover:text-yellow-400 focus:outline-none transition-colors duration-200" 
                                    data-rating="{{ $i }}">
                                <svg class="w-8 h-8 fill-current" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            </button>
                        @endfor
                        <span id="rating-text" class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ old('rating', $testimonial->rating) }}/5</span>
                    </div>
                    <input type="hidden" name="rating" id="rating" value="{{ old('rating', $testimonial->rating) }}">
                    @error('rating')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Testimonial Content -->
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        Your Testimonial <span class="text-red-500">*</span>
                    </label>
                    <textarea name="content" id="content" rows="6" 
                              placeholder="Share your experience working with us..."
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 resize-vertical">{{ old('content', $testimonial->content) }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <div class="flex justify-between mt-1">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Minimum 10 characters, maximum 1000 characters.
                        </p>
                        <span id="char-count" class="text-xs text-gray-500 dark:text-gray-400">{{ strlen(old('content', $testimonial->content)) }}/1000</span>
                    </div>
                </div>

                <!-- Current Image -->
                @if($testimonial->image)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Current Photo
                        </label>
                        <div class="flex items-center space-x-4">
                            <img src="{{ asset('storage/' . $testimonial->image) }}" 
                                 alt="Current photo" 
                                 class="h-20 w-20 object-cover rounded-lg border border-gray-300 dark:border-gray-600">
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                <p>Upload a new photo below to replace this one, or leave empty to keep current photo.</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Photo Upload -->
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        {{ $testimonial->image ? 'Replace Photo (Optional)' : 'Upload Photo (Optional)' }}
                    </label>
                    <!-- Universal File Uploader for Client Photo -->
                    <x-universal-file-uploader 
                        :id="'testimonial-photo-uploader-edit-' . $testimonial->id"
                        name="image"
                        :single-mode="true"
                        :max-files="1"
                        max-file-size="2MB"
                        :accepted-file-types="['image/jpeg', 'image/jpg', 'image/png', 'image/gif']"
                        drop-description="Drop your photo here or click to browse"
                        :show-file-list="true"
                        :allow-preview="true"
                        :gallery-mode="false"
                        :compact="false"
                        theme="default"
                        container-class="testimonial-photo-uploader"
                        upload-endpoint="{{ route('client.testimonials.upload-temp') }}"
                        delete-endpoint="{{ route('client.testimonials.delete-temp') }}"
                        :existing-files="$testimonial->image ? [['name' => 'Current Photo', 'url' => asset('storage/' . $testimonial->image), 'size' => 'N/A', 'type' => 'image']] : []"
                    />
                    @error('image')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Supported formats: PNG, JPG, GIF. Maximum size: 2MB.
                        @if($testimonial->image)
                            <br>Leave empty to keep current photo.
                        @endif
                    </p>
                </div>

                <!-- Update Notice -->
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                Important Notice
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                <p>When you update this testimonial, its status will be reset to "Pending" for review by our team.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('client.testimonials.index') }}" 
                       class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        Cancel
                    </a>
                    
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                            id="submit-btn">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                            Update Testimonial
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Same rating functionality as create form
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.rating-star');
            const ratingInput = document.getElementById('rating');
            const ratingText = document.getElementById('rating-text');
            const contentTextarea = document.getElementById('content');
            const charCount = document.getElementById('char-count');
            const submitBtn = document.getElementById('submit-btn');

            let currentRating = {{ old('rating', $testimonial->rating) }};

            // Initialize rating
            updateStars(currentRating);

            // Rating star functionality
            stars.forEach((star, index) => {
                star.addEventListener('click', function() {
                    currentRating = index + 1;
                    ratingInput.value = currentRating;
                    updateStars(currentRating);
                    updateRatingText();
                });

                star.addEventListener('mouseenter', function() {
                    updateStars(index + 1, true);
                });
            });

            document.querySelector('.rating-star').parentElement.addEventListener('mouseleave', function() {
                updateStars(currentRating);
            });

            function updateStars(rating, isHover = false) {
                stars.forEach((star, index) => {
                    if (index < rating) {
                        star.classList.add('text-yellow-400');
                        star.classList.remove('text-gray-300');
                    } else {
                        star.classList.remove('text-yellow-400');
                        star.classList.add('text-gray-300');
                    }
                });
            }

            function updateRatingText() {
                const ratingTexts = ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
                ratingText.textContent = currentRating > 0 ? `${currentRating}/5 - ${ratingTexts[currentRating]}` : 'Click to rate';
            }

            // Character count functionality
            function updateCharCount() {
                const length = contentTextarea.value.length;
                charCount.textContent = `${length}/1000`;
                
                if (length > 1000) {
                    charCount.classList.add('text-red-500');
                    charCount.classList.remove('text-gray-500');
                } else {
                    charCount.classList.remove('text-red-500');
                    charCount.classList.add('text-gray-500');
                }

                submitBtn.disabled = length < 10 || length > 1000 || currentRating === 0;
            }

            contentTextarea.addEventListener('input', updateCharCount);
            updateCharCount(); // Initial call

            // Initialize rating text
            updateRatingText();
        });
    </script>
    @endpush
</x-layouts.client>