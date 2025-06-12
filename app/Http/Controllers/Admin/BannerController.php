<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\BannerCategory;
use Illuminate\Http\Request;
use App\Services\BannerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class BannerController extends Controller
{
    protected $bannerService;

    public function __construct(BannerService $bannerService)
    {
        $this->bannerService = $bannerService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'category', 'status']);
        $banners = $this->bannerService->getBannersForAdmin($filters, 10);
        $categories = BannerCategory::orderBy('display_order')->get();

        return view('admin.banners.index', compact('banners', 'categories'));
    }

    public function create()
    {
        $categories = BannerCategory::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return view('admin.banners.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'banner_category_id' => 'required|exists:banner_categories,id',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:50',
            'button_link' => 'nullable|string|max:255',
            'link_type' => 'nullable|string|in:auto,internal,external,route,email,phone,anchor',
            'open_in_new_tab' => 'boolean',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        try {
            DB::transaction(function () use ($request, $validated, &$banner) {
                // Set default display order
                if (empty($validated['display_order'])) {
                    $validated['display_order'] = Banner::where('banner_category_id', $validated['banner_category_id'])
                        ->max('display_order') + 1;
                }

                // Create banner without images first
                $banner = Banner::create($validated);

                // Process temporary images
                $this->processTempImagesFromSession($banner);
            });

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Banner created successfully!',
                    'banner' => $banner->fresh()->load('category'),
                    'redirect' => route('admin.banners.edit', $banner)
                ]);
            }

            return redirect()->route('admin.banners.index')
                ->with('success', 'Banner created successfully!');

        } catch (\Exception $e) {
            \Log::error('Banner creation failed: ' . $e->getMessage(), [
                'validated_data' => $validated
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create banner: ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create banner. Please try again.');
        }
    }

    public function edit(Banner $banner)
    {
        $categories = BannerCategory::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return view('admin.banners.edit', compact('banner', 'categories'));
    }

    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'banner_category_id' => 'required|exists:banner_categories,id',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:50',
            'button_link' => 'nullable|string|max:255',
            'link_type' => 'nullable|string|in:auto,internal,external,route,email,phone,anchor',
            'open_in_new_tab' => 'boolean',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        try {
            $banner->update($validated);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Banner updated successfully!',
                    'banner' => $banner->fresh()->load('category')
                ]);
            }

            return redirect()->route('admin.banners.index')
                ->with('success', 'Banner updated successfully.');

        } catch (\Exception $e) {
            \Log::error('Banner update failed: ' . $e->getMessage(), [
                'banner_id' => $banner->id,
                'validated_data' => $validated
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update banner: ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update banner. Please try again.');
        }
    }
    public function uploadTempImages(Request $request)
    {
        try {
            $request->validate([
                'temp_images' => 'required|array|min:1|max:2',
                'temp_images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'category' => 'nullable|string|in:desktop,mobile',
                'categories' => 'nullable|array',
                'categories.*' => 'string|in:desktop,mobile',
            ]);

            $uploadedFiles = [];
            $files = $request->file('temp_images');
            $categories = $request->input('categories', []);
            
            // Consistent session key format
            $sessionKey = 'banner_temp_files_' . session()->getId();
            $sessionData = session()->get($sessionKey, []);

            foreach ($files as $index => $file) {
                $imageType = $categories[$index] ?? ($request->input('category')) ?? ($index === 0 ? 'desktop' : 'mobile');
                
                // Generate unique temp identifier with better format
                $tempId = 'temp_' . $imageType . '_' . uniqid() . '_' . time();
                $tempFilename = $tempId . '.' . $file->getClientOriginalExtension();
                $tempPath = $file->storeAs('temp/banners', $tempFilename, 'public');
    
                // Enhanced temp file metadata
                $tempImageData = [
                    'temp_id' => $tempId,
                    'temp_path' => $tempPath,
                    'original_name' => $file->getClientOriginalName(),
                    'image_type' => $imageType,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_at' => now()->toISOString(),
                    'session_id' => session()->getId()
                ];
    
                // Store in session with type as key for easy replacement
                $sessionData[$imageType] = $tempImageData;
                session()->put($sessionKey, $sessionData);
    
                $uploadedFiles[] = [
                    'id' => $tempId, // This should match temp_id
                    'temp_id' => $tempId, // Explicit temp_id
                    'name' => ucfirst($imageType) . ' Image',
                    'file_name' => $file->getClientOriginalName(),
                    'category' => $imageType,
                    'type' => $imageType,
                    'url' => Storage::disk('public')->url($tempPath),
                    'size' => $this->formatFileSize($file->getSize()),
                    'temp_path' => $tempPath,
                    'is_temp' => true, // Mark as temporary file
                    'created_at' => now()->format('M j, Y H:i')
                ];
            }
    
            \Log::info('Temp files uploaded', [
                'files' => $uploadedFiles,
                'session_key' => $sessionKey
            ]);
    
            return response()->json([
                'success' => true,
                'message' => count($uploadedFiles) === 1 
                    ? 'Image uploaded successfully!' 
                    : count($uploadedFiles) . ' images uploaded successfully!',
                'files' => $uploadedFiles
            ]);
    
        } catch (\Exception $e) {
            \Log::error('Temporary image upload failed: ' . $e->getMessage());
    
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete temporary image
     */
    public function deleteTempImage(Request $request)
{
    try {
        // Log the incoming request for debugging
        \Log::info('Delete temp image request received', [
            'method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'raw_content' => $request->getContent(),
            'all_input' => $request->all(),
            'json_input' => $request->json()->all() ?? null
        ]);

        // Handle both JSON and form data
        $input = [];
        
        if ($request->isJson()) {
            $input = $request->json()->all();
        } else {
            $input = $request->all();
        }
        
        // Try to get temp_id from various sources
        $tempId = $input['temp_id'] ?? 
                  $input['id'] ?? 
                  $request->input('temp_id') ?? 
                  $request->input('id') ?? 
                  $request->getContent();
        
        // If content is JSON string, try to decode it
        if (empty($tempId) && $request->getContent()) {
            $rawContent = $request->getContent();
            if (is_string($rawContent)) {
                $decoded = json_decode($rawContent, true);
                if (is_array($decoded)) {
                    $tempId = $decoded['temp_id'] ?? $decoded['id'] ?? null;
                } else {
                    // Might be just the temp_id as plain text
                    $tempId = trim($rawContent, '"');
                }
            }
        }
        
        \Log::info('Extracted temp_id', ['temp_id' => $tempId]);

        if (empty($tempId)) {
            return response()->json([
                'success' => false,
                'message' => 'Missing temp file identifier',
                'debug' => [
                    'input' => $input,
                    'raw_content' => $request->getContent(),
                    'method' => $request->method()
                ]
            ], 400);
        }

        $sessionKey = 'banner_temp_files_' . session()->getId();
        $sessionData = session()->get($sessionKey, []);

        \Log::info('Session data for temp files', [
            'session_key' => $sessionKey,
            'session_data' => $sessionData
        ]);

        // Find the temp file by ID
        $tempFileData = null;
        $imageType = null;
        
        foreach ($sessionData as $type => $data) {
            if (isset($data['temp_id']) && $data['temp_id'] === $tempId) {
                $tempFileData = $data;
                $imageType = $type;
                break;
            }
        }

        if (!$tempFileData) {
            \Log::warning('Temporary file not found', [
                'temp_id' => $tempId,
                'available_temp_ids' => array_map(function($data) {
                    return $data['temp_id'] ?? 'no_temp_id';
                }, $sessionData)
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Temporary file not found',
                'debug' => [
                    'temp_id' => $tempId,
                    'available_files' => array_keys($sessionData)
                ]
            ], 404);
        }

        // Delete physical file
        if (Storage::disk('public')->exists($tempFileData['temp_path'])) {
            Storage::disk('public')->delete($tempFileData['temp_path']);
        }

        // Remove from session
        unset($sessionData[$imageType]);
        session()->put($sessionKey, $sessionData);

        \Log::info('Temporary file deleted successfully', [
            'temp_id' => $tempId,
            'image_type' => $imageType,
            'session_id' => session()->getId()
        ]);

        return response()->json([
            'success' => true,
            'message' => ucfirst($imageType) . ' image deleted successfully!'
        ]);

    } catch (\Exception $e) {
        \Log::error('Temporary image deletion failed: ' . $e->getMessage(), [
            'request_data' => $request->all(),
            'raw_content' => $request->getContent(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to delete temporary image: ' . $e->getMessage()
        ], 500);
    }
}
    public function getTempFiles(Request $request)
    {
        try {
            $sessionKey = 'banner_temp_files_' . session()->getId();
            $sessionData = session()->get($sessionKey, []);
            
            $files = [];
            foreach ($sessionData as $imageType => $data) {
                // Verify file still exists
                if (Storage::disk('public')->exists($data['temp_path'])) {
                    $files[] = [
                        'id' => $data['temp_id'],
                        'name' => ucfirst($imageType) . ' Image',
                        'file_name' => $data['original_name'],
                        'category' => $imageType,
                        'type' => $imageType,
                        'url' => Storage::disk('public')->url($data['temp_path']),
                        'size' => $this->formatFileSize($data['file_size']),
                        'temp_id' => $data['temp_id'],
                        'is_temp' => true,
                        'created_at' => \Carbon\Carbon::parse($data['uploaded_at'])->format('M j, Y H:i')
                    ];
                } else {
                    // Clean up broken reference
                    unset($sessionData[$imageType]);
                }
            }
            
            // Update session with cleaned data
            session()->put($sessionKey, $sessionData);

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

    protected function handleTempImages(Banner $banner)
{
    $sessionKey = 'temp_banner_images_' . session()->getId();
    $tempImages = session()->get($sessionKey, []);

    \Log::info('Processing temp images', [
        'banner_id' => $banner->id,
        'session_key' => $sessionKey,
        'temp_images' => $tempImages
    ]);

    if (empty($tempImages)) {
        \Log::info('No temp images found in session');
        return;
    }

    foreach ($tempImages as $imageType => $tempImageData) {
        try {
            $this->moveTempImageToPermanent($tempImageData, $banner, $imageType);
        } catch (\Exception $e) {
            \Log::error('Failed to process temp image: ' . $e->getMessage(), [
                'banner_id' => $banner->id,
                'image_type' => $imageType,
                'temp_data' => $tempImageData
            ]);
        }
    }

    // Clear session data after processing
    session()->forget($sessionKey);
    \Log::info('Cleared temp images from session');
}

    /**
     * Handle images from create page (temp files or traditional upload)
     */
    protected function handleCreatePageImages(Request $request, Banner $banner)
    {
        $sessionKey = 'temp_banner_images_' . session()->getId();
        $tempImages = session()->get($sessionKey, []);

        // Handle traditional file uploads first
        if ($request->hasFile('banner_images')) {
            $categories = $request->input('image_categories', []);
            
            foreach ($request->file('banner_images') as $index => $file) {
                $imageType = $categories[$index] ?? ($index === 0 ? 'desktop' : 'mobile');
                $this->processAndAssignImage($file, $banner, $imageType);
            }
        }

        // Handle temporary uploaded files
        if (!empty($tempImages)) {
            foreach ($tempImages as $imageType => $tempImageData) {
                $this->moveTempImageToPermanent($tempImageData, $banner, $imageType);
            }

            // Clear temporary images from session
            session()->forget($sessionKey);
        }
    }

    /**
     * Move temporary image to permanent location
     */
    protected function moveTempImageToPermanent(array $tempImageData, Banner $banner, string $imageType)
    {
        try {
            $tempPath = $tempImageData['temp_path'];
            
            if (!Storage::disk('public')->exists($tempPath)) {
                throw new \Exception('Temporary file not found: ' . $tempPath);
            }

            // Generate permanent filename
            $extension = pathinfo($tempImageData['original_name'], PATHINFO_EXTENSION);
            $filename = $this->generateImageFilename(
                $tempImageData['original_name'], 
                $imageType, 
                $banner->id, 
                $extension
            );
            $directory = "banners/{$banner->id}";
            $permanentPath = $directory . '/' . $filename;

            // Ensure directory exists
            Storage::disk('public')->makeDirectory($directory);

            // Move file from temp to permanent location
            if (Storage::disk('public')->move($tempPath, $permanentPath)) {
                // Update banner with new image path
                $this->assignImageToBanner($banner, $permanentPath, $imageType);
                
                \Log::info('Temporary image moved to permanent location', [
                    'banner_id' => $banner->id,
                    'image_type' => $imageType,
                    'from' => $tempPath,
                    'to' => $permanentPath,
                    'original_name' => $tempImageData['original_name']
                ]);
                
                return $permanentPath;
            } else {
                throw new \Exception('Failed to move file from temp to permanent location');
            }

        } catch (\Exception $e) {
            \Log::error('Error moving temporary image: ' . $e->getMessage(), [
                'banner_id' => $banner->id,
                'image_type' => $imageType,
                'temp_data' => $tempImageData
            ]);
            throw $e;
        }
    }
protected function processTempImagesFromSession(Banner $banner)
    {
        $sessionKey = 'banner_temp_files_' . session()->getId();
        $sessionData = session()->get($sessionKey, []);

        if (empty($sessionData)) {
            return;
        }

        foreach ($sessionData as $imageType => $tempImageData) {
            try {
                if (!Storage::disk('public')->exists($tempImageData['temp_path'])) {
                    \Log::warning('Temporary file not found during processing: ' . $tempImageData['temp_path']);
                    continue;
                }

                $this->moveTempImageToPermanent($tempImageData, $banner, $imageType);
                
            } catch (\Exception $e) {
                \Log::error('Failed to process temp image: ' . $e->getMessage(), [
                    'banner_id' => $banner->id,
                    'image_type' => $imageType,
                    'temp_data' => $tempImageData
                ]);
            }
        }

        // Clear processed temporary images from session
        session()->forget($sessionKey);
        
        // Also cleanup any physical temp files for this session
        $this->cleanupSessionTempFiles(session()->getId());
    }

    /**
     * Process and assign image directly (for traditional uploads)
     */
    protected function processAndAssignImage($file, Banner $banner, string $imageType)
    {
        $filename = $this->generateImageFilename($file->getClientOriginalName(), $imageType, $banner->id, $file->getClientOriginalExtension());
        $directory = "banners/{$banner->id}";
        $filePath = $directory . '/' . $filename;

        // Process and store image
        $storedPath = $this->processAndStoreImage($file, $filePath);

        // Update banner with image path
        $this->assignImageToBanner($banner, $storedPath, $imageType);
    }

    /**
     * Generate image filename with extension parameter
     */
    protected function generateImageFilename($originalName, string $imageType, int $bannerId, string $extension = null)
    {
        if (!$extension) {
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        }
        
        $timestamp = now()->format('YmdHis');
        $random = Str::random(6);
        
        return "banner_{$bannerId}_{$imageType}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Cleanup old temporary files (run via scheduler)
     */
    public function cleanupTempFiles()
    {
        try {
            $tempDir = 'temp/banners';
            $cutoffTime = now()->subHours(2);
            $deletedCount = 0;

            if (Storage::disk('public')->exists($tempDir)) {
                $files = Storage::disk('public')->files($tempDir);

                foreach ($files as $file) {
                    $lastModified = Storage::disk('public')->lastModified($file);
                    
                    if ($lastModified < $cutoffTime->timestamp) {
                        Storage::disk('public')->delete($file);
                        $deletedCount++;
                    }
                }
            }

            // Also cleanup old session data (optional - be careful with this)
            // This would require custom session cleanup logic

            \Log::info("Cleaned up {$deletedCount} temporary banner files");

            return response()->json([
                'success' => true,
                'message' => "Cleaned up {$deletedCount} temporary files",
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            \Log::error('Temporary files cleanup failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Cleanup failed: ' . $e->getMessage()
            ], 500);
        }
    }
    protected function cleanupSessionTempFiles(string $sessionId)
    {
        try {
            $tempDir = 'temp/banners';
            
            if (!Storage::disk('public')->exists($tempDir)) {
                return;
            }

            $files = Storage::disk('public')->files($tempDir);
            $deletedCount = 0;

            foreach ($files as $file) {
                // Check if file belongs to this session (contains session ID or is old)
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
     * Enhanced image upload handler for Universal File Uploader
     */
    public function uploadImages(Request $request, Banner $banner)
{
    try {
        // Updated validation to match universal uploader field names
        $request->validate([
            'banner_images' => 'required|array|min:1|max:2',
            'banner_images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'category' => 'nullable|string|in:desktop,mobile',
            'categories' => 'nullable|array',
            'categories.*' => 'string|in:desktop,mobile',
        ]);

        $uploadedFiles = [];
        $files = $request->file('banner_images');
        $categories = $request->input('categories', []);

        foreach ($files as $index => $file) {
            // Determine image type from category or index
            $imageType = $categories[$index] ?? ($request->input('category')) ?? ($index === 0 ? 'desktop' : 'mobile');
            
            try {
                $fileData = $this->processImageUpload($file, $banner, $imageType);
                $uploadedFiles[] = $fileData;

                \Log::info("Banner image uploaded successfully", [
                    'banner_id' => $banner->id,
                    'image_type' => $imageType,
                    'file_path' => $fileData['file_path']
                ]);

            } catch (\Exception $e) {
                \Log::error('Individual image upload failed: ' . $e->getMessage(), [
                    'banner_id' => $banner->id,
                    'image_type' => $imageType,
                    'file_name' => $file->getClientOriginalName()
                ]);
                // Continue with other files instead of failing completely
            }
        }

        if (empty($uploadedFiles)) {
            throw new \Exception('No images were uploaded successfully');
        }

        // Clear banner cache if using cache
        if (method_exists($this->bannerService, 'clearCache')) {
            $this->bannerService->clearCache();
        }

        return response()->json([
            'success' => true,
            'message' => count($uploadedFiles) === 1 
                ? 'Image uploaded successfully!' 
                : count($uploadedFiles) . ' images uploaded successfully!',
            'files' => $uploadedFiles,
            'banner' => $banner->fresh()->load('category')
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
        ], 422);

    } catch (\Exception $e) {
        \Log::error('Banner image upload failed: ' . $e->getMessage(), [
            'banner_id' => $banner->id,
            'request_data' => $request->except(['banner_images'])
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Upload failed: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Enhanced image deletion handler
     */
    public function deleteImage(Request $request, Banner $banner)
    {
        try {
            // Handle both direct image type parameter and file ID
            if ($request->has('image_type')) {
                // Direct image type deletion (legacy support)
                $imageType = $request->input('image_type');
                return $this->deleteImageByType($banner, $imageType);
            }

            // File ID deletion (for Universal File Uploader)
            $fileId = $request->input('file_id') ?? $request->getContent();
            
            if (str_starts_with($fileId, 'desktop_')) {
                return $this->deleteImageByType($banner, 'desktop');
            } elseif (str_starts_with($fileId, 'mobile_')) {
                return $this->deleteImageByType($banner, 'mobile');
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid image identifier'
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Banner image deletion failed: ' . $e->getMessage(), [
                'banner_id' => $banner->id,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image: ' . $e->getMessage()
            ], 500);
        }
    }
    protected function processTempImages(array $tempImagesData, Banner $banner)
{
    $sessionKey = 'temp_banner_images_' . session()->getId();
    $sessionData = session()->get($sessionKey, []);

    foreach ($tempImagesData as $tempDataJson) {
        try {
            $tempData = json_decode($tempDataJson, true);
            if (!$tempData || !isset($tempData['type'])) {
                continue;
            }

            $imageType = $tempData['type']; // desktop or mobile
            
            // Get temp file data from session
            if (isset($sessionData[$imageType])) {
                $tempImageData = $sessionData[$imageType];
                $this->moveTempImageToPermanent($tempImageData, $banner, $imageType);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to process temp image: ' . $e->getMessage());
        }
    }

    // Clear session data
    session()->forget($sessionKey);
}

    /**
     * Process individual image upload
     */
    protected function processImageUpload($file, Banner $banner, string $imageType)
    {
        // Validate image type
        if (!in_array($imageType, ['desktop', 'mobile'])) {
            throw new \InvalidArgumentException('Invalid image type. Must be desktop or mobile.');
        }

        // Generate unique filename
        $filename = $this->generateImageFilename($file, $imageType, $banner->id);
        $directory = "banners/{$banner->id}";
        $filePath = $directory . '/' . $filename;

        // Process and store image
        $storedPath = $this->processAndStoreImage($file, $filePath);

        // Update banner record
        $this->assignImageToBanner($banner, $storedPath, $imageType);

        // Return file data for Universal File Uploader
        return [
            'id' => $imageType . '_' . $banner->id,
            'name' => ucfirst($imageType) . ' Image',
            'file_name' => $filename,
            'file_path' => $storedPath,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'category' => $imageType,
            'url' => Storage::disk('public')->url($storedPath),
            'download_url' => Storage::disk('public')->url($storedPath),
            'size' => $this->formatFileSize($file->getSize()),
            'type' => $imageType,
            'created_at' => now()->format('M j, Y H:i')
        ];
    }

    /**
     * Delete image by type
     */
    protected function deleteImageByType(Banner $banner, string $imageType)
    {
        if ($imageType === 'desktop' && $banner->image) {
            Storage::disk('public')->delete($banner->image);
            $banner->update(['image' => null]);
            $message = 'Desktop image deleted successfully!';
        } elseif ($imageType === 'mobile' && $banner->mobile_image) {
            Storage::disk('public')->delete($banner->mobile_image);
            $banner->update(['mobile_image' => null]);
            $message = 'Mobile image deleted successfully!';
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Image not found or already deleted'
            ], 404);
        }

        // Clear cache
        if (method_exists($this->bannerService, 'clearCache')) {
            $this->bannerService->clearCache();
        }

        \Log::info("Banner image deleted successfully", [
            'banner_id' => $banner->id,
            'image_type' => $imageType
        ]);

        return response()->json([
            'success' => true,
            'message' => $message,
            'banner' => $banner->fresh()->load('category')
        ]);
    }

    /**
     * Process and store image with optimization
     */
    protected function processAndStoreImage($file, string $filePath)
    {
        try {
            // Ensure directory exists
            $directory = dirname($filePath);
            Storage::disk('public')->makeDirectory($directory);

            // Try with Intervention Image for optimization
            if (class_exists('Intervention\Image\ImageManager')) {
                $manager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                $image = $manager->read($file->getRealPath());
                
                // Resize to max 1920px width while maintaining aspect ratio
                $image->scaleDown(width: 1920);
                
                // Encode as JPEG with 85% quality for better compression
                $encoded = $image->toJpeg(85);
                
                // Store the processed image
                Storage::disk('public')->put($filePath, $encoded);
                
                \Log::info("Image processed with Intervention Image", [
                    'file_path' => $filePath,
                    'original_size' => $file->getSize(),
                    'processed_size' => strlen($encoded)
                ]);
                
                return $filePath;
            }

            // Fallback to basic upload
            $storedPath = $file->storeAs(dirname($filePath), basename($filePath), 'public');
            
            \Log::info("Image stored with basic upload", [
                'file_path' => $storedPath,
                'size' => $file->getSize()
            ]);
            
            return $storedPath;

        } catch (\Exception $e) {
            \Log::warning('Image processing failed, using basic upload: ' . $e->getMessage());
            
            // Final fallback
            return $file->storeAs(dirname($filePath), basename($filePath), 'public');
        }
    }

    /**
     * Assign image to banner based on type
     */
    protected function assignImageToBanner(Banner $banner, string $imagePath, string $imageType)
    {
        if ($imageType === 'mobile') {
            // Delete old mobile image if exists
            if ($banner->mobile_image) {
                Storage::disk('public')->delete($banner->mobile_image);
            }
            $banner->update(['mobile_image' => $imagePath]);
        } else {
            // Delete old desktop image if exists
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            $banner->update(['image' => $imagePath]);
        }
    }

    /**
     * Format file size in human readable format
     */
    protected function formatFileSize(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    public function destroy(Banner $banner)
    {
        try {
            // Delete images if they exist
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }

            if ($banner->mobile_image) {
                Storage::disk('public')->delete($banner->mobile_image);
            }

            $banner->delete();

            // Clear cache
            if (method_exists($this->bannerService, 'clearCache')) {
                $this->bannerService->clearCache();
            }

            return redirect()->route('admin.banners.index')
                ->with('success', 'Banner deleted successfully.');

        } catch (\Exception $e) {
            \Log::error('Banner deletion failed: ' . $e->getMessage(), [
                'banner_id' => $banner->id
            ]);

            return redirect()->route('admin.banners.index')
                ->with('error', 'Failed to delete banner. Please try again.');
        }
    }

    public function toggleStatus(Banner $banner)
    {
        $this->bannerService->toggleStatus($banner);
        return redirect()->back()->with('success', 'Banner status updated successfully.');
    }

    public function duplicate(Banner $banner)
    {
        $newBanner = $this->bannerService->duplicate($banner);
        return redirect()->route('admin.banners.edit', $newBanner)
            ->with('success', 'Banner duplicated successfully. You can now edit the copy.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'banner_ids' => 'required|array|min:1',
            'banner_ids.*' => 'exists:banners,id',
        ]);

        $action = $request->input('action');
        $bannerIds = $request->input('banner_ids');

        switch ($action) {
            case 'activate':
                $count = $this->bannerService->bulkUpdateStatus($bannerIds, true);
                $message = "{$count} banner(s) activated successfully.";
                break;
            case 'deactivate':
                $count = $this->bannerService->bulkUpdateStatus($bannerIds, false);
                $message = "{$count} banner(s) deactivated successfully.";
                break;
            case 'delete':
                $count = $this->bannerService->bulkDelete($bannerIds);
                $message = "{$count} banner(s) deleted successfully.";
                break;
            default:
                return redirect()->back()->with('error', 'Invalid action.');
        }

        return redirect()->back()->with('success', $message);
    }

    public function statistics()
    {
        $stats = $this->bannerService->getStatistics();

        $stats['recent_banners'] = Banner::with('category')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($banner) {
                return [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'category' => $banner->category->name,
                    'status' => $banner->is_active ? 'active' : 'inactive',
                    'created_at' => $banner->created_at->format('M j, Y'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    public function export(Request $request)
    {
        $filters = $request->only(['search', 'category', 'status']);
        $banners = $this->bannerService->getBannersForAdmin($filters, 1000);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="banners-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($banners) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID', 'Title', 'Subtitle', 'Description', 'Category',
                'Button Text', 'Button Link', 'Is Active', 'Display Order',
                'Start Date', 'End Date', 'Created At', 'Updated At'
            ]);

            foreach ($banners as $banner) {
                fputcsv($file, [
                    $banner->id,
                    $banner->title,
                    $banner->subtitle,
                    $banner->description,
                    $banner->category->name,
                    $banner->button_text,
                    $banner->button_link,
                    $banner->is_active ? 'Yes' : 'No',
                    $banner->display_order,
                    $banner->start_date ? $banner->start_date->format('Y-m-d H:i:s') : '',
                    $banner->end_date ? $banner->end_date->format('Y-m-d H:i:s') : '',
                    $banner->created_at->format('Y-m-d H:i:s'),
                    $banner->updated_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Reorder banners within a category
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'banner_ids' => 'required|array|min:1',
            'banner_ids.*' => 'exists:banners,id',
            'category_id' => 'required|exists:banner_categories,id',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $bannerIds = $request->input('banner_ids');
                $categoryId = $request->input('category_id');
                
                foreach ($bannerIds as $index => $bannerId) {
                    Banner::where('id', $bannerId)
                          ->where('banner_category_id', $categoryId)
                          ->update(['display_order' => $index + 1]);
                }
            });

            // Clear cache
            if (method_exists($this->bannerService, 'clearCache')) {
                $this->bannerService->clearCache();
            }

            return response()->json([
                'success' => true,
                'message' => 'Banners reordered successfully.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Banner reorder failed: ' . $e->getMessage(), [
                'banner_ids' => $request->input('banner_ids'),
                'category_id' => $request->input('category_id')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder banners.'
            ], 500);
        }
    }

    /**
     * Search banners (AJAX endpoint)
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:1|max:255',
            'category' => 'nullable|exists:banner_categories,id',
            'status' => 'nullable|in:active,inactive,scheduled,expired',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        try {
            $query = Banner::with(['category'])
                ->where(function ($q) use ($request) {
                    $searchTerm = $request->input('query');
                    $q->where('title', 'like', "%{$searchTerm}%")
                      ->orWhere('subtitle', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%");
                });

            // Apply category filter
            if ($request->filled('category')) {
                $query->where('banner_category_id', $request->input('category'));
            }

            // Apply status filter
            if ($request->filled('status')) {
                $status = $request->input('status');
                $now = now();
                
                switch ($status) {
                    case 'active':
                        $query->where('is_active', true)
                              ->where(function ($q) use ($now) {
                                  $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
                              })
                              ->where(function ($q) use ($now) {
                                  $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
                              });
                        break;
                    case 'inactive':
                        $query->where('is_active', false);
                        break;
                    case 'scheduled':
                        $query->where('is_active', true)->where('start_date', '>', $now);
                        break;
                    case 'expired':
                        $query->where('end_date', '<', $now);
                        break;
                }
            }

            $limit = $request->input('limit', 10);
            $banners = $query->orderBy('created_at', 'desc')
                           ->limit($limit)
                           ->get();

            return response()->json([
                'success' => true,
                'banners' => $banners->map(function ($banner) {
                    return [
                        'id' => $banner->id,
                        'title' => $banner->title,
                        'subtitle' => $banner->subtitle,
                        'category' => $banner->category->name,
                        'status' => $banner->status,
                        'is_active' => $banner->is_active,
                        'image_url' => $banner->hasDesktopImage() ? $banner->imageUrl : null,
                        'edit_url' => route('admin.banners.edit', $banner),
                        'created_at' => $banner->created_at->format('M j, Y'),
                    ];
                }),
                'total' => $banners->count(),
            ]);

        } catch (\Exception $e) {
            \Log::error('Banner search failed: ' . $e->getMessage(), [
                'query' => $request->input('query'),
                'filters' => $request->only(['category', 'status', 'limit'])
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Search failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Get banner counts for dashboard
     */
    public function getCounts()
    {
        try {
            $now = now();
            
            $counts = [
                'total' => Banner::count(),
                'active' => Banner::where('is_active', true)->count(),
                'inactive' => Banner::where('is_active', false)->count(),
                'live' => Banner::active()->count(),
                'scheduled' => Banner::where('is_active', true)
                    ->where('start_date', '>', $now)
                    ->count(),
                'expired' => Banner::where('end_date', '<', $now)->count(),
            ];

            return response()->json([
                'success' => true,
                'counts' => $counts
            ]);

        } catch (\Exception $e) {
            \Log::error('Banner counts failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get banner counts.'
            ], 500);
        }
    }

    /**
     * Clone/duplicate a banner
     */
    public function clone(Banner $banner)
    {
        try {
            DB::transaction(function () use ($banner, &$newBanner) {
                // Create new banner with copied data
                $newBanner = $banner->replicate([
                    'image',
                    'mobile_image'
                ]);
                
                $newBanner->title = $banner->title . ' (Copy)';
                $newBanner->is_active = false;
                $newBanner->display_order = Banner::where('banner_category_id', $banner->banner_category_id)
                    ->max('display_order') + 1;
                $newBanner->save();

                // Copy images if they exist
                if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                    $newImagePath = $this->copyBannerImage($banner->image, $newBanner->id, 'desktop');
                    if ($newImagePath) {
                        $newBanner->update(['image' => $newImagePath]);
                    }
                }

                if ($banner->mobile_image && Storage::disk('public')->exists($banner->mobile_image)) {
                    $newMobileImagePath = $this->copyBannerImage($banner->mobile_image, $newBanner->id, 'mobile');
                    if ($newMobileImagePath) {
                        $newBanner->update(['mobile_image' => $newMobileImagePath]);
                    }
                }
            });

            return redirect()->route('admin.banners.edit', $newBanner)
                ->with('success', 'Banner cloned successfully. You can now edit the copy.');

        } catch (\Exception $e) {
            \Log::error('Banner clone failed: ' . $e->getMessage(), [
                'original_banner_id' => $banner->id
            ]);

            return redirect()->back()
                ->with('error', 'Failed to clone banner. Please try again.');
        }
    }

    /**
     * Copy banner image to new banner directory
     */
    protected function copyBannerImage(string $originalPath, int $newBannerId, string $imageType)
    {
        try {
            $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
            $newFilename = "banner_{$newBannerId}_{$imageType}_" . now()->format('YmdHis') . "_" . Str::random(6) . ".{$extension}";
            $newPath = "banners/{$newBannerId}/{$newFilename}";

            // Ensure directory exists
            Storage::disk('public')->makeDirectory("banners/{$newBannerId}");

            // Copy the file
            if (Storage::disk('public')->copy($originalPath, $newPath)) {
                return $newPath;
            }

            return null;

        } catch (\Exception $e) {
            \Log::warning('Failed to copy banner image: ' . $e->getMessage(), [
                'original_path' => $originalPath,
                'new_banner_id' => $newBannerId,
                'image_type' => $imageType
            ]);

            return null;
        }
    }
}