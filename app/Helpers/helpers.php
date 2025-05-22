<?php
// File: app/helpers.php

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
}