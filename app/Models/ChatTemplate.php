<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'trigger',
        'message',
        'type',
        'conditions',
        'is_active',
        'usage_count'
    ];

    protected $casts = [
        'conditions' => 'array',
        'is_active' => 'boolean',
        'usage_count' => 'integer'
    ];

    // =======================
    // SCOPES
    // =======================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // =======================
    // METHODS
    // =======================

    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    public function toApiArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'trigger' => $this->trigger,
            'message' => $this->message,
            'type' => $this->type
        ];
    }
}