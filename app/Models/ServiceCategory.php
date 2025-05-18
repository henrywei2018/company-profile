<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasActiveTrait;
use App\Traits\HasSlugTrait;
use App\Traits\HasSortOrderTrait;

class ServiceCategory extends Model
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
        'icon',
        'description',
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
     * Get the services for the category.
     */
    public function services()
    {
        return $this->hasMany(Service::class, 'category_id');
    }
    
    /**
     * Get active services for the category.
     */
    public function activeServices()
    {
        return $this->hasMany(Service::class, 'category_id')->active();
    }
}