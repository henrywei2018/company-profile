<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateClientRequest
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Validate client-specific request parameters
        if ($request->is('client/*')) {
            $this->validateClientRequest($request);
        }

        return $next($request);
    }

    /**
     * Validate client request parameters.
     */
    protected function validateClientRequest(Request $request): void
    {
        // Check for suspicious parameters
        $suspiciousParams = ['admin', 'root', 'administrator', 'sudo'];
        
        foreach ($request->all() as $key => $value) {
            if (in_array(strtolower($key), $suspiciousParams) || 
                (is_string($value) && in_array(strtolower($value), $suspiciousParams))) {
                \Log::warning('Suspicious client request detected', [
                    'user_id' => auth()->id(),
                    'ip' => $request->ip(),
                    'params' => $request->all(),
                    'url' => $request->fullUrl(),
                ]);
                
                abort(400, 'Invalid request parameters');
            }
        }

        // Validate file uploads for client area
        if ($request->hasFile('files')) {
            $this->validateClientFileUploads($request);
        }
    }

    /**
     * Validate client file uploads.
     */
    protected function validateClientFileUploads(Request $request): void
    {
        $allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'gif', 'zip'];
        $maxFileSize = 10 * 1024 * 1024; // 10MB

        foreach ($request->file('files', []) as $file) {
            if (!$file->isValid()) {
                abort(400, 'Invalid file upload');
            }

            $extension = strtolower($file->getClientOriginalExtension());
            if (!in_array($extension, $allowedExtensions)) {
                abort(400, 'File type not allowed: ' . $extension);
            }

            if ($file->getSize() > $maxFileSize) {
                abort(400, 'File size too large. Maximum allowed: 10MB');
            }
        }
    }
}