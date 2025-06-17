<?php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use App\Models\TeamMember;
use App\Models\TeamMemberDepartment;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class AboutController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->shareBaseData();
    }
    /**
     * Display the about page with data from database.
     */
    public function index(): View
    {
        // Get company profile from database
        $companyProfile = CompanyProfile::getInstance();
        
        // Get featured team members for about page
        $featuredTeamMembers = TeamMember::with('department')
            ->active()
            ->featured()
            ->ordered()
            ->limit(8)
            ->get();
        
        // Get all departments with active team members count
        $departments = TeamMemberDepartment::withCount(['activeTeamMembers'])
            ->active()
            ->ordered()
            ->get();
        
        // Get company statistics from database
        $statistics = $this->getCompanyStatistics();
        
        // Get company values from profile or settings
        $companyValues = $this->getCompanyValues($companyProfile);
        
        // Get company milestones/timeline
        $milestones = $this->getCompanyMilestones();
        
        // Get services from settings or database
        $services = $this->getCompanyServices();
        
        // Get certifications if available
        $certifications = $this->getCertifications();
        
        // SEO data
        $seoData = [
            'title' => 'Tentang Kami - ' . ($companyProfile->company_name ?? config('app.name')),
            'description' => $companyProfile->about ?? 'Pelajari lebih lanjut tentang ' . config('app.name') . ' dan komitmen kami dalam memberikan solusi terbaik.',
            'keywords' => 'tentang kami, profil perusahaan, tim, visi misi, ' . ($companyProfile->company_name ?? config('app.name')),
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => route('home')],
                ['name' => 'About', 'url' => route('about')]
            ]
        ];

        return view('pages.about.index', compact(
            'companyProfile',
            'featuredTeamMembers',
            'departments',
            'statistics',
            'companyValues',
            'milestones',
            'services',
            'certifications',
            'seoData'
        ));
    }

    /**
     * Display the full team page.
     */
    public function team(): View
    {
        $companyProfile = CompanyProfile::getInstance();
        
        // Get all active team members with departments
        $teamMembers = TeamMember::with('department')
            ->active()
            ->ordered()
            ->get();
        
        // Group team members by department
        $teamByDepartment = $teamMembers->groupBy(function($member) {
            return $member->department ? $member->department->name : 'Other';
        });
        
        // Get departments for navigation
        $departments = TeamMemberDepartment::withCount(['activeTeamMembers'])
            ->active()
            ->ordered()
            ->get();
        
        $seoData = [
            'title' => 'Tim Kami - ' . ($companyProfile->company_name ?? config('app.name')),
            'description' => 'Kenali tim profesional kami yang berpengalaman dan berkomitmen memberikan layanan terbaik.',
            'keywords' => 'tim, karyawan, staff, team, ' . ($companyProfile->company_name ?? config('app.name')),
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => route('home')],
                ['name' => 'Team', 'url' => route('about.team')]
            ]
        ];

        return view('pages.about.team', compact(
            'companyProfile',
            'teamMembers',
            'teamByDepartment',
            'departments',
            'seoData'
        ));
    }

    /**
     * Get company statistics from database and settings.
     */
    protected function getCompanyStatistics(): array
    {
        return Cache::remember('company_statistics', 3600, function() {
            $companyProfile = CompanyProfile::getInstance();
            
            return [
                'years_experience' => $companyProfile->established_year ? 
                    now()->year - $companyProfile->established_year : 
                    (Setting::get('company_years_experience', 5)),
                    
                'projects_completed' => $companyProfile->projects_completed ?? 
                    Setting::get('company_projects_completed', 100),
                    
                'happy_clients' => $companyProfile->clients_count ?? 
                    Setting::get('company_clients_count', 50),
                    
                'team_members' => $companyProfile->employees_count ?? 
                    TeamMember::active()->count(),
                    
                'awards' => Setting::get('company_awards_count', 5),
                'countries' => Setting::get('company_countries_served', 3)
            ];
        });
    }

    /**
     * Get company values from profile or settings.
     */
    protected function getCompanyValues($companyProfile): array
    {
        // Try to get from company profile first
        if ($companyProfile && $companyProfile->values) {
            $values = $companyProfile->values;
            if (is_array($values) && !empty($values)) {
                return array_map(function($value, $index) {
                    return [
                        'title' => is_array($value) ? ($value['title'] ?? 'Value ' . ($index + 1)) : $value,
                        'description' => is_array($value) ? ($value['description'] ?? '') : '',
                        'icon' => is_array($value) ? ($value['icon'] ?? $this->getDefaultValueIcon($index)) : $this->getDefaultValueIcon($index)
                    ];
                }, $values, array_keys($values));
            }
        }
        
        // Fallback to settings or default values
        $defaultValues = Setting::get('company_values', [
            [
                'title' => 'Innovation',
                'description' => 'Kami selalu mencari solusi inovatif untuk setiap tantangan.',
                'icon' => 'lightbulb'
            ],
            [
                'title' => 'Quality',
                'description' => 'Kualitas tinggi adalah prioritas utama dalam setiap project.',
                'icon' => 'award'
            ],
            [
                'title' => 'Integrity',
                'description' => 'Membangun kepercayaan melalui transparansi dan kejujuran.',
                'icon' => 'shield-check'
            ],
            [
                'title' => 'Collaboration',
                'description' => 'Bekerja sama dengan klien sebagai partner strategis.',
                'icon' => 'users'
            ]
        ]);
        
        return $defaultValues;
    }

    /**
     * Get company milestones from settings.
     */
    protected function getCompanyMilestones(): array
    {
        return Setting::get('company_milestones', [
            [
                'year' => '2020',
                'title' => 'Company Founded',
                'description' => 'Memulai perjalanan dengan visi memberikan solusi teknologi terbaik.'
            ],
            [
                'year' => '2021',
                'title' => 'First Major Project',
                'description' => 'Menyelesaikan project pertama yang menjadi fondasi kepercayaan klien.'
            ],
            [
                'year' => '2022',
                'title' => 'Team Expansion',
                'description' => 'Memperluas tim dengan talent-talent terbaik di industri.'
            ],
            [
                'year' => '2023',
                'title' => 'Market Leadership',
                'description' => 'Menjadi salah satu pemimpin pasar dalam solusi teknologi.'
            ],
            [
                'year' => '2024',
                'title' => 'Innovation Award',
                'description' => 'Meraih penghargaan sebagai perusahaan paling inovatif.'
            ]
        ]);
    }

    /**
     * Get company services from settings.
     */
    protected function getCompanyServices(): array
    {
        return Setting::get('company_services', [
            [
                'title' => 'Web Development',
                'description' => 'Pengembangan website dan aplikasi web yang responsif dan modern.',
                'icon' => 'code'
            ],
            [
                'title' => 'Mobile Development',
                'description' => 'Pembuatan aplikasi mobile iOS dan Android yang user-friendly.',
                'icon' => 'smartphone'
            ],
            [
                'title' => 'UI/UX Design',
                'description' => 'Desain antarmuka yang menarik dan pengalaman pengguna yang optimal.',
                'icon' => 'palette'
            ],
            [
                'title' => 'Digital Marketing',
                'description' => 'Strategi pemasaran digital untuk meningkatkan visibility online.',
                'icon' => 'trending-up'
            ],
            [
                'title' => 'Cloud Solutions',
                'description' => 'Implementasi dan migrasi ke cloud infrastructure yang scalable.',
                'icon' => 'cloud'
            ],
            [
                'title' => 'Consulting',
                'description' => 'Konsultasi teknologi untuk optimasi proses bisnis Anda.',
                'icon' => 'users'
            ]
        ]);
    }

    /**
     * Get certifications if available.
     */
    protected function getCertifications(): array
    {
        // Check if Certification model exists and get data
        if (class_exists('App\Models\Certification')) {
            try {
                return \App\Models\Certification::active()
                    ->valid()
                    ->ordered()
                    ->get()
                    ->toArray();
            } catch (\Exception $e) {
                \Log::info('Certification model not available or table does not exist');
            }
        }
        
        // Fallback to settings
        return Setting::get('company_certifications', []);
    }

    /**
     * Get default icon for values based on index.
     */
    protected function getDefaultValueIcon(int $index): string
    {
        $icons = ['lightbulb', 'award', 'shield-check', 'users', 'star', 'heart'];
        return $icons[$index] ?? 'star';
    }

    /**
     * Get company statistics for AJAX requests.
     */
    public function getStats(Request $request): JsonResponse
    {
        try {
            $stats = $this->getCompanyStatistics();
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to get company statistics: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load statistics'
            ], 500);
        }
    }

    /**
     * Get team members for AJAX requests.
     */
    public function getTeamMembers(Request $request): JsonResponse
    {
        try {
            $department = $request->get('department');
            $featured = $request->boolean('featured');
            $limit = $request->get('limit', 10);
            
            $query = TeamMember::with('department')->active();
            
            if ($department) {
                $query->whereHas('department', function($q) use ($department) {
                    $q->where('slug', $department);
                });
            }
            
            if ($featured) {
                $query->featured();
            }
            
            $teamMembers = $query->ordered()
                ->limit($limit)
                ->get()
                ->map(function($member) {
                    return [
                        'id' => $member->id,
                        'name' => $member->name,
                        'position' => $member->position,
                        'department' => $member->department ? $member->department->name : null,
                        'photo_url' => $member->photo_url,
                        'bio' => $member->bio,
                        'linkedin' => $member->linkedin,
                        'twitter' => $member->twitter,
                    ];
                });
            
            return response()->json([
                'success' => true,
                'data' => $teamMembers
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to get team members: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load team members'
            ], 500);
        }
    }

    /**
     * Get company profile for AJAX requests.
     */
    public function getCompanyProfile(Request $request): JsonResponse
    {
        try {
            $companyProfile = CompanyProfile::getInstance();
            
            $data = [
                'company_name' => $companyProfile->company_name,
                'tagline' => $companyProfile->tagline,
                'about' => $companyProfile->about,
                'vision' => $companyProfile->vision,
                'mission' => $companyProfile->mission,
                'established_year' => $companyProfile->established_year,
                'logo_url' => $companyProfile->logo_url,
                'contact_info' => $companyProfile->contact_info,
                'full_address' => $companyProfile->full_address,
                'is_complete' => $companyProfile->isComplete(),
            ];
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to get company profile: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load company profile'
            ], 500);
        }
    }
}