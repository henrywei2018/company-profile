<?php
// File: app/Traits/HandlesFileUploads.php

namespace App\Traits;

use App\Services\UniversalFileUploadService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

trait HandlesFileUploads
{
    protected UniversalFileUploadService $fileUploadService;

    /**
     * Initialize the file upload service
     */
    protected function initializeFileUploadService(): void
    {
        if (!isset($this->fileUploadService)) {
            $this->fileUploadService = app(UniversalFileUploadService::class);
        }
    }

    /**
     * Handle file uploads for any model
     */
    protected function handleFileUploads(
        Request $request,
        $model,
        string $relationshipMethod,
        array $config = []
    ): JsonResponse {
        $this->initializeFileUploadService();

        try {
            // Default configuration
            $defaultConfig = [
                'disk' => 'public',
                'max_file_size' => 10 * 1024 * 1024, // 10MB
                'max_files' => 10,
                'directory_prefix' => strtolower(class_basename($model)) . 's',
                'generate_thumbnails' => false,
                'image_resize' => ['enabled' => false]
            ];

            $config = array_merge($defaultConfig, $config);
            $directory = $config['directory_prefix'] . '/' . $model->id . '/files';

            $uploadedFiles = [];

            DB::transaction(function () use ($request, $model, $relationshipMethod, $directory, $config, &$uploadedFiles) {
                $fileData = $this->fileUploadService->uploadFiles(
                    $request,
                    $directory,
                    $config,
                    // Before save callback
                    function ($fileData, $file, $request) use ($model) {
                        return array_merge($fileData, [
                            'category' => $request->input('category', 'general'),
                            'description' => $request->input('description'),
                            'is_public' => $request->boolean('is_public', false),
                            'uploaded_by' => auth()->id(),
                        ]);
                    },
                    // After save callback
                    function ($fileData, $file, $request) use ($model, $relationshipMethod, &$uploadedFiles) {
                        // Create the file record using the relationship
                        $fileRecord = $model->{$relationshipMethod}()->create([
                            'file_name' => $fileData['original_name'],
                            'file_path' => $fileData['path'],
                            'file_size' => $fileData['size'],
                            'file_type' => $fileData['mime_type'],
                            'category' => $fileData['category'],
                            'description' => $fileData['description'],
                            'is_public' => $fileData['is_public'],
                            'thumbnail_path' => $fileData['thumbnail_path'],
                            'download_count' => 0,
                            'uploaded_by' => $fileData['uploaded_by'],
                        ]);

                        $uploadedFiles[] = $this->formatFileResponse($fileRecord, $model);
                    }
                );
            });

            $count = count($uploadedFiles);
            $message = $count === 1 ? 'File uploaded successfully!' : "{$count} files uploaded successfully!";

            return response()->json([
                'success' => true,
                'message' => $message,
                'files' => $uploadedFiles,
                'count' => $count
            ]);

        } catch (\Exception $e) {
            \Log::error('File upload failed: ' . $e->getMessage(), [
                'model' => get_class($model),
                'model_id' => $model->id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Handle file deletion for any model
     */
    protected function handleFileDelete(
        $model,
        $fileModel,
        string $relationshipMethod
    ): JsonResponse {
        $this->initializeFileUploadService();

        try {
            // Verify the file belongs to the model
            if ($fileModel->{$this->getModelForeignKey($model)} !== $model->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }

            DB::transaction(function () use ($fileModel) {
                // Delete physical file
                $this->fileUploadService->deleteFile(
                    $fileModel->file_path,
                    'public',
                    $fileModel->thumbnail_path
                );

                // Delete database record
                $fileModel->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully!'
            ]);

        } catch (\Exception $e) {
            \Log::error('File deletion failed: ' . $e->getMessage(), [
                'model' => get_class($model),
                'model_id' => $model->id,
                'file_id' => $fileModel->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete file'
            ], 500);
        }
    }

    /**
     * Handle file preview for any model
     */
    protected function handleFilePreview(
        $model,
        $fileModel
    ): string {
        $this->initializeFileUploadService();

        try {
            // Verify the file belongs to the model
            if ($fileModel->{$this->getModelForeignKey($model)} !== $model->id) {
                return '<div class="text-center py-8 text-red-600">File not found</div>';
            }

            return $this->fileUploadService->generatePreview(
                $fileModel->file_path,
                $fileModel->file_type,
                'public'
            );

        } catch (\Exception $e) {
            \Log::error('File preview failed: ' . $e->getMessage(), [
                'model' => get_class($model),
                'model_id' => $model->id,
                'file_id' => $fileModel->id
            ]);

            return '<div class="text-center py-8 text-red-600">Preview generation failed</div>';
        }
    }

    /**
     * Handle file download for any model
     */
    protected function handleFileDownload(
        $model,
        $fileModel
    ) {
        try {
            // Verify the file belongs to the model
            if ($fileModel->{$this->getModelForeignKey($model)} !== $model->id) {
                abort(404);
            }

            if (!\Storage::disk('public')->exists($fileModel->file_path)) {
                return redirect()->back()->with('error', 'File not found.');
            }

            // Increment download count
            $fileModel->increment('download_count');

            // Log download
            \Log::info('File downloaded', [
                'model' => get_class($model),
                'model_id' => $model->id,
                'file_id' => $fileModel->id,
                'file_name' => $fileModel->file_name,
                'user_id' => auth()->id(),
            ]);

            return \Storage::disk('public')->download(
                $fileModel->file_path,
                $fileModel->file_name
            );

        } catch (\Exception $e) {
            \Log::error('File download failed: ' . $e->getMessage(), [
                'model' => get_class($model),
                'model_id' => $model->id,
                'file_id' => $fileModel->id
            ]);

            return redirect()->back()->with('error', 'Download failed.');
        }
    }

    /**
     * Handle bulk file operations
     */
    protected function handleBulkFileOperations(
        Request $request,
        $model,
        string $relationshipMethod
    ): JsonResponse {
        $this->initializeFileUploadService();

        $request->validate([
            'action' => 'required|in:delete,download',
            'file_ids' => 'required|array|min:1',
            'file_ids.*' => 'integer'
        ]);

        try {
            $files = $model->{$relationshipMethod}()
                ->whereIn('id', $request->file_ids)
                ->get();

            if ($files->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid files selected'
                ], 422);
            }

            switch ($request->action) {
                case 'delete':
                    return $this->handleBulkDelete($files);

                case 'download':
                    return $this->handleBulkDownload($files, $model);

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid action'
                    ], 422);
            }

        } catch (\Exception $e) {
            \Log::error('Bulk operation failed: ' . $e->getMessage(), [
                'model' => get_class($model),
                'model_id' => $model->id,
                'action' => $request->action
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Bulk operation failed'
            ], 500);
        }
    }

    /**
     * Handle bulk file deletion
     */
    protected function handleBulkDelete($files): JsonResponse
    {
        $deleted = 0;
        $failed = 0;

        DB::transaction(function () use ($files, &$deleted, &$failed) {
            foreach ($files as $file) {
                try {
                    $this->fileUploadService->deleteFile(
                        $file->file_path,
                        'public',
                        $file->thumbnail_path
                    );
                    $file->delete();
                    $deleted++;
                } catch (\Exception $e) {
                    $failed++;
                    \Log::error('Individual file deletion failed: ' . $e->getMessage(), [
                        'file_id' => $file->id
                    ]);
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => "Deleted {$deleted} file(s)" . ($failed > 0 ? ", {$failed} failed" : ""),
            'deleted' => $deleted,
            'failed' => $failed
        ]);
    }

    /**
     * Handle bulk file download
     */
    protected function handleBulkDownload($files, $model)
    {
        try {
            $zipName = strtolower(class_basename($model)) . '_files_' . $model->id . '_' . now()->format('Y-m-d_H-i-s') . '.zip';
            $filePaths = $files->pluck('file_path')->toArray();

            $zipPath = $this->fileUploadService->createZipArchive($filePaths, $zipName);

            if (!$zipPath) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create download archive'
                ], 500);
            }

            // Increment download counts
            foreach ($files as $file) {
                $file->increment('download_count');
            }

            // Log bulk download
            \Log::info('Bulk files downloaded', [
                'model' => get_class($model),
                'model_id' => $model->id,
                'file_count' => $files->count(),
                'user_id' => auth()->id(),
            ]);

            return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            \Log::error('Bulk download failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create download archive'
            ], 500);
        }
    }

    /**
     * Get file statistics for a model
     */
    protected function getFileStatistics($model, string $relationshipMethod): array
    {
        $files = $model->{$relationshipMethod}()->get();

        return [
            'total_files' => $files->count(),
            'total_size' => $files->sum('file_size'),
            'total_downloads' => $files->sum('download_count'),
            'files_by_category' => $files->groupBy('category')->map->count(),
            'files_by_type' => $files->groupBy(function ($file) {
                return $this->fileUploadService->getFileCategory($file->file_type);
            })->map->count(),
            'recent_uploads' => $files->sortByDesc('created_at')->take(5)->values(),
            'most_downloaded' => $files->sortByDesc('download_count')->take(5)->values(),
            'largest_files' => $files->sortByDesc('file_size')->take(5)->values(),
        ];
    }

    /**
     * Search files for a model
     */
    protected function searchFiles(
        Request $request,
        $model,
        string $relationshipMethod
    ): JsonResponse {
        $request->validate([
            'query' => 'required|string|min:1|max:255',
            'category' => 'nullable|string',
            'type' => 'nullable|string',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $query = $model->{$relationshipMethod}();
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
        $files = $query->orderBy('created_at', 'desc')->limit($limit)->get();

        return response()->json([
            'success' => true,
            'files' => $files->map(function ($file) use ($model) {
                return $this->formatFileResponse($file, $model);
            }),
            'total' => $files->count(),
        ]);
    }

    /**
     * Format file response for API
     */
    protected function formatFileResponse($file, $model): array
    {
        $modelClass = strtolower(class_basename($model));
        
        return [
            'id' => $file->id,
            'name' => $file->file_name,
            'size' => $this->fileUploadService->formatFileSize($file->file_size),
            'type' => $this->getFileTypeName($file->file_type),
            'category' => $file->category ?: 'General',
            'description' => $file->description,
            'is_public' => $file->is_public,
            'download_count' => $file->download_count,
            'created_at' => $file->created_at->format('M j, Y H:i'),
            'download_url' => route("admin.{$modelClass}s.files.download", [$model, $file]),
            'preview_url' => $this->canPreview($file->file_type) ? 
                route("admin.{$modelClass}s.files.preview", [$model, $file]) : null,
            'thumbnail_url' => $file->thumbnail_path ? 
                \Storage::disk('public')->url($file->thumbnail_path) : null,
        ];
    }

    /**
     * Get human-readable file type name
     */
    protected function getFileTypeName(string $mimeType): string
    {
        $types = [
            'application/pdf' => 'PDF Document',
            'application/msword' => 'Word Document',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'Word Document',
            'application/vnd.ms-excel' => 'Excel Spreadsheet',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'Excel Spreadsheet',
            'application/vnd.ms-powerpoint' => 'PowerPoint Presentation',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'PowerPoint Presentation',
            'text/plain' => 'Text File',
            'text/csv' => 'CSV File',
            'application/zip' => 'ZIP Archive',
            'application/x-rar-compressed' => 'RAR Archive',
            'application/x-7z-compressed' => '7Z Archive',
        ];

        if (str_starts_with($mimeType, 'image/')) {
            return 'Image';
        }

        return $types[$mimeType] ?? 'Unknown';
    }

    /**
     * Check if file type can be previewed
     */
    protected function canPreview(string $mimeType): bool
    {
        return str_starts_with($mimeType, 'image/') ||
               $mimeType === 'application/pdf' ||
               str_starts_with($mimeType, 'text/') ||
               in_array($mimeType, ['application/json', 'application/xml']);
    }

    /**
     * Get the foreign key for the model
     */
    protected function getModelForeignKey($model): string
    {
        $className = strtolower(class_basename($model));
        return $className . '_id';
    }
}