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
use RahulHaque\LaravelFilepond\Http\Controllers\FilepondController;

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

        return view('admin.projects.files.create', compact('project'));
    }

    /**
     * Store newly uploaded files via regular form submission.
     */
    public function store(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $request->validate([
            'files' => 'required|array|min:1',
            'files.*' => 'required|file|max:10240', // Max 10MB per file
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_public' => 'boolean',
        ]);

        $uploadedFiles = [];
        $totalUploaded = 0;

        DB::transaction(function () use ($request, $project, &$uploadedFiles, &$totalUploaded) {
            foreach ($request->file('files') as $uploadedFile) {
                try {
                    // Upload file
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
                        'category' => $request->input('category', 'general'),
                        'description' => $request->input('description'),
                        'is_public' => $request->boolean('is_public', false),
                        'download_count' => 0,
                    ]);

                    $uploadedFiles[] = $projectFile;
                    $totalUploaded++;

                } catch (\Exception $e) {
                    \Log::error('File upload failed: ' . $e->getMessage(), [
                        'project_id' => $project->id,
                        'file_name' => $uploadedFile->getClientOriginalName(),
                    ]);
                    // Continue with other files
                }
            }
        });

        if ($totalUploaded > 0) {
            // Send notification
            if (class_exists('App\Facades\Notifications')) {
                Notifications::send('project.files_uploaded', [
                    'project' => $project,
                    'file_count' => $totalUploaded,
                    'files' => $uploadedFiles
                ]);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $totalUploaded === 1 
                        ? 'File uploaded successfully!' 
                        : "{$totalUploaded} files uploaded successfully!",
                    'files' => collect($uploadedFiles)->map(function($file) use ($project) {
                        return [
                            'id' => $file->id,
                            'name' => $file->file_name,
                            'size' => $file->formatted_file_size,
                            'type' => $file->file_type,
                            'download_url' => route('admin.projects.files.download', [$project, $file]),
                            'created_at' => $file->created_at->format('M j, Y H:i')
                        ];
                    })
                ]);
            }

            return redirect()->route('admin.projects.files.index', $project)
                ->with('success', $totalUploaded === 1 
                    ? 'File uploaded successfully!' 
                    : "{$totalUploaded} files uploaded successfully!");
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'No files were uploaded successfully.'
            ], 422);
        }

        return redirect()->back()
            ->with('error', 'No files were uploaded successfully.');
    }

    /**
     * FilePond upload endpoint - handles single file upload to temporary storage
     * This extends the FilePond controller from the package
     */
    public function upload(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        try {
            // Validate the file
            $request->validate([
                'file' => 'required|file|max:10240', // Max 10MB
            ]);

            $file = $request->file('file');
            
            // Additional validation
            $errors = $this->validateFile($file);
            if (!empty($errors)) {
                return response()->json(['error' => implode(', ', $errors)], 422);
            }

            // Use the FilePond controller from the package
            $filepondController = new FilepondController();
            
            // Store the file temporarily using FilePond
            $response = $filepondController->upload($request);
            
            // If successful, store additional project metadata in session
            if ($response->getStatusCode() === 200) {
                $serverId = $response->getContent();
                
                // Store project context in session
                session()->put("filepond_project_{$serverId}", [
                    'project_id' => $project->id,
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                    'uploaded_at' => now()->toISOString()
                ]);
            }

            return $response;

        } catch (\Exception $e) {
            \Log::error('FilePond upload failed: ' . $e->getMessage(), [
                'project_id' => $project->id,
                'file_name' => $request->file('file')?->getClientOriginalName()
            ]);
            
            return response()->json(['error' => 'Upload failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * FilePond delete endpoint - removes temporary file
     */
    public function delete(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        try {
            // Get the server ID from request body
            $serverId = $request->getContent();
            
            if (empty($serverId)) {
                return response()->json(['error' => 'Invalid server ID'], 400);
            }

            // Use the FilePond controller from the package
            $filepondController = new FilepondController();
            $response = $filepondController->delete($request);
            
            // Clean up our session data
            session()->forget("filepond_project_{$serverId}");
            
            return $response;

        } catch (\Exception $e) {
            \Log::error('FilePond delete failed: ' . $e->getMessage());
            return response()->json(['error' => 'Delete failed'], 500);
        }
    }

    /**
     * Process FilePond form submission
     */
    public function processFilePond(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $request->validate([
            'filepond_files' => 'nullable|array',
            'filepond_files.*' => 'string', // Server IDs from FilePond
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_public' => 'boolean',
        ]);

        $serverIds = $request->input('filepond_files', []);
        $processedFiles = [];

        if (empty($serverIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No files to process'
            ], 422);
        }

        DB::transaction(function () use ($serverIds, $request, $project, &$processedFiles) {
            foreach ($serverIds as $serverId) {
                try {
                    // Get file metadata from session
                    $fileData = session()->get("filepond_project_{$serverId}");
                    
                    if (!$fileData || $fileData['project_id'] != $project->id) {
                        \Log::warning('Invalid FilePond server ID or project mismatch', [
                            'server_id' => $serverId,
                            'expected_project' => $project->id
                        ]);
                        continue;
                    }

                    // Get the temporary file path from FilePond
                    $tempPath = config('filepond.path') . '/' . $serverId;
                    $tempDisk = config('filepond.disk', 'local');
                    
                    if (!Storage::disk($tempDisk)->exists($tempPath)) {
                        \Log::warning('FilePond temporary file not found', [
                            'server_id' => $serverId,
                            'temp_path' => $tempPath
                        ]);
                        continue;
                    }

                    // Generate permanent path
                    $filename = $this->generateSafeFilename($fileData['original_name']);
                    $permanentPath = 'projects/' . $project->id . '/files/' . $filename;

                    // Move from temp to permanent location
                    $publicDisk = 'public';

                    if ($tempDisk === $publicDisk) {
                        // Same disk, just move
                        if (Storage::disk($publicDisk)->move($tempPath, $permanentPath)) {
                            $this->createFileRecord($project, $fileData, $permanentPath, $request, $processedFiles);
                        }
                    } else {
                        // Different disks, copy then delete
                        $tempContent = Storage::disk($tempDisk)->get($tempPath);
                        if (Storage::disk($publicDisk)->put($permanentPath, $tempContent)) {
                            Storage::disk($tempDisk)->delete($tempPath);
                            $this->createFileRecord($project, $fileData, $permanentPath, $request, $processedFiles);
                        }
                    }

                    // Clean up session data
                    session()->forget("filepond_project_{$serverId}");

                } catch (\Exception $e) {
                    \Log::error('FilePond file processing failed: ' . $e->getMessage(), [
                        'server_id' => $serverId,
                        'project_id' => $project->id
                    ]);
                }
            }
        });

        $processedCount = count($processedFiles);

        if ($processedCount > 0) {
            // Send notification
            if (class_exists('App\Facades\Notifications')) {
                Notifications::send('project.files_uploaded', [
                    'project' => $project,
                    'file_count' => $processedCount,
                    'files' => $processedFiles
                ]);
            }

            $message = $processedCount === 1 
                ? 'File uploaded successfully!' 
                : "{$processedCount} files uploaded successfully!";

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'files' => collect($processedFiles)->map(function($file) use ($project) {
                        return [
                            'id' => $file->id,
                            'name' => $file->file_name,
                            'size' => $file->formatted_file_size,
                            'type' => $file->file_type,
                            'category' => $file->category,
                            'download_url' => route('admin.projects.files.download', [$project, $file]),
                            'created_at' => $file->created_at->format('M j, Y H:i')
                        ];
                    })
                ]);
            }

            return redirect()->route('admin.projects.files.index', $project)
                ->with('success', $message);
        }

        $message = 'No files were processed successfully.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message
            ], 422);
        }

        return redirect()->back()->with('error', $message);
    }

    /**
     * Download a project file.
     */
    public function download(Project $project, ProjectFile $file)
    {
        $this->authorize('view', $project);

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
        if (class_exists('App\Facades\Notifications')) {
            Notifications::send('project.file_deleted', [
                'project' => $project,
                'file_name' => $fileName
            ]);
        }

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
     * Cleanup old temporary files
     */
    public function cleanupTempFiles()
    {
        try {
            $tempPath = config('filepond.path');
            $tempDisk = config('filepond.disk', 'local');
            
            $deletedCount = 0;
            
            if (Storage::disk($tempDisk)->exists($tempPath)) {
                $files = Storage::disk($tempDisk)->files($tempPath);
                
                foreach ($files as $file) {
                    $fileAge = now()->diffInHours(Storage::disk($tempDisk)->lastModified($file) ?: 0);
                    
                    // Delete files older than 2 hours
                    if ($fileAge > 2) {
                        Storage::disk($tempDisk)->delete($file);
                        $deletedCount++;
                    }
                }
            }
            
            // Clean up old session data
            $sessionKeys = array_keys(session()->all());
            $filepondKeys = array_filter($sessionKeys, function($key) {
                return str_starts_with($key, 'filepond_project_');
            });
            
            $sessionCleanedCount = 0;
            foreach ($filepondKeys as $key) {
                $data = session()->get($key);
                if (isset($data['uploaded_at'])) {
                    $uploadedAt = \Carbon\Carbon::parse($data['uploaded_at']);
                    if ($uploadedAt->addHours(2)->isPast()) {
                        session()->forget($key);
                        $sessionCleanedCount++;
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => "Cleaned up {$deletedCount} temporary files and {$sessionCleanedCount} session entries",
                'deleted_files' => $deletedCount,
                'cleared_sessions' => $sessionCleanedCount
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Temp files cleanup failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Cleanup failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper method to create file record
     */
    private function createFileRecord(Project $project, array $fileData, string $permanentPath, Request $request, array &$processedFiles)
    {
        $projectFile = $project->files()->create([
            'file_path' => $permanentPath,
            'file_name' => $fileData['original_name'],
            'file_size' => $fileData['size'],
            'file_type' => $fileData['type'],
            'category' => $request->input('category', 'general'),
            'description' => $request->input('description'),
            'is_public' => $request->boolean('is_public', false),
            'download_count' => 0,
        ]);

        $processedFiles[] = $projectFile;
    }

    /**
     * Generate safe filename
     */
    private function generateSafeFilename(string $originalName): string
    {
        $pathInfo = pathinfo($originalName);
        $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';
        $basename = $pathInfo['filename'] ?? 'file';
        
        return uniqid() . '_' . Str::slug($basename) . $extension;
    }

    /**
     * Validate uploaded file.
     */
    private function validateFile(\Illuminate\Http\UploadedFile $file): array
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

        // Check filename length
        if (strlen($file->getClientOriginalName()) > 255) {
            $errors[] = 'Filename too long (max 255 characters)';
        }

        return $errors;
    }

    /**
     * Check if file type is allowed.
     */
    private function isFileTypeAllowed(string $mimeType): bool
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

            // Other
            'application/json',
            'application/xml',
            'text/xml',
        ];

        return in_array($mimeType, $allowedTypes) || str_starts_with($mimeType, 'image/');
    }
    public function show(Project $project)
{
    $this->authorize('view', $project);

    // Get all files with relationships
    $allFiles = $project->files()
        ->orderBy('created_at', 'desc')
        ->get();

    // Group files by category
    $filesByCategory = $allFiles->groupBy(function ($file) {
        return $file->category ?: 'general';
    });

    // Group files by type (using FileHelper)
    $filesByType = $allFiles->groupBy(function ($file) {
        return $file->file_category; // This uses the accessor from ProjectFile model
    });

    // Calculate statistics
    $totalFiles = $allFiles->count();
    $totalSize = $allFiles->sum('file_size');
    $totalDownloads = $allFiles->sum('download_count');

    // Get recent activity
    $recentFiles = $allFiles->take(5);

    return view('admin.projects.files.show', compact(
        'project',
        'allFiles',
        'filesByCategory',
        'filesByType',
        'totalFiles',
        'totalSize',
        'totalDownloads',
        'recentFiles'
    ));
}

/**
 * Preview a specific file.
 */
public function preview(Project $project, ProjectFile $file)
{
    $this->authorize('view', $project);

    if ($file->project_id !== $project->id) {
        abort(404);
    }

    if (!Storage::disk('public')->exists($file->file_path)) {
        return response()->json([
            'error' => 'File not found'
        ], 404);
    }

    $mimeType = $file->file_type;
    $filePath = Storage::disk('public')->path($file->file_path);

    try {
        // Handle different file types for preview
        if (str_starts_with($mimeType, 'image/')) {
            return $this->previewImage($file);
        }

        if ($mimeType === 'application/pdf') {
            return $this->previewPdf($file);
        }

        if (str_starts_with($mimeType, 'text/') || 
            in_array($mimeType, ['application/json', 'application/xml'])) {
            return $this->previewText($file, $filePath);
        }

        // For other file types, show file info
        return $this->previewFileInfo($file);

    } catch (\Exception $e) {
        \Log::error('File preview failed: ' . $e->getMessage(), [
            'project_id' => $project->id,
            'file_id' => $file->id,
        ]);

        return response()->json([
            'error' => 'Preview generation failed'
        ], 500);
    }
}

/**
 * Get file thumbnail for grid view.
 */
public function thumbnail(Project $project, ProjectFile $file)
{
    $this->authorize('view', $project);

    if ($file->project_id !== $project->id) {
        abort(404);
    }

    if (!Storage::disk('public')->exists($file->file_path)) {
        abort(404);
    }

    // For images, return resized thumbnail
    if (str_starts_with($file->file_type, 'image/')) {
        try {
            $imagePath = Storage::disk('public')->path($file->file_path);
            
            // Check if Intervention Image is available
            if (class_exists('\Intervention\Image\Laravel\Facades\Image')) {
                $image = \Intervention\Image\Laravel\Facades\Image::make($imagePath);
                $image->resize(150, 150, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                
                return $image->response();
            } else {
                // Fallback to original image
                return response()->file($imagePath);
            }
        } catch (\Exception $e) {
            // Return default file icon if image processing fails
            return $this->getDefaultThumbnail($file->file_category);
        }
    }

    // For non-images, return default icon
    return $this->getDefaultThumbnail($file->file_category);
}

/**
 * Bulk download selected files.
 */
public function bulkDownload(Request $request, Project $project)
{
    $this->authorize('view', $project);

    $request->validate([
        'file_ids' => 'required|array|min:1',
        'file_ids.*' => 'exists:project_files,id',
    ]);

    $files = $project->files()
        ->whereIn('id', $request->file_ids)
        ->get();

    if ($files->isEmpty()) {
        return redirect()->back()
            ->with('error', 'No valid files selected for download.');
    }

    try {
        // Create temporary zip file
        $zipFileName = 'project_files_' . $project->id . '_' . now()->format('Y-m-d_H-i-s') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);
        
        // Ensure temp directory exists
        if (!is_dir(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE) !== TRUE) {
            throw new \Exception('Cannot create zip file');
        }

        $addedFiles = 0;
        foreach ($files as $file) {
            if (Storage::disk('public')->exists($file->file_path)) {
                $filePath = Storage::disk('public')->path($file->file_path);
                $zip->addFile($filePath, $file->file_name);
                $addedFiles++;
                
                // Increment download count
                $file->increment('download_count');
            }
        }

        $zip->close();

        if ($addedFiles === 0) {
            unlink($zipPath);
            return redirect()->back()
                ->with('error', 'No files were available for download.');
        }

        // Log bulk download
        \Log::info('Bulk files downloaded', [
            'project_id' => $project->id,
            'file_count' => $addedFiles,
            'user_id' => auth()->id(),
        ]);

        // Return zip file and schedule for deletion
        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);

    } catch (\Exception $e) {
        \Log::error('Bulk download failed: ' . $e->getMessage(), [
            'project_id' => $project->id,
            'file_ids' => $request->file_ids,
        ]);

        return redirect()->back()
            ->with('error', 'Failed to create download archive.');
    }
}

/**
 * Bulk delete selected files.
 */
public function bulkDelete(Request $request, Project $project)
{
    $this->authorize('update', $project);

    $request->validate([
        'file_ids' => 'required|array|min:1',
        'file_ids.*' => 'exists:project_files,id',
    ]);

    $files = $project->files()
        ->whereIn('id', $request->file_ids)
        ->get();

    if ($files->isEmpty()) {
        return redirect()->back()
            ->with('error', 'No valid files selected for deletion.');
    }

    $deletedCount = 0;
    $fileNames = [];

    DB::transaction(function () use ($files, &$deletedCount, &$fileNames) {
        foreach ($files as $file) {
            try {
                // Delete physical file
                if (Storage::disk('public')->exists($file->file_path)) {
                    Storage::disk('public')->delete($file->file_path);
                }

                $fileNames[] = $file->file_name;
                $file->delete();
                $deletedCount++;

            } catch (\Exception $e) {
                \Log::error('Individual file deletion failed: ' . $e->getMessage(), [
                    'file_id' => $file->id,
                    'file_name' => $file->file_name,
                ]);
            }
        }
    });

    // Send notification
    if (class_exists('App\Facades\Notifications')) {
        Notifications::send('project.files_bulk_deleted', [
            'project' => $project,
            'file_count' => $deletedCount,
            'file_names' => $fileNames
        ]);
    }

    if ($deletedCount > 0) {
        return redirect()->back()
            ->with('success', "{$deletedCount} file(s) deleted successfully!");
    } else {
        return redirect()->back()
            ->with('error', 'No files were deleted.');
    }
}

/**
 * Get file statistics for dashboard.
 */
public function getStatistics(Project $project)
{
    $this->authorize('view', $project);

    $files = $project->files;
    
    $stats = [
        'total_files' => $files->count(),
        'total_size' => $files->sum('file_size'),
        'total_downloads' => $files->sum('download_count'),
        'files_by_category' => $files->groupBy('category')->map->count(),
        'files_by_type' => $files->groupBy('file_category')->map->count(),
        'recent_uploads' => $files->sortByDesc('created_at')->take(5)->values(),
        'most_downloaded' => $files->sortByDesc('download_count')->take(5)->values(),
        'largest_files' => $files->sortByDesc('file_size')->take(5)->values(),
    ];

    return response()->json($stats);
}

/**
 * Search files within project.
 */
public function search(Request $request, Project $project)
{
    $this->authorize('view', $project);

    $request->validate([
        'query' => 'required|string|min:1|max:255',
        'category' => 'nullable|string',
        'type' => 'nullable|string',
        'limit' => 'nullable|integer|min:1|max:100',
    ]);

    $query = $project->files();
    $searchTerm = $request->input('query');

    // Search in file names and descriptions
    $query->where(function ($q) use ($searchTerm) {
        $q->where('file_name', 'like', "%{$searchTerm}%")
          ->orWhere('description', 'like', "%{$searchTerm}%");
    });

    // Apply filters
    if ($request->filled('category')) {
        $query->where('category', $request->input('category'));
    }

    if ($request->filled('type')) {
        $type = $request->input('type');
        $query->where(function ($q) use ($type) {
            switch ($type) {
                case 'image':
                    $q->where('file_type', 'like', 'image/%');
                    break;
                case 'document':
                    $q->whereIn('file_type', [
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'text/plain',
                        'text/csv'
                    ]);
                    break;
                case 'archive':
                    $q->whereIn('file_type', [
                        'application/zip',
                        'application/x-rar-compressed',
                        'application/x-7z-compressed'
                    ]);
                    break;
            }
        });
    }

    $limit = $request->input('limit', 20);
    $files = $query->orderBy('created_at', 'desc')
                  ->limit($limit)
                  ->get();

    return response()->json([
        'success' => true,
        'files' => $files->map(function ($file) use ($project) {
            return [
                'id' => $file->id,
                'name' => $file->file_name,
                'size' => $file->formatted_file_size,
                'type' => $file->file_type_name,
                'category' => $file->category ?: 'General',
                'created_at' => $file->created_at->format('M j, Y H:i'),
                'download_url' => route('admin.projects.files.download', [$project, $file]),
                'preview_url' => $file->isPreviewable() ? 
                    route('admin.projects.files.preview', [$project, $file]) : null,
            ];
        }),
        'total' => $files->count(),
    ]);
}

/**
 * Preview image file.
 */
private function previewImage(ProjectFile $file)
{
    $imageUrl = Storage::url($file->file_path);
    
    $html = "
        <div class='text-center'>
            <img src='{$imageUrl}' alt='{$file->file_name}' class='max-w-full max-h-96 mx-auto rounded-lg shadow-md'>
            <div class='mt-4 text-sm text-gray-600 dark:text-gray-400'>
                <p><strong>Dimensions:</strong> <span id='image-dimensions'>Loading...</span></p>
                <p><strong>Size:</strong> {$file->formatted_file_size}</p>
            </div>
        </div>
        <script>
            const img = new Image();
            img.onload = function() {
                document.getElementById('image-dimensions').textContent = this.width + ' × ' + this.height + ' pixels';
            };
            img.src = '{$imageUrl}';
        </script>
    ";

    return response($html);
}

/**
 * Preview PDF file.
 */
private function previewPdf(ProjectFile $file)
{
    $pdfUrl = Storage::url($file->file_path);
    
    $html = "
        <div class='text-center'>
            <iframe src='{$pdfUrl}' class='w-full h-96 border rounded-lg' type='application/pdf'>
                <p>Your browser does not support PDF preview. 
                   <a href='{$pdfUrl}' target='_blank' class='text-blue-600 hover:text-blue-800'>Download the PDF</a>
                </p>
            </iframe>
            <div class='mt-4'>
                <a href='{$pdfUrl}' target='_blank' class='inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700'>
                    <svg class='w-4 h-4 mr-2' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                        <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14'/>
                    </svg>
                    Open in New Tab
                </a>
            </div>
        </div>
    ";

    return response($html);
}

/**
 * Preview text file.
 */
private function previewText(ProjectFile $file, string $filePath)
{
    try {
        $content = file_get_contents($filePath);
        
        // Limit content size for preview
        if (strlen($content) > 10000) {
            $content = substr($content, 0, 10000) . "\n\n... (truncated)";
        }

        $escapedContent = htmlspecialchars($content);
        
        $html = "
            <div class='text-left'>
                <div class='bg-gray-100 dark:bg-gray-900 rounded-lg p-4 max-h-96 overflow-auto'>
                    <pre class='text-sm font-mono whitespace-pre-wrap'>{$escapedContent}</pre>
                </div>
                <div class='mt-4 text-sm text-gray-600 dark:text-gray-400'>
                    <p><strong>Size:</strong> {$file->formatted_file_size}</p>
                    <p><strong>Type:</strong> {$file->file_type}</p>
                </div>
            </div>
        ";

        return response($html);
        
    } catch (\Exception $e) {
        return $this->previewFileInfo($file);
    }
}

/**
 * Preview file information.
 */
private function previewFileInfo(ProjectFile $file)
{
    $html = "
        <div class='text-center'>
            <div class='mx-auto w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mb-4'>
                <svg class='w-12 h-12 text-gray-400' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                    <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'/>
                </svg>
            </div>
            <h3 class='text-lg font-medium text-gray-900 dark:text-white mb-2'>{$file->file_name}</h3>
            <div class='space-y-2 text-sm text-gray-600 dark:text-gray-400'>
                <p><strong>Size:</strong> {$file->formatted_file_size}</p>
                <p><strong>Type:</strong> {$file->file_type_name}</p>
                <p><strong>Category:</strong> " . ucfirst($file->category ?: 'General') . "</p>
                <p><strong>Uploaded:</strong> {$file->created_at->format('M j, Y g:i A')}</p>
                <p><strong>Downloads:</strong> {$file->download_count}</p>
            </div>
            " . ($file->description ? "<div class='mt-4 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg'><p class='text-sm'>{$file->description}</p></div>" : "") . "
        </div>
    ";

    return response($html);
}

/**
 * Get default thumbnail for file types.
 */
private function getDefaultThumbnail(string $category)
{
    // Return a simple SVG icon based on file category
    $color = match($category) {
        'document' => '#3b82f6',
        'image' => '#10b981',
        'archive' => '#f59e0b',
        default => '#6b7280'
    };

    $svg = "
        <svg width='150' height='150' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
            <rect width='24' height='24' fill='#f8fafc'/>
            <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' stroke='{$color}' d='M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'/>
        </svg>
    ";

    return response($svg)->header('Content-Type', 'image/svg+xml');
}
}