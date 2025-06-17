<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\CompanyProfile;
use App\Http\Requests\QuotationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Facades\Notifications;
use App\Models\TempNotifiable;

class QuotationController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->shareBaseData();
    }

    /**
     * Show the quotation request form.
     */
    public function create(Request $request)
    {
        // Get all active services for the dropdown
        $services = Service::active()
            ->orderBy('title')
            ->get(['id', 'title', 'short_description']);

        // Get service categories for grouping
        $serviceCategories = ServiceCategory::active()
            ->with(['activeServices' => function ($query) {
                $query->orderBy('title');
            }])
            ->orderBy('name')
            ->get();

        // Pre-select service if coming from service page
        $selectedService = null;
        if ($request->filled('service')) {
            $selectedService = Service::active()
                ->where('slug', $request->service)
                ->orWhere('id', $request->service)
                ->first();
        }

        // Company profile for contact info
        $companyProfile = CompanyProfile::getInstance();

        // SEO Data
        $seoData = [
            'title' => 'Request Quotation - CV Usaha Prima Lestari',
            'description' => 'Request a detailed quotation for your project. Get professional consultation and competitive pricing for our services.',
            'keywords' => 'quotation request, project estimate, consultation, pricing',
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => route('home')],
                ['name' => 'Request Quotation', 'url' => route('quotation.create')]
            ]
        ];

        return view('pages.quotation.create', compact(
            'services',
            'serviceCategories',
            'selectedService',
            'companyProfile',
            'seoData'
        ));
    }

    /**
     * Store a newly created quotation request.
     * Based on actual quotations table fields.
     */
    public function store(QuotationRequest $request)
    {
        try {
            // Create quotation record with actual table fields
            $quotationData = $request->validated();
            
            // Handle file uploads to quotation_attachments table
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('quotations/attachments', $fileName, 'public');
                    
                    $attachments[] = [
                        'file_path' => $filePath,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ];
                }
            }

            // Set fields according to actual table structure
            $quotationData['quotation_number'] = $this->generateQuotationNumber();
            $quotationData['status'] = 'pending';
            $quotationData['priority'] = $this->calculatePriority($quotationData);
            
            // Set client_id if user is authenticated
            if (auth()->check()) {
                $quotationData['client_id'] = auth()->id();
            }

            // Remove fields that don't exist in table
            unset($quotationData['attachments'], $quotationData['terms_accepted'], $quotationData['privacy_accepted']);

            $quotation = Quotation::create($quotationData);

            // Save attachments to separate table
            if (!empty($attachments)) {
                foreach ($attachments as $attachment) {
                    $quotation->attachments()->create($attachment);
                }
            }

            // Send notifications
            $this->sendQuotationNotifications($quotation);

            // Redirect to thank you page
            return redirect()->route('quotation.thank-you', [
                'reference' => $quotation->quotation_number
            ])->with('success', 'Your quotation request has been submitted successfully!');

        } catch (\Exception $e) {
            \Log::error('Quotation submission error: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'There was an error submitting your request. Please try again.']);
        }
    }

    /**
     * Show the thank you page after successful submission.
     */
    public function thankYou(Request $request)
    {
        $reference = $request->get('reference');
        $quotation = null;

        if ($reference) {
            $quotation = Quotation::where('quotation_number', $reference)->first();
        }

        // Company profile for contact info
        $companyProfile = CompanyProfile::getInstance();

        // Get next steps information
        $nextSteps = [
            [
                'step' => 1,
                'title' => 'Review & Analysis',
                'description' => 'Our team will review your requirements and analyze project specifications.',
                'duration' => '24-48 hours'
            ],
            [
                'step' => 2,
                'title' => 'Site Survey (if needed)',
                'description' => 'We may schedule a site visit for accurate measurements and assessment.',
                'duration' => '2-5 days'
            ],
            [
                'step' => 3,
                'title' => 'Quotation Preparation',
                'description' => 'Detailed quotation with breakdown of costs and timeline will be prepared.',
                'duration' => '3-7 days'
            ],
            [
                'step' => 4,
                'title' => 'Quotation Delivery',
                'description' => 'Final quotation will be sent via email and phone consultation if needed.',
                'duration' => 'Within 7 days'
            ]
        ];

        // SEO Data
        $seoData = [
            'title' => 'Thank You - Quotation Request Submitted',
            'description' => 'Thank you for your quotation request. We will review your requirements and get back to you soon.',
            'noindex' => true
        ];

        return view('pages.quotation.thank-you', compact(
            'quotation',
            'companyProfile',
            'nextSteps',
            'seoData'
        ));
    }

    /**
     * Check quotation status (for authenticated users or with reference).
     */
    public function status(Request $request)
    {
        $quotation = null;
        
        if (auth()->check()) {
            // For authenticated users, show their quotations
            $quotation = Quotation::where('client_id', auth()->id())
                ->where('quotation_number', $request->reference)
                ->first();
        } else {
            // For guests, require email verification
            $request->validate([
                'reference' => 'required|string',
                'email' => 'required|email'
            ]);
            
            $quotation = Quotation::where('quotation_number', $request->reference)
                ->where('email', $request->email)
                ->first();
        }

        if (!$quotation) {
            return back()->withErrors(['error' => 'Quotation not found or invalid credentials.']);
        }

        return view('pages.quotation.status', compact('quotation'));
    }

    /**
     * Generate unique quotation number.
     */
    private function generateQuotationNumber(): string
    {
        $prefix = 'QUO';
        $year = date('Y');
        $month = date('m');
        
        // Get the last quotation number for this month
        $lastQuotation = Quotation::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastQuotation ? 
            ((int) substr($lastQuotation->quotation_number, -4)) + 1 : 1;

        return sprintf('%s%s%s%04d', $prefix, $year, $month, $sequence);
    }

    /**
     * Calculate priority based on available data.
     */
    private function calculatePriority(array $data): string
    {
        $priority = 'normal';
        
        // High priority conditions - adjust based on your business logic
        if (
            (isset($data['budget_range']) && strpos(strtolower($data['budget_range']), 'high') !== false) ||
            (isset($data['project_type']) && in_array($data['project_type'], ['emergency', 'urgent']))
        ) {
            $priority = 'high';
        }
        
        // Urgent priority for emergency projects
        if (
            (isset($data['project_type']) && $data['project_type'] === 'emergency') ||
            (isset($data['requirements']) && stripos($data['requirements'], 'urgent') !== false)
        ) {
            $priority = 'urgent';
        }

        return $priority;
    }

    /**
     * Send notifications for new quotation.
     */
    private function sendQuotationNotifications(Quotation $quotation): void
    {
        try {
            // Create temp notifiable for client
            $clientNotifiable = TempNotifiable::forQuotation(
                $quotation->email, 
                $quotation->name
            );

            // Send confirmation to client
            Notifications::send('quotation.received', $quotation, $clientNotifiable);

            // Send notification to admin team
            Notifications::send('quotation.new_request', $quotation);

            \Log::info('Quotation notifications sent successfully', [
                'quotation_id' => $quotation->id,
                'reference' => $quotation->reference_number
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to send quotation notifications: ' . $e->getMessage(), [
                'quotation_id' => $quotation->id ?? 'unknown'
            ]);
        }
    }

    /**
     * Download quotation file (for clients).
     */
    public function downloadQuotation(Request $request, Quotation $quotation)
    {
        // Verify access (either owner or has access token)
        if (!$this->canAccessQuotation($quotation, $request)) {
            abort(403);
        }

        if (!$quotation->quotation_file) {
            abort(404, 'Quotation file not found.');
        }

        $filePath = storage_path('app/private/quotations/' . $quotation->quotation_file);
        
        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath, 'Quotation-' . $quotation->reference_number . '.pdf');
    }

    /**
     * Check if user can access quotation.
     */
    private function canAccessQuotation(Quotation $quotation, Request $request): bool
    {
        // Authenticated user with same email
        if (auth()->check() && auth()->user()->email === $quotation->email) {
            return true;
        }

        // Valid access token
        if ($request->filled('token') && $request->token === $quotation->access_token) {
            return true;
        }

        return false;
    }
}