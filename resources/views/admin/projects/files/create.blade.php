{{-- resources/views/admin/projects/files/create.blade.php --}}
<x-layouts.admin title="Upload Project Files">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Upload Files</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Upload files for {{ $project->title }}
            </p>
        </div>
        
        <div class="flex items-center space-x-3 mt-4 md:mt-0">
            <x-admin.button 
                href="{{ route('admin.projects.files.index', $project) }}" 
                color="light"
                size="sm"
            >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Files
            </x-admin.button>
            
            <x-admin.button 
                href="{{ route('admin.projects.show', $project) }}" 
                color="info"
                size="sm"
            >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
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
        'Upload' => '#'
    ]" class="mb-6" />

    <!-- Project Info -->
    <x-admin.card class="mb-6">
        <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-300">{{ $project->title }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2 text-sm">
                        <div>
                            <span class="text-blue-700 dark:text-blue-400">Status:</span>
                            <span class="font-medium text-blue-900 dark:text-blue-300">{{ $project->formatted_status }}</span>
                        </div>
                        @if($project->client)
                            <div>
                                <span class="text-blue-700 dark:text-blue-400">Client:</span>
                                <span class="font-medium text-blue-900 dark:text-blue-300">{{ $project->client->name }}</span>
                            </div>
                        @endif
                        <div>
                            <span class="text-blue-700 dark:text-blue-400">Current Files:</span>
                            <span class="font-medium text-blue-900 dark:text-blue-300">{{ $project->files->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-admin.card>

    <!-- Upload Instructions -->
    <x-admin.help-text type="info" class="mb-6">
        <x-slot name="title">File Upload Guidelines</x-slot>
        <ul class="list-disc list-inside space-y-1 text-sm">
            <li>Maximum file size: <strong>10MB</strong> per file</li>
            <li>Maximum files per upload: <strong>10 files</strong></li>
            <li>Supported formats: PDF, Word, Excel, PowerPoint, Images, Archives, CAD files</li>
            <li>Files will be organized by category for easy management</li>
            <li>Use descriptive names for better organization</li>
        </ul>
    </x-admin.help-text>

    <!-- FilePond Upload Interface -->
    <x-admin.card>
        <x-slot name="header">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                File Upload
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Drag and drop files or click to browse
            </p>
        </x-slot>

        <!-- FilePond Component -->
        <x-admin.filepond-uploader 
            :project="$project"
            name="files"
            :multiple="true"
            :maxFiles="10"
            maxFileSize="10MB"
            :acceptedFileTypes="[
                'image/*',
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'text/plain',
                'text/csv',
                'application/zip',
                'application/x-rar-compressed',
                'application/x-7z-compressed',
                'application/json',
                'application/xml'
            ]"
            :allowImagePreview="true"
            :allowImageCrop="false"
            :allowImageResize="true"
            :imageResizeTargetWidth="1200"
            :imageResizeTargetHeight="800"
            dropDescription="Drop files here or click to browse"
            category="general"
            :isPublic="false"
        />
    </x-admin.card>

    <!-- Current Project Files Summary -->
    @if($project->files->count() > 0)
        <x-admin.card class="mt-6">
            <x-slot name="header">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    Recent Files
                </h3>
                <a href="{{ route('admin.projects.files.index', $project) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    View All Files →
                </a>
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($project->files->take(6) as $file)
                    <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center">
                                @if(str_starts_with($file->file_type, 'image/'))
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                @endif
                            </div>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ $file->file_name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $file->formatted_file_size }} • {{ $file->created_at->format('M j') }}
                            </p>
                        </div>
                        <div class="ml-3 flex-shrink-0">
                            <a href="{{ route('admin.projects.files.download', [$project, $file]) }}" 
                               class="text-blue-600 hover:text-blue-800 text-xs">
                                Download
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($project->files->count() > 6)
                <div class="mt-4 text-center">
                    <a href="{{ route('admin.projects.files.index', $project) }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View {{ $project->files->count() - 6 }} more files →
                    </a>
                </div>
            @endif
        </x-admin.card>
    @endif

    <!-- File Categories Reference -->
    <x-admin.card class="mt-6">
        <x-slot name="header">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                File Categories Reference
            </h3>
        </x-slot>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 text-sm">
            <div>
                <h4 class="font-medium text-gray-900 dark:text-white">Documents</h4>
                <p class="text-gray-600 dark:text-gray-400">General documents, reports, specifications</p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900 dark:text-white">Images</h4>
                <p class="text-gray-600 dark:text-gray-400">Photos, screenshots, diagrams</p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900 dark:text-white">Plans</h4>
                <p class="text-gray-600 dark:text-gray-400">Technical drawings, blueprints</p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900 dark:text-white">Contracts</h4>
                <p class="text-gray-600 dark:text-gray-400">Legal documents, agreements</p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900 dark:text-white">Certificates</h4>
                <p class="text-gray-600 dark:text-gray-400">Quality certificates, compliance docs</p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900 dark:text-white">Presentations</h4>
                <p class="text-gray-600 dark:text-gray-400">PowerPoint, proposals</p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900 dark:text-white">Invoices</h4>
                <p class="text-gray-600 dark:text-gray-400">Financial documents, receipts</p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900 dark:text-white">Archives</h4>
                <p class="text-gray-600 dark:text-gray-400">ZIP files, compressed folders</p>
            </div>
        </div>
    </x-admin.card>
</x-layouts.admin>