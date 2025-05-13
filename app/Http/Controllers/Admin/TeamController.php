<?php
// File: app/Http/Controllers/Admin/TeamController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    /**
     * Display a listing of the team members.
     */
    public function index(Request $request)
    {
        $teamMembers = TeamMember::when($request->filled('search'), function ($query) use ($request) {
                return $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%")
                      ->orWhere('position', 'like', "%{$request->search}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                return $query->where('is_active', $request->status === 'active');
            })
            ->orderBy('sort_order')
            ->paginate(10);
        
        return view('admin.team.index', compact('teamMembers'));
    }

    /**
     * Show the form for creating a new team member.
     */
    public function create()
    {
        return view('admin.team.create');
    }

    /**
     * Store a newly created team member.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'social_linkedin' => 'nullable|url|max:255',
            'social_twitter' => 'nullable|url|max:255',
            'social_facebook' => 'nullable|url|max:255',
            'social_instagram' => 'nullable|url|max:255',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer|min:0',
            'photo' => 'nullable|image|max:1024',
        ]);
        
        // Get max sort order if not specified
        if (!$request->filled('sort_order')) {
            $validated['sort_order'] = TeamMember::max('sort_order') + 1;
        }
        
        // Create team member
        $teamMember = TeamMember::create($validated);
        
        // Handle photo upload
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('team', 'public');
            $teamMember->update(['photo' => $path]);
        }
        
        return redirect()->route('admin.team.index')
            ->with('success', 'Team member created successfully!');
    }

    /**
     * Display the specified team member.
     */
    public function show(TeamMember $teamMember)
    {
        return view('admin.team.show', compact('teamMember'));
    }

    /**
     * Show the form for editing the specified team member.
     */
    public function edit(TeamMember $teamMember)
    {
        return view('admin.team.edit', compact('teamMember'));
    }

    /**
     * Update the specified team member.
     */
    public function update(Request $request, TeamMember $teamMember)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'social_linkedin' => 'nullable|url|max:255',
            'social_twitter' => 'nullable|url|max:255',
            'social_facebook' => 'nullable|url|max:255',
            'social_instagram' => 'nullable|url|max:255',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer|min:0',
            'photo' => 'nullable|image|max:1024',
        ]);
        
        // Update team member
        $teamMember->update($validated);
        
        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($teamMember->photo) {
                Storage::disk('public')->delete($teamMember->photo);
            }
            
            // Store new photo
            $path = $request->file('photo')->store('team', 'public');
            $teamMember->update(['photo' => $path]);
        }
        
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
            'is_featured' => !$teamMember->is_featured
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
}