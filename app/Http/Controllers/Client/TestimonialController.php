<?php
// File: app/Http/Controllers/Client/TestimonialController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Testimonial;
use App\Services\ClientAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class TestimonialController extends Controller
{
    protected ClientAccessService $clientAccessService;

    public function __construct(ClientAccessService $clientAccessService)
    {
        $this->clientAccessService = $clientAccessService;
    }

    /**
     * Display a listing of the client's testimonials.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Only get testimonials for the authenticated user
        $query = Testimonial::where('client_id', $user->id)
            ->with(['project']);

        // Search within user's own testimonials
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                    ->orWhereHas('project', function ($pq) use ($search) {
                        $pq->where('title', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sort by newest first by default
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $testimonials = $query->paginate(10)->withQueryString();

        // Get user's projects for dropdown
        $userProjects = Project::where('client_id', $user->id)
            ->select('id', 'title')
            ->get();

        // Stats for the authenticated user only
        $stats = [
            'total' => Testimonial::where('client_id', $user->id)->count(),
            'pending' => Testimonial::where('client_id', $user->id)->where('status', 'pending')->count(),
            'approved' => Testimonial::where('client_id', $user->id)->where('status', 'approved')->count(),
            'featured' => Testimonial::where('client_id', $user->id)->where('status', 'featured')->count(),
        ];

        return view('client.testimonials.index', compact('testimonials', 'userProjects', 'stats'));
    }

    /**
     * Show the form for creating a new testimonial
     */
    public function create()
    {
        $user = auth()->user();

        // Get only projects for the authenticated user
        $userProjects = Project::where('client_id', $user->id)
            ->select('id', 'title', 'status')
            ->get();

        return view('client.testimonials.create', compact('userProjects'));
    }

    /**
     * Store a newly created testimonial
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'content' => 'required|string|min:10|max:1000',
            'rating' => 'required|integer|min:1|max:5',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'temp_files' => 'nullable|array', // For universal uploader temp files
            'temp_files.*' => 'string', // Temp file IDs
        ]);

        // Verify project belongs to authenticated user if project_id is provided
        if ($validated['project_id']) {
            $project = Project::where('id', $validated['project_id'])
                ->where('client_id', $user->id)
                ->first();

            if (!$project) {
                return back()->withInput()
                    ->with('error', 'You can only create testimonials for your own projects.');
            }
        }

        DB::beginTransaction();
        try {
            // Force set client_id to authenticated user
            $validated['client_id'] = $user->id;
            $validated['client_name'] = $user->name;
            $validated['client_position'] = $user->position ?? '';
            $validated['client_company'] = $user->company ?? '';
            $validated['status'] = 'pending'; // New testimonials start as pending
            $validated['is_active'] = false; // Admin needs to approve
            $validated['submitted_at'] = now();

            // Remove temp_files from validated data before creating testimonial
            $tempFiles = $validated['temp_files'] ?? [];
            unset($validated['temp_files']);

            // Create testimonial
            $testimonial = Testimonial::create($validated);

            // Handle image upload - check both regular upload and temp files
            $imagePath = null;

            // First, check for regular file upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('testimonials', 'public');
            }
            // Then, check for temp files from universal uploader
            elseif (!empty($tempFiles)) {
                $imagePath = $this->processTempFiles($tempFiles, 'testimonials');
            }

            // Update testimonial with image path if we have one
            if ($imagePath) {
                $testimonial->update(['image' => $imagePath]);
            }

            DB::commit();

            return redirect()->route('client.testimonials.index')
                ->with('success', 'Testimonial submitted successfully! It will be reviewed by our team.');

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
        // Ensure the testimonial belongs to the authenticated user
        if ($testimonial->client_id !== auth()->id()) {
            abort(403, 'You can only view your own testimonials.');
        }

        $testimonial->load(['project']);

        return view('client.testimonials.show', compact('testimonial'));
    }

    /**
     * Show the form for editing the testimonial
     */
    public function edit(Testimonial $testimonial)
    {
        // Ensure the testimonial belongs to the authenticated user
        if ($testimonial->client_id !== auth()->id()) {
            abort(403, 'You can only edit your own testimonials.');
        }

        // Only allow editing if testimonial is pending or rejected
        if (!in_array($testimonial->status, ['pending', 'rejected'])) {
            return redirect()->route('client.testimonials.index')
                ->with('error', 'You can only edit testimonials that are pending or rejected.');
        }

        $user = auth()->user();
        $userProjects = Project::where('client_id', $user->id)
            ->select('id', 'title', 'status')
            ->get();

        return view('client.testimonials.edit', compact('testimonial', 'userProjects'));
    }

    /**
     * Update the specified testimonial
     */
    public function update(Request $request, Testimonial $testimonial)
    {
        // Ensure the testimonial belongs to the authenticated user
        if ($testimonial->client_id !== auth()->id()) {
            abort(403, 'You can only update your own testimonials.');
        }

        // Only allow editing if testimonial is pending or rejected
        if (!in_array($testimonial->status, ['pending', 'rejected'])) {
            return redirect()->route('client.testimonials.index')
                ->with('error', 'You can only edit testimonials that are pending or rejected.');
        }

        $user = auth()->user();

        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'content' => 'required|string|min:10|max:1000',
            'rating' => 'required|integer|min:1|max:5',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'temp_files' => 'nullable|array', // For universal uploader temp files
            'temp_files.*' => 'string', // Temp file IDs
        ]);

        // Verify project belongs to authenticated user if project_id is provided
        if ($validated['project_id']) {
            $project = Project::where('id', $validated['project_id'])
                ->where('client_id', $user->id)
                ->first();

            if (!$project) {
                return back()->withInput()
                    ->with('error', 'You can only link testimonials to your own projects.');
            }
        }

        DB::beginTransaction();
        try {
            // Reset status to pending when updated
            $validated['status'] = 'pending';
            $validated['submitted_at'] = now();

            // Remove temp_files from validated data before updating testimonial
            $tempFiles = $validated['temp_files'] ?? [];
            unset($validated['temp_files']);

            // Handle image upload - check both regular upload and temp files
            $imagePath = null;
            $shouldUpdateImage = false;

            // First, check for regular file upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($testimonial->image) {
                    Storage::disk('public')->delete($testimonial->image);
                }

                $imagePath = $request->file('image')->store('testimonials', 'public');
                $shouldUpdateImage = true;
            }
            // Then, check for temp files from universal uploader
            elseif (!empty($tempFiles)) {
                // Delete old image
                if ($testimonial->image) {
                    Storage::disk('public')->delete($testimonial->image);
                }

                $imagePath = $this->processTempFiles($tempFiles, 'testimonials');
                $shouldUpdateImage = true;
            }

            // Update testimonial
            $testimonial->update($validated);

            // Update image path if we have a new one
            if ($shouldUpdateImage && $imagePath) {
                $testimonial->update(['image' => $imagePath]);
            }

            DB::commit();

            return redirect()->route('client.testimonials.index')
                ->with('success', 'Testimonial updated successfully! It will be reviewed again.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error updating testimonial: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified testimonial
     */
    public function destroy(Testimonial $testimonial)
    {
        // Ensure the testimonial belongs to the authenticated user
        if ($testimonial->client_id !== auth()->id()) {
            abort(403, 'You can only delete your own testimonials.');
        }

        // Only allow deletion if testimonial is pending or rejected
        if (!in_array($testimonial->status, ['pending', 'rejected'])) {
            return redirect()->route('client.testimonials.index')
                ->with('error', 'You can only delete testimonials that are pending or rejected.');
        }

        try {
            // Delete image if exists
            if ($testimonial->image) {
                Storage::disk('public')->delete($testimonial->image);
            }

            $testimonial->delete();

            return redirect()->route('client.testimonials.index')
                ->with('success', 'Testimonial deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->route('client.testimonials.index')
                ->with('error', 'Error deleting testimonial: ' . $e->getMessage());
        }
    }

    public function getStats()
    {
        $user = auth()->user();

        $stats = [
            'total' => Testimonial::where('client_id', $user->id)->count(),
            'pending' => Testimonial::where('client_id', $user->id)->where('status', 'pending')->count(),
            'approved' => Testimonial::where('client_id', $user->id)->where('status', 'approved')->count(),
            'rejected' => Testimonial::where('client_id', $user->id)->where('status', 'rejected')->count(),
            'featured' => Testimonial::where('client_id', $user->id)->where('status', 'featured')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Process temporary files from universal uploader
     */
    private function processTempFiles(array $tempFileIds, string $destinationFolder = 'testimonials')
    {
        if (empty($tempFileIds)) {
            return null;
        }

        $tempFileId = $tempFileIds[0]; // Take first file for single mode
        $tempPath = storage_path("app/temp/uploads/{$tempFileId}");

        if (!File::exists($tempPath)) {
            throw new \Exception("Temporary file not found: {$tempFileId}");
        }

        // Generate new filename
        $extension = File::extension($tempPath);
        $newFilename = Str::uuid() . '.' . $extension;
        $destinationPath = "{$destinationFolder}/{$newFilename}";

        // Move file from temp to permanent storage
        $success = Storage::disk('public')->put(
            $destinationPath,
            File::get($tempPath)
        );

        if (!$success) {
            throw new \Exception("Failed to move temporary file to permanent storage");
        }

        // Clean up temp file
        File::delete($tempPath);

        return $destinationPath;
    }

    /**
     * Handle temporary file uploads for universal uploader
     */
    public function uploadTemp(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $file = $request->file('image');
            $tempId = Str::uuid();
            $tempPath = storage_path("app/temp/uploads/{$tempId}");

            // Ensure temp directory exists
            $tempDir = dirname($tempPath);
            if (!File::exists($tempDir)) {
                File::makeDirectory($tempDir, 0755, true);
            }

            // Move file to temp location
            $file->move($tempDir, $tempId);

            return response()->json([
                'success' => true,
                'temp_id' => $tempId,
                'filename' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'type' => $file->getMimeType(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete temporary files
     */
    public function deleteTemp(Request $request)
    {
        $request->validate([
            'temp_id' => 'required|string',
        ]);

        try {
            $tempPath = storage_path("app/temp/uploads/{$request->temp_id}");

            if (File::exists($tempPath)) {
                File::delete($tempPath);
            }

            return response()->json([
                'success' => true,
                'message' => 'Temporary file deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Delete failed: ' . $e->getMessage()
            ], 500);
        }
    }
}