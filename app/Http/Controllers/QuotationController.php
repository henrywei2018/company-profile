<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\QuotationAttachment;
use App\Models\Service;
use App\Models\User;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Services\QuotationService;
use App\Services\ProjectService;
use App\Services\TempNotifiable;
use App\Facades\Notifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;

class QuotationController extends Controller
{
    protected $quotationService;
    protected $projectService;

    public function __construct(QuotationService $quotationService = null, ProjectService $projectService = null)
    {
        $this->quotationService = $quotationService;
        $this->projectService = $projectService;
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

                return match ($range) {
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
            })
            ->when($request->filled('has_project'), function ($q) use ($request) {
                if ($request->has_project === '1') {
                    return $q->where('project_created', true);
                } elseif ($request->has_project === '0') {
                    return $q->where('project_created', false);
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
            'projects_created' => Quotation::where('project_created', true)->count(),
            'ready_for_project' => Quotation::where('status', 'approved')
                ->where('client_approved', true)
                ->where('project_created', false)
                ->count(),
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
            ->where(function ($query) {
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
                    $notificationType = match ($validated['status']) {
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

        // Check if project exists for this quotation
        $existingProject = null;
        if ($quotation->project_created && Schema::hasColumn('projects', 'quotation_id')) {
            $existingProject = Project::where('quotation_id', $quotation->id)->first();
        }

        return view('admin.quotations.show', compact(
            'quotation',
            'relatedQuotations',
            'existingProject'
        ));
    }

    /**
     * Convert quotation to project with comprehensive data mapping
     */
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
            'create_immediately' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Prepare project data from quotation
            $defaultProjectData = [
                'title' => $projectData['project_title'] ?? $quotation->project_type ?? 'Project from Quotation #' . $quotation->id,
                'description' => $projectData['project_description'] ?? $quotation->requirements ?? '',
                'short_description' => Str::limit($quotation->requirements ?? '', 200),
                'client_id' => $quotation->client_id,
                'quotation_id' => $quotation->id,
                'location' => $quotation->location,
                'status' => 'planning',
                'start_date' => $quotation->start_date,
                'year' => $quotation->start_date ? $quotation->start_date->year : now()->year,
                'featured' => false,
                'is_active' => true,
            ];

            // Add service mapping if exists
            if ($quotation->service_id && Schema::hasColumn('projects', 'service_id')) {
                $defaultProjectData['service_id'] = $quotation->service_id;
            }

            // Add category if provided or try to map from service
            if (!empty($projectData['project_category_id'])) {
                $defaultProjectData['project_category_id'] = $projectData['project_category_id'];
            } elseif ($quotation->service && Schema::hasColumn('projects', 'project_category_id')) {
                // Try to find a matching category based on service
                $category = ProjectCategory::where('name', 'like', '%' . $quotation->service->title . '%')
                    ->orWhere('name', 'like', '%' . $quotation->project_type . '%')
                    ->first();
                if ($category) {
                    $defaultProjectData['project_category_id'] = $category->id;
                }
            }

            // Add estimated completion date
            if (!empty($projectData['estimated_completion_date'])) {
                $defaultProjectData['estimated_completion_date'] = $projectData['estimated_completion_date'];
            } elseif ($quotation->start_date) {
                // Estimate 3 months from start date if no specific date provided
                $defaultProjectData['estimated_completion_date'] = $quotation->start_date->addMonths(3);
            }

            // Add budget information
            if (!empty($projectData['budget'])) {
                $defaultProjectData['budget'] = $projectData['budget'];
            } elseif ($quotation->estimated_cost) {
                // Try to extract numeric value from estimated cost
                $numericCost = preg_replace('/[^\d.]/', '', $quotation->estimated_cost);
                if (is_numeric($numericCost)) {
                    $defaultProjectData['budget'] = floatval($numericCost);
                }
            }

            // Add priority
            if (!empty($projectData['priority'])) {
                $defaultProjectData['priority'] = $projectData['priority'];
            } else {
                // Map quotation priority to project priority
                $defaultProjectData['priority'] = $quotation->priority ?? 'normal';
            }

            // Add client name if no client_id but we have client info
            if (!$quotation->client_id && Schema::hasColumn('projects', 'client_name')) {
                $defaultProjectData['client_name'] = $quotation->name;
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

            // Copy attachments from quotation to project if applicable
            if ($quotation->attachments()->count() > 0 && Schema::hasTable('project_files')) {
                foreach ($quotation->attachments as $attachment) {
                    try {
                        // Copy file to project directory
                        $originalPath = $attachment->file_path;
                        $newPath = 'project_files/' . $project->id . '/' . basename($originalPath);

                        if (Storage::disk('public')->exists($originalPath)) {
                            Storage::disk('public')->copy($originalPath, $newPath);

                            // Create project file record
                            $project->files()->create([
                                'file_path' => $newPath,
                                'file_name' => $attachment->file_name,
                                'file_type' => $attachment->file_type,
                                'file_size' => $attachment->file_size,
                                'uploaded_by' => auth()->id(),
                                'is_public' => false,
                                'description' => 'Transferred from quotation #' . $quotation->id,
                            ]);
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
            }

            // Update quotation to mark project as created
            $quotationUpdateData = [
                'project_created' => true,
                'project_created_at' => now(),
            ];

            // Add admin notes about project creation
            if ($quotation->admin_notes) {
                $quotationUpdateData['admin_notes'] = $quotation->admin_notes . "\n\n"
                    . "Project created: " . $project->title . " on " . now()->format('Y-m-d H:i:s');
            } else {
                $quotationUpdateData['admin_notes'] = "Project created: " . $project->title . " on " . now()->format('Y-m-d H:i:s');
            }

            $quotation->update($quotationUpdateData);

            // Send notifications
            try {
                // Notify admin about successful conversion
                Notifications::send('quotation.converted', [
                    'quotation' => $quotation,
                    'project' => $project
                ]);

                // Notify client if they have an account
                if ($quotation->client) {
                    Notifications::send('project.created', $project, $quotation->client);
                }

                Log::info('Quotation converted to project successfully', [
                    'quotation_id' => $quotation->id,
                    'project_id' => $project->id,
                    'project_title' => $project->title
                ]);

            } catch (\Exception $e) {
                Log::error('Failed to send conversion notifications: ' . $e->getMessage());
            }

            DB::commit();

            // Create initial milestone if requested
            if ($request->boolean('create_initial_milestone', true)) {
                try {
                    $project->milestones()->create([
                        'title' => 'Project Initiation',
                        'description' => 'Initial project setup and planning phase',
                        'due_date' => now()->addWeeks(2),
                        'status' => 'pending',
                        'progress_percent' => 0,
                        'sort_order' => 1,
                        'is_critical' => true,
                    ]);
                } catch (\Exception $e) {
                    Log::warning('Failed to create initial milestone: ' . $e->getMessage());
                }
            }

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

        $categories = ProjectCategory::where('is_active', true)->orderBy('name')->get();

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

            // Create project with minimal data
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
                'is_active' => true,
                'priority' => $quotation->priority ?? 'normal',
            ];

            // Add service mapping if exists
            if ($quotation->service_id && Schema::hasColumn('projects', 'service_id')) {
                $projectData['service_id'] = $quotation->service_id;
            }

            // Add client name if no client_id
            if (!$quotation->client_id && Schema::hasColumn('projects', 'client_name')) {
                $projectData['client_name'] = $quotation->name;
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

            // Update quotation
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
                'message' => 'Failed to convert quotation to project.'
            ], 500);
        }
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
                    $notificationType = match ($validated['status']) {
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
                    $notificationType = match ($request->status) {
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
     * Bulk actions with centralized notifications
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,delete,change_status,convert_to_projects',
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
                        $quotation->update([
                            'status' => 'approved',
                            'approved_at' => now(),
                            'last_communication_at' => now()
                        ]);

                        if ($sendNotifications) {
                            $this->sendQuotationNotification($quotation, 'quotation.approved');
                        }
                    }
                    $message = "{$count} quotation(s) approved successfully!";
                    break;

                case 'reject':
                    foreach ($quotations as $quotation) {
                        $quotation->update([
                            'status' => 'rejected',
                            'last_communication_at' => now()
                        ]);

                        if ($sendNotifications) {
                            $this->sendQuotationNotification($quotation, 'quotation.rejected');
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

                        if ($sendNotifications && $oldStatus !== $request->new_status) {
                            $notificationType = match ($request->new_status) {
                                'approved' => 'quotation.approved',
                                'rejected' => 'quotation.rejected',
                                default => 'quotation.status_updated'
                            };

                            $this->sendQuotationNotification($quotation, $notificationType);
                        }
                    }
                    $message = "{$count} quotation(s) status updated successfully!";
                    break;

                case 'convert_to_projects':
                    $convertedCount = 0;
                    $skippedCount = 0;

                    foreach ($quotations as $quotation) {
                        if ($quotation->status === 'approved' && !$quotation->project_created) {
                            try {
                                $this->performQuickConversion($quotation);
                                $convertedCount++;
                            } catch (\Exception $e) {
                                Log::error("Failed to convert quotation {$quotation->id}: " . $e->getMessage());
                                $skippedCount++;
                            }
                        } else {
                            $skippedCount++;
                        }
                    }

                    $message = "{$convertedCount} quotation(s) converted to projects successfully!";
                    if ($skippedCount > 0) {
                        $message .= " {$skippedCount} quotation(s) were skipped (not approved or already converted).";
                    }
                    break;

                case 'delete':
                    foreach ($quotations as $quotation) {
                        // Delete attachments first
                        foreach ($quotation->attachments as $attachment) {
                            Storage::disk('public')->delete($attachment->file_path);
                            $attachment->delete();
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

        $callback = function () use ($quotations) {
            $file = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($file, [
                'ID',
                'Name',
                'Email',
                'Company',
                'Phone',
                'Service',
                'Project Type',
                'Location',
                'Budget Range',
                'Status',
                'Priority',
                'Client Approved',
                'Project Created',
                'Created At',
                'Updated At'
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
                    $quotation->project_created ? 'Yes' : 'No',
                    $quotation->created_at->format('Y-m-d H:i:s'),
                    $quotation->updated_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Delete quotation
     */
    public function destroy(Quotation $quotation)
    {
        try {
            DB::beginTransaction();

            // Check if quotation has been converted to project
            if ($quotation->project_created) {
                $project = Project::where('quotation_id', $quotation->id)->first();
                if ($project) {
                    return redirect()->back()
                        ->with('error', 'Cannot delete quotation that has been converted to a project. Delete the project first.');
                }
            }

            // Delete attachments
            foreach ($quotation->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
                $attachment->delete();
            }

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
            'projects_created' => Quotation::where('project_created', true)->count(),
            'conversion_rate' => $this->calculateConversionRate(),
            'needs_attention' => Quotation::where('status', 'pending')
                ->where(function ($query) {
                    $query->whereIn('priority', ['high', 'urgent'])
                        ->orWhere('created_at', '<', Carbon::now()->subDays(3));
                })
                ->count(),
            'monthly_stats' => $this->getMonthlyStats(),
            'service_stats' => $this->getServiceStats(),
        ];

        return response()->json($stats);
    }

    // Helper Methods

    /**
     * Send notification for quotation
     */
    private function sendQuotationNotification(Quotation $quotation, string $type)
    {
        try {
            $clientNotifiable = $quotation->client
                ? $quotation->client
                : TempNotifiable::forQuotation($quotation->email, $quotation->name);

            Notifications::send($type, $quotation, $clientNotifiable);
        } catch (\Exception $e) {
            Log::error("Failed to send notification {$type} for quotation {$quotation->id}");
        }
    }

    /**
     * Perform quick conversion without form validation
     */
    private function performQuickConversion(Quotation $quotation)
    {
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
            'is_active' => true,
            'priority' => $quotation->priority ?? 'normal',
        ];

        // Add service mapping if exists
        if ($quotation->service_id && Schema::hasColumn('projects', 'service_id')) {
            $projectData['service_id'] = $quotation->service_id;
        }

        // Add client name if no client_id
        if (!$quotation->client_id && Schema::hasColumn('projects', 'client_name')) {
            $projectData['client_name'] = $quotation->name;
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

        // Update quotation
        $quotation->update([
            'project_created' => true,
            'project_created_at' => now(),
        ]);

        return $project;
    }

    /**
     * Extract numeric budget from text
     */
    private function extractBudgetFromText(?string $text): ?float
    {
        if (!$text)
            return null;

        // Remove currency symbols and extract numbers
        $numbers = preg_replace('/[^\d.,]/', '', $text);
        $numbers = str_replace(',', '', $numbers);

        if (is_numeric($numbers)) {
            return floatval($numbers);
        }

        return null;
    }

    /**
     * Calculate conversion rate from quotations to projects
     */
    private function calculateConversionRate(): float
    {
        $totalQuotations = Quotation::count();
        if ($totalQuotations === 0)
            return 0;

        $convertedQuotations = Quotation::where('project_created', true)->count();
        return round(($convertedQuotations / $totalQuotations) * 100, 1);
    }

    /**
     * Get monthly statistics
     */
    private function getMonthlyStats(): array
    {
        return Quotation::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->toArray();
    }

    /**
     * Get service statistics
     */
    private function getServiceStats(): array
    {
        return Quotation::join('services', 'quotations.service_id', '=', 'services.id')
            ->selectRaw('services.title, COUNT(*) as count')
            ->groupBy('services.title', 'services.id')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
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
}