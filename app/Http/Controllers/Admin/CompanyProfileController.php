<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompanyProfileController extends Controller
{
    /**
     * Display the company profile form.
     */
    public function edit()
    {
        $companyProfile = CompanyProfile::getInstance();
        
        return view('admin.company.edit', compact('companyProfile'));
    }

    /**
     * Update the company profile.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'about' => 'nullable|string',
            'description' => 'nullable|string',
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'email' => 'required|email|max:255',
            'alternative_email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'alternative_phone' => 'nullable|string|max:20',
            'address' => 'required|string',
            'facebook' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'linkedin' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'legal_name' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:50',
            'registration_number' => 'nullable|string|max:50',
            'established' => 'nullable|integer|min:1900|max:' . date('Y'),
            'latitude' => 'nullable|string|max:20',
            'longitude' => 'nullable|string|max:20',
            'map_embed' => 'nullable|string',
            'business_hours' => 'nullable|array',
            'business_hours.*' => 'array',
            'logo' => 'nullable|image|max:2048',
            'logo_white' => 'nullable|image|max:2048',
        ]);
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($request->company_has_logo) {
                Storage::disk('public')->delete($request->company_old_logo);
            }
            
            $path = $request->file('logo')->store('company', 'public');
            $validated['logo'] = $path;
        }
        
        // Handle white logo upload
        if ($request->hasFile('logo_white')) {
            // Delete old white logo if exists
            if ($request->company_has_logo_white) {
                Storage::disk('public')->delete($request->company_old_logo_white);
            }
            
            $path = $request->file('logo_white')->store('company', 'public');
            $validated['logo_white'] = $path;
        }
        
        // Convert business hours to JSON
        if (isset($validated['business_hours']) && is_array($validated['business_hours'])) {
            $validated['business_hours'] = json_encode($validated['business_hours']);
        }
        
        // Update company profile
        $companyProfile = CompanyProfile::getInstance();
        $companyProfile->update($validated);
        
        return redirect()->route('admin.company.edit')
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
        
        return view('admin.company.seo', compact('companyProfile', 'seo'));
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
            if ($seo && $seo->og_image) {
                Storage::disk('public')->delete($seo->og_image);
            }
            
            $path = $request->file('og_image')->store('company/seo', 'public');
            
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
        
        return redirect()->route('admin.company.seo')
            ->with('success', 'SEO information updated successfully!');
    }
    
    /**
     * Display the certificates management page.
     */
    public function certificates()
    {
        $companyProfile = CompanyProfile::getInstance();
        $certificates = $companyProfile->certificates()->orderBy('sort_order')->get();
        
        return view('admin.company.certificates', compact('companyProfile', 'certificates'));
    }
}