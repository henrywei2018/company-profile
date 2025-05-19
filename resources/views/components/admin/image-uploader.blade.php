<!-- resources/views/components/admin/image-uploader.blade.php -->
@props([
    'name',
    'label' => null,
    'value' => null,
    'preview' => null,
    'accept' => '.jpg,.jpeg,.png,.gif,.svg,.webp',
    'disabled' => false,
    'required' => false,
    'multiple' => false,
    'helper' => null,
    'id' => null,
    'maxFileSize' => 5, // in MB
    'maxFiles' => 10,
    'showRemoveButton' => true,
    'aspectRatio' => null, // Options: '1:1', '16:9', '4:3', '3:2'
    'previewSize' => 'md' // Options: sm, md, lg
])

@php
    $id = $id ?? $name;
    $previewUrl = $preview ?? $value;
    $isMultiple = $multiple && !$aspectRatio; // Aspect ratio feature not available with multiple uploads
    
    // Preview size classes
    $previewSizeClasses = [
        'sm' => 'h-20',
        'md' => 'h-32',
        'lg' => 'h-48'
    ][$previewSize] ?? 'h-32';
    
    // Aspect ratio classes
    $aspectRatioClasses = '';
    if ($aspectRatio) {
        $aspectRatioClasses = [
            '1:1' => 'aspect-w-1 aspect-h-1',
            '16:9' => 'aspect-w-16 aspect-h-9',
            '4:3' => 'aspect-w-4 aspect-h-3',
            '3:2' => 'aspect-w-3 aspect-h-2'
        ][$aspectRatio] ?? '';
    }
@endphp

<div 
    x-data="{
        files: [],
        previewUrls: @js(is_array($previewUrl) ? $previewUrl : ($previewUrl ? [$previewUrl] : [])),
        isMultiple: {{ $isMultiple ? 'true' : 'false' }},
        isDragging: false,
        maxFiles: {{ $maxFiles }},
        maxFileSize: {{ $maxFileSize }}, // MB
        acceptedFileTypes: @js(explode(',', $accept)),
        errors: [],
        
        addFiles(newFiles) {
            if (this.disabled) return;
            
            // Clear previous errors
            this.errors = [];
            
            // Validate and add files
            const filesToAdd = Array.from(newFiles).filter(file => {
                // Check file type
                const fileExt = '.' + file.name.split('.').pop().toLowerCase();
                if (!this.acceptedFileTypes.some(type => type.toLowerCase() === fileExt)) {
                    this.errors.push(`File type ${fileExt} is not allowed.`);
                    return false;
                }
                
                // Check file size
                if (file.size > this.maxFileSize * 1024 * 1024) {
                    this.errors.push(`File ${file.name} exceeds the maximum file size of ${this.maxFileSize}MB.`);
                    return false;
                }
                
                return true;
            });
            
            // Check max files limit
            if (!this.isMultiple && filesToAdd.length > 0) {
                // Single file mode, replace the current file
                this.files = [filesToAdd[0]];
                this.previewUrls = [];
                this.createPreviewUrl(filesToAdd[0]);
            } else if (this.files.length + filesToAdd.length <= this.maxFiles) {
                // Add all valid files
                filesToAdd.forEach(file => {
                    this.files.push(file);
                    this.createPreviewUrl(file);
                });
            } else {
                this.errors.push(`Cannot add more than ${this.maxFiles} files.`);
            }
        },
        
        createPreviewUrl(file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                // For single file, replace the preview
                if (!this.isMultiple) {
                    this.previewUrls = [e.target.result];
                } else {
                    this.previewUrls.push(e.target.result);
                }
            };
            reader.readAsDataURL(file);
        },
        
        removeFile(index) {
            // If we're removing an existing file, not a new upload
            if (index < this.previewUrls.length && index >= this.files.length) {
                this.previewUrls.splice(index, 1);
            } else {
                this.files.splice(index, 1);
                this.previewUrls.splice(index, 1);
            }
            
            // Reset the input if all files are removed
            if (this.files.length === 0 && this.previewUrls.length === 0) {
                const input = this.$refs.fileInput;
                if (input) input.value = '';
            }
        }
    }"
    class="mb-4"
>
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium mb-2 {{ $disabled ? 'text-gray-400 dark:text-neutral-500' : 'text-gray-700 dark:text-neutral-300' }}">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <!-- File Drop Zone -->
    <div 
        class="border-2 border-dashed rounded-lg px-6 pt-5 pb-6 cursor-pointer transition-colors"
        :class="{
            'border-blue-400 bg-blue-50 dark:border-blue-700 dark:bg-blue-900/30': isDragging,
            'border-gray-300 hover:border-blue-400 dark:border-neutral-700 dark:hover:border-blue-700': !isDragging,
            'opacity-50 cursor-not-allowed': {{ $disabled ? 'true' : 'false' }}
        }"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="isDragging = false; addFiles($event.dataTransfer.files)"
        @click.prevent="$refs.fileInput.click()"
    >
        <div class="space-y-2 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-neutral-500" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <div class="flex text-sm text-gray-600 dark:text-neutral-400 justify-center">
                <label for="{{ $id }}" class="relative cursor-pointer bg-white dark:bg-neutral-800 rounded-md font-medium text-blue-600 dark:text-blue-500 hover:text-blue-500 dark:hover:text-blue-400 focus-within:outline-none">
                    <span>{{ $multiple ? 'Upload files' : 'Upload a file' }}</span>
                    <input 
                        id="{{ $id }}" 
                        name="{{ $name }}{{ $multiple ? '[]' : '' }}" 
                        type="file" 
                        class="sr-only" 
                        {{ $multiple ? 'multiple' : '' }} 
                        {{ $disabled ? 'disabled' : '' }}
                        {{ $required ? 'required' : '' }}
                        accept="{{ $accept }}"
                        x-ref="fileInput"
                        @change="addFiles($event.target.files)"
                    >
                </label>
                <p class="pl-1">or drag and drop</p>
            </div>
            <p class="text-xs text-gray-500 dark:text-neutral-500">
                {{ implode(', ', explode(',', $accept)) }} up to {{ $maxFileSize }}MB{{ $multiple ? ' (Max ' . $maxFiles . ' files)' : '' }}
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
    
    <!-- File Previews -->
    <div x-show="previewUrls.length > 0" class="mt-2 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
        <template x-for="(url, index) in previewUrls" :key="index">
            <div class="relative group rounded-lg border border-gray-200 dark:border-neutral-700 overflow-hidden {{ $aspectRatioClasses }}">
                <div class="{{ $aspectRatio ? '' : $previewSizeClasses }} w-full overflow-hidden bg-gray-100 dark:bg-neutral-800">
                    <img :src="url" alt="Preview" class="h-full w-full object-cover">
                </div>
                
                @if($showRemoveButton)
                    <button 
                        type="button" 
                        class="absolute top-1 right-1 size-6 bg-red-600 text-white rounded-full opacity-0 group-hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-opacity"
                        x-show="!{{ $disabled ? 'true' : 'false' }}"
                        @click.stop="removeFile(index)"
                    >
                        <svg class="size-4 m-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                @endif
            </div>
        </template>
    </div>
    
    @if($helper && !$errors->has($name))
        <div class="mt-1">
            <span class="text-xs text-gray-500 dark:text-neutral-400">{{ $helper }}</span>
        </div>
    @endif
    
    @error($name)
        <div class="mt-1">
            <span class="text-xs text-red-600 dark:text-red-500">{{ $message }}</span>
        </div>
    @enderror
</div>  