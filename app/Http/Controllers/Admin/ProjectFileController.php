<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Services\FileUploadService;
use App\Services\FilePondService;
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
     * FilePond process endpoint - handles single file upload to temporary storage
     */
    public function process(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        try {
            $file = $request->file('file');
            
            if (!$file || !$file->isValid()) {
                return response('No valid file provided', 400);
            }

            // Validate file
            $errors = $this->validateFile($file);
            if (!empty($errors)) {
                return response(implode(', ', $errors), 422);
            }

            // Generate unique filename
            $filename = uniqid('filepond_') . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            
            // Store in temporary location
            $tempPath = 'temp/filepond/' . $filename;
            $file->storeAs('temp/filepond', $filename, 'public');

            // Store metadata in session for later processing
            session()->put("filepond_files.{$filename}", [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'type' => $file->getMimeType(),
                'temp_path' => $tempPath,
                'project_id' => $project->id,
                'uploaded_at' => now()->toISOString()
            ]);

            // Return the filename as the server ID
            return response($filename, 200)
                ->header('Content-Type', 'text/plain');

        } catch (\Exception $e) {
            \Log::error('FilePond process failed: ' . $e->getMessage(), [
                'project_id' => $project->id,
                'file_name' => $request->file('file')?->getClientOriginalName()
            ]);
            
            return response('Upload failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * FilePond revert endpoint - removes temporary file
     */
    public function revert(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        try {
            // FilePond sends the server ID in the request body for DELETE requests
            $serverId = $request->getContent();
            
            if (empty($serverId)) {
                return response('Invalid server ID', 400);
            }

            // Get file metadata from session
            $fileData = session()->get("filepond_files.{$serverId}");
            
            if ($fileData && isset($fileData['temp_path'])) {
                // Delete temporary file
                if (Storage::disk('public')->exists($fileData['temp_path'])) {
                    Storage::disk('public')->delete($fileData['temp_path']);
                }
                
                // Remove from session
                session()->forget("filepond_files.{$serverId}");
            }
            
            return response('', 200);

        } catch (\Exception $e) {
            \Log::error('FilePond revert failed: ' . $e->getMessage());
            return response('', 500);
        }
    }

    /**
     * Process FilePond submitted files - move from temp to permanent storage
     */
    public function processSubmission(Request $request, Project $project)
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
                    // Get file info from session
                    $fileData = session()->get("filepond_files.{$serverId}");
                    
                    if (!$fileData) {
                        \Log::warning('Invalid server ID in FilePond submission', ['server_id' => $serverId]);
                        continue;
                    }

                    // Check if temp file exists
                    if (!Storage::disk('public')->exists($fileData['temp_path'])) {
                        \Log::warning('Temp file not found for FilePond submission', [
                            'server_id' => $serverId,
                            'temp_path' => $fileData['temp_path']
                        ]);
                        continue;
                    }

                    // Generate permanent path
                    $permanentPath = 'projects/' . $project->id . '/files/' . 
                        uniqid() . '_' . Str::slug(pathinfo($fileData['original_name'], PATHINFO_FILENAME)) . 
                        '.' . pathinfo($fileData['original_name'], PATHINFO_EXTENSION);
                    
                    // Move from temp to permanent location
                    if (Storage::disk('public')->move($fileData['temp_path'], $permanentPath)) {
                        // Create database record
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
                        
                        // Clean up session
                        session()->forget("filepond_files.{$serverId}");
                    }

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
     * Cleanup old temporary FilePond files
     */
    public function cleanupTempFiles()
    {
        try {
            $tempPath = 'temp/filepond';
            
            if (Storage::disk('public')->exists($tempPath)) {
                $files = Storage::disk('public')->files($tempPath);
                $deletedCount = 0;
                
                foreach ($files as $file) {
                    $fileAge = now()->diffInHours(Storage::disk('public')->lastModified($file) ?: 0);
                    
                    // Delete files older than 2 hours
                    if ($fileAge > 2) {
                        Storage::disk('public')->delete($file);
                        $deletedCount++;
                    }
                }
                
                return response()->json([
                    'success' => true,
                    'message' => "Cleaned up {$deletedCount} temporary files",
                    'deleted_count' => $deletedCount
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'No temporary files to clean up',
                'deleted_count' => 0
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