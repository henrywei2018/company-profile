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
                'client_position' => $user->position,
                'client_company' => $user->company,
                'status' => 'pending', // All client submissions start as pending
            ]);

            // Handle image upload if present
            if ($request->hasFile('image')) {
                $testimonialData['image'] = $this->handleImageUpload($request->file('image'));
            } elseif ($request->filled('temp_files')) {
                $testimonialData['image'] = $this->processTempFiles($request->temp_files);
            }

            $testimonial = Testimonial::create($testimonialData);

            DB::commit();

            return redirect()->route('client.testimonials.index')
                ->with('success', 'Testimonial submitted successfully! It will be reviewed by our team within 24-48 hours.');

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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
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

            // Log for debugging
            \Log::info("Client testimonial temp upload successful", [
                'temp_id' => $tempId,
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'temp_id' => $tempId,
                'filename' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'type' => $file->getMimeType(),
                'url' => url("storage/temp/uploads/{$tempId}"), // For preview if needed
            ]);

        } catch (\Exception $e) {
            \Log::error('Client testimonial temp upload failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

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

                \Log::info("Client testimonial temp file deleted", [
                    'temp_id' => $request->temp_id,
                    'user_id' => auth()->id()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Temporary file deleted successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Client testimonial temp delete failed', [
                'error' => $e->getMessage(),
                'temp_id' => $request->temp_id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Delete failed: ' . $e->getMessage()
            ], 500);
        }
    }
}