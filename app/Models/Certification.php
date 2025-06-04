<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasActiveTrait;
use App\Traits\HasSortOrderTrait;
use App\Traits\ImageableTrait;

class Certification extends Model
{
    use HasFactory, HasActiveTrait, HasSortOrderTrait, ImageableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'issuer',
        'description',
        'image',
        'issue_date',
        'expiry_date',
        'is_active',
        'sort_order',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'is_active' => 'boolean',
    ];
    
    /**
     * Scope a query to only include valid certifications.
     */
    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expiry_date')
                ->orWhere('expiry_date', '>=', now());
        });
    }

    public function companyProfile()
    {
        return $this->belongsTo(CompanyProfile::class);
    }
}