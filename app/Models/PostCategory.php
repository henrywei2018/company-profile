<?php
// File: app/Models/PostCategory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasSlugTrait;

class PostCategory extends Model
{
    use HasFactory, HasSlugTrait;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }

    public function getPostCountAttribute()
    {
        return $this->posts()->published()->count();
    }
}
