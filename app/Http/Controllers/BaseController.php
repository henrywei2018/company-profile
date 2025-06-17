<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as Controller;

class BaseController extends Controller
{
    protected $companyProfile;
    protected $navLinks;
    protected $additionalNav;
    protected $announcementBanner;

    public function __construct()
    {
        // Load company profile (null-safe)
        try {
            $this->companyProfile = \App\Models\CompanyProfile::getInstance();
        } catch (\Exception $e) {
            $this->companyProfile = null;
        }

        // Navigation structure (can be moved to config if preferred)
        $this->navLinks = [
            [
                'label' => 'Home',
                'route' => 'home',
            ],
            [
                'label' => 'Layanan',
                'route' => 'services.index',
            ],
            [
                'label' => 'Portfolio',
                'dropdown' => [
                    ['label' => 'Projects', 'route' => 'portfolio.index'],
                    ['label' => 'General Supplier', 'route' => 'services.show', 'params' => ['services' => 'service-2']],
                    ['label' => 'Service 3', 'route' => 'services.show', 'params' => ['services' => 'service-3']],
                ],
                'route' => 'portfolio.index',
            ],
            [
                'label' => 'Services',
                'dropdown' => [
                    ['label' => 'Service 1', 'route' => 'services.show', 'params' => ['services' => 'service-1']],
                    ['label' => 'Service 2', 'route' => 'services.show', 'params' => ['services' => 'service-2']],
                    ['label' => 'Service 3', 'route' => 'services.show', 'params' => ['services' => 'service-3']],
                ],
            ],
            [
                'label' => 'Blog',
                'route' => 'blog.index',
            ],
            [
                'label' => 'Portfolio',
                'route' => 'portfolio.index',
            ],
            [
                'label' => 'Contact',
                'route' => 'contact.index',
            ],
        ];

        $this->additionalNav = [
            ['label' => 'Pengumuman', 'url' => '/pengumuman'],
            ['label' => 'Layanan Publik', 'url' => '/layanan-publik'],
            ['label' => 'Statistik', 'url' => '/statistik'],
        ];

        $this->announcementBanner = config('app.announcement_banner') ?? null; // Or load from DB/setting
    }

    /**
     * Share data with all views rendered from controllers extending this base.
     */
    protected function shareBaseData()
    {
        view()->share([
            'companyProfile'     => $this->companyProfile,
            'navLinks'           => $this->navLinks,
            'additionalNav'      => $this->additionalNav,
            'announcementBanner' => $this->announcementBanner,
        ]);
    }
}
