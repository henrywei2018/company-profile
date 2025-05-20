<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMemberDepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Requests\StoreTeamMemberDepartmentRequest;
use App\Http\Requests\UpdateTeamMemberDepartmentRequest;

class TeamMemberDepartmentController extends Controller
{
    /**
     * Display a listing of the team member departments.
     */
    public function index(Request $request)
    {
        $query = TeamMemberDepartment::query()
            ->withCount('teamMembers')
            ->when($request->filled('search'), function ($query) use ($request) {
                return $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%")
                      ->orWhere('description', 'like', "%{$request->search}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                return $query->where('is_active', $request->status === 'active' || $request->status === '1');
            });

        $departments = $query->ordered()->paginate(10)->withQueryString();

        // Get unread messages and pending quotations counts for header notifications
        $unreadMessages = \App\Models\Message::unread()->count();
        $pendingQuotations = \App\Models\Quotation::pending()->count();

        return view('admin.team-departments.index', compact('departments', 'unreadMessages', 'pendingQuotations'));
    }

    /**
     * Show the form for creating a new department.
     */
    public function create()
    {
        // Get unread messages and pending quotations counts for header notifications
        $unreadMessages = \App\Models\Message::unread()->count();
        $pendingQuotations = \App\Models\Quotation::pending()->count();

        return view('admin.team-departments.create', compact('unreadMessages', 'pendingQuotations'));
    }

    /**
     * Store a newly created department.
     */
    public function store(StoreTeamMemberDepartmentRequest $request)
    {
        $departmentData = $request->validated();

        // Generate slug if not provided
        if (empty($departmentData['slug'])) {
            $departmentData['slug'] = Str::slug($departmentData['name']);
        }

        TeamMemberDepartment::create($departmentData);

        return redirect()->route('admin.team-departments.index')
            ->with('success', 'Department created successfully!');
    }

    /**
     * Display the specified department.
     */
    public function show(TeamMemberDepartment $teamMemberDepartment)
    {
        $teamMemberDepartment->load('teamMembers');

        // For AJAX requests, return HTML for the modal
        if(request()->ajax()) {
            $html = view('admin.team-departments.partials.department-details', compact('teamMemberDepartment'))->render();
            return response()->json(['html' => $html]);
        }

        return view('admin.team-departments.show', compact('teamMemberDepartment'));
    }

    // Update the parameter name to match the route parameter
    public function edit(TeamMemberDepartment $teamMemberDepartment)
    {
        return view('admin.team-departments.edit', compact('teamMemberDepartment'));
    }

    // Update the parameter name to match the route parameter
    public function update(UpdateTeamMemberDepartmentRequest $request, TeamMemberDepartment $teamMemberDepartment)
    {
        $departmentData = $request->validated();

        // Generate slug if not provided
        if (empty($departmentData['slug'])) {
            $departmentData['slug'] = Str::slug($departmentData['name']);
        }

        $teamMemberDepartment->update($departmentData);

        return redirect()->route('admin.team-departments.index')
            ->with('success', 'Department updated successfully!');
    }

    // Update the parameter name to match the route parameter
    public function destroy(TeamMemberDepartment $teamMemberDepartment)
    {
        // Check if department has team members
        if ($teamMemberDepartment->teamMembers()->count() > 0) {
            return redirect()->route('admin.team-departments.index')
                ->with('error', 'Cannot delete department with associated team members!');
        }

        // Delete department
        $teamMemberDepartment->delete();

        return redirect()->route('admin.team-departments.index')
            ->with('success', 'Department deleted successfully!');
    }

    // Update the parameter name to match the route parameter
    public function toggleActive(TeamMemberDepartment $teamMemberDepartment)
    {
        $teamMemberDepartment->update([
            'is_active' => !$teamMemberDepartment->is_active
        ]);

        return redirect()->back()
            ->with('success', 'Department status updated!');
    }

    /**
     * Update sort order
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:team_member_departments,id',
        ]);

        foreach ($request->order as $index => $id) {
            TeamMemberDepartment::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }
}