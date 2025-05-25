<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompanyProfileController extends Controller
{
    public function index()
    {
        $profile = CompanyProfile::firstOrFail();
        return view('admin.company.index', compact('profile'));
    }
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
            'legal_name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'about' => 'nullable|string|max:200',
            'description' => 'nullable|string',
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'email' => 'required|email|max:255',
            'alternative_email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:25',
            'alternative_phone' => 'nullable|string|max:25',
            'address' => 'required|string|max:255',
            'facebook' => 'nullable|url',
            'twitter' => 'nullable|url',
            'instagram' => 'nullable|url',
            'linkedin' => 'nullable|url',
            'youtube' => 'nullable|url',
            'whatsapp' => 'nullable|string|max:25',
            'tax_id' => 'nullable|string|max:100',
            'registration_number' => 'nullable|string|max:100',
            'established' => 'nullable|integer|min:1900|max:' . date('Y'),
            'map_embed' => 'nullable|string',
            'latitude' => 'nullable|string|max:50',
            'longitude' => 'nullable|string|max:50',
            'business_hours' => 'nullable|array',
            'business_hours.*.open' => 'nullable|string',
            'business_hours.*.close' => 'nullable|string',
            'business_hours.*.closed' => 'nullable|boolean',
            'logo' => 'nullable|image',
            'logo_white' => 'nullable|image',
        ]);

        $companyProfile = CompanyProfile::firstOrFail();

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('company', 'public');
        }

        if ($request->hasFile('logo_white')) {
            $validated['logo_white'] = $request->file('logo_white')->store('company', 'public');
        }

        $companyProfile->update($validated);

        return redirect()->route('admin.company.edit')->with('success', 'Company profile updated successfully.');
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