<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'satisfaction_rating',
        'ease_of_use',
        'comments',
        'page_url',
        'user_agent',
        'ip_address',
        'session_id',
        'user_id',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'satisfaction_rating' => 'integer',
    ];

    /**
     * Relationship with User model
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for filtering by satisfaction rating
     */
    public function scopeBySatisfactionRating($query, int $rating)
    {
        return $query->where('satisfaction_rating', $rating);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('submitted_at', [$startDate, $endDate]);
    }

    /**
     * Get satisfaction rating as text
     */
    public function getSatisfactionTextAttribute(): string
    {
        return match($this->satisfaction_rating) {
            1 => 'Buruk',
            2 => 'Kurang',
            3 => 'Biasa',
            4 => 'Baik',
            5 => 'Luar Biasa',
            default => 'Tidak diketahui'
        };
    }

    /**
     * Get ease of use as text
     */
    public function getEaseOfUseTextAttribute(): string
    {
        return match($this->ease_of_use) {
            'very_easy' => 'Sangat mudah',
            'easy' => 'Mudah',
            'neutral' => 'Biasa saja',
            'difficult' => 'Sulit',
            'very_difficult' => 'Sangat sulit',
            default => 'Tidak diisi'
        };
    }
}
