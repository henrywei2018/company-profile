<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_session_id',
        'sender_type',
        'sender_id',
        'message',
        'message_type',
        'metadata', // JSON field
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    // =======================
    // RELATIONSHIPS
    // =======================

    public function chatSession(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // =======================
    // SCOPES - UNTUK ADMIN DASHBOARD
    // =======================

    public function scopeFromVisitor($query)
    {
        return $query->where('sender_type', 'visitor');
    }

    public function scopeFromOperator($query)
    {
        return $query->where('sender_type', 'operator');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('message_type', $type);
    }

    // =======================
    // ESSENTIAL METHODS
    // =======================

    public function getSenderName(): string
    {
        switch ($this->sender_type) {
            case 'visitor':
                if ($this->sender_id && $this->sender) {
                    return $this->sender->name;
                }
                return $this->chatSession->getVisitorName();
                
            case 'operator':
                if ($this->sender_id && $this->sender) {
                    return $this->sender->name;
                }
                return 'Support Team';
                
            case 'system':
            case 'bot':
                return 'System';
                
            default:
                return 'Unknown';
        }
    }

    public function getSenderAvatar(): string
    {
        if ($this->sender_type === 'system') {
            return asset('images/system-avatar.png');
        }

        if ($this->sender && $this->sender->avatar_url) {
            return $this->sender->avatar_url;
        }

        return asset('images/default-avatar.png');
    }

    public function getSenderTypeClass(): string
    {
        return match($this->sender_type) {
            'visitor' => 'message-visitor',
            'operator' => 'message-operator',
            'system' => 'message-system',
            'bot' => 'message-bot',
            default => 'message-unknown'
        };
    }

    public function getFormattedTime(): string
    {
        return $this->created_at->format('H:i');
    }

    public function getTimestamp(): int
    {
        return $this->created_at->timestamp;
    }

    // =======================
    // UNTUK API RESPONSE
    // =======================

    public function toApiArray(): array
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'sender_type' => $this->sender_type,
            'sender_id' => $this->sender_id,
            'sender_name' => $this->getSenderName(),
            'sender_avatar' => $this->getSenderAvatar(),
            'message_type' => $this->message_type,
            'metadata' => $this->metadata,
            'is_read' => $this->is_read,
            'created_at' => $this->created_at,
            'formatted_time' => $this->getFormattedTime(),
            'timestamp' => $this->getTimestamp()
        ];
    }
}