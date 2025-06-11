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
            // Traditional file upload validation
            'banner_images' => 'nullable|array|max:2',
            'banner_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            // Universal uploader data
            'image_categories' => 'nullable|array',
            'image_categories.*' => 'string|in:desktop,mobile',
        ]);

        try {
            DB::transaction(function () use ($request, $validated, &$banner) {
                // Set default display order
                if (empty($validated['display_order'])) {
                    $validated['display_order'] = Banner::where('banner_category_id', $validated['banner_category_id'])
                        ->max('display_order') + 1;
                }

                // Create banner without images first
                $bannerData = collect($validated)->except(['banner_images', 'image_categories'])->toArray();
                $banner = Banner::create($bannerData);

                // Handle image uploads if present
                if ($request->hasFile('banner_images') || $request->filled('temp_images')) {
                    $this->handleCreatePageImages($request, $banner);
                }
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
                'validated_data' => $validated,
                'has_files' => $request->hasFile('banner_images')
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
                'files' => 'required|array|min:1|max:2',
                'files.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'categories' => 'nullable|array',
                'categories.*' => 'string|in:desktop,mobile',
            ]);

            $uploadedFiles = [];
            $categories = $request->input('categories', []);
            $sessionKey = 'temp_banner_images_' . session()->getId();

            foreach ($request->file('files') as $index => $file) {
                $imageType = $categories[$index] ?? ($index === 0 ? 'desktop' : 'mobile');
                
                // Store in temporary directory
                $tempFilename = 'temp_' . uniqid() . '_' . $imageType . '.' . $file->getClientOriginalExtension();
                $tempPath = $file->storeAs('temp/banners', $tempFilename, 'public');

                // Store temp file info in session
                $tempImageData = [
                    'temp_path' => $tempPath,
                    'original_name' => $file->getClientOriginalName(),
                    'image_type' => $imageType,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_at' => now()->toISOString()
                ];

                // Store in session grouped by type
                $sessionData = session()->get($sessionKey, []);
                $sessionData[$imageType] = $tempImageData;
                session()->put($sessionKey, $sessionData);

                $uploadedFiles[] = [
                    'id' => 'temp_' . $imageType . '_' . uniqid(),
                    'name' => ucfirst($imageType) . ' Image',
                    'file_name' => $file->getClientOriginalName(),
                    'category' => $imageType,
                    'type' => $imageType,
                    'url' => Storage::disk('public')->url($tempPath),
                    'size' => $this->formatFileSize($file->getSize()),
                    'temp_path' => $tempPath,
                    'is_temp' => true
                ];
            }

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
            $tempPath = $request->input('temp_path');
            $imageType = $request->input('image_type');
            $sessionKey = 'temp_banner_images_' . session()->getId();

            if (!$tempPath || !$imageType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required parameters'
                ], 400);
            }

            // Delete physical file
            if (Storage::disk('public')->exists($tempPath)) {
                Storage::disk('public')->delete($tempPath);
            }

            // Remove from session
            $sessionData = session()->get($sessionKey, []);
            unset($sessionData[$imageType]);
            session()->put($sessionKey, $sessionData);

            return response()->json([
                'success' => true,
                'message' => 'Temporary image deleted successfully!'
            ]);

        } catch (\Exception $e) {
            \Log::error('Temporary image deletion failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete temporary image'
            ], 500);
        }
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
                \Log::warning('Temporary file not found: ' . $tempPath);
                return;
            }

            // Generate permanent filename
            $extension = pathinfo($tempImageData['original_name'], PATHINFO_EXTENSION);
            $filename = $this->generateImageFilename($tempImageData['original_name'], $imageType, $banner->id, $extension);
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
                    'to' => $permanentPath
                ]);
            } else {
                \Log::error('Failed to move temporary image', [
                    'banner_id' => $banner->id,
                    'image_type' => $imageType,
                    'temp_path' => $tempPath
                ]);
            }

        } catch (\Exception $e) {
            \Log::error('Error moving temporary image: ' . $e->getMessage(), [
                'banner_id' => $banner->id,
                'image_type' => $imageType,
                'temp_data' => $tempImageData
            ]);
        }
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
            $cutoffTime = now()->subHours(2); // Clean files older than 2 hours
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

    /**
     * Enhanced image upload handler for Universal File Uploader
     */
    public function uploadImages(Request $request, Banner $banner)
    {
        try {
            // Validate the request
            $request->validate([
                'files' => 'required|array|min:1|max:2',
                'files.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
                'category' => 'nullable|string|in:desktop,mobile',
            ]);

            $uploadedFiles = [];
            $categories = $request->input('categories', []);

            foreach ($request->file('files') as $index => $file) {
                // Determine image type from category or index
                $imageType = $categories[$index] ?? ($index === 0 ? 'desktop' : 'mobile');
                
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
                'request_data' => $request->except(['files'])
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