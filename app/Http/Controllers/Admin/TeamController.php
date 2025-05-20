<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
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
     *
     * @param FileUploadService $fileUploadService
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
            ->when($request->filled('search'), function ($query) use ($request) {
                return $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%")
                      ->orWhere('position', 'like', "%{$request->search}%")
                      ->orWhere('department', 'like', "%{$request->search}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                return $query->where('is_active', $request->status === 'active' || $request->status === '1');
            })
            ->when($request->filled('featured'), function ($query) use ($request) {
                return $query->where('featured', $request->featured === '1');
            });

        $teamMembers = $query->ordered()->paginate(10)->withQueryString();

        // Get unread messages and pending quotations counts for header notifications
        $unreadMessages = \App\Models\Message::unread()->count();
        $pendingQuotations = \App\Models\Quotation::pending()->count();

        return view('admin.team.index', compact('teamMembers', 'unreadMessages', 'pendingQuotations'));
    }

    /**
     * Show the form for creating a new team member.
     */
    public function create()
    {
        // Get unread messages and pending quotations counts for header notifications
        $unreadMessages = \App\Models\Message::unread()->count();
        $pendingQuotations = \App\Models\Quotation::pending()->count();

        return view('admin.team.create', compact('unreadMessages', 'pendingQuotations'));
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

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $path = $this->fileUploadService->uploadImage(
                $request->file('photo'),
                'team/photos',
                null,
                600,
                600
            );
            $teamMember->update(['photo' => $path]);
        }

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
        $teamMember->load('seo');

        // Get unread messages and pending quotations counts for header notifications
        $unreadMessages = \App\Models\Message::unread()->count();
        $pendingQuotations = \App\Models\Quotation::pending()->count();

        return view('admin.team.show', compact('teamMember', 'unreadMessages', 'pendingQuotations'));
    }

    /**
     * Show the form for editing the specified team member.
     */
    public function edit(TeamMember $teamMember)
    {
        $teamMember->load('seo');

        // Get unread messages and pending quotations counts for header notifications
        $unreadMessages = \App\Models\Message::unread()->count();
        $pendingQuotations = \App\Models\Quotation::pending()->count();

        return view('admin.team.edit', compact('teamMember', 'unreadMessages', 'pendingQuotations'));
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

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($teamMember->photo) {
                Storage::disk('public')->delete($teamMember->photo);
            }

            $path = $this->fileUploadService->uploadImage(
                $request->file('photo'),
                'team/photos',
                null,
                600,
                600
            );
            $teamMember->update(['photo' => $path]);
        }

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
            'order.*' => 'integer|exists:teams,id',
        ]);

        foreach ($request->order as $index => $id) {
            TeamMember::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }
}