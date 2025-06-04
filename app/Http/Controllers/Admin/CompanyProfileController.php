<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\Certification;
use App\Services\CompanyProfileService;
use App\Facades\Notifications;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CompanyProfileController extends Controller
{
    protected CompanyProfileService $companyProfileService;

    public function __construct(CompanyProfileService $companyProfileService)
    {
        $this->companyProfileService = $companyProfileService;
    }

    /**
     * Display the company profile overview.
     */
    public function index()
    {
        try {
            $companyProfile = $this->companyProfileService->getProfile();
            $statistics = $this->companyProfileService->getStatistics();

            return view('admin.company.index', compact('companyProfile', 'statistics'));
        } catch (\Exception $e) {
            Log::error('Failed to load company profile: ' . $e->getMessage());

            // Create default profile if none exists
            try {
                $companyProfile = CompanyProfile::getInstance();
                $statistics = $this->companyProfileService->getStatistics();

                return view('admin.company.index', compact('companyProfile', 'statistics'))
                    ->with('warning', 'Company profile was created with default values. Please update your information.');
            } catch (\Exception $e2) {
                Log::error('Failed to create default company profile: ' . $e2->getMessage());
                return redirect()->back()->with('error', 'Unable to load company profile.');
            }
        }
    }

    /**
     * Show the form for editing the company profile.
     */
    public function edit()
    {
        try {
            $companyProfile = $this->companyProfileService->getProfile();

            // Ensure values is always an array for the form
            if (empty($companyProfile->values) || !is_array($companyProfile->values)) {
                $companyProfile->values = [''];
            }

            return view('admin.company.edit', compact('companyProfile'));
        } catch (\Exception $e) {
            Log::error('Failed to load company profile edit form: ' . $e->getMessage());
            return redirect()->route('admin.company.index')
                ->with('error', 'Failed to load company profile edit form.');
        }
    }

    /**
     * Update the company profile - FIXED for type safety
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'about' => 'nullable|string|max:500',
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:25',
            'address' => 'required|string',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'facebook' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'linkedin' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            'whatsapp' => 'nullable|string|max:25',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'values' => 'nullable|array',
            'values.*' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            // Handle logo upload directly in controller
            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');

                // Validate
                if (!$logo->isValid()) {
                    throw new \Exception('Invalid logo file uploaded.');
                }

                // Get current profile to delete old logo
                $currentProfile = $this->companyProfileService->getProfile();
                if (!empty($currentProfile->logo)) {
                    Storage::disk('public')->delete($currentProfile->logo);
                }

                // Store new logo
                $filename = time() . '_logo.' . $logo->getClientOriginalExtension();
                $logoPath = Storage::disk('public')->putFileAs('company/logos', $logo, $filename);
                $validated['logo'] = $logoPath;

                Log::info('Logo uploaded in controller', ['path' => $logoPath]);
            }

            // Process values array
            if (isset($validated['values']) && is_array($validated['values'])) {
                $validated['values'] = array_values(array_filter($validated['values'], function ($value) {
                    return !empty(trim($value ?? ''));
                }));

                if (empty($validated['values'])) {
                    $validated['values'] = null;
                }
            }

            // Clean optional fields
            $optionalFields = [
                'tagline',
                'about',
                'vision',
                'mission',
                'city',
                'postal_code',
                'country',
                'facebook',
                'twitter',
                'instagram',
                'linkedin',
                'youtube',
                'whatsapp',
                'latitude',
                'longitude'
            ];

            foreach ($optionalFields as $field) {
                if (isset($validated[$field]) && is_string($validated[$field]) && empty(trim($validated[$field]))) {
                    $validated[$field] = null;
                }
            }

            // Convert coordinates to strings
            if (isset($validated['latitude']) && is_numeric($validated['latitude'])) {
                $validated['latitude'] = (string) $validated['latitude'];
            }

            if (isset($validated['longitude']) && is_numeric($validated['longitude'])) {
                $validated['longitude'] = (string) $validated['longitude'];
            }

            // Update profile (pass null for logo since we handled it above)
            $companyProfile = $this->companyProfileService->updateProfile(
                $validated,
                null, // Don't pass logo file to service
                null
            );

            Log::info('Company profile updated successfully', [
                'company_id' => $companyProfile->id,
                'updated_by' => auth()->id(),
                'fields_updated' => array_keys($validated)
            ]);

            // Try to send notification
            try {
                Notifications::send('company.profile_updated', $companyProfile);
            } catch (\Exception $e) {
                Log::warning('Failed to send notification: ' . $e->getMessage());
            }

            return redirect()->route('admin.company.index')
                ->with('success', 'Company profile updated successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to update company profile: ' . $e->getMessage(), [
                'request_data' => $request->except(['logo']),
                'error_trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update company profile: ' . $e->getMessage());
        }
    }

    /**
     * Show the SEO settings form.
     */
    public function seo()
    {
        try {
            $companyProfile = $this->companyProfileService->getProfile();
            $seo = $companyProfile->getSeoData();

            return view('admin.company.seo', compact('companyProfile', 'seo'));
        } catch (\Exception $e) {
            Log::error('Failed to load SEO settings: ' . $e->getMessage());
            return redirect()->route('admin.company.index')
                ->with('error', 'Failed to load SEO settings.');
        }
    }

    /**
     * Update the SEO information.
     */
    public function updateSeo(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:60',
            'description' => 'nullable|string|max:160',
            'keywords' => 'nullable|string|max:255',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            foreach (['title', 'description', 'keywords'] as $field) {
                if (isset($validated[$field]) && is_string($validated[$field]) && empty(trim($validated[$field]))) {
                    $validated[$field] = null;
                }
            }

            $companyProfile = $this->companyProfileService->updateSeo(
                $validated,
                $request->file('og_image')
            );

            Log::info('Company SEO updated successfully', [
                'company_id' => $companyProfile->id,
                'updated_by' => auth()->id()
            ]);

            return redirect()->route('admin.company.seo')
                ->with('success', 'SEO information updated successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to update SEO information: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update SEO information. Please try again.');
        }
    }

    /**
     * Show the certificates management page - FIXED to not use relationship
     */
    public function certificates()
    {
        try {
            $companyProfile = $this->companyProfileService->getProfile();

            // Get certificates directly from model, not through relationship
            $certificates = Certification::orderBy('created_at', 'desc')->get();

            // Alternative queries with specific criteria:
            // $certificates = Certification::where('status', 'active')->orderBy('sort_order')->get();
            // $certificates = Certification::whereNotNull('certificate_file')->orderBy('issue_date', 'desc')->get();

            return view('admin.company.certificates', compact('companyProfile', 'certificates'));
        } catch (\Exception $e) {
            Log::error('Failed to load certificates: ' . $e->getMessage());
            return redirect()->route('admin.company.index')
                ->with('error', 'Failed to load certificates.');
        }
    }

    /**
     * Display detailed company profile information.
     */
    public function show()
    {
        try {
            $companyProfile = $this->companyProfileService->getProfile();
            $statistics = $this->companyProfileService->getStatistics();
            $seo = $companyProfile->getSeoData();

            return view('admin.company.show', compact('companyProfile', 'statistics', 'seo'));
        } catch (\Exception $e) {
            Log::error('Failed to show company profile details: ' . $e->getMessage());
            return redirect()->route('admin.company.index')
                ->with('error', 'Failed to load company profile details.');
        }
    }

    /**
     * Export company profile data.
     */
    public function export()
    {
        try {
            $data = $this->companyProfileService->exportProfileData();

            return response()->json($data)
                ->header('Content-Disposition', 'attachment; filename="company-profile-export-' . now()->format('Y-m-d') . '.json"');

        } catch (\Exception $e) {
            Log::error('Failed to export company profile: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to export company profile data.');
        }
    }
    public function exportPdf(Request $request)
    {
        try {
            $companyProfile = $this->companyProfileService->getProfile();
            $statistics = $this->companyProfileService->getStatistics();
            $seo = $companyProfile->getSeoData();
            $certificates = Certification::orderBy('created_at', 'desc')->get();
            
            // Prepare data for PDF
            $data = [
                'company' => $companyProfile,
                'statistics' => $statistics,
                'seo' => $seo,
                'certificates' => $certificates,
                'exportDate' => Carbon::now(),
                'exportedBy' => auth()->user()->name ?? 'System',
                'title' => 'Company Profile Report'
            ];

            // Generate PDF
            $pdf = Pdf::loadView('admin.company.pdf.export', $data)
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'defaultFont' => 'sans-serif'
                ]);

            $filename = 'company-profile-' . now()->format('Y-m-d') . '.pdf';

            Log::info('Company profile PDF exported', [
                'filename' => $filename,
                'exported_by' => auth()->id()
            ]);

            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('Failed to export PDF: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to export PDF. Please try again.');
        }
    }

    /**
     * Stream PDF (view in browser)
     */
    public function streamPdf(Request $request)
    {
        try {
            $companyProfile = $this->companyProfileService->getProfile();
            $statistics = $this->companyProfileService->getStatistics();
            $seo = $companyProfile->getSeoData();
            $certificates = Certification::orderBy('created_at', 'desc')->get();
            
            $data = [
                'company' => $companyProfile,
                'statistics' => $statistics,
                'seo' => $seo,
                'certificates' => $certificates,
                'exportDate' => Carbon::now(),
                'exportedBy' => auth()->user()->name ?? 'System',
                'title' => 'Company Profile Report'
            ];

            $pdf = Pdf::loadView('admin.company.pdf.export', $data)
                ->setPaper('a4', 'portrait');

            return $pdf->stream('company-profile-' . now()->format('Y-m-d') . '.pdf');

        } catch (\Exception $e) {
            Log::error('Failed to stream PDF: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to view PDF. Please try again.');
        }
    }

    /**
     * Export certificates only to PDF
     */
    public function exportCertificatesPdf(Request $request)
    {
        try {
            $companyProfile = $this->companyProfileService->getProfile();
            $certificates = Certification::orderBy('created_at', 'desc')->get();
            
            $data = [
                'company' => $companyProfile,
                'certificates' => $certificates,
                'exportDate' => Carbon::now(),
                'exportedBy' => auth()->user()->name ?? 'System',
                'title' => 'Company Certificates Report'
            ];

            $pdf = Pdf::loadView('admin.company.pdf.certificates', $data)
                ->setPaper('a4', 'portrait');

            $filename = 'company-certificates-' . now()->format('Y-m-d') . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('Failed to export certificates PDF: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to export certificates PDF. Please try again.');
        }
    }

    /**
     * Bulk export options
     */
    public function bulkExport(Request $request)
    {
        $format = $request->get('format', 'pdf');
        
        switch ($format) {
            case 'pdf':
                return $this->exportPdf($request);
            case 'json':
                return $this->export();
            case 'certificates':
                return $this->exportCertificatesPdf($request);
            default:
                return redirect()->back()
                    ->with('error', 'Invalid export format selected.');
        }
    }
}