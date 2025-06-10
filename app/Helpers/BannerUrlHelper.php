<?php
// File: app/Helpers/BannerUrlHelper.php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;

class BannerUrlHelper
{
    /**
     * Process banner URL with base URL support
     */
    public static function processUrl($url, $linkType = null, $baseUrl = null)
    {
        if (empty($url)) {
            return null;
        }

        $baseUrl = $baseUrl ?: config('app.url');
        
        // Auto-detect link type if not provided
        if (!$linkType || $linkType === 'auto') {
            $linkType = self::detectLinkType($url);
        }

        return match($linkType) {
            'external' => self::processExternalUrl($url),
            'internal' => self::processInternalUrl($url, $baseUrl),
            'route' => self::processRouteUrl($url),
            'email' => self::processEmailUrl($url),
            'phone' => self::processPhoneUrl($url),
            'anchor' => self::processAnchorUrl($url),
            default => self::autoProcessUrl($url, $baseUrl)
        };
    }

    /**
     * Detect link type based on URL pattern
     */
    public static function detectLinkType($url)
    {
        // Email detection
        if (filter_var($url, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }

        // Phone detection
        if (preg_match('/^\+?[\d\s\-\(\)]+$/', $url)) {
            return 'phone';
        }

        // Anchor detection
        if (str_starts_with($url, '#')) {
            return 'anchor';
        }

        // External URL detection
        if (preg_match('/^https?:\/\//', $url)) {
            $parsed = parse_url($url);
            $currentDomain = parse_url(config('app.url'), PHP_URL_HOST);
            
            if (isset($parsed['host']) && $parsed['host'] !== $currentDomain) {
                return 'external';
            }
            return 'internal';
        }

        // Route detection
        if (preg_match('/^[a-zA-Z][a-zA-Z0-9._-]*$/', $url) && Route::has($url)) {
            return 'route';
        }

        // Default to internal
        return 'internal';
    }

    /**
     * Process external URL
     */
    protected static function processExternalUrl($url)
    {
        // Ensure protocol is present
        if (!preg_match('/^https?:\/\//', $url)) {
            return 'https://' . $url;
        }
        return $url;
    }

    /**
     * Process internal URL with base URL
     */
    protected static function processInternalUrl($url, $baseUrl)
    {
        // If already a full URL, check if it's our domain
        if (preg_match('/^https?:\/\//', $url)) {
            $parsed = parse_url($url);
            $baseParsed = parse_url($baseUrl);
            
            if (isset($parsed['host']) && $parsed['host'] === $baseParsed['host']) {
                return $url; // Same domain, return as is
            }
            return $url; // Different domain, treat as external
        }

        // Clean up the URL path
        $url = ltrim($url, '/');
        
        // Use Laravel's url() helper for proper URL generation
        return url($url);
    }

    /**
     * Process route URL
     */
    protected static function processRouteUrl($routeName)
    {
        try {
            // Handle routes with parameters
            if (str_contains($routeName, ':')) {
                [$route, $params] = explode(':', $routeName, 2);
                $paramArray = array_map('trim', explode(',', $params));
                return route($route, $paramArray);
            }

            return route($routeName);
        } catch (\Exception $e) {
            \Log::warning("Invalid route name: {$routeName}", [
                'error' => $e->getMessage()
            ]);
            return '#invalid-route';
        }
    }

    /**
     * Process email URL
     */
    protected static function processEmailUrl($email)
    {
        return 'mailto:' . $email;
    }

    /**
     * Process phone URL
     */
    protected static function processPhoneUrl($phone)
    {
        // Clean phone number for tel: link
        $cleanPhone = preg_replace('/[^\d+]/', '', $phone);
        return 'tel:' . $cleanPhone;
    }

    /**
     * Process anchor URL
     */
    protected static function processAnchorUrl($anchor)
    {
        return str_starts_with($anchor, '#') ? $anchor : '#' . $anchor;
    }

    /**
     * Auto-process URL with intelligent detection
     */
    protected static function autoProcessUrl($url, $baseUrl)
    {
        $detectedType = self::detectLinkType($url);
        return self::processUrl($url, $detectedType, $baseUrl);
    }

    /**
     * Check if URL should open in new tab
     */
    public static function shouldOpenInNewTab($url, $linkType = null, $forceNewTab = false)
    {
        // If explicitly forced, return true
        if ($forceNewTab) {
            return true;
        }

        // Auto-detect if type not provided
        if (!$linkType) {
            $linkType = self::detectLinkType($url);
        }

        // External links should always open in new tab
        return $linkType === 'external';
    }

    /**
     * Generate complete link attributes array
     */
    public static function getLinkAttributes($url, $linkType = null, $openInNewTab = false, $baseUrl = null)
    {
        $processedUrl = self::processUrl($url, $linkType, $baseUrl);
        $shouldOpenNewTab = self::shouldOpenInNewTab($url, $linkType, $openInNewTab);

        $attributes = ['href' => $processedUrl];

        if ($shouldOpenNewTab) {
            $attributes['target'] = '_blank';
            $attributes['rel'] = 'noopener noreferrer';
        }

        return $attributes;
    }

    /**
     * Generate HTML link tag
     */
    public static function generateLinkHtml($url, $text, $linkType = null, $openInNewTab = false, $classes = '', $baseUrl = null)
    {
        if (empty($url) || empty($text)) {
            return '';
        }

        $attributes = self::getLinkAttributes($url, $linkType, $openInNewTab, $baseUrl);
        
        if ($classes) {
            $attributes['class'] = $classes;
        }

        $html = '<a';
        foreach ($attributes as $attr => $value) {
            $html .= ' ' . $attr . '="' . htmlspecialchars($value) . '"';
        }
        $html .= '>' . htmlspecialchars($text) . '</a>';

        return $html;
    }

    /**
     * Validate URL format
     */
    public static function validateUrl($url, $linkType = null)
    {
        if (empty($url)) {
            return ['valid' => false, 'message' => 'URL cannot be empty'];
        }

        $linkType = $linkType ?: self::detectLinkType($url);

        switch ($linkType) {
            case 'email':
                if (!filter_var($url, FILTER_VALIDATE_EMAIL)) {
                    return ['valid' => false, 'message' => 'Invalid email format'];
                }
                break;

            case 'phone':
                if (!preg_match('/^\+?[\d\s\-\(\)]+$/', $url)) {
                    return ['valid' => false, 'message' => 'Invalid phone number format'];
                }
                break;

            case 'external':
                $processedUrl = self::processExternalUrl($url);
                if (!filter_var($processedUrl, FILTER_VALIDATE_URL)) {
                    return ['valid' => false, 'message' => 'Invalid external URL format'];
                }
                break;

            case 'route':
                $routeName = str_contains($url, ':') ? explode(':', $url)[0] : $url;
                if (!Route::has($routeName)) {
                    return ['valid' => false, 'message' => "Route '{$routeName}' does not exist"];
                }
                break;

            case 'anchor':
                if (!str_starts_with($url, '#') && !preg_match('/^[a-zA-Z][\w-]*$/', ltrim($url, '#'))) {
                    return ['valid' => false, 'message' => 'Invalid anchor format'];
                }
                break;
        }

        return ['valid' => true, 'message' => 'URL is valid'];
    }

    /**
     * Get common route suggestions for banners
     */
    public static function getCommonRoutes()
    {
        $commonRoutes = [
            'home' => 'Home Page',
            'about' => 'About Page',
            'contact' => 'Contact Page',
            'services' => 'Services Page',
            'products' => 'Products Page',
            'blog' => 'Blog Page',
            'portfolio' => 'Portfolio Page',
            'login' => 'Login Page',
            'register' => 'Register Page',
        ];

        // Filter to only include routes that actually exist
        $existingRoutes = [];
        foreach ($commonRoutes as $route => $label) {
            if (Route::has($route)) {
                $existingRoutes[$route] = $label;
            }
        }

        return $existingRoutes;
    }

    /**
     * Get URL preview information
     */
    public static function getUrlPreview($url, $linkType = null, $baseUrl = null)
    {
        if (empty($url)) {
            return null;
        }

        $linkType = $linkType ?: self::detectLinkType($url);
        $processedUrl = self::processUrl($url, $linkType, $baseUrl);

        return [
            'original' => $url,
            'processed' => $processedUrl,
            'type' => $linkType,
            'type_label' => self::getLinkTypeLabel($linkType),
            'opens_new_tab' => self::shouldOpenInNewTab($url, $linkType),
            'is_valid' => self::validateUrl($url, $linkType)['valid']
        ];
    }

    /**
     * Get human-readable link type label
     */
    public static function getLinkTypeLabel($linkType)
    {
        return match($linkType) {
            'external' => 'External Link',
            'internal' => 'Internal Link',
            'route' => 'Route Link',
            'email' => 'Email Link',
            'phone' => 'Phone Link',
            'anchor' => 'Anchor Link',
            'auto' => 'Auto-detect',
            default => 'Unknown'
        };
    }

    /**
     * Convert relative URL to absolute using base URL
     */
    public static function makeAbsolute($url, $baseUrl = null)
    {
        if (empty($url)) {
            return null;
        }

        // Already absolute
        if (preg_match('/^https?:\/\//', $url)) {
            return $url;
        }

        // Protocol-relative
        if (str_starts_with($url, '//')) {
            $protocol = parse_url($baseUrl ?: config('app.url'), PHP_URL_SCHEME);
            return $protocol . ':' . $url;
        }

        // Relative URL
        $baseUrl = rtrim($baseUrl ?: config('app.url'), '/');
        $url = ltrim($url, '/');
        
        return $baseUrl . '/' . $url;
    }

    /**
     * Extract domain from URL
     */
    public static function extractDomain($url)
    {
        $parsed = parse_url($url);
        return $parsed['host'] ?? null;
    }

    /**
     * Check if URL is same domain as base URL
     */
    public static function isSameDomain($url, $baseUrl = null)
    {
        $baseUrl = $baseUrl ?: config('app.url');
        $baseDomain = self::extractDomain($baseUrl);
        $urlDomain = self::extractDomain($url);
        
        return $baseDomain === $urlDomain;
    }
}