<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\QuotationAttachment;
use App\Models\Service;
use App\Models\User;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Services\QuotationService;
use App\Services\TempNotifiable;
use App\Facades\Notifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Str;

class QuotationController extends Controller
{
    protected $quotationService;

    public function __construct(QuotationService $quotationService = null)
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
        
        // Enhanced statistics
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
        
        // Calculate priority and attention metrics
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
     * Show create form for manual quotation creation
     */
    public function create()
    {
        $services = Service::active()->orderBy('title')->get();
        $clients = User::role('client')->orderBy('name')->get();
        
        return view('admin.quotations.create', compact('services', 'clients'));
    }

    /**
     * Store quotation from public form with centralized notifications
     */
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
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
        ]);

        try {
            DB::beginTransaction();

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

            // Handle file attachments
            $attachmentCount = 0;
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    if ($attachmentCount < 5) {
                        QuotationAttachment::createFromUploadedFile($file, $quotation);
                        $attachmentCount++;
                    }
                }
            }

            // Send notifications using centralized system
            try {
                // Create appropriate notifiable
                $clientNotifiable = $quotation->client 
                    ? $quotation->client 
                    : TempNotifiable::forQuotation($quotation->email, $quotation->name, [
                        'quotation_id' => $quotation->id,
                        'project_type' => $quotation->project_type
                    ]);

                // Send confirmation to client
                if (settings('quotation_client_confirmation_enabled', true)) {
                    Notifications::send('quotation.confirmation', $quotation, $clientNotifiable);
                    
                    Log::info('Quotation confirmation sent to client', [
                        'quotation_id' => $quotation->id,
                        'client_email' => $quotation->email,
                        'is_registered' => $quotation->client ? true : false
                    ]);
                }
                
                // Send notification to admin
                if (settings('notify_admin_new_quotation', true)) {
                    Notifications::send('quotation.created', $quotation);
                    
                    Log::info('Quotation notification sent to admin', [
                        'quotation_id' => $quotation->id
                    ]);
                }
                
            } catch (\Exception $e) {
                Log::error('Failed to send quotation notification: ' . $e->getMessage(), [
                    'quotation_id' => $quotation->id
                ]);
                // Continue without failing the request
            }

            DB::commit();

            // Store success message in session
            session()->flash('quotation_success', [
                'id' => $quotation->id,
                'name' => $quotation->name,
                'email' => $quotation->email,
                'created_at' => $quotation->created_at,
                'confirmation_sent' => settings('quotation_client_confirmation_enabled', true)
            ]);

            return redirect()->route('quotation.thank-you');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to store quotation: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to submit quotation request. Please try again.');
        }
    }

    /**
     * Admin store method for creating quotations with centralized notifications
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
            'send_notification' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Set timestamps based on status
            if ($validated['status'] === 'reviewed') {
                $validated['reviewed_at'] = now();
            } elseif ($validated['status'] === 'approved') {
                $validated['approved_at'] = now();
                $validated['reviewed_at'] = now();
            }

            // Create the quotation
            $quotation = Quotation::create($validated);

            // Handle file attachments
            $attachmentCount = 0;
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    if ($attachmentCount < 10) {
                        QuotationAttachment::createFromUploadedFile($file, $quotation);
                        $attachmentCount++;
                    }
                }
            }

            // Send notification using centralized system
            if ($request->boolean('send_notification', false)) {
                try {
                    // Create appropriate notifiable
                    $clientNotifiable = $quotation->client 
                        ? $quotation->client 
                        : TempNotifiable::forQuotation($quotation->email, $quotation->name);

                    // Send appropriate notification based on status
                    $notificationType = match($validated['status']) {
                        'pending' => 'quotation.confirmation',
                        'approved' => 'quotation.approved',
                        'rejected' => 'quotation.rejected',
                        default => 'quotation.status_updated'
                    };

                    Notifications::send($notificationType, $quotation, $clientNotifiable);
                    
                    Log::info('Admin-created quotation notification sent', [
                        'quotation_id' => $quotation->id,
                        'status' => $validated['status'],
                        'notification_type' => $notificationType,
                        'client_email' => $quotation->email
                    ]);
                    
                } catch (\Exception $e) {
                    Log::error('Failed to send admin quotation notification: ' . $e->getMessage(), [
                        'quotation_id' => $quotation->id
                    ]);
                }
            }

            DB::commit();

            // Handle continue editing vs redirect to show
            if ($request->has('action') && $request->action === 'save_and_continue') {
                return redirect()->route('admin.quotations.edit', $quotation)
                    ->with('success', 'Quotation created successfully! Continue editing...');
            }

            $successMessage = 'Quotation created successfully!';
            if ($request->boolean('send_notification', false)) {
                $successMessage .= ' Client has been notified via email.';
            }

            return redirect()->route('admin.quotations.show', $quotation)
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create admin quotation: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create quotation. Please try again.');
        }
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
        
        // Get similar quotations
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
     * Update quotation with centralized notifications
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
            'send_notification' => 'boolean',
        ]);
        
        try {
            DB::beginTransaction();
            
            $oldStatus = $quotation->status;
            $quotation->update($validated);
            
            // Send notification if status changed and notification is requested
            if ($oldStatus !== $validated['status'] && $request->boolean('send_notification', false)) {
                try {
                    // Create appropriate notifiable
                    $clientNotifiable = $quotation->client 
                        ? $quotation->client 
                        : TempNotifiable::forQuotation($quotation->email, $quotation->name);

                    // Send status-specific notification
                    $notificationType = match($validated['status']) {
                        'approved' => 'quotation.approved',
                        'rejected' => 'quotation.rejected',
                        default => 'quotation.status_updated'
                    };

                    Notifications::send($notificationType, $quotation, $clientNotifiable);
                    
                    Log::info('Quotation status update notification sent', [
                        'quotation_id' => $quotation->id,
                        'old_status' => $oldStatus,
                        'new_status' => $validated['status'],
                        'notification_type' => $notificationType,
                        'client_email' => $quotation->email
                    ]);
                    
                } catch (\Exception $e) {
                    Log::error('Failed to send quotation update notification: ' . $e->getMessage(), [
                        'quotation_id' => $quotation->id
                    ]);
                }
            }
            
            DB::commit();
            
            // Handle continue editing vs redirect to show
            if ($request->has('action') && $request->action === 'save_and_continue') {
                return redirect()->route('admin.quotations.edit', $quotation)
                    ->with('success', 'Quotation updated successfully! Continue editing...');
            }
            
            $successMessage = 'Quotation updated successfully!';
            if ($oldStatus !== $validated['status'] && $request->boolean('send_notification', false)) {
                $successMessage .= ' Client has been notified of status change.';
            }
            
            return redirect()->route('admin.quotations.show', $quotation)
                ->with('success', $successMessage);
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update quotation: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update quotation. Please try again.');
        }
    }

    /**
     * Quick status update with centralized notifications
     */
    public function updateStatus(Request $request, Quotation $quotation)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewed,approved,rejected',
            'admin_notes' => 'nullable|string',
            'send_notification' => 'boolean',
        ]);
        
        try {
            DB::beginTransaction();
            
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
            
            // Send notification using centralized system
            if ($oldStatus !== $request->status && $request->boolean('send_notification', true)) {
                try {
                    // Create appropriate notifiable
                    $clientNotifiable = $quotation->client 
                        ? $quotation->client 
                        : TempNotifiable::forQuotation($quotation->email, $quotation->name);

                    // Send status-specific notification
                    $notificationType = match($request->status) {
                        'approved' => 'quotation.approved',
                        'rejected' => 'quotation.rejected', 
                        default => 'quotation.status_updated'
                    };

                    Notifications::send($notificationType, $quotation, $clientNotifiable);
                    
                    Log::info('Quick status update notification sent', [
                        'quotation_id' => $quotation->id,
                        'old_status' => $oldStatus,
                        'new_status' => $request->status,
                        'notification_type' => $notificationType
                    ]);
                    
                } catch (\Exception $e) {
                    Log::error('Failed to send status update notification: ' . $e->getMessage());
                }
            }
            
            DB::commit();
            
            // If approved, offer to create project
            if ($request->status === 'approved') {
                session()->flash('info', 'Quotation approved! You can now create a project from this quotation.');
            }
            
            $successMessage = 'Quotation status updated to ' . ucfirst($request->status) . '!';
            if ($request->boolean('send_notification', true)) {
                $successMessage .= ' Client has been notified.';
            }
            
            return redirect()->back()->with('success', $successMessage);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update quotation status: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to update status. Please try again.');
        }
    }

    /**
     * Send custom response using centralized notifications
     */
    public function sendResponse(Request $request, Quotation $quotation)
    {
        $request->validate([
            'email_subject' => 'required|string|max:255',
            'email_message' => 'required|string',
            'include_quotation' => 'boolean',
        ]);
        
        try {
            // Create appropriate notifiable
            $clientNotifiable = $quotation->client 
                ? $quotation->client 
                : TempNotifiable::forQuotation($quotation->email, $quotation->name, [
                    'custom_subject' => $request->email_subject,
                    'custom_message' => $request->email_message,
                    'include_quotation' => $request->boolean('include_quotation', false)
                ]);

            // Send custom response notification
            Notifications::send('quotation.custom_response', $quotation, $clientNotifiable);
            
            // Log the communication
            $quotation->update([
                'last_communication_at' => now(),
                'admin_notes' => ($quotation->admin_notes ? $quotation->admin_notes . "\n\n" : '') 
                    . "Email sent on " . now()->format('Y-m-d H:i:s') . ":\n" 
                    . "Subject: " . $request->email_subject
            ]);
            
            Log::info('Custom quotation response sent', [
                'quotation_id' => $quotation->id,
                'subject' => $request->email_subject,
                'recipient' => $quotation->email
            ]);
            
            return redirect()->back()
                ->with('success', 'Response email sent successfully!');
                
        } catch (\Exception $e) {
            Log::error('Failed to send quotation response: ' . $e->getMessage());
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
     * Bulk actions with centralized notifications
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,delete,change_status',
            'quotation_ids' => 'required|string',
            'new_status' => 'required_if:action,change_status|in:pending,reviewed,approved,rejected',
            'send_notifications' => 'boolean',
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
        
        try {
            DB::beginTransaction();
            
            $sendNotifications = $request->boolean('send_notifications', false);
            
            switch ($request->action) {
                case 'approve':
                    foreach ($quotations as $quotation) {
                        $oldStatus = $quotation->status;
                        $quotation->update([
                            'status' => 'approved', 
                            'approved_at' => now(),
                            'last_communication_at' => now()
                        ]);
                        
                        // Send notification using centralized system
                        if ($sendNotifications) {
                            try {
                                $clientNotifiable = $quotation->client 
                                    ? $quotation->client 
                                    : TempNotifiable::forQuotation($quotation->email, $quotation->name);
                                
                                Notifications::send('quotation.approved', $quotation, $clientNotifiable);
                            } catch (\Exception $e) {
                                Log::error('Failed to send bulk approval notification for quotation ' . $quotation->id);
                            }
                        }
                    }
                    $message = "{$count} quotation(s) approved successfully!";
                    break;
                    
                case 'reject':
                    foreach ($quotations as $quotation) {
                        $oldStatus = $quotation->status;
                        $quotation->update([
                            'status' => 'rejected',
                            'last_communication_at' => now()
                        ]);
                        
                        // Send notification using centralized system
                        if ($sendNotifications) {
                            try {
                                $clientNotifiable = $quotation->client 
                                    ? $quotation->client 
                                    : TempNotifiable::forQuotation($quotation->email, $quotation->name);
                                
                                Notifications::send('quotation.rejected', $quotation, $clientNotifiable);
                            } catch (\Exception $e) {
                                Log::error('Failed to send bulk rejection notification for quotation ' . $quotation->id);
                            }
                        }
                    }
                    $message = "{$count} quotation(s) rejected successfully!";
                    break;
                    
                case 'change_status':
                    foreach ($quotations as $quotation) {
                        $oldStatus = $quotation->status;
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
                        
                        // Send notification using centralized system
                        if ($sendNotifications && $oldStatus !== $request->new_status) {
                            try {
                                $clientNotifiable = $quotation->client 
                                    ? $quotation->client 
                                    : TempNotifiable::forQuotation($quotation->email, $quotation->name);
                                
                                $notificationType = match($request->new_status) {
                                    'approved' => 'quotation.approved',
                                    'rejected' => 'quotation.rejected',
                                    default => 'quotation.status_updated'
                                };
                                
                                Notifications::send($notificationType, $quotation, $clientNotifiable);
                            } catch (\Exception $e) {
                                Log::error('Failed to send bulk status update notification for quotation ' . $quotation->id);
                            }
                        }
                    }
                    $message = "{$count} quotation(s) status updated successfully!";
                    break;
                    
                case 'delete':
                    foreach ($quotations as $quotation) {
                        // Delete attachments first
                        if ($quotation->attachments()->count() > 0) {
                            foreach ($quotation->attachments as $attachment) {
                                Storage::disk('public')->delete($attachment->file_path);
                                $attachment->delete();
                            }
                        }
                        $quotation->delete();
                    }
                    $message = "{$count} quotation(s) deleted successfully!";
                    break;
            }
            
            DB::commit();
            
            if ($sendNotifications && in_array($request->action, ['approve', 'reject', 'change_status'])) {
                $message .= ' Email notifications have been sent to clients.';
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk quotation action failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to perform bulk action. Please try again.');
        }
    }

    /**
     * Delete quotation
     */
    public function destroy(Quotation $quotation)
    {
        try {
            DB::beginTransaction();
            
            // Delete attachments
            $quotation->attachments()->delete();
            
            $quotation->delete();
            
            DB::commit();
            
            return redirect()->route('admin.quotations.index')
                ->with('success', 'Quotation deleted successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete quotation: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to delete quotation. Please try again.');
        }
    }

    /**
     * Download attachment
     */
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

    /**
     * Quick approve quotation with notification
     */
    public function quickApprove(Quotation $quotation)
    {
        try {
            DB::beginTransaction();
            
            $quotation->update([
                'status' => 'approved',
                'approved_at' => now(),
                'last_communication_at' => now()
            ]);
            
            // Send approval notification using centralized system
            try {
                $clientNotifiable = $quotation->client 
                    ? $quotation->client 
                    : TempNotifiable::forQuotation($quotation->email, $quotation->name);
                
                Notifications::send('quotation.approved', $quotation, $clientNotifiable);
                
                Log::info('Quick approval notification sent', [
                    'quotation_id' => $quotation->id,
                    'client_email' => $quotation->email
                ]);
                
            } catch (\Exception $e) {
                Log::error('Failed to send quick approval notification: ' . $e->getMessage());
            }
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Quotation approved successfully! Client has been notified.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to quick approve quotation: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Failed to approve quotation. Please try again.');
        }
    }

    /**
     * Quick reject quotation with notification
     */
    public function quickReject(Quotation $quotation)
    {
        try {
            DB::beginTransaction();
            
            $quotation->update([
                'status' => 'rejected',
                'last_communication_at' => now()
            ]);
            
            // Send rejection notification using centralized system
            try {
                $clientNotifiable = $quotation->client 
                    ? $quotation->client 
                    : TempNotifiable::forQuotation($quotation->email, $quotation->name);
                
                Notifications::send('quotation.rejected', $quotation, $clientNotifiable);
                
                Log::info('Quick rejection notification sent', [
                    'quotation_id' => $quotation->id,
                    'client_email' => $quotation->email
                ]);
                
            } catch (\Exception $e) {
                Log::error('Failed to send quick rejection notification: ' . $e->getMessage());
            }
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Quotation rejected successfully! Client has been notified.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to quick reject quotation: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Failed to reject quotation. Please try again.');
        }
    }

    /**
     * Mark quotation as reviewed
     */
    public function markAsReviewed(Quotation $quotation)
    {
        try {
            $quotation->update([
                'status' => 'reviewed',
                'reviewed_at' => now(),
                'last_communication_at' => now()
            ]);
            
            return redirect()->back()->with('success', 'Quotation marked as reviewed!');
            
        } catch (\Exception $e) {
            Log::error('Failed to mark quotation as reviewed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update quotation status.');
        }
    }

    /**
     * Update quotation priority
     */
    public function updatePriority(Request $request, Quotation $quotation)
    {
        $request->validate([
            'priority' => 'required|in:low,normal,high,urgent'
        ]);
        
        try {
            $quotation->update(['priority' => $request->priority]);
            
            return redirect()->back()->with('success', 'Quotation priority updated to ' . ucfirst($request->priority) . '!');
            
        } catch (\Exception $e) {
            Log::error('Failed to update quotation priority: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update priority.');
        }
    }

    /**
     * Link quotation to existing client
     */
    public function linkClient(Request $request, Quotation $quotation)
    {
        $request->validate([
            'client_id' => 'required|exists:users,id'
        ]);
        
        try {
            $client = User::findOrFail($request->client_id);
            
            if (!$client->hasRole('client')) {
                return redirect()->back()->with('error', 'Selected user is not a client.');
            }
            
            $quotation->update(['client_id' => $client->id]);
            
            return redirect()->back()->with('success', 'Quotation linked to client: ' . $client->name);
            
        } catch (\Exception $e) {
            Log::error('Failed to link quotation to client: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to link client.');
        }
    }

    /**
     * View quotation communications history
     */
    public function communications(Quotation $quotation)
    {
        // Get all communications related to this quotation
        $communications = collect();
        
        // Add quotation creation
        $communications->push([
            'type' => 'quotation_created',
            'date' => $quotation->created_at,
            'description' => 'Quotation submitted',
            'details' => "Quotation request received from {$quotation->name}",
        ]);
        
        // Add status changes
        if ($quotation->reviewed_at) {
            $communications->push([
                'type' => 'status_change',
                'date' => $quotation->reviewed_at,
                'description' => 'Quotation reviewed',
                'details' => 'Quotation status changed to reviewed',
            ]);
        }
        
        if ($quotation->approved_at) {
            $communications->push([
                'type' => 'status_change',
                'date' => $quotation->approved_at,
                'description' => 'Quotation approved',
                'details' => 'Quotation status changed to approved',
            ]);
        }
        
        // Add communication notes from admin_notes
        if ($quotation->admin_notes) {
            $notes = explode("\n\n", $quotation->admin_notes);
            foreach ($notes as $note) {
                if (str_contains($note, 'Email sent on')) {
                    $lines = explode("\n", $note);
                    $dateLine = $lines[0] ?? '';
                    $subjectLine = $lines[1] ?? '';
                    
                    if (preg_match('/Email sent on (.+):/', $dateLine, $matches)) {
                        $communications->push([
                            'type' => 'email_sent',
                            'date' => Carbon::parse($matches[1]),
                            'description' => 'Email sent to client',
                            'details' => $subjectLine,
                        ]);
                    }
                }
            }
        }
        
        // Sort by date
        $communications = $communications->sortBy('date');
        
        return view('admin.quotations.communications', compact('quotation', 'communications'));
    }

    /**
     * Public thank you page after quotation submission
     */
    public function thankYou()
    {
        $quotationData = session('quotation_success');
        
        if (!$quotationData) {
            return redirect()->route('quotation.create')
                ->with('error', 'No quotation data found. Please submit a new quotation request.');
        }
        
        // Clear the session data so page can't be refreshed
        session()->forget('quotation_success');
        
        return view('pages.quotation-thank-you', compact('quotationData'));
    }

    /**
     * Get quotation counts for dashboard widgets
     */
    public function getCounts()
    {
        return response()->json([
            'total' => Quotation::count(),
            'pending' => Quotation::where('status', 'pending')->count(),
            'approved' => Quotation::where('status', 'approved')->count(),
            'needs_attention' => Quotation::where('status', 'pending')
                ->where(function($query) {
                    $query->whereIn('priority', ['high', 'urgent'])
                          ->orWhere('created_at', '<', Carbon::now()->subDays(3));
                })
                ->count(),
            'today' => Quotation::whereDate('created_at', Carbon::today())->count(),
        ]);
    }
    public function convertToProject(Request $request, Quotation $quotation)
{
    // Validate quotation eligibility
    if ($quotation->status !== 'approved') {
        return redirect()->back()
            ->with('error', 'Only approved quotations can be converted to projects.');
    }

    if ($quotation->project_created) {
        $existingProject = Project::where('quotation_id', $quotation->id)->first();
        if ($existingProject) {
            return redirect()->route('admin.projects.show', $existingProject)
                ->with('info', 'This quotation has already been converted to a project.');
        }
    }

    // Validate additional project data if provided
    $projectData = $request->validate([
        'project_title' => 'nullable|string|max:255',
        'project_category_id' => 'nullable|exists:project_categories,id',
        'project_description' => 'nullable|string',
        'estimated_completion_date' => 'nullable|date|after:today',
        'budget' => 'nullable|numeric|min:0',
        'priority' => 'nullable|in:low,normal,high,urgent',
        'copy_attachments' => 'boolean',
        'create_initial_milestone' => 'boolean',
        'notify_client' => 'boolean',
    ]);

    try {
        DB::beginTransaction();

        // Prepare project data using existing quotation fields
        $defaultProjectData = [
            'title' => $projectData['project_title'] ?? $quotation->project_type ?? 'Project from Quotation #' . $quotation->id,
            'description' => $projectData['project_description'] ?? $quotation->requirements ?? '',
            'client_id' => $quotation->client_id,
            'quotation_id' => $quotation->id,
            'location' => $quotation->location,
            'status' => 'planning',
            'start_date' => $quotation->start_date,
            'year' => $quotation->start_date ? $quotation->start_date->year : now()->year,
            'featured' => false,
        ];

        // Add optional fields only if they exist in projects table
        if (Schema::hasColumn('projects', 'project_category_id') && !empty($projectData['project_category_id'])) {
            $defaultProjectData['project_category_id'] = $projectData['project_category_id'];
        }

        if (Schema::hasColumn('projects', 'service_id') && $quotation->service_id) {
            $defaultProjectData['service_id'] = $quotation->service_id;
        }

        if (Schema::hasColumn('projects', 'estimated_completion_date') && !empty($projectData['estimated_completion_date'])) {
            $defaultProjectData['estimated_completion_date'] = $projectData['estimated_completion_date'];
        }

        if (Schema::hasColumn('projects', 'budget') && !empty($projectData['budget'])) {
            $defaultProjectData['budget'] = $projectData['budget'];
        }

        if (Schema::hasColumn('projects', 'priority')) {
            $defaultProjectData['priority'] = $projectData['priority'] ?? $quotation->priority ?? 'normal';
        }

        if (Schema::hasColumn('projects', 'client_name') && !$quotation->client_id) {
            $defaultProjectData['client_name'] = $quotation->name;
        }

        if (Schema::hasColumn('projects', 'short_description')) {
            $defaultProjectData['short_description'] = Str::limit($quotation->requirements ?? '', 200);
        }

        if (Schema::hasColumn('projects', 'is_active')) {
            $defaultProjectData['is_active'] = true;
        }

        // Generate unique slug
        $baseSlug = Str::slug($defaultProjectData['title']);
        $slug = $baseSlug;
        $counter = 1;
        while (Project::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        $defaultProjectData['slug'] = $slug;

        // Filter to only existing project columns
        $projectColumns = Schema::getColumnListing('projects');
        $defaultProjectData = array_intersect_key($defaultProjectData, array_flip($projectColumns));

        // Create the project
        $project = Project::create($defaultProjectData);

        // Copy attachments if requested and if project files system exists
        if ($request->boolean('copy_attachments', false) && Schema::hasTable('project_files')) {
            $this->copyAttachmentsToProject($quotation, $project);
        }

        // Create initial milestone if requested and if milestone system exists
        if ($request->boolean('create_initial_milestone', true) && Schema::hasTable('project_milestones')) {
            $this->createInitialMilestone($project, $quotation);
        }

        // Update quotation using existing fields
        $quotation->update([
            'project_created' => true,
            'project_created_at' => now(),
            'admin_notes' => ($quotation->admin_notes ? $quotation->admin_notes . "\n\n" : '') 
                . "Converted to project: " . $project->title . " on " . now()->format('Y-m-d H:i:s')
        ]);

        // Send notifications if requested
        if ($request->boolean('notify_client', false)) {
            try {
                if ($quotation->client) {
                    // You can add notification logic here if you have a notification system
                    Log::info('Project conversion notification sent to client', [
                        'quotation_id' => $quotation->id,
                        'project_id' => $project->id,
                        'client_email' => $quotation->client->email
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to send conversion notifications: ' . $e->getMessage());
            }
        }

        DB::commit();

        return redirect()->route('admin.projects.show', $project)
            ->with('success', 'Quotation successfully converted to project: ' . $project->title);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Failed to convert quotation to project: ' . $e->getMessage(), [
            'quotation_id' => $quotation->id
        ]);

        return redirect()->back()
            ->with('error', 'Failed to convert quotation to project. Please try again.');
    }
}

/**
 * Quick convert quotation to project with minimal data
 */
public function quickConvertToProject(Quotation $quotation)
{
    if ($quotation->status !== 'approved') {
        return response()->json([
            'success' => false,
            'message' => 'Only approved quotations can be converted to projects.'
        ], 422);
    }

    if ($quotation->project_created) {
        return response()->json([
            'success' => false,
            'message' => 'This quotation has already been converted to a project.'
        ], 422);
    }

    try {
        DB::beginTransaction();

        // Create project with minimal data from quotation
        $projectData = [
            'title' => $quotation->project_type ?? 'Project from Quotation #' . $quotation->id,
            'description' => $quotation->requirements ?? 'Project created from quotation request',
            'client_id' => $quotation->client_id,
            'quotation_id' => $quotation->id,
            'location' => $quotation->location,
            'status' => 'planning',
            'start_date' => $quotation->start_date,
            'year' => $quotation->start_date ? $quotation->start_date->year : now()->year,
            'featured' => false,
            'priority' => $quotation->priority ?? 'normal',
        ];

        // Add optional fields only if they exist in projects table
        if (Schema::hasColumn('projects', 'service_id') && $quotation->service_id) {
            $projectData['service_id'] = $quotation->service_id;
        }

        if (Schema::hasColumn('projects', 'client_name') && !$quotation->client_id) {
            $projectData['client_name'] = $quotation->name;
        }

        if (Schema::hasColumn('projects', 'short_description')) {
            $projectData['short_description'] = Str::limit($quotation->requirements ?? '', 200);
        }

        if (Schema::hasColumn('projects', 'is_active')) {
            $projectData['is_active'] = true;
        }

        // Generate unique slug
        $baseSlug = Str::slug($projectData['title']);
        $slug = $baseSlug;
        $counter = 1;
        while (Project::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        $projectData['slug'] = $slug;

        // Filter to only existing columns
        $projectColumns = Schema::getColumnListing('projects');
        $projectData = array_intersect_key($projectData, array_flip($projectColumns));

        $project = Project::create($projectData);

        // Update quotation using existing fields
        $quotation->update([
            'project_created' => true,
            'project_created_at' => now(),
            'admin_notes' => ($quotation->admin_notes ? $quotation->admin_notes . "\n\n" : '') 
                . "Project created: " . $project->title . " on " . now()->format('Y-m-d H:i:s')
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Quotation successfully converted to project!',
            'project_url' => route('admin.projects.show', $project),
            'project_title' => $project->title
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Quick convert failed: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Failed to convert quotation to project: ' . $e->getMessage()
        ], 500);
    }
}
private function copyAttachmentsToProject(Quotation $quotation, Project $project): int
{
    $copiedCount = 0;

    if (!Schema::hasTable('project_files') || $quotation->attachments->count() === 0) {
        return $copiedCount;
    }

    foreach ($quotation->attachments as $attachment) {
        try {
            // Generate new file path for project
            $originalPath = $attachment->file_path;
            $fileName = pathinfo($attachment->file_name, PATHINFO_FILENAME);
            $extension = pathinfo($attachment->file_name, PATHINFO_EXTENSION);
            $newFileName = $fileName . '_from_quotation_' . $quotation->id . '.' . $extension;
            $newPath = 'project_files/' . $project->id . '/' . $newFileName;

            // Copy file if it exists
            if (Storage::disk('public')->exists($originalPath)) {
                // Ensure directory exists
                $directory = dirname($newPath);
                if (!Storage::disk('public')->exists($directory)) {
                    Storage::disk('public')->makeDirectory($directory);
                }

                // Copy the file
                Storage::disk('public')->copy($originalPath, $newPath);

                // Create project file record - adjust fields based on your project_files table structure
                $fileData = [
                    'file_path' => $newPath,
                    'file_name' => $newFileName,
                    'file_type' => $attachment->file_type,
                    'file_size' => $attachment->file_size,
                    'uploaded_by' => auth()->id(),
                    'description' => 'Transferred from quotation #' . $quotation->id,
                ];

                // Add optional fields if they exist in project_files table
                if (Schema::hasColumn('project_files', 'original_name')) {
                    $fileData['original_name'] = $attachment->file_name;
                }
                if (Schema::hasColumn('project_files', 'is_public')) {
                    $fileData['is_public'] = false;
                }
                if (Schema::hasColumn('project_files', 'category')) {
                    $fileData['category'] = 'quotation_transfer';
                }

                $project->files()->create($fileData);
                $copiedCount++;
            }
        } catch (\Exception $e) {
            Log::warning('Failed to copy attachment from quotation to project', [
                'quotation_id' => $quotation->id,
                'project_id' => $project->id,
                'attachment_id' => $attachment->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    return $copiedCount;
}
private function createInitialMilestone(Project $project, Quotation $quotation): bool
{
    if (!Schema::hasTable('project_milestones')) {
        return false;
    }
    
    try {
        $milestoneData = [
            'title' => 'Project Initiation',
            'description' => 'Initial project setup and planning phase from quotation #' . $quotation->id,
            'due_date' => now()->addWeeks(2),
            'status' => 'pending',
            'sort_order' => 1,
        ];
        
        // Add optional fields if they exist
        if (Schema::hasColumn('project_milestones', 'progress_percent')) {
            $milestoneData['progress_percent'] = 0;
        }
        if (Schema::hasColumn('project_milestones', 'is_critical')) {
            $milestoneData['is_critical'] = true;
        }
        if (Schema::hasColumn('project_milestones', 'created_by')) {
            $milestoneData['created_by'] = auth()->id();
        }
        
        $project->milestones()->create($milestoneData);
        return true;
    } catch (\Exception $e) {
        Log::warning('Failed to create initial milestone: ' . $e->getMessage());
        return false;
    }
}

/**
 * Show conversion form for quotation to project
 */
public function showConversionForm(Quotation $quotation)
{
    // Validate quotation eligibility
    if ($quotation->status !== 'approved') {
        return redirect()->back()
            ->with('error', 'Only approved quotations can be converted to projects.');
    }

    if ($quotation->project_created) {
        $existingProject = Project::where('quotation_id', $quotation->id)->first();
        if ($existingProject) {
            return redirect()->route('admin.projects.show', $existingProject)
                ->with('info', 'This quotation has already been converted to a project.');
        }
    }

    // Get project categories if the table exists
    $categories = collect();
    if (Schema::hasTable('project_categories')) {
        $categories = ProjectCategory::where('is_active', true)->orderBy('name')->get();
    }
    
    // Pre-populate suggested data
    $suggestedData = [
        'title' => $quotation->project_type ?? 'Project from Quotation #' . $quotation->id,
        'description' => $quotation->requirements,
        'location' => $quotation->location,
        'start_date' => $quotation->start_date?->format('Y-m-d'),
        'budget' => $this->extractBudgetFromText($quotation->estimated_cost ?? $quotation->budget_range),
        'priority' => $quotation->priority ?? 'normal',
    ];

    return view('admin.quotations.convert-to-project', compact(
        'quotation',
        'categories', 
        'suggestedData'
    ));
}

/**
 * Get conversion statistics
 */
public function getConversionStatistics()
{
    $stats = Quotation::getConversionMetrics();
    
    // Add additional statistics
    $stats['ready_for_conversion'] = Quotation::where('status', 'approved')
        ->where('project_created', false)
        ->count();
    
    $stats['conversion_by_month'] = Quotation::where('project_created', true)
        ->selectRaw('YEAR(project_created_at) as year, MONTH(project_created_at) as month, COUNT(*) as count')
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->limit(12)
        ->get();

    return response()->json($stats);
}

/**
 * Bulk convert quotations to projects
 */
public function bulkConvertToProjects(Request $request)
{
    $request->validate([
        'quotation_ids' => 'required|string',
        'notify_clients' => 'boolean',
        'create_milestones' => 'boolean',
        'copy_attachments' => 'boolean',
    ]);

    // Parse comma-separated IDs
    $quotationIds = array_filter(explode(',', $request->quotation_ids));
    
    if (empty($quotationIds)) {
        return redirect()->back()->with('error', 'No quotations selected.');
    }

    try {
        $results = Quotation::bulkConvertToProjects($quotationIds);
        
        $successCount = count($results['successful']);
        $failedCount = count($results['failed']);
        $skippedCount = count($results['skipped']);
        
        $message = "Conversion completed: {$successCount} successful";
        if ($failedCount > 0) {
            $message .= ", {$failedCount} failed";
        }
        if ($skippedCount > 0) {
            $message .= ", {$skippedCount} skipped";
        }
        $message .= ".";
        
        if ($failedCount > 0 || $skippedCount > 0) {
            session()->flash('conversion_details', $results);
        }
        
        return redirect()->back()->with('success', $message);
        
    } catch (\Exception $e) {
        Log::error('Bulk conversion failed: ' . $e->getMessage());
        
        return redirect()->back()
            ->with('error', 'Bulk conversion failed. Please try again.');
    }
}

/**
 * Get quotations ready for conversion (AJAX endpoint)
 */
public function getReadyForConversion(Request $request)
{
    $quotations = Quotation::where('status', 'approved')
        ->where('project_created', false)
        ->with(['service', 'client'])
        ->orderBy('approved_at', 'asc')
        ->paginate($request->get('per_page', 15));

    return response()->json([
        'success' => true,
        'quotations' => $quotations->map(function ($quotation) {
            return [
                'id' => $quotation->id,
                'name' => $quotation->name,
                'project_type' => $quotation->project_type,
                'service' => $quotation->service?->title,
                'budget_range' => $quotation->budget_range,
                'created_at' => $quotation->created_at->format('Y-m-d'),
                'approved_at' => $quotation->approved_at->format('Y-m-d'),
                'readiness_score' => $quotation->getConversionReadinessScore(),
                'conversion_status' => $quotation->getConversionStatusText(),
                'actions' => $quotation->getConversionActions(),
            ];
        }),
        'pagination' => [
            'current_page' => $quotations->currentPage(),
            'last_page' => $quotations->lastPage(),
            'per_page' => $quotations->perPage(),
            'total' => $quotations->total(),
        ]
    ]);
}
}