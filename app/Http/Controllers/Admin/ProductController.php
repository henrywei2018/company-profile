<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use App\Models\Service;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $filters = $request->only([
            'search',
            'category',
            'service',
            'status',
            'brand',
            'stock_status'
        ]);

        // Build query with filters
        $query = Product::with(['category', 'service']);

        // Apply filters
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('sku', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('brand', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('short_description', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['category'])) {
            $query->where('product_category_id', $filters['category']);
        }

        if (!empty($filters['service'])) {
            $query->where('service_id', $filters['service']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['brand'])) {
            $query->where('brand', $filters['brand']);
        }

        if (!empty($filters['stock_status'])) {
            $query->where('stock_status', $filters['stock_status']);
        }

        // Get paginated results
        $products = $query->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get data for filters
        $categories = ProductCategory::active()
            ->orderBy('name')
            ->get();

        $services = Service::active()
            ->orderBy('title')
            ->get();

        // Get unique brands from products
        $brands = Product::whereNotNull('brand')
            ->where('brand', '!=', '')
            ->distinct()
            ->orderBy('brand')
            ->pluck('brand');

        return view('admin.products.index', compact(
            'products',
            'categories',
            'services',
            'brands'
        ));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        // Get active categories for dropdown
        $categories = ProductCategory::active()
            ->orderBy('name')
            ->get();

        // Get active services for dropdown
        $services = Service::active()
            ->orderBy('title')
            ->get();

        return view('admin.products.create', compact('categories', 'services'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(StoreProductRequest $request)
    {
        \Log::info('Product creation attempt', [
            'validated_data' => $request->validated(),
            'all_input' => $request->all()
        ]);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'product_category_id' => 'nullable|exists:product_categories,id',
            'service_id' => 'nullable|exists:services,id',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'currency' => 'nullable|string|max:3',
            'price_type' => 'nullable|in:fixed,quote,contact',
            'stock_quantity' => 'nullable|integer|min:0',
            'manage_stock' => 'boolean',
            'stock_status' => 'nullable|in:in_stock,out_of_stock,on_backorder',
            'specifications' => 'nullable|array',
            'technical_specs' => 'nullable|array',
            'dimensions' => 'nullable|array',
            'weight' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:published,draft,archived',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        try {
            DB::transaction(function () use ($request, $validated, &$product) {
                // Generate slug if not provided
                if (empty($validated['slug'])) {
                    $validated['slug'] = Str::slug($validated['name']);

                    // Ensure slug is unique
                    $originalSlug = $validated['slug'];
                    $counter = 1;
                    while (Product::where('slug', $validated['slug'])->exists()) {
                        $validated['slug'] = $originalSlug . '-' . $counter;
                        $counter++;
                    }
                }

                // Set default values
                $validated['currency'] = $validated['currency'] ?? 'IDR';
                $validated['price_type'] = $validated['price_type'] ?? 'fixed';
                $validated['stock_status'] = $validated['stock_status'] ?? 'in_stock';
                $validated['status'] = $validated['status'] ?? 'draft';
                $validated['is_featured'] = $request->has('is_featured');
                $validated['is_active'] = $request->has('is_active');
                $validated['manage_stock'] = $request->has('manage_stock');

                // Set sort order
                if (empty($validated['sort_order'])) {
                    $validated['sort_order'] = Product::max('sort_order') + 1;
                }

                // Create product
                $product = Product::create($validated);

                // Process temporary images if any
                $this->processTempImagesFromSession($product);
            });

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product created successfully!',
                    'redirect' => route('admin.products.index')
                ]);
            }

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Product created successfully!');

        } catch (\Exception $e) {
            \Log::error('Product creation failed: ' . $e->getMessage(), [
                'validated_data' => $validated,
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create product. Please try again.'
                ], 422);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create product. Please try again.');
        }
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        $product->load([
            'category',
            'service',
            'images' => function ($query) {
                $query->ordered();
            },
            'relatedServices'
        ]);

        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $product->load([
            'category',
            'service',
            'images' => function ($query) {
                $query->ordered();
            },
            'relatedServices'
        ]);

        $categories = ProductCategory::active()->ordered()->get();
        $services = Service::active()->ordered()->get();

        return view('admin.products.edit', compact('product', 'categories', 'services'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        // Handle AJAX image management actions
        if ($request->has('action')) {
            return $this->handleImageAction($request, $product);
        }

        // Regular update validation
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            // Add other validation rules as needed
        ]);

        try {
            DB::beginTransaction();

            // Update product basic information
            $updateData = [
                'name' => $request->name,
                'sku' => $request->sku,
                'short_description' => $request->short_description,
                'description' => $request->description,
                'status' => $request->status,
                'is_featured' => $request->boolean('is_featured'),
                'is_active' => $request->boolean('is_active'),
            ];

            // Handle temporary uploaded images
            $this->processTemporaryImages($product, $updateData);

            $product->update($updateData);

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Product updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Product update failed: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Failed to update product. Please try again.');
        }
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        try {
            DB::transaction(function () use ($product) {
                // Delete all product images
                foreach ($product->images as $image) {
                    if (Storage::disk('public')->exists($image->image_path)) {
                        Storage::disk('public')->delete($image->image_path);
                    }
                }

                // Delete featured image if exists
                if ($product->featured_image && Storage::disk('public')->exists($product->featured_image)) {
                    Storage::disk('public')->delete($product->featured_image);
                }

                // Delete gallery images
                if ($product->gallery) {
                    foreach ($product->gallery as $imagePath) {
                        if (Storage::disk('public')->exists($imagePath)) {
                            Storage::disk('public')->delete($imagePath);
                        }
                    }
                }

                // Delete the product (this will cascade delete images and relations)
                $product->delete();
            });

            return redirect()->route('admin.products.index')
                ->with('success', 'Product deleted successfully.');

        } catch (\Exception $e) {
            \Log::error('Product deletion failed: ' . $e->getMessage(), [
                'product_id' => $product->id
            ]);

            return redirect()->route('admin.products.index')
                ->with('error', 'Failed to delete product. Please try again.');
        }
    }

    /**
     * Upload temporary images for Universal File Uploader.
     */
    public function uploadTempImages(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            $file = $request->file('file');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = 'temp/products/' . $filename;

            // Store in temp directory
            $file->storeAs('public/' . dirname($path), basename($path));

            $tempFile = [
                'id' => Str::uuid(),
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'url' => Storage::url($path),
                'size' => $file->getSize(),
                'type' => $file->getMimeType(),
                'category' => $request->input('category', 'gallery'), // featured or gallery
                'uploaded_at' => now()->toISOString()
            ];

            // Store in session
            $tempFiles = session('temp_product_images', []);
            $tempFiles[] = $tempFile;
            session(['temp_product_images' => $tempFiles]);

            return response()->json([
                'success' => true,
                'file' => $tempFile,
                'message' => 'Image uploaded successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Temp image upload failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image.'
            ], 500);
        }
    }

    /**
     * Delete temporary image.
     */
    public function deleteTempImage(Request $request)
    {
        $request->validate([
            'file_id' => 'required|string'
        ]);

        try {
            $fileId = $request->file_id;
            $tempFiles = session('temp_product_images', []);

            foreach ($tempFiles as $index => $tempFile) {
                if ($tempFile['id'] === $fileId) {
                    // Delete physical file
                    if (Storage::disk('public')->exists($tempFile['path'])) {
                        Storage::disk('public')->delete($tempFile['path']);
                    }

                    // Remove from session
                    unset($tempFiles[$index]);
                    session(['temp_product_images' => array_values($tempFiles)]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Temporary file deleted successfully.'
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Temporary file not found.'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Delete temp image failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete temporary file.'
            ], 500);
        }
    }

    /**
     * Get temporary files from session.
     */
    public function getTempFiles()
    {
        $tempFiles = session('temp_product_images', []);

        return response()->json($tempFiles);
    }

    /**
     * Process temporary images from session and attach to product.
     */
    protected function processTempImagesFromSession(Product $product)
    {
        $sessionKey = 'product_temp_files_' . session()->getId();
        $sessionData = session()->get($sessionKey, []);

        if (empty($sessionData)) {
            return;
        }

        foreach ($sessionData as $imageType => $images) {
            if (is_array($images)) {
                foreach ($images as $tempImageData) {
                    try {
                        if (!Storage::disk('public')->exists($tempImageData['temp_path'])) {
                            \Log::warning('Product temporary file not found during processing: ' . $tempImageData['temp_path']);
                            continue;
                        }

                        $this->moveTempImageToPermanent($tempImageData, $product, $imageType);

                    } catch (\Exception $e) {
                        \Log::error('Failed to process product temp image: ' . $e->getMessage(), [
                            'product_id' => $product->id,
                            'image_type' => $imageType,
                            'temp_data' => $tempImageData
                        ]);
                    }
                }
            }
        }

        // Clear processed temporary images from session
        session()->forget($sessionKey);

        // Cleanup physical temp files for this session
        $this->cleanupSessionTempFiles(session()->getId());
    }
    protected function processTemporaryImages(Product $product, &$updateData)
    {
        $tempFiles = session('temp_product_images', []);

        if (empty($tempFiles)) {
            return;
        }

        $gallery = $product->gallery ?? [];

        foreach ($tempFiles as $tempFile) {
            // Move from temp to permanent location
            $tempPath = $tempFile['path'];
            $filename = basename($tempPath);
            $permanentPath = 'products/' . $product->id . '/' . $filename;

            // Create directory if it doesn't exist
            $directory = dirname(storage_path('app/public/' . $permanentPath));
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            // Move file
            if (Storage::disk('public')->exists($tempPath)) {
                Storage::disk('public')->move($tempPath, $permanentPath);

                // Determine where to place the image based on category
                $category = $tempFile['category'] ?? 'gallery';

                if ($category === 'featured' && !isset($updateData['featured_image'])) {
                    // Set as featured image if none exists yet
                    $updateData['featured_image'] = $permanentPath;
                } else {
                    // Add to gallery
                    $gallery[] = $permanentPath;
                }
            }
        }

        // Update gallery in the update data
        if (!empty($gallery)) {
            $updateData['gallery'] = $gallery;
        }

        // Clear temporary files from session
        session()->forget('temp_product_images');
    }

    /**
     * Move temporary image to permanent location.
     */
    protected function moveTempImageToPermanent(array $tempImageData, Product $product, string $imageType)
    {
        try {
            $tempPath = $tempImageData['temp_path'];

            if (!Storage::disk('public')->exists($tempPath)) {
                throw new \Exception('Temporary file not found: ' . $tempPath);
            }

            $extension = pathinfo($tempImageData['original_name'], PATHINFO_EXTENSION);
            $filename = $this->generateImageFilename(
                $tempImageData['original_name'],
                $imageType,
                $product->id,
                $extension
            );
            $directory = "products/{$product->id}";
            $permanentPath = $directory . '/' . $filename;

            // Ensure directory exists
            Storage::disk('public')->makeDirectory($directory);

            // Move file from temp to permanent location
            if (Storage::disk('public')->move($tempPath, $permanentPath)) {
                if ($imageType === 'featured') {
                    // Update product featured image
                    $product->update(['featured_image' => $permanentPath]);
                } else {
                    // Create ProductImage record for gallery
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $permanentPath,
                        'alt_text' => $tempImageData['original_name'],
                        'is_featured' => $imageType === 'featured',
                        'sort_order' => ProductImage::where('product_id', $product->id)->max('sort_order') + 1
                    ]);
                }

                \Log::info('Product temporary image moved to permanent location', [
                    'product_id' => $product->id,
                    'image_type' => $imageType,
                    'from' => $tempPath,
                    'to' => $permanentPath
                ]);

                return $permanentPath;
            } else {
                throw new \Exception('Failed to move file from temp to permanent location');
            }

        } catch (\Exception $e) {
            \Log::error('Error moving product temporary image: ' . $e->getMessage(), [
                'product_id' => $product->id,
                'image_type' => $imageType,
                'temp_data' => $tempImageData
            ]);
            throw $e;
        }
    }

    /**
     * Generate unique filename for product image.
     */
    protected function generateImageFilename($originalName, string $imageType, int $productId, string $extension = null)
    {
        if (!$extension) {
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        }

        $timestamp = now()->format('YmdHis');
        $random = Str::random(6);

        return "product_{$productId}_{$imageType}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Cleanup session temporary files.
     */
    protected function cleanupSessionTempFiles(string $sessionId)
    {
        try {
            $tempDir = 'temp/products';

            if (!Storage::disk('public')->exists($tempDir)) {
                return;
            }

            $files = Storage::disk('public')->files($tempDir);
            $deletedCount = 0;

            foreach ($files as $file) {
                $filename = basename($file);
                if (
                    str_contains($filename, $sessionId) ||
                    Storage::disk('public')->lastModified($file) < now()->subHours(1)->timestamp
                ) {

                    Storage::disk('public')->delete($file);
                    $deletedCount++;
                }
            }

            if ($deletedCount > 0) {
                \Log::info("Cleaned up {$deletedCount} product session temporary files for session: {$sessionId}");
            }

        } catch (\Exception $e) {
            \Log::warning('Failed to cleanup product session temp files: ' . $e->getMessage());
        }
    }

    /**
     * Format file size in human readable format.
     */
    protected function formatFileSize(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Delete specific product images.
     */
    protected function deleteProductImage(Request $request, Product $product)
    {
        $imagePath = $request->input('image_path');
        $imageType = $request->input('image_type');
        $galleryIndex = $request->input('gallery_index');

        if ($imageType === 'featured') {
            // Delete featured image
            if ($product->featured_image && $product->featured_image === $imagePath) {
                // Delete physical file
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }

                $product->update(['featured_image' => null]);

                return response()->json([
                    'success' => true,
                    'message' => 'Featured image deleted successfully.'
                ]);
            }
        } elseif ($imageType === 'gallery') {
            // Delete gallery image
            $gallery = $product->gallery ?? [];

            if (isset($gallery[$galleryIndex]) && $gallery[$galleryIndex] === $imagePath) {
                // Delete physical file
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }

                // Remove from gallery array
                unset($gallery[$galleryIndex]);
                $gallery = array_values($gallery); // Reindex array

                $product->update(['gallery' => $gallery]);

                return response()->json([
                    'success' => true,
                    'message' => 'Gallery image deleted successfully.'
                ]);
            }
        }

        return response()->json(['success' => false, 'message' => 'Image not found'], 404);
    }
    protected function handleImageAction(Request $request, Product $product)
    {
        $action = $request->input('action');

        try {
            switch ($action) {
                case 'delete_image':
                    return $this->deleteProductImage($request, $product);

                case 'set_featured':
                    return $this->setFeaturedImage($request, $product);

                case 'convert_to_gallery':
                    return $this->convertToGallery($request, $product);

                case 'reorder_gallery':
                    return $this->reorderGallery($request, $product);

                default:
                    return response()->json(['success' => false, 'message' => 'Unknown action'], 400);
            }
        } catch (\Exception $e) {
            \Log::error('Image action failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Action failed'], 500);
        }
    }

    /**
     * Attach service relations to product.
     */
    protected function attachServiceRelations(Product $product, array $relations)
    {
        foreach ($relations as $relation) {
            if (isset($relation['service_id']) && isset($relation['relation_type'])) {
                $product->relatedServices()->attach($relation['service_id'], [
                    'relation_type' => $relation['relation_type']
                ]);
            }
        }
    }

    /**
     * Sync service relations for product.
     */
    protected function syncServiceRelations(Product $product, array $relations)
    {
        $syncData = [];

        foreach ($relations as $relation) {
            if (isset($relation['service_id']) && isset($relation['relation_type'])) {
                $syncData[$relation['service_id']] = [
                    'relation_type' => $relation['relation_type']
                ];
            }
        }

        $product->relatedServices()->sync($syncData);
    }

    /**
     * Toggle product featured status.
     */
    public function toggleFeatured(Product $product)
    {
        $product->update(['is_featured' => !$product->is_featured]);

        $status = $product->is_featured ? 'featured' : 'unfeatured';

        return redirect()->back()
            ->with('success', "Product {$status} successfully.");
    }

    /**
     * Toggle product active status.
     */
    public function toggleActive(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);

        $status = $product->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Product {$status} successfully.");
    }

    /**
     * Update product sort order.
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $productIds = $request->input('product_ids');

                foreach ($productIds as $index => $productId) {
                    Product::where('id', $productId)
                        ->update(['sort_order' => $index + 1]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Product order updated successfully.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Product reorder failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder products.'
            ], 500);
        }
    }

    /**
     * Bulk action handler.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,feature,unfeature,delete,publish,draft',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
        ]);

        $action = $request->input('action');
        $productIds = $request->input('product_ids');

        try {
            DB::transaction(function () use ($action, $productIds) {
                switch ($action) {
                    case 'activate':
                        Product::whereIn('id', $productIds)->update(['is_active' => true]);
                        break;
                    case 'deactivate':
                        Product::whereIn('id', $productIds)->update(['is_active' => false]);
                        break;
                    case 'feature':
                        Product::whereIn('id', $productIds)->update(['is_featured' => true]);
                        break;
                    case 'unfeature':
                        Product::whereIn('id', $productIds)->update(['is_featured' => false]);
                        break;
                    case 'publish':
                        Product::whereIn('id', $productIds)->update(['status' => 'published']);
                        break;
                    case 'draft':
                        Product::whereIn('id', $productIds)->update(['status' => 'draft']);
                        break;
                    case 'delete':
                        $products = Product::whereIn('id', $productIds)->get();
                        foreach ($products as $product) {
                            // Delete images and files
                            foreach ($product->images as $image) {
                                if (Storage::disk('public')->exists($image->image_path)) {
                                    Storage::disk('public')->delete($image->image_path);
                                }
                            }
                            $product->delete();
                        }
                        break;
                }
            });

            $count = count($productIds);
            $actionText = str_replace(['_'], [' '], $action);

            return redirect()->back()
                ->with('success', "{$count} product(s) {$actionText}d successfully.");

        } catch (\Exception $e) {
            \Log::error('Product bulk action failed: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Bulk action failed. Please try again.');
        }
    }

    /**
     * Get product statistics.
     */
    public function getStatistics()
    {
        try {
            $stats = [
                'total' => Product::count(),
                'published' => Product::where('status', 'published')->count(),
                'draft' => Product::where('status', 'draft')->count(),
                'archived' => Product::where('status', 'archived')->count(),
                'active' => Product::where('is_active', true)->count(),
                'featured' => Product::where('is_featured', true)->count(),
                'in_stock' => Product::where('stock_status', 'in_stock')->count(),
                'out_of_stock' => Product::where('stock_status', 'out_of_stock')->count(),
                'low_stock' => Product::where('manage_stock', true)
                    ->where('stock_quantity', '<=', 5)
                    ->where('stock_quantity', '>', 0)
                    ->count(),
            ];

            // Recent products
            $stats['recent_products'] = Product::with(['category', 'service'])
                ->latest()
                ->limit(5)
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'category' => $product->category?->name,
                        'status' => $product->status,
                        'stock_status' => $product->stock_status,
                        'created_at' => $product->created_at->format('M j, Y'),
                    ];
                });

            // Top categories
            $stats['top_categories'] = ProductCategory::withCount('products')
                ->having('products_count', '>', 0)
                ->orderBy('products_count', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($category) {
                    return [
                        'name' => $category->name,
                        'count' => $category->products_count,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            \Log::error('Product statistics failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get product statistics.'
            ], 500);
        }
    }

    /**
     * Export products to CSV.
     */
    public function export(Request $request)
    {
        $filters = $request->only(['search', 'category', 'service', 'status', 'brand', 'stock_status']);

        $query = Product::with(['category', 'service'])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($filters['category'] ?? null, function ($query, $category) {
                $query->where('product_category_id', $category);
            })
            ->when($filters['service'] ?? null, function ($query, $service) {
                $query->where('service_id', $service);
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($filters['brand'] ?? null, function ($query, $brand) {
                $query->where('brand', $brand);
            })
            ->when($filters['stock_status'] ?? null, function ($query, $stockStatus) {
                $query->where('stock_status', $stockStatus);
            });

        $products = $query->orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="products-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID',
                'Name',
                'SKU',
                'Category',
                'Service',
                'Brand',
                'Model',
                'Price',
                'Sale Price',
                'Currency',
                'Price Type',
                'Stock Quantity',
                'Stock Status',
                'Weight',
                'Status',
                'Is Featured',
                'Is Active',
                'Created At'
            ]);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->name,
                    $product->sku,
                    $product->category?->name,
                    $product->service?->name,
                    $product->brand,
                    $product->model,
                    $product->price,
                    $product->sale_price,
                    $product->currency,
                    $product->price_type,
                    $product->stock_quantity,
                    $product->stock_status,
                    $product->weight,
                    $product->status,
                    $product->is_featured ? 'Yes' : 'No',
                    $product->is_active ? 'Yes' : 'No',
                    $product->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Search products (AJAX endpoint).
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:1|max:255',
            'category' => 'nullable|exists:product_categories,id',
            'service' => 'nullable|exists:services,id',
            'status' => 'nullable|in:draft,published,archived',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        try {
            $query = Product::with(['category', 'service'])
                ->where(function ($q) use ($request) {
                    $searchTerm = $request->input('query');
                    $q->where('name', 'like', "%{$searchTerm}%")
                        ->orWhere('sku', 'like', "%{$searchTerm}%")
                        ->orWhere('description', 'like', "%{$searchTerm}%")
                        ->orWhere('brand', 'like', "%{$searchTerm}%");
                });

            // Apply filters
            if ($request->filled('category')) {
                $query->where('product_category_id', $request->input('category'));
            }

            if ($request->filled('service')) {
                $query->where('service_id', $request->input('service'));
            }

            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }

            $limit = $request->input('limit', 10);
            $products = $query->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'products' => $products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'category' => $product->category?->name,
                        'service' => $product->service?->name,
                        'brand' => $product->brand,
                        'status' => $product->status,
                        'stock_status' => $product->stock_status,
                        'is_active' => $product->is_active,
                        'is_featured' => $product->is_featured,
                        'price' => $product->formatted_price,
                        'image_url' => $product->featured_image_url,
                        'edit_url' => route('admin.products.edit', $product),
                        'created_at' => $product->created_at->format('M j, Y'),
                    ];
                }),
                'total' => $products->count(),
            ]);

        } catch (\Exception $e) {
            \Log::error('Product search failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Search failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Upload images for existing product (Universal File Uploader).
     */
    public function uploadImages(Request $request, Product $product)
    {
        try {
            $request->validate([
                'product_images' => 'required|array|min:1|max:10',
                'product_images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'image_types' => 'nullable|array',
                'image_types.*' => 'string|in:gallery,featured',
            ]);

            $uploadedFiles = [];
            $files = $request->file('product_images');
            $imageTypes = $request->input('image_types', []);

            foreach ($files as $index => $file) {
                $imageType = $imageTypes[$index] ?? 'gallery';

                try {
                    $fileData = $this->processImageUpload($file, $product, $imageType);
                    $uploadedFiles[] = $fileData;

                    \Log::info("Product image uploaded successfully", [
                        'product_id' => $product->id,
                        'image_type' => $imageType,
                        'file_path' => $fileData['file_path']
                    ]);

                } catch (\Exception $e) {
                    \Log::error('Individual product image upload failed: ' . $e->getMessage(), [
                        'product_id' => $product->id,
                        'image_type' => $imageType,
                        'file_name' => $file->getClientOriginalName()
                    ]);
                }
            }

            if (empty($uploadedFiles)) {
                throw new \Exception('No images were uploaded successfully');
            }

            return response()->json([
                'success' => true,
                'message' => count($uploadedFiles) === 1
                    ? 'Image uploaded successfully!'
                    : count($uploadedFiles) . ' images uploaded successfully!',
                'files' => $uploadedFiles,
                'product' => $product->fresh()->load(['category', 'service'])
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Product image upload failed: ' . $e->getMessage(), [
                'product_id' => $product->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete product image.
     */
    public function deleteImage(Request $request, Product $product)
    {
        try {
            $request->validate([
                'image_id' => 'required',
            ]);

            $imageId = $request->input('image_id');

            // Handle ProductImage deletion
            if (is_numeric($imageId)) {
                $image = ProductImage::where('product_id', $product->id)
                    ->where('id', $imageId)
                    ->firstOrFail();

                // Delete physical file
                if (Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }

                // Delete database record
                $image->delete();

                $message = 'Product image deleted successfully!';
            } else {
                throw new \Exception('Invalid image ID');
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'product' => $product->fresh()->load(['category', 'service', 'images'])
            ]);

        } catch (\Exception $e) {
            \Log::error('Product image deletion failed: ' . $e->getMessage(), [
                'product_id' => $product->id,
                'image_id' => $request->input('image_id')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image. Please try again.'
            ], 500);
        }
    }
    protected function setFeaturedImage(Request $request, Product $product)
    {
        $imagePath = $request->input('image_path');
        $gallery = $product->gallery ?? [];

        // Check if image exists in gallery
        $imageIndex = array_search($imagePath, $gallery);
        if ($imageIndex === false) {
            return response()->json(['success' => false, 'message' => 'Image not found in gallery'], 404);
        }

        // Move current featured to gallery if exists
        if ($product->featured_image) {
            $gallery[] = $product->featured_image;
        }

        // Remove from gallery and set as featured
        unset($gallery[$imageIndex]);
        $gallery = array_values($gallery); // Reindex array

        $product->update([
            'featured_image' => $imagePath,
            'gallery' => $gallery
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Image set as featured successfully.'
        ]);
    }

    /**
     * Delete featured image.
     */
    protected function deleteFeaturedImage(Product $product)
    {
        if ($product->featured_image) {
            Storage::disk('public')->delete($product->featured_image);
            $product->update(['featured_image' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Featured image deleted successfully!',
                'product' => $product->fresh()->load(['category', 'service'])
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No featured image to delete'
        ], 404);
    }
    protected function convertToGallery(Request $request, Product $product)
    {
        $imagePath = $request->input('image_path');

        if ($product->featured_image && $product->featured_image === $imagePath) {
            $gallery = $product->gallery ?? [];
            $gallery[] = $imagePath;

            $product->update([
                'featured_image' => null,
                'gallery' => $gallery
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Featured image converted to gallery successfully.'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Featured image not found'], 404);
    }

    /**
     * Reorder gallery images
     */
    protected function reorderGallery(Request $request, Product $product)
    {
        $newOrder = $request->input('gallery_order');

        if (is_array($newOrder)) {
            $product->update(['gallery' => $newOrder]);

            return response()->json([
                'success' => true,
                'message' => 'Gallery order updated successfully.'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid gallery order'], 400);
    }


    /**
     * Process individual image upload for existing product.
     */
    protected function processImageUpload($file, Product $product, string $imageType)
    {
        // Generate unique filename
        $filename = $this->generateImageFilename($file->getClientOriginalName(), $imageType, $product->id, $file->getClientOriginalExtension());
        $directory = "products/{$product->id}";
        $filePath = $directory . '/' . $filename;

        // Process and store image
        $storedPath = $this->processAndStoreImage($file, $filePath);

        // SIMPLIFIED: Only create ProductImage records (no more featured_image field)
        $productImage = ProductImage::create([
            'product_id' => $product->id,
            'image_path' => $storedPath,
            'alt_text' => $file->getClientOriginalName(),
            'is_featured' => $imageType === 'featured', // Set as featured if specified
            'sort_order' => ProductImage::where('product_id', $product->id)->max('sort_order') + 1
        ]);

        return [
            'id' => $productImage->id,
            'name' => $imageType === 'featured' ? 'Featured Image' : 'Gallery Image',
            'file_name' => $filename,
            'file_path' => $storedPath,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'category' => $imageType,
            'url' => Storage::disk('public')->url($storedPath),
            'size' => $this->formatFileSize($file->getSize()),
            'type' => $imageType,
            'created_at' => now()->format('M j, Y H:i')
        ];
    }

    /**
     * Process and store image with optimization.
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

                \Log::info("Product image processed with Intervention Image", [
                    'file_path' => $filePath,
                    'original_size' => $file->getSize(),
                    'processed_size' => strlen($encoded)
                ]);

                return $filePath;
            }

            // Fallback to basic upload
            $storedPath = $file->storeAs(dirname($filePath), basename($filePath), 'public');

            \Log::info("Product image stored with basic upload", [
                'file_path' => $storedPath,
                'size' => $file->getSize()
            ]);

            return $storedPath;

        } catch (\Exception $e) {
            \Log::warning('Product image processing failed, using basic upload: ' . $e->getMessage());

            // Final fallback
            return $file->storeAs(dirname($filePath), basename($filePath), 'public');
        }
    }

    /**
     * Cleanup old temporary files.
     */
    public function cleanupTempFiles()
    {
        try {
            $tempFiles = session('temp_product_images', []);

            foreach ($tempFiles as $tempFile) {
                if (Storage::disk('public')->exists($tempFile['path'])) {
                    Storage::disk('public')->delete($tempFile['path']);
                }
            }

            session()->forget('temp_product_images');

            return response()->json([
                'success' => true,
                'message' => 'Temporary files cleaned up successfully.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Cleanup temp files failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to cleanup temporary files.'
            ], 500);
        }
    }

    /**
     * Duplicate product.
     */
    public function duplicate(Product $product)
    {
        try {
            DB::transaction(function () use ($product, &$newProduct) {
                // Create new product with copied data
                $newProduct = $product->replicate([
                    'featured_image',
                    'gallery'
                ]);

                $newProduct->name = $product->name . ' (Copy)';
                $newProduct->slug = null; // Will be auto-generated
                $newProduct->sku = null; // Will need to be set manually
                $newProduct->is_featured = false;
                $newProduct->sort_order = Product::max('sort_order') + 1;
                $newProduct->save();

                // Copy images if they exist
                if ($product->featured_image && Storage::disk('public')->exists($product->featured_image)) {
                    $newFeaturedImagePath = $this->copyProductImage($product->featured_image, $newProduct->id, 'featured');
                    if ($newFeaturedImagePath) {
                        $newProduct->update(['featured_image' => $newFeaturedImagePath]);
                    }
                }

                // Copy gallery images
                foreach ($product->images as $image) {
                    if (Storage::disk('public')->exists($image->image_path)) {
                        $newImagePath = $this->copyProductImage($image->image_path, $newProduct->id, 'gallery');
                        if ($newImagePath) {
                            ProductImage::create([
                                'product_id' => $newProduct->id,
                                'image_path' => $newImagePath,
                                'alt_text' => $image->alt_text,
                                'is_featured' => false,
                                'sort_order' => $image->sort_order
                            ]);
                        }
                    }
                }

                // Copy service relations
                foreach ($product->relatedServices as $service) {
                    $newProduct->relatedServices()->attach($service->id, [
                        'relation_type' => $service->pivot->relation_type
                    ]);
                }
            });

            return redirect()->route('admin.products.edit', $newProduct)
                ->with('success', 'Product duplicated successfully. You can now edit the copy.');

        } catch (\Exception $e) {
            \Log::error('Product duplication failed: ' . $e->getMessage(), [
                'original_product_id' => $product->id
            ]);

            return redirect()->back()
                ->with('error', 'Failed to duplicate product. Please try again.');
        }
    }

    /**
     * Copy product image to new product directory.
     */
    protected function copyProductImage(string $originalPath, int $newProductId, string $imageType)
    {
        try {
            $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
            $newFilename = "product_{$newProductId}_{$imageType}_" . now()->format('YmdHis') . "_" . Str::random(6) . ".{$extension}";
            $newPath = "products/{$newProductId}/{$newFilename}";

            // Ensure directory exists
            Storage::disk('public')->makeDirectory("products/{$newProductId}");

            // Copy the file
            if (Storage::disk('public')->copy($originalPath, $newPath)) {
                return $newPath;
            }

            return null;

        } catch (\Exception $e) {
            \Log::warning('Failed to copy product image: ' . $e->getMessage(), [
                'original_path' => $originalPath,
                'new_product_id' => $newProductId,
                'image_type' => $imageType
            ]);

            return null;
        }
    }
    protected function updateImageOrder(Request $request, Product $product)
    {
        $imageOrder = $request->input('image_order');

        if (!is_array($imageOrder)) {
            return response()->json(['success' => false, 'message' => 'Invalid image order data'], 400);
        }

        try {
            foreach ($imageOrder as $orderData) {
                if (isset($orderData['id']) && isset($orderData['sort_order'])) {
                    $product->images()
                        ->where('id', $orderData['id'])
                        ->update(['sort_order' => $orderData['sort_order']]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Image order updated successfully.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Update image order failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update image order'], 500);
        }
    }
    protected function handleImageActions(Request $request, Product $product)
    {
        $action = $request->input('action');

        try {
            switch ($action) {
                case 'toggle_featured_product_image':
                    return $this->toggleFeaturedProductImage($request, $product);

                case 'delete_product_image':
                    return $this->deleteProductImageById($request, $product);

                case 'update_image_alt_text':
                    return $this->updateImageAltText($request, $product);

                case 'delete_legacy_image':
                    return $this->deleteLegacyImage($request, $product);

                case 'update_image_order':  // ADD THIS CASE
                    return $this->updateImageOrder($request, $product);

                default:
                    return response()->json(['success' => false, 'message' => 'Unknown action'], 400);
            }
        } catch (\Exception $e) {
            \Log::error('Image action failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Action failed'], 500);
        }
    }
}