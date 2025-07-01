<?php
// File: app/Http/Controllers/Admin/CertificationController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\FileUploadService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class CertificationController extends Controller
{
    protected $fileUploadService;

    /**
     * Create a new controller instance.
     *
     * @param FileUploadService $fileUploadService
     */
    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Display a listing of the certifications.
     */
    public function index(Request $request)
    {
        $certifications = Certification::when($request->filled('search'), function ($query) use ($request) {
            return $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('issuer', 'like', "%{$request->search}%");
            });
        })
            ->when($request->filled('status'), function ($query) use ($request) {
                return $query->where('is_active', $request->status === 'active');
            })
            ->when($request->filled('valid'), function ($query) use ($request) {
                if ($request->valid === 'valid') {
                    return $query->valid();
                } else {
                    return $query->where(function ($q) {
                        $q->whereNotNull('expiry_date')
                            ->where('expiry_date', '<', now());
                    });
                }
            })
            ->ordered()
            ->paginate(10);

        return view('admin.certifications.index', compact('certifications'));
    }

    /**
     * Show the form for creating a new certification.
     */
    public function create()
    {
        return view('admin.certifications.create');
    }

    /**
     * Store a newly created certification.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'issuer' => 'required|string|max:255',
            'description' => 'nullable|string',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:10240', // Traditional upload fallback
        ]);
        
        if (!isset($validated['sort_order'])) {
            $validated['sort_order'] = Certification::max('sort_order') + 1;
        }
        
        // Create certification
        $certification = Certification::create($validated);
        
        // Process temporary files from universal uploader (follows banner pattern)
        $this->processTempImagesFromSession($certification);
        
        // Handle traditional file upload as fallback
        if ($request->hasFile('image')) {
            $path = $this->processFileUpload($request->file('image'));
            $certification->update(['image' => $path]);
        }
        
        return redirect()->route('admin.certifications.index')
            ->with('success', 'Certification created successfully!');
    }

    /**
     * Display the specified certification.
     */
    public function show(Certification $certification)
    {
        return view('admin.certifications.show', compact('certification'));
    }

    /**
     * Show the form for editing the specified certification.
     */
    public function edit(Certification $certification)
    {
        return view('admin.certifications.edit', compact('certification'));
    }

    /**
     * Update the specified certification.
     */
    public function update(Request $request, Certification $certification)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'issuer' => 'required|string|max:255',
            'description' => 'nullable|string',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:10240', // Traditional upload fallback
        ]);
        
        // Update certification
        $certification->update($validated);
        
        // Process temporary files from universal uploader (follows banner pattern)
        $this->processTempImagesFromSession($certification);
        
        // Handle traditional file upload as fallback
        if ($request->hasFile('image')) {
            // Delete old file if exists
            if ($certification->image) {
                Storage::disk('public')->delete($certification->image);
            }
            
            $path = $this->processFileUpload($request->file('image'));
            $certification->update(['image' => $path]);
        }
        
        return redirect()->route('admin.certifications.index')
            ->with('success', 'Certification updated successfully!');
    }

    /**
     * Remove the specified certification.
     */
    public function destroy(Certification $certification)
    {
        // Delete file
        if ($certification->image) {
            Storage::disk('public')->delete($certification->image);
        }
        
        // Delete certification
        $certification->delete();
        
        return redirect()->route('admin.certifications.index')
            ->with('success', 'Certification deleted successfully!');
    }

    /**
     * Toggle active status
     */
    public function toggleActive(Certification $certification)
    {
        $certification->update([
            'is_active' => !$certification->is_active
        ]);

        return redirect()->back()
            ->with('success', 'Certification status updated!');
    }

    /**
     * Update sort order
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:certifications,id',
        ]);

        foreach ($request->order as $index => $id) {
            Certification::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }public function uploadTempImages(Request $request)
    {
        try {
            $request->validate([
                'certification_files' => 'required|file|mimes:jpeg,png,jpg,gif,pdf|max:10240',
            ]);

            $file = $request->file('certification_files');
            
            // Generate unique temp identifier (following banner pattern)
            $tempId = 'temp_certification_' . uniqid() . '_' . time();
            $tempFilename = $tempId . '.' . $file->getClientOriginalExtension();
            $tempPath = $file->storeAs('temp/certifications', $tempFilename, 'public');

            // Enhanced temp file metadata (following banner pattern)
            $tempFileData = [
                'temp_id' => $tempId,
                'temp_path' => $tempPath,
                'original_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_at' => now()->toISOString(),
                'session_id' => session()->getId()
            ];

            // Store in session (following banner pattern)
            $sessionKey = 'certification_temp_files_' . session()->getId();
            session()->put($sessionKey, $tempFileData);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully!',
                'files' => [[
                    'id' => $tempId,
                    'temp_id' => $tempId,
                    'name' => 'Certification File',
                    'file_name' => $file->getClientOriginalName(),
                    'url' => Storage::disk('public')->url($tempPath),
                    'size' => $this->formatFileSize($file->getSize()),
                    'type' => str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'pdf',
                    'is_temp' => true,
                    'created_at' => now()->format('M j, Y H:i')
                ]]
            ]);

        } catch (\Exception $e) {
            \Log::error('Certification temp file upload failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete temporary files (following banner pattern)
     */
    public function deleteTempImage(Request $request)
    {
        try {
            // Handle both JSON and form data (following banner pattern)
            $input = [];
            if ($request->isJson()) {
                $input = $request->json()->all();
            } else {
                $input = $request->all();
            }
            
            $tempId = $input['temp_id'] ?? $input['id'] ?? $request->input('temp_id') ?? $request->input('id') ?? $request->getContent();
            
            $sessionKey = 'certification_temp_files_' . session()->getId();
            $tempFileData = session()->get($sessionKey);

            if ($tempFileData && $tempFileData['temp_id'] === $tempId) {
                // Delete physical file
                if (Storage::disk('public')->exists($tempFileData['temp_path'])) {
                    Storage::disk('public')->delete($tempFileData['temp_path']);
                }

                // Remove from session
                session()->forget($sessionKey);

                return response()->json([
                    'success' => true,
                    'message' => 'File deleted successfully!'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Certification temp file deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get temporary files info (following banner pattern)
     */
    public function getTempFiles(Request $request)
    {
        try {
            $sessionKey = 'certification_temp_files_' . session()->getId();
            $tempFileData = session()->get($sessionKey);
            
            $files = [];
            if ($tempFileData && Storage::disk('public')->exists($tempFileData['temp_path'])) {
                $files[] = [
                    'id' => $tempFileData['temp_id'],
                    'temp_id' => $tempFileData['temp_id'],
                    'name' => 'Certification File',
                    'file_name' => $tempFileData['original_name'],
                    'url' => Storage::disk('public')->url($tempFileData['temp_path']),
                    'size' => $this->formatFileSize($tempFileData['file_size']),
                    'type' => str_starts_with($tempFileData['mime_type'], 'image/') ? 'image' : 'pdf',
                    'is_temp' => true,
                    'created_at' => \Carbon\Carbon::parse($tempFileData['uploaded_at'])->format('M j, Y H:i')
                ];
            }

            return response()->json([
                'success' => true,
                'files' => $files
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to get temp files: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get temporary files'
            ], 500);
        }
    }
    public function cleanupTempFiles(Request $request)
    {
        try {
            $sessionKey = 'certification_temp_files_' . session()->getId();
            $tempFileData = session()->get($sessionKey);

            if ($tempFileData && isset($tempFileData['temp_path'])) {
                if (Storage::disk('public')->exists($tempFileData['temp_path'])) {
                    Storage::disk('public')->delete($tempFileData['temp_path']);
                }
                session()->forget($sessionKey);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            \Log::error('Certification temp file cleanup failed: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Process temporary files from session (following banner pattern)
     */
    protected function processTempImagesFromSession(Certification $certification)
    {
        $sessionKey = 'certification_temp_files_' . session()->getId();
        $tempFileData = session()->get($sessionKey);

        if (empty($tempFileData)) {
            return;
        }

        try {
            if (!Storage::disk('public')->exists($tempFileData['temp_path'])) {
                \Log::warning('Temporary file not found during processing: ' . $tempFileData['temp_path']);
                return;
            }

            $this->moveTempFileToPermanent($tempFileData, $certification);
            
        } catch (\Exception $e) {
            \Log::error('Failed to process temp file: ' . $e->getMessage(), [
                'certification_id' => $certification->id,
                'temp_data' => $tempFileData
            ]);
        }

        // Clear processed temporary file from session
        session()->forget($sessionKey);
        
        // Also cleanup any physical temp files for this session
        $this->cleanupSessionTempFiles(session()->getId());
    }

    /**
     * Move temporary file to permanent location (following banner pattern)
     */
    protected function moveTempFileToPermanent(array $tempFileData, Certification $certification)
    {
        try {
            $tempPath = $tempFileData['temp_path'];
            
            if (!Storage::disk('public')->exists($tempPath)) {
                throw new \Exception('Temporary file not found: ' . $tempPath);
            }

            // Generate permanent filename
            $extension = pathinfo($tempFileData['original_name'], PATHINFO_EXTENSION);
            $filename = $this->generateFilename($tempFileData['original_name'], $certification->id, $extension);
            $directory = "certifications";
            $permanentPath = $directory . '/' . $filename;

            // Ensure directory exists
            Storage::disk('public')->makeDirectory($directory);

            // Move file from temp to permanent location
            if (Storage::disk('public')->move($tempPath, $permanentPath)) {
                // Update certification with new file path
                $certification->update(['image' => $permanentPath]);
                
                \Log::info('Temporary file moved to permanent location', [
                    'certification_id' => $certification->id,
                    'from' => $tempPath,
                    'to' => $permanentPath,
                    'original_name' => $tempFileData['original_name']
                ]);
                
                return $permanentPath;
            } else {
                throw new \Exception('Failed to move file from temp to permanent location');
            }

        } catch (\Exception $e) {
            \Log::error('Error moving temporary file: ' . $e->getMessage(), [
                'certification_id' => $certification->id,
                'temp_data' => $tempFileData
            ]);
            throw $e;
        }
    }

    /**
     * Generate filename (following banner pattern)
     */
    protected function generateFilename($originalName, int $certificationId, string $extension = null)
    {
        if (!$extension) {
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        }
        
        $timestamp = now()->format('YmdHis');
        $random = Str::random(6);
        
        return "certification_{$certificationId}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Cleanup session temp files (following banner pattern)
     */
    protected function cleanupSessionTempFiles(string $sessionId)
    {
        try {
            $tempDir = 'temp/certifications';
            
            if (!Storage::disk('public')->exists($tempDir)) {
                return;
            }

            $files = Storage::disk('public')->files($tempDir);
            $deletedCount = 0;

            foreach ($files as $file) {
                // Check if file belongs to this session or is old
                $filename = basename($file);
                if (str_contains($filename, $sessionId) || 
                    Storage::disk('public')->lastModified($file) < now()->subHours(1)->timestamp) {
                    
                    Storage::disk('public')->delete($file);
                    $deletedCount++;
                }
            }

            if ($deletedCount > 0) {
                \Log::info("Cleaned up {$deletedCount} session temporary files for session: {$sessionId}");
            }

        } catch (\Exception $e) {
            \Log::warning('Failed to cleanup session temp files: ' . $e->getMessage());
        }
    }

    /**
     * Process file upload for both images and PDFs (traditional upload)
     */
    protected function processFileUpload($file)
    {
        if (str_starts_with($file->getMimeType(), 'image/')) {
            return $this->fileUploadService->uploadImage($file, 'certifications', null, 800);
        } else {
            return $file->store('certifications', 'public');
        }
    }

    /**
     * Format file size helper (following banner pattern)
     */
    protected function formatFileSize($bytes)
    {
        if ($bytes === 0) return '0 Bytes';
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes, $k));
        return number_format($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }
}