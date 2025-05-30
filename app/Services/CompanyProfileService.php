<?php

namespace App\Services;

use App\Models\CompanyProfile;
use App\Services\FileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CompanyProfileService
{
    protected FileUploadService $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function getProfile(): CompanyProfile
    {
        return CompanyProfile::getInstance();
    }

    public function updateProfile(array $data, ?UploadedFile $logo = null, ?UploadedFile $logoWhite = null): CompanyProfile
    {
        $profile = $this->getProfile();

        if ($logo) {
            if ($profile->logo) {
                Storage::disk('public')->delete($profile->logo);
            }
            $data['logo'] = $this->fileUploadService->uploadImage($logo, 'company', null, 300);
        }

        if ($logoWhite) {
            if ($profile->logo_white) {
                Storage::disk('public')->delete($profile->logo_white);
            }
            $data['logo_white'] = $this->fileUploadService->uploadImage($logoWhite, 'company', null, 300);
        }

        $profile->update($data);
        return $profile;
    }

    public function updateSeo(array $seoData, ?UploadedFile $ogImage = null): CompanyProfile
    {
        $profile = $this->getProfile();

        if ($ogImage) {
            $seo = $profile->getSeoData();
            if ($seo && $seo->og_image) {
                Storage::disk('public')->delete($seo->og_image);
            }

            $path = $this->fileUploadService->uploadImage($ogImage, 'company/seo', null, 1200, 630);
            $seoData['og_image'] = $path;
        }

        $profile->updateSeo($seoData);
        return $profile;
    }

    public function getStatistics(): array
    {
        $profile = $this->getProfile();
        
        return [
            'profile_complete' => $this->isProfileComplete($profile),
            'social_links_count' => $this->countSocialLinks($profile),
            'has_logo' => !empty($profile->logo),
            'has_description' => !empty($profile->description),
            'contact_info_complete' => $this->isContactInfoComplete($profile),
        ];
    }

    protected function isProfileComplete(CompanyProfile $profile): bool
    {
        $requiredFields = ['legal_name', 'email', 'phone', 'address'];
        
        foreach ($requiredFields as $field) {
            if (empty($profile->$field)) {
                return false;
            }
        }
        
        return true;
    }

    protected function countSocialLinks(CompanyProfile $profile): int
    {
        $socialFields = ['facebook', 'twitter', 'instagram', 'linkedin', 'youtube'];
        $count = 0;
        
        foreach ($socialFields as $field) {
            if (!empty($profile->$field)) {
                $count++;
            }
        }
        
        return $count;
    }

    protected function isContactInfoComplete(CompanyProfile $profile): bool
    {
        return !empty($profile->email) && !empty($profile->phone) && !empty($profile->address);
    }
}