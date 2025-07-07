<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display the products index page with filtering
     */
    public function index(Request $request)
{
    // Set page meta for SEO
    $this->setPageMeta(
        'Our Products - ' . $this->siteConfig['site_title'],
        'Explore our complete range of construction and engineering products. Quality materials and solutions for your projects.',
        'construction products, building materials, engineering products, construction supplies',
        asset($this->siteConfig['site_logo'])
    );

    // Get filter parameters
    $category = $request->get('category');
    $service = $request->get('service');
    $brand = $request->get('brand');
    $priceRange = $request->get('price_range');
    $search = $request->get('search');
    $sortBy = $request->get('sort', 'latest');
    $perPage = $request->get('per_page', 12);

    // Build products query with proper image relationships
    $productsQuery = Product::where('status', 'published')
        ->where('is_active', true)
        ->with([
            'category', 
            'service', 
            'images' => function($query) {
                $query->orderBy('is_featured', 'desc')
                      ->orderBy('sort_order', 'asc');
            }
        ]);

    // Apply search filter
    if ($search) {
        $productsQuery->where(function($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
        });
    }

    // Apply category filter
    if ($category && $category !== 'all') {
        $productsQuery->whereHas('category', function($query) use ($category) {
            $query->where('slug', $category);
        });
    }

    // Apply service filter
    if ($service && $service !== 'all') {
        $productsQuery->whereHas('service', function($query) use ($service) {
            $query->where('slug', $service);
        });
    }

    // Apply brand filter
    if ($brand && $brand !== 'all') {
        $productsQuery->where('brand', $brand);
    }

    // Apply price range filter
    if ($priceRange && $priceRange !== 'all') {
        $ranges = [
            'under-100' => [0, 100000],
            '100-500' => [100000, 500000],
            '500-1000' => [500000, 1000000],
            '1000-5000' => [1000000, 5000000],
            'over-5000' => [5000000, PHP_INT_MAX],
        ];
        
        if (isset($ranges[$priceRange])) {
            $productsQuery->whereBetween('price', $ranges[$priceRange]);
        }
    }

    // Apply sorting
    switch ($sortBy) {
        case 'name':
            $productsQuery->orderBy('name', 'asc');
            break;
        case 'price_low':
            $productsQuery->orderBy('price', 'asc');
            break;
        case 'price_high':
            $productsQuery->orderBy('price', 'desc');
            break;
        case 'featured':
            $productsQuery->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc');
            break;
        case 'latest':
        default:
            $productsQuery->orderBy('created_at', 'desc');
            break;
    }

    // Get paginated products
    $products = $productsQuery->paginate($perPage);

    // Get featured products for hero section with proper image loading
    $featuredProducts = Cache::remember('featured_products_public', 1800, function () {
        return Product::where('status', 'published')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->with([
                'category', 
                'service',
                'images' => function($query) {
                    $query->orderBy('is_featured', 'desc')
                          ->orderBy('sort_order', 'asc');
                }
            ])
            ->limit(6)
            ->get();
    });

    // Get filter options
    $categories = Cache::remember('product_categories_with_counts', 1800, function () {
        return ProductCategory::where('is_active', true)
            ->withCount(['activeProducts'])
            ->having('active_products_count', '>', 0)
            ->orderBy('sort_order')
            ->get();
    });

    $services = Cache::remember('product_services_with_counts', 1800, function () {
        return Service::where('is_active', true)
            ->withCount(['activeProducts'])
            ->having('active_products_count', '>', 0)
            ->orderBy('sort_order')
            ->get();
    });

    // Get available brands
    $brands = Cache::remember('product_brands', 1800, function () {
        return Product::where('status', 'published')
            ->where('is_active', true)
            ->whereNotNull('brand')
            ->distinct()
            ->orderBy('brand')
            ->pluck('brand');
    });

    // Get product statistics
    $stats = [
        'total_products' => Product::where('status', 'published')->count(),
        'active_categories' => ProductCategory::where('is_active', true)->count(),
        'featured_products' => Product::where('is_featured', true)->where('status', 'published')->count(),
        'in_stock_products' => Product::where('stock_status', 'in_stock')->where('status', 'published')->count(),
    ];

    return view('pages.products.index', compact(
        'products',
        'featuredProducts',
        'categories',
        'services',
        'brands',
        'stats',
        'search',
        'category',
        'service',
        'brand',
        'priceRange',
        'sortBy'
    ));
}

    /**
     * Display a specific product
     */
    public function show($slug)
    {
        // Find product by slug with all necessary relationships
        $product = Product::where('slug', $slug)
            ->where('status', 'published')
            ->where('is_active', true)
            ->with([
                'category', 
                'service', 
                'images' => function($query) {
                    $query->orderBy('is_featured', 'desc')
                        ->orderBy('sort_order', 'asc');
                },
                'relatedServices'
            ])
            ->firstOrFail();

        // Set page meta for SEO
        $this->setPageMeta(
            $product->name . ' - Products - ' . $this->siteConfig['site_title'],
            $product->short_description ?: 'Learn more about our ' . $product->name . ' product.',
            $product->name . ', ' . ($product->brand ?: '') . ', construction product, building material',
            $product->featured_image_url ?: asset($this->siteConfig['site_logo'])
        );

        // Get related products
        $relatedProducts = Product::where('status', 'published')
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->where(function($query) use ($product) {
                if ($product->category_id) {
                    $query->where('product_category_id', $product->category_id);
                } elseif ($product->service_id) {
                    $query->where('service_id', $product->service_id);
                } elseif ($product->brand) {
                    $query->where('brand', $product->brand);
                } else {
                    $query->where('is_featured', true);
                }
            })
            ->with(['category', 'service'])
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        return view('pages.products.show', compact(
            'product',
            'relatedProducts'
        ));
    }

    /**
     * Get products by category (AJAX endpoint)
     */
    public function getByCategory($categorySlug, Request $request)
    {
        $category = ProductCategory::where('slug', $categorySlug)
            ->where('is_active', true)
            ->firstOrFail();

        $products = Product::where('status', 'published')
            ->where('is_active', true)
            ->where('category_id', $category->id)
            ->with(['category', 'service'])
            ->paginate($request->get('per_page', 12));

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'category' => $category,
                'products' => $products
            ]);
        }

        return view('pages.products.category', compact('category', 'products'));
    }
}