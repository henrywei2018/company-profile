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
        'last_activity_at',
        'auto_assignment',
        'notification_preferences',
        'signature',
        'department'
    ];

    protected $casts = [
        'is_online' => 'boolean',
        'is_available' => 'boolean',
        'auto_assignment' => 'boolean',
        'last_activity_at' => 'datetime',
        'notification_preferences' => 'json'
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedSessions(): HasMany
    {
        return $this->hasMany(ChatSession::class, 'assigned_operator_id', 'user_id');
    }

    public function activeSessions(): HasMany
    {
        return $this->assignedSessions()->where('status', 'active');
    }

    // Scopes
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
        return $query->whereHas('activeSessions', function($q) {
            $q->havingRaw('COUNT(*) < max_concurrent_chats');
        });
    }

    // Accessors
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
            return 'status-offline';
        }

        return $this->is_available ? 'status-online' : 'status-away';
    }

    public function getCurrentLoad(): int
    {
        return $this->activeSessions()->count();
    }

    public function getLoadPercentage(): float
    {
        $current = $this->getCurrentLoad();
        $max = $this->max_concurrent_chats ?: 1;
        
        return round(($current / $max) * 100, 1);
    }

    // Helper methods
    public function hasCapacity(): bool
    {
        return $this->getCurrentLoad() < $this->max_concurrent_chats;
    }

    public function canTakeNewChat(): bool
    {
        return $this->is_online && $this->is_available && $this->hasCapacity();
    }

    public function updateActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }

    public function goOnline(): void
    {
        $this->update([
            'is_online' => true,
            'is_available' => true,
            'last_activity_at' => now()
        ]);
    }

    public function goOffline(): void
    {
        $this->update([
            'is_online' => false,
            'is_available' => false
        ]);
    }

    public function setAway(): void
    {
        $this->update([
            'is_online' => true,
            'is_available' => false
        ]);
    }
}