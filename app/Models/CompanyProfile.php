<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ImageableTrait;

class CompanyProfile extends Model
{
    use HasFactory, ImageableTrait;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'company_profile';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
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
        'established_year',
        'employees_count',
        'projects_completed',
        'clients_count',
        'website',
        'map_coordinates',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'values' => 'array',
        'established_year' => 'integer',
        'employees_count' => 'integer',
        'projects_completed' => 'integer',
        'clients_count' => 'integer',
    ];
    
    /**
     * Get the company profile instance.
     */
    public static function getInstance()
    {
        $profile = self::first();
        
        if (!$profile) {
            $profile = self::create([
                'company_name' => config('app.name', 'CV Usaha Prima Lestari'),
            ]);
        }
        
        return $profile;
    }
    
    /**
     * Update company profile.
     */
    public static function updateProfile(array $data)
    {
        $profile = self::getInstance();
        
        return $profile->update($data);
    }
    
    /**
     * Get the logo URL.
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        
        return asset('images/default-logo.png');
    }
    
    /**
     * Get complete address.
     */
    public function getCompleteAddressAttribute()
    {
        $address = $this->address;
        
        if ($this->city) {
            $address .= ', ' . $this->city;
        }
        
        if ($this->postal_code) {
            $address .= ' ' . $this->postal_code;
        }
        
        if ($this->country) {
            $address .= ', ' . $this->country;
        }
        
        return $address;
    }
}