<?php
// File: app/Helpers/FileHelper.php

namespace App\Helpers;

class FileHelper
{
    /**
     * Format file size in human readable format.
     *
     * @param int $size File size in bytes
     * @param int $precision Number of decimal places
     * @return string Formatted file size
     */
    public static function formatFileSize(int $size, int $precision = 2): string
{
    if ($size === 0) {
        return '0 B';
    }

    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    $base = log($size, 1024);
    $index = floor($base);

    // Ensure we don't exceed array bounds
    $index = min($index, count($units) - 1);
    $index = max($index, 0);

    $formattedSize = round(pow(1024, $base - $index), $precision);
    
    // Ensure we always return a string
    $result = $formattedSize . ' ' . $units[$index];
    
    return (string) $result;
}

    /**
     * Get file icon class based on file type/extension.
     *
     * @param string $mimeType File MIME type
     * @param string|null $extension File extension (optional)
     * @return string Icon class or SVG
     */
    public static function getFileIcon(string $mimeType, ?string $extension = null): string
    {
        // Image files
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        // Document types
        $iconMap = [
            // PDF
            'application/pdf' => 'pdf',
            
            // Word documents
            'application/msword' => 'word',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'word',
            
            // Excel spreadsheets
            'application/vnd.ms-excel' => 'excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'excel',
            'text/csv' => 'excel',
            
            // PowerPoint presentations
            'application/vnd.ms-powerpoint' => 'powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'powerpoint',
            
            // Archives
            'application/zip' => 'archive',
            'application/x-rar-compressed' => 'archive',
            'application/x-7z-compressed' => 'archive',
            'application/gzip' => 'archive',
            'application/x-tar' => 'archive',
            
            // Text files
            'text/plain' => 'text',
            'text/html' => 'code',
            'text/css' => 'code',
            'text/javascript' => 'code',
            'application/javascript' => 'code',
            'application/json' => 'code',
            'application/xml' => 'code',
            'text/xml' => 'code',
            
            // Video files
            'video/mp4' => 'video',
            'video/avi' => 'video',
            'video/mov' => 'video',
            'video/wmv' => 'video',
            'video/flv' => 'video',
            'video/webm' => 'video',
            
            // Audio files
            'audio/mp3' => 'audio',
            'audio/wav' => 'audio',
            'audio/ogg' => 'audio',
            'audio/m4a' => 'audio',
            'audio/aac' => 'audio',
        ];

        return $iconMap[$mimeType] ?? 'file';
    }

    /**
     * Get file type category.
     *
     * @param string $mimeType
     * @return string
     */
    public static function getFileCategory(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }

        if (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        }

        $documentTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'text/csv',
        ];

        if (in_array($mimeType, $documentTypes)) {
            return 'document';
        }

        $archiveTypes = [
            'application/zip',
            'application/x-rar-compressed',
            'application/x-7z-compressed',
            'application/gzip',
            'application/x-tar',
        ];

        if (in_array($mimeType, $archiveTypes)) {
            return 'archive';
        }

        return 'other';
    }

    /**
     * Check if file type is allowed for upload.
     *
     * @param string $mimeType
     * @return bool
     */
    public static function isAllowedFileType(string $mimeType): bool
    {
        $allowedTypes = [
            // Documents
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'text/csv',

            // Images
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/svg+xml',
            'image/webp',

            // Archives
            'application/zip',
            'application/x-rar-compressed',
            'application/x-7z-compressed',

            // CAD Files (if needed)
            'application/dwg',
            'application/dxf',

            // Other
            'application/json',
            'application/xml',
            'text/xml',
        ];

        return in_array($mimeType, $allowedTypes) || str_starts_with($mimeType, 'image/');
    }

    /**
     * Get maximum allowed file size in bytes.
     *
     * @return int
     */
    public static function getMaxFileSize(): int
    {
        return config('filesystems.max_file_size', 10 * 1024 * 1024); // 10MB default
    }

    /**
     * Validate file for upload.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function validateFile(\Illuminate\Http\UploadedFile $file): array
    {
        $errors = [];

        // Check file size
        if ($file->getSize() > self::getMaxFileSize()) {
            $errors[] = 'File size exceeds ' . self::formatFileSize(self::getMaxFileSize()) . ' limit';
        }

        // Check file type
        if (!self::isAllowedFileType($file->getMimeType())) {
            $errors[] = 'File type not allowed: ' . $file->getMimeType();
        }

        // Check filename length
        if (strlen($file->getClientOriginalName()) > 255) {
            $errors[] = 'Filename too long (max 255 characters)';
        }

        // Check for potentially dangerous files
        $dangerousExtensions = ['exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js'];
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (in_array($extension, $dangerousExtensions)) {
            $errors[] = 'File type potentially dangerous';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Get file extension from MIME type.
     *
     * @param string $mimeType
     * @return string
     */
    public static function getExtensionFromMimeType(string $mimeType): string
    {
        $mimeToExtension = [
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/vnd.ms-powerpoint' => 'ppt',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'text/plain' => 'txt',
            'text/csv' => 'csv',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/svg+xml' => 'svg',
            'image/webp' => 'webp',
            'application/zip' => 'zip',
            'application/x-rar-compressed' => 'rar',
            'application/x-7z-compressed' => '7z',
            'application/json' => 'json',
            'application/xml' => 'xml',
            'text/xml' => 'xml',
        ];

        return $mimeToExtension[$mimeType] ?? 'file';
    }

    /**
     * Generate safe filename.
     *
     * @param string $filename
     * @return string
     */
    public static function sanitizeFilename(string $filename): string
    {
        // Remove or replace dangerous characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        
        // Remove multiple underscores
        $filename = preg_replace('/_+/', '_', $filename);
        
        // Remove leading/trailing underscores and dots
        $filename = trim($filename, '_.');
        
        // Ensure filename is not empty
        if (empty($filename)) {
            $filename = 'file_' . time();
        }

        return $filename;
    }

    /**
     * Get human readable file type name.
     *
     * @param string $mimeType
     * @return string
     */
    public static function getFileTypeName(string $mimeType): string
    {
        $typeNames = [
            'application/pdf' => 'PDF Document',
            'application/msword' => 'Word Document',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'Word Document',
            'application/vnd.ms-excel' => 'Excel Spreadsheet',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'Excel Spreadsheet',
            'application/vnd.ms-powerpoint' => 'PowerPoint Presentation',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'PowerPoint Presentation',
            'text/plain' => 'Text File',
            'text/csv' => 'CSV File',
            'application/zip' => 'ZIP Archive',
            'application/x-rar-compressed' => 'RAR Archive',
            'application/x-7z-compressed' => '7-Zip Archive',
            'application/json' => 'JSON File',
            'application/xml' => 'XML File',
            'text/xml' => 'XML File',
        ];

        if (str_starts_with($mimeType, 'image/')) {
            return 'Image File';
        }

        if (str_starts_with($mimeType, 'video/')) {
            return 'Video File';
        }

        if (str_starts_with($mimeType, 'audio/')) {
            return 'Audio File';
        }

        return $typeNames[$mimeType] ?? 'Unknown File Type';
    }

    /**
     * Calculate total size of multiple files.
     *
     * @param array $fileSizes Array of file sizes in bytes
     * @return int Total size in bytes
     */
    public static function calculateTotalSize(array $fileSizes): int
    {
        return array_sum($fileSizes);
    }

    /**
     * Check if total file size exceeds limit.
     *
     * @param array $fileSizes
     * @param int|null $limit Custom limit in bytes (null for default)
     * @return bool
     */
    public static function exceedsSizeLimit(array $fileSizes, ?int $limit = null): bool
    {
        $totalSize = self::calculateTotalSize($fileSizes);
        $maxSize = $limit ?? config('filesystems.total_upload_limit', 50 * 1024 * 1024); // 50MB default
        
        return $totalSize > $maxSize;
    }
}