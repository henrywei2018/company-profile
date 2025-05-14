<?php
// File: app/Http/Controllers/Admin/CompanyProfileController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\FileUploadService;

class CompanyProfileController extends Controller
{
    protected $fileUploadService;

    /**
     * Create a new controller instance.
     *
     * @param FileUploadService $fileUploadService
     */
    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Display the company profile form.
     */
    public function index()
    {
        $companyProfile = CompanyProfile::getInstance();
        
        return view('admin.company-profile.index', compact('companyProfile'));
    }

    /**
     * Update the company profile.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'about' => 'nullable|string',
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'history' => 'nullable|string',
            'values' => 'nullable|array',
            'values.*' => 'string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'facebook' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'linkedin' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'latitude' => 'nullable|string|max:20',
            'longitude' => 'nullable|string|max:20',
            'logo' => 'nullable|image|max:2048',
            'established_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'employees_count' => 'nullable|integer|min:1',
            'projects_completed' => 'nullable|integer|min:0',
            'clients_count' => 'nullable|integer|min:0',
        ]);
        
        $companyProfile = CompanyProfile::getInstance();
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($companyProfile->logo) {
                Storage::disk('public')->delete($companyProfile->logo);
            }
            
            $path = $this->fileUploadService->uploadImage(
                $request->file('logo'),
                'company',
                null,
                500
            );
            $validated['logo'] = $path;
        }
        
        // Format values if provided
        if (isset($validated['values']) && is_array($validated['values'])) {
            $validated['values'] = array_filter($validated['values']);
        }
        
        // Update company profile
        $companyProfile->update($validated);
        
        return redirect()->route('admin.company-profile.index')
            ->with('success', 'Company profile updated successfully!');
    }
    
    /**
     * Display the SEO form.
     */
    public function seo()
    {
        $companyProfile = CompanyProfile::getInstance();
        
        // Get or create SEO data
        $seo = $companyProfile->getSeoData();
        
        return view('admin.company-profile.seo', compact('companyProfile', 'seo'));
    }
    
    /**
     * Update the SEO information.
     */
    public function updateSeo(Request $request)
    {
        $validated = $request->validate([
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'seo_keywords' => 'nullable|string',
            'og_image' => 'nullable|image|max:2048',
        ]);
        
        $companyProfile = CompanyProfile::getInstance();
        
        // Handle OG image upload
        if ($request->hasFile('og_image')) {
            // Get or create SEO data
            $seo = $companyProfile->getSeoData();
            
            // Delete old OG image if exists
            if ($seo->og_image) {
                Storage::disk('public')->delete($seo->og_image);
            }
            
            $path = $this->fileUploadService->uploadImage(
                $request->file('og_image'),
                'company/seo',
                null,
                1200,
                630
            );
            
            // Update SEO data with new OG image
            $companyProfile->updateSeo([
                'title' => $request->seo_title,
                'description' => $request->seo_description,
                'keywords' => $request->seo_keywords,
                'og_image' => $path,
            ]);
        } else {
            // Update SEO data without changing OG image
            $companyProfile->updateSeo([
                'title' => $request->seo_title,
                'description' => $request->seo_description,
                'keywords' => $request->seo_keywords,
            ]);
        }
        
        return redirect()->route('admin.company-profile.seo')
            ->with('success', 'SEO information updated successfully!');
    }
}