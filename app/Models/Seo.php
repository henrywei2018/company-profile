<?php
// File: app/Models/Seo.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seo extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'keywords',
        'og_image',
        'seoable_id',
        'seoable_type',
    ];

    public function seoable()
    {
        return $this->morphTo();
    }
}