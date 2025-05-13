<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasActiveTrait;
use App\Traits\HasSortOrderTrait;
use App\Traits\ImageableTrait;

class TeamMember extends Model
{
    use HasFactory, HasActiveTrait, HasSortOrderTrait, ImageableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'position',
        'bio',
        'email',
        'phone',
        'linkedin',
        'image',
        'is_active',
        'sort_order',
        'department',
        'social_linkedin',
        'social_twitter',
        'social_facebook',
        'social_instagram',
        'is_featured',
        'photo',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];
    
    /**
     * Scope a query to only include featured team members.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
    
    /**
     * Scope a query to only exclude featured team members.
     */
    public function scopeNotFeatured($query)
    {
        return $query->where('is_featured', false);
    }
    
    /**
     * Get photo URL.
     */
    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        
        return asset('images/default-team-member.jpg');
    }
}