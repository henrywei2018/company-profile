<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\QuotationAttachment;
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
     * Display a listing of quotations with enhanced filtering (following message management approach)
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
            ->when($request->filled('priority'), function ($q) use ($request) {
                if ($request->priority === 'high') {
                    return $q->whereIn('priority', ['high', 'urgent']);
                }
                return $q->where('priority', $request->priority);
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
            ->when($request->filled('created_from') && $request->filled('created_to'), function ($q) use ($request) {
                return $q->whereBetween('created_at', [
                    Carbon::parse($request->created_from)->startOfDay(),
                    Carbon::parse($request->created_to)->endOfDay()
                ]);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                return $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('company', 'like', "%{$search}%")
                          ->orWhere('project_type', 'like', "%{$search}%")
                          ->orWhere('requirements', 'like', "%{$search}%");
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
        
        // Enhanced statistics similar to message management
        $statusCounts = [
            'total' => Quotation::count(),
            'pending' => Quotation::where('status', 'pending')->count(),
            'reviewed' => Quotation::where('status', 'reviewed')->count(),
            'approved' => Quotation::where('status', 'approved')->count(),
            'rejected' => Quotation::where('status', 'rejected')->count(),
            'client_approved' => Quotation::where('client_approved', true)->count(),
            'this_month' => Quotation::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
            'this_week' => Quotation::whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])->count(),
            'today' => Quotation::whereDate('created_at', Carbon::today())->count(),
        ];
        
        // Calculate needs attention (high priority + overdue)
        $statusCounts['urgent'] = Quotation::where('priority', 'urgent')
            ->where('status', 'pending')
            ->count();
            
        $statusCounts['high_priority'] = Quotation::whereIn('priority', ['high', 'urgent'])
            ->where('status', 'pending')
            ->count();
            
        $statusCounts['overdue'] = Quotation::where('status', 'pending')
            ->where('created_at', '<', Carbon::now()->subDays(3))
            ->count();
            
        $statusCounts['needs_attention'] = Quotation::where('status', 'pending')
            ->where(function($query) {
                $query->whereIn('priority', ['high', 'urgent'])
                      ->orWhere('created_at', '<', Carbon::now()->subDays(3));
            })
            ->count();
        
        $services = Service::all();
        
        return view('admin.quotations.index', compact('quotations', 'services', 'statusCounts'));
    }

    /**
     * Display detailed quotation view
     */
    public function show(Quotation $quotation)
    {
        $quotation->load(['service', 'client', 'attachments']);
        
        // Mark as reviewed if it's the first time viewing
        if ($quotation->status === 'pending' && !$quotation->reviewed_at) {
            $quotation->update([
                'reviewed_at' => now(),
                'status' => 'reviewed'
            ]);
        }
        
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
            'priority' => 'nullable|in:low,normal,high,urgent',
            'admin_notes' => 'nullable|string',
            'estimated_cost' => 'nullable|string|max:255',
            'estimated_timeline' => 'nullable|string|max:255',
            'internal_notes' => 'nullable|string',
            'additional_info' => 'nullable|string',
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
        
        // Handle continue editing vs redirect to show
        if ($request->has('action') && $request->action === 'save_and_continue') {
            return redirect()->route('admin.quotations.edit', $quotation)
                ->with('success', 'Quotation updated successfully! Continue editing...');
        }
        
        return redirect()->route('admin.quotations.show', $quotation)
            ->with('success', 'Quotation updated successfully!');
    }

    /**
     * Quick status update (similar to message toggle-read)
     */
    public function updateStatus(Request $request, Quotation $quotation)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewed,approved,rejected',
            'admin_notes' => 'nullable|string',
        ]);
        
        $oldStatus = $quotation->status;
        
        $updateData = [
            'status' => $request->status,
            'last_communication_at' => now(),
        ];
        
        // Add admin notes if provided
        if ($request->filled('admin_notes')) {
            $updateData['admin_notes'] = $request->admin_notes;
        }
        
        // Add timestamp for specific statuses
        if ($request->status === 'reviewed' && $oldStatus !== 'reviewed') {
            $updateData['reviewed_at'] = now();
        } elseif ($request->status === 'approved' && $oldStatus !== 'approved') {
            $updateData['approved_at'] = now();
        }
        
        $quotation->update($updateData);
        
        // Send notification email
        if ($oldStatus !== $request->status) {
            try {
                Mail::to($quotation->email)->send(new QuotationStatusUpdated($quotation));
            } catch (\Exception $e) {
                \Log::error('Failed to send quotation status email: ' . $e->getMessage());
            }
        }
        
        // If approved, offer to create project
        if ($request->status === 'approved') {
            session()->flash('info', 'Quotation approved! You can now create a project from this quotation.');
        }
        
        return redirect()->back()
            ->with('success', 'Quotation status updated to ' . ucfirst($request->status) . '!');
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

    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'nullable|string|max:20',
        'company' => 'nullable|string|max:255',
        'service_id' => 'nullable|exists:services,id',
        'project_type' => 'required|string|max:255',
        'location' => 'nullable|string|max:255',
        'requirements' => 'required|string',
        'budget_range' => 'nullable|string|max:100',
        'start_date' => 'nullable|date|after:today',
        'attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', // 10MB max per file
    ]);

    // Link to existing user if authenticated
    if (auth()->check()) {
        $validated['client_id'] = auth()->id();
    } else {
        // Check if user with this email already exists
        $existingUser = User::where('email', $validated['email'])->first();
        if ($existingUser) {
            $validated['client_id'] = $existingUser->id;
        }
    }

    // Set default values
    $validated['status'] = 'pending';
    $validated['priority'] = 'normal';
    $validated['source'] = 'website';

    // Create the quotation
    $quotation = Quotation::create($validated);

    // Handle file attachments using QuotationAttachment
    if ($request->hasFile('attachments')) {
        foreach ($request->file('attachments') as $file) {
            QuotationAttachment::createFromUploadedFile($file, $quotation);
        }
    }

    // Send notification emails
    try {
        // Send confirmation to client
        Mail::to($quotation->email)->send(new \App\Mail\QuotationReceived($quotation));
        
        // Send notification to admin
        $adminEmail = config('mail.admin_email', 'admin@usahaprimalestari.com');
        Mail::to($adminEmail)->send(new \App\Mail\QuotationReceived($quotation, true));
        
    } catch (\Exception $e) {
        \Log::error('Failed to send quotation notification emails: ' . $e->getMessage());
        // Don't fail the request if email fails
    }

    // Store success message in session
    session()->flash('quotation_success', [
        'id' => $quotation->id,
        'name' => $quotation->name,
        'email' => $quotation->email,
        'created_at' => $quotation->created_at
    ]);

    return redirect()->route('quotation.thank-you');
}

/**
 * Admin store method for creating quotations from admin panel.
 */
public function adminStore(Request $request)
{
    $validated = $request->validate([
        'client_id' => 'nullable|exists:users,id',
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'nullable|string|max:20',
        'company' => 'nullable|string|max:255',
        'service_id' => 'nullable|exists:services,id',
        'project_type' => 'required|string|max:255',
        'location' => 'nullable|string|max:255',
        'requirements' => 'required|string',
        'budget_range' => 'nullable|string|max:100',
        'start_date' => 'nullable|date',
        'status' => 'required|in:pending,reviewed,approved,rejected',
        'priority' => 'required|in:low,normal,high,urgent',
        'source' => 'required|string|max:50',
        'estimated_cost' => 'nullable|string|max:255',
        'estimated_timeline' => 'nullable|string|max:255',
        'admin_notes' => 'nullable|string',
        'internal_notes' => 'nullable|string',
        'additional_info' => 'nullable|string',
        'attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
    ]);

    // Set timestamps based on status
    if ($validated['status'] === 'reviewed') {
        $validated['reviewed_at'] = now();
    } elseif ($validated['status'] === 'approved') {
        $validated['approved_at'] = now();
        $validated['reviewed_at'] = now();
    }

    // Create the quotation
    $quotation = Quotation::create($validated);

    // Handle file attachments using QuotationAttachment
    if ($request->hasFile('attachments')) {
        foreach ($request->file('attachments') as $file) {
            QuotationAttachment::createFromUploadedFile($file, $quotation);
        }
    }

    // Send notification email to client if status is not pending
    if ($validated['status'] !== 'pending') {
        try {
            Mail::to($quotation->email)->send(new QuotationStatusUpdated($quotation));
        } catch (\Exception $e) {
            \Log::error('Failed to send quotation status email: ' . $e->getMessage());
        }
    }

    // Handle continue editing vs redirect to show
    if ($request->has('action') && $request->action === 'save_and_continue') {
        return redirect()->route('admin.quotations.edit', $quotation)
            ->with('success', 'Quotation created successfully! Continue editing...');
    }

    return redirect()->route('admin.quotations.show', $quotation)
        ->with('success', 'Quotation created successfully!');
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
        $newQuotation->priority = 'normal';
        $newQuotation->admin_notes = null;
        $newQuotation->internal_notes = null;
        $newQuotation->client_approved = null;
        $newQuotation->client_approved_at = null;
        $newQuotation->reviewed_at = null;
        $newQuotation->approved_at = null;
        $newQuotation->last_communication_at = null;
        $newQuotation->created_at = now();
        $newQuotation->save();
        
        return redirect()->route('admin.quotations.edit', $newQuotation)
            ->with('success', 'Quotation duplicated successfully!');
    }

    /**
     * Export quotations with current filters
     */
    public function export(Request $request)
    {
        $query = Quotation::with(['service', 'client'])
            ->when($request->filled('status'), function ($q) use ($request) {
                return $q->where('status', $request->status);
            })
            ->when($request->filled('service'), function ($q) use ($request) {
                return $q->where('service_id', $request->service);
            })
            ->when($request->filled('created_from') && $request->filled('created_to'), function ($q) use ($request) {
                return $q->whereBetween('created_at', [
                    Carbon::parse($request->created_from)->startOfDay(),
                    Carbon::parse($request->created_to)->endOfDay()
                ]);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                return $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('company', 'like', "%{$search}%")
                          ->orWhere('project_type', 'like', "%{$search}%");
                });
            });
        
        $quotations = $query->get();
        
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
                'Priority', 'Client Approved', 'Created At', 'Updated At'
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
                    $quotation->priority,
                    $quotation->client_approved ? 'Yes' : ($quotation->client_approved === false ? 'No' : 'Pending'),
                    $quotation->created_at->format('Y-m-d H:i:s'),
                    $quotation->updated_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk actions (similar to message bulk actions)
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,delete,change_status',
            'quotation_ids' => 'required|string', // Comma-separated IDs
            'new_status' => 'required_if:action,change_status|in:pending,reviewed,approved,rejected',
        ]);
        
        // Parse comma-separated IDs
        $quotationIds = array_filter(explode(',', $request->quotation_ids));
        
        if (empty($quotationIds)) {
            return redirect()->back()->with('error', 'No quotations selected.');
        }
        
        $quotations = Quotation::whereIn('id', $quotationIds)->get();
        $count = $quotations->count();
        
        if ($count === 0) {
            return redirect()->back()->with('error', 'Selected quotations not found.');
        }
        
        switch ($request->action) {
            case 'approve':
                $quotations->each(function ($quotation) {
                    $quotation->update([
                        'status' => 'approved', 
                        'approved_at' => now(),
                        'last_communication_at' => now()
                    ]);
                    
                    // Send notification email
                    try {
                        Mail::to($quotation->email)->send(new QuotationStatusUpdated($quotation));
                    } catch (\Exception $e) {
                        \Log::error('Failed to send approval email for quotation ' . $quotation->id);
                    }
                });
                $message = "{$count} quotation(s) approved successfully!";
                break;
                
            case 'reject':
                $quotations->each(function ($quotation) {
                    $quotation->update([
                        'status' => 'rejected',
                        'last_communication_at' => now()
                    ]);
                    
                    // Send notification email
                    try {
                        Mail::to($quotation->email)->send(new QuotationStatusUpdated($quotation));
                    } catch (\Exception $e) {
                        \Log::error('Failed to send rejection email for quotation ' . $quotation->id);
                    }
                });
                $message = "{$count} quotation(s) rejected successfully!";
                break;
                
            case 'change_status':
                $quotations->each(function ($quotation) use ($request) {
                    $updateData = [
                        'status' => $request->new_status,
                        'last_communication_at' => now()
                    ];
                    
                    if ($request->new_status === 'reviewed') {
                        $updateData['reviewed_at'] = now();
                    } elseif ($request->new_status === 'approved') {
                        $updateData['approved_at'] = now();
                    }
                    
                    $quotation->update($updateData);
                });
                $message = "{$count} quotation(s) status updated successfully!";
                break;
                
            case 'delete':
                $quotations->each(function ($quotation) {
                    // Delete attachments first
                    if ($quotation->attachments()->count() > 0) {
                        foreach ($quotation->attachments as $attachment) {
                            Storage::disk('public')->delete($attachment->file_path);
                            $attachment->delete();
                        }
                    }
                    $quotation->delete();
                });
                $message = "{$count} quotation(s) deleted successfully!";
                break;
        }
        
        return redirect()->back()->with('success', $message);
    }

    /**
     * Delete quotation
     */
    public function destroy(Quotation $quotation)
    {
        // Delete attachments (this will also delete files from storage due to model boot method)
        $quotation->attachments()->delete();
        
        $quotation->delete();
        
        return redirect()->route('admin.quotations.index')
            ->with('success', 'Quotation deleted successfully!');
    }

    public function downloadAttachment(Quotation $quotation, $attachmentId)
    {
        $attachment = $quotation->attachments()->findOrFail($attachmentId);
        
        if (!$attachment->exists()) {
            abort(404, 'File not found');
        }
        
        return response()->download(
            storage_path('app/public/' . $attachment->file_path),
            $attachment->file_name
        );
    }

    /**
     * Generate quotation statistics for dashboard
     */
    public function statistics()
    {
        $stats = [
            'total_quotations' => Quotation::count(),
            'pending' => Quotation::where('status', 'pending')->count(),
            'approved' => Quotation::where('status', 'approved')->count(),
            'rejected' => Quotation::where('status', 'rejected')->count(),
            'client_approved' => Quotation::where('client_approved', true)->count(),
            'needs_attention' => Quotation::where('status', 'pending')
                ->where(function($query) {
                    $query->whereIn('priority', ['high', 'urgent'])
                          ->orWhere('created_at', '<', Carbon::now()->subDays(3));
                })
                ->count(),
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