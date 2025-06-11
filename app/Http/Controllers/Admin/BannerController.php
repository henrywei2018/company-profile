<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\BannerCategory;
use App\Traits\HandlesFileUploads;
use Illuminate\Http\Request;
use App\Services\BannerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use App\Helpers\FileHelper;

class BannerController extends Controller
{
    use HandlesFileUploads;

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
            // File validation for traditional uploads
            'desktop_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        try {
            DB::transaction(function () use ($request, &$validated, &$banner) {
                // Set default display order if not provided
                if (empty($validated['display_order'])) {
                    $validated['display_order'] = Banner::where('banner_category_id', $validated['banner_category_id'])->max('display_order') + 1;
                }

                // Create the banner first
                $banner = Banner::create($validated);

                // Handle image uploads
                $this->handleImageUploads($request, $banner);
            });

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Banner created successfully!',
                    'banner' => $banner->load('category'),
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

            $categories = BannerCategory::where('is_active', true)
                ->orderBy('display_order')
                ->get();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create banner. Please try again.')
                ->with(compact('categories'));
        }
    }

    public function edit(Banner $banner)
    {
        $categories = BannerCategory::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        // Get existing banner images for the file uploader
        $existingImages = [];
        if ($banner->image) {
            $existingImages[] = [
                'id' => 'desktop_' . $banner->id,
                'name' => 'Desktop Image',
                'file_name' => basename($banner->image),
                'type' => 'desktop',
                'url' => $banner->imageUrl,
                'file_path' => $banner->image,
                'size' => $this->getImageSize($banner->image),
                'download_url' => $banner->imageUrl,
            ];
        }

        if ($banner->mobile_image) {
            $existingImages[] = [
                'id' => 'mobile_' . $banner->id,
                'name' => 'Mobile Image',
                'file_name' => basename($banner->mobile_image),
                'type' => 'mobile',
                'url' => $banner->mobileImageUrl,
                'file_path' => $banner->mobile_image,
                'size' => $this->getImageSize($banner->mobile_image),
                'download_url' => $banner->mobileImageUrl,
            ];
        }

        return view('admin.banners.edit', compact('banner', 'categories', 'existingImages'));
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
            // File validation for traditional uploads
            'desktop_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        try {
            DB::transaction(function () use ($request, $banner, &$validated) {
                // Update the banner
                $banner->update($validated);

                // Handle image uploads
                $this->handleImageUploads($request, $banner);
            });

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
                'validated_data' => $validated,
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update banner: ' . $e->getMessage()
                ], 422);
            }

            $categories = BannerCategory::where('is_active', true)
                ->orderBy('display_order')
                ->get();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update banner. Please try again.')
                ->with(compact('banner', 'categories'));
        }
    }

    public function uploadImages(Request $request, Banner $banner)
    {
        try {
            $request->validate([
                'files' => 'required|array|min:1|max:2',
                'files.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'category' => 'nullable|string|in:desktop,mobile',
            ]);

            $uploadedFiles = $this->handleUniversalFileUploads($request, $banner);

            return response()->json([
                'success' => true,
                'message' => count($uploadedFiles) === 1 ? 'Image uploaded successfully!' : count($uploadedFiles) . ' images uploaded successfully!',
                'files' => $uploadedFiles,
                'banner' => $banner->fresh()->load('category')
            ]);

        } catch (\Exception $e) {
            \Log::error('AJAX image upload failed: ' . $e->getMessage(), [
                'banner_id' => $banner->id,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Handle image deletion
     */
    public function deleteImage(Request $request, Banner $banner)
    {
        $request->validate([
            'image_type' => 'required|string|in:desktop,mobile'
        ]);

        $imageType = $request->input('image_type');
        
        try {
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

            \Log::info("Image deleted successfully", [
                'banner_id' => $banner->id,
                'image_type' => $imageType
            ]);

            return response()->json([
                'success' => true,
                'message' => $message,
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
     * Handle banner image uploads and assignment
     */
    private function handleBannerImages(Request $request, Banner $banner)
    {
        if (!$request->hasFile('files')) {
            return;
        }

        $uploadedFiles = [];
        $imageType = $request->input('image_type', 'desktop'); // desktop or mobile

        foreach ($request->file('files') as $index => $file) {
            try {
                // Validate image
                $this->validateBannerImage($file);

                // Determine image type from request or index
                $currentImageType = $request->input("image_types.{$index}", $imageType);
                
                // Generate filename
                $filename = $this->generateBannerImageFilename($file, $currentImageType, $banner->id);
                $directory = "banners/{$banner->id}";
                $filePath = $directory . '/' . $filename;

                // Process and store image
                $storedPath = $this->processAndStoreImage($file, $filePath);

                // Update banner with image path
                $this->assignImageToBanner($banner, $storedPath, $currentImageType);

                $uploadedFiles[] = [
                    'type' => $currentImageType,
                    'path' => $storedPath,
                    'url' => Storage::disk('public')->url($storedPath),
                    'size' => $file->getSize()
                ];

            } catch (\Exception $e) {
                \Log::error('Banner image upload failed: ' . $e->getMessage(), [
                    'banner_id' => $banner->id,
                    'file_name' => $file->getClientOriginalName()
                ]);
                throw $e;
            }
        }

        return $uploadedFiles;
    }

    /**
     * Validate banner image
     */
    private function validateImageFile($file)
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
     * Generate safe filename for banner image
     */
    private function generateImageFilename($file, $imageType, $bannerId)
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('YmdHis');
        $random = Str::random(6);
        
        return "banner_{$bannerId}_{$imageType}_{$timestamp}_{$random}.{$extension}";
    }
    

    /**
     * Process and store image with resizing
     */
    private function processAndStoreImage($file, $filePath)
    {
        try {
            // Ensure directory exists
            $directory = dirname($filePath);
            Storage::disk('public')->makeDirectory($directory);

            // Try with Intervention Image v3/v4
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

            // Fallback to basic upload if Intervention Image is not available
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
    private function assignImageToBanner(Banner $banner, $imagePath, $imageType)
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
     * Get image file size
     */
    private function getImageSize($imagePath)
    {
        try {
            if (Storage::disk('public')->exists($imagePath)) {
                return Storage::disk('public')->size($imagePath);
            }
        } catch (\Exception $e) {
            \Log::warning('Could not get image size: ' . $e->getMessage());
        }
        
        return 0;
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

    // ... (keep other existing methods like toggleStatus, duplicate, bulkAction, etc.)
    
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

        $stats['popular_categories'] = BannerCategory::withCount('banners')
            ->orderBy('banners_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'banners_count' => $category->banners_count,
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
    private function handleImageUploads(Request $request, Banner $banner)
    {
        // Handle traditional file uploads
        if ($request->hasFile('desktop_image')) {
            $this->handleSingleImageUpload($request->file('desktop_image'), $banner, 'desktop');
        }

        if ($request->hasFile('mobile_image')) {
            $this->handleSingleImageUpload($request->file('mobile_image'), $banner, 'mobile');
        }

        // Handle universal file uploader files
        if ($request->hasFile('files')) {
            $this->handleUniversalFileUploads($request, $banner);
        }
    }

    /**
     * Handle single image upload (traditional form)
     */
    private function handleSingleImageUpload($file, Banner $banner, string $imageType)
    {
        try {
            // Validate image
            $this->validateImageFile($file);

            // Generate filename
            $filename = $this->generateImageFilename($file, $imageType, $banner->id);
            $directory = "banners/{$banner->id}";
            $filePath = $directory . '/' . $filename;

            // Process and store image
            $storedPath = $this->processAndStoreImage($file, $filePath);

            // Update banner with image path
            $this->assignImageToBanner($banner, $storedPath, $imageType);

            \Log::info("Image uploaded successfully", [
                'banner_id' => $banner->id,
                'image_type' => $imageType,
                'file_path' => $storedPath
            ]);

        } catch (\Exception $e) {
            \Log::error('Single image upload failed: ' . $e->getMessage(), [
                'banner_id' => $banner->id,
                'image_type' => $imageType,
                'file_name' => $file->getClientOriginalName()
            ]);
            throw $e;
        }
    }

    /**
     * Handle universal file uploader files
     */
    private function handleUniversalFileUploads(Request $request, Banner $banner)
    {
        $uploadedFiles = [];
        $categories = $request->input('categories', []);
        $descriptions = $request->input('descriptions', []);

        foreach ($request->file('files') as $index => $file) {
            try {
                // Determine image type from category or index
                $imageType = $categories[$index] ?? ($index === 0 ? 'desktop' : 'mobile');
                
                // Validate image
                $this->validateImageFile($file);

                // Generate filename
                $filename = $this->generateImageFilename($file, $imageType, $banner->id);
                $directory = "banners/{$banner->id}";
                $filePath = $directory . '/' . $filename;

                // Process and store image
                $storedPath = $this->processAndStoreImage($file, $filePath);

                // Update banner with image path
                $this->assignImageToBanner($banner, $storedPath, $imageType);

                $uploadedFiles[] = [
                    'id' => $imageType . '_' . $banner->id,
                    'name' => ucfirst($imageType) . ' Image',
                    'file_name' => $filename,
                    'type' => $imageType,
                    'url' => Storage::disk('public')->url($storedPath),
                    'file_path' => $storedPath,
                    'size' => FileHelper::formatFileSize($file->getSize()),
                    'download_url' => Storage::disk('public')->url($storedPath),
                ];

                \Log::info("Universal file upload successful", [
                    'banner_id' => $banner->id,
                    'image_type' => $imageType,
                    'file_path' => $storedPath
                ]);

            } catch (\Exception $e) {
                \Log::error('Universal file upload failed: ' . $e->getMessage(), [
                    'banner_id' => $banner->id,
                    'file_name' => $file->getClientOriginalName(),
                    'index' => $index
                ]);
                // Continue with other files
            }
        }

        return $uploadedFiles;
    }
}