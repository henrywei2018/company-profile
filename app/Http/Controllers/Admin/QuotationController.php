<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\Service;
use App\Models\User;
use App\Models\Project;
use App\Services\QuotationService;
use App\Mail\QuotationStatusUpdated;
use App\Mail\QuotationResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QuotationController extends Controller
{
    protected $quotationService;

    public function __construct(QuotationService $quotationService)
    {
        $this->quotationService = $quotationService;
    }

    /**
     * Display a listing of quotations with enhanced filtering
     */
    public function index(Request $request)
    {
        $query = Quotation::with(['service', 'client'])
            ->when($request->filled('status'), function ($q) use ($request) {
                return $q->where('status', $request->status);
            })
            ->when($request->filled('service'), function ($q) use ($request) {
                return $q->where('service_id', $request->service);
            })
            ->when($request->filled('date_range'), function ($q) use ($request) {
                $range = $request->date_range;
                $now = Carbon::now();
                
                return match($range) {
                    'today' => $q->whereDate('created_at', $now->toDateString()),
                    'week' => $q->whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()]),
                    'month' => $q->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year),
                    'quarter' => $q->whereBetween('created_at', [$now->firstOfQuarter(), $now->lastOfQuarter()]),
                    'year' => $q->whereYear('created_at', $now->year),
                    default => $q
                };
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                return $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('company', 'like', "%{$search}%")
                          ->orWhere('project_type', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('client_approved'), function ($q) use ($request) {
                if ($request->client_approved === '1') {
                    return $q->where('client_approved', true);
                } elseif ($request->client_approved === '0') {
                    return $q->where('client_approved', false);
                }
                return $q;
            });

        // Handle sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $quotations = $query->orderBy($sortField, $sortDirection)->paginate(15);
        
        // Statistics
        $stats = [
            'total' => Quotation::count(),
            'pending' => Quotation::where('status', 'pending')->count(),
            'reviewed' => Quotation::where('status', 'reviewed')->count(),
            'approved' => Quotation::where('status', 'approved')->count(),
            'rejected' => Quotation::where('status', 'rejected')->count(),
            'client_approved' => Quotation::where('client_approved', true)->count(),
            'this_month' => Quotation::whereMonth('created_at', Carbon::now()->month)->count(),
        ];
        
        $services = Service::all();
        
        return view('admin.quotations.index', compact('quotations', 'services', 'stats'));
    }

    /**
     * Display detailed quotation view
     */
    public function show(Quotation $quotation)
    {
        $quotation->load(['service', 'client', 'attachments']);
        
        // Get related quotations from same client
        $relatedQuotations = null;
        if ($quotation->client_id) {
            $relatedQuotations = Quotation::where('client_id', $quotation->client_id)
                ->where('id', '!=', $quotation->id)
                ->latest()
                ->limit(5)
                ->get();
        }
        
        // Get similar quotations (same service or project type)
        $similarQuotations = Quotation::where('id', '!=', $quotation->id)
            ->where(function ($query) use ($quotation) {
                $query->where('service_id', $quotation->service_id)
                      ->orWhere('project_type', $quotation->project_type);
            })
            ->latest()
            ->limit(3)
            ->get();
        
        return view('admin.quotations.show', compact(
            'quotation', 
            'relatedQuotations', 
            'similarQuotations'
        ));
    }

    /**
     * Show edit form
     */
    public function edit(Quotation $quotation)
    {
        $quotation->load(['service', 'client']);
        $services = Service::all();
        $clients = User::role('client')->get();
        
        return view('admin.quotations.edit', compact('quotation', 'services', 'clients'));
    }

    /**
     * Update quotation
     */
    public function update(Request $request, Quotation $quotation)
    {
        $validated = $request->validate([
            'service_id' => 'nullable|exists:services,id',
            'client_id' => 'nullable|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'project_type' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'requirements' => 'nullable|string',
            'budget_range' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'status' => 'required|in:pending,reviewed,approved,rejected',
            'admin_notes' => 'nullable|string',
            'estimated_cost' => 'nullable|string|max:255',
            'estimated_timeline' => 'nullable|string|max:255',
            'internal_notes' => 'nullable|string',
        ]);
        
        $oldStatus = $quotation->status;
        $quotation->update($validated);
        
        // Send notification if status changed
        if ($oldStatus !== $validated['status']) {
            try {
                Mail::to($quotation->email)->send(new QuotationStatusUpdated($quotation));
            } catch (\Exception $e) {
                \Log::error('Failed to send quotation status email: ' . $e->getMessage());
            }
        }
        
        return redirect()->route('admin.quotations.show', $quotation)
            ->with('success', 'Quotation updated successfully!');
    }

    /**
     * Quick status update
     */
    public function updateStatus(Request $request, Quotation $quotation)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewed,approved,rejected',
            'admin_notes' => 'nullable|string',
        ]);
        
        $oldStatus = $quotation->status;
        
        $quotation->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'reviewed_at' => $request->status === 'reviewed' ? now() : $quotation->reviewed_at,
            'approved_at' => $request->status === 'approved' ? now() : $quotation->approved_at,
        ]);
        
        // Send notification email
        try {
            Mail::to($quotation->email)->send(new QuotationStatusUpdated($quotation));
        } catch (\Exception $e) {
            \Log::error('Failed to send quotation status email: ' . $e->getMessage());
        }
        
        // If approved, offer to create project
        if ($request->status === 'approved') {
            session()->flash('info', 'Quotation approved! You can now create a project from this quotation.');
        }
        
        return redirect()->back()
            ->with('success', 'Quotation status updated to ' . ucfirst($request->status));
    }

    /**
     * Send custom response email
     */
    public function sendResponse(Request $request, Quotation $quotation)
    {
        $request->validate([
            'email_subject' => 'required|string|max:255',
            'email_message' => 'required|string',
            'include_quotation' => 'boolean',
        ]);
        
        try {
            Mail::to($quotation->email)->send(new QuotationResponse(
                $quotation,
                $request->email_subject,
                $request->email_message,
                $request->boolean('include_quotation')
            ));
            
            // Log the communication
            $quotation->update([
                'last_communication_at' => now(),
                'admin_notes' => ($quotation->admin_notes ? $quotation->admin_notes . "\n\n" : '') 
                    . "Email sent on " . now()->format('Y-m-d H:i:s') . ":\n" 
                    . "Subject: " . $request->email_subject
            ]);
            
            return redirect()->back()
                ->with('success', 'Response email sent successfully!');
                
        } catch (\Exception $e) {
            \Log::error('Failed to send quotation response email: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to send email. Please try again.');
        }
    }

    /**
     * Create project from quotation
     */
    public function createProject(Quotation $quotation)
    {
        if ($quotation->status !== 'approved') {
            return redirect()->back()
                ->with('error', 'Only approved quotations can be converted to projects.');
        }
        
        return redirect()->route('admin.projects.create', [
            'from_quotation' => $quotation->id
        ])->with('info', 'Creating project from quotation...');
    }

    /**
     * Duplicate quotation
     */
    public function duplicate(Quotation $quotation)
    {
        $newQuotation = $quotation->replicate();
        $newQuotation->status = 'pending';
        $newQuotation->admin_notes = null;
        $newQuotation->client_approved = null;
        $newQuotation->client_approved_at = null;
        $newQuotation->reviewed_at = null;
        $newQuotation->approved_at = null;
        $newQuotation->created_at = now();
        $newQuotation->save();
        
        return redirect()->route('admin.quotations.edit', $newQuotation)
            ->with('success', 'Quotation duplicated successfully!');
    }

    /**
     * Export quotations
     */
    public function export(Request $request)
    {
        $quotations = Quotation::with(['service', 'client'])
            ->when($request->filled('status'), function ($q) use ($request) {
                return $q->where('status', $request->status);
            })
            ->when($request->filled('date_from'), function ($q) use ($request) {
                return $q->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($q) use ($request) {
                return $q->whereDate('created_at', '<=', $request->date_to);
            })
            ->get();
        
        $filename = 'quotations_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($quotations) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'ID', 'Name', 'Email', 'Company', 'Phone', 'Service', 
                'Project Type', 'Location', 'Budget Range', 'Status', 
                'Client Approved', 'Created At', 'Updated At'
            ]);
            
            foreach ($quotations as $quotation) {
                fputcsv($file, [
                    $quotation->id,
                    $quotation->name,
                    $quotation->email,
                    $quotation->company,
                    $quotation->phone,
                    $quotation->service?->title,
                    $quotation->project_type,
                    $quotation->location,
                    $quotation->budget_range,
                    $quotation->status,
                    $quotation->client_approved ? 'Yes' : 'No',
                    $quotation->created_at->format('Y-m-d H:i:s'),
                    $quotation->updated_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,delete,change_status',
            'quotation_ids' => 'required|array',
            'quotation_ids.*' => 'exists:quotations,id',
            'new_status' => 'required_if:action,change_status|in:pending,reviewed,approved,rejected',
        ]);
        
        $quotations = Quotation::whereIn('id', $request->quotation_ids)->get();
        $count = $quotations->count();
        
        switch ($request->action) {
            case 'approve':
                $quotations->each(function ($quotation) {
                    $quotation->update(['status' => 'approved', 'approved_at' => now()]);
                });
                $message = "{$count} quotations approved successfully!";
                break;
                
            case 'reject':
                $quotations->each(function ($quotation) {
                    $quotation->update(['status' => 'rejected']);
                });
                $message = "{$count} quotations rejected successfully!";
                break;
                
            case 'change_status':
                $quotations->each(function ($quotation) use ($request) {
                    $quotation->update(['status' => $request->new_status]);
                });
                $message = "{$count} quotations status updated successfully!";
                break;
                
            case 'delete':
                $quotations->each(function ($quotation) {
                    $quotation->delete();
                });
                $message = "{$count} quotations deleted successfully!";
                break;
        }
        
        return redirect()->back()->with('success', $message);
    }

    /**
     * Delete quotation
     */
    public function destroy(Quotation $quotation)
    {
        // Delete attachments
        if ($quotation->attachments()->count() > 0) {
            foreach ($quotation->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
                $attachment->delete();
            }
        }
        
        $quotation->delete();
        
        return redirect()->route('admin.quotations.index')
            ->with('success', 'Quotation deleted successfully!');
    }

    /**
     * Generate quotation statistics
     */
    public function statistics()
    {
        $stats = [
            'total_quotations' => Quotation::count(),
            'pending' => Quotation::where('status', 'pending')->count(),
            'approved' => Quotation::where('status', 'approved')->count(),
            'rejected' => Quotation::where('status', 'rejected')->count(),
            'client_approved' => Quotation::where('client_approved', true)->count(),
            'monthly_stats' => Quotation::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->get(),
            'service_stats' => Quotation::join('services', 'quotations.service_id', '=', 'services.id')
                ->selectRaw('services.title, COUNT(*) as count')
                ->groupBy('services.title')
                ->orderBy('count', 'desc')
                ->get(),
            'conversion_rate' => Quotation::where('status', 'approved')->count() / max(1, Quotation::count()) * 100,
        ];
        
        return response()->json($stats);
    }
}