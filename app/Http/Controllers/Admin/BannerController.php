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

    /**
     * Display a listing of banners
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'category', 'status']);
        $banners = $this->bannerService->getBannersForAdmin($filters, 10);
        $categories = BannerCategory::orderBy('display_order')->get();

        return view('admin.banners.index', compact('banners', 'categories'));
    }

    /**
     * Show the form for creating a new banner
     */
    public function create()
    {
        $categories = BannerCategory::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return view('admin.banners.create', compact('categories'));
    }

    /**
     * Store a newly created banner
     */
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
            // File validation
            'desktop_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'files' => 'nullable|array|max:2',
            'files.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $banner = null;
        $uploadedFiles = [];

        try {
            DB::transaction(function () use ($request, $validated, &$banner, &$uploadedFiles) {
                // Set default display order
                if (empty($validated['display_order'])) {
                    $validated['display_order'] = Banner::where('banner_category_id', $validated['banner_category_id'])
                        ->max('display_order') + 1;
                }

                // Create banner
                $banner = Banner::create($validated);

                if (!$banner) {
                    throw new \Exception('Failed to create banner record');
                }

                \Log::info('Banner created successfully', [
                    'banner_id' => $banner->id,
                    'title' => $banner->title
                ]);

                // Handle file uploads
                $uploadedFiles = $this->processFileUploads($request, $banner);

                \Log::info('File processing completed', [
                    'banner_id' => $banner->id,
                    'uploaded_count' => count($uploadedFiles)
                ]);
            });

            $this->bannerService->clearCache();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Banner created successfully!',
                    'banner' => $banner->load('category'),
                    'uploaded_files' => $uploadedFiles,
                    'redirect' => route('admin.banners.index')
                ]);
            }

            return redirect()->route('admin.banners.index')
                ->with('success', 'Banner created successfully.');

        } catch (\Exception $e) {
            \Log::error('Banner creation failed: ' . $e->getMessage(), [
                'validated_data' => $validated,
                'trace' => $e->getTraceAsString()
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

    /**
     * Show the form for editing a banner
     */
    public function edit(Banner $banner)
    {
        $categories = BannerCategory::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return view('admin.banners.edit', compact('banner', 'categories'));
    }

    /**
     * Update the specified banner
     */
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
            // File validation
            'desktop_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'files' => 'nullable|array|max:2',
            'files.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $uploadedFiles = [];

        try {
            DB::transaction(function () use ($request, $banner, $validated, &$uploadedFiles) {
                // Update banner
                $updated = $banner->update($validated);
                
                if (!$updated) {
                    throw new \Exception('Failed to update banner record');
                }

                \Log::info('Banner updated successfully', [
                    'banner_id' => $banner->id
                ]);

                // Handle file uploads
                $uploadedFiles = $this->processFileUploads($request, $banner);

                \Log::info('File processing completed for update', [
                    'banner_id' => $banner->id,
                    'uploaded_count' => count($uploadedFiles)
                ]);
            });

            $this->bannerService->clearCache();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Banner updated successfully!',
                    'banner' => $banner->fresh()->load('category'),
                    'uploaded_files' => $uploadedFiles
                ]);
            }

            return redirect()->route('admin.banners.index')
                ->with('success', 'Banner updated successfully.');

        } catch (\Exception $e) {
            \Log::error('Banner update failed: ' . $e->getMessage(), [
                'banner_id' => $banner->id,
                'validated_data' => $validated,
                'trace' => $e->getTraceAsString()
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

    /**
     * AJAX image upload for existing banners
     */
    public function uploadImages(Request $request, Banner $banner)
    {
        try {
            $request->validate([
                'files' => 'required|array|min:1|max:2',
                'files.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            ]);

            $uploadedFiles = [];

            DB::transaction(function () use ($request, $banner, &$uploadedFiles) {
                $uploadedFiles = $this->processFileUploads($request, $banner);
            });

            return response()->json([
                'success' => true,
                'message' => count($uploadedFiles) === 1 ? 'Image uploaded successfully!' : count($uploadedFiles) . ' images uploaded successfully!',
                'files' => $uploadedFiles,
                'banner' => $banner->fresh()->load('category')
            ]);

        } catch (\Exception $e) {
            \Log::error('AJAX image upload failed: ' . $e->getMessage(), [
                'banner_id' => $banner->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Delete specific banner image
     */
    public function deleteImage(Request $request, Banner $banner)
    {
        $request->validate([
            'image_type' => 'required|string|in:desktop,mobile'
        ]);

        $imageType = $request->input('image_type');
        
        try {
            DB::transaction(function () use ($banner, $imageType) {
                if ($imageType === 'desktop' && $banner->image) {
                    Storage::disk('public')->delete($banner->image);
                    $banner->update(['image' => null]);
                } elseif ($imageType === 'mobile' && $banner->mobile_image) {
                    Storage::disk('public')->delete($banner->mobile_image);
                    $banner->update(['mobile_image' => null]);
                } else {
                    throw new \Exception('Image not found or already deleted');
                }
            });

            \Log::info("Image deleted successfully", [
                'banner_id' => $banner->id,
                'image_type' => $imageType
            ]);

            return response()->json([
                'success' => true,
                'message' => ucfirst($imageType) . ' image deleted successfully!',
                'banner' => $banner->fresh()->load('category')
            ]);

        } catch (\Exception $e) {
            \Log::error('Image deletion failed: ' . $e->getMessage(), [
                'banner_id' => $banner->id,
                'image_type' => $imageType
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified banner
     */
    public function destroy(Banner $banner)
    {
        try {
            DB::transaction(function () use ($banner) {
                // Delete images if they exist
                if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                    Storage::disk('public')->delete($banner->image);
                }

                if ($banner->mobile_image && Storage::disk('public')->exists($banner->mobile_image)) {
                    Storage::disk('public')->delete($banner->mobile_image);
                }

                $banner->delete();
            });

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

    /**
     * Process all file uploads (traditional inputs + universal uploader)
     */
    private function processFileUploads(Request $request, Banner $banner)
    {
        $uploadedFiles = [];

        \Log::info('Processing file uploads', [
            'banner_id' => $banner->id,
            'request_files' => array_keys($request->allFiles()),
            'has_desktop' => $request->hasFile('desktop_image'),
            'has_mobile' => $request->hasFile('mobile_image'),
            'has_files_array' => $request->hasFile('files'),
        ]);

        try {
            // Handle traditional file inputs
            if ($request->hasFile('desktop_image')) {
                $uploadedFiles[] = $this->processFile($request->file('desktop_image'), $banner, 'desktop');
            }

            if ($request->hasFile('mobile_image')) {
                $uploadedFiles[] = $this->processFile($request->file('mobile_image'), $banner, 'mobile');
            }

            // Handle universal file uploader files
            if ($request->hasFile('files')) {
                $files = $request->file('files');
                $categories = $this->getFileCategories($request);

                foreach ($files as $index => $file) {
                    $imageType = $this->determineImageType($categories, $index);
                    $uploadedFiles[] = $this->processFile($file, $banner, $imageType);
                }
            }

            \Log::info('All files processed successfully', [
                'banner_id' => $banner->id,
                'total_uploaded' => count($uploadedFiles)
            ]);

            return $uploadedFiles;

        } catch (\Exception $e) {
            \Log::error('File processing failed: ' . $e->getMessage(), [
                'banner_id' => $banner->id,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Process a single file upload
     */
    private function processFile($file, Banner $banner, string $imageType)
    {
        \Log::info('Processing single file', [
            'banner_id' => $banner->id,
            'image_type' => $imageType,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize()
        ]);

        // Validate file
        $this->validateFile($file);

        // Generate filename and paths
        $filename = $this->generateFilename($file, $imageType, $banner->id);
        $directory = "banners/{$banner->id}";
        $filePath = $directory . '/' . $filename;

        // Store file
        $storedPath = $this->storeFile($file, $filePath);

        // Update banner record
        $this->updateBannerImage($banner, $storedPath, $imageType);

        \Log::info('File processed successfully', [
            'banner_id' => $banner->id,
            'image_type' => $imageType,
            'stored_path' => $storedPath
        ]);

        return [
            'id' => $imageType . '_' . $banner->id,
            'name' => ucfirst($imageType) . ' Image',
            'file_name' => $filename,
            'type' => $imageType,
            'url' => Storage::disk('public')->url($storedPath),
            'file_path' => $storedPath,
            'size' => $this->formatFileSize($file->getSize()),
            'download_url' => Storage::disk('public')->url($storedPath),
        ];
    }

    /**
     * Validate uploaded file
     */
    private function validateFile($file)
    {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \InvalidArgumentException('Invalid image type. Allowed: JPEG, PNG, GIF, WebP');
        }

        if ($file->getSize() > $maxSize) {
            throw new \InvalidArgumentException('Image size exceeds 5MB limit');
        }

        if (!getimagesize($file->getRealPath())) {
            throw new \InvalidArgumentException('Invalid image file');
        }
    }

    /**
     * Generate unique filename
     */
    private function generateFilename($file, string $imageType, int $bannerId)
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('YmdHis');
        $random = Str::random(6);
        
        return "banner_{$bannerId}_{$imageType}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Store file with optional image processing
     */
    private function storeFile($file, string $filePath)
    {
        try {
            // Ensure directory exists
            $directory = dirname($filePath);
            Storage::disk('public')->makeDirectory($directory);

            // Try with Intervention Image if available
            if (class_exists('Intervention\Image\ImageManager')) {
                $manager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                $image = $manager->read($file->getRealPath());
                
                // Resize to max 1920px width while maintaining aspect ratio
                $image->scaleDown(width: 1920);
                
                // Encode as JPEG with 85% quality
                $encoded = $image->toJpeg(85);
                
                // Store the processed image
                $stored = Storage::disk('public')->put($filePath, $encoded);
                
                if (!$stored) {
                    throw new \Exception('Failed to store processed image');
                }
                
                \Log::info("Image processed with Intervention Image", [
                    'file_path' => $filePath,
                    'original_size' => $file->getSize(),
                    'processed_size' => strlen($encoded)
                ]);
                
                return $filePath;
            }

            // Fallback to basic upload
            $storedPath = $file->storeAs(dirname($filePath), basename($filePath), 'public');
            
            if (!$storedPath) {
                throw new \Exception('Failed to store image file');
            }
            
            return $storedPath;

        } catch (\Exception $e) {
            \Log::error('File storage failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update banner with image path
     */
    private function updateBannerImage(Banner $banner, string $imagePath, string $imageType)
    {
        // Verify file exists
        if (!Storage::disk('public')->exists($imagePath)) {
            throw new \Exception("Stored image file not found: {$imagePath}");
        }

        if ($imageType === 'mobile') {
            // Delete old mobile image if exists
            if ($banner->mobile_image && Storage::disk('public')->exists($banner->mobile_image)) {
                Storage::disk('public')->delete($banner->mobile_image);
            }
            
            $updated = $banner->update(['mobile_image' => $imagePath]);
        } else {
            // Delete old desktop image if exists
            if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                Storage::disk('public')->delete($banner->image);
            }
            
            $updated = $banner->update(['image' => $imagePath]);
        }

        if (!$updated) {
            throw new \Exception("Failed to update banner {$imageType} field");
        }

        // Refresh banner instance
        $banner->refresh();
    }

    /**
     * Get file categories from request
     */
    private function getFileCategories(Request $request)
    {
        // Check multiple possible input names
        $categories = $request->input('category', []);
        if (empty($categories)) {
            $categories = $request->input('categories', []);
        }
        
        return $categories;
    }

    /**
     * Determine image type based on categories and index
     */
    private function determineImageType($categories, int $index)
    {
        if (is_array($categories) && isset($categories[$index])) {
            return $categories[$index];
        } elseif (is_string($categories)) {
            return $categories;
        } elseif ($index > 0) {
            return 'mobile';
        }
        
        return 'desktop'; // default
    }

    /**
     * Format file size in human readable format
     */
    private function formatFileSize(int $bytes, int $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    // Additional banner management methods
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
}