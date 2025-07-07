<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasActiveTrait;
use App\Traits\HasSlugTrait;
use App\Traits\HasSortOrderTrait;

class ProductCategory extends Model
{
    use HasFactory, HasActiveTrait, HasSlugTrait, HasSortOrderTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'parent_id',
        'service_category_id',
        'is_active',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the parent category.
     */
    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    /**
     * Get the child categories.
     */
    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id')->ordered();
    }

    /**
     * Get all descendants (children, grandchildren, etc.)
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Get the service category this product category belongs to.
     */
    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    /**
     * Get the products in this category.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get active products in this category.
     */
    public function activeProducts()
    {
        return $this->hasMany(Product::class)->active()->published();
    }

    /**
     * Get all products including from child categories.
     */
    public function allProducts()
    {
        $productIds = collect([$this->id]);
        
        // Get all descendant category IDs
        $this->collectDescendantIds($productIds);
        
        return Product::whereIn('product_category_id', $productIds);
    }

    /**
     * Recursively collect descendant category IDs.
     */
    private function collectDescendantIds($collection)
    {
        $children = $this->children()->pluck('id');
        
        foreach ($children as $childId) {
            if (!$collection->contains($childId)) {
                $collection->push($childId);
                $child = static::find($childId);
                if ($child) {
                    $child->collectDescendantIds($collection);
                }
            }
        }
    }

    /**
     * Scope a query to only include root categories.
     */
    public function scopeRootCategories($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope a query to categories belonging to a specific service category.
     */
    public function scopeWithServiceCategory($query, $serviceCategoryId)
    {
        return $query->where('service_category_id', $serviceCategoryId);
    }

    /**
     * Scope a query to categories with active products.
     */
    public function scopeWithActiveProducts($query)
    {
        return $query->whereHas('products', function ($q) {
            $q->active()->published();
        });
    }

    /**
     * Get the icon URL.
     */
    public function getIconUrlAttribute()
    {
        if ($this->icon) {
            return asset('storage/' . $this->icon);
        }
        
        return asset('images/default-category-icon.png');
    }

    /**
     * Get the full category path (breadcrumb).
     */
    public function getFullPathAttribute()
    {
        $path = collect([$this->name]);
        $parent = $this->parent;
        
        while ($parent) {
            $path->prepend($parent->name);
            $parent = $parent->parent;
        }
        
        return $path->implode(' > ');
    }

    /**
     * Get the depth level of this category.
     */
    public function getDepthAttribute()
    {
        $depth = 0;
        $parent = $this->parent;
        
        while ($parent) {
            $depth++;
            $parent = $parent->parent;
        }
        
        return $depth;
    }

    /**
     * Check if this category has children.
     */
    public function hasChildren()
    {
        return $this->children()->count() > 0;
    }

    /**
     * Check if this category is a descendant of another category.
     */
    public function isDescendantOf(ProductCategory $category)
    {
        $parent = $this->parent;
        
        while ($parent) {
            if ($parent->id === $category->id) {
                return true;
            }
            $parent = $parent->parent;
        }
        
        return false;
    }

    /**
     * Get categories formatted for select dropdown.
     */
    public static function getSelectOptions($includeEmpty = true)
    {
        $options = [];
        
        if ($includeEmpty) {
            $options[''] = 'Select a category';
        }
        
        $categories = static::with('parent')
            ->active()
            ->ordered()
            ->get()
            ->sortBy('full_path');
        
        foreach ($categories as $category) {
            $prefix = str_repeat('— ', $category->depth);
            $options[$category->id] = $prefix . $category->name;
        }
        
        return $options;
    }
}