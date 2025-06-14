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
        'file_path',
        'file_type',
        'file_size',
        'is_read',
        'read_at',
        'template_id'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'file_size' => 'integer'
    ];

    // Relationships
    public function chatSession(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ChatTemplate::class, 'template_id');
    }

    // Scopes
    public function scopeFromClient($query)
    {
        return $query->where('sender_type', 'client');
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

    // Accessors
    public function getSenderName(): string
    {
        if ($this->sender_type === 'system') {
            return 'System';
        }

        if ($this->sender) {
            return $this->sender->name;
        }

        return match($this->sender_type) {
            'client' => $this->chatSession->getVisitorName(),
            'operator' => 'Operator',
            default => 'Unknown'
        };
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

    public function getFormattedMessage(): string
    {
        if ($this->message_type === 'file') {
            return $this->formatFileMessage();
        }

        return nl2br(e($this->message));
    }

    private function formatFileMessage(): string
    {
        $fileName = basename($this->message);
        $fileUrl = asset('storage/' . $this->file_path);
        
        return "<a href=\"{$fileUrl}\" target=\"_blank\" class=\"file-link\">
                    <i class=\"bi bi-file-earmark\"></i> {$fileName}
                </a>";
    }

    public function getFileSizeFormatted(): string
    {
        if (!$this->file_size) {
            return 'Unknown size';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    // Helper methods
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }
    }

    public function isFromClient(): bool
    {
        return $this->sender_type === 'client';
    }

    public function isFromOperator(): bool
    {
        return $this->sender_type === 'operator';
    }

    public function isSystemMessage(): bool
    {
        return $this->sender_type === 'system';
    }

    public function isFileMessage(): bool
    {
        return $this->message_type === 'file';
    }
}