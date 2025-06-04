<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RobotsController extends Controller
{
    /**
     * Generate robots.txt
     */
    public function robots(): Response
    {
        $robotsContent = settings('seo_robots_txt');
        
        if (!$robotsContent) {
            $robotsContent = $this->getDefaultRobotsTxt();
        }
        
        return response($robotsContent)
            ->header('Content-Type', 'text/plain');
    }
    
    /**
     * Get default robots.txt content
     */
    protected function getDefaultRobotsTxt(): string
    {
        $content = "User-agent: *\n";
        
        // Check if site should be indexed
        $allowIndexing = settings('enable_indexing', true);
        
        if (!$allowIndexing) {
            $content .= "Disallow: /\n";
        } else {
            // Disallow admin and sensitive areas
            $content .= "Disallow: /admin\n";
            $content .= "Disallow: /admin/*\n";
            $content .= "Disallow: /api\n";
            $content .= "Disallow: /api/*\n";
            $content .= "Disallow: /login\n";
            $content .= "Disallow: /register\n";
            $content .= "Disallow: /password\n";
            $content .= "Disallow: /storage\n";
            $content .= "Disallow: /*?*\n";
            $content .= "Allow: /storage/images\n";
            $content .= "Allow: /storage/uploads\n";
        }
        
        $content .= "\n";
        
        // Add sitemap reference
        $content .= "Sitemap: " . url('/sitemap.xml') . "\n";
        
        return $content;
    }
}