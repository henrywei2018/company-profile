<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatOperator extends Model
{
    protected $fillable = [
        'user_id',
        'is_online',
        'is_available',
        'max_concurrent_chats',
        'current_chats_count',
        'last_seen_at',
        'settings',
    ];

    protected $casts = [
        'is_online' => 'boolean',
        'is_available' => 'boolean',
        'last_seen_at' => 'datetime',
        'settings' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedChats(): HasMany
    {
        return $this->hasMany(ChatSession::class, 'assigned_operator_id', 'user_id');
    }

    public function activeChats(): HasMany
    {
        return $this->assignedChats()->where('status', 'active');
    }

    // Scopes
    public function scopeOnline($query)
    {
        return $query->where('is_online', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    // Helper methods
    public function canTakeNewChat(): bool
    {
        return $this->is_online && 
               $this->is_available && 
               $this->current_chats_count < $this->max_concurrent_chats;
    }

    public function goOnline(): void
    {
        $this->update([
            'is_online' => true,
            'last_seen_at' => now(),
        ]);
    }

    public function goOffline(): void
    {
        $this->update([
            'is_online' => false,
            'last_seen_at' => now(),
        ]);
    }

    public function updateLastSeen(): void
    {
        $this->update(['last_seen_at' => now()]);
    }

    public function incrementChatCount(): void
    {
        $this->increment('current_chats_count');
    }

    public function decrementChatCount(): void
    {
        if ($this->current_chats_count > 0) {
            $this->decrement('current_chats_count');
        }
    }
}