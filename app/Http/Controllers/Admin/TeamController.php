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
                return $query->where('featured', $request->is_featured === '1');
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
        // Create the team member
        $teamData = $request->validated();

        // Generate slug if not provided
        if (empty($teamData['slug'])) {
            $teamData['slug'] = Str::slug($teamData['name']);
        }

        $teamMember = TeamMember::create($teamData);

        // Handle photo upload from temp files
        $this->processTempFiles($teamMember);

        // Handle SEO
        if ($request->filled('meta_title') || $request->filled('meta_description') || $request->filled('meta_keywords')) {
            $teamMember->updateSeo([
                'title' => $request->meta_title,
                'description' => $request->meta_description,
                'keywords' => $request->meta_keywords,
            ]);
        }

        return redirect()->route('admin.team.index')
            ->with('success', 'Team member created successfully!');
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
        // Update team member
        $teamData = $request->validated();

        // Generate slug if not provided
        if (empty($teamData['slug'])) {
            $teamData['slug'] = Str::slug($teamData['name']);
        }

        $teamMember->update($teamData);

        // Handle photo upload from temp files
        $this->processTempFiles($teamMember);

        // Handle SEO
        $teamMember->updateSeo([
            'title' => $request->meta_title,
            'description' => $request->meta_description,
            'keywords' => $request->meta_keywords,
        ]);

        return redirect()->route('admin.team.index')
            ->with('success', 'Team member updated successfully!');
    }

    /**
     * Remove the specified team member.
     */
    public function destroy(TeamMember $teamMember)
    {
        // Delete photo
        if ($teamMember->photo) {
            Storage::disk('public')->delete($teamMember->photo);
        }

        // Delete team member
        $teamMember->delete();

        return redirect()->route('admin.team.index')
            ->with('success', 'Team member deleted successfully!');
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
            'featured' => !$teamMember->featured
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

    /**
     * Temporary image upload for photo
     */
    public function uploadTempImages(Request $request)
    {
        $request->validate([
            'temp_images' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB
            'category' => 'sometimes|string|in:photo'
        ]);

        try {
            $file = $request->file('temp_images');
            $category = $request->get('category', 'photo');
            
            // Store temp file
            $tempPath = $file->store('temp/team', 'public');
            
            // Store in session for later processing
            $sessionKey = 'team_temp_files';
            $tempFiles = session()->get($sessionKey, []);
            
            $tempFile = [
                'id' => uniqid(),
                'temp_id' => uniqid(),
                'path' => $tempPath,
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'type' => $file->getMimeType(),
                'category' => $category,
                'url' => Storage::url($tempPath),
                'is_temp' => true,
                'uploaded_at' => now()->toISOString()
            ];
            
            // Replace existing file for same category (single photo mode)
            $tempFiles = array_filter($tempFiles, function ($existing) use ($category) {
                return $existing['category'] !== $category;
            });
            
            $tempFiles[] = $tempFile;
            session()->put($sessionKey, $tempFiles);

            return response()->json([
                'success' => true,
                'message' => 'Photo uploaded successfully!',
                'files' => [$tempFile]
            ]);

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
        $request->validate([
            'temp_id' => 'sometimes|string',
            'id' => 'sometimes|string',
            'image_type' => 'sometimes|string'
        ]);

        try {
            $tempId = $request->get('temp_id') ?: $request->get('id');
            $imageType = $request->get('image_type', 'photo');
            
            $sessionKey = 'team_temp_files';
            $tempFiles = session()->get($sessionKey, []);
            
            $updatedFiles = [];
            $deletedFile = null;
            
            foreach ($tempFiles as $file) {
                if (($file['temp_id'] === $tempId || $file['id'] === $tempId) || 
                    ($file['category'] === $imageType)) {
                    $deletedFile = $file;
                    // Delete physical file
                    if (isset($file['path'])) {
                        Storage::disk('public')->delete($file['path']);
                    }
                } else {
                    $updatedFiles[] = $file;
                }
            }
            
            session()->put($sessionKey, $updatedFiles);

            return response()->json([
                'success' => true,
                'message' => 'Photo deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current temp files
     */
    public function getTempFiles()
    {
        try {
            $tempFiles = session()->get('team_temp_files', []);
            
            return response()->json([
                'success' => true,
                'files' => $tempFiles
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get temp files',
                'files' => []
            ]);
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
     * Process temporary files and move them to permanent storage
     */
    protected function processTempFiles(TeamMember $teamMember)
    {
        $sessionKey = 'team_temp_files';
        $tempFiles = session()->get($sessionKey, []);
        
        if (empty($tempFiles)) {
            return;
        }

        foreach ($tempFiles as $tempFile) {
            if ($tempFile['category'] === 'photo') {
                // Delete old photo if exists
                if ($teamMember->photo) {
                    Storage::disk('public')->delete($teamMember->photo);
                }

                // Move temp file to permanent location
                $newPath = $this->fileUploadService->uploadImage(
                    new \Illuminate\Http\File(Storage::disk('public')->path($tempFile['path'])),
                    'team/photos',
                    null,
                    600,
                    600
                );

                $teamMember->update(['photo' => $newPath]);

                // Delete temp file
                Storage::disk('public')->delete($tempFile['path']);
            }
        }

        // Clear session
        session()->forget($sessionKey);
    }
}