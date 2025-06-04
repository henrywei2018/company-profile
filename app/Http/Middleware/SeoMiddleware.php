<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Helpers\SeoHelper;

class SeoMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Only process HTML responses
        if (!$this->isHtmlResponse($response)) {
            return $response;
        }
        
        // Skip admin routes
        if ($request->is('admin/*')) {
            return $response;
        }
        
        // Get current route info for auto SEO
        $routeName = $request->route()?->getName();
        $this->injectAutoSeo($routeName, $request);
        
        return $response;
    }
    
    /**
     * Check if response is HTML
     */
    protected function isHtmlResponse($response): bool
    {
        return $response instanceof \Illuminate\Http\Response 
            && str_contains($response->headers->get('Content-Type', ''), 'text/html');
    }
    
    /**
     * Inject auto SEO based on route
     */
    protected function injectAutoSeo(?string $routeName, Request $request): void
    {
        if (!$routeName) {
            return;
        }
        
        // Auto SEO for different route patterns
        $autoSeo = $this->getAutoSeoForRoute($routeName, $request);
        
        if ($autoSeo) {
            View::share('autoSeo', $autoSeo);
        }
    }
    
    /**
     * Get auto SEO data for specific routes
     */
    protected function getAutoSeoForRoute(string $routeName, Request $request): ?array
    {
        switch ($routeName) {
            case 'home':
                return [
                    'title' => settings('site_name'),
                    'description' => settings('site_description'),
                    'type' => 'website'
                ];
                
            case 'about':
                return [
                    'title' => 'About Us',
                    'description' => 'Learn more about ' . settings('site_name') . ' - Professional Construction & General Supplier',
                    'type' => 'website'
                ];
                
            case 'services.index':
                return [
                    'title' => 'Our Services',
                    'description' => 'Explore our comprehensive range of construction and supply services',
                    'type' => 'website'
                ];
                
            case 'projects.index':
                return [
                    'title' => 'Our Projects',
                    'description' => 'View our portfolio of completed construction projects and success stories',
                    'type' => 'website'
                ];
                
            case 'contact':
                return [
                    'title' => 'Contact Us',
                    'description' => 'Get in touch with ' . settings('site_name') . ' for your construction and supply needs',
                    'type' => 'website'
                ];
                
            case 'blog.index':
                return [
                    'title' => 'Blog & News',
                    'description' => 'Latest news, insights and updates from ' . settings('site_name'),
                    'type' => 'website'
                ];
                
            default:
                return null;
        }
    }
}