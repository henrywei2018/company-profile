<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Post;
use App\Models\Project;

class SitemapController extends Controller
{
    /**
     * Generate dynamic sitemap for the entire application
     */
    public function index()
    {
        $urls = collect();
        
        // Add static pages
        $staticUrls = $this->getStaticUrls();
        $urls = $urls->concat($staticUrls);
        
        // Add dynamic content
        $urls = $urls->concat($this->getProductUrls());
        $urls = $urls->concat($this->getBlogUrls());
        $urls = $urls->concat($this->getPublicProjectUrls());
        
        return response()->view('sitemap.index', compact('urls'))
            ->header('Content-Type', 'application/xml');
    }
    
    /**
     * Generate sitemap specifically for client area (authenticated)
     */
    public function clientSitemap()
    {
        $urls = collect();
        
        // Add client static pages
        $clientUrls = $this->getClientUrls();
        $urls = $urls->concat($clientUrls);
        
        return response()->view('sitemap.client', compact('urls'))
            ->header('Content-Type', 'application/xml');
    }
    
    /**
     * Get static URLs for main site
     */
    private function getStaticUrls()
    {
        return collect([
            [
                'url' => url('/'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '1.0'
            ],
            [
                'url' => url('/about'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.8'
            ],
            [
                'url' => url('/services'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.9'
            ],
            [
                'url' => url('/products'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '0.9'
            ],
            [
                'url' => url('/projects'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.8'
            ],
            [
                'url' => url('/blog'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '0.8'
            ],
            [
                'url' => url('/contact'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.7'
            ],
        ]);
    }
    
    /**
     * Get product URLs
     */
    private function getProductUrls()
    {
        $urls = collect();
        
        // Product categories
        if (class_exists(ProductCategory::class)) {
            $categories = ProductCategory::where('is_active', true)->get();
            foreach ($categories as $category) {
                $urls->push([
                    'url' => url("/products/category/{$category->slug}"),
                    'lastmod' => $category->updated_at->toDateString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.7'
                ]);
            }
        }
        
        // Individual products
        if (class_exists(Product::class)) {
            $products = Product::where('status', 'active')->get();
            foreach ($products as $product) {
                $urls->push([
                    'url' => url("/products/{$product->slug}"),
                    'lastmod' => $product->updated_at->toDateString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.6'
                ]);
            }
        }
        
        return $urls;
    }
    
    /**
     * Get blog post URLs
     */
    private function getBlogUrls()
    {
        $urls = collect();
        
        if (class_exists(Post::class)) {
            $posts = Post::where('status', 'published')->get();
            foreach ($posts as $post) {
                $urls->push([
                    'url' => url("/blog/{$post->slug}"),
                    'lastmod' => $post->updated_at->toDateString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.6'
                ]);
            }
        }
        
        return $urls;
    }
    
    /**
     * Get public project URLs
     */
    private function getPublicProjectUrls()
    {
        $urls = collect();
        
        if (class_exists(Project::class)) {
            $projects = Project::where('status', 'completed')
                ->where('featured', true)
                ->get();
                
            foreach ($projects as $project) {
                $urls->push([
                    'url' => url("/projects/{$project->slug}"),
                    'lastmod' => $project->updated_at->toDateString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.7'
                ]);
            }
        }
        
        return $urls;
    }
    
    /**
     * Get client area URLs
     */
    private function getClientUrls()
    {
        return collect([
            // Dashboard
            [
                'url' => route('client.dashboard'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '1.0'
            ],
            
            // Products & E-commerce
            [
                'url' => route('client.products.index'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '0.9'
            ],
            [
                'url' => route('client.cart.index'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'hourly',
                'priority' => '0.8'
            ],
            [
                'url' => route('client.orders.index'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '0.9'
            ],
            [
                'url' => route('client.orders.checkout'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.7'
            ],
            
            // Projects & Services
            [
                'url' => route('client.projects.index'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '0.9'
            ],
            [
                'url' => route('client.quotations.index'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '0.9'
            ],
            [
                'url' => route('client.quotations.create'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.7'
            ],
            
            // Communication
            [
                'url' => route('client.messages.index'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '0.8'
            ],
            [
                'url' => route('client.messages.create'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.7'
            ],
            [
                'url' => route('client.chat.index'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '0.8'
            ],
            [
                'url' => route('client.chat.history'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '0.6'
            ],
            
            // Notifications
            [
                'url' => route('client.notifications.index'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'hourly',
                'priority' => '0.7'
            ],
            [
                'url' => route('client.notifications.preferences'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.5'
            ],
            
            // Testimonials
            [
                'url' => route('client.testimonials.index'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.6'
            ],
            [
                'url' => route('client.testimonials.create'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.5'
            ],
        ]);
    }
    
    /**
     * Generate robots.txt
     */
    public function robots()
    {
        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /client/\n";  // Protect client area from crawling
        $content .= "Disallow: /admin/\n";   // Protect admin area from crawling
        $content .= "Disallow: /api/\n";     // Protect API endpoints
        $content .= "Disallow: /storage/temp/\n"; // Protect temp files
        $content .= "\n";
        $content .= "Sitemap: " . url('/sitemap.xml') . "\n";
        $content .= "Sitemap: " . url('/sitemap-client.xml') . "\n";
        
        return response($content)->header('Content-Type', 'text/plain');
    }
}