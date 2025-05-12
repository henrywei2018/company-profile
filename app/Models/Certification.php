<?php
// File: app/Models/Certification.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ImageableTrait;

class Certification extends Model
{
    use HasFactory, ImageableTrait;

    protected $fillable = [
        'name',
        'issuer',
        'description',
        'issue_date',
        'expiry_date',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expiry_date')
              ->orWhere('expiry_date', '>=', now());
        });
    }

    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order');
    }
}