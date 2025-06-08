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
}