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
            'open_in_new_tab' => 'boolean',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        try {
            DB::transaction(function () use ($request, &$validated) {
                // Set default display order if not provided
                if (empty($validated['display_order'])) {
                    $validated['display_order'] = Banner::where('banner_category_id', $validated['banner_category_id'])->max('display_order') + 1;
                }

                // Create the banner
                $banner = Banner::create($validated);

                // Handle file uploads if present
                if ($request->hasFile('files') || $request->filled('existing_images')) {
                    $this->handleBannerImages($request, $banner);
                }
            });

            return redirect()->route('admin.banners.index')
                ->with('success', 'Banner created successfully.');

        } catch (\Exception $e) {
            \Log::error('Banner creation failed: ' . $e->getMessage(), [
                'validated_data' => $validated
            ]);

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
            'open_in_new_tab' => 'boolean',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        try {
            DB::transaction(function () use ($request, $banner, &$validated) {
                // Update the banner
                $banner->update($validated);

                // Handle file uploads if present
                if ($request->hasFile('files')) {
                    $this->handleBannerImages($request, $banner);
                }
            });

            return redirect()->route('admin.banners.index')
                ->with('success', 'Banner updated successfully.');

        } catch (\Exception $e) {
            \Log::error('Banner update failed: ' . $e->getMessage(), [
                'banner_id' => $banner->id,
                'validated_data' => $validated
            ]);

            $categories = BannerCategory::where('is_active', true)
                ->orderBy('display_order')
                ->get();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update banner. Please try again.')
                ->with(compact('banner', 'categories'));
        }
    }

    /**
     * Handle banner image uploads using the universal file upload system
     */
    public function uploadImages(Request $request, Banner $banner)
    {
        $config = [
            'disk' => 'public',
            'max_file_size' => 5 * 1024 * 1024, // 5MB
            'max_files' => 2, // Desktop + Mobile
            'directory_prefix' => 'banners',
            'generate_thumbnails' => false,
            'image_resize' => [
                'enabled' => true,
                'max_width' => 1920,
                'max_height' => 1080,
                'quality' => 85
            ],
            'allowed_types' => [
                'image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'
            ]
        ];

        return $this->handleFileUploads(
            $request,
            $banner,
            'images', // This would be a custom relationship we'll create
            $config
        );
    }

    /**
     * Handle image deletion
     */
    public function deleteImage(Request $request, Banner $banner)
    {
        $imageType = $request->input('image_type', 'desktop');
        
        try {
            if ($imageType === 'desktop' && $banner->image) {
                Storage::disk('public')->delete($banner->image);
                $banner->update(['image' => null]);
            } elseif ($imageType === 'mobile' && $banner->mobile_image) {
                Storage::disk('public')->delete($banner->mobile_image);
                $banner->update(['mobile_image' => null]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully!'
            ]);

        } catch (\Exception $e) {
            \Log::error('Banner image deletion failed: ' . $e->getMessage(), [
                'banner_id' => $banner->id,
                'image_type' => $imageType
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image'
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
    private function validateBannerImage($file)
    {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \InvalidArgumentException('Invalid image type. Allowed: JPEG, PNG, GIF, WebP');
        }

        if ($file->getSize() > $maxSize) {
            throw new \InvalidArgumentException('Image size exceeds 5MB limit');
        }
    }

    /**
     * Generate safe filename for banner image
     */
    private function generateBannerImageFilename($file, $imageType, $bannerId)
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('YmdHis');
        
        return "banner_{$bannerId}_{$imageType}_{$timestamp}.{$extension}";
    }

    /**
     * Process and store image with resizing
     */
    private function processAndStoreImage($file, $filePath)
    {
        try {
            // Try with Intervention Image v3
            if (class_exists('\Intervention\Image\ImageManager')) {
                $manager = new \Intervention\Image\ImageManager(
                    new \Intervention\Image\Drivers\Gd\Driver()
                );

                $image = $manager->read($file->getRealPath());
                
                // Resize to max 1920px width while maintaining aspect ratio
                $image->scaleDown(width: 1920);
                
                // Encode as JPEG with 85% quality
                $encoded = $image->toJpeg(85);
                
                // Store the processed image
                Storage::disk('public')->put($filePath, $encoded);
                
                return $filePath;
            }

            // Fallback to basic upload
            return $file->storeAs(dirname($filePath), basename($filePath), 'public');

        } catch (\Exception $e) {
            \Log::warning('Image processing failed, using basic upload: ' . $e->getMessage());
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
}