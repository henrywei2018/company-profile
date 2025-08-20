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
            'search', 'category', 'service', 'status', 'brand', 'stock_status'
        ]);

        // Build query with filters
        $query = Product::with(['category', 'service', 'images' => function ($query) {
            $query->where('is_featured', true)->ordered()->limit(1);
        }]);

        // Apply filters
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('sku', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('brand', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('short_description', 'like', '%' . $filters['search'] . '%');
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
        $categories = ProductCategory::active()->orderBy('name')->get();
        $services = Service::active()->orderBy('title')->get();
        $brands = Product::whereNotNull('brand')->where('brand', '!=', '')->distinct()->orderBy('brand')->pluck('brand');

        return view('admin.products.index', compact('products', 'categories', 'services', 'brands'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = ProductCategory::active()->ordered()->get();
        $services = Service::active()->ordered()->get();

        return view('admin.products.create', compact('categories', 'services'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // Generate slug if not provided
            if (empty($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            // Ensure unique slug
            $baseSlug = $validated['slug'];
            $counter = 1;
            while (Product::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $baseSlug . '-' . $counter;
                $counter++;
            }

            // Create product
            $product = Product::create($validated);

            // Process temporary images from Universal File Uploader
            $this->processTemporaryImages($product);

            DB::commit();

            return redirect()
                ->route('admin.products.edit', $product)
                ->with('success', 'Product created successfully! You can now upload images and make additional changes.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Product creation failed: ' . $e->getMessage(), [
                'validated_data' => $validated
            ]);

            return back()
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
        $this->loadProductRelations($product);
        
        $categories = ProductCategory::active()->ordered()->get();
        $services = Service::active()->ordered()->get();

        return view('admin.products.edit', compact('product', 'categories', 'services'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        // Handle AJAX image management actions
        if ($request->has('action')) {
            return $this->handleImageAction($request, $product);
        }

        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // Handle slug generation
            if (empty($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            // Ensure unique slug (excluding current product)
            if ($validated['slug'] !== $product->slug) {
                $baseSlug = $validated['slug'];
                $counter = 1;
                while (Product::where('slug', $validated['slug'])->where('id', '!=', $product->id)->exists()) {
                    $validated['slug'] = $baseSlug . '-' . $counter;
                    $counter++;
                }
            }

            // Update product
            $product->update($validated);

            DB::commit();

            // Process images after main transaction completes successfully
            // This prevents file storage issues if transaction fails
            $this->processNewImages($request, $product);
            $this->processTemporaryImages($product);

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Product updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Product update failed: ' . $e->getMessage());

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
            DB::beginTransaction();

            // Delete associated images
            foreach ($product->images as $image) {
                $this->deleteProductImageFile($image);
                $image->delete();
            }

            // Delete product directory
            $productDir = 'products/' . $product->id;
            if (Storage::disk('public')->exists($productDir)) {
                Storage::disk('public')->deleteDirectory($productDir);
            }

            // Delete product
            $product->delete();

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Product deleted successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Product deletion failed: ' . $e->getMessage());

            return back()
                ->with('error', 'Failed to delete product. Please try again.');
        }
    }

    // =========================================================================
    // IMAGE MANAGEMENT METHODS (Universal File Uploader + ProductImage)
    // =========================================================================

    /**
     * Handle AJAX image management actions.
     */
    public function handleImageAction(Request $request, Product $product)
    {
        $action = $request->input('action');
        
        Log::info('Image action request received', [
            'action' => $action,
            'product_id' => $product->id,
            'request_data' => $request->all()
        ]);

        switch ($action) {
            case 'toggle_featured_product_image':
                return $this->toggleFeaturedProductImage($request, $product);
            
            case 'delete_product_image':
                return $this->deleteProductImageAction($request, $product);
            
            case 'update_image_alt_text':
                return $this->updateImageAltText($request, $product);
            
            case 'update_image_order':
                return $this->updateImageOrder($request, $product);

            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid action specified.'
                ], 400);
        }
    }

    /**
     * Upload temporary images for Universal File Uploader.
     */
    public function uploadTempImages(Request $request)
    {
        // Handle both single file and multiple files from universal uploader
        if ($request->hasFile('product_images')) {
            $files = $request->file('product_images');
            if (!is_array($files)) {
                $files = [$files];
            }
        } else {
            $request->validate([
                'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'category' => 'nullable|string|in:featured,gallery'
            ]);
            $files = [$request->file('file')];
        }

        $uploadedFiles = [];

        foreach ($files as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }

            // Validate individual file
            $validator = validator(['file' => $file], [
                'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file: ' . $validator->errors()->first()
                ], 422);
            }

            try {
                $category = $request->input('category', 'gallery');
            
                // Generate unique filename
                $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $tempPath = 'temp/products/' . $filename;

                // Store in temp directory
                $storedPath = $file->storeAs('public/temp/products', $filename);

                // Create temp file record for session
                $tempFile = [
                    'id' => Str::uuid(),
                    'name' => $file->getClientOriginalName(),
                    'path' => $tempPath,
                    'url' => Storage::url($tempPath),
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                    'category' => $category,
                    'uploaded_at' => now()->toISOString()
                ];

                // Store in session
                $tempFiles = session('temp_product_images', []);
                $tempFiles[] = $tempFile;
                session(['temp_product_images' => $tempFiles]);

                $uploadedFiles[] = $tempFile;

            } catch (\Exception $e) {
                Log::error('Temp image upload failed: ' . $e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload image: ' . $e->getMessage()
                ], 500);
            }
        }

        // Return success response for all uploaded files
        if (count($uploadedFiles) === 1) {
            return response()->json([
                'success' => true,
                'file' => $uploadedFiles[0],
                'message' => 'Image uploaded successfully'
            ]);
        } else {
            return response()->json([
                'success' => true,
                'files' => $uploadedFiles,
                'message' => count($uploadedFiles) . ' images uploaded successfully'
            ]);
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
            Log::error('Delete temp image failed: ' . $e->getMessage());

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
        
        return response()->json([
            'success' => true,
            'files' => $tempFiles
        ]);
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
            Log::error('Cleanup temp files failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to cleanup temporary files.'
            ], 500);
        }
    }

    // =========================================================================
    // PRODUCT IMAGE MANAGEMENT (ProductImage Relationship)
    // =========================================================================

    /**
     * Toggle featured status of ProductImage.
     */
    protected function toggleFeaturedProductImage(Request $request, Product $product)
    {
        Log::info('Toggling featured image', [
            'product_id' => $product->id,
            'image_id' => $request->input('image_id')
        ]);

        $request->validate([
            'image_id' => 'required|integer|exists:product_images,id'
        ]);

        try {
            DB::beginTransaction();

            $image = ProductImage::where('id', $request->image_id)
                                ->where('product_id', $product->id)
                                ->firstOrFail();

            $newFeaturedStatus = !$image->is_featured;

            // If setting as featured, unset other featured images first
            if ($newFeaturedStatus) {
                ProductImage::where('product_id', $product->id)
                          ->where('id', '!=', $image->id)
                          ->update(['is_featured' => false]);
            }

            $image->update(['is_featured' => $newFeaturedStatus]);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $newFeaturedStatus ? 'Image set as featured' : 'Featured status removed',
                'is_featured' => $newFeaturedStatus
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Toggle featured image failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update featured status.'
            ], 500);
        }
    }

    /**
     * Delete ProductImage.
     */
    protected function deleteProductImageAction(Request $request, Product $product)
    {
        Log::info('Deleting product image', [
            'product_id' => $product->id,
            'image_id' => $request->input('image_id')
        ]);

        $request->validate([
            'image_id' => 'required|integer|exists:product_images,id'
        ]);

        try {
            DB::beginTransaction();

            $image = ProductImage::where('id', $request->image_id)
                                ->where('product_id', $product->id)
                                ->firstOrFail();

            // Delete physical file
            $this->deleteProductImageFile($image);

            // Delete database record
            $image->delete();
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete product image failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image.'
            ], 500);
        }
    }

    /**
     * Update image alt text.
     */
    protected function updateImageAltText(Request $request, Product $product)
    {
        $request->validate([
            'image_id' => 'required|integer|exists:product_images,id',
            'alt_text' => 'nullable|string|max:255'
        ]);

        try {
            $image = ProductImage::where('id', $request->image_id)
                                ->where('product_id', $product->id)
                                ->firstOrFail();

            $image->update(['alt_text' => $request->alt_text]);

            return response()->json([
                'success' => true,
                'message' => 'Alt text updated successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Update image alt text failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update alt text.'
            ], 500);
        }
    }

    /**
     * Update image order.
     */
    protected function updateImageOrder(Request $request, Product $product)
    {
        $request->validate([
            'image_order' => 'required|array|min:1',
            'image_order.*.id' => 'required|integer|exists:product_images,id',
            'image_order.*.sort_order' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->image_order as $orderData) {
                ProductImage::where('id', $orderData['id'])
                          ->where('product_id', $product->id)
                          ->update(['sort_order' => $orderData['sort_order']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Image order updated successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Update image order failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update image order.'
            ], 500);
        }
    }

    // =========================================================================
    // BULK OPERATIONS & UTILITIES
    // =========================================================================

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
     * Duplicate product.
     */
    public function duplicate(Product $product)
    {
        try {
            DB::beginTransaction();

            // Create duplicate with modified name
            $duplicateData = $product->toArray();
            unset($duplicateData['id'], $duplicateData['created_at'], $duplicateData['updated_at']);
            
            $duplicateData['name'] = $duplicateData['name'] . ' (Copy)';
            $duplicateData['slug'] = Str::slug($duplicateData['name']) . '-' . time();
            $duplicateData['status'] = 'draft';

            $duplicate = Product::create($duplicateData);

            // Copy images
            foreach ($product->images as $image) {
                $newImagePath = $this->copyProductImage($image->image_path, $duplicate->id);
                
                if ($newImagePath) {
                    ProductImage::create([
                        'product_id' => $duplicate->id,
                        'image_path' => $newImagePath,
                        'alt_text' => $image->alt_text,
                        'sort_order' => $image->sort_order,
                        'is_featured' => $image->is_featured,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.products.edit', $duplicate)
                ->with('success', 'Product duplicated successfully. You can now edit the copy.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Product duplication failed: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to duplicate product. Please try again.');
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

        try {
            $productIds = $request->product_ids;
            $action = $request->action;
            $count = count($productIds);

            DB::beginTransaction();

            switch ($action) {
                case 'activate':
                    Product::whereIn('id', $productIds)->update(['is_active' => true]);
                    $message = "{$count} products activated successfully.";
                    break;

                case 'deactivate':
                    Product::whereIn('id', $productIds)->update(['is_active' => false]);
                    $message = "{$count} products deactivated successfully.";
                    break;

                case 'feature':
                    Product::whereIn('id', $productIds)->update(['is_featured' => true]);
                    $message = "{$count} products marked as featured successfully.";
                    break;

                case 'unfeature':
                    Product::whereIn('id', $productIds)->update(['is_featured' => false]);
                    $message = "{$count} products unfeatured successfully.";
                    break;

                case 'publish':
                    Product::whereIn('id', $productIds)->update(['status' => 'published']);
                    $message = "{$count} products published successfully.";
                    break;

                case 'draft':
                    Product::whereIn('id', $productIds)->update(['status' => 'draft']);
                    $message = "{$count} products set as draft successfully.";
                    break;

                case 'delete':
                    $products = Product::whereIn('id', $productIds)->get();
                    
                    foreach ($products as $product) {
                        // Delete images
                        foreach ($product->images as $image) {
                            $this->deleteProductImageFile($image);
                        }
                        
                        // Delete product directory
                        $productDir = 'products/' . $product->id;
                        if (Storage::disk('public')->exists($productDir)) {
                            Storage::disk('public')->deleteDirectory($productDir);
                        }
                    }
                    
                    Product::whereIn('id', $productIds)->delete();
                    $message = "{$count} products deleted successfully.";
                    break;
            }

            DB::commit();

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Bulk action failed: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Bulk action failed. Please try again.');
        }
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
                foreach ($request->product_ids as $index => $productId) {
                    Product::where('id', $productId)->update(['sort_order' => $index + 1]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Product order updated successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Product reorder failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder products.'
            ], 500);
        }
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    /**
     * Load product relationships for show/edit views.
     */
    protected function loadProductRelations(Product $product)
    {
        return $product->load([
            'category',
            'service',
            'images' => function ($query) {
                $query->ordered();
            },
            'relatedServices'
        ]);
    }

    /**
     * Process new images directly from form submission.
     */
    protected function processNewImages(Request $request, Product $product)
    {
        if (!$request->hasFile('product_images')) {
            Log::info('No product_images files found in request', [
                'product_id' => $product->id,
                'request_files' => array_keys($request->allFiles())
            ]);
            return;
        }

        $files = $request->file('product_images');
        if (!is_array($files)) {
            $files = [$files];
        }

        Log::info('Processing product images', [
            'product_id' => $product->id,
            'file_count' => count($files)
        ]);

        foreach ($files as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }

            try {
                // Validate file
                $validator = validator(['file' => $file], [
                    'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
                ]);

                if ($validator->fails()) {
                    Log::warning('Invalid file uploaded: ' . $validator->errors()->first());
                    continue;
                }

                // Generate filename and path
                $extension = $file->getClientOriginalExtension();
                $filename = 'product_' . $product->id . '_' . time() . '_' . Str::random(6) . '.' . $extension;
                $relativePath = 'products/' . $product->id . '/' . $filename;

                // Ensure directory exists
                $productDir = 'products/' . $product->id;
                Storage::disk('public')->makeDirectory($productDir);
                
                Log::info('Attempting to store file', [
                    'filename' => $filename,
                    'relative_path' => $relativePath,
                    'storage_path' => 'public/products/' . $product->id,
                    'file_size' => $file->getSize()
                ]);

                // Store file in public disk - try multiple methods
                $storedPath = null;
                
                // Method 1: Using storeAs with public disk
                try {
                    $storedPath = $file->storeAs('products/' . $product->id, $filename, 'public');
                    Log::info('Method 1 storeAs result', ['stored_path' => $storedPath]);
                } catch (\Exception $e) {
                    Log::error('Method 1 failed', ['error' => $e->getMessage()]);
                }
                
                // Method 2: Fallback using move
                if (!$storedPath) {
                    try {
                        $destinationPath = Storage::disk('public')->path('products/' . $product->id . '/' . $filename);
                        $directory = dirname($destinationPath);
                        if (!file_exists($directory)) {
                            mkdir($directory, 0755, true);
                        }
                        
                        if ($file->move($directory, $filename)) {
                            $storedPath = 'products/' . $product->id . '/' . $filename;
                            Log::info('Method 2 move result', ['stored_path' => $storedPath]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Method 2 failed', ['error' => $e->getMessage()]);
                    }
                }
                
                Log::info('File storage result', [
                    'stored_path' => $storedPath,
                    'success' => !empty($storedPath)
                ]);

                if ($storedPath) {
                    // Verify file actually exists using the stored path
                    $fileExists = Storage::disk('public')->exists($storedPath);
                    $fullPath = Storage::disk('public')->path($storedPath);
                    
                    Log::info('File verification', [
                        'stored_path' => $storedPath,
                        'relative_path' => $relativePath,
                        'full_path' => $fullPath,
                        'file_exists' => $fileExists,
                        'file_size_on_disk' => $fileExists ? filesize($fullPath) : null
                    ]);
                    
                    if ($fileExists) {
                        // Create ProductImage record
                        $maxSort = ProductImage::where('product_id', $product->id)->max('sort_order') ?? 0;
                        
                        ProductImage::create([
                            'product_id' => $product->id,
                            'image_path' => $storedPath, // Use the actual stored path
                            'alt_text' => $product->name,
                            'sort_order' => $maxSort + 1,
                            'is_featured' => false, // Can be set later via management interface
                        ]);

                        Log::info('New product image processed successfully', [
                            'product_id' => $product->id,
                            'image_path' => $storedPath,
                            'full_path' => $fullPath
                        ]);
                    } else {
                        Log::error('File was not stored properly', [
                            'stored_path' => $storedPath,
                            'expected_path' => $relativePath,
                            'full_path' => $fullPath
                        ]);
                    }
                } else {
                    Log::error('Failed to store file', [
                        'filename' => $filename,
                        'storage_path' => 'public/products/' . $product->id
                    ]);
                }

            } catch (\Exception $e) {
                Log::error('Failed to process new product image: ' . $e->getMessage(), [
                    'product_id' => $product->id,
                    'file_name' => $file->getClientOriginalName()
                ]);
            }
        }
    }

    /**
     * Process temporary images from Universal File Uploader.
     */
    protected function processTemporaryImages(Product $product)
    {
        $tempFiles = session('temp_product_images', []);

        if (empty($tempFiles)) {
            return;
        }

        foreach ($tempFiles as $tempFile) {
            try {
                if (!Storage::disk('public')->exists($tempFile['path'])) {
                    Log::warning('Temp file not found: ' . $tempFile['path']);
                    continue;
                }

                // Move from temp to permanent location
                $permanentPath = $this->moveToProductDirectory($tempFile, $product->id);
                
                if ($permanentPath) {
                    // Create ProductImage record
                    $maxSort = ProductImage::where('product_id', $product->id)->max('sort_order') ?? 0;
                    
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $permanentPath,
                        'alt_text' => $product->name,
                        'sort_order' => $maxSort + 1,
                        'is_featured' => $tempFile['category'] === 'featured' && 
                                       !ProductImage::where('product_id', $product->id)->where('is_featured', true)->exists(),
                    ]);

                    Log::info('Temp image processed successfully', [
                        'product_id' => $product->id,
                        'temp_path' => $tempFile['path'],
                        'permanent_path' => $permanentPath
                    ]);
                }

            } catch (\Exception $e) {
                Log::error('Failed to process temp image: ' . $e->getMessage(), [
                    'product_id' => $product->id,
                    'temp_file' => $tempFile
                ]);
            }
        }

        // Clear processed temp files
        session()->forget('temp_product_images');
        
        // Cleanup temp directory
        $this->cleanupTempDirectory();
    }

    /**
     * Move temp file to product directory.
     */
    protected function moveToProductDirectory(array $tempFile, int $productId): ?string
    {
        try {
            $extension = pathinfo($tempFile['path'], PATHINFO_EXTENSION);
            $filename = 'product_' . $productId . '_' . time() . '_' . Str::random(6) . '.' . $extension;
            $permanentPath = 'products/' . $productId . '/' . $filename;

            // Ensure product directory exists
            Storage::disk('public')->makeDirectory('products/' . $productId);

            // Move file
            if (Storage::disk('public')->move($tempFile['path'], $permanentPath)) {
                return $permanentPath;
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Failed to move temp file: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete ProductImage file from storage.
     */
    protected function deleteProductImageFile(ProductImage $image)
    {
        try {
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to delete image file: ' . $e->getMessage(), [
                'image_path' => $image->image_path
            ]);
        }
    }

    /**
     * Copy product image to new product directory.
     */
    protected function copyProductImage(string $originalPath, int $newProductId): ?string
    {
        try {
            $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
            $newFilename = 'product_' . $newProductId . '_' . time() . '_' . Str::random(6) . '.' . $extension;
            $newPath = 'products/' . $newProductId . '/' . $newFilename;

            // Ensure directory exists
            Storage::disk('public')->makeDirectory('products/' . $newProductId);

            // Copy the file
            if (Storage::disk('public')->copy($originalPath, $newPath)) {
                return $newPath;
            }

            return null;

        } catch (\Exception $e) {
            Log::warning('Failed to copy product image: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cleanup old temp directories.
     */
    protected function cleanupTempDirectory()
    {
        try {
            $tempDir = 'temp/products';
            
            if (Storage::disk('public')->exists($tempDir)) {
                $files = Storage::disk('public')->files($tempDir);
                
                foreach ($files as $file) {
                    $fileTime = Storage::disk('public')->lastModified($file);
                    
                    // Delete files older than 1 hour
                    if ($fileTime < now()->subHour()->timestamp) {
                        Storage::disk('public')->delete($file);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to cleanup temp directory: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // API & UTILITY ENDPOINTS
    // =========================================================================

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
                'active' => Product::where('is_active', true)->count(),
                'featured' => Product::where('is_featured', true)->count(),
                'with_images' => Product::whereHas('images')->count(),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Get product statistics failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics.'
            ], 500);
        }
    }

    /**
     * Export products.
     */
    public function export(Request $request)
    {
        try {
            // Implementation for export functionality
            return response()->json([
                'success' => true,
                'message' => 'Export functionality coming soon.'
            ]);

        } catch (\Exception $e) {
            Log::error('Product export failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to export products.'
            ], 500);
        }
    }

    /**
     * Search products (AJAX endpoint).
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2|max:100'
        ]);

        try {
            $query = $request->input('query');
            
            $products = Product::with(['category', 'service'])
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('sku', 'like', "%{$query}%")
                      ->orWhere('brand', 'like', "%{$query}%");
                })
                ->where('is_active', true)
                ->limit(10)
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'brand' => $product->brand,
                        'category' => $product->category?->name,
                        'service' => $product->service?->title,
                        'status' => $product->status,
                        'url' => route('admin.products.show', $product),
                        'edit_url' => route('admin.products.edit', $product),
                        'featured_image' => $product->featured_image_url,
                    ];
                });

            return response()->json([
                'success' => true,
                'products' => $products
            ]);

        } catch (\Exception $e) {
            Log::error('Product search failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Search failed. Please try again.'
            ], 500);
        }
    }
}