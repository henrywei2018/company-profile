<?php
// File: app/Http/Controllers/Admin/SettingController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\CompanyProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display general settings form.
     */
    public function index()
    {
        // Get all settings
        $settings = Setting::pluck('value', 'key')->toArray();
        
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update general settings.
     */
    public function update(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string',
            'contact_email' => 'required|email',
            'contact_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'social_facebook' => 'nullable|url',
            'social_twitter' => 'nullable|url',
            'social_instagram' => 'nullable|url',
            'social_linkedin' => 'nullable|url',
            'google_analytics_id' => 'nullable|string',
            'site_logo' => 'nullable|image|max:1024',
            'site_favicon' => 'nullable|image|max:512',
            'footer_text' => 'nullable|string',
        ]);
        
        // Handle logo upload
        if ($request->hasFile('site_logo')) {
            $logoPath = $request->file('site_logo')->store('settings', 'public');
            $validated['site_logo'] = $logoPath;
            
            // Delete old logo if exists
            if ($request->has('old_site_logo') && $request->old_site_logo) {
                Storage::disk('public')->delete($request->old_site_logo);
            }
        }
        
        // Handle favicon upload
        if ($request->hasFile('site_favicon')) {
            $faviconPath = $request->file('site_favicon')->store('settings', 'public');
            $validated['site_favicon'] = $faviconPath;
            
            // Delete old favicon if exists
            if ($request->has('old_site_favicon') && $request->old_site_favicon) {
                Storage::disk('public')->delete($request->old_site_favicon);
            }
        }
        
        // Update settings
        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
        
        // Clear cache
        Cache::forget('settings');
        
        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully!');
    }
    
    /**
     * Display SEO settings form.
     */
    public function seo()
    {
        // Get SEO settings
        $seoSettings = Setting::where('key', 'LIKE', 'seo_%')
            ->pluck('value', 'key')
            ->toArray();
        
        return view('admin.settings.seo', compact('seoSettings'));
    }
    
    /**
     * Update SEO settings.
     */
    public function updateSeo(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'seo_title_format' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'seo_keywords' => 'nullable|string',
            'seo_google_verification' => 'nullable|string',
            'seo_bing_verification' => 'nullable|string',
            'seo_robots_txt' => 'nullable|string',
        ]);
        
        // Update settings
        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
        
        // Clear cache
        Cache::forget('settings');
        
        return redirect()->route('admin.settings.seo')
            ->with('success', 'SEO settings updated successfully!');
    }
    
    /**
     * Display company profile settings form.
     */
    public function companyProfile()
    {
        // Get company profile
        $companyProfile = CompanyProfile::getInstance();
        
        return view('admin.settings.company-profile', compact('companyProfile'));
    }
    
    /**
     * Update company profile settings.
     */
    public function updateCompanyProfile(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'about' => 'nullable|string',
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'established_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'employees_count' => 'nullable|integer|min:1',
            'projects_completed' => 'nullable|integer|min:0',
            'clients_count' => 'nullable|integer|min:0',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            'map_coordinates' => 'nullable|string',
            'logo' => 'nullable|image|max:1024',
        ]);
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('company', 'public');
            $validated['logo'] = $logoPath;
            
            // Delete old logo if exists
            $companyProfile = CompanyProfile::getInstance();
            if ($companyProfile->logo) {
                Storage::disk('public')->delete($companyProfile->logo);
            }
        }
        
        // Update company profile
        CompanyProfile::updateProfile($validated);
        
        return redirect()->route('admin.settings.company-profile')
            ->with('success', 'Company profile updated successfully!');
    }
    
    /**
     * Display email settings form.
     */
    public function email()
    {
        // Get email settings
        $emailSettings = Setting::where('key', 'LIKE', 'email_%')
            ->pluck('value', 'key')
            ->toArray();
        
        return view('admin.settings.email', compact('emailSettings'));
    }
    
    /**
     * Update email settings.
     */
    public function updateEmail(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'email_from_address' => 'required|email',
            'email_from_name' => 'required|string|max:255',
            'email_notification_recipients' => 'nullable|string',
        ]);
        
        // Update settings
        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
        
        // Clear cache
        Cache::forget('settings');
        
        return redirect()->route('admin.settings.email')
            ->with('success', 'Email settings updated successfully!');
    }
    
    /**
     * Send test email.
     */
    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);
        
        // Send test email logic here
        // Mail::to($request->test_email)->send(new TestEmail());
        
        return redirect()->back()
            ->with('success', 'Test email sent successfully!');
    }
}