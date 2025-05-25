<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

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
     * Alias for human_filesize function
     *
     * @param integer $bytes Size in bytes to convert
     * @param integer $precision Number of decimal places to show
     * @return string
     */
    function format_file_size($bytes, $precision = 2)
    {
        return human_filesize($bytes, $precision);
    }
}

if (!function_exists('get_file_icon_class')) {
    /**
     * Get CSS class for file type icon
     *
     * @param string $filename
     * @return string
     */
    function get_file_icon_class($filename)
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        return match($extension) {
            'pdf' => 'text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900/30',
            'doc', 'docx' => 'text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/30',
            'xls', 'xlsx' => 'text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900/30',
            'jpg', 'jpeg', 'png', 'gif', 'webp' => 'text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-900/30',
            'zip', 'rar', '7z' => 'text-orange-600 dark:text-orange-400 bg-orange-100 dark:bg-orange-900/30',
            default => 'text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700'
        };
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
}