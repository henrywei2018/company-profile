<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasActiveTrait;
use App\Traits\HasSlugTrait;
use App\Traits\HasSortOrderTrait;
use App\Traits\SeoableTrait;
use App\Traits\FilterableTrait;

class Product extends Model
{
    use HasFactory, HasActiveTrait, HasSlugTrait, HasSortOrderTrait, SeoableTrait, FilterableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'sku',
        'short_description',
        'description',
        'product_category_id',
        'service_id',
        'brand',
        'model',
        'price',
        'sale_price',
        'currency',
        'price_type',
        'stock_quantity',
        'manage_stock',
        'stock_status',
        'featured_image',
        'gallery',
        'specifications',
        'technical_specs',
        'dimensions',
        'weight',
        'status',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'gallery' => 'array',
        'specifications' => 'array',
        'technical_specs' => 'array',
        'dimensions' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'manage_stock' => 'boolean',
    ];

    /**
     * The filterable attributes for the model.
     *
     * @var array
     */
    protected $filterable = [
        'product_category_id',
        'service_id',
        'brand',
        'status',
        'stock_status',
        'is_featured',
        'is_active',
        'search',
    ];

    /**
     * The searchable attributes for the model.
     *
     * @var array
     */
    protected $searchable = [
        'name',
        'sku',
        'short_description',
        'description',
        'brand',
        'model',
    ];

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    /**
     * Get the primary service for this product.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the product images.
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class)->ordered();
    }

    /**
     * Get the featured image.
     */
    public function featuredImageModel()
    {
        return $this->hasOne(ProductImage::class)->where('is_featured', true);
    }

    /**
     * Get related services through pivot table.
     */
    public function relatedServices()
    {
        return $this->belongsToMany(Service::class, 'product_service_relations')
                    ->withPivot('relation_type')
                    ->withTimestamps();
    }

    /**
     * Get compatible services.
     */
    public function compatibleServices()
    {
        return $this->relatedServices()->wherePivot('relation_type', 'compatible');
    }

    /**
     * Get recommended services.
     */
    public function recommendedServices()
    {
        return $this->relatedServices()->wherePivot('relation_type', 'recommended');
    }

    /**
     * Get required services.
     */
    public function requiredServices()
    {
        return $this->relatedServices()->wherePivot('relation_type', 'required');
    }

    /**
     * Get quotations that include this product.
     */
    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    /**
     * Scope a query to only include published products.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope a query to only include featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include products in stock.
     */
    public function scopeInStock($query)
    {
        return $query->where('stock_status', 'in_stock');
    }

    /**
     * Scope a query to products by category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('product_category_id', $categoryId);
    }

    /**
     * Scope a query to products by service.
     */
    public function scopeByService($query, $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    /**
     * Scope a query to products by brand.
     */
    public function scopeByBrand($query, $brand)
    {
        return $query->where('brand', $brand);
    }

    /**
     * Scope a query to products within price range.
     */
    public function scopePriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    /**
     * Get the featured image URL.
     */
    public function getFeaturedImageUrlAttribute()
    {
        if ($this->featured_image) {
            return asset('storage/' . $this->featured_image);
        }
        
        // Try to get from images relationship
        $featuredImage = $this->images()->where('is_featured', true)->first();
        if ($featuredImage) {
            return asset('storage/' . $featuredImage->image_path);
        }
        
        // Get first image if no featured image
        $firstImage = $this->images()->first();
        if ($firstImage) {
            return asset('storage/' . $firstImage->image_path);
        }
        
        return asset('images/default-product.jpg');
    }

    /**
     * Get the formatted price.
     */
    public function getFormattedPriceAttribute()
    {
        if ($this->price_type === 'quote') {
            return 'Request Quote';
        }
        
        if ($this->price_type === 'contact') {
            return 'Contact for Price';
        }
        
        if ($this->sale_price && (float)$this->sale_price < (float)$this->price) {
            return 'Rp ' . number_format((float)$this->sale_price, 0, ',', '.') . 
                   ' <del class="text-gray-500">Rp ' . number_format((float)$this->price, 0, ',', '.') . '</del>';
        }
        
        if ($this->price) {
            return 'Rp ' . number_format((float)$this->price, 0, ',', '.');
        }
        
        return 'Price not set';
    }

    /**
     * Get the current selling price.
     */
    public function getCurrentPriceAttribute()
    {
        if ($this->sale_price && (float)$this->sale_price < (float)$this->price) {
            return (float)$this->sale_price;
        }
        
        return (float)$this->price;
    }

    /**
     * Get the stock status label.
     */
    public function getStockStatusLabelAttribute()
    {
        return match($this->stock_status) {
            'in_stock' => 'In Stock',
            'out_of_stock' => 'Out of Stock',
            'on_backorder' => 'On Backorder',
            default => 'Unknown'
        };
    }

    /**
     * Get the stock status color class.
     */
    public function getStockStatusColorAttribute()
    {
        return match($this->stock_status) {
            'in_stock' => 'text-green-600',
            'out_of_stock' => 'text-red-600',
            'on_backorder' => 'text-yellow-600',
            default => 'text-gray-600'
        };
    }

    /**
     * Check if product is in stock.
     */
    public function isInStock()
    {
        if (!$this->manage_stock) {
            return $this->stock_status === 'in_stock';
        }
        
        return $this->stock_quantity > 0 && $this->stock_status === 'in_stock';
    }

    /**
     * Check if product is on sale.
     */
    public function isOnSale()
    {
        return $this->sale_price && (float)$this->sale_price < (float)$this->price;
    }

    /**
     * Get the discount percentage.
     */
    public function getDiscountPercentageAttribute()
    {
        if (!$this->isOnSale()) {
            return 0;
        }
        
        $price = (float)$this->price;
        $salePrice = (float)$this->sale_price;
        
        return round((($price - $salePrice) / $price) * 100);
    }

    /**
     * Get all available brands.
     */
    public static function getAvailableBrands()
    {
        return static::whereNotNull('brand')
                    ->distinct()
                    ->orderBy('brand')
                    ->pluck('brand');
    }

    /**
     * Get products formatted for select dropdown.
     */
    public static function getSelectOptions($includeEmpty = true)
    {
        $options = [];
        
        if ($includeEmpty) {
            $options[''] = 'Select a product';
        }
        
        $products = static::active()
                         ->published()
                         ->orderBy('name')
                         ->get();
        
        foreach ($products as $product) {
            $label = $product->name;
            if ($product->sku) {
                $label .= " ({$product->sku})";
            }
            $options[$product->id] = $label;
        }
        
        return $options;
    }

    /**
     * Update stock quantity.
     */
    public function updateStock($quantity, $operation = 'set')
    {
        if (!$this->manage_stock) {
            return false;
        }
        
        switch ($operation) {
            case 'add':
                $this->stock_quantity += $quantity;
                break;
            case 'subtract':
                $this->stock_quantity -= $quantity;
                break;
            case 'set':
            default:
                $this->stock_quantity = $quantity;
                break;
        }
        
        // Update stock status based on quantity
        if ($this->stock_quantity <= 0) {
            $this->stock_status = 'out_of_stock';
        } elseif ($this->stock_status === 'out_of_stock') {
            $this->stock_status = 'in_stock';
        }
        
        return $this->save();
    }
}