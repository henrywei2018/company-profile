<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatTemplate extends Model
{
    protected $fillable = [
        'name',
        'trigger',
        'message',
        'type',
        'conditions',
        'is_active',
        'usage_count',
    ];

    protected $casts = [
        'conditions' => 'array',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Helper methods
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    public function matchesTrigger(string $message): bool
    {
        if (!$this->trigger) {
            return false;
        }

        return str_contains(strtolower($message), strtolower($this->trigger));
    }
}