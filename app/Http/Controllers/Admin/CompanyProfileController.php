<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\Certification;
use App\Services\CompanyProfileService;
use App\Facades\Notifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            // Process values array to remove empty entries - FIXED
            if (isset($validated['values']) && is_array($validated['values'])) {
                $validated['values'] = array_values(array_filter($validated['values'], function($value) {
                    return !empty(trim($value ?? ''));
                }));
                
                if (empty($validated['values'])) {
                    $validated['values'] = null;
                }
            }

            // Convert empty strings to null for optional fields - FIXED
            $optionalFields = ['tagline', 'about', 'vision', 'mission', 'city', 'postal_code', 'country', 
                              'facebook', 'twitter', 'instagram', 'linkedin', 'youtube', 'whatsapp', 
                              'latitude', 'longitude'];
            
            foreach ($optionalFields as $field) {
                if (isset($validated[$field]) && is_string($validated[$field]) && empty(trim($validated[$field]))) {
                    $validated[$field] = null;
                }
            }

            // Convert coordinates to strings if they're numeric - FIXED
            if (isset($validated['latitude']) && is_numeric($validated['latitude'])) {
                $validated['latitude'] = (string) $validated['latitude'];
            }
            
            if (isset($validated['longitude']) && is_numeric($validated['longitude'])) {
                $validated['longitude'] = (string) $validated['longitude'];
            }

            $companyProfile = $this->companyProfileService->updateProfile(
                $validated,
                $request->file('logo'),
                null
            );

            Log::info('Company profile updated successfully', [
                'company_id' => $companyProfile->id,
                'updated_by' => auth()->id(),
                'fields_updated' => array_keys($validated)
            ]);

            // Send notification (with error handling)
            try {
                Notifications::send('company.profile_updated', $companyProfile);
            } catch (\Exception $e) {
                Log::warning('Failed to send company profile update notification: ' . $e->getMessage());
            }

            return redirect()->route('admin.company.index')
                ->with('success', 'Company profile updated successfully.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
            
        } catch (\Exception $e) {
            Log::error('Failed to update company profile: ' . $e->getMessage(), [
                'request_data' => $request->except(['logo']),
                'error_trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update company profile. Please check your data and try again.');
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
}