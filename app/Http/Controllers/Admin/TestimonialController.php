<?php
// File: app/Http/Controllers/Admin/TestimonialController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class TestimonialController extends Controller
{
    /**
     * Display a listing of testimonials
     */
    public function index(Request $request)
    {
        $query = Testimonial::with(['project', 'client']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                  ->orWhere('client_company', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhereHas('project', function($pq) use ($search) {
                      $pq->where('title', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'featured') {
                $query->featured();
            } else {
                $query->where('status', $request->status);
            }
        }

        // Project filter
        if ($request->filled('project_id')) {
            $query->forProject($request->project_id);
        }

        // Rating filter
        if ($request->filled('rating')) {
            $query->where('rating', '>=', $request->rating);
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $testimonials = $query->paginate(15)->withQueryString();

        // Statistics for dashboard
        $stats = [
            'total' => Testimonial::count(),
            'active' => Testimonial::active()->count(),
            'featured' => Testimonial::featured()->count(),
            'pending' => Testimonial::pending()->count(),
            'average_rating' => round(Testimonial::active()->avg('rating') ?? 0, 1),
        ];

        $projects = Project::select('id', 'title')->get();

        return view('admin.testimonials.index', compact('testimonials', 'stats', 'projects'));
    }

    /**
     * Show the form for creating a new testimonial
     */
    public function create()
    {
        $projects = Project::select('id', 'title')->get();
        $clients = User::select('id', 'name', 'email', 'company')->get();
        
        return view('admin.testimonials.create', compact('projects', 'clients'));
    }

    /**
     * Store a newly created testimonial
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'client_id' => 'nullable|exists:users,id',
            'client_name' => 'required|string|max:255',
            'client_position' => 'nullable|string|max:255',
            'client_company' => 'nullable|string|max:255',
            'content' => 'required|string|min:10',
            'rating' => 'required|integer|min:1|max:5',
            'is_active' => 'boolean',
            'featured' => 'boolean',
            'status' => 'required|in:pending,approved,rejected,featured',
            'admin_notes' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Set default values
            $validated['is_active'] = $request->boolean('is_active', true);
            $validated['featured'] = $request->boolean('featured', false);

            // Auto-approve if status is approved/featured
            if (in_array($validated['status'], ['approved', 'featured'])) {
                $validated['approved_at'] = now();
            }

            // Create testimonial
            $testimonial = Testimonial::create($validated);

            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('testimonials', 'public');
                $testimonial->update(['image' => $imagePath]);
            }

            DB::commit();

            return redirect()->route('admin.testimonials.index')
                ->with('success', 'Testimonial created successfully!');

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
        $testimonial->load(['project', 'client']);
        
        return view('admin.testimonials.show', compact('testimonial'));
    }

    /**
     * Show the form for editing the testimonial
     */
    public function edit(Testimonial $testimonial)
    {
        $projects = Project::select('id', 'title')->get();
        $clients = User::select('id', 'name', 'email', 'company')->get();
        
        return view('admin.testimonials.edit', compact('testimonial', 'projects', 'clients'));
    }

    /**
     * Update the specified testimonial
     */
    public function update(Request $request, Testimonial $testimonial)
    {
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'client_id' => 'nullable|exists:users,id',
            'client_name' => 'required|string|max:255',
            'client_position' => 'nullable|string|max:255',
            'client_company' => 'nullable|string|max:255',
            'content' => 'required|string|min:10',
            'rating' => 'required|integer|min:1|max:5',
            'is_active' => 'boolean',
            'featured' => 'boolean',
            'status' => 'required|in:pending,approved,rejected,featured',
            'admin_notes' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $validated['is_active'] = $request->boolean('is_active');
            $validated['featured'] = $request->boolean('featured');

            // Handle status change to approved
            if ($validated['status'] === 'approved' && $testimonial->status !== 'approved') {
                $validated['approved_at'] = now();
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($testimonial->image) {
                    Storage::disk('public')->delete($testimonial->image);
                }
                
                $imagePath = $request->file('image')->store('testimonials', 'public');
                $validated['image'] = $imagePath;
            }

            $testimonial->update($validated);

            DB::commit();

            return redirect()->route('admin.testimonials.index')
                ->with('success', 'Testimonial updated successfully!');

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
        try {
            // Delete image
            if ($testimonial->image) {
                Storage::disk('public')->delete($testimonial->image);
            }

            $testimonial->delete();

            return redirect()->route('admin.testimonials.index')
                ->with('success', 'Testimonial deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting testimonial: ' . $e->getMessage());
        }
    }

    /**
     * Toggle active status
     */
    public function toggleActive(Testimonial $testimonial)
    {
        $testimonial->update([
            'is_active' => !$testimonial->is_active
        ]);

        $status = $testimonial->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "Testimonial {$status} successfully!");
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Testimonial $testimonial)
    {
        if ($testimonial->featured) {
            $testimonial->removeFeatured();
            $message = 'Testimonial removed from featured!';
        } else {
            $testimonial->setAsFeatured();
            $message = 'Testimonial set as featured!';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Approve testimonial
     */
    public function approve(Testimonial $testimonial)
    {
        $testimonial->approve();

        return redirect()->back()
            ->with('success', 'Testimonial approved successfully!');
    }

    /**
     * Reject testimonial
     */
    public function reject(Request $request, Testimonial $testimonial)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:500'
        ]);

        $testimonial->reject($request->rejection_reason);

        return redirect()->back()
            ->with('success', 'Testimonial rejected.');
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,feature,unfeature,approve,reject,delete',
            'testimonial_ids' => 'required|array',
            'testimonial_ids.*' => 'exists:testimonials,id'
        ]);

        $testimonials = Testimonial::whereIn('id', $request->testimonial_ids);
        $count = $testimonials->count();

        switch ($request->action) {
            case 'activate':
                $testimonials->update(['is_active' => true]);
                $message = "{$count} testimonials activated.";
                break;
            case 'deactivate':
                $testimonials->update(['is_active' => false]);
                $message = "{$count} testimonials deactivated.";
                break;
            case 'feature':
                $testimonials->update(['featured' => true, 'status' => 'featured']);
                $message = "{$count} testimonials featured.";
                break;
            case 'unfeature':
                $testimonials->update(['featured' => false]);
                $message = "{$count} testimonials unfeatured.";
                break;
            case 'approve':
                $testimonials->update([
                    'status' => 'approved',
                    'is_active' => true,
                    'approved_at' => now()
                ]);
                $message = "{$count} testimonials approved.";
                break;
            case 'reject':
                $testimonials->update(['status' => 'rejected', 'is_active' => false]);
                $message = "{$count} testimonials rejected.";
                break;
            case 'delete':
                // Delete images first
                $testimonials->get()->each(function($testimonial) {
                    if ($testimonial->image) {
                        Storage::disk('public')->delete($testimonial->image);
                    }
                });
                $testimonials->delete();
                $message = "{$count} testimonials deleted.";
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Get statistics
     */
    public function statistics()
    {
        $stats = [
            'total' => Testimonial::count(),
            'active' => Testimonial::active()->count(),
            'featured' => Testimonial::featured()->count(),
            'pending' => Testimonial::pending()->count(),
            'approved' => Testimonial::approved()->count(),
            'average_rating' => round(Testimonial::active()->avg('rating') ?? 0, 1),
            'ratings_distribution' => Testimonial::active()
                ->selectRaw('rating, COUNT(*) as count')
                ->groupBy('rating')
                ->orderBy('rating', 'desc')
                ->pluck('count', 'rating')
                ->toArray(),
        ];

        return response()->json($stats);
    }
}