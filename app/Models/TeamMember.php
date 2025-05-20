<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterableTrait;
use App\Traits\HasActiveTrait;
use App\Traits\HasSlugTrait;
use App\Traits\HasSortOrderTrait;
use App\Traits\SeoableTrait;
use App\Traits\ImageableTrait;

class TeamMember extends Model
{
    use HasFactory, FilterableTrait, HasActiveTrait, HasSlugTrait, HasSortOrderTrait, SeoableTrait, ImageableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'position',
        'department',
        'bio',
        'email',
        'phone',
        'photo',
        'facebook',
        'twitter',
        'linkedin',
        'instagram',
        'featured',
        'is_active',
        'sort_order',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'featured' => 'boolean',
        'is_active' => 'boolean',
    ];
    
    /**
     * The filterable attributes for the model.
     *
     * @var array
     */
    protected $filterable = [
        'department',
        'featured',
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
        'position',
        'department',
        'bio',
    ];
    
    /**
     * Scope a query to only include featured team members.
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }
    public function department()
    {
        return $this->belongsTo(TeamMemberDepartment::class, 'department_id');
    }
    
    /**
     * Get photo URL.
     */
    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        
        return asset('images/default-profile.png');
    }

    /**
     * Check if team member has social links.
     */
    public function hasSocialLinks()
    {
        return $this->facebook || $this->twitter || $this->linkedin || $this->instagram;
    }
    
    /**
     * Get active social links.
     */
    public function getSocialLinksAttribute()
    {
        $links = [];
        
        if ($this->facebook) {
            $links['facebook'] = $this->facebook;
        }
        
        if ($this->twitter) {
            $links['twitter'] = $this->twitter;
        }
        
        if ($this->linkedin) {
            $links['linkedin'] = $this->linkedin;
        }
        
        if ($this->instagram) {
            $links['instagram'] = $this->instagram;
        }
        
        return $links;
    }
}