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

        // Base query - only get testimonials for the authenticated user
        $query = Testimonial::where('client_id', $user->id)
            ->with(['project:id,title,status', 'client:id,name,email']);

        // Enhanced search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                    ->orWhere('client_name', 'like', "%{$search}%")
                    ->orWhere('client_company', 'like', "%{$search}%")
                    ->orWhereHas('project', function ($pq) use ($search) {
                        $pq->where('title', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Rating filter
        if ($request->filled('rating')) {
            $query->where('rating', '>=', $request->rating);
        }

        // Project filter  
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting with client-friendly defaults
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        // Validate sort fields to prevent SQL injection
        $allowedSortFields = ['created_at', 'updated_at', 'rating', 'status', 'client_name'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'created_at';
        }

        $query->orderBy($sortField, $sortDirection);

        // Paginate results
        $testimonials = $query->paginate(12)->withQueryString();

        // Get user's projects for filters/dropdowns
        $userProjects = Project::where('client_id', $user->id)
            ->select('id', 'title', 'status')
            ->orderBy('title')
            ->get();

        // Enhanced stats for client dashboard
        $stats = $this->getClientTestimonialStats($user->id);

        // Recent activity
        $recentActivity = $this->getRecentTestimonialActivity($user->id);

        // Suggestions for improvement
        $suggestions = $this->getTestimonialSuggestions($user->id);

        return view('client.testimonials.index', compact(
            'testimonials',
            'userProjects',
            'stats',
            'recentActivity',
            'suggestions'
        ));
    }
    protected function getClientTestimonialStats($clientId)
    {
        $baseQuery = Testimonial::where('client_id', $clientId);

        return [
            'total' => $baseQuery->count(),
            'pending' => $baseQuery->where('status', 'pending')->count(),
            'approved' => $baseQuery->where('status', 'approved')->count(),
            'rejected' => $baseQuery->where('status', 'rejected')->count(),
            'featured' => $baseQuery->where('status', 'featured')->count(),
            'this_month' => $baseQuery->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'avg_rating' => round($baseQuery->avg('rating'), 1),
            'highest_rating' => $baseQuery->max('rating'),
            'total_with_projects' => $baseQuery->whereNotNull('project_id')->count(),
            'completion_rate' => $this->calculateCompletionRate($clientId),
            'response_time' => $this->calculateAverageResponseTime($clientId)
        ];
    }

    /**
     * Get recent testimonial activity
     */
    protected function getRecentTestimonialActivity($clientId)
    {
        return Testimonial::where('client_id', $clientId)
            ->with(['project:id,title'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($testimonial) {
                return [
                    'id' => $testimonial->id,
                    'action' => $this->getActivityAction($testimonial),
                    'description' => $this->getActivityDescription($testimonial),
                    'date' => $testimonial->updated_at,
                    'status' => $testimonial->status,
                    'project_title' => $testimonial->project->title ?? 'General Testimonial'
                ];
            });
    }

    /**
     * Get personalized suggestions for the client
     */
    protected function getTestimonialSuggestions($clientId)
    {
        $suggestions = [];

        // Check for completed projects without testimonials
        $projectsWithoutTestimonials = Project::where('client_id', $clientId)
            ->where('status', 'completed')
            ->whereDoesntHave('testimonials')
            ->count();

        if ($projectsWithoutTestimonials > 0) {
            $suggestions[] = [
                'type' => 'missing_testimonials',
                'title' => 'Projects Need Testimonials',
                'message' => "You have {$projectsWithoutTestimonials} completed projects that could benefit from testimonials.",
                'action' => 'Write testimonials for completed projects',
                'link' => route('client.testimonials.create'),
                'icon' => 'star'
            ];
        }

        // Check for rejected testimonials that can be revised
        $rejectedCount = Testimonial::where('client_id', $clientId)
            ->where('status', 'rejected')
            ->count();

        if ($rejectedCount > 0) {
            $suggestions[] = [
                'type' => 'rejected_testimonials',
                'title' => 'Revise Rejected Testimonials',
                'message' => "You have {$rejectedCount} testimonials that were rejected and can be improved.",
                'action' => 'Review and edit rejected testimonials',
                'link' => route('client.testimonials.index', ['status' => 'rejected']),
                'icon' => 'edit'
            ];
        }

        // Check testimonial quality suggestions
        $lowRatingCount = Testimonial::where('client_id', $clientId)
            ->where('rating', '<', 4)
            ->count();

        if ($lowRatingCount > 0) {
            $suggestions[] = [
                'type' => 'improve_ratings',
                'title' => 'Consider Higher Ratings',
                'message' => "Some of your testimonials have lower ratings. Consider if any experiences deserve higher ratings.",
                'action' => 'Review your ratings',
                'link' => route('client.testimonials.index'),
                'icon' => 'star'
            ];
        }

        return $suggestions;
    }

    /**
     * Calculate completion rate (approved/total ratio)
     */
    protected function calculateCompletionRate($clientId)
    {
        $total = Testimonial::where('client_id', $clientId)->count();
        if ($total === 0)
            return 0;

        $approved = Testimonial::where('client_id', $clientId)
            ->whereIn('status', ['approved', 'featured'])
            ->count();

        return round(($approved / $total) * 100, 1);
    }

    /**
     * Calculate average response time for testimonials
     */
    protected function calculateAverageResponseTime($clientId)
    {
        $testimonials = Testimonial::where('client_id', $clientId)
            ->whereNotNull('approved_at')
            ->whereColumn('approved_at', '>', 'created_at')
            ->get(['created_at', 'approved_at']);

        if ($testimonials->isEmpty())
            return null;

        $totalHours = $testimonials->sum(function ($testimonial) {
            return $testimonial->created_at->diffInHours($testimonial->approved_at);
        });

        $averageHours = $totalHours / $testimonials->count();

        if ($averageHours < 24) {
            return round($averageHours, 1) . ' hours';
        } else {
            return round($averageHours / 24, 1) . ' days';
        }
    }
    protected function getActivityAction($testimonial)
    {
        switch ($testimonial->status) {
            case 'pending':
                return 'Submitted';
            case 'approved':
                return 'Approved';
            case 'rejected':
                return 'Rejected';
            case 'featured':
                return 'Featured';
            default:
                return 'Updated';
        }
    }

    /**
     * Get activity description
     */
    protected function getActivityDescription($testimonial)
    {
        $base = "Testimonial for " . ($testimonial->project->title ?? 'general services');

        switch ($testimonial->status) {
            case 'pending':
                return $base . " is under review";
            case 'approved':
                return $base . " has been approved and is now live";
            case 'rejected':
                return $base . " needs revision";
            case 'featured':
                return $base . " has been featured on the website";
            default:
                return $base . " was updated";
        }
    }
    private function optimizeImage($imagePath)
    {
        try {
            if (extension_loaded('gd')) {
                $imageInfo = getimagesize($imagePath);
                if (!$imageInfo)
                    return;

                $width = $imageInfo[0];
                $height = $imageInfo[1];
                $type = $imageInfo[2];

                // Only resize if image is larger than 800px
                if ($width > 800 || $height > 800) {
                    $maxDimension = 800;
                    $ratio = min($maxDimension / $width, $maxDimension / $height);
                    $newWidth = (int) ($width * $ratio);
                    $newHeight = (int) ($height * $ratio);

                    // Create new image resource
                    $newImage = imagecreatetruecolor($newWidth, $newHeight);

                    // Load original image
                    switch ($type) {
                        case IMAGETYPE_JPEG:
                            $source = imagecreatefromjpeg($imagePath);
                            break;
                        case IMAGETYPE_PNG:
                            $source = imagecreatefrompng($imagePath);
                            imagealphablending($newImage, false);
                            imagesavealpha($newImage, true);
                            break;
                        case IMAGETYPE_GIF:
                            $source = imagecreatefromgif($imagePath);
                            break;
                        default:
                            return; // Unsupported format
                    }

                    // Resize
                    imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                    // Save optimized image
                    switch ($type) {
                        case IMAGETYPE_JPEG:
                            imagejpeg($newImage, $imagePath, 85);
                            break;
                        case IMAGETYPE_PNG:
                            imagepng($newImage, $imagePath, 8);
                            break;
                        case IMAGETYPE_GIF:
                            imagegif($newImage, $imagePath);
                            break;
                    }

                    // Clean up memory
                    imagedestroy($newImage);
                    imagedestroy($source);
                }
            }
        } catch (\Exception $e) {
            // Optimization failed, but don't throw error - just log it
            \Log::warning('Image optimization failed', [
                'error' => $e->getMessage(),
                'path' => $imagePath
            ]);
        }
    }

    /**
     * Show the form for creating a new testimonial
     */
    public function create()
    {
        $user = auth()->user();

        // Get projects available for testimonials (completed projects preferred)
        $userProjects = Project::where('client_id', $user->id)
            ->select('id', 'title', 'status')
            ->orderByRaw("CASE 
                WHEN status = 'completed' THEN 1 
                WHEN status = 'in_progress' THEN 2 
                ELSE 3 END")
            ->orderBy('title')
            ->get();

        // Check if user has eligible projects
        $hasEligibleProjects = $userProjects->whereIn('status', ['completed', 'in_progress'])->count() > 0;

        // Get suggestions for what to write about
        $writingSuggestions = $this->getWritingSuggestions($user->id);

        return view('client.testimonials.create', compact(
            'userProjects',
            'hasEligibleProjects',
            'writingSuggestions'
        ));
    }
    protected function getWritingSuggestions($clientId)
    {
        return [
            'What specific results did you achieve?',
            'How did our team exceed your expectations?',
            'What was unique about your experience?',
            'Would you recommend our services to others?',
            'How did we help solve your business challenges?',
            'What impressed you most about our communication?',
            'How was the project delivery and timeline?'
        ];
    }

    /**
     * Store a newly created testimonial
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'project_id' => [
                'nullable',
                'exists:projects,id',
                function ($attribute, $value, $fail) use ($user) {
                    if ($value && !Project::where('id', $value)->where('client_id', $user->id)->exists()) {
                        $fail('You can only link testimonials to your own projects.');
                    }
                }
            ],
            'content' => 'required|string|min:50|max:1500', // Increased minimum for quality
            'rating' => 'required|integer|min:1|max:5',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'temp_files' => 'nullable|array',
            'temp_files.*' => 'string',
        ]);

        DB::beginTransaction();
        try {
            // Auto-populate client info from authenticated user
            $testimonialData = array_merge($validated, [
                'client_id' => $user->id,
                'client_name' => $user->name,
                'client_position' => $user->position ?? null,
                'client_company' => $user->company ?? null,
                'status' => 'pending', // All client submissions start as pending
            ]);

            // Handle image upload using universal uploader
            if ($request->hasFile('image')) {
                $testimonialData['image'] = $this->handleImageUpload($request->file('image'));
            } elseif ($request->filled('temp_files')) {
                $testimonialData['image'] = $this->processTempFiles($request->temp_files);
            } else {
                // Check for universal uploader temp images in session
                $sessionKey = 'temp_testimonial_images_' . session()->getId();
                $tempImages = session($sessionKey, []);

                if (!empty($tempImages)) {
                    $testimonialData['image'] = $this->processUniversalUploaderFiles($tempImages);
                    // Clear the session after processing
                    session()->forget($sessionKey);
                }
            }

            $testimonial = Testimonial::create($testimonialData);

            DB::commit();

            return redirect()->route('client.testimonials.index')
                ->with('success', 'Testimonial submitted successfully! It will be reviewed by our team within 24-48 hours.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Client testimonial creation failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

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
            'project_id' => [
                'nullable',
                'exists:projects,id',
                function ($attribute, $value, $fail) use ($user) {
                    if ($value && !Project::where('id', $value)->where('client_id', $user->id)->exists()) {
                        $fail('You can only link testimonials to your own projects.');
                    }
                }
            ],
            'content' => 'required|string|min:50|max:1500',
            'rating' => 'required|integer|min:1|max:5',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'temp_files' => 'nullable|array',
            'temp_files.*' => 'string',
        ]);

        DB::beginTransaction();
        try {
            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($testimonial->image) {
                    Storage::disk('public')->delete($testimonial->image);
                }
                $validated['image'] = $this->handleImageUpload($request->file('image'));
            } elseif ($request->filled('temp_files')) {
                // Delete old image if exists
                if ($testimonial->image) {
                    Storage::disk('public')->delete($testimonial->image);
                }
                $validated['image'] = $this->processTempFiles($request->temp_files);
            } else {
                // Check for universal uploader temp images in session
                $sessionKey = 'temp_testimonial_images_' . session()->getId();
                $tempImages = session($sessionKey, []);

                if (!empty($tempImages)) {
                    // Delete old image if exists
                    if ($testimonial->image) {
                        Storage::disk('public')->delete($testimonial->image);
                    }
                    $validated['image'] = $this->processUniversalUploaderFiles($tempImages);
                    // Clear the session after processing
                    session()->forget($sessionKey);
                }
            }

            // Reset status to pending when updated
            $validated['status'] = 'pending';

            $testimonial->update($validated);

            DB::commit();

            return redirect()->route('client.testimonials.index')
                ->with('success', 'Testimonial updated successfully! It will be reviewed again by our team.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Client testimonial update failed', [
                'user_id' => $user->id,
                'testimonial_id' => $testimonial->id,
                'error' => $e->getMessage()
            ]);

            return back()->withInput()
                ->with('error', 'Error updating testimonial: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified testimonial
     */
    public function destroy(Testimonial $testimonial)
    {
        if ($testimonial->client_id !== auth()->id()) {
            abort(403, 'You can only delete your own testimonials.');
        }
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
        return response()->json($this->getClientTestimonialStats($user->id));
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
        $extension = File::extension($tempPath) ?: 'jpg';
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
    public function uploadTempImages(Request $request)
    {
        try {
            // Validation for single file upload (matching universal uploader expectations)
            $request->validate([
                'testimonial_images' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);

            $file = $request->file('testimonial_images');
            $sessionKey = 'temp_testimonial_images_' . session()->getId();

            try {
                $fileData = $this->processTemporaryImageUpload($file);

                // Store temp file info in session (universal uploader compatible format)
                session()->put($sessionKey, $fileData);

                \Log::info("Client testimonial temp image uploaded", [
                    'session_key' => $sessionKey,
                    'file_path' => $fileData['file_path'],
                    'user_id' => auth()->id()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Client photo uploaded successfully!',
                    'files' => [$fileData] // Wrap in array for universal uploader compatibility
                ]);

            } catch (\Exception $e) {
                \Log::error('Client testimonial temp image upload failed: ' . $e->getMessage());
                throw new \Exception('Failed to upload client photo: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete temporary files
     */
    public function deleteTempImage(Request $request)
    {
        try {
            $sessionKey = 'temp_testimonial_images_' . session()->getId();

            // Get temp file data from session
            $tempData = session()->get($sessionKey);

            if ($tempData && isset($tempData['temp_path'])) {
                Storage::disk('public')->delete($tempData['temp_path']);
                session()->forget($sessionKey);

                \Log::info("Client testimonial temp image deleted", [
                    'session_key' => $sessionKey,
                    'user_id' => auth()->id()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Client photo removed successfully!'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No client photo found'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Client testimonial temp image delete failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete client photo: ' . $e->getMessage()
            ], 500);
        }
    }
    protected function processTemporaryImageUpload($file)
    {
        $filename = 'temp_client_testimonial_' . Str::random(10) . '_' . time() . '.' . $file->getClientOriginalExtension();
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
    protected function processUniversalUploaderFiles($tempImages)
    {
        if (empty($tempImages) || !isset($tempImages['temp_path'])) {
            return null;
        }

        $tempPath = $tempImages['temp_path'];

        if (!Storage::disk('public')->exists($tempPath)) {
            throw new \Exception("Temporary file not found: {$tempPath}");
        }

        // Generate new filename for permanent storage
        $extension = pathinfo($tempImages['file_name'], PATHINFO_EXTENSION);
        $newFilename = Str::uuid() . '.' . $extension;
        $permanentPath = 'testimonials/' . $newFilename;

        // Move from temp to permanent location
        $success = Storage::disk('public')->move($tempPath, $permanentPath);

        if (!$success) {
            throw new \Exception("Failed to move temporary file to permanent storage");
        }

        return $permanentPath;
    }
    private function handleImageUpload($file)
    {
        try {
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('testimonials', $filename, 'public');

            // Optional: Optimize image
            if (extension_loaded('gd') || extension_loaded('imagick')) {
                $this->optimizeImage(storage_path('app/public/' . $path));
            }

            return $path;

        } catch (\Exception $e) {
            \Log::error('Client testimonial image upload failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            throw new \Exception('Image upload failed: ' . $e->getMessage());
        }
    }
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
}