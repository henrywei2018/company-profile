<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Services\FileUploadService;
use App\Facades\Notifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProjectFileController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Display a listing of project files.
     */
    public function index(Project $project)
    {
        $this->authorize('view', $project);

        $files = $project->files()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Group files by category
        $filesByCategory = $project->files()
            ->orderBy('category')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('category');

        $totalSize = $project->files()->sum('file_size');
        $totalDownloads = $project->files()->sum('download_count');

        return view('admin.projects.files.index', compact(
            'project',
            'files',
            'filesByCategory',
            'totalSize',
            'totalDownloads'
        ));
    }

    /**
     * Show the form for creating a new file upload.
     */
    public function create(Project $project)
    {
        $this->authorize('update', $project);

        $categories = $this->getFileCategories();

        return view('admin.projects.files.create', compact('project', 'categories'));
    }

    /**
     * Store newly uploaded files.
     */
    public function store(Request $request, Project $project)
{
    $this->authorize('update', $project);

    try {
        $validated = $request->validate([
            'files' => 'required|array|min:1|max:10',
            'files.*' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,svg,zip,rar,7z,txt,csv',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_public' => 'boolean',
        ], [
            'files.required' => 'Please select at least one file to upload.',
            'files.*.max' => 'Each file must not exceed 10MB.',
            'files.*.mimes' => 'File type not supported. Please use: PDF, DOC, XLS, PPT, images, or archives.',
        ]);

        $uploadedFiles = [];
        $totalUploaded = 0;
        $errors = [];

        DB::transaction(function () use ($request, $project, &$uploadedFiles, &$totalUploaded, &$errors, $validated) {
            foreach ($request->file('files') as $index => $uploadedFile) {
                try {
                    // Additional server-side validation
                    if ($uploadedFile->getSize() > 10 * 1024 * 1024) {
                        $errors[] = "File {$uploadedFile->getClientOriginalName()} exceeds 10MB limit";
                        continue;
                    }

                    // Upload file using the service
                    $path = $this->fileUploadService->uploadFile(
                        $uploadedFile,
                        'projects/' . $project->id . '/files'
                    );

                    // Create file record
                    $projectFile = $project->files()->create([
                        'file_path' => $path,
                        'file_name' => $uploadedFile->getClientOriginalName(),
                        'file_size' => $uploadedFile->getSize(),
                        'file_type' => $uploadedFile->getMimeType(),
                        'category' => $validated['category'] ?? 'general',
                        'description' => $validated['description'] ?? null,
                        'is_public' => $request->boolean('is_public', false),
                        'download_count' => 0,
                    ]);

                    $uploadedFiles[] = $projectFile;
                    $totalUploaded++;

                    \Log::info('File uploaded successfully', [
                        'project_id' => $project->id,
                        'file_name' => $projectFile->file_name,
                        'file_size' => $projectFile->file_size,
                        'user_id' => auth()->id()
                    ]);

                } catch (\Exception $e) {
                    \Log::error('File upload failed: ' . $e->getMessage(), [
                        'project_id' => $project->id,
                        'file_name' => $uploadedFile->getClientOriginalName(),
                        'error' => $e->getMessage(),
                        'user_id' => auth()->id()
                    ]);
                    
                    $errors[] = "Failed to upload {$uploadedFile->getClientOriginalName()}: " . $e->getMessage();
                }
            }
        });

        // Prepare response
        if ($totalUploaded > 0) {
            // Send notification
            try {
                Notifications::send('project.files_uploaded', [
                    'project' => $project,
                    'file_count' => $totalUploaded,
                    'files' => $uploadedFiles
                ]);
            } catch (\Exception $e) {
                \Log::warning('Failed to send file upload notification: ' . $e->getMessage());
            }

            $message = $totalUploaded === 1
                ? 'File uploaded successfully!'
                : "{$totalUploaded} files uploaded successfully!";

            if (!empty($errors)) {
                $message .= ' However, ' . count($errors) . ' files failed to upload.';
            }

            // Handle AJAX vs regular request
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'uploaded_count' => $totalUploaded,
                    'errors' => $errors,
                    'redirect_url' => route('admin.projects.files.index', $project)
                ]);
            }

            $redirectUrl = route('admin.projects.files.index', $project);
            return redirect($redirectUrl)->with('success', $message);
        }

        // No files uploaded successfully
        $errorMessage = 'No files were uploaded successfully.';
        if (!empty($errors)) {
            $errorMessage .= ' Errors: ' . implode(', ', $errors);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'errors' => $errors
            ], 422);
        }

        return redirect()->back()
            ->withErrors(['files' => $errorMessage])
            ->withInput();

    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('File upload validation failed', [
            'project_id' => $project->id,
            'errors' => $e->errors(),
            'user_id' => auth()->id()
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        throw $e;

    } catch (\Exception $e) {
        \Log::error('Unexpected error during file upload', [
            'project_id' => $project->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'user_id' => auth()->id()
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }

        return redirect()->back()
            ->with('error', 'An unexpected error occurred. Please try again.')
            ->withInput();
    }
}

    /**
     * Download a project file.
     */
    public function download(Project $project, ProjectFile $file)
    {
        $this->authorize('viewFiles', $project);

        if ($file->project_id !== $project->id) {
            abort(404);
        }

        if (!Storage::disk('public')->exists($file->file_path)) {
            return redirect()->back()
                ->with('error', 'File not found.');
        }

        // Increment download count
        $file->increment('download_count');

        // Log download
        \Log::info('File downloaded', [
            'project_id' => $project->id,
            'file_id' => $file->id,
            'file_name' => $file->file_name,
            'user_id' => auth()->id(),
        ]);

        return Storage::disk('public')->download(
            $file->file_path,
            $file->file_name
        );
    }

    /**
     * Remove a project file.
     */
    public function destroy(Project $project, ProjectFile $file)
    {
        $this->authorize('update', $project);

        if ($file->project_id !== $project->id) {
            abort(404);
        }

        // Delete physical file
        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        $fileName = $file->file_name;
        $file->delete();

        // Send notification
        Notifications::send('project.file_deleted', [
            'project' => $project,
            'file_name' => $fileName
        ]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully!'
            ]);
        }

        return redirect()->back()
            ->with('success', 'File deleted successfully!');
    }

    /**
     * Bulk upload files.
     */
    public function bulkUpload(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $request->validate([
            'files' => 'required|array|min:1|max:20', // Max 20 files at once
            'files.*' => 'required|file|max:10240',
            'category' => 'nullable|string|max:255',
            'is_public' => 'boolean',
        ]);

        $uploadedFiles = [];
        $failedFiles = [];
        $totalSize = 0;
        $deletedCount = 0;

        DB::transaction(function () use ($request, $project, &$uploadedFiles, &$failedFiles, &$totalSize) {
            foreach ($request->file('files') as $uploadedFile) {
                try {
                    $path = $this->fileUploadService->uploadFile(
                        $uploadedFile,
                        'projects/' . $project->id . '/files'
                    );

                    $projectFile = $project->files()->create([
                        'file_path' => $path,
                        'file_name' => $uploadedFile->getClientOriginalName(),
                        'file_size' => $uploadedFile->getSize(),
                        'file_type' => $uploadedFile->getMimeType(),
                        'category' => $request->input('category', 'general'),
                        'description' => 'Bulk uploaded file',
                        'is_public' => $request->boolean('is_public', false),
                        'download_count' => 0,
                    ]);

                    $uploadedFiles[] = $projectFile;
                    $totalSize += $uploadedFile->getSize();

                } catch (\Exception $e) {
                    $failedFiles[] = $uploadedFile->getClientOriginalName();
                    \Log::error('Bulk upload failed for file: ' . $e->getMessage());
                }
            }
        });

        $successCount = count($uploadedFiles);
        $failureCount = count($failedFiles);

        if ($successCount > 0) {
            Notifications::send('project.bulk_files_uploaded', [
                'project' => $project,
                'success_count' => $successCount,
                'total_size' => $totalSize
            ]);
        }

        $message = "Bulk upload completed: {$successCount} files uploaded successfully";
        if ($failureCount > 0) {
            $message .= ", {$failureCount} files failed";
        }

        return response()->json([
            'success' => true,
            'message' => "{$deletedCount} files deleted successfully!",
            'deleted_count' => $deletedCount
        ]);
    }

    /**
     * Update file details.
     */
    public function update(Request $request, Project $project, ProjectFile $file)
    {
        $this->authorize('update', $project);

        if ($file->project_id !== $project->id) {
            abort(404);
        }

        $validated = $request->validate([
            'file_name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_public' => 'boolean',
        ]);

        $file->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'File updated successfully!',
            'file' => $file->fresh()
        ]);
    }

    /**
     * Get file statistics for a project.
     */
    public function statistics(Project $project)
    {
        $this->authorize('view', $project);

        $stats = [
            'total_files' => $project->files()->count(),
            'total_size' => $project->files()->sum('file_size'),
            'total_downloads' => $project->files()->sum('download_count'),
            'public_files' => $project->files()->where('is_public', true)->count(),
            'private_files' => $project->files()->where('is_public', false)->count(),
            'by_category' => $project->files()
                ->selectRaw('category, COUNT(*) as count, SUM(file_size) as size')
                ->groupBy('category')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [
                        $item->category ?: 'uncategorized' => [
                            'count' => $item->count,
                            'size' => $item->size,
                            'formatted_size' => $this->formatFileSize($item->size)
                        ]
                    ];
                }),
            'by_type' => $project->files()
                ->selectRaw('file_type, COUNT(*) as count')
                ->groupBy('file_type')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->file_type => $item->count];
                }),
            'recent_uploads' => $project->files()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($file) {
                    return [
                        'id' => $file->id,
                        'name' => $file->file_name,
                        'size' => $file->formatted_file_size,
                        'uploaded_at' => $file->created_at->diffForHumans(),
                        'downloads' => $file->download_count
                    ];
                })
        ];

        return response()->json($stats);
    }

    /**
     * Search files within a project.
     */
    public function search(Request $request, Project $project)
    {
        $this->authorize('view', $project);

        $request->validate([
            'query' => 'required|string|min:2|max:255',
            'category' => 'nullable|string',
            'file_type' => 'nullable|string',
        ]);

        $query = $project->files()
            ->where(function ($q) use ($request) {
                $searchTerm = $request->query;
                $q->where('category', $request->input('category'))
                    ->orwhere('file_type', 'like', $request->input('file_type') . '%');
            });

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('file_type')) {
            $query->where('file_type', 'like', $request->file_type . '%');
        }

        $files = $query->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'files' => $files->map(function ($file)use ($project) {
                return [
                    'id' => $file->id,
                    'name' => $file->file_name,
                    'category' => $file->category,
                    'size' => $file->formatted_file_size,
                    'type' => $file->file_type,
                    'description' => $file->description,
                    'is_public' => $file->is_public,
                    'downloads' => $file->download_count,
                    'uploaded_at' => $file->created_at->format('M j, Y'),
                    'download_url' => route('admin.projects.files.download', [$project, $file])
                ];
            }),
            'total' => $files->count()
        ]);
    }

    /**
     * Export file list as CSV.
     */
    public function export(Project $project)
    {
        $this->authorize('view', $project);

        $files = $project->files()->orderBy('created_at', 'desc')->get();

        $csvData = [];
        $csvData[] = [
            'File Name',
            'Category',
            'Size',
            'Type',
            'Downloads',
            'Public',
            'Uploaded At',
            'Description'
        ];

        foreach ($files as $file) {
            $csvData[] = [
                $file->file_name,
                $file->category ?: 'Uncategorized',
                $file->formatted_file_size,
                $file->file_type,
                $file->download_count,
                $file->is_public ? 'Yes' : 'No',
                $file->created_at->format('Y-m-d H:i:s'),
                $file->description ?: ''
            ];
        }

        $filename = "project-{$project->id}-files-" . now()->format('Y-m-d-H-i-s') . '.csv';

        $handle = fopen('php://temp', 'r+');
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * Get available file categories.
     */
    protected function getFileCategories(): array
    {
        return [
            'documents' => 'Documents',
            'images' => 'Images',
            'plans' => 'Plans & Drawings',
            'contracts' => 'Contracts',
            'reports' => 'Reports',
            'certificates' => 'Certificates',
            'presentations' => 'Presentations',
            'specifications' => 'Specifications',
            'invoices' => 'Invoices',
            'correspondence' => 'Correspondence',
            'photos' => 'Project Photos',
            'videos' => 'Videos',
            'archives' => 'Archives',
            'other' => 'Other',
        ];
    }

    /**
     * Format file size in human readable format.
     */
    protected function formatFileSize(int $size): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, 2) . ' ' . $units[$i];
    }

    /**
     * Check if file type is allowed.
     */
    protected function isFileTypeAllowed(string $mimeType): bool
    {
        $allowedTypes = [
            // Documents
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'text/csv',

            // Images
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/svg+xml',
            'image/webp',

            // Archives
            'application/zip',
            'application/x-rar-compressed',
            'application/x-7z-compressed',

            // CAD Files
            'application/dwg',
            'application/dxf',

            // Other
            'application/json',
            'application/xml',
        ];

        return in_array($mimeType, $allowedTypes);
    }

    /**
     * Validate file before upload.
     */
    protected function validateFile(\Illuminate\Http\UploadedFile $file): array
    {
        $errors = [];

        // Check file size (max 10MB)
        if ($file->getSize() > 10 * 1024 * 1024) {
            $errors[] = 'File size exceeds 10MB limit';
        }

        // Check file type
        if (!$this->isFileTypeAllowed($file->getMimeType())) {
            $errors[] = 'File type not allowed: ' . $file->getMimeType();
        }

        // Check filename
        if (strlen($file->getClientOriginalName()) > 255) {
            $errors[] = 'Filename too long (max 255 characters)';
        }

        return $errors;
    }

    /**
     * Preview file content (for supported types).
     */
    public function preview(Project $project, ProjectFile $file)
    {
        $this->authorize('viewFiles', $project);

        if ($file->project_id !== $project->id) {
            abort(404);
        }

        if (!Storage::disk('public')->exists($file->file_path)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        // Only allow preview for certain file types
        $previewableTypes = [
            'application/pdf',
            'text/plain',
            'text/csv',
            'application/json',
            'text/html',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/svg+xml',
            'image/webp'
        ];

        if (!in_array($file->file_type, $previewableTypes)) {
            return redirect()->back()->with('error', 'File preview not supported for this file type.');
        }

        $filePath = Storage::disk('public')->path($file->file_path);

        if (str_starts_with($file->file_type, 'image/')) {
            return response()->file($filePath, [
                'Content-Type' => $file->file_type,
                'Content-Disposition' => 'inline; filename="' . $file->file_name . '"'
            ]);
        }

        if ($file->file_type === 'application/pdf') {
            return response()->file($filePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $file->file_name . '"'
            ]);
        }

        // For text files, show content in browser
        if (str_starts_with($file->file_type, 'text/')) {
            $content = Storage::disk('public')->get($file->file_path);
            return response($content, 200, [
                'Content-Type' => $file->file_type . '; charset=utf-8',
                'Content-Disposition' => 'inline; filename="' . $file->file_name . '"'
            ]);
        }

        return redirect()->route('admin.projects.files.download', [$project, $file]);
    }

    /**
     * Get file thumbnail/icon.
     */
    public function thumbnail(Project $project, ProjectFile $file)
    {
        $this->authorize('viewFiles', $project);

        if ($file->project_id !== $project->id) {
            abort(404);
        }

        // For images, return resized thumbnail
        if (str_starts_with($file->file_type, 'image/') && Storage::disk('public')->exists($file->file_path)) {
            try {
                $thumbnailPath = 'projects/' . $project->id . '/thumbnails/' . basename($file->file_path);

                if (!Storage::disk('public')->exists($thumbnailPath)) {
                    // Generate thumbnail using intervention/image or similar
                    $this->generateThumbnail($file->file_path, $thumbnailPath);
                }

                if (Storage::disk('public')->exists($thumbnailPath)) {
                    return response()->file(Storage::disk('public')->path($thumbnailPath));
                }
            } catch (\Exception $e) {
                \Log::error('Thumbnail generation failed: ' . $e->getMessage());
            }
        }

        // Return default icon based on file type
        return $this->getFileTypeIcon($file->file_type);
    }

    /**
     * Generate thumbnail for image files.
     */
    protected function generateThumbnail(string $originalPath, string $thumbnailPath): void
    {
        // This would require intervention/image package
        // For now, just copy the original file
        $thumbnailDir = dirname($thumbnailPath);
        if (!Storage::disk('public')->exists($thumbnailDir)) {
            Storage::disk('public')->makeDirectory($thumbnailDir);
        }

        Storage::disk('public')->copy($originalPath, $thumbnailPath);
    }

    /**
     * Get file type icon.
     */
    protected function getFileTypeIcon(string $mimeType)
    {
        // Return SVG icon based on file type
        $iconPath = public_path('images/file-icons/');

        $icon = match ($mimeType) {
            'application/pdf' => 'pdf.svg',
            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'word.svg',
            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'excel.svg',
            'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'powerpoint.svg',
            'application/zip', 'application/x-rar-compressed', 'application/x-7z-compressed' => 'archive.svg',
            'text/plain' => 'text.svg',
            'text/csv' => 'csv.svg',
            'application/json' => 'json.svg',
            default => str_starts_with($mimeType, 'image/') ? 'image.svg' : 'file.svg'
        };

        $iconFullPath = $iconPath . $icon;
        if (file_exists($iconFullPath)) {
            return response()->file($iconFullPath);
        }

        // Return default SVG if specific icon not found
        $defaultSvg = '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" fill="#64748b"/>
            <polyline points="14,2 14,8 20,8" fill="#475569"/>
        </svg>';

        return response($defaultSvg, 200, ['Content-Type' => 'image/svg+xml']);
    }

    /**
     * Organize files into folders/categories.
     */
    public function organize(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $request->validate([
            'files' => 'required|array',
            'files.*.id' => 'required|exists:project_files,id',
            'files.*.category' => 'required|string|max:255',
        ]);

        $updatedCount = 0;

        DB::transaction(function () use ($request, $project, &$updatedCount) {
            foreach ($request->files as $fileData) {
                $file = ProjectFile::where('id', $fileData['id'])
                    ->where('project_id', $project->id)
                    ->first();

                if ($file) {
                    $file->update(['category' => $fileData['category']]);
                    $updatedCount++;
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => "{$updatedCount} files organized successfully!",
            'updated_count' => $updatedCount
        ]);
    }

    /**
     * Get disk usage for project files.
     */
    public function diskUsage(Project $project)
    {
        $this->authorize('view', $project);

        $totalSize = $project->files()->sum('file_size');
        $fileCount = $project->files()->count();

        // Get size by category
        $sizeByCategory = $project->files()
            ->selectRaw('COALESCE(category, "uncategorized") as category, SUM(file_size) as total_size, COUNT(*) as file_count')
            ->groupBy('category')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category,
                    'size' => $item->total_size,
                    'formatted_size' => $this->formatFileSize($item->total_size),
                    'file_count' => $item->file_count,
                    'percentage' => $item->total_size > 0 ? round(($item->total_size / $this->getTotalProjectSize()) * 100, 1) : 0
                ];
            });

        return response()->json([
            'success' => true,
            'total_size' => $totalSize,
            'formatted_total_size' => $this->formatFileSize($totalSize),
            'file_count' => $fileCount,
            'size_by_category' => $sizeByCategory,
            'storage_limit' => $this->getStorageLimit(),
            'usage_percentage' => $this->getStorageUsagePercentage($totalSize)
        ]);
    }

    /**
     * Clean up orphaned files.
     */
    public function cleanup(Project $project)
    {
        $this->authorize('update', $project);

        $orphanedFiles = [];
        $deletedCount = 0;

        // Find files in storage that don't have database records
        $projectPath = 'projects/' . $project->id . '/files';
        if (Storage::disk('public')->exists($projectPath)) {
            $storageFiles = Storage::disk('public')->files($projectPath);
            $dbFilePaths = $project->files()->pluck('file_path')->toArray();

            foreach ($storageFiles as $storageFile) {
                if (!in_array($storageFile, $dbFilePaths)) {
                    $orphanedFiles[] = $storageFile;
                    Storage::disk('public')->delete($storageFile);
                    $deletedCount++;
                }
            }
        }

        // Find database records without actual files
        $missingFiles = [];
        foreach ($project->files as $file) {
            if (!Storage::disk('public')->exists($file->file_path)) {
                $missingFiles[] = $file->file_name;
                $file->delete();
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Cleanup completed. Removed {$deletedCount} orphaned files and " . count($missingFiles) . " missing database records.",
            'orphaned_files_deleted' => $deletedCount,
            'missing_records_removed' => count($missingFiles),
            'orphaned_files' => $orphanedFiles,
            'missing_files' => $missingFiles
        ]);
    }

    /**
     * Get total project size including all files.
     */
    protected function getTotalProjectSize(): int
    {
        return ProjectFile::sum('file_size');
    }

    /**
     * Get storage limit (could be from config or settings).
     */
    protected function getStorageLimit(): int
    {
        return config('filesystems.project_storage_limit', 1024 * 1024 * 1024); // 1GB default
    }

    /**
     * Get storage usage percentage.
     */
    protected function getStorageUsagePercentage(int $usedSize): float
    {
        $limit = $this->getStorageLimit();
        return $limit > 0 ? round(($usedSize / $limit) * 100, 1) : 0;
    }
    /**
     * Bulk delete files.
     */
    public function bulkDelete(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $request->validate([
            'file_ids' => 'required|array|min:1',
            'file_ids.*' => 'exists:project_files,id',
        ]);

        $files = ProjectFile::whereIn('id', $request->file_ids)
            ->where('project_id', $project->id)
            ->get();

        $deletedCount = 0;
        $deletedFiles = [];

        DB::transaction(function () use ($files, &$deletedCount, &$deletedFiles) {
            foreach ($files as $file) {
                try {
                    if (Storage::disk('public')->exists($file->file_path)) {
                        Storage::disk('public')->delete($file->file_path);
                    }

                    $deletedFiles[] = $file->file_name;
                    $file->delete();
                    $deletedCount++;

                } catch (\Exception $e) {
                    \Log::error('Bulk delete failed for file: ' . $e->getMessage());
                }
            }
        });

        if ($deletedCount > 0) {
            Notifications::send('project.bulk_files_deleted', [
                'project' => $project,
                'deleted_count' => $deletedCount,
                'file_names' => $deletedFiles
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "{$deletedCount} files deleted successfully!",
            'deleted_count' => $deletedCount,
            'deleted_files' => $deletedFiles
        ]);
    }
}