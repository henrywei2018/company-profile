<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMemberDepartment;
use App\Http\Requests\StoreTeamMemberDepartmentRequest;
use App\Http\Requests\UpdateTeamMemberDepartmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeamMemberDepartmentController extends Controller
{
    /**
     * Display a listing of the departments.
     */
    public function index()
    {
        $departments = TeamMemberDepartment::withCount('teamMembers')->orderBy('name')->paginate(10);
        
        // Get notification counts for header
        $unreadMessages = \App\Models\Message::unread()->count();
        $pendingQuotations = \App\Models\Quotation::pending()->count();
        
        return view('admin.team-member-departments.index', compact('departments', 'unreadMessages', 'pendingQuotations'));
    }

    /**
     * Show the form for creating a new department.
     */
    public function create()
    {
        // Get notification counts for header
        $unreadMessages = \App\Models\Message::unread()->count();
        $pendingQuotations = \App\Models\Quotation::pending()->count();
        
        return view('admin.team-member-departments.create', compact('unreadMessages', 'pendingQuotations'));
    }

    /**
     * Store a newly created department.
     */
    public function store(StoreTeamMemberDepartmentRequest $request)
    {
        $validated = $request->validated();
        
        // Generate slug from name
        $validated['slug'] = Str::slug($validated['name']);
        
        // Create department
        TeamMemberDepartment::create($validated);
        
        return redirect()->route('admin.team-member-departments.index')
            ->with('success', 'Department created successfully!');
    }

    /**
     * Show the form for editing the specified department.
     */
    public function edit(TeamMemberDepartment $teamMemberDepartment)
    {
        // Get notification counts for header
        $unreadMessages = \App\Models\Message::unread()->count();
        $pendingQuotations = \App\Models\Quotation::pending()->count();
        
        return view('admin.team-member-departments.edit', compact('teamMemberDepartment', 'unreadMessages', 'pendingQuotations'));
    }

    /**
     * Update the specified department.
     */
    public function update(UpdateTeamMemberDepartmentRequest $request, TeamMemberDepartment $teamMemberDepartment)
    {
        $validated = $request->validated();
        
        // Generate slug from name
        $validated['slug'] = Str::slug($validated['name']);
        
        // Update department
        $teamMemberDepartment->update($validated);
        
        return redirect()->route('admin.team-member-departments.index')
            ->with('success', 'Department updated successfully!');
    }

    /**
     * Remove the specified department.
     */
    public function destroy(TeamMemberDepartment $teamMemberDepartment)
    {
        // Check if department has team members
        if ($teamMemberDepartment->teamMembers()->count() > 0) {
            return redirect()->route('admin.team-member-departments.index')
                ->with('error', 'Cannot delete department with associated team members!');
        }
        
        // Delete department
        $teamMemberDepartment->delete();
        
        return redirect()->route('admin.team-member-departments.index')
            ->with('success', 'Department deleted successfully!');
    }
    
    /**
     * Update active status
     */
    public function toggleActive(TeamMemberDepartment $teamMemberDepartment)
    {
        $teamMemberDepartment->update([
            'is_active' => !$teamMemberDepartment->is_active
        ]);
        
        return redirect()->back()
            ->with('success', 'Department status updated!');
    }
}