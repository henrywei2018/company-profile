<?php

namespace App\Helpers;

use App\Models\CompanyProfile;

class SeoHelper
{
    /**
     * Generate page title with format
     */
    public static function generateTitle(?string $pageTitle = null): string
    {
        $siteName = settings('site_name', config('app.name'));
        $titleFormat = settings('seo_title_format', '%title% | %site_name%');
        
        if (!$pageTitle) {
            return $siteName;
        }
        
        return str_replace(
            ['%title%', '%site_name%'],
            [$pageTitle, $siteName],
            $titleFormat
        );
    }
    
    /**
     * Generate meta description
     */
    public static function generateDescription(?string $content = null, int $maxLength = 160): string
    {
        if ($content) {
            $description = strip_tags($content);
            return strlen($description) > $maxLength 
                ? substr($description, 0, $maxLength - 3) . '...'
                : $description;
        }
        
        return settings('seo_description', settings('site_description', ''));
    }
    
    /**
     * Generate meta keywords
     */
    public static function generateKeywords(?string $additionalKeywords = null): string
    {
        $defaultKeywords = settings('seo_keywords', '');
        
        if ($additionalKeywords) {
            return $defaultKeywords ? $defaultKeywords . ', ' . $additionalKeywords : $additionalKeywords;
        }
        
        return $defaultKeywords;
    }
    
    /**
     * Generate canonical URL
     */
    public static function generateCanonicalUrl(?string $path = null): string
    {
        if ($path) {
            return url($path);
        }
        
        return request()->url();
    }
    
    /**
     * Generate Open Graph image URL
     */
    public static function generateOgImage($model = null): string
    {
        // Check if model has specific OG image
        if ($model && method_exists($model, 'getOgImageAttribute')) {
            $ogImage = $model->getOgImageAttribute();
            if ($ogImage) {
                return $ogImage;
            }
        }
        
        // Check if model has featured image
        if ($model && isset($model->featured_image) && $model->featured_image) {
            return asset('storage/' . $model->featured_image);
        }
        
        // Check if model has image
        if ($model && isset($model->image) && $model->image) {
            return asset('storage/' . $model->image);
        }
        
        // Use company logo
        $companyProfile = CompanyProfile::getInstance();
        if ($companyProfile->logo_url) {
            return $companyProfile->logo_url;
        }
        
        // Default OG image
        return asset('images/og-default.jpg');
    }
    
    /**
     * Generate schema.org JSON-LD for company
     */
    public static function generateCompanySchema(): array
    {
        $companyProfile = CompanyProfile::getInstance();
        
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => settings('business_type', 'Organization'),
            'name' => $companyProfile->company_name ?: settings('site_name'),
            'url' => url('/'),
            'description' => $companyProfile->about ?: settings('site_description'),
        ];
        
        // Add contact info
        if ($companyProfile->email) {
            $schema['email'] = $companyProfile->email;
        }
        
        if ($companyProfile->phone) {
            $schema['telephone'] = $companyProfile->phone;
        }
        
        // Add address
        if ($companyProfile->address) {
            $schema['address'] = [
                '@type' => 'PostalAddress',
                'streetAddress' => $companyProfile->address,
                'addressLocality' => $companyProfile->city,
                'postalCode' => $companyProfile->postal_code,
                'addressCountry' => $companyProfile->country ?: 'ID'
            ];
        }
        
        // Add logo
        if ($companyProfile->logo_url) {
            $schema['logo'] = $companyProfile->logo_url;
        }
        
        // Add social media
        $socialLinks = $companyProfile->social_links;
        if (!empty($socialLinks)) {
            $schema['sameAs'] = array_values($socialLinks);
        }
        
        return $schema;
    }
    
    /**
     * Generate breadcrumb schema
     */
    public static function generateBreadcrumbSchema(array $breadcrumbs): array
    {
        $items = [];
        
        foreach ($breadcrumbs as $index => $breadcrumb) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $breadcrumb['name'],
                'item' => isset($breadcrumb['url']) ? url($breadcrumb['url']) : null
            ];
        }
        
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items
        ];
    }
    
    /**
     * Generate robots meta content
     */
    public static function generateRobots(bool $index = true, bool $follow = true): string
    {
        $robots = [];
        $robots[] = $index ? 'index' : 'noindex';
        $robots[] = $follow ? 'follow' : 'nofollow';
        
        return implode(', ', $robots);
    }
    
    /**
     * Get all tracking codes
     */
    public static function getTrackingCodes(): array
    {
        return [
            'google_analytics' => settings('google_analytics_id'),
            'google_tag_manager' => settings('google_tag_manager_id'),
            'google_verification' => settings('seo_google_verification'),
            'bing_verification' => settings('seo_bing_verification'),
        ];
    }
}