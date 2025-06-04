<?php

namespace App\Services;

use App\Models\CompanyProfile;
use App\Models\Certification;
use App\Services\FileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CompanyProfileService
{
    protected FileUploadService $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Get the company profile instance.
     */
    public function getProfile(): CompanyProfile
    {
        return Cache::remember('company_profile', 3600, function () {
            return CompanyProfile::getInstance();
        });
    }

    /**
     * Update the company profile.
     */
    public function updateProfile(array $data, ?UploadedFile $logo = null, ?UploadedFile $logoWhite = null): CompanyProfile
    {
        $profile = $this->getProfile();

        // Handle logo upload
        if ($logo) {
            if (!empty($profile->logo)) {
                Storage::disk('public')->delete($profile->logo);
            }
            
            $data['logo'] = $this->fileUploadService->uploadImage(
                $logo, 
                'company/logos', 
                null, 
                300, 
                120
            );
        }

        // Process values array - FIXED for type safety
        if (isset($data['values'])) {
            if (is_array($data['values'])) {
                // Filter out empty values
                $data['values'] = array_values(array_filter($data['values'], function($value) {
                    return is_string($value) && !empty(trim($value));
                }));
            } else {
                // If it's not an array, set to empty array
                $data['values'] = [];
            }
        }

        // Clean string data
        foreach (['company_name', 'tagline', 'about', 'vision', 'mission', 'email', 'phone', 'address'] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = trim($data[$field]);
                // Convert empty strings to null for optional fields
                if (empty($data[$field]) && !in_array($field, ['company_name', 'email', 'phone', 'address'])) {
                    $data[$field] = null;
                }
            }
        }

        $profile->update($data);
        
        // Clear cache
        Cache::forget('company_profile');
        
        Log::info('Company profile updated', [
            'profile_id' => $profile->id,
            'updated_fields' => array_keys($data)
        ]);

        return $profile;
    }

    /**
     * Update SEO information.
     */
    public function updateSeo(array $seoData, ?UploadedFile $ogImage = null): CompanyProfile
    {
        $profile = $this->getProfile();

        // Handle OG image upload
        if ($ogImage) {
            $seo = $profile->getSeoData();
            
            // Delete old OG image
            if ($seo && !empty($seo->og_image)) {
                Storage::disk('public')->delete($seo->og_image);
            }

            $path = $this->fileUploadService->uploadImage(
                $ogImage, 
                'company/seo', 
                null, 
                1200, 
                630
            );
            
            $seoData['og_image'] = $path;
        }

        $profile->updateSeo($seoData);
        
        // Clear cache
        Cache::forget('company_profile');
        
        Log::info('Company SEO updated', [
            'profile_id' => $profile->id,
            'seo_fields' => array_keys($seoData)
        ]);

        return $profile;
    }

    /**
     * Get profile statistics and completion data - FIXED certificate handling
     */
    public function getStatistics(): array
    {
        $profile = $this->getProfile();
        
        return [
            'profile_complete' => $this->isProfileComplete($profile),
            'completion_percentage' => $profile->getCompletionPercentage(),
            'social_links_count' => $this->countSocialLinks($profile),
            'has_logo' => !empty($profile->logo),
            'has_description' => !empty(trim($profile->about ?? '')),
            'contact_info_complete' => $this->isContactInfoComplete($profile),
            'seo_configured' => $this->isSeoConfigured($profile),
            'certificates_count' => $this->getCertificatesCount(), // FIXED - no relationship
            'missing_fields' => $this->getMissingFields($profile),
        ];
    }

    /**
     * Get certificates count safely - FIXED to not use relationship
     */
    protected function getCertificatesCount(): int
    {
        try {
            // Count all certificates directly (no relationship needed)
            return Certification::count();
            
            // Alternative approaches with specific criteria:
            // return Certification::where('status', 'active')->count();
            // return Certification::whereNotNull('certificate_file')->count();
            // return Certification::where('expiry_date', '>', now())->count();
            
        } catch (\Exception $e) {
            Log::warning('Failed to count certificates: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get all certificates for the company - FIXED
     */
    public function getCertificates()
    {
        try {
            return Certification::orderBy('created_at', 'desc')->get();
        } catch (\Exception $e) {
            Log::warning('Failed to get certificates: ' . $e->getMessage());
            return collect(); // Return empty collection
        }
    }

    /**
     * Check if profile is substantially complete.
     */
    protected function isProfileComplete(CompanyProfile $profile): bool
    {
        $requiredFields = ['company_name', 'email', 'phone', 'address'];
        
        foreach ($requiredFields as $field) {
            $value = $profile->getAttribute($field);
            if (empty($value) || (is_string($value) && empty(trim($value)))) {
                return false;
            }
        }
        
        // Consider profile complete if it's at least 80% filled
        $percentage = $profile->getCompletionPercentage();
        return is_numeric($percentage) && $percentage >= 80;
    }

    /**
     * Count active social media links.
     */
    protected function countSocialLinks(CompanyProfile $profile): int
    {
        $socialFields = ['facebook', 'twitter', 'instagram', 'linkedin', 'youtube'];
        $count = 0;
        
        foreach ($socialFields as $field) {
            $value = $profile->getAttribute($field);
            if (is_string($value) && !empty(trim($value))) {
                $count++;
            }
        }
        
        return $count;
    }

    /**
     * Check if contact information is complete.
     */
    protected function isContactInfoComplete(CompanyProfile $profile): bool
    {
        $email = $profile->getAttribute('email');
        $phone = $profile->getAttribute('phone');
        $address = $profile->getAttribute('address');
        
        return !empty($email) && is_string($email) && filter_var(trim($email), FILTER_VALIDATE_EMAIL) &&
               !empty($phone) && is_string($phone) && !empty(trim($phone)) &&
               !empty($address) && is_string($address) && !empty(trim($address));
    }

    /**
     * Check if SEO is properly configured.
     */
    protected function isSeoConfigured(CompanyProfile $profile): bool
    {
        try {
            $seo = $profile->seo;
            
            if (!$seo) {
                return false;
            }
            
            $title = $seo->getAttribute('title');
            $description = $seo->getAttribute('description');
            $keywords = $seo->getAttribute('keywords');
            
            return !empty($title) && is_string($title) && !empty(trim($title)) &&
                   !empty($description) && is_string($description) && !empty(trim($description)) &&
                   !empty($keywords) && is_string($keywords) && !empty(trim($keywords));
        } catch (\Exception $e) {
            Log::warning('Error checking SEO configuration: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get list of missing required fields.
     */
    protected function getMissingFields(CompanyProfile $profile): array
    {
        $requiredFields = [
            'company_name' => 'Company Name',
            'email' => 'Email Address',
            'phone' => 'Phone Number',
            'address' => 'Business Address',
            'about' => 'About Description',
        ];
        
        $missing = [];
        
        foreach ($requiredFields as $field => $label) {
            $value = $profile->getAttribute($field);
            if (empty($value) || (is_string($value) && empty(trim($value)))) {
                $missing[] = $label;
            }
        }
        
        return $missing;
    }

    /**
     * Export company profile data.
     */
    public function exportProfileData(): array
    {
        $profile = $this->getProfile();
        $seo = $profile->getSeoData();
        $certificates = $profile->certificates()->get();
        
        return [
            'company_profile' => [
                'basic_info' => [
                    'company_name' => $profile->company_name,
                    'tagline' => $profile->tagline,
                    'about' => $profile->about,
                    'vision' => $profile->vision,
                    'mission' => $profile->mission,
                    'values' => $profile->values,
                ],
                'contact_info' => [
                    'email' => $profile->email,
                    'phone' => $profile->phone,
                    'whatsapp' => $profile->whatsapp,
                    'address' => $profile->address,
                    'city' => $profile->city,
                    'postal_code' => $profile->postal_code,
                    'country' => $profile->country,
                ],
                'social_media' => [
                    'facebook' => $profile->facebook,
                    'twitter' => $profile->twitter,
                    'instagram' => $profile->instagram,
                    'linkedin' => $profile->linkedin,
                    'youtube' => $profile->youtube,
                ],
                'location' => [
                    'latitude' => $profile->latitude,
                    'longitude' => $profile->longitude,
                ],
                'branding' => [
                    'logo_url' => $profile->logo_url,
                    'has_logo' => !empty($profile->logo),
                ],
            ],
            'seo_data' => $seo ? [
                'title' => $seo->title,
                'description' => $seo->description,
                'keywords' => $seo->keywords,
                'og_image' => $seo->og_image,
            ] : null,
            'certificates' => $certificates->map(function($cert) {
                return [
                    'name' => $cert->name,
                    'issuer' => $cert->issuer,
                    'issue_date' => $cert->issue_date,
                    'expiry_date' => $cert->expiry_date,
                    'status' => $cert->status,
                ];
            })->toArray(),
            'statistics' => $this->getStatistics(),
            'exported_at' => now()->toISOString(),
            'exported_by' => auth()->user() ? auth()->user()->name : 'System'
        ];
    }

    /**
     * Validate company profile data before updating.
     */
    public function validateProfileData(array $data): array
    {
        $errors = [];
        
        // Validate company name
        if (empty($data['company_name']) || !is_string($data['company_name']) || empty(trim($data['company_name']))) {
            $errors['company_name'] = 'Company name is required';
        }
        
        // Validate email
        if (empty($data['email']) || !is_string($data['email']) || !filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Valid email address is required';
        }
        
        // Validate phone
        if (empty($data['phone']) || !is_string($data['phone']) || empty(trim($data['phone']))) {
            $errors['phone'] = 'Phone number is required';
        }
        
        // Validate address
        if (empty($data['address']) || !is_string($data['address']) || empty(trim($data['address']))) {
            $errors['address'] = 'Business address is required';
        }
        
        // Validate URL fields
        $urlFields = ['facebook', 'twitter', 'instagram', 'linkedin', 'youtube'];
        foreach ($urlFields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                if (!is_string($data[$field]) || !filter_var(trim($data[$field]), FILTER_VALIDATE_URL)) {
                    $errors[$field] = 'Please enter a valid URL';
                }
            }
        }
        
        // Validate coordinates
        if (isset($data['latitude']) && !empty($data['latitude'])) {
            if (!is_numeric($data['latitude']) || !is_string($data['latitude'])) {
                $errors['latitude'] = 'Latitude must be a valid number';
            }
        }
        
        if (isset($data['longitude']) && !empty($data['longitude'])) {
            if (!is_numeric($data['longitude']) || !is_string($data['longitude'])) {
                $errors['longitude'] = 'Longitude must be a valid number';
            }
        }
        
        // Validate text length limits
        if (isset($data['about']) && !empty($data['about'])) {
            if (!is_string($data['about']) || strlen($data['about']) > 500) {
                $errors['about'] = 'About description must not exceed 500 characters';
            }
        }
        
        if (isset($data['tagline']) && !empty($data['tagline'])) {
            if (!is_string($data['tagline']) || strlen($data['tagline']) > 255) {
                $errors['tagline'] = 'Tagline must not exceed 255 characters';
            }
        }
        
        // Validate values array
        if (isset($data['values']) && !empty($data['values'])) {
            if (!is_array($data['values'])) {
                $errors['values'] = 'Values must be an array';
            } else {
                foreach ($data['values'] as $index => $value) {
                    if (!is_string($value)) {
                        $errors["values.{$index}"] = 'Each value must be a string';
                    }
                }
            }
        }
        
        return $errors;
    }

    /**
     * Get profile suggestions for improvement.
     */
    public function getProfileSuggestions(): array
    {
        $profile = $this->getProfile();
        $statistics = $this->getStatistics();
        $suggestions = [];
        
        // Logo suggestion
        if (!$statistics['has_logo']) {
            $suggestions[] = [
                'type' => 'warning',
                'title' => 'Add Company Logo',
                'message' => 'Upload your company logo to improve brand recognition.',
                'action' => 'Upload Logo',
                'route' => 'admin.company.edit'
            ];
        }
        
        // Description suggestion
        if (!$statistics['has_description']) {
            $suggestions[] = [
                'type' => 'info',
                'title' => 'Add Company Description',
                'message' => 'Write a compelling description about your company.',
                'action' => 'Add Description',
                'route' => 'admin.company.edit'
            ];
        }
        
        // Social media suggestion
        if ($statistics['social_links_count'] < 3) {
            $suggestions[] = [
                'type' => 'info',
                'title' => 'Connect Social Media',
                'message' => 'Add more social media links to increase online presence.',
                'action' => 'Add Social Links',
                'route' => 'admin.company.edit'
            ];
        }
        
        // SEO suggestion
        if (!$statistics['seo_configured']) {
            $suggestions[] = [
                'type' => 'warning',
                'title' => 'Configure SEO',
                'message' => 'Set up SEO information to improve search engine visibility.',
                'action' => 'Configure SEO',
                'route' => 'admin.company.seo'
            ];
        }
        
        // Vision/Mission suggestion
        if (empty($profile->vision) || empty($profile->mission)) {
            $suggestions[] = [
                'type' => 'info',
                'title' => 'Add Vision & Mission',
                'message' => 'Define your company\'s vision and mission statements.',
                'action' => 'Add Vision & Mission',
                'route' => 'admin.company.edit'
            ];
        }
        
        return $suggestions;
    }

    /**
     * Search company profiles (for future multi-company support).
     */
    public function searchProfiles(string $query = ''): \Illuminate\Database\Eloquent\Collection
    {
        return CompanyProfile::search($query)->get();
    }

    /**
     * Get company profile for public display.
     */
    public function getPublicProfile(): array
    {
        $profile = $this->getProfile();
        
        return [
            'name' => $profile->company_name,
            'tagline' => $profile->tagline,
            'about' => $profile->about,
            'vision' => $profile->vision,
            'mission' => $profile->mission,
            'values' => $profile->values,
            'contact' => [
                'email' => $profile->email,
                'phone' => $profile->phone,
                'whatsapp' => $profile->whatsapp,
                'address' => $profile->full_address,
            ],
            'social_links' => $profile->social_links,
            'logo_url' => $profile->logo_url,
            'location' => [
                'latitude' => $profile->latitude,
                'longitude' => $profile->longitude,
            ],
        ];
    }

    /**
     * Backup company profile data.
     */
    public function backupProfile(): string
    {
        $data = $this->exportProfileData();
        $filename = 'company-profile-backup-' . now()->format('Y-m-d-H-i-s') . '.json';
        $path = 'backups/company/' . $filename;
        
        Storage::disk('local')->put($path, json_encode($data, JSON_PRETTY_PRINT));
        
        Log::info('Company profile backup created', [
            'filename' => $filename,
            'path' => $path
        ]);
        
        return $path;
    }

    /**
     * Restore company profile from backup.
     */
    public function restoreProfile(string $backupPath): bool
    {
        try {
            if (!Storage::disk('local')->exists($backupPath)) {
                throw new \Exception('Backup file not found');
            }
            
            $backupData = json_decode(Storage::disk('local')->get($backupPath), true);
            
            if (!$backupData || !isset($backupData['company_profile'])) {
                throw new \Exception('Invalid backup file format');
            }
            
            $profile = $this->getProfile();
            $profileData = $backupData['company_profile'];
            
            // Merge all profile sections
            $updateData = array_merge(
                $profileData['basic_info'] ?? [],
                $profileData['contact_info'] ?? [],
                $profileData['social_media'] ?? [],
                $profileData['location'] ?? []
            );
            
            $profile->update($updateData);
            
            // Restore SEO data if exists
            if (isset($backupData['seo_data']) && $backupData['seo_data']) {
                $profile->updateSeo($backupData['seo_data']);
            }
            
            // Clear cache
            Cache::forget('company_profile');
            
            Log::info('Company profile restored from backup', [
                'backup_path' => $backupPath
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to restore company profile: ' . $e->getMessage(), [
                'backup_path' => $backupPath
            ]);
            
            return false;
        }
    }
}