<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;


if (!function_exists('settings')) {
    /**
     * Get setting value by key
     */
    function settings(string $key, $default = null)
    {
        try {
            return app(App\Services\SettingsService::class)->get($key, $default);
        } catch (\Exception $e) {
            \Log::warning("Settings helper error for key '{$key}': " . $e->getMessage());
            return $default;
        }
    }
}

if (!function_exists('update_setting')) {
    /**
     * Update setting value
     */
    function update_setting(string $key, $value): bool
    {
        try {
            return app(App\Services\SettingsService::class)->set($key, $value);
        } catch (\Exception $e) {
            \Log::error("Failed to update setting '{$key}': " . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('site_name')) {
    /**
     * Get site name
     */
    function site_name(): string
    {
        return settings('site_name', config('app.name'));
    }
}

if (!function_exists('site_description')) {
    /**
     * Get site description
     */
    function site_description(): string
    {
        return settings('site_description', 'Professional Construction & General Supplier');
    }
}

if (!function_exists('contact_email')) {
    /**
     * Get contact email
     */
    function contact_email(): string
    {
        return settings('contact_email', config('mail.from.address'));
    }
}

if (!function_exists('contact_phone')) {
    /**
     * Get contact phone
     */
    function contact_phone(): string
    {
        return settings('contact_phone', '');
    }
}

// SEO Helper Functions
if (!function_exists('seo_title')) {
    /**
     * Generate SEO title
     */
    function seo_title(?string $title = null): string
    {
        return \App\Helpers\SeoHelper::generateTitle($title);
    }
}

if (!function_exists('seo_description')) {
    /**
     * Generate SEO description
     */
    function seo_description(?string $content = null): string
    {
        return \App\Helpers\SeoHelper::generateDescription($content);
    }
}

if (!function_exists('seo_keywords')) {
    /**
     * Generate SEO keywords
     */
    function seo_keywords(?string $additional = null): string
    {
        return \App\Helpers\SeoHelper::generateKeywords($additional);
    }
}

if (!function_exists('og_image')) {
    /**
     * Generate Open Graph image URL
     */
    function og_image($model = null): string
    {
        return \App\Helpers\SeoHelper::generateOgImage($model);
    }
}

if (!function_exists('canonical_url')) {
    /**
     * Generate canonical URL
     */
    function canonical_url(?string $path = null): string
    {
        return \App\Helpers\SeoHelper::generateCanonicalUrl($path);
    }
}

if (!function_exists('company_schema')) {
    /**
     * Generate company schema
     */
    function company_schema(): array
    {
        return \App\Helpers\SeoHelper::generateCompanySchema();
    }
}

if (!function_exists('breadcrumb_schema')) {
    /**
     * Generate breadcrumb schema
     */
    function breadcrumb_schema(array $breadcrumbs): array
    {
        return \App\Helpers\SeoHelper::generateBreadcrumbSchema($breadcrumbs);
    }
}

if (!function_exists('human_filesize')) {
    /**
     * Convert bytes to human readable format
     *
     * @param integer $bytes Size in bytes to convert
     * @param integer $precision Number of decimal places to show
     * @return string
     */
    function human_filesize($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

if (!function_exists('format_file_size')) {
    /**
     * Format file size in human readable format.
     *
     * @param int $size
     * @param int $precision
     * @return string
     */
    function format_file_size(int $size, int $precision = 2): string
    {
        return \App\Helpers\FileHelper::formatFileSize($size, $precision);
    }
}

if (!function_exists('get_file_icon')) {
    /**
     * Get file icon based on MIME type.
     *
     * @param string $mimeType
     * @param string|null $extension
     * @return string
     */
    function get_file_icon(string $mimeType, ?string $extension = null): string
    {
        return \App\Helpers\FileHelper::getFileIcon($mimeType, $extension);
    }
}

if (!function_exists('is_image_file')) {
    /**
     * Check if file is an image
     *
     * @param string $filename
     * @return bool
     */
    function is_image_file($filename)
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
    }
}

if (!function_exists('get_file_type_name')) {
    /**
     * Get human readable file type name
     *
     * @param string $filename
     * @return string
     */
    function get_file_type_name($filename)
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        return match($extension) {
            'pdf' => 'PDF Document',
            'doc' => 'Word Document',
            'docx' => 'Word Document',
            'xls' => 'Excel Spreadsheet',
            'xlsx' => 'Excel Spreadsheet',
            'jpg', 'jpeg' => 'JPEG Image',
            'png' => 'PNG Image',
            'gif' => 'GIF Image',
            'webp' => 'WebP Image',
            'zip' => 'ZIP Archive',
            'rar' => 'RAR Archive',
            '7z' => '7-Zip Archive',
            default => strtoupper($extension) . ' File'
        };
    }

    if (!function_exists('settings')) {
    /**
     * Get a setting value
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    function settings($key = null, $default = null)
    {
        if (is_null($key)) {
            return Cache::remember('settings', 3600, function () {
                try {
                    return Setting::pluck('value', 'key')->toArray();
                } catch (\Exception $e) {
                    return [];
                }
            });
        }

        $settings = Cache::remember('settings', 3600, function () {
            try {
                return Setting::pluck('value', 'key')->toArray();
            } catch (\Exception $e) {
                return [];
            }
        });

        return $settings[$key] ?? $default;
    }
}

if (!function_exists('update_setting')) {
    /**
     * Update a setting value
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    function update_setting($key, $value): bool
    {
        try {
            $setting = Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );

            // Clear cache
            Cache::forget('settings');

            // Return true if the setting was created or updated successfully
            return $setting !== null;
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('get_setting')) {
    /**
     * Get a single setting value (alias for settings function)
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function get_setting($key, $default = null)
    {
        return settings($key, $default);
    }
}

if (!function_exists('has_setting')) {
    /**
     * Check if a setting exists
     *
     * @param string $key
     * @return bool
     */
    function has_setting($key): bool
    {
        try {
            $settings = settings();
            return array_key_exists($key, $settings);
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('remove_setting')) {
    /**
     * Remove a setting
     *
     * @param string $key
     * @return bool
     */
    function remove_setting($key): bool
    {
        try {
            $deleted = Setting::where('key', $key)->delete();
            
            // Clear cache
            Cache::forget('settings');
            
            return $deleted > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('settings_array')) {
    /**
     * Get all settings as an array
     *
     * @return array
     */
    function settings_array(): array
    {
        return settings() ?? [];
    }
}

if (!function_exists('bulk_update_settings')) {
    /**
     * Update multiple settings at once
     *
     * @param array $settings
     * @return bool
     */
    function bulk_update_settings(array $settings): bool
    {
        try {
            foreach ($settings as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
            
            // Clear cache
            Cache::forget('settings');
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
if (!function_exists('is_allowed_file_type')) {
    /**
     * Check if file type is allowed for upload.
     *
     * @param string $mimeType
     * @return bool
     */
    function is_allowed_file_type(string $mimeType): bool
    {
        return \App\Helpers\FileHelper::isAllowedFileType($mimeType);
    }
}

if (!function_exists('validate_uploaded_file')) {
    /**
     * Validate uploaded file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return array
     */
    function validate_uploaded_file(\Illuminate\Http\UploadedFile $file): array
    {
        return \App\Helpers\FileHelper::validateFile($file);
    }
}

if (!function_exists('sanitize_filename')) {
    /**
     * Sanitize filename for safe storage.
     *
     * @param string $filename
     * @return string
     */
    function sanitize_filename(string $filename): string
    {
        return \App\Helpers\FileHelper::sanitizeFilename($filename);
    }
}

if (!function_exists('get_file_type_name')) {
    /**
     * Get human readable file type name.
     *
     * @param string $mimeType
     * @return string
     */
    function get_file_type_name(string $mimeType): string
    {
        return \App\Helpers\FileHelper::getFileTypeName($mimeType);
    }
}
}