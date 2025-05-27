<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && $request->is('client/*')) {
            $this->setClientLocale($request);
        }

        return $next($request);
    }

    /**
     * Set client locale based on user preference.
     */
    protected function setClientLocale(Request $request): void
    {
        $user = auth()->user();
        
        // Get locale from user preference, session, or browser
        $locale = $user->preferred_locale ?? 
                 session('locale') ?? 
                 $this->getBrowserLocale($request) ?? 
                 config('app.locale');

        // Validate locale is supported
        $supportedLocales = config('app.supported_locales', ['en']);
        if (!in_array($locale, $supportedLocales)) {
            $locale = config('app.locale');
        }

        app()->setLocale($locale);
        session(['locale' => $locale]);
    }

    /**
     * Get browser locale from Accept-Language header.
     */
    protected function getBrowserLocale(Request $request): ?string
    {
        $acceptLanguage = $request->header('Accept-Language');
        if (!$acceptLanguage) {
            return null;
        }

        // Parse Accept-Language header
        preg_match('/^([a-z]{2})/', $acceptLanguage, $matches);
        return $matches[1] ?? null;
    }
}