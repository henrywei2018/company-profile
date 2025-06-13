<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMemberDepartment;
use App\Http\Requests\StoreTeamMemberDepartmentRequest;
use App\Http\Requests\UpdateTeamMemberDepartmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TeamMemberDepartmentController extends Controller
{
    /**
     * Display a listing of the departments.
     */
    public function index(Request $request)
    {
        $query = TeamMemberDepartment::withCount('teamMembers')
            ->when($request->filled('search'), function ($query) use ($request) {
                return $query->where(function ($q) use ($request) {
                    $searchTerm = $request->search;
                    $q->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                return $query->where('is_active', $request->status === 'active' || $request->status === '1');
            });

        $departments = $query->orderBy('sort_order')->orderBy('name')->paginate(15)->withQueryString();

        // Get notification counts for header (following your pattern)
        $unreadMessages = \App\Models\Message::unread()->count();
        $pendingQuotations = \App\Models\Quotation::pending()->count();

        return view('admin.team-member-departments.index', compact(
            'departments', 
            'unreadMessages', 
            'pendingQuotations'
        ));
    }

    /**
     * Show the form for creating a new department.
     */
    public function create()
    {
        // Get notification counts for header
        $unreadMessages = \App\Models\Message::unread()->count();
        $pendingQuotations = \App\Models\Quotation::pending()->count();

        return view('admin.team-member-departments.create', compact(
            'unreadMessages', 
            'pendingQuotations'
        ));
    }

    /**
     * Store a newly created department.
     */
    public function store(StoreTeamMemberDepartmentRequest $request)
    {
        try {
            $validated = $request->validated();

            // Generate slug from name if not provided
            if (empty($validated['slug'])) {
                $validated['slug'] = $this->generateUniqueSlug($validated['name']);
            }

            // Set sort order if not provided
            if (empty($validated['sort_order'])) {
                $validated['sort_order'] = TeamMemberDepartment::max('sort_order') + 1;
            }

            $department = TeamMemberDepartment::create($validated);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Department created successfully!',
                    'department' => $department->fresh()->load('teamMembers')
                ]);
            }

            return redirect()->route('admin.team-member-departments.index')
                ->with('success', 'Department created successfully!');

        } catch (\Exception $e) {
            \Log::error('Department creation failed: ' . $e->getMessage(), [
                'validated_data' => $validated ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create department: ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create department. Please try again.');
        }
    }

    /**
     * Display the specified department.
     */
    public function show(TeamMemberDepartment $teamMemberDepartment)
    {
        $teamMemberDepartment->load(['teamMembers' => function ($query) {
            $query->orderBy('sort_order')->orderBy('name');
        }]);

        // Get notification counts for header
        $unreadMessages = \App\Models\Message::unread()->count();
        $pendingQuotations = \App\Models\Quotation::pending()->count();

        // Get department statistics
        $stats = [
            'total_members' => $teamMemberDepartment->teamMembers->count(),
            'active_members' => $teamMemberDepartment->teamMembers->where('is_active', true)->count(),
            'featured_members' => $teamMemberDepartment->teamMembers->where('featured', true)->count(),
            'recent_additions' => $teamMemberDepartment->teamMembers()
                ->where('created_at', '>=', now()->subDays(30))
                ->count()
        ];

        return view('admin.team-member-departments.show', compact(
            'teamMemberDepartment',
            'stats',
            'unreadMessages',
            'pendingQuotations'
        ));
    }

    /**
     * Show the form for editing the specified department.
     */
    public function edit(TeamMemberDepartment $teamMemberDepartment)
    {
        // Get notification counts for header
        $unreadMessages = \App\Models\Message::unread()->count();
        $pendingQuotations = \App\Models\Quotation::pending()->count();

        return view('admin.team-member-departments.edit', compact(
            'teamMemberDepartment',
            'unreadMessages',
            'pendingQuotations'
        ));
    }

    /**
     * Update the specified department.
     */
    public function update(UpdateTeamMemberDepartmentRequest $request, TeamMemberDepartment $teamMemberDepartment)
    {
        try {
            $validated = $request->validated();

            // Generate slug from name if not provided or if name changed
            if (empty($validated['slug']) || $teamMemberDepartment->name !== $validated['name']) {
                $validated['slug'] = $this->generateUniqueSlug($validated['name'], $teamMemberDepartment->id);
            }

            $teamMemberDepartment->update($validated);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Department updated successfully!',
                    'department' => $teamMemberDepartment->fresh()->load('teamMembers')
                ]);
            }

            return redirect()->route('admin.team-member-departments.index')
                ->with('success', 'Department updated successfully!');

        } catch (\Exception $e) {
            \Log::error('Department update failed: ' . $e->getMessage(), [
                'department_id' => $teamMemberDepartment->id,
                'validated_data' => $validated ?? null
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update department: ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update department. Please try again.');
        }
    }

    /**
     * Remove the specified department.
     */
    public function destroy(TeamMemberDepartment $teamMemberDepartment)
    {
        try {
            // Check if department has team members
            if ($teamMemberDepartment->teamMembers()->count() > 0) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete department with associated team members!'
                    ], 422);
                }

                return redirect()->route('admin.team-member-departments.index')
                    ->with('error', 'Cannot delete department with associated team members!');
            }

            $departmentName = $teamMemberDepartment->name;
            $teamMemberDepartment->delete();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Department '{$departmentName}' deleted successfully!"
                ]);
            }

            return redirect()->route('admin.team-member-departments.index')
                ->with('success', "Department '{$departmentName}' deleted successfully!");

        } catch (\Exception $e) {
            \Log::error('Department deletion failed: ' . $e->getMessage(), [
                'department_id' => $teamMemberDepartment->id
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete department: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('admin.team-member-departments.index')
                ->with('error', 'Failed to delete department. Please try again.');
        }
    }

    /**
     * Toggle active status
     */
    public function toggleActive(TeamMemberDepartment $teamMemberDepartment)
    {
        try {
            $newStatus = !$teamMemberDepartment->is_active;
            $teamMemberDepartment->update(['is_active' => $newStatus]);

            $message = $newStatus ? 'Department activated successfully!' : 'Department deactivated successfully!';

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'is_active' => $newStatus
                ]);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Department status toggle failed: ' . $e->getMessage(), [
                'department_id' => $teamMemberDepartment->id
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update department status'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to update department status');
        }
    }

    /**
     * Update sort order of departments
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:team_member_departments,id'
        ]);

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->order as $index => $departmentId) {
                    TeamMemberDepartment::where('id', $departmentId)
                        ->update(['sort_order' => $index + 1]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Department order updated successfully!'
            ]);

        } catch (\Exception $e) {
            \Log::error('Department order update failed: ' . $e->getMessage(), [
                'order' => $request->order
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update department order'
            ], 500);
        }
    }

    /**
     * Bulk action handler
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string|in:activate,deactivate,delete',
            'department_ids' => 'required|array|min:1',
            'department_ids.*' => 'exists:team_member_departments,id'
        ]);

        try {
            $departmentIds = $request->department_ids;
            $action = $request->action;
            $affectedCount = 0;

            switch ($action) {
                case 'activate':
                    $affectedCount = TeamMemberDepartment::whereIn('id', $departmentIds)
                        ->update(['is_active' => true]);
                    $message = "{$affectedCount} department(s) activated successfully!";
                    break;

                case 'deactivate':
                    $affectedCount = TeamMemberDepartment::whereIn('id', $departmentIds)
                        ->update(['is_active' => false]);
                    $message = "{$affectedCount} department(s) deactivated successfully!";
                    break;

                case 'delete':
                    // Check for departments with team members
                    $departmentsWithMembers = TeamMemberDepartment::whereIn('id', $departmentIds)
                        ->whereHas('teamMembers')
                        ->pluck('name')
                        ->toArray();

                    if (!empty($departmentsWithMembers)) {
                        $departmentsList = implode(', ', $departmentsWithMembers);
                        return redirect()->back()->with('error', 
                            "Cannot delete departments with team members: {$departmentsList}");
                    }

                    $affectedCount = TeamMemberDepartment::whereIn('id', $departmentIds)->delete();
                    $message = "{$affectedCount} department(s) deleted successfully!";
                    break;

                default:
                    return redirect()->back()->with('error', 'Invalid action selected.');
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Department bulk action failed: ' . $e->getMessage(), [
                'action' => $request->action,
                'department_ids' => $request->department_ids
            ]);

            return redirect()->back()->with('error', 'Bulk action failed. Please try again.');
        }
    }

    /**
     * Get department statistics
     */
    public function statistics()
    {
        try {
            $totalDepartments = TeamMemberDepartment::count();
            $activeDepartments = TeamMemberDepartment::where('is_active', true)->count();
            $inactiveDepartments = TeamMemberDepartment::where('is_active', false)->count();

            // Recent departments
            $recentDepartments = TeamMemberDepartment::latest()
                ->take(5)
                ->get()
                ->map(function ($dept) {
                    return [
                        'title' => $dept->name,
                        'category' => $dept->team_members_count . ' members',
                        'status' => $dept->is_active ? 'active' : 'inactive',
                        'created_at' => $dept->created_at->format('M d, Y')
                    ];
                });

            // Departments by member count
            $departmentsByMembers = TeamMemberDepartment::withCount('teamMembers')
                ->orderBy('team_members_count', 'desc')
                ->take(5)
                ->get()
                ->map(function ($dept) {
                    return [
                        'name' => $dept->name,
                        'count' => $dept->team_members_count
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'overview' => [
                        'total_departments' => ['count' => $totalDepartments, 'label' => 'Total Departments'],
                        'active_departments' => ['count' => $activeDepartments, 'label' => 'Active'],
                        'inactive_departments' => ['count' => $inactiveDepartments, 'label' => 'Inactive'],
                        'avg_members_per_dept' => [
                            'count' => $totalDepartments > 0 ? round(\App\Models\TeamMember::count() / $totalDepartments, 1) : 0,
                            'label' => 'Avg Members/Dept'
                        ]
                    ],
                    'recent_items' => $recentDepartments,
                    'popular_categories' => $departmentsByMembers,
                    'additional_metrics' => [
                        'This Month' => TeamMemberDepartment::whereMonth('created_at', now()->month)->count() . ' new departments',
                        'Total Team Members' => \App\Models\TeamMember::count() . ' across all departments',
                        'Largest Department' => $departmentsByMembers->first()['name'] ?? 'None' . ' (' . ($departmentsByMembers->first()['count'] ?? 0) . ' members)'
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Department statistics failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to load statistics'
            ], 500);
        }
    }

    /**
     * Export departments data
     */
    public function export(Request $request)
    {
        try {
            $query = TeamMemberDepartment::withCount('teamMembers');

            // Apply filters
            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $searchTerm = $request->search;
                    $q->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%");
                });
            }

            if ($request->filled('status')) {
                $query->where('is_active', $request->status === 'active' || $request->status === '1');
            }

            $departments = $query->orderBy('sort_order')->orderBy('name')->get();

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="team-departments-' . date('Y-m-d') . '.csv"',
            ];

            $callback = function () use ($departments) {
                $file = fopen('php://output', 'w');

                // CSV Headers
                fputcsv($file, [
                    'ID', 'Name', 'Slug', 'Description', 'Is Active', 
                    'Sort Order', 'Team Members Count', 'Created At', 'Updated At'
                ]);

                // CSV Data
                foreach ($departments as $department) {
                    fputcsv($file, [
                        $department->id,
                        $department->name,
                        $department->slug,
                        $department->description,
                        $department->is_active ? 'Yes' : 'No',
                        $department->sort_order,
                        $department->team_members_count,
                        $department->created_at->format('Y-m-d H:i:s'),
                        $department->updated_at->format('Y-m-d H:i:s'),
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            \Log::error('Department export failed: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Export failed. Please try again.');
        }
    }

    /**
     * Search departments (AJAX endpoint)
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:1|max:255',
            'limit' => 'nullable|integer|min:1|max:50'
        ]);

        try {
            $searchTerm = $request->input('query');
            $limit = $request->input('limit', 10);

            $departments = TeamMemberDepartment::withCount('teamMembers')
                ->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%");
                })
                ->orderBy('sort_order')
                ->orderBy('name')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'departments' => $departments->map(function ($dept) {
                    return [
                        'id' => $dept->id,
                        'name' => $dept->name,
                        'description' => $dept->description,
                        'is_active' => $dept->is_active,
                        'team_members_count' => $dept->team_members_count,
                        'edit_url' => route('admin.team-member-departments.edit', $dept),
                        'created_at' => $dept->created_at->format('M j, Y')
                    ];
                }),
                'total' => $departments->count()
            ]);

        } catch (\Exception $e) {
            \Log::error('Department search failed: ' . $e->getMessage(), [
                'query' => $request->query
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Search failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Generate unique slug
     */
    protected function generateUniqueSlug($name, $excludeId = null)
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if slug exists
     */
    protected function slugExists($slug, $excludeId = null)
    {
        $query = TeamMemberDepartment::where('slug', $slug);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}