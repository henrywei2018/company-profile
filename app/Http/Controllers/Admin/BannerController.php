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
use Intervention\Image\Laravel\Facades\Image;
use RahulHaque\LaravelFilepond\Http\Controllers\FilepondController;

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

        // Get all categories for filter dropdown
        $categories = BannerCategory::orderBy('display_order')->get();

        return view('admin.banners.index', compact('banners', 'categories'));
    }

    public function create()
    {
        // Get active categories for the dropdown
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

            // FilePond fields
            'desktop_image_filepond' => 'nullable|string',
            'mobile_image_filepond' => 'nullable|string',

            // Fallback traditional uploads
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        try {
            DB::transaction(function () use ($request, &$validated) {
                // Handle desktop image
                if ($request->filled('desktop_image_filepond')) {
                    $validated['image'] = $this->processFilepondImage(
                        $request->input('desktop_image_filepond'),
                        'banners/desktop'
                    );
                } elseif ($request->hasFile('image')) {
                    $validated['image'] = $this->handleImageUpload($request->file('image'), 'banners/desktop');
                }

                // Handle mobile image
                if ($request->filled('mobile_image_filepond')) {
                    $validated['mobile_image'] = $this->processFilepondImage(
                        $request->input('mobile_image_filepond'),
                        'banners/mobile'
                    );
                } elseif ($request->hasFile('mobile_image')) {
                    $validated['mobile_image'] = $this->handleImageUpload($request->file('mobile_image'), 'banners/mobile');
                }

                // Create the banner
                Banner::create($validated);
            });

            return redirect()->route('admin.banners.index')
                ->with('success', 'Banner created successfully.');

        } catch (\Exception $e) {
            \Log::error('Banner creation failed: ' . $e->getMessage(), [
                'validated_data' => $validated
            ]);

            // Get categories again for the view
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
        // Get active categories for the dropdown
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
            'open_in_new_tab' => 'boolean',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',

            // FilePond fields
            'desktop_image_filepond' => 'nullable|string',
            'mobile_image_filepond' => 'nullable|string',

            // Fallback traditional uploads
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        try {
            DB::transaction(function () use ($request, $banner, &$validated) {
                // Handle desktop image
                if ($request->filled('desktop_image_filepond')) {
                    // Delete old image if exists
                    if ($banner->image) {
                        Storage::delete('public/' . $banner->image);
                    }

                    $validated['image'] = $this->processFilepondImage(
                        $request->input('desktop_image_filepond'),
                        'banners/desktop'
                    );
                } elseif ($request->hasFile('image')) {
                    // Delete old image if exists
                    if ($banner->image) {
                        Storage::delete('public/' . $banner->image);
                    }

                    $validated['image'] = $this->handleImageUpload($request->file('image'), 'banners/desktop');
                }

                // Handle mobile image
                if ($request->filled('mobile_image_filepond')) {
                    // Delete old mobile image if exists
                    if ($banner->mobile_image) {
                        Storage::delete('public/' . $banner->mobile_image);
                    }

                    $validated['mobile_image'] = $this->processFilepondImage(
                        $request->input('mobile_image_filepond'),
                        'banners/mobile'
                    );
                } elseif ($request->hasFile('mobile_image')) {
                    // Delete old mobile image if exists
                    if ($banner->mobile_image) {
                        Storage::delete('public/' . $banner->mobile_image);
                    }

                    $validated['mobile_image'] = $this->handleImageUpload($request->file('mobile_image'), 'banners/mobile');
                }

                // Update the banner
                $banner->update($validated);
            });

            return redirect()->route('admin.banners.index')
                ->with('success', 'Banner updated successfully.');

        } catch (\Exception $e) {
            \Log::error('Banner update failed: ' . $e->getMessage(), [
                'banner_id' => $banner->id,
                'validated_data' => $validated
            ]);

            // Get categories again for the view
            $categories = BannerCategory::where('is_active', true)
                ->orderBy('display_order')
                ->get();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update banner. Please try again.')
                ->with(compact('banner', 'categories'));
        }
    }
    public function destroy(Banner $banner)
    {
        try {
            // Delete images if they exist
            if ($banner->image) {
                Storage::delete('public/' . $banner->image);
            }

            if ($banner->mobile_image) {
                Storage::delete('public/' . $banner->mobile_image);
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
    protected function handleImageUpload($image, $path)
    {
        // Generate a unique filename
        $filename = uniqid() . '.' . $image->getClientOriginalExtension();
        $storagePath = $path . '/' . $filename;

        try {
            // Try Intervention Image v3 first
            if (class_exists('\Intervention\Image\ImageManager')) {
                $manager = new \Intervention\Image\ImageManager(
                    new \Intervention\Image\Drivers\Gd\Driver() // or Imagick\Driver()
                );

                $processedImage = $manager->read($image->getRealPath());

                // Resize maintaining aspect ratio
                $processedImage->scaleDown(width: 1920);

                // Encode as JPEG with 85% quality
                $encoded = $processedImage->toJpeg(85);

                // Save to storage
                Storage::put('public/' . $storagePath, $encoded);

                return $storagePath;
            }

            // Try Intervention Image v2 fallback
            if (class_exists('\Intervention\Image\Facades\Image')) {
                $img = Image::make($image->getRealPath())
                    ->resize(1920, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->encode('jpg', 85);

                // Save to storage
                Storage::put('public/' . $storagePath, $img);

                return $storagePath;
            }

            // If Intervention Image is not available, use basic Laravel upload
            return $image->storeAs($path, $filename, 'public');

        } catch (\Exception $e) {
            \Log::warning('Image processing failed, using basic upload: ' . $e->getMessage());

            // Fallback to basic upload without processing
            return $image->storeAs($path, $filename, 'public');
        }
    }
    protected function handleImageUploadWithDimensions($image, $path, $maxWidth = 1920, $maxHeight = null)
    {
        // Generate a unique filename
        $filename = uniqid() . '.' . $image->getClientOriginalExtension();
        $storagePath = $path . '/' . $filename;

        try {
            // Try Intervention Image v3 first
            if (class_exists('\Intervention\Image\ImageManager')) {
                $manager = new \Intervention\Image\ImageManager(
                    new \Intervention\Image\Drivers\Gd\Driver()
                );

                $processedImage = $manager->read($image->getRealPath());

                // Resize with specific dimensions
                if ($maxHeight) {
                    $processedImage->cover($maxWidth, $maxHeight);
                } else {
                    $processedImage->scaleDown(width: $maxWidth);
                }

                // Encode as JPEG with 85% quality
                $encoded = $processedImage->toJpeg(85);

                // Save to storage
                Storage::put('public/' . $storagePath, $encoded);

                return $storagePath;
            }

            // Try Intervention Image v2 fallback
            if (class_exists('\Intervention\Image\Facades\Image')) {
                $constraint = function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                };

                if ($maxHeight) {
                    $img = Image::make($image->getRealPath())
                        ->fit($maxWidth, $maxHeight)
                        ->encode('jpg', 85);
                } else {
                    $img = Image::make($image->getRealPath())
                        ->resize($maxWidth, null, $constraint)
                        ->encode('jpg', 85);
                }

                // Save to storage
                Storage::put('public/' . $storagePath, $img);

                return $storagePath;
            }

            // If Intervention Image is not available, use basic Laravel upload
            return $image->storeAs($path, $filename, 'public');

        } catch (\Exception $e) {
            \Log::warning('Image processing failed, using basic upload: ' . $e->getMessage());

            // Fallback to basic upload without processing
            return $image->storeAs($path, $filename, 'public');
        }
    }
    public function toggleStatus(Banner $banner)
    {
        $bannerService = app(BannerService::class);
        $bannerService->toggleStatus($banner);

        return redirect()->back()->with('success', 'Banner status updated successfully.');
    }
    public function duplicate(Banner $banner)
    {
        $bannerService = app(BannerService::class);
        $newBanner = $bannerService->duplicate($banner);

        return redirect()->route('admin.banners.edit', $newBanner)
            ->with('success', 'Banner duplicated successfully. You can now edit the copy.');
    }
    public function filepondUpload(Request $request)
    {
        try {
            // Validate the file
            $request->validate([
                'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Max 2MB
            ]);

            $file = $request->file('file');

            // Use the FilePond controller from the package
            $filepondController = new FilepondController();
            $response = $filepondController->upload($request);

            // If successful, store additional banner metadata in session
            if ($response->getStatusCode() === 200) {
                $serverId = $response->getContent();

                // Store banner context in session
                session()->put("filepond_banner_{$serverId}", [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                    'uploaded_at' => now()->toISOString()
                ]);
            }

            return $response;

        } catch (\Exception $e) {
            \Log::error('FilePond banner upload failed: ' . $e->getMessage(), [
                'file_name' => $request->file('file')?->getClientOriginalName()
            ]);

            return response()->json(['error' => 'Upload failed: ' . $e->getMessage()], 500);
        }
    }
    public function filepondDelete(Request $request)
    {
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
            session()->forget("filepond_banner_{$serverId}");

            return $response;

        } catch (\Exception $e) {
            \Log::error('FilePond banner delete failed: ' . $e->getMessage());
            return response()->json(['error' => 'Delete failed'], 500);
        }
    }
    public function removeImage(Request $request, Banner $banner)
    {
        $imageType = $request->input('image_type', 'desktop');

        $this->bannerService->removeImage($banner, $imageType);

        return redirect()->back()->with('success', 'Image removed successfully.');
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
    public function reorder(Request $request)
    {
        $request->validate([
            'banner_ids' => 'required|array',
            'category_id' => 'required|exists:banner_categories,id',
        ]);

        $this->bannerService->reorderBanners($request->banner_ids, $request->category_id);

        return response()->json(['success' => true, 'message' => 'Banners reordered successfully.']);
    }
    public function statistics()
    {
        $stats = $this->bannerService->getStatistics();

        // Add recent banners
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

        // Add popular categories
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
        // Get filtered banners
        $filters = $request->only(['search', 'category', 'status']);
        $banners = $this->bannerService->getBannersForAdmin($filters, 1000); // Large limit for export

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="banners-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($banners) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'ID',
                'Title',
                'Subtitle',
                'Description',
                'Category',
                'Button Text',
                'Button Link',
                'Is Active',
                'Display Order',
                'Start Date',
                'End Date',
                'Created At',
                'Updated At'
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
    protected function processFilepondImage(string $serverId, string $directory): string
    {
        try {
            // Get file metadata from session
            $fileData = session()->get("filepond_banner_{$serverId}");

            if (!$fileData) {
                throw new \Exception('FilePond file data not found in session');
            }

            // Get the temporary file path from FilePond
            $tempPath = config('filepond.path') . '/' . $serverId;
            $tempDisk = config('filepond.disk', 'local');

            if (!Storage::disk($tempDisk)->exists($tempPath)) {
                throw new \Exception('FilePond temporary file not found');
            }

            // Generate permanent filename
            $filename = $this->generateSafeFilename($fileData['original_name']);
            $permanentPath = $directory . '/' . $filename;

            // Move from temp to permanent location
            $publicDisk = 'public';

            if ($tempDisk === $publicDisk) {
                // Same disk, just move
                if (!Storage::disk($publicDisk)->move($tempPath, $permanentPath)) {
                    throw new \Exception('Failed to move file to permanent location');
                }
            } else {
                // Different disks, copy then delete
                $tempContent = Storage::disk($tempDisk)->get($tempPath);
                if (!Storage::disk($publicDisk)->put($permanentPath, $tempContent)) {
                    throw new \Exception('Failed to copy file to permanent location');
                }
                Storage::disk($tempDisk)->delete($tempPath);
            }

            // Clean up session data
            session()->forget("filepond_banner_{$serverId}");

            return $permanentPath;

        } catch (\Exception $e) {
            \Log::error('FilePond image processing failed: ' . $e->getMessage(), [
                'server_id' => $serverId,
                'directory' => $directory
            ]);

            throw $e;
        }
    }

    protected function generateSafeFilename(string $originalName): string
    {
        $pathInfo = pathinfo($originalName);
        $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';
        $basename = $pathInfo['filename'] ?? 'banner';

        return uniqid() . '_' . Str::slug($basename) . $extension;
    }
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
            $filepondKeys = array_filter($sessionKeys, function ($key) {
                return str_starts_with($key, 'filepond_banner_');
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
            \Log::error('Banner temp files cleanup failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Cleanup failed: ' . $e->getMessage()
            ], 500);
        }
    }
}