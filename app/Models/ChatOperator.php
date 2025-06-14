<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatOperator extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'is_online',
        'is_available',
        'max_concurrent_chats',
        'current_chats_count',
        'last_seen_at',
        'settings' // JSON field
    ];

    protected $casts = [
        'is_online' => 'boolean',
        'is_available' => 'boolean',
        'last_seen_at' => 'datetime',
        'settings' => 'array'
    ];

    // =======================
    // RELATIONSHIPS
    // =======================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chatSessions(): HasMany
    {
        return $this->hasMany(ChatSession::class, 'assigned_operator_id', 'user_id');
    }

    public function activeSessions(): HasMany
    {
        return $this->chatSessions()->where('status', 'active');
    }

    // =======================
    // SCOPES - UNTUK ADMIN DASHBOARD
    // =======================

    public function scopeOnline($query)
    {
        return $query->where('is_online', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_online', true)->where('is_available', true);
    }

    public function scopeWithCapacity($query)
    {
        return $query->whereColumn('current_chats_count', '<', 'max_concurrent_chats');
    }

    // =======================
    // ESSENTIAL METHODS
    // =======================

    public function getStatusText(): string
    {
        if (!$this->is_online) {
            return 'Offline';
        }
        return $this->is_available ? 'Online' : 'Away';
    }

    public function getStatusClass(): string
    {
        if (!$this->is_online) {
            return 'text-gray-500';
        }
        return $this->is_available ? 'text-green-500' : 'text-yellow-500';
    }

    public function getCurrentLoad(): int
    {
        return $this->current_chats_count;
    }

    public function getLoadPercentage(): float
    {
        if ($this->max_concurrent_chats <= 0) {
            return 0;
        }
        return round(($this->current_chats_count / $this->max_concurrent_chats) * 100, 1);
    }

    public function canAcceptNewChat(): bool
    {
        return $this->is_online 
            && $this->is_available 
            && $this->current_chats_count < $this->max_concurrent_chats;
    }

    public function updateChatCount(): void
    {
        $activeCount = $this->activeSessions()->count();
        $this->update(['current_chats_count' => $activeCount]);
    }

    public function setOnline(bool $online = true): void
    {
        $this->update([
            'is_online' => $online,
            'last_seen_at' => now()
        ]);
    }

    public function setAvailable(bool $available = true): void
    {
        $this->update([
            'is_available' => $available,
            'last_seen_at' => now()
        ]);
    }

    // =======================
    // UNTUK API RESPONSE
    // =======================

    public function toApiArray(): array
    {
        return [
            'id' => $this->user_id,
            'name' => $this->user->name,
            'avatar' => $this->user->avatar_url,
            'status' => $this->getStatusText(),
            'status_class' => $this->getStatusClass(),
            'is_online' => $this->is_online,
            'is_available' => $this->is_available,
            'current_load' => $this->getCurrentLoad(),
            'max_chats' => $this->max_concurrent_chats,
            'load_percentage' => $this->getLoadPercentage(),
            'last_seen' => $this->last_seen_at?->diffForHumans()
        ];
    }
}