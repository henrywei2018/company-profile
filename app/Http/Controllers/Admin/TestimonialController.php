<?php
// File: app/Http/Controllers/Admin/TestimonialController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;

class TestimonialController extends Controller
{
    /**
     * Display a listing of testimonials
     */
    public function index(Request $request)
    {
        $query = Testimonial::with(['project', 'client']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                    ->orWhere('client_company', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhereHas('project', function ($pq) use ($search) {
                        $pq->where('title', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'featured') {
                $query->featured();
            } else {
                $query->where('status', $request->status);
            }
        }

        // Project filter
        if ($request->filled('project_id')) {
            $query->forProject($request->project_id);
        }

        // Rating filter
        if ($request->filled('rating')) {
            $query->where('rating', '>=', $request->rating);
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $testimonials = $query->paginate(15)->withQueryString();

        // Statistics for dashboard
        $stats = [
            'total' => Testimonial::count(),
            'active' => Testimonial::active()->count(),
            'featured' => Testimonial::featured()->count(),
            'pending' => Testimonial::pending()->count(),
            'average_rating' => round(Testimonial::active()->avg('rating') ?? 0, 1),
        ];

        $projects = Project::select('id', 'title')->get();

        return view('admin.testimonials.index', compact('testimonials', 'stats', 'projects'));
    }

    /**
     * Show the form for creating a new testimonial
     */
    public function create()
    {
        // Get only users who have completed projects
        $clients = User::whereHas('projects', function($query) {
            $query->where('status', 'completed');
        })
        ->select('id', 'name', 'email', 'company', 'position')
        ->orderBy('name')
        ->get();
        
        // Get all completed projects for initial load
        $projects = Project::with('client')
            ->where('status', 'completed')
            ->select('id', 'title', 'client_id')
            ->orderBy('title')
            ->get();
        
        return view('admin.testimonials.create', compact('projects', 'clients'));
    }

    /**
     * Store a newly created testimonial
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'client_id' => 'nullable|exists:users,id',
            'client_name' => 'required|string|max:255',
            'client_position' => 'nullable|string|max:255',
            'client_company' => 'nullable|string|max:255',
            'content' => 'required|string|min:10',
            'rating' => 'required|integer|min:1|max:5',
            'is_active' => 'boolean',
            'featured' => 'boolean',
            'status' => 'required|in:pending,approved,rejected,featured',
            'admin_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Set default values
            $validated['is_active'] = $request->boolean('is_active', true);
            $validated['featured'] = $request->boolean('featured', false);

            // Auto-approve if status is approved/featured
            if (in_array($validated['status'], ['approved', 'featured'])) {
                $validated['approved_at'] = now();
            }

            // Handle temporary image uploads
            $sessionKey = 'temp_testimonial_images_' . session()->getId();
            $tempImages = session($sessionKey, []);
            
            // Create testimonial
            $testimonial = Testimonial::create($validated);

            // Process uploaded images
            if (!empty($tempImages)) {
                foreach ($tempImages as $tempImage) {
                    if (Storage::disk('public')->exists($tempImage['path'])) {
                        // Move from temp to permanent location
                        $permanentPath = 'testimonials/' . basename($tempImage['path']);
                        Storage::disk('public')->move($tempImage['path'], $permanentPath);
                        
                        // Update testimonial with image path
                        $testimonial->update(['image' => $permanentPath]);
                        break; // Only one image for testimonials
                    }
                }
                
                // Clear temp session
                session()->forget($sessionKey);
            }

            DB::commit();

            return redirect()->route('admin.testimonials.index')
                ->with('success', 'Testimonial created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error creating testimonial: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified testimonial
     */
    public function show(Testimonial $testimonial)
    {
        $testimonial->load(['project', 'client']);

        return view('admin.testimonials.show', compact('testimonial'));
    }

    /**
     * Show the form for editing the testimonial
     */
    public function edit(Testimonial $testimonial)
    {
        $projects = Project::select('id', 'title')->get();
        $clients = User::select('id', 'name', 'email', 'company')->get();

        return view('admin.testimonials.edit', compact('testimonial', 'projects', 'clients'));
    }

    /**
     * Update the specified testimonial
     */
    public function update(Request $request, Testimonial $testimonial)
    {
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'client_id' => 'nullable|exists:users,id',
            'client_name' => 'required|string|max:255',
            'client_position' => 'nullable|string|max:255',
            'client_company' => 'nullable|string|max:255',
            'content' => 'required|string|min:10',
            'rating' => 'required|integer|min:1|max:5',
            'is_active' => 'boolean',
            'featured' => 'boolean',
            'status' => 'required|in:pending,approved,rejected,featured',
            'admin_notes' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Fallback for traditional upload
        ]);

        DB::beginTransaction();
        try {
            $validated['is_active'] = $request->boolean('is_active');
            $validated['featured'] = $request->boolean('featured');

            // Handle status change to approved
            if ($validated['status'] === 'approved' && $testimonial->status !== 'approved') {
                $validated['approved_at'] = now();
            }

            // Handle traditional image upload (fallback)
            if ($request->hasFile('image')) {
                // Delete old image
                if ($testimonial->image) {
                    Storage::disk('public')->delete($testimonial->image);
                }

                $imagePath = $request->file('image')->store('testimonials/' . $testimonial->id, 'public');
                $validated['image'] = $imagePath;
            }

            // Remove image from validated data if not uploaded
            if (!$request->hasFile('image')) {
                unset($validated['image']);
            }

            $testimonial->update($validated);

            // Process temporary images from universal uploader (if any)
            $this->processTempImagesFromSession($testimonial);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Testimonial updated successfully!',
                    'testimonial' => $testimonial->fresh()
                ]);
            }

            return redirect()->route('admin.testimonials.index')
                ->with('success', 'Testimonial updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Testimonial update failed: ' . $e->getMessage(), [
                'testimonial_id' => $testimonial->id,
                'request_data' => $request->except(['image', 'testimonial_images'])
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating testimonial: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()
                ->with('error', 'Error updating testimonial: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified testimonial
     */
    public function destroy(Testimonial $testimonial)
    {
        try {
            // Delete image
            if ($testimonial->image) {
                Storage::disk('public')->delete($testimonial->image);
            }

            $testimonial->delete();

            return redirect()->route('admin.testimonials.index')
                ->with('success', 'Testimonial deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting testimonial: ' . $e->getMessage());
        }
    }

    /**
     * Toggle active status
     */
    public function toggleActive(Testimonial $testimonial)
    {
        $testimonial->update([
            'is_active' => !$testimonial->is_active
        ]);

        $status = $testimonial->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Testimonial {$status} successfully!");
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Testimonial $testimonial)
    {
        if ($testimonial->featured) {
            $testimonial->removeFeatured();
            $message = 'Testimonial removed from featured!';
        } else {
            $testimonial->setAsFeatured();
            $message = 'Testimonial set as featured!';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Approve testimonial
     */
    public function approve(Testimonial $testimonial)
    {
        $testimonial->approve();

        return redirect()->back()
            ->with('success', 'Testimonial approved successfully!');
    }

    /**
     * Reject testimonial
     */
    public function reject(Request $request, Testimonial $testimonial)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:500'
        ]);

        $testimonial->reject($request->rejection_reason);

        return redirect()->back()
            ->with('success', 'Testimonial rejected.');
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,feature,unfeature,approve,reject,delete',
            'testimonial_ids' => 'required|array',
            'testimonial_ids.*' => 'exists:testimonials,id'
        ]);

        $testimonials = Testimonial::whereIn('id', $request->testimonial_ids);
        $count = $testimonials->count();

        switch ($request->action) {
            case 'activate':
                $testimonials->update(['is_active' => true]);
                $message = "{$count} testimonials activated.";
                break;
            case 'deactivate':
                $testimonials->update(['is_active' => false]);
                $message = "{$count} testimonials deactivated.";
                break;
            case 'feature':
                $testimonials->update(['featured' => true, 'status' => 'featured']);
                $message = "{$count} testimonials featured.";
                break;
            case 'unfeature':
                $testimonials->update(['featured' => false]);
                $message = "{$count} testimonials unfeatured.";
                break;
            case 'approve':
                $testimonials->update([
                    'status' => 'approved',
                    'is_active' => true,
                    'approved_at' => now()
                ]);
                $message = "{$count} testimonials approved.";
                break;
            case 'reject':
                $testimonials->update(['status' => 'rejected', 'is_active' => false]);
                $message = "{$count} testimonials rejected.";
                break;
            case 'delete':
                // Delete images first
                $testimonials->get()->each(function ($testimonial) {
                    if ($testimonial->image) {
                        Storage::disk('public')->delete($testimonial->image);
                    }
                });
                $testimonials->delete();
                $message = "{$count} testimonials deleted.";
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Get statistics
     */
    public function statistics()
    {
        $stats = [
            'total' => Testimonial::count(),
            'active' => Testimonial::active()->count(),
            'featured' => Testimonial::featured()->count(),
            'pending' => Testimonial::pending()->count(),
            'approved' => Testimonial::approved()->count(),
            'average_rating' => round(Testimonial::active()->avg('rating') ?? 0, 1),
            'ratings_distribution' => Testimonial::active()
                ->selectRaw('rating, COUNT(*) as count')
                ->groupBy('rating')
                ->orderBy('rating', 'desc')
                ->pluck('count', 'rating')
                ->toArray(),
        ];

        return response()->json($stats);
    }
    public function uploadImages(Request $request, Testimonial $testimonial)
    {
        try {
            // Validation for single file upload (since testimonials only need one image)
            $request->validate([
                'testimonial_images' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Single file, 2MB max
                'category' => 'nullable|string|in:profile',
                'description' => 'nullable|string|max:255',
            ]);

            $file = $request->file('testimonial_images');

            try {
                $fileData = $this->processImageUpload($file, $testimonial);

                \Log::info("Testimonial image uploaded successfully", [
                    'testimonial_id' => $testimonial->id,
                    'file_path' => $fileData['file_path']
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Client photo uploaded successfully!',
                    'files' => [$fileData], // Wrap in array for universal uploader compatibility
                    'testimonial' => $testimonial->fresh()
                ]);

            } catch (\Exception $e) {
                \Log::error('Testimonial image upload failed: ' . $e->getMessage(), [
                    'testimonial_id' => $testimonial->id,
                    'file_name' => $file->getClientOriginalName()
                ]);

                throw new \Exception('Failed to upload image: ' . $e->getMessage());
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Testimonial image upload failed: ' . $e->getMessage(), [
                'testimonial_id' => $testimonial->id,
                'request_data' => $request->except(['testimonial_images'])
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
    public function deleteImage(Request $request, Testimonial $testimonial)
    {
        try {
            // Handle file ID deletion (for Universal File Uploader)
            $fileId = $request->input('file_id') ?? $request->getContent();

            if (str_starts_with($fileId, 'profile_') || $request->has('image_type')) {
                return $this->deleteTestimonialImage($testimonial);
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid image identifier'
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Testimonial image deletion failed: ' . $e->getMessage(), [
                'testimonial_id' => $testimonial->id,
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
    protected function processImageUpload($file, Testimonial $testimonial)
    {
        // Generate unique filename
        $filename = $this->generateImageFilename($file, $testimonial->id);
        $directory = "testimonials/{$testimonial->id}";
        $filePath = $directory . '/' . $filename;

        // Process and store image
        $storedPath = $this->processAndStoreImage($file, $filePath);

        // Update testimonial record
        $this->assignImageToTestimonial($testimonial, $storedPath);

        // Return file data for Universal File Uploader
        return [
            'id' => 'profile_' . $testimonial->id,
            'name' => 'Client Photo',
            'file_name' => $filename,
            'file_path' => $storedPath,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'category' => 'profile',
            'url' => Storage::disk('public')->url($storedPath),
            'download_url' => Storage::disk('public')->url($storedPath),
            'size' => $this->formatFileSize($file->getSize()),
            'type' => 'profile',
            'created_at' => now()->format('M j, Y H:i')
        ];
    }

    /**
     * Delete testimonial image
     */
    protected function deleteTestimonialImage(Testimonial $testimonial)
    {
        if ($testimonial->image) {
            Storage::disk('public')->delete($testimonial->image);
            $testimonial->update(['image' => null]);
            $message = 'Client photo deleted successfully!';
        } else {
            $message = 'No image to delete.';
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Generate unique filename for image
     */
    protected function generateImageFilename($file, $testimonialId)
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('Y-m-d_H-i-s');
        $randomString = Str::random(8);

        return "testimonial_{$testimonialId}_client_photo_{$timestamp}_{$randomString}.{$extension}";
    }

    /**
     * Process and store image with optimization
     */
    protected function processAndStoreImage($file, $filePath)
    {
        // Ensure directory exists
        $directory = dirname($filePath);
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        // Process image with Intervention Image for optimization
        if (class_exists('Intervention\Image\ImageManager')) {
            try {
                $manager = new ImageManager(['driver' => 'gd']);
                $image = $manager->make($file->getRealPath());

                // Resize if too large (max 800x800 for client photos)
                if ($image->width() > 800 || $image->height() > 800) {
                    $image->resize(800, 800, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }

                // Save optimized image
                $fullPath = storage_path('app/public/' . $filePath);
                $image->save($fullPath, 85); // 85% quality

                return $filePath;
            } catch (\Exception $e) {
                \Log::warning('Image optimization failed, using original: ' . $e->getMessage());
            }
        }

        // Fallback: store original file
        return $file->storeAs(dirname($filePath), basename($filePath), 'public');
    }

    /**
     * Assign image to testimonial
     */
    protected function assignImageToTestimonial(Testimonial $testimonial, string $imagePath)
    {
        // Delete old image if exists
        if ($testimonial->image) {
            Storage::disk('public')->delete($testimonial->image);
        }

        $testimonial->update(['image' => $imagePath]);
    }

    /**
     * Format file size for display
     */
    protected function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }

    /**
     * Temporary image upload handler for create form
     */
    public function uploadTempImages(Request $request)
    {
        try {
            // Validation for single file upload
            $request->validate([
                'testimonial_images' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);

            $file = $request->file('testimonial_images');
            $sessionKey = 'temp_testimonial_images_' . session()->getId();

            try {
                $fileData = $this->processTemporaryImageUpload($file);

                // Store temp file info in session
                session()->put($sessionKey, $fileData);

                \Log::info("Temporary testimonial image uploaded", [
                    'session_key' => $sessionKey,
                    'file_path' => $fileData['file_path']
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Image uploaded successfully!',
                    'files' => [$fileData] // Wrap in array for universal uploader compatibility
                ]);

            } catch (\Exception $e) {
                \Log::error('Temporary image upload failed: ' . $e->getMessage());
                throw new \Exception('Failed to upload temporary image: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
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
            $fileId = $request->input('file_id') ?? $request->getContent();
            $sessionKey = 'temp_testimonial_images_' . session()->getId();

            // Get temp file data from session
            $tempData = session()->get($sessionKey);

            if ($tempData && isset($tempData['temp_path'])) {
                Storage::disk('public')->delete($tempData['temp_path']);
                session()->forget($sessionKey);

                return response()->json([
                    'success' => true,
                    'message' => 'Temporary image deleted successfully!'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No temporary image found'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete temporary image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process temporary image upload
     */
    protected function processTemporaryImageUpload($file)
    {
        $filename = 'temp_testimonial_' . Str::random(10) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $tempPath = 'temp/testimonials/' . $filename;

        // Store in temp directory
        $storedPath = $file->storeAs('temp/testimonials', $filename, 'public');

        return [
            'id' => 'temp_' . time(),
            'name' => 'Client Photo',
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $storedPath,
            'temp_path' => $storedPath,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'category' => 'profile',
            'url' => Storage::disk('public')->url($storedPath),
            'download_url' => Storage::disk('public')->url($storedPath),
            'size' => $this->formatFileSize($file->getSize()),
            'type' => 'temp',
            'created_at' => now()->format('M j, Y H:i')
        ];
    }

    /**
     * Process temporary images when creating/updating testimonial
     */
    protected function processTempImagesFromSession(Testimonial $testimonial)
    {
        $sessionKey = 'temp_testimonial_images_' . session()->getId();
        $tempData = session()->get($sessionKey);

        if ($tempData && isset($tempData['temp_path'])) {
            try {
                // Move temp file to permanent location
                $this->moveTempImageToPermanent($tempData, $testimonial);

                // Clear session data
                session()->forget($sessionKey);

            } catch (\Exception $e) {
                \Log::error('Failed to process temporary image: ' . $e->getMessage());
            }
        }
    }

    /**
     * Move temporary image to permanent location
     */
    protected function moveTempImageToPermanent(array $tempData, Testimonial $testimonial)
    {
        if (!Storage::disk('public')->exists($tempData['temp_path'])) {
            throw new \Exception('Temporary file not found');
        }

        // Generate permanent filename and path
        $extension = pathinfo($tempData['file_name'], PATHINFO_EXTENSION);
        $filename = $this->generateImageFilenameFromExtension($extension, $testimonial->id);
        $directory = "testimonials/{$testimonial->id}";
        $permanentPath = $directory . '/' . $filename;

        // Ensure directory exists
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        // Move file from temp to permanent location
        Storage::disk('public')->move($tempData['temp_path'], $permanentPath);

        // Update testimonial with image path
        $testimonial->update(['image' => $permanentPath]);

        \Log::info('Moved temporary image to permanent location', [
            'testimonial_id' => $testimonial->id,
            'from' => $tempData['temp_path'],
            'to' => $permanentPath
        ]);
    }

    /**
     * Generate unique filename for image from extension
     */
    protected function generateImageFilenameFromExtension($extension, $testimonialId)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $randomString = Str::random(8);

        return "testimonial_{$testimonialId}_client_photo_{$timestamp}_{$randomString}.{$extension}";
    }

    public function cleanupTempFiles()
    {
        try {
            $tempDir = 'temp/testimonials';
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

            \Log::info("Cleaned up {$deletedCount} temporary testimonial files");

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
    public function getClientDetails(Request $request, $clientId)
    {
        try {
            $client = User::findOrFail($clientId);
            
            return response()->json([
                'success' => true,
                'client' => [
                    'id' => $client->id,
                    'name' => $client->name,
                    'email' => $client->email,
                    'company' => $client->company ?? '',
                    'position' => $client->position ?? '',
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Client not found'
            ], 404);
        }
    }
    public function getClientProjects(Request $request, $clientId)
    {
        try {
            $projects = Project::where('client_id', $clientId)
                ->where('status', 'completed')
                ->select('id', 'title', 'client_id', 'completed_at', 'description')
                ->orderBy('completed_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'projects' => $projects->map(function($project) {
                    return [
                        'id' => $project->id,
                        'title' => $project->title,
                        'client_id' => $project->client_id,
                        'completed_at' => $project->completed_at ? $project->completed_at->format('M Y') : '',
                        'description' => \Str::limit($project->description ?? '', 100)
                    ];
                }),
                'count' => $projects->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching client projects: ' . $e->getMessage()
            ], 500);
        }
    }
public function getClientsWithCompletedProjects(Request $request)
    {
        try {
            $search = $request->get('search', '');
            
            $clients = User::whereHas('projects', function($query) {
                $query->where('status', 'completed');
            })
            ->when($search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('company', 'like', "%{$search}%");
                });
            })
            ->withCount(['projects' => function($query) {
                $query->where('status', 'completed');
            }])
            ->select('id', 'name', 'email', 'company', 'position')
            ->orderBy('name')
            ->limit(50)
            ->get();

            return response()->json([
                'success' => true,
                'clients' => $clients->map(function($client) {
                    return [
                        'id' => $client->id,
                        'name' => $client->name,
                        'email' => $client->email,
                        'company' => $client->company ?? '',
                        'position' => $client->position ?? '',
                        'completed_projects_count' => $client->projects_count,
                        'display_text' => $client->name . ' (' . $client->email . ')' . 
                                        ($client->company ? ' - ' . $client->company : ''),
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching clients: ' . $e->getMessage()
            ], 500);
        }
    }
}