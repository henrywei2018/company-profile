<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Events\ChatMessageSent;

class ChatMessage extends Model
{
    protected $fillable = [
        'chat_session_id',
        'sender_type',
        'sender_id',
        'message',
        'message_type',
        'metadata',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        // Broadcast when message is created
        static::created(function ($model) {
            $model->load('chatSession', 'sender');
            
            // Update session activity
            $model->chatSession->updateActivity();
            
            // Broadcast message
            broadcast(new ChatMessageSent($model, $model->chatSession))->toOthers();
        });
    }

    // Relationships
    public function chatSession(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeFromVisitor($query)
    {
        return $query->where('sender_type', 'visitor');
    }

    public function scopeFromOperator($query)
    {
        return $query->where('sender_type', 'operator');
    }

    public function scopeFromBot($query)
    {
        return $query->where('sender_type', 'bot');
    }

    // Helper methods
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    public function isFromVisitor(): bool
    {
        return $this->sender_type === 'visitor';
    }

    public function isFromOperator(): bool
    {
        return $this->sender_type === 'operator';
    }

    public function isFromBot(): bool
    {
        return $this->sender_type === 'bot';
    }

    public function getSenderName(): string
    {
        if ($this->sender) {
            return $this->sender->name;
        }

        return match($this->sender_type) {
            'bot' => 'Assistant',
            'system' => 'System',
            'visitor' => $this->chatSession->getVisitorName(),
            default => 'Unknown'
        };
    }

    // Format for WebSocket broadcast
    public function toWebSocketArray(): array
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'sender_type' => $this->sender_type,
            'sender_name' => $this->getSenderName(),
            'message_type' => $this->message_type,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at->toISOString(),
            'formatted_time' => $this->created_at->format('H:i'),
            'is_from_visitor' => $this->isFromVisitor(),
            'is_from_operator' => $this->isFromOperator(),
            'is_from_bot' => $this->isFromBot(),
        ];
    }
}