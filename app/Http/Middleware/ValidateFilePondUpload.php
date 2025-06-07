<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateFilePondUpload
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply to FilePond upload routes
        if (!$this->isFilePondRoute($request)) {
            return $next($request);
        }

        // Validate request structure
        if (!$this->validateRequestStructure($request)) {
            return response()->json([
                'error' => 'Invalid request structure'
            ], 422);
        }

        // Validate file if present
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            // Check file size (10MB max)
            if ($file->getSize() > 10 * 1024 * 1024) {
                return response()->json([
                    'error' => 'File size exceeds 10MB limit'
                ], 422);
            }

            // Check file type
            if (!$this->isAllowedFileType($file->getMimeType())) {
                return response()->json([
                    'error' => 'File type not allowed: ' . $file->getMimeType()
                ], 422);
            }

            // Check for malicious files
            if ($this->isMaliciousFile($file)) {
                return response()->json([
                    'error' => 'File appears to be malicious and cannot be uploaded'
                ], 422);
            }

            // Validate file name
            if (!$this->isValidFileName($file->getClientOriginalName())) {
                return response()->json([
                    'error' => 'Invalid file name'
                ], 422);
            }
        }

        return $next($request);
    }

    /**
     * Check if this is a FilePond route
     */
    protected function isFilePondRoute(Request $request): bool
    {
        return str_contains($request->route()->getName() ?? '', 'filepond');
    }

    /**
     * Validate request structure
     */
    protected function validateRequestStructure(Request $request): bool
    {
        // For process endpoint, we need a file
        if (str_contains($request->route()->getName() ?? '', 'process')) {
            return $request->hasFile('file');
        }

        // For revert endpoint, we need content
        if (str_contains($request->route()->getName() ?? '', 'revert')) {
            return !empty($request->getContent());
        }

        return true;
    }

    /**
     * Check if file type is allowed
     */
    protected function isAllowedFileType(string $mimeType): bool
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

            // CAD Files
            'application/dwg',
            'application/dxf',

            // Other
            'application/json',
            'application/xml',
        ];

        return in_array($mimeType, $allowedTypes);
    }

    /**
     * Check for malicious files
     */
    protected function isMaliciousFile($file): bool
    {
        $fileName = strtolower($file->getClientOriginalName());
        $content = file_get_contents($file->getRealPath());

        // Check for dangerous file extensions
        $dangerousExtensions = [
            '.exe', '.bat', '.cmd', '.com', '.pif', '.scr', '.vbs', '.js',
            '.jar', '.php', '.pl', '.py', '.sh', '.asp', '.aspx', '.jsp'
        ];

        foreach ($dangerousExtensions as $ext) {
            if (str_ends_with($fileName, $ext)) {
                return true;
            }
        }

        // Check for script content in files
        $maliciousPatterns = [
            '/<script/i',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload=/i',
            '/onerror=/i',
            '/<iframe/i',
            '/<object/i',
            '/<embed/i',
            '/eval\(/i',
            '/base64_decode/i',
        ];

        foreach ($maliciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        // Check for PHP tags in non-PHP files
        if (!str_ends_with($fileName, '.php') && 
            (strpos($content, '<?php') !== false || strpos($content, '<?=') !== false)) {
            return true;
        }

        return false;
    }

    /**
     * Validate file name
     */
    protected function isValidFileName(string $fileName): bool
    {
        // Check length
        if (strlen($fileName) > 255) {
            return false;
        }

        // Check for dangerous characters
        $dangerousChars = ['..', '\\', '/', ':', '*', '?', '"', '<', '>', '|'];
        foreach ($dangerousChars as $char) {
            if (strpos($fileName, $char) !== false) {
                return false;
            }
        }

        // Check for hidden files (starting with .)
        if (str_starts_with($fileName, '.')) {
            return false;
        }

        // Check for reserved names (Windows)
        $reservedNames = [
            'CON', 'PRN', 'AUX', 'NUL', 'COM1', 'COM2', 'COM3', 'COM4', 'COM5',
            'COM6', 'COM7', 'COM8', 'COM9', 'LPT1', 'LPT2', 'LPT3', 'LPT4',
            'LPT5', 'LPT6', 'LPT7', 'LPT8', 'LPT9'
        ];

        $nameWithoutExt = pathinfo($fileName, PATHINFO_FILENAME);
        if (in_array(strtoupper($nameWithoutExt), $reservedNames)) {
            return false;
        }

        return true;
    }
}