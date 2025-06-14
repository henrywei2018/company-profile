<?php

// =======================
// COMPLETE CLEAN CHAT MODELS
// Berdasarkan kebutuhan chat widget + admin dashboard
// =======================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// =======================
// ChatSession Model
// =======================
class ChatSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'visitor_info', // JSON field
        'status',
        'assigned_operator_id',
        'priority',
        'source',
        'started_at',
        'last_activity_at',
        'ended_at',
        'summary',
        'metadata' // JSON field
    ];

    protected $casts = [
        'visitor_info' => 'array',
        'metadata' => 'array',
        'started_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'ended_at' => 'datetime'
    ];

    // =======================
    // RELATIONSHIPS
    // =======================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedOperator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_operator_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function latestMessage(): BelongsTo
    {
        return $this->belongsTo(ChatMessage::class, 'id', 'chat_session_id')
            ->latest();
    }

    // =======================
    // SCOPES - UNTUK ADMIN DASHBOARD
    // =======================

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeAssignedTo($query, int $operatorId)
    {
        return $query->where('assigned_operator_id', $operatorId);
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_operator_id');
    }

    public function scopeByPriority($query, string $direction = 'desc')
    {
        $order = match($direction) {
            'asc' => "CASE priority WHEN 'low' THEN 1 WHEN 'normal' THEN 2 WHEN 'high' THEN 3 WHEN 'urgent' THEN 4 ELSE 2 END ASC",
            default => "CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'normal' THEN 3 WHEN 'low' THEN 4 ELSE 3 END ASC"
        };
        
        return $query->orderByRaw($order);
    }

    // =======================
    // ESSENTIAL METHODS - UNTUK WIDGET & ADMIN
    // =======================

    public function getVisitorName(): string
    {
        if ($this->user_id && $this->user) {
            return $this->user->name;
        }
        return $this->visitor_info['name'] ?? 'Guest';
    }

    public function getVisitorEmail(): ?string
    {
        if ($this->user_id && $this->user) {
            return $this->user->email;
        }
        return $this->visitor_info['email'] ?? null;
    }

    // =======================
    // ADMIN DASHBOARD METHODS
    // =======================

    public function getWaitingTimeInMinutes(): int
    {
        if ($this->status !== 'waiting') {
            return 0;
        }
        return $this->started_at->diffInMinutes(now());
    }

    public function getDurationInMinutes(): ?int
    {
        if (!$this->started_at || !$this->ended_at) {
            return null;
        }
        return $this->started_at->diffInMinutes($this->ended_at);
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'waiting' => 'bg-yellow-100 text-yellow-800',
            'active' => 'bg-green-100 text-green-800',
            'closed' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getPriorityBadgeClass(): string
    {
        return match($this->priority) {
            'low' => 'bg-blue-100 text-blue-800',
            'normal' => 'bg-gray-100 text-gray-800',
            'high' => 'bg-orange-100 text-orange-800',
            'urgent' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function canBeAssigned(): bool
    {
        return in_array($this->status, ['waiting', 'active']) && !$this->assigned_operator_id;
    }

    public function canBeClosed(): bool
    {
        return in_array($this->status, ['waiting', 'active']);
    }

    public function isAssignedTo(int $operatorId): bool
    {
        return $this->assigned_operator_id === $operatorId;
    }

    public function hasUnreadMessages(): bool
    {
        return $this->messages()
            ->where('sender_type', 'visitor')
            ->where('is_read', false)
            ->exists();
    }

    public function getUnreadMessagesCount(): int
    {
        return $this->messages()
            ->where('sender_type', 'visitor')
            ->where('is_read', false)
            ->count();
    }

    public function getQueuePosition(): int
    {
        if ($this->status !== 'waiting') {
            return 0;
        }

        return static::where('status', 'waiting')
            ->where('started_at', '<', $this->started_at)
            ->count() + 1;
    }
}