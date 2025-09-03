<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Models\CompanyProfile;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ProjectCategory;
use App\Models\ProductCategory; // NEW: Add ProductCategory
use App\Models\Setting;
use App\Models\Banner;
use App\Models\BannerCategory;

class BaseController extends Controller
{
    protected $companyProfile;
    protected $settings;
    protected $navLinks;
    protected $globalServices;
    protected $serviceCategories;
    protected $projectCategories;
    protected $productCategories; // NEW: Add product categories
    protected $socialMedia;
    protected $contactInfo;
    protected $siteConfig;
    protected $announcementBanner;

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
        try {
            // Company Profile - informasi dasar perusahaan
            $this->companyProfile = CompanyProfile::getInstance();
        } catch (\Exception $e) {
            $this->companyProfile = null;
        }
        
        // Settings - konfigurasi umum website
        $this->settings = $this->getSettings();
        
        // Services untuk menu navigasi dan footer
        $this->globalServices = Cache::remember('global_services', 3600, function () {
            return Service::where('is_active', true)
                ->orderBy('sort_order', 'asc')
                ->orderBy('title', 'asc')
                ->limit(10)
                ->get(['id', 'title', 'slug', 'short_description']);
        });
        
        // Service Categories untuk menu dropdown
        $this->serviceCategories = Cache::remember('service_categories', 3600, function () {
            return ServiceCategory::where('is_active', true)
                ->withCount(['activeServices'])
                ->orderBy('sort_order', 'asc')
                ->orderBy('name', 'asc')
                ->get(['id', 'name', 'slug', 'description']);
        });
        
        // Project Categories untuk filter/menu
        $this->projectCategories = Cache::remember('project_categories', 3600, function () {
            return ProjectCategory::where('is_active', true)
                ->withCount(['activeProjects'])
                ->orderBy('sort_order', 'asc')
                ->orderBy('name', 'asc')
                ->get(['id', 'name', 'slug', 'description']);
        });
        
        // NEW: Product Categories untuk filter/menu
        $this->productCategories = Cache::remember('product_categories', 3600, function () {
            return ProductCategory::where('is_active', true)
                ->withCount(['products' => function($query) {
                    $query->where('status', 'published')->where('is_active', true);
                }])
                ->orderBy('sort_order', 'asc')
                ->orderBy('name', 'asc')
                ->get(['id', 'name', 'slug', 'description']);
        });
        
        // Navigation structure
        $this->navLinks = $this->buildNavigationLinks();
        
        // Social Media Links
        $this->socialMedia = $this->getSocialMediaLinks();
        
        // Contact Information
        $this->contactInfo = $this->getContactInfo();
        
        // Site Configuration
        $this->siteConfig = $this->getSiteConfig();

        // Announcement Banner
        $this->announcementBanner = $this->getAnnouncementBanner();

        // Share data ke semua views
        $this->shareDataToViews();
    }

    /**
     * Build navigation links structure
     */
    protected function buildNavigationLinks()
    {
        return [
            [
                'label' => 'Beranda',
                'route' => 'home',
                'icon' => 'home',
                'active_routes' => ['home']
            ],
            [
                'label' => 'Tentang Kami',
                'route' => 'about.index',
                'icon' => 'users',
                'active_routes' => ['about.*'],
                'dropdown' => [
                    [
                        'label' => 'Profil Perusahaan', 
                        'route' => 'about.index',
                        'description' => 'Pelajari tentang perusahaan kami'
                    ],
                    [
                        'label' => 'Tim Kami', 
                        'route' => 'about.team',
                        'description' => 'Kenali para profesional kami'
                    ],
                ]
            ],
            [
                'label' => 'Layanan',
                'route' => 'services.index',
                'icon' => 'briefcase',
                'active_routes' => ['services.*'],
                'dropdown' => $this->buildServicesDropdown()
            ],
            // NEW: Products navigation
            [
                'label' => 'Produk',
                'route' => 'products.index',
                'icon' => 'cube',
                'active_routes' => ['products.*'],
                'dropdown' => $this->buildProductsDropdown()
            ],
            [
                'label' => 'Portfolio',
                'route' => 'portfolio.index',
                'icon' => 'folder',
                'active_routes' => ['portfolio.*'],
                'dropdown' => $this->buildPortfolioDropdown()
            ],
            [
                'label' => 'Blog',
                'route' => 'blog.index',
                'icon' => 'document-text',
                'active_routes' => ['blog.*']
            ],
            [
                'label' => 'Hubungi Kami',
                'route' => 'contact.index',
                'icon' => 'mail',
                'active_routes' => ['contact.*'],
                'dropdown' => [
                    [
                        'label' => 'Hubungi Kami', 
                        'route' => 'contact.index',
                        'description' => 'Silakan hubungi kami'
                    ],
                ]
            ]
        ];
    }

    /**
     * Build services dropdown menu
     */
    protected function buildServicesDropdown()
    {
        $dropdown = [
            [
                'label' => 'Semua Layanan',
                'route' => 'services.index',
                'description' => 'Lihat semua layanan kami'
            ]
        ];

        // Add service categories
        foreach ($this->serviceCategories as $category) {
            if ($category->activeServices_count > 0) {
                $dropdown[] = [
                    'label' => $category->name,
                    'route' => 'services.index',
                    'params' => ['category' => $category->slug],
                    'description' => $category->description
                ];
            }
        }

        return $dropdown;
    }

    /**
     * NEW: Build products dropdown menu
     */
    protected function buildProductsDropdown()
    {
        $dropdown = [
            [
                'label' => 'Semua Produk',
                'route' => 'products.index',
                'description' => 'Lihat semua produk kami'
            ]
        ];

        // Add product categories
        foreach ($this->productCategories as $category) {
            if ($category->products_count > 0) {
                $dropdown[] = [
                    'label' => $category->name,
                    'route' => 'products.index',
                    'params' => ['category' => $category->slug],
                    'description' => $category->description
                ];
            }
        }

        return $dropdown;
    }

    /**
     * Build portfolio dropdown menu
     */
    protected function buildPortfolioDropdown()
    {
        $dropdown = [
            [
                'label' => 'Semua Proyek',
                'route' => 'portfolio.index',
                'description' => 'Lihat semua proyek kami'
            ]
        ];

        // Add project categories
        foreach ($this->projectCategories as $category) {
            if ($category->activeProjects_count > 0) {
                $dropdown[] = [
                    'label' => $category->name,
                    'route' => 'portfolio.index',
                    'params' => ['category' => $category->slug],
                    'description' => $category->description
                ];
            }
        }

        return $dropdown;
    }

    /**
     * Get settings dari database atau cache
     */
    protected function getSettings()
    {
        return Cache::remember('site_settings', 3600, function () {
            $settingsArray = [];
            
            try {
                $settings = Setting::all();
                foreach ($settings as $setting) {
                    $settingsArray[$setting->key] = $setting->value;
                }
            } catch (\Exception $e) {
                // Return default settings if table doesn't exist
                $settingsArray = $this->getDefaultSettings();
            }
            
            return $settingsArray;
        });
    }

    /**
     * Get default settings fallback
     */
    protected function getDefaultSettings()
    {
        return [
            'site_title' => config('app.name'),
            'site_description' => 'Professional construction and services company',
            'site_keywords' => 'construction, services, professional',
            'social_facebook' => '',
            'social_instagram' => '',
            'social_twitter' => '',
            'social_linkedin' => '',
            'social_youtube' => '',
            'social_whatsapp' => '',
            'google_analytics' => '',
            'facebook_pixel' => '',
            'maintenance_mode' => false,
            'site_announcement' => '',
            'show_announcement' => false,
        ];
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
            'whatsapp' => $this->settings['social_whatsapp'] ?? $this->companyProfile->whatsapp ?? '',
        ];
    }

    /**
     * Get contact information
     */
    protected function getContactInfo()
    {
        return [
            'phone' => $this->companyProfile->phone ?? $this->settings['contact_phone'] ?? '',
            'email' => $this->companyProfile->email ?? $this->settings['contact_email'] ?? '',
            'address' => $this->companyProfile->address ?? $this->settings['contact_address'] ?? '',
            'city' => $this->companyProfile->city ?? $this->settings['contact_city'] ?? '',
            'postal_code' => $this->companyProfile->postal_code ?? $this->settings['contact_postal_code'] ?? '',
            'whatsapp' => $this->companyProfile->whatsapp ?? $this->settings['contact_whatsapp'] ?? '',
            'business_hours' => $this->settings['business_hours'] ?? 'Mon-Fri: 8 AM - 6 PM',
            'emergency_phone' => $this->settings['emergency_phone'] ?? '',
        ];
    }

    /**
     * Get site configuration
     */
    protected function getSiteConfig()
    {
        return [
            'site_title' => $this->settings['site_title'] ?? config('app.name'),
            'site_description' => $this->settings['site_description'] ?? '',
            'site_keywords' => $this->settings['site_keywords'] ?? '',
            'site_logo' => $this->companyProfile->logo ?? '/images/logo.png',
            'site_favicon' => $this->settings['site_favicon'] ?? '/images/favicon.ico',
            'google_analytics' => $this->settings['google_analytics'] ?? '',
            'facebook_pixel' => $this->settings['facebook_pixel'] ?? '',
            'maintenance_mode' => $this->settings['maintenance_mode'] ?? false,
        ];
    }

    /**
     * Get announcement banner
     */
    protected function getAnnouncementBanner()
    {
        if (!($this->settings['show_announcement'] ?? false)) {
            return null;
        }

        return [
            'message' => $this->settings['site_announcement'] ?? '',
            'type' => $this->settings['announcement_type'] ?? 'info',
            'link' => $this->settings['announcement_link'] ?? '',
            'dismissible' => $this->settings['announcement_dismissible'] ?? true,
        ];
    }

    /**
     * Share data ke semua views
     */
    protected function shareDataToViews()
    {
        View::share([
            'companyProfile' => $this->companyProfile,
            'navLinks' => $this->navLinks,
            'globalServices' => $this->globalServices,
            'serviceCategories' => $this->serviceCategories,
            'projectCategories' => $this->projectCategories,
            'productCategories' => $this->productCategories, // NEW: Share product categories
            'socialMedia' => $this->socialMedia,
            'contactInfo' => $this->contactInfo,
            'siteConfig' => $this->siteConfig,
            'announcementBanner' => $this->announcementBanner,
            'settings' => $this->settings,
        ]);
    }

    /**
     * Share base data - alias untuk shareDataToViews untuk backward compatibility
     */
    protected function shareBaseData()
    {
        // This method is for backward compatibility
        // Data is already shared in loadGlobalData method
        return;
    }

    /**
     * Get banner by category helper
     */
    protected function getBannersByCategory($categorySlug, $limit = null)
    {
        try {
            $category = BannerCategory::where('slug', $categorySlug)->first();
            
            if (!$category) {
                return collect();
            }

            $query = Banner::where('banner_category_id', $category->id)
                ->where('is_active', true)
                ->orderBy('display_order', 'asc')
                ->orderBy('created_at', 'desc');

            if ($limit) {
                $query->limit($limit);
            }

            return $query->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Set page meta data
     */
    protected function setPageMeta($title = null, $description = null, $keywords = null, $image = null)
    {
        View::share([
            'autoSeo' => [
                'title' => $title ?? $this->siteConfig['site_title'],
                'description' => $description ?? $this->siteConfig['site_description'],
                'keywords' => $keywords ?? $this->siteConfig['site_keywords'],
                'image' => $image ?? asset($this->siteConfig['site_logo']),
                'type' => 'website',
                'url' => request()->url(),
            ]
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

        View::share(['breadcrumbs' => $breadcrumb]);
    }

    /**
     * Clear cache helper
     */
    protected function clearCache()
    {
        Cache::forget('site_settings');
        Cache::forget('global_services');
        Cache::forget('service_categories');
        Cache::forget('project_categories');
        Cache::forget('product_categories'); // NEW: Clear product categories cache
    }

    /**
     * Get featured content for sidebars/footers
     */
    protected function getFeaturedContent()
    {
        return Cache::remember('featured_content', 1800, function () {
            return [
                'services' => $this->globalServices->where('featured', true)->take(3),
                'projects' => \App\Models\Project::where('is_active', true)
                    ->where('featured', true)
                    ->with(['category', 'images'])
                    ->latest()
                    ->take(3)
                    ->get(),
                'products' => \App\Models\Product::where('status', 'published') // NEW: Featured products
                    ->where('is_active', true)
                    ->where('is_featured', true)
                    ->with(['category'])
                    ->latest()
                    ->take(3)
                    ->get(),
                'testimonials' => \App\Models\Testimonial::where('is_active', true)
                    ->where('featured', true)
                    ->latest()
                    ->take(3)
                    ->get(),
            ];
        });
    }
}