<?php
// File: app/Models/TeamMember.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ImageableTrait;

class TeamMember extends Model
{
    use HasFactory, ImageableTrait;

    protected $fillable = [
        'name',
        'position',
        'bio',
        'email',
        'phone',
        'linkedin',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order');
    }
}