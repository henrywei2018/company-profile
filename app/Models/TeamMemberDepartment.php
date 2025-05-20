<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasActiveTrait;
use App\Traits\HasSlugTrait;
use App\Traits\HasSortOrderTrait;

class TeamMemberDepartment extends Model
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
     * Get the team members for the department.
     */
    public function teamMembers()
    {
        return $this->hasMany(TeamMember::class, 'department_id');
    }
    
    /**
     * Get active team members for the department.
     */
    public function activeTeamMembers()
    {
        return $this->hasMany(TeamMember::class, 'department_id')->active();
    }
}