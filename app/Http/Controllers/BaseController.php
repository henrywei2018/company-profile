<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\CompanyProfile;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ProjectCategory;
use App\Models\Setting;
use App\Models\Banner;
use App\Models\BannerCategory;

class BaseController extends Controller
{
    protected $companyProfile;
    protected $settings;
    protected $globalServices;
    protected $serviceCategories;
    protected $projectCategories;
    protected $socialMedia;
    protected $contactInfo;
    protected $siteConfig;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->loadGlobalData();
            return $next($request);
        });
    }

    /**
     * Load global data yang dibutuhkan di semua halaman public
     */
    protected function loadGlobalData()
    {
        // Company Profile - informasi dasar perusahaan
        $this->companyProfile = CompanyProfile::first();
        
        // Settings - konfigurasi umum website
        $this->settings = $this->getSettings();
        
        // Services untuk menu navigasi dan footer
        $this->globalServices = Service::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->limit(10)
            ->get();
        
        // Service Categories untuk menu dropdown
        $this->serviceCategories = ServiceCategory::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();
        
        // Project Categories untuk filter/menu
        $this->projectCategories = ProjectCategory::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();
        
        // Social Media Links
        $this->socialMedia = $this->getSocialMediaLinks();
        
        // Contact Information
        $this->contactInfo = $this->getContactInfo();
        
        // Site Configuration
        $this->siteConfig = $this->getSiteConfig();

        // Share data ke semua views
        $this->shareDataToViews();
    }

    /**
     * Get settings dari database atau cache
     */
    protected function getSettings()
    {
        return cache()->remember('site_settings', 3600, function () {
            $settingsArray = [];
            $settings = Setting::all();
            
            foreach ($settings as $setting) {
                $settingsArray[$setting->key] = $setting->value;
            }
            
            return $settingsArray;
        });
    }

    /**
     * Get social media links
     */
    protected function getSocialMediaLinks()
    {
        return [
            'facebook' => $this->settings['social_facebook'] ?? '',
            'instagram' => $this->settings['social_instagram'] ?? '',
            'twitter' => $this->settings['social_twitter'] ?? '',
            'linkedin' => $this->settings['social_linkedin'] ?? '',
            'youtube' => $this->settings['social_youtube'] ?? '',
            'whatsapp' => $this->settings['social_whatsapp'] ?? '',
            'telegram' => $this->settings['social_telegram'] ?? '',
        ];
    }

    /**
     * Get contact information
     */
    protected function getContactInfo()
    {
        return [
            'phone' => $this->settings['contact_phone'] ?? ($this->companyProfile->phone ?? ''),
            'email' => $this->settings['contact_email'] ?? ($this->companyProfile->email ?? ''),
            'address' => $this->settings['contact_address'] ?? ($this->companyProfile->address ?? ''),
            'working_hours' => $this->settings['working_hours'] ?? 'Senin - Jumat: 08:00 - 17:00',
            'whatsapp_number' => $this->settings['whatsapp_number'] ?? '',
            'whatsapp_message' => $this->settings['whatsapp_message'] ?? 'Halo, saya ingin bertanya tentang layanan Anda.',
        ];
    }

    /**
     * Get site configuration
     */
    protected function getSiteConfig()
    {
        return [
            'site_name' => $this->settings['site_name'] ?? ($this->companyProfile->company_name ?? 'CV Usaha Prima Lestari'),
            'site_title' => $this->settings['site_title'] ?? 'CV Usaha Prima Lestari - Solusi Terbaik untuk Kebutuhan Anda',
            'site_description' => $this->settings['site_description'] ?? ($this->companyProfile->description ?? ''),
            'site_keywords' => $this->settings['site_keywords'] ?? 'cv usaha prima lestari, jasa, layanan',
            'site_logo' => $this->settings['site_logo'] ?? ($this->companyProfile->logo ?? '/images/logo.png'),
            'site_favicon' => $this->settings['site_favicon'] ?? '/images/favicon.ico',
            'google_analytics' => $this->settings['google_analytics'] ?? '',
            'facebook_pixel' => $this->settings['facebook_pixel'] ?? '',
            'chat_widget' => $this->settings['chat_widget'] ?? '',
            'maintenance_mode' => $this->settings['maintenance_mode'] ?? false,
            'site_announcement' => $this->settings['site_announcement'] ?? '',
            'show_announcement' => $this->settings['show_announcement'] ?? false,
        ];
    }

    /**
     * Share data ke semua views
     */
    protected function shareDataToViews()
    {
        View::share([
            'globalCompanyProfile' => $this->companyProfile,
            'globalSettings' => $this->settings,
            'globalServices' => $this->globalServices,
            'globalServiceCategories' => $this->serviceCategories,
            'globalProjectCategories' => $this->projectCategories,
            'globalSocialMedia' => $this->socialMedia,
            'globalContactInfo' => $this->contactInfo,
            'globalSiteConfig' => $this->siteConfig,
        ]);
    }

    /**
     * Get banner by category helper
     */
    protected function getBannersByCategory($categorySlug, $limit = null)
    {
        $category = BannerCategory::where('slug', $categorySlug)->first();
        
        if (!$category) {
            return collect();
        }

        $query = Banner::where('banner_category_id', $category->id)
            ->where('is_active', true)
            ->orderBy('display_order', 'asc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Set page meta data
     */
    protected function setPageMeta($title = null, $description = null, $keywords = null, $image = null)
    {
        View::share([
            'pageTitle' => $title ?? $this->siteConfig['site_title'],
            'pageDescription' => $description ?? $this->siteConfig['site_description'],
            'pageKeywords' => $keywords ?? $this->siteConfig['site_keywords'],
            'pageImage' => $image ?? asset($this->siteConfig['site_logo']),
            'pageUrl' => request()->url(),
        ]);
    }

    /**
     * Generate breadcrumb
     */
    protected function setBreadcrumb($items = [])
    {
        $breadcrumb = [
            ['name' => 'Home', 'url' => route('home')]
        ];

        foreach ($items as $item) {
            $breadcrumb[] = [
                'name' => $item['name'],
                'url' => $item['url'] ?? null
            ];
        }

        View::share('breadcrumb', $breadcrumb);
    }

    /**
     * Check if maintenance mode
     */
    protected function checkMaintenanceMode()
    {
        if ($this->siteConfig['maintenance_mode'] && !auth()->check()) {
            return view('pages.maintenance');
        }
        
        return null;
    }

    /**
     * Add global JavaScript variables
     */
    protected function addGlobalJsVars($vars = [])
    {
        $globalVars = array_merge([
            'baseUrl' => url('/'),
            'apiUrl' => url('/api'),
            'csrfToken' => csrf_token(),
            'locale' => app()->getLocale(),
            'contactInfo' => $this->contactInfo,
            'siteConfig' => [
                'name' => $this->siteConfig['site_name'],
                'whatsapp_number' => $this->contactInfo['whatsapp_number'],
                'whatsapp_message' => $this->contactInfo['whatsapp_message'],
            ]
        ], $vars);

        View::share('globalJsVars', $globalVars);
    }
}