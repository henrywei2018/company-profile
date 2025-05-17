<!-- resources/views/components/image-gallery.blade.php -->
@props(['images' => [], 'name' => 'images', 'maxFiles' => 5, 'label' => 'Images', 'required' => false, 'helper' => null])

<div {{ $attributes->merge(['class' => 'mb-6']) }}>
    <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-200">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    
    <div 
        x-data="imageGallery({
            initialImages: @js($images),
            inputName: @js($name),
            maxFiles: @js($maxFiles),
        })"
        class="space-y-4"
    >
        <!-- Preview area -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4" x-show="images.length > 0">
            <template x-for="(image, index) in images" :key="index">
                <div class="relative group border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-800">
                    <template x-if="image.preview">
                        <div class="aspect-w-1 aspect-h-1">
                            <img :src="image.preview" class="w-full h-full object-cover" :alt="'Image ' + (index + 1)">
                        </div>
                    </template>
                    <template x-if="!image.preview">
                        <div class="aspect-w-1 aspect-h-1 flex items-center justify-center bg-gray-200 dark:bg-gray-700">
                            <svg class="w-8 h-8 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </template>
                    
                    <div class="absolute inset-0 flex flex-col justify-between opacity-0 group-hover:opacity-100 bg-black bg-opacity-50 transition duration-200">
                        <div class="p-2 flex justify-end">
                            <button type="button" @click="removeImage(index)" class="p-1 bg-red-600 text-white rounded-full hover:bg-red-700 focus:outline-none">
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="p-2 text-white text-xs">
                            <div x-text="image.name" class="truncate"></div>
                            <div x-show="image.size" x-text="formatFileSize(image.size)" class="text-gray-300"></div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        
        <!-- Message if no images -->
        <div x-show="images.length === 0" class="text-center py-8 px-4 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg">
            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No images added yet</p>
        </div>
        
        <!-- Upload button -->
        <div class="flex items-center justify-center">
            <label class="cursor-pointer bg-white dark:bg-gray-800 py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none">
                <span x-text="uploadButtonText()"></span>
                <input type="file" multiple accept="image/*" class="hidden" @change="handleFileSelect" :disabled="images.length >= maxFiles">
            </label>
        </div>
        
        <!-- Hidden inputs to store data -->
        <div>
            <template x-for="(image, index) in images.filter(img => img.id)" :key="'existing-'+index">
                <input type="hidden" :name="`${inputName}[existing][]`" :value="image.id">
            </template>
        </div>
        
        @if($helper)
            <div class="mt-1">
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $helper }}</span>
            </div>
        @endif
        
        @error($name)
            <div class="mt-1">
                <span class="text-xs text-red-600 dark:text-red-500">{{ $message }}</span>
            </div>
        @enderror
    </div>
</div>

@once
@push('scripts')
<script>
    function imageGallery(config) {
        return {
            images: [],
            inputName: config.inputName || 'images',
            maxFiles: config.maxFiles || 5,
            
            init() {
                // Initialize with existing images
                if (config.initialImages && Array.isArray(config.initialImages)) {
                    this.images = config.initialImages.map(img => {
                        return {
                            id: img.id || null,
                            name: img.name || 'Existing image',
                            preview: img.url,
                            size: null,
                            file: null
                        };
                    });
                }
            },
            
            handleFileSelect(event) {
                if (!event.target.files) return;
                
                const newFiles = Array.from(event.target.files);
                const remainingSlots = this.maxFiles - this.images.length;
                
                if (remainingSlots <= 0) {
                    alert(`Maximum ${this.maxFiles} images allowed.`);
                    return;
                }
                
                // Only take what we can fit
                const filesToAdd = newFiles.slice(0, remainingSlots);
                
                filesToAdd.forEach(file => {
                    // Create preview
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.images.push({
                            id: null,
                            name: file.name,
                            preview: e.target.result,
                            size: file.size,
                            file: file
                        });
                    };
                    reader.readAsDataURL(file);
                });
                
                // Reset the input
                event.target.value = '';
            },
            
            removeImage(index) {
                this.images.splice(index, 1);
            },
            
            uploadButtonText() {
                if (this.images.length >= this.maxFiles) {
                    return `Maximum ${this.maxFiles} images reached`;
                }
                return this.images.length > 0 ? 'Add more images' : 'Upload images';
            },
            
            formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }
        };
    }
</script>
@endpush

@push('styles')
<style>
    /* Aspect ratio utility */
    .aspect-w-1 { position: relative; padding-bottom: 100%; }
    .aspect-w-1 > * { position: absolute; height: 100%; width: 100%; top: 0; right: 0; bottom: 0; left: 0; }
</style>
@endpush
@endonce