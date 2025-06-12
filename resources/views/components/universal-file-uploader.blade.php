@props([
    'name' => 'files',
    'multiple' => true,
    'maxFiles' => 10,
    'maxFileSize' => '10MB',
    'acceptedFileTypes' => [],
    'dropDescription' => 'Drop files here or click to browse',
    'uploadEndpoint' => null,
    'deleteEndpoint' => null,
    'previewEndpoint' => null,
    'allowPreview' => true,
    'showProgress' => true,
    'enableCategories' => false,
    'categories' => [],
    'enableDescription' => false,
    'enablePublicToggle' => false,
    'autoUpload' => false,
    'uploadOnDrop' => false,
    'existingFiles' => [],
    'compact' => false,
    'theme' => 'default', // default, minimal, modern
    'containerClass' => '',
    'id' => null,
    'singleMode' => false, // NEW: Force single file mode
    'replaceMode' => false, // NEW: Replace existing files instead of adding
    'showFileList' => true, // NEW: Show/hide file list
    'instantUpload' => false, // NEW: Upload immediately on selection
    'galleryMode' => false, // NEW: Show files in gallery grid
    'dragOverlay' => true, // NEW: Show drag overlay
])

@php
    $componentId = $id ?? 'uploader_' . uniqid();
    $defaultAcceptedTypes = [
        'image/*',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/plain',
        'text/csv',
        'application/zip',
    ];
    $fileTypes = !empty($acceptedFileTypes) ? $acceptedFileTypes : $defaultAcceptedTypes;
    
    // Auto-adjust settings for single mode
    if ($singleMode) {
        $multiple = false;
        $maxFiles = 1;
        $replaceMode = true;
    }
@endphp

<div x-data="enhancedFileUploader({
    componentId: '{{ $componentId }}',
    name: '{{ $name }}',
    multiple: {{ $multiple ? 'true' : 'false' }},
    maxFiles: {{ $maxFiles }},
    maxFileSize: '{{ $maxFileSize }}',
    acceptedFileTypes: @js($fileTypes),
    uploadEndpoint: '{{ $uploadEndpoint }}',
    deleteEndpoint: '{{ $deleteEndpoint }}',
    previewEndpoint: '{{ $previewEndpoint }}',
    allowPreview: {{ $allowPreview ? 'true' : 'false' }},
    showProgress: {{ $showProgress ? 'true' : 'false' }},
    enableCategories: {{ $enableCategories ? 'true' : 'false' }},
    categories: @js($categories),
    enableDescription: {{ $enableDescription ? 'true' : 'false' }},
    enablePublicToggle: {{ $enablePublicToggle ? 'true' : 'false' }},
    autoUpload: {{ $autoUpload ? 'true' : 'false' }},
    uploadOnDrop: {{ $uploadOnDrop ? 'true' : 'false' }},
    existingFiles: @js($existingFiles),
    compact: {{ $compact ? 'true' : 'false' }},
    theme: '{{ $theme }}',
    singleMode: {{ $singleMode ? 'true' : 'false' }},
    replaceMode: {{ $replaceMode ? 'true' : 'false' }},
    showFileList: {{ $showFileList ? 'true' : 'false' }},
    instantUpload: {{ $instantUpload ? 'true' : 'false' }},
    galleryMode: {{ $galleryMode ? 'true' : 'false' }},
    dragOverlay: {{ $dragOverlay ? 'true' : 'false' }}
})" class="enhanced-file-uploader {{ $containerClass }}" id="{{ $componentId }}">

    <!-- Drop Zone -->
    <div class="relative">
        <!-- Main Drop Area -->
        <div @dragover.prevent="handleDragOver($event)" 
             @dragleave.prevent="handleDragLeave($event)" 
             @drop.prevent="handleDrop($event)"
             @click="$refs.fileInput.click()"
             :class="{
                'border-blue-500 bg-blue-50 dark:bg-blue-900/20': dragOver,
                'border-gray-300 dark:border-gray-600': !dragOver,
                'p-3': compact,
                'p-6': !compact && !singleMode,
                'p-4': singleMode,
                'cursor-pointer': true,
                'border-2 border-dashed rounded-xl': !galleryMode,
                'rounded-lg border': galleryMode
             }"
             class="relative transition-all duration-300 hover:border-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 group"
             :class="getThemeClasses()">
             
            <!-- Drop Zone Content -->
            <div v-if="!singleMode || (!existingFiles.length && !files.length)" class="text-center">
                <!-- Icon & Content for Normal Mode -->
                <div v-if="!compact && !singleMode" class="space-y-4">
                    <div class="relative mx-auto w-20 h-20">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full opacity-20 group-hover:opacity-30 transition-opacity"></div>
                        <div class="relative w-full h-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-gray-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 12l2 2 4-4"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                            {{ $dropDescription }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                            Maximum {{ $maxFiles }} files, {{ $maxFileSize }} each
                        </p>
                    </div>
                    <button type="button" class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transform hover:scale-105 transition-all duration-200 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Choose Files
                    </button>
                </div>
            </div>
            
            <!-- Single Mode - Show Current File -->
            <div v-else-if="singleMode && (existingFiles.length || files.length)" class="text-center">
                <!-- Show existing or selected file -->
                <template x-if="files.length > 0">
                    <div class="space-y-3">
                        <div class="mx-auto w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="files[0].name"></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="formatFileSize(files[0].size)"></p>
                        </div>
                        <p class="text-xs text-blue-600 dark:text-blue-400">Click to replace</p>
                    </div>
                </template>
                
                <template x-if="files.length === 0 && existingFiles.length > 0">
                    <div class="space-y-3">
                        <div class="mx-auto w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="existingFiles[0].name || existingFiles[0].file_name"></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Current file</p>
                        </div>
                        <p class="text-xs text-blue-600 dark:text-blue-400">Click to replace</p>
                    </div>
                </template>
            </div>

            <!-- Hidden File Input -->
            <input x-ref="fileInput" 
                   type="file" 
                   :name="multiple ? name + '[]' : name" 
                   :multiple="multiple && !singleMode" 
                   :accept="acceptedFileTypes.join(',')" 
                   @change="handleFileSelect($event)" 
                   class="hidden">
        </div>

        <!-- Drag Overlay -->
        <div x-show="dragOver && dragOverlay" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-blue-500 bg-opacity-20 rounded-xl flex items-center justify-center z-10 pointer-events-none">
            <div class="text-center">
                <svg class="w-16 h-16 text-blue-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 12l2 2 4-4"/>
                </svg>
                <p class="text-lg font-semibold text-blue-800">Drop files here!</p>
            </div>
        </div>
    </div>

    <!-- Global Progress Bar -->
    <div x-show="globalProgress.show && showProgress" x-transition class="mt-4">
        <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-2">
            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" :style="`width: ${globalProgress.percentage}%`"></div>
        </div>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1" x-text="globalProgress.text"></p>
    </div>

    <!-- File Lists -->
    <div x-show="showFileList">
        <!-- Existing Files -->
        <div x-show="existingFiles.length > 0 && !singleMode" class="mt-6">
            <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Current Files</h4>
            <div :class="galleryMode ? 'grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4' : 'space-y-3'">
                <template x-for="(file, index) in existingFiles" :key="'existing-' + index">
                    <div class="relative bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow"
                         :class="galleryMode ? 'p-3' : 'p-4'">
                        
                        <!-- Gallery Mode Layout -->
                        <div x-show="galleryMode" class="text-center">
                            <div class="w-full h-24 rounded-lg flex items-center justify-center mb-2" :class="getFileIcon(file.type || file.file_type)">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate" x-text="file.name || file.file_name"></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="file.size || formatFileSize(file.file_size || 0)"></p>
                        </div>
                        
                        <!-- List Mode Layout -->
                        <div x-show="!galleryMode" class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-12 h-12 rounded-lg flex items-center justify-center" :class="getFileIcon(file.type || file.file_type)">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate" x-text="file.name || file.file_name"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="file.size || formatFileSize(file.file_size || 0)"></p>
                            </div>
                        </div>

                        <!-- File Actions -->
                        <div class="flex items-center space-x-2 mt-3" :class="galleryMode ? 'justify-center' : ''">
                            
                            <button v-if="deleteEndpoint" @click="deleteExistingFile(file, index)" class="text-red-600 hover:text-red-800 dark:text-red-400 text-xs">
                                Delete
                            </button>
                        </div>

                        <!-- Delete button for single mode -->
                        <button v-if="singleMode && deleteEndpoint" @click="deleteExistingFile(file, index)"
                                class="absolute top-2 right-2 p-1 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 rounded-full">
                            <svg class="w-3 h-3 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <!-- Selected Files (for upload) -->
        <div x-show="files.length > 0 && !singleMode" x-transition class="mt-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white">Selected Files</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="`${files.length} file${files.length > 1 ? 's' : ''} ready for upload`"></p>
                    </div>
                </div>
                <button @click="clearAllFiles()" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium">
                    Clear All
                </button>
            </div>

            <!-- Selected Files Grid/List -->
            <div :class="galleryMode ? 'grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4' : 'space-y-3'" class="mb-6">
                <template x-for="(file, index) in files" :key="'selected-' + index">
                    <div class="relative bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow"
                         :class="galleryMode ? 'p-3' : 'p-4'">
                        
                        <!-- Gallery Mode Layout -->
                        <div x-show="galleryMode" class="text-center">
                            <div class="w-full h-24 rounded-lg flex items-center justify-center mb-2" :class="getFileIcon(file.type)">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate" x-text="file.name"></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="formatFileSize(file.size)"></p>
                        </div>
                        
                        <!-- List Mode Layout -->
                        <div x-show="!galleryMode" class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-12 h-12 rounded-lg flex items-center justify-center" :class="getFileIcon(file.type)">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate" x-text="file.name"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="formatFileSize(file.size)"></p>
                            </div>
                        </div>

                        <!-- Upload Progress -->
                        <div x-show="file.uploading" class="mt-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-500 dark:text-gray-400">Uploading...</span>
                                <span class="text-gray-500 dark:text-gray-400" x-text="`${file.progress || 0}%`"></span>
                            </div>
                            <div class="mt-1 bg-gray-200 dark:bg-gray-700 rounded-full h-1">
                                <div class="bg-blue-600 h-1 rounded-full transition-all duration-300" :style="`width: ${file.progress || 0}%`"></div>
                            </div>
                        </div>

                        <!-- Upload Status -->
                        <div x-show="file.uploaded" class="mt-2 flex items-center text-xs text-green-600 dark:text-green-400">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Uploaded
                        </div>

                        <div x-show="file.error" class="mt-2 flex items-center text-xs text-red-600 dark:text-red-400">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span x-text="file.error"></span>
                        </div>

                        <!-- Remove Button -->
                        <button @click="removeFile(index)" class="absolute top-2 right-2 p-1 bg-gray-100 hover:bg-red-100 dark:bg-gray-700 dark:hover:bg-red-900/30 rounded-full transition-colors">
                            <svg class="w-3 h-3 text-gray-400 hover:text-red-600 dark:hover:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Upload Options for Multiple Files -->
    <div x-show="files.length > 0 && !autoUpload && !instantUpload && !singleMode && (enableCategories || enableDescription || enablePublicToggle)" 
         class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-6 mt-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <!-- Category -->
            <div x-show="enableCategories && categories.length > 0">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                <select x-model="uploadOptions.category" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <option value="">Select Category</option>
                    <template x-for="category in categories" :key="category.value">
                        <option :value="category.value" x-text="category.label"></option>
                    </template>
                </select>
            </div>

            <!-- Description -->
            <div x-show="enableDescription">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                <input type="text" x-model="uploadOptions.description" placeholder="Optional description" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
            </div>

            <!-- Public Access -->
            <div x-show="enablePublicToggle" class="flex items-end">
                <label class="flex items-center">
                    <input type="checkbox" x-model="uploadOptions.isPublic" class="rounded border-gray-300 text-blue-600 focus:border-blue-500 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Make files public</span>
                </label>
            </div>
        </div>

        <!-- Upload Actions -->
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                <span x-text="`${files.length} file${files.length > 1 ? 's' : ''} selected`"></span>
                <span class="mx-1">•</span>
                <span x-text="`Total size: ${formatFileSize(getTotalSize())}`"></span>
            </div>

            <button @click="uploadFiles()" :disabled="files.length === 0 || uploading" :class="{ 'opacity-50 cursor-not-allowed': files.length === 0 || uploading }" class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transform hover:scale-105 transition-all duration-200 shadow-lg disabled:transform-none">
                <svg x-show="!uploading" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <svg x-show="uploading" class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="uploading ? 'Uploading...' : 'Upload Files'"></span>
            </button>
        </div>
    </div>

    <!-- Preview Modal -->
    <div x-show="previewModal.show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closePreview()">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="fixed inset-0 bg-black bg-opacity-50"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white" x-text="previewModal.title"></h3>
                    <button @click="closePreview()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="p-6">
                    <div id="preview-content" x-html="previewModal.content">
                        <!-- Preview content will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Alpine.js Component Script -->
<script>
function enhancedFileUploader(config = {}) {
    return {
        // Configuration
        componentId: config.componentId || 'uploader_' + Date.now(),
        name: config.name || 'files',
        multiple: config.multiple ?? true,
        maxFiles: config.maxFiles || 10,
        maxFileSize: config.maxFileSize || '10MB',
        acceptedFileTypes: config.acceptedFileTypes || [],
        uploadEndpoint: config.uploadEndpoint,
        deleteEndpoint: config.deleteEndpoint,
        previewEndpoint: config.previewEndpoint,
        allowPreview: config.allowPreview ?? true,
        showProgress: config.showProgress ?? true,
        enableCategories: config.enableCategories ?? false,
        categories: config.categories || [],
        enableDescription: config.enableDescription ?? false,
        enablePublicToggle: config.enablePublicToggle ?? false,
        autoUpload: config.autoUpload ?? false,
        uploadOnDrop: config.uploadOnDrop ?? false,
        compact: config.compact ?? false,
        theme: config.theme || 'default',
        singleMode: config.singleMode ?? false,
        replaceMode: config.replaceMode ?? false,
        showFileList: config.showFileList ?? true,
        instantUpload: config.instantUpload ?? false,
        galleryMode: config.galleryMode ?? false,
        dragOverlay: config.dragOverlay ?? true,

        // State
        dragOver: false,
        dragCounter: 0, // Track nested drag events
        files: [],
        existingFiles: config.existingFiles || [],
        uploading: false,
        uploadOptions: {
            category: '',
            description: '',
            isPublic: false
        },
        globalProgress: {
            show: false,
            percentage: 0,
            text: ''
        },
        previewModal: {
            show: false,
            title: '',
            content: ''
        },

        // Enhanced Event Handlers
        handleDragOver(event) {
            event.preventDefault();
            this.dragOver = true;
        },

        handleDragLeave(event) {
            event.preventDefault();
            this.dragCounter--;
            if (this.dragCounter === 0) {
                this.dragOver = false;
            }
        },

        handleDrop(event) {
            event.preventDefault();
            this.dragOver = false;
            this.dragCounter = 0;
            
            const droppedFiles = Array.from(event.dataTransfer.files);
            this.addFiles(droppedFiles);

            if ((this.uploadOnDrop || this.instantUpload) && this.uploadEndpoint) {
                this.uploadFiles();
            }
        },

        handleFileSelect(event) {
            const selectedFiles = Array.from(event.target.files);
            this.addFiles(selectedFiles);
            event.target.value = ''; // Reset input

            if ((this.autoUpload || this.instantUpload) && this.uploadEndpoint) {
                this.uploadFiles();
            }
        },

        addFiles(newFiles) {
            newFiles.forEach(file => {
                // Single mode: replace existing
                if (this.singleMode) {
                    this.files = [];
                    if (this.replaceMode) {
                        this.existingFiles = [];
                    }
                }

                // Check file limit
                if (!this.singleMode && this.files.length >= this.maxFiles) {
                    this.showNotification(`Maximum ${this.maxFiles} files allowed`, 'warning');
                    return;
                }

                // Validate file
                if (!this.validateFile(file)) {
                    return;
                }

                // Add file
                const fileObj = {
                    file: file,
                    name: file.name,
                    size: file.size,
                    type: file.type,
                    uploading: false,
                    uploaded: false,
                    progress: 0,
                    error: null,
                    id: Date.now() + Math.random() // Unique ID
                };

                this.files.push(fileObj);

                // Auto-upload for single mode with instant upload
                if (this.singleMode && this.instantUpload && this.uploadEndpoint) {
                    this.uploadFiles();
                }
            });

            // Emit event for external handling
            this.$dispatch('files-selected', {
                files: this.files,
                component: this.componentId
            });
        },

        validateFile(file) {
            // Size validation
            const maxSizeBytes = this.parseFileSize(this.maxFileSize);
            if (file.size > maxSizeBytes) {
                this.showNotification(`File "${file.name}" exceeds ${this.maxFileSize} limit`, 'error');
                return false;
            }

            // Type validation
            if (this.acceptedFileTypes.length > 0) {
                const isValidType = this.acceptedFileTypes.some(type => {
                    if (type.includes('*')) {
                        return file.type.startsWith(type.split('/')[0] + '/');
                    }
                    return file.type === type;
                });

                if (!isValidType) {
                    this.showNotification(`File type "${file.type}" is not allowed`, 'error');
                    return false;
                }
            }

            return true;
        },

        removeFile(index) {
            this.files.splice(index, 1);
            
            this.$dispatch('file-removed', {
                index: index,
                component: this.componentId
            });
        },

        clearAllFiles() {
            this.files = [];
            
            this.$dispatch('files-cleared', {
                component: this.componentId
            });
        },

        async uploadFiles() {
            if (this.files.length === 0 || !this.uploadEndpoint) return;

            this.uploading = true;
            this.showGlobalProgress(true, 0, 'Preparing upload...');

            try {
                const formData = new FormData();

                // Add files to form data
                this.files.forEach((fileObj, index) => {
                    if (this.singleMode) {
                        formData.append(this.name, fileObj.file);
                    } else {
                        formData.append(`${this.name}[]`, fileObj.file);
                    }
                    fileObj.uploading = true;
                });

                // Add upload options
                if (this.enableCategories && this.uploadOptions.category) {
                    formData.append('category', this.uploadOptions.category);
                }
                if (this.enableDescription && this.uploadOptions.description) {
                    formData.append('description', this.uploadOptions.description);
                }
                if (this.enablePublicToggle) {
                    formData.append('is_public', this.uploadOptions.isPublic ? '1' : '0');
                }

                // Add CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (csrfToken) {
                    formData.append('_token', csrfToken);
                }

                const result = await this.performUpload(formData);

                if (result.success) {
                    this.handleUploadSuccess(result);
                } else {
                    throw new Error(result.message || 'Upload failed');
                }

            } catch (error) {
                this.handleUploadError(error);
            }

            this.uploading = false;
        },

        async performUpload(formData) {
            return new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();

                // Upload progress tracking
                xhr.upload.addEventListener('progress', (e) => {
                    if (e.lengthComputable) {
                        const percentComplete = Math.round((e.loaded / e.total) * 100);
                        this.showGlobalProgress(true, percentComplete, `Uploading... ${percentComplete}%`);

                        // Update individual file progress
                        this.files.forEach(fileObj => {
                            if (fileObj.uploading) {
                                fileObj.progress = percentComplete;
                            }
                        });
                    }
                });

                xhr.addEventListener('load', () => {
                    try {
                        const result = JSON.parse(xhr.responseText);
                        resolve(result);
                    } catch (error) {
                        reject(new Error('Invalid response from server'));
                    }
                });

                xhr.addEventListener('error', () => {
                    reject(new Error('Network error occurred'));
                });

                xhr.open('POST', this.uploadEndpoint);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.send(formData);
            });
        },

        handleUploadSuccess(result) {
            this.files.forEach(fileObj => {
                fileObj.uploading = false;
                fileObj.uploaded = true;
                fileObj.progress = 100;
            });

            this.showNotification(result.message || 'Files uploaded successfully!', 'success');
            this.showGlobalProgress(false);

            // Add uploaded files to existing files if provided
            if (result.files) {
                if (this.singleMode && this.replaceMode) {
                    this.existingFiles = result.files;
                } else {
                    this.existingFiles.push(...result.files);
                }
            }

            // Clear files after successful upload
            setTimeout(() => {
                this.files = [];
                this.uploadOptions = {
                    category: '',
                    description: '',
                    isPublic: false
                };
            }, this.singleMode ? 500 : 2000);

            // Emit custom event
            this.$dispatch('files-uploaded', {
                files: result.files,
                component: this.componentId,
                result: result
            });
        },

        handleUploadError(error) {
            this.files.forEach(fileObj => {
                fileObj.uploading = false;
                fileObj.error = error.message;
            });

            this.showNotification(error.message, 'error');
            this.showGlobalProgress(false);

            this.$dispatch('upload-error', {
                error: error.message,
                component: this.componentId
            });
        },

        async deleteExistingFile(file, index) {
    if (!this.deleteEndpoint) {
        this.showNotification('Delete endpoint not configured', 'error');
        return;
    }

    if (!confirm(`Are you sure you want to delete "${file.name || file.file_name}"?`)) {
        return;
    }

    try {
        let deleteUrl = this.deleteEndpoint;
        let requestMethod = 'DELETE';
        let requestBody = {};

        // Handle different deletion scenarios
        if (file.temp_id || file.is_temp) {
            // This is a temporary file - send as JSON body
            requestBody = {
                temp_id: file.temp_id || file.id,
                id: file.temp_id || file.id,
                image_type: file.category || file.type
            };
            
            console.log('Deleting temp file with data:', requestBody); // Debug log
        } else if (file.id) {
            // This is a regular file with ID
            deleteUrl = this.deleteEndpoint.replace(':id', file.id);
            requestBody = {
                file_id: file.id,
                image_type: file.category || file.type
            };
        } else {
            // Fallback for files with category/type information only
            requestBody = {
                image_type: file.category || file.type || 'desktop'
            };
        }

        const response = await fetch(deleteUrl, {
            method: requestMethod,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            },
            body: JSON.stringify(requestBody)
        });

        const result = await response.json();

        if (result.success) {
            this.existingFiles.splice(index, 1);
            this.showNotification(result.message || 'File deleted successfully!', 'success');

            this.$dispatch('file-deleted', {
                file: file,
                component: this.componentId
            });
        } else {
            throw new Error(result.message || 'Delete failed');
        }
    } catch (error) {
        console.error('Delete error:', error);
        this.showNotification(error.message || 'Failed to delete file', 'error');
    }
},

        async previewFile(file) {
            if (!this.allowPreview || !this.previewEndpoint) {
                this.showNotification('Preview not available', 'warning');
                return;
            }

            this.previewModal.show = true;
            this.previewModal.title = file.name || file.file_name;
            this.previewModal.content = '<div class="flex items-center justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div></div>';

            try {
                const url = this.previewEndpoint.replace(':id', file.id);
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html,application/json'
                    }
                });

                if (response.ok) {
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        const result = await response.json();
                        this.previewModal.content = result.html || '<div class="text-center py-8 text-gray-500">No preview available</div>';
                    } else {
                        this.previewModal.content = await response.text();
                    }
                } else {
                    throw new Error('Preview failed to load');
                }
            } catch (error) {
                this.previewModal.content = `<div class="text-center py-8 text-red-600">Preview failed: ${error.message}</div>`;
            }
        },

        closePreview() {
            this.previewModal.show = false;
            this.previewModal.content = '';
        },

        // Utility Methods
        getFileIcon(mimeType) {
            if (!mimeType) return 'bg-gray-500';

            if (mimeType.startsWith('image/')) {
                return 'bg-green-500';
            } else if (mimeType === 'application/pdf') {
                return 'bg-red-500';
            } else if (mimeType.includes('word') || mimeType.includes('document')) {
                return 'bg-blue-500';
            } else if (mimeType.includes('excel') || mimeType.includes('sheet')) {
                return 'bg-emerald-500';
            } else if (mimeType.includes('powerpoint') || mimeType.includes('presentation')) {
                return 'bg-orange-500';
            } else if (mimeType.includes('zip') || mimeType.includes('archive')) {
                return 'bg-purple-500';
            } else {
                return 'bg-gray-500';
            }
        },

        getThemeClasses() {
            switch (this.theme) {
                case 'minimal':
                    return 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900';
                case 'modern':
                    return 'border-gradient-to-r from-blue-400 to-purple-500 bg-gradient-to-br from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20';
                default:
                    return 'bg-white dark:bg-gray-900';
            }
        },

        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },

        parseFileSize(size) {
            const units = {
                'B': 1,
                'KB': 1024,
                'MB': 1024 * 1024,
                'GB': 1024 * 1024 * 1024
            };
            const match = size.match(/^(\d+(?:\.\d+)?)\s*(B|KB|MB|GB)$/i);
            if (match) {
                return parseFloat(match[1]) * units[match[2].toUpperCase()];
            }
            return parseInt(size) || 0;
        },

        getTotalSize() {
            return this.files.reduce((total, file) => total + file.size, 0);
        },

        showGlobalProgress(show, percentage = 0, text = '') {
            this.globalProgress.show = show;
            this.globalProgress.percentage = percentage;
            this.globalProgress.text = text;
        },

        showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full shadow-lg rounded-lg p-4 ${this.getNotificationClasses(type)} transform transition-all duration-300 ease-in-out`;
            notification.innerHTML = `
                <div class="flex">
                    <div class="flex-shrink-0">
                        ${this.getNotificationIcon(type)}
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
        },

        getNotificationClasses(type) {
            const classes = {
                success: 'bg-green-50 border border-green-200 text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-400',
                error: 'bg-red-50 border border-red-200 text-red-800 dark:bg-red-900/20 dark:border-red-800 dark:text-red-400',
                warning: 'bg-yellow-50 border border-yellow-200 text-yellow-800 dark:bg-yellow-900/20 dark:border-yellow-800 dark:text-yellow-400',
                info: 'bg-blue-50 border border-blue-200 text-blue-800 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-400'
            };
            return classes[type] || classes.info;
        },

        getNotificationIcon(type) {
            const icons = {
                success: '<svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
                error: '<svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
                warning: '<svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>',
                info: '<svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>'
            };
            return icons[type] || icons.info;
        }
    }
}
</script>