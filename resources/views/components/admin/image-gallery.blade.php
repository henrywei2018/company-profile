<!-- resources/views/components/admin/image-gallery.blade.php -->
@props([
    'name' => 'images',
    'label' => 'Image Gallery',
    'images' => [],
    'maxFiles' => 10,
    'accept' => '.jpg,.jpeg,.png,.webp',
    'helper' => null,
    'error' => null,
    'aspectRatio' => null, // Options: '1:1', '16:9', '4:3', '3:2'
    'columns' => 4,
    'lightbox' => true,
    'showRemoveButton' => true,
    'showFeaturedToggle' => true,
    'showAltTextField' => true
])

@php
    // Convert aspect ratio to CSS classes if needed
    $aspectRatioClasses = '';
    if ($aspectRatio) {
        $aspectRatioClasses = match($aspectRatio) {
            '1:1' => 'aspect-square',
            '16:9' => 'aspect-video',
            '4:3' => 'aspect-[4/3]',
            '3:2' => 'aspect-[3/2]',
            default => ''
        };
    }
    
    // Column classes
    $colClasses = match(min(6, max(1, intval($columns)))) {
        1 => 'grid-cols-1',
        2 => 'grid-cols-1 sm:grid-cols-2',
        3 => 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3',
        4 => 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4',
        5 => 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5',
        6 => 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6',
    };
@endphp

<div class="mb-4 space-y-4" x-data="imageGallery()">
    @if($label)
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            {{ $label }}
        </label>
    @endif
    
    <!-- Dropzone -->
    <div 
        class="border-2 border-dashed rounded-lg p-4 text-center cursor-pointer transition-colors hover:border-blue-400 dark:hover:border-blue-700" 
        x-on:dragover.prevent="isDragging = true"
        x-on:dragleave.prevent="isDragging = false"
        x-on:drop.prevent="handleDrop"
        x-on:click="$refs.fileInput.click()"
        x-bind:class="{'bg-blue-50 border-blue-400 dark:bg-blue-900/30 dark:border-blue-700': isDragging}"
    >
        <input 
            type="file" 
            name="{{ $name }}[]" 
            id="{{ $name }}" 
            multiple 
            accept="{{ $accept }}"
            class="hidden" 
            x-ref="fileInput"
            x-on:change="handleFileSelect"
        >
        
        <div class="space-y-2">
            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-neutral-500" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <div class="flex text-sm text-gray-600 dark:text-neutral-400 justify-center">
                <span class="relative bg-white dark:bg-neutral-800 rounded-md font-medium text-blue-600 dark:text-blue-500 hover:text-blue-500 dark:hover:text-blue-400 cursor-pointer">
                    Upload images
                </span>
                <p class="pl-1">or drag and drop</p>
            </div>
            <p class="text-xs text-gray-500 dark:text-neutral-500">
                {{ $accept }} up to {{ $maxFiles }} files
            </p>
        </div>
    </div>
    
    <!-- Error Messages -->
    <div x-show="errors.length > 0" class="mt-2">
        <ul class="text-sm text-red-600 dark:text-red-500 space-y-1">
            <template x-for="error in errors" :key="error">
                <li x-text="error"></li>
            </template>
        </ul>
    </div>
    
    @error($name)
        <div class="mt-1">
            <span class="text-xs text-red-600 dark:text-red-500">{{ $message }}</span>
        </div>
    @enderror
    
    <!-- Existing Images Preview -->
    <div x-show="existingImages.length > 0 || newImages.length > 0" class="mt-4">
        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Project Images</h3>
        
        <div class="grid {{ $colClasses }} gap-4">
            <!-- Existing Images -->
            <template x-for="(image, index) in existingImages" :key="'existing-'+index">
                <div class="border border-gray-200 dark:border-neutral-700 rounded-lg overflow-hidden bg-white dark:bg-neutral-800 relative group">
                    <!-- Image with aspect ratio -->
                    <div class="{{ $aspectRatioClasses }} relative overflow-hidden">
                        <img :src="image.url" :alt="image.alt || ''" class="w-full h-full object-cover">
                        
                        @if($lightbox)
                        <!-- Lightbox trigger -->
                        <button 
                            type="button" 
                            class="absolute inset-0 w-full h-full bg-black bg-opacity-0 group-hover:bg-opacity-20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                            x-on:click.stop="openLightbox(index)"
                        >
                            <svg class="w-8 h-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                            </svg>
                        </button>
                        @endif
                        
                        @if($showFeaturedToggle)
                        <!-- Featured toggle -->
                        <div class="absolute top-2 left-2">
                            <input 
                                type="radio" 
                                :name="'featured_image'" 
                                :id="'featured_'+image.id" 
                                :value="image.id" 
                                :checked="image.featured"
                                class="hidden peer" 
                                x-on:change="setFeatured(index)"
                            >
                            <label 
                                :for="'featured_'+image.id" 
                                class="inline-flex items-center justify-center size-8 rounded-full border-2 cursor-pointer transition-colors bg-white/80 border-gray-300 text-gray-400 peer-checked:bg-yellow-100 peer-checked:border-yellow-400 peer-checked:text-yellow-600 hover:border-gray-400 dark:bg-neutral-700/80 dark:border-neutral-600 dark:text-neutral-400 dark:peer-checked:bg-yellow-900/30 dark:peer-checked:border-yellow-500 dark:peer-checked:text-yellow-500"
                                title="Set as featured image"
                            >
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-bind:class="{'fill-current': image.featured}">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                            </label>
                            <input type="hidden" :name="'existing_images[]'" :value="image.id">
                        </div>
                        @endif
                    </div>
                    
                    <!-- Image details -->
                    <div class="p-3">
                        @if($showAltTextField)
                        <div class="mb-2">
                            <label :for="'alt_text_'+index" class="sr-only">Alt Text</label>
                            <input 
                                type="text" 
                                :name="'existing_alt_text[]'" 
                                :id="'alt_text_'+index" 
                                :value="image.alt" 
                                placeholder="Alt text for image" 
                                class="w-full text-sm border-gray-300 rounded-md dark:bg-neutral-700 dark:border-neutral-600 dark:text-white"
                                x-on:change="updateAltText(index, $event.target.value)"
                            >
                        </div>
                        @endif
                        
                        <!-- Actions -->
                        <div class="flex justify-end gap-2">
                            @if($showRemoveButton)
                            <button 
                                type="button" 
                                class="inline-flex items-center p-1 border border-transparent rounded-full shadow-sm text-white bg-red-600 hover:bg-red-700" 
                                x-on:click.stop="removeExistingImage(index)"
                                title="Remove image"
                            >
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                            @endif
                            
                            <!-- You can add additional actions here if needed -->
                            {{ $actions ?? '' }}
                        </div>
                    </div>
                </div>
            </template>
            
            <!-- New Images -->
            <template x-for="(image, index) in newImages" :key="'new-'+index">
                <div class="border border-gray-200 dark:border-neutral-700 rounded-lg overflow-hidden bg-white dark:bg-neutral-800 relative group">
                    <!-- Image with aspect ratio -->
                    <div class="{{ $aspectRatioClasses }} relative overflow-hidden">
                        <img :src="image.url" :alt="image.name || ''" class="w-full h-full object-cover">
                        
                        <div class="absolute top-0 right-0 bg-blue-500 text-white text-xs px-2 py-1">
                            New
                        </div>
                    </div>
                    
                    <!-- Image details -->
                    <div class="p-3">
                        @if($showAltTextField)
                        <div class="mb-2">
                            <label :for="'new_alt_text_'+index" class="sr-only">Alt Text</label>
                            <input 
                                type="text" 
                                :name="'new_alt_text[]'" 
                                :id="'new_alt_text_'+index" 
                                placeholder="Alt text for image" 
                                class="w-full text-sm border-gray-300 rounded-md dark:bg-neutral-700 dark:border-neutral-600 dark:text-white"
                            >
                        </div>
                        @endif
                        
                        <!-- Actions -->
                        <div class="flex justify-end gap-2">
                            <button 
                                type="button" 
                                class="inline-flex items-center p-1 border border-transparent rounded-full shadow-sm text-white bg-red-600 hover:bg-red-700" 
                                x-on:click.stop="removeNewImage(index)"
                                title="Remove image"
                            >
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
    
    @if($helper)
        <div class="mt-1">
            <span class="text-xs text-gray-500 dark:text-neutral-400">{{ $helper }}</span>
        </div>
    @endif
    
    <!-- Lightbox -->
    @if($lightbox)
    <template x-teleport="body">
        <div 
            x-show="lightboxOpen" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-90"
            x-on:click="closeLightbox"
        >
            <div 
                class="relative max-w-4xl max-h-[90vh] mx-auto p-2" 
                x-on:click.stop
            >
                <!-- Close button -->
                <button 
                    type="button" 
                    class="absolute top-4 right-4 z-10 size-10 flex items-center justify-center rounded-full bg-black bg-opacity-50 text-white hover:bg-opacity-70 focus:outline-none"
                    x-on:click="closeLightbox"
                >
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                
                <!-- Image -->
                <div class="flex items-center justify-center h-full">
                    <img 
                        x-show="lightboxIndex !== null" 
                        x-bind:src="lightboxIndex !== null ? (lightboxIndex < existingImages.length ? existingImages[lightboxIndex].url : newImages[lightboxIndex - existingImages.length].url) : ''" 
                        class="max-h-[85vh] max-w-full object-contain"
                    >
                </div>
                
                <!-- Navigation buttons -->
                <div class="absolute inset-y-0 left-4 flex items-center">
                    <button 
                        type="button" 
                        class="p-2 rounded-full bg-black bg-opacity-50 text-white hover:bg-opacity-70 focus:outline-none"
                        x-on:click.stop="prevImage"
                        x-show="existingImages.length + newImages.length > 1"
                    >
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                </div>
                
                <div class="absolute inset-y-0 right-4 flex items-center">
                    <button 
                        type="button" 
                        class="p-2 rounded-full bg-black bg-opacity-50 text-white hover:bg-opacity-70 focus:outline-none"
                        x-on:click.stop="nextImage"
                        x-show="existingImages.length + newImages.length > 1"
                    >
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </template>
    @endif
</div>

<script>
    function imageGallery() {
        return {
            existingImages: @json($images ?? []),
            newImages: [],
            isDragging: false,
            errors: [],
            lightboxOpen: false,
            lightboxIndex: null,
            
            init() {
                // Set one image as featured if none is marked
                if (this.existingImages.length > 0 && !this.existingImages.some(img => img.featured)) {
                    this.existingImages[0].featured = true;
                }
            },
            
            handleFileSelect(event) {
                this.addFiles(event.target.files);
            },
            
            handleDrop(event) {
                this.isDragging = false;
                this.addFiles(event.dataTransfer.files);
            },
            
            addFiles(fileList) {
                this.errors = [];
                
                // Check if we've reached the maximum number of files
                if (this.existingImages.length + this.newImages.length + fileList.length > {{ $maxFiles }}) {
                    this.errors.push(`Maximum of {{ $maxFiles }} images allowed.`);
                    return;
                }
                
                // Check file types and create previews
                Array.from(fileList).forEach(file => {
                    // Check file type
                    if (!file.type.match('image.*')) {
                        this.errors.push(`${file.name} is not an image.`);
                        return;
                    }
                    
                    // Check file size (5MB max by default)
                    if (file.size > 5 * 1024 * 1024) {
                        this.errors.push(`${file.name} exceeds the maximum file size of 5MB.`);
                        return;
                    }
                    
                    // Create preview URL
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.newImages.push({
                            file: file,
                            name: file.name,
                            url: e.target.result,
                        });
                    };
                    reader.readAsDataURL(file);
                });
            },
            
            removeExistingImage(index) {
                if (confirm('Are you sure you want to remove this image?')) {
                    const removedImage = this.existingImages.splice(index, 1)[0];
                    
                    // If the removed image was featured, set the first remaining image as featured
                    if (removedImage.featured && this.existingImages.length > 0) {
                        this.existingImages[0].featured = true;
                    }
                }
            },
            
            removeNewImage(index) {
                this.newImages.splice(index, 1);
            },
            
            setFeatured(index) {
                // Clear all featured flags
                this.existingImages.forEach(img => img.featured = false);
                
                // Set selected image as featured
                this.existingImages[index].featured = true;
            },
            
            updateAltText(index, text) {
                this.existingImages[index].alt = text;
            },
            
            // Lightbox functionality
            openLightbox(index) {
                this.lightboxIndex = index;
                this.lightboxOpen = true;
                document.body.classList.add('overflow-hidden');
            },
            
            closeLightbox() {
                this.lightboxOpen = false;
                document.body.classList.remove('overflow-hidden');
            },
            
            prevImage() {
                const totalImages = this.existingImages.length + this.newImages.length;
                this.lightboxIndex = (this.lightboxIndex - 1 + totalImages) % totalImages;
            },
            
            nextImage() {
                const totalImages = this.existingImages.length + this.newImages.length;
                this.lightboxIndex = (this.lightboxIndex + 1) % totalImages;
            }
        }
    }
</script>