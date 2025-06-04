<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CompanyProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'tagline',
        'about',
        'vision',
        'mission',
        'history',
        'values',
        'logo',
        'email',
        'phone',
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

    protected $appends = [
        'logo_url',
        'full_address',
        'certificates_count' // Add this to appends
    ];

    /**
     * REMOVED: certificates() relationship method
     * Since your certifications table doesn't have company_profile_id,
     * we'll use an accessor instead of a relationship
     */

    /**
     * Get certificates count as an accessor (not a relationship)
     */
    public function getCertificatesCountAttribute(): int
    {
        try {
            // Count all certificates since they don't belong to specific company
            return \App\Models\Certification::count();
            
            // Alternative: If you have specific criteria
            // return \App\Models\Certification::where('status', 'active')->count();
            // return \App\Models\Certification::whereNotNull('certificate_file')->count();
        } catch (\Exception $e) {
            \Log::warning('Failed to count certificates: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get all certificates (as a collection, not relationship)
     */
    public function getAllCertificates()
    {
        try {
            return \App\Models\Certification::orderBy('created_at', 'desc')->get();
        } catch (\Exception $e) {
            \Log::warning('Failed to get certificates: ' . $e->getMessage());
            return collect(); // Return empty collection
        }
    }

    /**
     * Get the company SEO data.
     */
    public function seo()
    {
        return $this->morphOne(Seo::class, 'seoable');
    }

    /**
     * Get company instance (always ID 1)
     */
    public static function getInstance()
    {
        $instance = self::first();

        if (!$instance) {
            $instance = self::create([
                'company_name' => config('app.name'),
                'email' => config('mail.from.address', 'info@example.com'),
                'phone' => '+62 123 456 7890',
                'address' => 'Jakarta, Indonesia',
                'country' => 'Indonesia',
                'about' => 'We are a professional construction and supply company.',
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
                'title' => $this->company_name,
                'description' => $this->about,
                'keywords' => $this->company_name . ', construction, supply',
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
     * Get completion percentage - FIXED for type safety
     */
    public function getCompletionPercentage(): int
    {
        $allFields = [
            'company_name', 'tagline', 'about', 'vision', 'mission',
            'email', 'phone', 'address', 'city', 'country',
            'facebook', 'twitter', 'instagram', 'linkedin', 'youtube',
            'logo'
        ];
        
        $filledFields = 0;
        $totalFields = count($allFields);
        
        foreach ($allFields as $field) {
            $value = $this->getAttribute($field);
            
            if (is_string($value)) {
                if (!empty(trim($value))) {
                    $filledFields++;
                }
            } elseif (is_array($value)) {
                if (!empty($value)) {
                    $filledFields++;
                }
            } elseif (!is_null($value)) {
                $filledFields++;
            }
        }
        
        return $totalFields > 0 ? (int) round(($filledFields / $totalFields) * 100) : 0;
    }

    /**
     * Get social media links as an array.
     */
    public function getSocialLinksAttribute(): array
    {
        $socialFields = ['facebook', 'twitter', 'instagram', 'linkedin', 'youtube'];
        $links = [];

        foreach ($socialFields as $platform) {
            $value = $this->getAttribute($platform);
            if (is_string($value) && !empty(trim($value))) {
                $links[$platform] = $value;
            }
        }

        return $links;
    }

    /**
     * Get values array - FIXED for type safety
     */
    public function getValuesAttribute($value): array
    {
        if (is_null($value)) {
            return [];
        }
        
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? array_filter($decoded) : [];
        }
        
        if (is_array($value)) {
            return array_filter($value);
        }
        
        return [];
    }

    /**
     * Set values attribute - FIXED for type safety
     */
    public function setValuesAttribute($value): void
    {
        if (is_null($value)) {
            $this->attributes['values'] = null;
            return;
        }
        
        if (is_string($value)) {
            $this->attributes['values'] = $value;
            return;
        }
        
        if (is_array($value)) {
            // Filter out empty values and re-index
            $filtered = array_values(array_filter($value, function($item) {
                return is_string($item) && !empty(trim($item));
            }));
            $this->attributes['values'] = json_encode($filtered);
            return;
        }
        
        $this->attributes['values'] = json_encode([]);
    }

    /**
     * Get the full formatted address - FIXED for string concatenation
     */
    public function getFullAddressAttribute(): string
    {
        $addressParts = [];
        
        if (!empty($this->address)) {
            $addressParts[] = trim($this->address);
        }
        
        if (!empty($this->city)) {
            $addressParts[] = trim($this->city);
        }
        
        if (!empty($this->postal_code)) {
            $addressParts[] = trim($this->postal_code);
        }
        
        if (!empty($this->country)) {
            $addressParts[] = trim($this->country);
        }

        return implode(', ', $addressParts);
    }

    /**
     * Get the URL for the company logo - FIXED for null safety
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (empty($this->logo)) {
            return null;
        }
        
        if (Storage::disk('public')->exists($this->logo)) {
            return asset('storage/' . $this->logo);
        }

        return null;
    }

    /**
     * Check if the profile is complete.
     */
    public function isComplete(): bool
    {
        $requiredFields = ['company_name', 'email', 'phone', 'address'];
        
        foreach ($requiredFields as $field) {
            $value = $this->getAttribute($field);
            if (empty($value) || (is_string($value) && empty(trim($value)))) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get contact information array.
     */
    public function getContactInfoAttribute(): array
    {
        return [
            'email' => $this->email ?? '',
            'phone' => $this->phone ?? '',
            'address' => $this->full_address,
            'whatsapp' => $this->whatsapp ?? '',
        ];
    }

    /**
     * Scope for searching company profiles.
     */
    public function scopeSearch($query, $search)
    {
        if (!empty($search)) {
            $searchTerm = trim($search);
            $query->where(function ($q) use ($searchTerm) {
                $q->where('company_name', 'like', "%{$searchTerm}%")
                  ->orWhere('about', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('phone', 'like', "%{$searchTerm}%");
            });
        }
        
        return $query;
    }
}