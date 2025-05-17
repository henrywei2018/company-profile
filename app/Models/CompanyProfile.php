<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\Certification;

class CompanyProfile extends Model
{
    use HasFactory;
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'tagline',
        'about',
        'description',
        'vision',
        'mission',
        'email',
        'alternative_email',
        'phone',
        'alternative_phone',
        'address',
        'facebook',
        'twitter',
        'instagram',
        'linkedin',
        'youtube',
        'whatsapp',
        'legal_name',
        'tax_id',
        'registration_number',
        'established',
        'latitude',
        'longitude',
        'map_embed',
        'business_hours',
        'logo',
        'logo_white',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'established' => 'integer',
        'business_hours' => 'array',
    ];

    /**
     * Get the company SEO data.
     */
    public function seo()
    {
        return $this->morphOne(Seo::class, 'seoable');
    }

    /**
     * Get the company certificates.
     */
    public function certificates()
    {
        return $this->hasMany(Certification::class);
    }

    /**
     * Get company instance (always ID 1)
     */
    public static function getInstance()
    {
        $instance = self::find(1);

        if (!$instance) {
            $instance = self::create([
                'name' => config('app.name'),
                'email' => 'info@example.com',
                'phone' => '+62 123 456 7890',
                'address' => 'Jakarta, Indonesia',
            ]);
        }

        return $instance;
    }

    /**
     * Get SEO data or create a new one.
     */
    public function getSeoData()
    {
        $seo = $this->seo;

        if (!$seo) {
            $seo = $this->seo()->create([
                'title' => $this->name,
                'description' => $this->about,
            ]);
        }

        return $seo;
    }

    /**
     * Update SEO data.
     */
    public function updateSeo(array $data)
    {
        $seo = $this->getSeoData();
        $seo->update($data);

        return $seo;
    }

    /**
     * Get the URL for the company logo.
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo && Storage::disk('public')->exists($this->logo)) {
            return asset('storage/' . $this->logo);
        }

        return null;
    }

    /**
     * Get the URL for the company white logo.
     */
    public function getLogoWhiteUrlAttribute()
    {
        if ($this->logo_white && Storage::disk('public')->exists($this->logo_white)) {
            return asset('storage/' . $this->logo_white);
        }

        return null;
    }

    /**
     * Get the business hours as an array.
     */
    public function getBusinessHoursArrayAttribute()
    {
        if (is_string($this->business_hours)) {
            return json_decode($this->business_hours, true) ?? [];
        }

        return $this->business_hours ?? [];
    }
}