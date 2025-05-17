<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Seo extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'keywords',
        'og_image',
        'twitter_card',
        'canonical_url',
        'robots',
    ];

    /**
     * Get the parent seoable model.
     */
    public function seoable()
    {
        return $this->morphTo();
    }

    /**
     * Get the URL for the og image.
     */
    public function getOgImageUrlAttribute()
    {
        if ($this->og_image && Storage::disk('public')->exists($this->og_image)) {
            return asset('storage/' . $this->og_image);
        }

        return null;
    }
}