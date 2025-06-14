<?php

// File: app/Models/ChatSession.php - Fixed Version dengan Safe Broadcasting

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'visitor_name',
        'visitor_email',
        'visitor_phone',
        'status',
        'priority',
        'assigned_operator_id',
        'started_at',
        'assigned_at',
        'closed_at',
        'closed_by',
        'close_reason',
        'rating',
        'feedback',
        'notes',
        'user_agent',
        'ip_address',
        'referrer_url',
        'current_url',
        'transferred_at',
        'transfer_reason'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'assigned_at' => 'datetime',
        'closed_at' => 'datetime',
        'transferred_at' => 'datetime',
        'rating' => 'integer'
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedOperator(): BelongsTo
    {
        return $this->belongsTo(ChatOperator::class, 'assigned_operator_id', 'user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    // Scopes
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, string $direction = 'asc')
    {
        $priorities = ['low' => 1, 'normal' => 2, 'high' => 3, 'urgent' => 4];
        
        return $query->orderByRaw(
            "CASE priority " .
            "WHEN 'urgent' THEN 4 " .
            "WHEN 'high' THEN 3 " .
            "WHEN 'normal' THEN 2 " .
            "WHEN 'low' THEN 1 " .
            "ELSE 2 END " . $direction
        );
    }

    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAssignedTo($query, int $operatorId)
    {
        return $query->where('assigned_operator_id', $operatorId);
    }

    // Accessors & Mutators
    public function getVisitorName(): string
    {
        return $this->visitor_name ?: ($this->user ? $this->user->name : 'Anonymous');
    }

    public function getVisitorEmail(): ?string
    {
        return $this->visitor_email ?: ($this->user ? $this->user->email : null);
    }

    public function getDurationInMinutes(): ?int
    {
        if (!$this->started_at || !$this->closed_at) {
            return null;
        }

        return $this->started_at->diffInMinutes($this->closed_at);
    }

    public function getWaitingTimeInMinutes(): int
    {
        if ($this->status !== 'waiting') {
            return 0;
        }

        return $this->created_at->diffInMinutes(now());
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'waiting' => 'bg-warning',
            'active' => 'bg-success',
            'closed' => 'bg-secondary',
            default => 'bg-light'
        };
    }

    public function getPriorityBadgeClass(): string
    {
        return match($this->priority) {
            'low' => 'bg-info',
            'normal' => 'bg-secondary',
            'high' => 'bg-warning',
            'urgent' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    // Helper methods
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
            ->where('sender_type', 'client')
            ->where('is_read', false)
            ->exists();
    }

    public function getUnreadMessagesCount(): int
    {
        return $this->messages()
            ->where('sender_type', 'client')
            ->where('is_read', false)
            ->count();
    }
}