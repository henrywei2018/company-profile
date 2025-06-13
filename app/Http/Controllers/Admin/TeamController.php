<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Models\TeamMemberDepartment;
use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
{
    protected $fileUploadService;

    /**
     * Create a new controller instance.
     */
    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Display a listing of the team members.
     */
    public function index(Request $request)
    {
        $query = TeamMember::query()
            ->with(['department'])
            ->when($request->filled('search'), function ($query) use ($request) {
                return $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%")
                      ->orWhere('position', 'like', "%{$request->search}%")
                      ->orWhere('bio', 'like', "%{$request->search}%")
                      ->orWhereHas('department', function ($dq) use ($request) {
                          $dq->where('name', 'like', "%{$request->search}%");
                      });
                });
            })
            ->when($request->filled('department'), function ($query) use ($request) {
                return $query->whereHas('department', function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->department}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                return $query->where('is_active', $request->status === 'active' || $request->status === '1');
            })
            ->when($request->filled('featured'), function ($query) use ($request) {
                return $query->where('is_featured', $request->is_featured === '1');
            });

        $teamMembers = $query->ordered()->paginate(10)->withQueryString();

        // Get departments for filter
        $departments = TeamMemberDepartment::where('is_active', true)->orderBy('name')->get();

        return view('admin.team.index', compact('teamMembers', 'departments'));
    }

    /**
     * Show the form for creating a new team member.
     */
    public function create()
    {
        $departments = TeamMemberDepartment::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.team.create', compact('departments'));
    }

    /**
     * Store a newly created team member.
     */
    public function store(StoreTeamRequest $request)
    {
        try {
            DB::transaction(function () use ($request, &$teamMember) {
                // Create the team member
                $teamData = $request->validated();

                // Generate slug if not provided
                if (empty($teamData['slug'])) {
                    $teamData['slug'] = Str::slug($teamData['name']);
                }

                $teamMember = TeamMember::create($teamData);

                // Process temporary images
                $this->processTempImagesFromSession($teamMember);
            });

            // Handle SEO
            if ($request->filled('meta_title') || $request->filled('meta_description') || $request->filled('meta_keywords')) {
                $teamMember->updateSeo([
                    'title' => $request->meta_title,
                    'description' => $request->meta_description,
                    'keywords' => $request->meta_keywords,
                ]);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Team member created successfully!',
                    'team_member' => $teamMember->fresh()->load('department'),
                    'redirect' => route('admin.team.edit', $teamMember)
                ]);
            }

            return redirect()->route('admin.team.index')
                ->with('success', 'Team member created successfully!');

        } catch (\Exception $e) {
            \Log::error('Team member creation failed: ' . $e->getMessage(), [
                'validated_data' => $request->validated()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create team member: ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create team member. Please try again.');
        }
    }

    /**
     * Display the specified team member.
     */
    public function show(TeamMember $teamMember)
    {
        $teamMember->load(['seo', 'department']);

        return view('admin.team.show', compact('teamMember'));
    }

    /**
     * Show the form for editing the specified team member.
     */
    public function edit(TeamMember $teamMember)
    {
        $teamMember->load(['seo', 'department']);
        $departments = TeamMemberDepartment::where('is_active', true)->orderBy('name')->get();

        return view('admin.team.edit', compact('teamMember', 'departments'));
    }

    /**
     * Update the specified team member.
     */
    public function update(UpdateTeamRequest $request, TeamMember $teamMember)
    {
        try {
            DB::transaction(function () use ($request, $teamMember) {
                // Update team member
                $teamData = $request->validated();

                // Generate slug if not provided
                if (empty($teamData['slug'])) {
                    $teamData['slug'] = Str::slug($teamData['name']);
                }

                $teamMember->update($teamData);

                // Process temporary images
                $this->processTempImagesFromSession($teamMember);
            });

            // Handle SEO
            $teamMember->updateSeo([
                'title' => $request->meta_title,
                'description' => $request->meta_description,
                'keywords' => $request->meta_keywords,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Team member updated successfully!',
                    'team_member' => $teamMember->fresh()->load('department')
                ]);
            }

            return redirect()->route('admin.team.index')
                ->with('success', 'Team member updated successfully!');

        } catch (\Exception $e) {
            \Log::error('Team member update failed: ' . $e->getMessage(), [
                'team_member_id' => $teamMember->id,
                'validated_data' => $request->validated()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update team member: ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update team member. Please try again.');
        }
    }

    /**
     * Remove the specified team member.
     */
    public function destroy(TeamMember $teamMember)
    {
        try {
            // Delete photo
            if ($teamMember->photo) {
                Storage::disk('public')->delete($teamMember->photo);
            }

            // Delete team member
            $teamMember->delete();

            return redirect()->route('admin.team.index')
                ->with('success', 'Team member deleted successfully!');

        } catch (\Exception $e) {
            \Log::error('Team member deletion failed: ' . $e->getMessage(), [
                'team_member_id' => $teamMember->id
            ]);

            return redirect()->route('admin.team.index')
                ->with('error', 'Failed to delete team member. Please try again.');
        }
    }

    /**
     * Toggle active status
     */
    public function toggleActive(TeamMember $teamMember)
    {
        $teamMember->update([
            'is_active' => !$teamMember->is_active
        ]);

        return redirect()->back()
            ->with('success', 'Team member status updated!');
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(TeamMember $teamMember)
    {
        $teamMember->update([
            'featured' => !$teamMember->is_featured
        ]);

        return redirect()->back()
            ->with('success', 'Team member featured status updated!');
    }

    /**
     * Update sort order
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:team_members,id',
        ]);

        foreach ($request->order as $index => $id) {
            TeamMember::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }
  
    public function uploadTempImages(Request $request)
    {
        try {
            // Log the incoming request for debugging
            \Log::info('Team photo upload request received', [
                'files' => array_keys($request->allFiles()),
                'data' => $request->except(['files']),
                'content_type' => $request->header('Content-Type')
            ]);
            
            // Support multiple possible field names from Universal File Uploader
            $fieldName = null;
            $file = null;
            
            // Check different possible field names that Universal File Uploader might use
            $possibleFields = ['temp_photo', 'temp_images', 'team_photo', 'files'];
            
            foreach ($possibleFields as $field) {
                if ($request->hasFile($field)) {
                    $fieldName = $field;
                    $file = $request->file($field);
                    \Log::info('Found file in field: ' . $field);
                    break;
                }
            }
            
            if (!$file) {
                \Log::error('No file found in request', [
                    'expected_fields' => $possibleFields,
                    'actual_files' => array_keys($request->allFiles()),
                    'all_input' => $request->all()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'No file uploaded. Expected one of: ' . implode(', ', $possibleFields),
                    'debug' => [
                        'request_files' => array_keys($request->allFiles()),
                        'request_data' => $request->except(['files'])
                    ]
                ], 400);
            }
            
            // Validate the file
            $validator = \Validator::make($request->all(), [
                $fieldName => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB
                'category' => 'sometimes|string|in:photo'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed: ' . $validator->errors()->first()
                ], 422);
            }

            $category = $request->get('category', 'photo');
            
            // Consistent session key format similar to banner
            $sessionKey = 'team_temp_files_' . session()->getId();
            $sessionData = session()->get($sessionKey, []);

            // Generate unique temp identifier
            $tempId = 'temp_' . $category . '_' . uniqid() . '_' . time();
            $tempFilename = $tempId . '.' . $file->getClientOriginalExtension();
            $tempPath = $file->storeAs('temp/team', $tempFilename, 'public');

            // Enhanced temp file metadata
            $tempImageData = [
                'temp_id' => $tempId,
                'temp_path' => $tempPath,
                'original_name' => $file->getClientOriginalName(),
                'image_type' => $category,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_at' => now()->toISOString(),
                'session_id' => session()->getId()
            ];

            // Store in session with type as key for easy replacement
            $sessionData[$category] = $tempImageData;
            session()->put($sessionKey, $sessionData);

            $uploadedFile = [
                'id' => $tempId,
                'temp_id' => $tempId,
                'name' => ucfirst($category) . ' Photo',
                'file_name' => $file->getClientOriginalName(),
                'category' => $category,
                'type' => $category,
                'url' => Storage::disk('public')->url($tempPath),
                'size' => $this->formatFileSize($file->getSize()),
                'temp_path' => $tempPath,
                'is_temp' => true,
                'created_at' => now()->format('M j, Y H:i')
            ];

            \Log::info('Temp team photo uploaded', [
                'file' => $uploadedFile,
                'session_key' => $sessionKey
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Photo uploaded successfully!',
                'files' => [$uploadedFile]
            ]);

        } catch (\Exception $e) {
            \Log::error('Temporary team photo upload failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete temporary image - Following BannerController pattern
     */
    public function deleteTempImage(Request $request)
    {
        try {
            // Log the incoming request for debugging
            \Log::info('Delete temp team photo request received', [
                'method' => $request->method(),
                'content_type' => $request->header('Content-Type'),
                'raw_content' => $request->getContent(),
                'all_input' => $request->all(),
                'json_input' => $request->json()->all() ?? null
            ]);

            // Handle both JSON and form data
            $input = [];
            
            if ($request->isJson()) {
                $input = $request->json()->all();
            } else {
                $input = $request->all();
            }
            
            // Try to get temp_id from various sources
            $tempId = $input['temp_id'] ?? 
                      $input['id'] ?? 
                      $request->input('temp_id') ?? 
                      $request->input('id') ?? 
                      $request->getContent();
            
            // If content is JSON string, try to decode it
            if (empty($tempId) && $request->getContent()) {
                $rawContent = $request->getContent();
                if (is_string($rawContent)) {
                    $decoded = json_decode($rawContent, true);
                    if (is_array($decoded)) {
                        $tempId = $decoded['temp_id'] ?? $decoded['id'] ?? null;
                    } else {
                        // Might be just the temp_id as plain text
                        $tempId = trim($rawContent, '"');
                    }
                }
            }
            
            \Log::info('Extracted temp_id', ['temp_id' => $tempId]);

            if (empty($tempId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing temp file identifier',
                    'debug' => [
                        'input' => $input,
                        'raw_content' => $request->getContent(),
                        'method' => $request->method()
                    ]
                ], 400);
            }

            $sessionKey = 'team_temp_files_' . session()->getId();
            $sessionData = session()->get($sessionKey, []);

            \Log::info('Session data for temp team files', [
                'session_key' => $sessionKey,
                'session_data' => $sessionData
            ]);

            // Find the temp file by ID
            $tempFileData = null;
            $imageType = null;
            
            foreach ($sessionData as $type => $data) {
                if (isset($data['temp_id']) && $data['temp_id'] === $tempId) {
                    $tempFileData = $data;
                    $imageType = $type;
                    break;
                }
            }

            if (!$tempFileData) {
                \Log::warning('Temporary team photo not found', [
                    'temp_id' => $tempId,
                    'available_temp_ids' => array_map(function($data) {
                        return $data['temp_id'] ?? 'no_temp_id';
                    }, $sessionData)
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Temporary file not found',
                    'debug' => [
                        'temp_id' => $tempId,
                        'available_files' => array_keys($sessionData)
                    ]
                ], 404);
            }

            // Delete physical file
            if (Storage::disk('public')->exists($tempFileData['temp_path'])) {
                Storage::disk('public')->delete($tempFileData['temp_path']);
            }

            // Remove from session
            unset($sessionData[$imageType]);
            session()->put($sessionKey, $sessionData);

            \Log::info('Temporary team photo deleted successfully', [
                'temp_id' => $tempId,
                'image_type' => $imageType,
                'session_id' => session()->getId()
            ]);

            return response()->json([
                'success' => true,
                'message' => ucfirst($imageType) . ' photo deleted successfully!'
            ]);

        } catch (\Exception $e) {
            \Log::error('Temporary team photo deletion failed: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'raw_content' => $request->getContent(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete temporary photo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current temp files
     */
    public function getTempFiles(Request $request)
    {
        try {
            $sessionKey = 'team_temp_files_' . session()->getId();
            $sessionData = session()->get($sessionKey, []);
            
            $files = [];
            foreach ($sessionData as $imageType => $data) {
                // Verify file still exists
                if (Storage::disk('public')->exists($data['temp_path'])) {
                    $files[] = [
                        'id' => $data['temp_id'],
                        'name' => ucfirst($imageType) . ' Photo',
                        'file_name' => $data['original_name'],
                        'category' => $imageType,
                        'type' => $imageType,
                        'url' => Storage::disk('public')->url($data['temp_path']),
                        'size' => $this->formatFileSize($data['file_size']),
                        'temp_id' => $data['temp_id'],
                        'is_temp' => true,
                        'created_at' => \Carbon\Carbon::parse($data['uploaded_at'])->format('M j, Y H:i')
                    ];
                } else {
                    // Clean up broken reference
                    unset($sessionData[$imageType]);
                }
            }
            
            // Update session with cleaned data
            session()->put($sessionKey, $sessionData);

            return response()->json([
                'success' => true,
                'files' => $files
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to get temp team files: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get temporary files'
            ], 500);
        }
    }

    /**
     * Delete photo from existing team member
     */
    public function deletePhoto(TeamMember $teamMember)
    {
        try {
            if ($teamMember->photo) {
                Storage::disk('public')->delete($teamMember->photo);
                $teamMember->update(['photo' => null]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Photo deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete photo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk action handler
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string|in:activate,deactivate,feature,unfeature,delete',
            'team_member_ids' => 'required|array|min:1',
            'team_member_ids.*' => 'exists:team_members,id'
        ]);

        $teamMemberIds = $request->team_member_ids;
        $action = $request->action;

        switch ($action) {
            case 'activate':
                TeamMember::whereIn('id', $teamMemberIds)->update(['is_active' => true]);
                $message = 'Team members activated successfully!';
                break;

            case 'deactivate':
                TeamMember::whereIn('id', $teamMemberIds)->update(['is_active' => false]);
                $message = 'Team members deactivated successfully!';
                break;

            case 'feature':
                TeamMember::whereIn('id', $teamMemberIds)->update(['featured' => true]);
                $message = 'Team members featured successfully!';
                break;

            case 'unfeature':
                TeamMember::whereIn('id', $teamMemberIds)->update(['featured' => false]);
                $message = 'Team members unfeatured successfully!';
                break;

            case 'delete':
                $teamMembers = TeamMember::whereIn('id', $teamMemberIds)->get();
                foreach ($teamMembers as $teamMember) {
                    if ($teamMember->photo) {
                        Storage::disk('public')->delete($teamMember->photo);
                    }
                    $teamMember->delete();
                }
                $message = 'Team members deleted successfully!';
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Get statistics for modal
     */
    public function statistics()
    {
        try {
            $totalMembers = TeamMember::count();
            $activeMembers = TeamMember::where('is_active', true)->count();
            $featuredMembers = TeamMember::where('featured', true)->count();
            $inactiveMembers = TeamMember::where('is_active', false)->count();

            $recentMembers = TeamMember::latest()
                ->take(5)
                ->get()
                ->map(function ($member) {
                    return [
                        'title' => $member->name,
                        'category' => $member->department->name ?? 'No Department',
                        'status' => $member->is_active ? 'active' : 'inactive',
                        'created_at' => $member->created_at->format('M d, Y')
                    ];
                });

            $departmentStats = TeamMemberDepartment::withCount('teamMembers')
                ->orderBy('team_members_count', 'desc')
                ->take(5)
                ->get()
                ->map(function ($department) {
                    return [
                        'name' => $department->name,
                        'count' => $department->team_members_count
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'overview' => [
                        'total_members' => ['count' => $totalMembers, 'label' => 'Total Members'],
                        'active_members' => ['count' => $activeMembers, 'label' => 'Active'],
                        'featured_members' => ['count' => $featuredMembers, 'label' => 'Featured'],
                        'inactive_members' => ['count' => $inactiveMembers, 'label' => 'Inactive']
                    ],
                    'recent_items' => $recentMembers,
                    'popular_categories' => $departmentStats,
                    'additional_metrics' => [
                        'This Month' => TeamMember::whereMonth('created_at', now()->month)->count() . ' new members',
                        'Last Month' => TeamMember::whereMonth('created_at', now()->subMonth()->month)->count() . ' new members'
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load statistics'
            ], 500);
        }
    }

    /**
     * Export team members
     */
    public function export(Request $request)
    {
        // Implementation for exporting team members
        // This can be similar to banner export functionality
        return response()->json([
            'success' => true,
            'message' => 'Export functionality will be implemented'
        ]);
    }

    /**
     * Process temporary files and move them to permanent storage - Following BannerController pattern
     */
    protected function processTempImagesFromSession(TeamMember $teamMember)
    {
        $sessionKey = 'team_temp_files_' . session()->getId();
        $sessionData = session()->get($sessionKey, []);

        if (empty($sessionData)) {
            return;
        }

        foreach ($sessionData as $imageType => $tempImageData) {
            try {
                if (!Storage::disk('public')->exists($tempImageData['temp_path'])) {
                    \Log::warning('Temporary team photo not found during processing: ' . $tempImageData['temp_path']);
                    continue;
                }

                $this->moveTempImageToPermanent($tempImageData, $teamMember, $imageType);
                
            } catch (\Exception $e) {
                \Log::error('Failed to process temp team photo: ' . $e->getMessage(), [
                    'team_member_id' => $teamMember->id,
                    'image_type' => $imageType,
                    'temp_data' => $tempImageData
                ]);
            }
        }

        // Clear processed temporary images from session
        session()->forget($sessionKey);
        
        // Also cleanup any physical temp files for this session
        $this->cleanupSessionTempFiles(session()->getId());
    }

    /**
     * Move temporary image to permanent location - Following BannerController pattern
     */
    protected function moveTempImageToPermanent(array $tempImageData, TeamMember $teamMember, string $imageType)
    {
        try {
            $tempPath = $tempImageData['temp_path'];
            
            if (!Storage::disk('public')->exists($tempPath)) {
                throw new \Exception('Temporary file not found: ' . $tempPath);
            }

            // Generate permanent filename
            $extension = pathinfo($tempImageData['original_name'], PATHINFO_EXTENSION);
            $filename = $this->generateImageFilename(
                $tempImageData['original_name'], 
                $imageType, 
                $teamMember->id, 
                $extension
            );
            $directory = "team/{$teamMember->id}";
            $permanentPath = $directory . '/' . $filename;

            // Ensure directory exists
            Storage::disk('public')->makeDirectory($directory);

            // Move file from temp to permanent location
            if (Storage::disk('public')->move($tempPath, $permanentPath)) {
                // Update team member with new image path
                $this->assignImageToTeamMember($teamMember, $permanentPath, $imageType);
                
                \Log::info('Temporary team photo moved to permanent location', [
                    'team_member_id' => $teamMember->id,
                    'image_type' => $imageType,
                    'from' => $tempPath,
                    'to' => $permanentPath,
                    'original_name' => $tempImageData['original_name']
                ]);
                
                return $permanentPath;
            } else {
                throw new \Exception('Failed to move file from temp to permanent location');
            }

        } catch (\Exception $e) {
            \Log::error('Error moving temporary team photo: ' . $e->getMessage(), [
                'team_member_id' => $teamMember->id,
                'image_type' => $imageType,
                'temp_data' => $tempImageData
            ]);
            throw $e;
        }
    }

    /**
     * Generate image filename with extension parameter
     */
    protected function generateImageFilename($originalName, string $imageType, int $teamMemberId, string $extension = null)
    {
        if (!$extension) {
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        }
        
        $timestamp = now()->format('YmdHis');
        $random = Str::random(6);
        
        return "team_{$teamMemberId}_{$imageType}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Assign image to team member based on type
     */
    protected function assignImageToTeamMember(TeamMember $teamMember, string $imagePath, string $imageType)
    {
        // Delete old photo if exists
        if ($teamMember->photo) {
            Storage::disk('public')->delete($teamMember->photo);
        }
        
        $teamMember->update(['photo' => $imagePath]);
    }

    /**
     * Cleanup old temporary files (run via scheduler)
     */
    public function cleanupTempFiles()
    {
        try {
            $tempDir = 'temp/team';
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

            \Log::info("Cleaned up {$deletedCount} temporary team files");

            return response()->json([
                'success' => true,
                'message' => "Cleaned up {$deletedCount} temporary files",
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            \Log::error('Temporary team files cleanup failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Cleanup failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cleanup session temp files
     */
    protected function cleanupSessionTempFiles(string $sessionId)
    {
        try {
            $tempDir = 'temp/team';
            
            if (!Storage::disk('public')->exists($tempDir)) {
                return;
            }

            $files = Storage::disk('public')->files($tempDir);
            $deletedCount = 0;

            foreach ($files as $file) {
                // Check if file belongs to this session (contains session ID or is old)
                $filename = basename($file);
                if (str_contains($filename, $sessionId) || 
                    Storage::disk('public')->lastModified($file) < now()->subHours(1)->timestamp) {
                    
                    Storage::disk('public')->delete($file);
                    $deletedCount++;
                }
            }

            if ($deletedCount > 0) {
                \Log::info("Cleaned up {$deletedCount} session temporary team files for session: {$sessionId}");
            }

        } catch (\Exception $e) {
            \Log::warning('Failed to cleanup session temp team files: ' . $e->getMessage());
        }
    }

    /**
     * Format file size in human readable format
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
     * Get file type category
     */
    public function getFileCategory(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        return 'other';
    }

    /**
     * Generate unique filename ensuring no conflicts
     */
    public function generateUniqueFilename(string $originalName, string $directory, string $disk = 'public'): string
    {
        $pathInfo = pathinfo($originalName);
        $basename = Str::slug($pathInfo['filename'] ?? 'file');
        $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';
        
        $filename = $basename . $extension;
        $path = $directory . '/' . $filename;
        $counter = 1;

        // Keep trying until we find a unique filename
        while (Storage::disk($disk)->exists($path)) {
            $filename = $basename . '_' . $counter . $extension;
            $path = $directory . '/' . $filename;
            $counter++;
        }

        return $filename;
    }
}