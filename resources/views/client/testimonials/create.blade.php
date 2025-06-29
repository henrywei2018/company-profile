{{-- resources/views/client/testimonials/create.blade.php --}}
<x-layouts.client title="Write Testimonial">
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Write a Testimonial</h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Share your experience with our services to help other potential clients.
                    </p>
                </div>
                <a href="{{ route('client.testimonials.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Testimonials
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <form action="{{ route('client.testimonials.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 p-6">
                @csrf

                <!-- Project Selection -->
                <div>
                    <label for="project_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        Related Project (Optional)
                    </label>
                    <select name="project_id" id="project_id" 
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                        <option value="">Select a project...</option>
                        @foreach($userProjects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('project_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Link this testimonial to a specific project you worked on with us.
                    </p>
                </div>

                <!-- Rating -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        Your Rating <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center space-x-1">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" 
                                    class="rating-star text-2xl text-gray-300 hover:text-yellow-400 focus:outline-none transition-colors duration-200" 
                                    data-rating="{{ $i }}">
                                <svg class="w-8 h-8 fill-current" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            </button>
                        @endfor
                        <span id="rating-text" class="ml-2 text-sm text-gray-600 dark:text-gray-400">Click to rate</span>
                    </div>
                    <input type="hidden" name="rating" id="rating" value="{{ old('rating') }}">
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
                              placeholder="Share your experience working with us. What did you like most? How did we help achieve your goals? What would you tell others about our services?"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 resize-vertical">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <div class="flex justify-between mt-1">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Minimum 10 characters, maximum 1000 characters.
                        </p>
                        <span id="char-count" class="text-xs text-gray-500 dark:text-gray-400">0/1000</span>
                    </div>
                </div>

                <!-- Photo Upload -->
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        Your Photo (Optional)
                    </label>
                    <!-- Universal File Uploader for Client Photo -->
                    <x-universal-file-uploader 
                        :id="'testimonial-photo-uploader-' . uniqid()"
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
                    />
                    @error('image')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Supported formats: PNG, JPG, GIF. Maximum size: 2MB.
                    </p>
                </div>

                <!-- Submission Guidelines -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                Submission Guidelines
                            </h3>
                            <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Your testimonial will be reviewed by our team before being published</li>
                                    <li>Please be honest and specific about your experience</li>
                                    <li>Avoid including sensitive business information</li>
                                    <li>You can edit your testimonial while it's pending or rejected</li>
                                </ul>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Submit Testimonial
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Rating functionality
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.rating-star');
            const ratingInput = document.getElementById('rating');
            const ratingText = document.getElementById('rating-text');
            const contentTextarea = document.getElementById('content');
            const charCount = document.getElementById('char-count');
            const submitBtn = document.getElementById('submit-btn');

            let currentRating = {{ old('rating', 0) }};

            // Initialize rating if old value exists
            if (currentRating > 0) {
                updateStars(currentRating);
            }

            // Rating star functionality
            stars.forEach((star, index) => {
                star.addEventListener('click', function() {
                    currentRating = index + 1;
                    ratingInput.value = currentRating;
                    updateStars(currentRating);
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

                if (!isHover) {
                    const ratingTexts = ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
                    ratingText.textContent = rating > 0 ? `${rating}/5 - ${ratingTexts[rating]}` : 'Click to rate';
                }
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

                // Enable/disable submit button
                submitBtn.disabled = length < 10 || length > 1000 || currentRating === 0;
            }

            contentTextarea.addEventListener('input', updateCharCount);
            updateCharCount(); // Initial call

            // Form validation
            document.querySelector('form').addEventListener('submit', function(e) {
                if (currentRating === 0) {
                    e.preventDefault();
                    alert('Please select a rating before submitting.');
                    return false;
                }

                if (contentTextarea.value.length < 10) {
                    e.preventDefault();
                    alert('Please write at least 10 characters for your testimonial.');
                    return false;
                }

                if (contentTextarea.value.length > 1000) {
                    e.preventDefault();
                    alert('Your testimonial is too long. Please keep it under 1000 characters.');
                    return false;
                }
            });
        });
    </script>
    @endpush
</x-layouts.client>