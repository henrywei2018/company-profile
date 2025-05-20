<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasActiveTrait;
use App\Traits\HasSlugTrait;
use App\Traits\HasSortOrderTrait;

class ProjectCategory extends Model
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
     * Get the projects for the category.
     */
    public function projects()
    {
        return $this->hasMany(Project::class, 'category_id');
    }
    
    /**
     * Get active projects for the category.
     */
    public function activeProjects()
    {
        return $this->hasMany(Project::class, 'category_id')->active();
    }
}