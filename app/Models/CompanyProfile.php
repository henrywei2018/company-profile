<?php
// File: app/Models/CompanyProfile.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    use HasFactory;

    protected $table = 'company_profile';

    protected $fillable = [
        'about',
        'vision',
        'mission',
        'history',
        'values',
        'company_name',
        'tagline',
        'logo',
        'phone',
        'email',
        'address',
        'city',
        'postal_code',
        'country',
        'facebook',
        'twitter',
        'instagram',
        'linkedin',
        'youtube',
        'whatsapp',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'values' => 'array',
    ];

    public static function getInstance()
    {
        return self::firstOrCreate([]);
    }
}