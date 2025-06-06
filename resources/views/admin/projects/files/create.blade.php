<!-- Upload Progress (Hidden Initially) -->
<div id="upload-progress" class="hidden">
    <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-2">
        <div id="progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
    </div>
    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mt-2">
        <span id="progress-text">Uploading files...</span>
        <span id="progress-percentage">0%</span>
    </div>
</div>

<!-- Debug Section (Remove in production) -->
<div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
    <h4 class="text-sm font-medium text-yellow-800 mb-2">Debug Information</h4>
    <div class="space-y-1 text-xs text-yellow-700">
        <button type="button" onclick="testFileInput()" class="px-2 py-1 bg-yellow-200 rounded text-yellow-800 hover:bg-yellow-300">
            Test File Input
        </button>
        <button type="button" onclick="testDropZone()" class="px-2 py-1 bg-yellow-200 rounded text-yellow-800 hover:bg-yellow-300 ml-2">
            Test Drop Zone
        </button>
        <button type="button" onclick="showDebugInfo()" class="px-2 py-1 bg-yellow-200 rounded text-yellow-800 hover:bg-yellow-300 ml-2">
            Show Debug Info
        </button>
    </div>
    <div id="debug-output" class="mt-2 text-xs text-yellow-700"></div>
</div>

<!-- Debug Section (Remove in production) -->
<div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
    <h4 class="text-sm font-medium text-yellow-800 mb-2">Debug Information</h4>
    <div class="space-y-1 text-xs text-yellow-700">
        <button type="button" onclick="testFileInput()" class="px-2 py-1 bg-yellow-200 rounded text-yellow-800 hover:bg-yellow-300">
            Test File Input
        </button>
        <button type="button" onclick="testDropZone()" class="px-2 py-1 bg-yellow-200 rounded text-yellow-800 hover:bg-yellow-300 ml-2">
            Test Drop Zone
        </button>
        <button type="button" onclick="showDebugInfo()" class="px-2 py-1 bg-yellow-200 rounded text-yellow-800 hover:bg-yellow-300 ml-2">
            Show Debug Info
        </button>
    </div>
    <div id="debug-output" class="mt-2 text-xs text-yellow-700{{-- resources/views/admin/projects/files/create.blade.php --}}
<x-layouts.admin title="Upload Project Files">
<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
<div>
<h1 class="text-2xl font-bold text-gray-900 dark:text-white">Upload Project Files</h1>
<p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
Add files and documents to "{{ $project->title }}"
</p>
</div>

<div class="flex items-center space-x-3 mt-4 md:mt-0">
<x-admin.button 
href="{{ route('admin.projects.files.index', $project) }}" 
color="light"
>
<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
</svg>
Back to Files
</x-admin.button>

<x-admin.button 
href="{{ route('admin.projects.show', $project) }}" 
color="info"
>
<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
</svg>
View Project
</x-admin.button>
</div>
</div>

<!-- Breadcrumb -->
<x-admin.breadcrumb :items="[
'Projects' => route('admin.projects.index'),
$project->title => route('admin.projects.show', $project),
'Files' => route('admin.projects.files.index', $project),
'Upload Files' => '#'
]" class="mb-6" />

<!-- Upload Guidelines -->
<x-admin.help-text type="info" class="mb-6" dismissible>
<x-slot name="title">File Upload Guidelines</x-slot>
<div class="space-y-2">
<p>• Maximum file size: <strong>10MB per file</strong></p>
<p>• Supported formats: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, PNG, GIF, ZIP, RAR</p>
<p>• You can upload multiple files at once</p>
<p>• Use meaningful file names and add descriptions for better organization</p>
</div>
</x-admin.help-text>

<!-- Upload Form -->
<x-admin.card>
<x-slot name="header">
<div class="flex items-center space-x-2">
<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
</svg>
<span class="font-medium">File Upload</span>
</div>
</x-slot>

<form action="{{ route('admin.projects.files.store', $project) }}" method="POST" enctype="multipart/form-data" id="upload-form">
@csrf

<div class="space-y-6">
<!-- File Selection Area -->
<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
        Select Files <span class="text-red-500">*</span>
    </label>
    
    <!-- Drag & Drop Zone -->
    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors duration-200 cursor-pointer" id="drop-zone">
        <div class="space-y-1 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <div class="flex text-sm text-gray-600 dark:text-gray-400">
                <label for="files" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                    <span>Upload files</span>
                    <input id="files" 
                           name="files[]" 
                           type="file" 
                           class="sr-only" 
                           multiple 
                           required 
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.svg,.zip,.rar,.7z,.txt,.csv">
                </label>
                <p class="pl-1">or drag and drop</p>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                PNG, JPG, PDF, DOC, XLS, ZIP up to 10MB each
            </p>
        </div>
    </div>
    
    @error('files')
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
    @error('files.*')
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>

<!-- Selected Files Preview -->
<div id="selected-files" class="hidden">
    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Selected Files:</h3>
    <div id="file-list" class="space-y-2 max-h-64 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-md p-3">
        <!-- Files will be listed here via JavaScript -->
    </div>
</div>

<!-- File Details -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div>
        <x-admin.select
            label="Category"
            name="category"
            :options="$categories"
            helper="Organize files by category for easier management"
        />
    </div>
    
    <div class="flex items-end">
        <x-admin.checkbox
            label="Make files public"
            name="is_public"
            helper="Allow clients to download these files directly"
        />
    </div>
</div>

<!-- Description -->
<x-admin.textarea
    label="Description (Optional)"
    name="description"
    rows="3"
    placeholder="Add a description for these files..."
    helper="This description will be applied to all uploaded files"
/>

<!-- Upload Progress (Hidden Initially) -->
<div id="upload-progress" class="hidden">
    <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-2">
        <div id="progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
    </div>
    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mt-2">
        <span id="progress-text">Uploading files...</span>
        <span id="progress-percentage">0%</span>
    </div>
</div>
</div>

<!-- Form Actions -->
<div class="flex items-center justify-between pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
<x-admin.button 
    href="{{ route('admin.projects.files.index', $project) }}" 
    color="light"
>
    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
    </svg>
    Cancel
</x-admin.button>

<div class="flex space-x-3">
    <x-admin.button 
        type="button" 
        color="light"
        onclick="clearFiles()"
        id="clear-button"
        style="display: none;"
    >
        Clear Files
    </x-admin.button>
    
    <x-admin.button 
        type="submit" 
        color="primary"
        id="upload-button"
        disabled
    >
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
        </svg>
        <span id="upload-text">Upload Files</span>
    </x-admin.button>
</div>
</div>
</form>
</x-admin.card>

<!-- Upload Tips -->
<x-admin.card class="mt-6">
<x-slot name="header">
<div class="flex items-center space-x-2">
<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
</svg>
<span class="font-medium">Upload Tips & Best Practices</span>
</div>
</x-slot>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div>
<h4 class="font-medium text-gray-900 dark:text-white mb-3">File Organization</h4>
<ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
    <li class="flex items-start">
        <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        Use descriptive file names (e.g., "Project_Blueprint_v2.pdf")
    </li>
    <li class="flex items-start">
        <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        Choose appropriate categories for better organization
    </li>
    <li class="flex items-start">
        <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        Add descriptions to provide context
    </li>
    <li class="flex items-start">
        <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        Group related files together
    </li>
</ul>
</div>

<div>
<h4 class="font-medium text-gray-900 dark:text-white mb-3">Security & Access</h4>
<ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
    <li class="flex items-start">
        <svg class="w-4 h-4 text-blue-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
        Private files are only accessible to admin users
    </li>
    <li class="flex items-start">
        <svg class="w-4 h-4 text-blue-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Public files can be downloaded by project clients
    </li>
    <li class="flex items-start">
        <svg class="w-4 h-4 text-blue-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        All uploads are scanned for security
    </li>
    <li class="flex items-start">
        <svg class="w-4 h-4 text-blue-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Download tracking is automatically enabled
    </li>
</ul>
</div>
</div>
</x-admin.card>

<!-- Recent Project Files (if any) -->
@if($project->files->count() > 0)
<x-admin.card class="mt-6">
<x-slot name="header">
<div class="flex items-center justify-between">
    <div class="flex items-center space-x-2">
        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="font-medium">Recent Files in This Project</span>
    </div>
    <x-admin.button 
        href="{{ route('admin.projects.files.index', $project) }}" 
        color="light" 
        size="sm"
    >
        View All Files
    </x-admin.button>
</div>
</x-slot>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
@foreach($project->files->take(6) as $file)
    <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
        <div class="flex-shrink-0">
            @if(str_starts_with($file->file_type, 'image/'))
                <div class="w-8 h-8 bg-green-100 dark:bg-green-800/30 rounded flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            @else
                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-800/30 rounded flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            @endif
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                {{ $file->file_name }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ $file->formatted_file_size }} • {{ $file->created_at->diffForHumans() }}
            </p>
        </div>
    </div>
@endforeach
</div>
</x-admin.card>
@endif
</x-layouts.admin>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
console.log('File upload form initialized');

// Get all required elements
const fileInput = document.getElementById('files');
const dropZone = document.getElementById('drop-zone');
const selectedFiles = document.getElementById('selected-files');
const fileList = document.getElementById('file-list');
const uploadButton = document.getElementById('upload-button');
const clearButton = document.getElementById('clear-button');
const uploadForm = document.getElementById('upload-form');
const uploadProgress = document.getElementById('upload-progress');
const progressBar = document.getElementById('progress-bar');
const progressText = document.getElementById('progress-text');
const progressPercentage = document.getElementById('progress-percentage');

// Check if all elements exist
const requiredElements = {
fileInput: 'files',
dropZone: 'drop-zone',
selectedFiles: 'selected-files',
fileList: 'file-list',
uploadButton: 'upload-button',
clearButton: 'clear-button',
uploadForm: 'upload-form',
uploadProgress: 'upload-progress',
progressBar: 'progress-bar',
progressText: 'progress-text',
progressPercentage: 'progress-percentage'
};

for (const [element, id] of Object.entries(requiredElements)) {
if (!document.getElementById(id)) {
console.error(`Required element not found: ${id}`);
return;
}
}

console.log('All required elements found');

// Check if CSRF token exists
const csrfToken = document.querySelector('meta[name="csrf-token"]');
if (!csrfToken) {
console.error('CSRF token not found. Make sure <meta name="csrf-token"> is in the page head.');
alert('Security token missing. Please refresh the page.');
return;
} else {
console.log('CSRF token found:', csrfToken.content.substring(0, 10) + '...');
}

// Test file input functionality
console.log('File input accept attribute:', fileInput.accept);
console.log('File input multiple:', fileInput.multiple);

// File input change handler
fileInput.addEventListener('change', function(e) {
console.log('File input changed. Files selected:', e.target.files.length);
if (e.target.files.length > 0) {
for (let i = 0; i < e.target.files.length; i++) {
const file = e.target.files[i];
console.log(`File ${i}: ${file.name} (${file.size} bytes, ${file.type})`);
}
}
handleFiles();
});

// Also handle click on drop zone to trigger file input
dropZone.addEventListener('click', function(e) {
// Only trigger if not clicking on the actual file input label
if (!e.target.closest('label[for="files"]')) {
console.log('Drop zone clicked, triggering file input');
e.preventDefault();
fileInput.click();
}
});

// Drag and drop handlers
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
dropZone.addEventListener(eventName, preventDefaults, false);
document.body.addEventListener(eventName, preventDefaults, false);
});

['dragenter', 'dragover'].forEach(eventName => {
dropZone.addEventListener(eventName, function(e) {
console.log('Drag event:', eventName);
highlight(e);
}, false);
});

['dragleave', 'drop'].forEach(eventName => {
dropZone.addEventListener(eventName, function(e) {
console.log('Drag event:', eventName);
unhighlight(e);
}, false);
});

dropZone.addEventListener('drop', function(e) {
console.log('Files dropped:', e.dataTransfer.files.length);
handleDrop(e);
}, false);

function preventDefaults(e) {
e.preventDefault();
e.stopPropagation();
}

function highlight(e) {
dropZone.classList.add('border-blue-400', 'bg-blue-50', 'dark:bg-blue-900/20');
}

function unhighlight(e) {
dropZone.classList.remove('border-blue-400', 'bg-blue-50', 'dark:bg-blue-900/20');
}

function handleDrop(e) {
const dt = e.dataTransfer;
const files = dt.files;

fileInput.files = files;
handleFiles();
}

function handleFiles() {
const files = Array.from(fileInput.files);

if (files.length > 0) {
displaySelectedFiles(files);
uploadButton.disabled = false;
clearButton.style.display = 'inline-flex';
} else {
selectedFiles.classList.add('hidden');
uploadButton.disabled = true;
clearButton.style.display = 'none';
}
}

function displaySelectedFiles(files) {
console.log('Displaying selected files:', files.length);
selectedFiles.classList.remove('hidden');

fileList.innerHTML = files.map((file, index) => {
const fileSize = formatFileSize(file.size);
const fileIcon = getFileIcon(file.type);
const isValid = validateFile(file);

console.log(`File ${index}: ${file.name}, Size: ${file.size}, Type: ${file.type}, Valid: ${isValid}`);

return `
<div class="flex items-center justify-between p-3 ${isValid ? 'bg-white dark:bg-gray-700' : 'bg-red-50 dark:bg-red-900/20'} border ${isValid ? 'border-gray-200 dark:border-gray-600' : 'border-red-200 dark:border-red-800'} rounded-lg">
    <div class="flex items-center space-x-3">
        <div class="flex-shrink-0">
            <div class="w-8 h-8 ${isValid ? 'bg-gray-100 dark:bg-gray-600' : 'bg-red-100 dark:bg-red-800'} rounded flex items-center justify-center">
                ${fileIcon}
            </div>
        </div>
        <div>
            <p class="text-sm font-medium ${isValid ? 'text-gray-900 dark:text-white' : 'text-red-800 dark:text-red-400'}">${file.name}</p>
            <p class="text-xs ${isValid ? 'text-gray-500 dark:text-gray-400' : 'text-red-600 dark:text-red-500'}">${fileSize} • ${file.type || 'Unknown type'}</p>
            ${!isValid ? `<p class="text-xs text-red-600 dark:text-red-400 mt-1">${getFileError(file)}</p>` : ''}
        </div>
    </div>
    <button type="button" onclick="removeFile(${index})" class="text-gray-400 hover:text-red-500 transition-colors" title="Remove file">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>
`;
}).join('');

// Update upload button state based on valid files
const validFiles = files.filter(file => validateFile(file));
if (validFiles.length > 0) {
uploadButton.disabled = false;
uploadButton.classList.remove('opacity-50', 'cursor-not-allowed');
} else {
uploadButton.disabled = true;
uploadButton.classList.add('opacity-50', 'cursor-not-allowed');
}
}

function validateFile(file) {
const maxSize = 10 * 1024 * 1024; // 10MB
const allowedTypes = [
'application/pdf',
'application/msword',
'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
'application/vnd.ms-excel',
'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
'application/vnd.ms-powerpoint',
'application/vnd.openxmlformats-officedocument.presentationml.presentation',
'image/jpeg',
'image/png',
'image/gif',
'image/svg+xml',
'image/webp',
'text/plain',
'text/csv',
'application/zip',
'application/x-rar-compressed',
'application/x-7z-compressed'
];

// Check file size
if (file.size > maxSize) {
console.warn(`File ${file.name} exceeds size limit: ${file.size} bytes`);
return false;
}

// Check file type
if (!allowedTypes.includes(file.type) && !file.type.startsWith('image/')) {
console.warn(`File ${file.name} has unsupported type: ${file.type}`);
return false;
}

// Check filename length
if (file.name.length > 255) {
console.warn(`File ${file.name} has name too long`);
return false;
}

return true;
}

function getFileError(file) {
const maxSize = 10 * 1024 * 1024;
if (file.size > maxSize) {
return 'File size exceeds 10MB limit';
}
return 'File type not supported';
}

function getFileIcon(fileType) {
if (fileType.startsWith('image/')) {
return '<svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>';
} else if (fileType === 'application/pdf') {
return '<svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>';
} else {
return '<svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>';
}
}

function formatFileSize(bytes) {
if (bytes === 0) return '0 Bytes';
const k = 1024;
const sizes = ['Bytes', 'KB', 'MB', 'GB'];
const i = Math.floor(Math.log(bytes) / Math.log(k));
return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Form submission with progress
uploadForm.addEventListener('submit', function(e) {
e.preventDefault();
console.log('Form submission started');

const formData = new FormData(this);
const files = Array.from(fileInput.files);

// Debug form data
console.log('Form action:', this.action);
console.log('Selected files:', files.length);
console.log('Form data entries:');
for (let [key, value] of formData.entries()) {
if (key === 'files[]') {
console.log(`${key}:`, value.name, value.size, value.type);
} else {
console.log(`${key}:`, value);
}
}

// Validate all files before upload
const invalidFiles = files.filter(file => !validateFile(file));
if (invalidFiles.length > 0) {
console.error('Invalid files found:', invalidFiles.map(f => f.name));
alert('Please remove invalid files before uploading:\n' + invalidFiles.map(f => `- ${f.name} (${getFileError(f)})`).join('\n'));
return;
}

if (files.length === 0) {
console.error('No files selected');
alert('Please select at least one file to upload.');
return;
}

// Check CSRF token
const csrfToken = document.querySelector('meta[name="csrf-token"]');
if (!csrfToken) {
console.error('CSRF token missing');
alert('Security token missing. Please refresh the page and try again.');
return;
}

// Show progress
uploadProgress.classList.remove('hidden');
uploadButton.disabled = true;
document.getElementById('upload-text').textContent = 'Uploading...';
progressBar.style.width = '10%';
progressPercentage.textContent = '10%';
progressText.textContent = `Preparing to upload ${files.length} file(s)...`;

console.log('Starting upload to:', this.action);

// Use fetch API instead of XMLHttpRequest for better Laravel compatibility
fetch(this.action, {
method: 'POST',
body: formData,
headers: {
'X-CSRF-TOKEN': csrfToken.content,
'X-Requested-With': 'XMLHttpRequest',
'Accept': 'application/json'
}
})
.then(async response => {
console.log('Response status:', response.status);
console.log('Response headers:', Object.fromEntries(response.headers.entries()));

let data = {};
const contentType = response.headers.get('content-type');

if (contentType && contentType.includes('application/json')) {
try {
    data = await response.json();
    console.log('Response data:', data);
} catch (e) {
    console.error('Failed to parse JSON response:', e);
}
} else {
const text = await response.text();
console.log('Response text (first 500 chars):', text.substring(0, 500));

// Try to extract Laravel error message
if (text.includes('<!DOCTYPE html>')) {
    const titleMatch = text.match(/<title>(.*?)<\/title>/);
    if (titleMatch) {
        throw new Error('Server error: ' + titleMatch[1]);
    }
}

data.message = 'Unexpected response format';
}

if (response.ok) {
// Success
progressBar.style.width = '100%';
progressPercentage.textContent = '100%';
progressText.textContent = 'Upload completed successfully!';
progressBar.classList.add('bg-green-600');
progressBar.classList.remove('bg-blue-600');

console.log('Upload successful');

// Redirect after success
setTimeout(() => {
    window.location.href = '{{ route('admin.projects.files.index', $project) }}';
}, 1500);
} else {
// Error response
let errorMessage = data.message || `HTTP ${response.status}: ${response.statusText}`;

// Handle Laravel validation errors
if (data.errors) {
    const errorList = Object.values(data.errors).flat();
    errorMessage = errorList.join(', ');
}

throw new Error(errorMessage);
}
})
.catch(error => {
console.error('Upload error:', error);
progressText.textContent = 'Upload failed: ' + error.message;
progressBar.classList.add('bg-red-600');
progressBar.classList.remove('bg-blue-600');
uploadButton.disabled = false;
document.getElementById('upload-text').textContent = 'Upload Files';

// Show detailed error to user
alert('Upload failed: ' + error.message);

// Hide progress after error
setTimeout(() => {
uploadProgress.classList.add('hidden');
progressBar.style.width = '0%';
progressBar.classList.remove('bg-red-600');
progressBar.classList.add('bg-blue-600');
}, 5000);
});
});

// Clear files function
window.clearFiles = function() {
console.log('Clearing all files');
fileInput.value = '';
selectedFiles.classList.add('hidden');
uploadButton.disabled = true;
uploadButton.classList.add('opacity-50', 'cursor-not-allowed');
clearButton.style.display = 'none';
uploadProgress.classList.add('hidden');
progressBar.style.width = '0%';
progressBar.classList.remove('bg-green-600', 'bg-red-600');
progressBar.classList.add('bg-blue-600');
document.getElementById('upload-text').textContent = 'Upload Files';
console.log('Files cleared successfully');
};

// Remove individual file function
window.removeFile = function(index) {
console.log('Removing file at index:', index);

try {
const dt = new DataTransfer();
const files = Array.from(fileInput.files);

console.log('Current files count:', files.length);

// Add all files except the one to remove
files.forEach((file, i) => {
if (i !== index) {
    dt.items.add(file);
}
});

fileInput.files = dt.files;
console.log('New files count:', fileInput.files.length);

// Trigger change event
const event = new Event('change', { bubbles: true });
fileInput.dispatchEvent(event);

} catch (error) {
console.error('Error removing file:', error);
// Fallback: clear all files if individual removal fails
clearFiles();
}
};

// Test functions on page load
console.log('clearFiles function available:', typeof window.clearFiles === 'function');
console.log('removeFile function available:', typeof window.removeFile === 'function');

// Debug functions
window.testFileInput = function() {
const output = document.getElementById('debug-output');
const fileInput = document.getElementById('files');

if (!fileInput) {
output.innerHTML = 'ERROR: File input not found!';
return;
}

output.innerHTML = `
<strong>File Input Test:</strong><br>
- Element exists: ✓<br>
- ID: ${fileInput.id}<br>
- Name: ${fileInput.name}<br>
- Type: ${fileInput.type}<br>
- Multiple: ${fileInput.multiple}<br>
- Accept: ${fileInput.accept}<br>
- Required: ${fileInput.required}<br>
- Files count: ${fileInput.files.length}<br>
- Click to test: 
`;

const testButton = document.createElement('button');
testButton.type = 'button';
testButton.className = 'px-2 py-1 bg-blue-200 rounded text-blue-800 hover:bg-blue-300 ml-1';
testButton.textContent = 'Open File Dialog';
testButton.onclick = () => {
console.log('Testing file input click');
fileInput.click();
};
output.appendChild(testButton);
};

window.testDropZone = function() {
const output = document.getElementById('debug-output');
const dropZone = document.getElementById('drop-zone');

if (!dropZone) {
output.innerHTML = 'ERROR: Drop zone not found!';
return;
}

output.innerHTML = `
<strong>Drop Zone Test:</strong><br>
- Element exists: ✓<br>
- ID: ${dropZone.id}<br>
- Classes: ${dropZone.className}<br>
- Event listeners: Check console for drag events<br>
`;

// Test drag events
console.log('Testing drop zone drag events...');
const testFile = new File(['test content'], 'test.txt', { type: 'text/plain' });
const dataTransfer = new DataTransfer();
dataTransfer.items.add(testFile);

const dragEvent = new DragEvent('dragenter', {
bubbles: true,
dataTransfer: dataTransfer
});

dropZone.dispatchEvent(dragEvent);

setTimeout(() => {
const leaveEvent = new DragEvent('dragleave', {
bubbles: true,
dataTransfer: dataTransfer
});
dropZone.dispatchEvent(leaveEvent);
}, 1000);
};

window.showDebugInfo = function() {
const output = document.getElementById('debug-output');
const fileInput = document.getElementById('files');
const uploadButton = document.getElementById('upload-button');
const form = document.getElementById('upload-form');
const csrfToken = document.querySelector('meta[name="csrf-token"]');

output.innerHTML = `
<strong>Complete Debug Information:</strong><br>
<strong>Elements:</strong><br>
- File input: ${fileInput ? '✓' : '✗'}<br>
- Upload button: ${uploadButton ? '✓' : '✗'}<br>
- Form: ${form ? '✓' : '✗'}<br>
- CSRF token: ${csrfToken ? '✓' : '✗'}<br>
<strong>Form Details:</strong><br>
- Form action: ${form ? form.action : 'N/A'}<br>
- Form method: ${form ? form.method : 'N/A'}<br>
- Form enctype: ${form ? form.enctype : 'N/A'}<br>
<strong>Button State:</strong><br>
- Upload button disabled: ${uploadButton ? uploadButton.disabled : 'N/A'}<br>
- Upload button text: ${uploadButton ? uploadButton.querySelector('#upload-text')?.textContent : 'N/A'}<br>
<strong>Files:</strong><br>
- Selected files: ${fileInput ? fileInput.files.length : 'N/A'}<br>
`;

if (fileInput && fileInput.files.length > 0) {
output.innerHTML += '<strong>File List:</strong><br>';
for (let i = 0; i < fileInput.files.length; i++) {
const file = fileInput.files[i];
output.innerHTML += `- ${file.name} (${file.size} bytes, ${file.type})<br>`;
}
}
};
});
</script>
@endpush