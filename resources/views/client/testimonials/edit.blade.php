{{-- resources/views/client/testimonials/edit.blade.php --}}
<x-layouts.client title="Edit Testimoni">
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Ubah Testimoni</h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Perbarui testimoni Anda. Setelah diedit, testimoni akan ditinjau kembali oleh tim kami.
                    </p>
                    <!-- Status Badge -->
                    @php
                        $statusConfig = [
                            'pending' => ['bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200', 'Under Review'],
                            'approved' => ['bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200', 'Approved'],
                            'featured' => ['bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200', 'Featured'],
                            'rejected' => ['bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200', 'Needs Revision']
                        ];
                        $config = $statusConfig[$testimonial->status] ?? ['bg-gray-100 text-gray-800', ucfirst($testimonial->status)];
                    @endphp
                    <div class="mt-3 flex items-center space-x-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config[0] }}">
                            {{ $config[1] }}
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            Update : {{ $testimonial->updated_at->format('M d, Y \a\t g:i A') }}
                        </span>
                    </div>
                </div>
                <a href="{{ route('client.testimonials.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali to Testimoni
                </a>
            </div>
        </div>

        <!-- Status-specific Messages -->
        @if($testimonial->status === 'rejected')
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Testimoni Perlu Direvisi</h3>
                        <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                            <p>Testimoni Anda telah ditinjau dan perlu diperbaiki. Harap perbarui konten di bawah ini dan kirimkan kembali.</p>
                            @if($testimonial->admin_notes)
                                <p class="mt-2 font-medium">Tanggapan Admin: {{ $testimonial->admin_notes }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @elseif($testimonial->status === 'pending')
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Direviu</h3>
                        <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                            <p>Testimoni Anda saat ini sedang ditinjau oleh tim kami. Anda masih dapat melakukan perubahan jika diperlukan.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <form action="{{ route('client.testimonials.update', $testimonial) }}" method="POST" enctype="multipart/form-data"
                class="space-y-6 p-6">
                @csrf
                @method('PUT')

                <!-- Project Selection -->
                <div>
                    <label for="project_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        Proyek Terkait (Optional)
                    </label>
                    <select name="project_id" id="project_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300">
                        <option value="">Pilih Proyek...</option>
                        @foreach ($userProjects as $project)
                            <option value="{{ $project->id }}"
                                {{ old('project_id', $testimonial->project_id) == $project->id ? 'selected' : '' }}>
                                {{ $project->title }}
                                @if($project->status === 'completed')
                                    (Completed)
                                @elseif($project->status === 'in_progress') 
                                    (In Progress)
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('project_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Hubungkan testimonial ini ke proyek spesifik yang Anda kerjakan bersama kami.
                    </p>
                </div>

                <!-- Rating -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        Rating <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center space-x-1">
                        @for ($i = 1; $i <= 5; $i++)
                            <button type="button"
                                class="rating-star text-2xl {{ $i <= old('rating', $testimonial->rating) ? 'text-yellow-400' : 'text-gray-300' }} hover:text-yellow-400 focus:outline-none transition-colors duration-200"
                                data-rating="{{ $i }}">
                                <svg class="w-8 h-8 fill-current" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                    </path>
                                </svg>
                            </button>
                        @endfor
                        <span id="rating-text" class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                            {{ old('rating', $testimonial->rating) }}/5 - 
                            @php
                                $ratingTexts = ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
                                echo $ratingTexts[old('rating', $testimonial->rating)] ?? 'Click to rate';
                            @endphp
                        </span>
                    </div>
                    <input type="hidden" name="rating" id="rating" value="{{ old('rating', $testimonial->rating) }}">
                    @error('rating')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Testimonial Content -->
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        Testimoni <span class="text-red-500">*</span>
                    </label>
                    <textarea name="content" id="content" rows="6"
                        placeholder="Share your experience working with us. What did you like most? How did we help achieve your goals? What would you tell others about our services?"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300 resize-vertical">{{ old('content', $testimonial->content) }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <div class="flex justify-between mt-1">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Minimal 50 karakter, maksimal 1500 karakter.
                        </p>
                        <span id="char-count" class="text-xs text-gray-500 dark:text-gray-400">{{ strlen(old('content', $testimonial->content)) }}/1500</span>
                    </div>
                </div>

                <!-- Current Photo Display -->
                @if($testimonial->image)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-3">
                            Foto Client
                        </label>
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <img src="{{ Storage::disk('public')->url($testimonial->image) }}" 
                                     alt="Foto Client" 
                                     class="h-20 w-20 rounded-lg object-cover border border-gray-300 dark:border-gray-600">
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Foto saat ini akan diganti jika Anda mengunggah yang baru di bawah.
                                </p>
                                <button type="button" id="remove-current-image" 
                                        class="mt-2 text-sm text-red-600 hover:text-red-500">
                                    Hapus Foto
                                </button>
                                <input type="hidden" name="remove_image" id="remove_image" value="0">
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Photo Upload -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 space-y-6">
                        <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            {{ $testimonial->image ? 'Update Client Photo (Optional)' : 'Client Photo (Optional)' }}
                        </label>

                        <!-- Universal File Uploader for Client Photo -->
                        <x-universal-file-uploader 
                            :id="'testimonial-photo-uploader-edit-' . $testimonial->id" 
                            name="testimonial_images" 
                            :multiple="false"
                            :maxFiles="1" 
                            maxFileSize="2MB" 
                            :acceptedFileTypes="['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']" 
                            :uploadEndpoint="route('client.testimonials.temp-upload')" 
                            :deleteEndpoint="route('client.testimonials.temp-delete')"
                            dropDescription="Drop new client photo here or click to browse (Max 2MB)" 
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

                        @error('image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="lg:col-span-1 space-y-6">
                        <x-temp-files-display 
                            :sessionKey="'temp_testimonial_images_' . session()->getId()" 
                            title="Foto Baru Client"
                            emptyMessage="No new photo uploaded" 
                            :showPreview="true" 
                            :allowHapus="true"
                            :deleteEndpoint="route('client.testimonials.temp-delete')" 
                            gridCols="grid-cols-1" 
                            :componentId="'temp-display-testimonial-edit'" />
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('client.testimonials.index') }}"
                        class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        Batal
                    </a>

                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                        id="submit-btn">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                            Update Testimoni
                        </span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Deletion Peringatan (only for pending/rejected testimonials) -->
        @if(in_array($testimonial->status, ['pending', 'rejected']))
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd"/>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2h8a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 2a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Hapus Testimonial</h3>
                        <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                            <p>Jika testimonial ini tidak lagi diperlukan, Anda dapat menghapusnya. Tindakan ini tidak dapat dibatalkan.</p>
                        </div>
                        <div class="mt-4">
                            <form action="{{ route('client.testimonials.destroy', $testimonial) }}" method="POST" 
                                  onsubmit="return confirm('Yakin ingin menghapus testimonial ini? Tindakan ini tidak dapat dibatalkan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                                    Hapus Testimonial
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const stars = document.querySelectorAll('.rating-star');
                const ratingInput = document.getElementById('rating');
                const ratingText = document.getElementById('rating-text');
                const contentTextarea = document.getElementById('content');
                const charCount = document.getElementById('char-count');
                const submitBtn = document.getElementById('submit-btn');
                const removeImageBtn = document.getElementById('remove-current-image');
                const removeImageInput = document.getElementById('remove_image');

                let currentRating = {{ old('rating', $testimonial->rating) }};

                // Initialize rating display
                updateStars(currentRating);

                // Rating star functionality
                stars.forEach((star, index) => {
                    star.addEventListener('click', function() {
                        currentRating = index + 1;
                        ratingInput.value = currentRating;
                        updateStars(currentRating);
                        validateForm();
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
                    charCount.textContent = `${length}/1500`;

                    if (length > 1500) {
                        charCount.classList.add('text-red-500');
                        charCount.classList.remove('text-gray-500', 'text-yellow-500');
                    } else if (length < 50) {
                        charCount.classList.add('text-yellow-500');
                        charCount.classList.remove('text-gray-500', 'text-red-500');
                    } else {
                        charCount.classList.remove('text-red-500', 'text-yellow-500');
                        charCount.classList.add('text-gray-500');
                    }

                    validateForm();
                }

                // Form validation
                function validateForm() {
                    const length = contentTextarea.value.length;
                    const isValid = length >= 50 && length <= 1500 && currentRating > 0;
                    
                    submitBtn.disabled = !isValid;
                    
                    if (!isValid) {
                        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    } else {
                        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                }

                contentTextarea.addEventListener('input', updateCharCount);
                updateCharCount(); // Initial call

                // Remove current image functionality
                if (removeImageBtn) {
                    removeImageBtn.addEventListener('click', function() {
                        if (confirm('Apakah Anda yakin ingin sampai menghapus foto saat ini?')) {
                            removeImageInput.value = '1';
                            this.closest('.bg-gray-50').style.display = 'none';
                            showNotification('Current photo will be removed when you save the testimonial.', 'info');
                        }
                    });
                }

                // Enhanced form validation on submit
                document.querySelector('form').addEventListener('submit', function(e) {
                    if (currentRating === 0) {
                        e.preventDefault();
                        showNotification('Please select a rating before submitting.', 'error');
                        return false;
                    }

                    const length = contentTextarea.value.length;
                    if (length < 50) {
                        e.preventDefault();
                        showNotification('Please write at least 50 characters for your testimonial.', 'error');
                        return false;
                    }

                    if (length > 1500) {
                        e.preventDefault();
                        showNotification('Your testimonial is too long. Please keep it under 1500 characters.', 'error');
                        return false;
                    }

                    // Show loading state
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `
                        <span class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Updating...
                        </span>
                    `;
                });

                // Universal uploader event handling
                const uploaderId = 'testimonial-photo-uploader-edit-{{ $testimonial->id }}';

                // Listen for universal uploader events
                document.addEventListener('files-uploaded', function(event) {
                    if (event.detail.component && event.detail.component.includes(uploaderId)) {
                        handleTempImageUploadBerhasil(event.detail);
                    }
                });

                document.addEventListener('file-deleted', function(event) {
                    if (event.detail.component && event.detail.component.includes(uploaderId)) {
                        handleTempImageHapus(event.detail);
                    }
                });

                // Handle temporary image upload success
                function handleTempImageUploadBerhasil(detail) {
                    showNotification(detail.message || 'New client photo uploaded successfully!', 'success');
                    console.log('Client testimonial temp image uploaded:', detail);
                }

                // Handle temporary image deletion
                function handleTempImageHapus(detail) {
                    showNotification(detail.message || 'New client photo removed!', 'info');
                    console.log('Client testimonial temp image deleted:', detail);
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
                    toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 transition-opacity duration-300 ${getToastColor(type)}`;
                    toast.textContent = message;
                    toast.style.opacity = '0';

                    document.body.appendChild(toast);

                    setTimeout(() => {
                        toast.style.opacity = '1';
                    }, 100);

                    setTimeout(() => {
                        toast.style.opacity = '0';
                        setTimeout(() => {
                            if (document.body.contains(toast)) {
                                document.body.removeChild(toast);
                            }
                        }, 300);
                    }, 4000);
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