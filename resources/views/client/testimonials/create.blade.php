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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Testimonials
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <form action="{{ route('client.testimonials.store') }}" method="POST" enctype="multipart/form-data"
                class="space-y-6 p-6">
                @csrf

                <!-- Project Selection -->
                <div>
                    <label for="project_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        Related Project (Optional)
                    </label>
                    <select name="project_id" id="project_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                        <option value="">Select a project...</option>
                        @foreach ($userProjects as $project)
                            <option value="{{ $project->id }}"
                                {{ old('project_id') == $project->id ? 'selected' : '' }}>
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
                        @for ($i = 1; $i <= 5; $i++)
                            <button type="button"
                                class="rating-star text-2xl text-gray-300 hover:text-yellow-400 focus:outline-none transition-colors duration-200"
                                data-rating="{{ $i }}">
                                <svg class="w-8 h-8 fill-current" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                    </path>
                                </svg>
                            </button>
                        @endfor
                        <span id="rating-text" class="ml-2 text-sm text-gray-600 dark:text-gray-400">Click to
                            rate</span>
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
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 space-y-6">
                        <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Client Photo (Optional)
                        </label>

                        <!-- Universal File Uploader for Client Photo -->
                        <x-universal-file-uploader :id="'testimonial-photo-uploader-create'" name="testimonial_images" :multiple="false"
                            :maxFiles="1" maxFileSize="2MB" :acceptedFileTypes="['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']" :uploadEndpoint="route('admin.testimonials.temp-upload')" :deleteEndpoint="route('admin.testimonials.temp-delete')"
                            dropDescription="Drop client photo here or click to browse (Max 2MB)" :enableCategories="false"
                            :enableDescription="false" :enablePublicToggle="false" :instantUpload="true" :galleryMode="false" :replaceMode="false"
                            :singleMode="true" containerClass="mb-4" theme="modern" :showFileList="false"
                            :showProgress="true" :dragOverlay="true" />

                        @error('image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="lg:col-span-1 space-y-6">
                        <x-temp-files-display :sessionKey="'temp_testimonial_images_' . session()->getId()" title="Uploaded Client Photo"
                            emptyMessage="No client photo uploaded yet" :showPreview="true" :allowDelete="true"
                            :deleteEndpoint="route('admin.testimonials.temp-delete')" gridCols="grid-cols-1" :componentId="'temp-display-testimonial'" />
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
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
                    switch (type) {
                        case 'success':
                            return 'bg-green-500';
                        case 'error':
                            return 'bg-red-500';
                        case 'warning':
                            return 'bg-yellow-500';
                        default:
                            return 'bg-blue-500';
                    }
                }
            });
        </script>
    @endpush
</x-layouts.client>
