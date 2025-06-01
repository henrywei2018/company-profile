<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class ChatSession extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'visitor_info',
        'status',
        'assigned_operator_id',
        'priority',
        'source',
        'started_at',
        'last_activity_at',
        'ended_at',
        'summary',
        'metadata',
    ];

    protected $casts = [
        'visitor_info' => 'array',
        'metadata' => 'array',
        'started_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->session_id)) {
                $model->session_id = Str::uuid();
            }
            if (empty($model->started_at)) {
                $model->started_at = now();
            }
            $model->last_activity_at = now();
        });

        // Broadcast when session is created
        static::created(function ($model) {
            try {
                broadcast(new \App\Events\ChatSessionStarted($model))->toOthers();
                \Log::info('ChatSession created broadcast sent', ['session_id' => $model->session_id]);
            } catch (\Exception $e) {
                \Log::error('Failed to broadcast ChatSession created', [
                    'session_id' => $model->session_id,
                    'error' => $e->getMessage()
                ]);
            }
        });

        // Broadcast when session status changes
        static::updated(function ($model) {
            try {
                if ($model->isDirty('status')) {
                    if ($model->status === 'closed') {
                        broadcast(new \App\Events\ChatSessionClosed($model))->toOthers();
                        \Log::info('ChatSession closed broadcast sent', ['session_id' => $model->session_id]);
                    } else {
                        broadcast(new \App\Events\ChatSessionUpdated($model))->toOthers();
                        \Log::info('ChatSession updated broadcast sent', [
                            'session_id' => $model->session_id,
                            'status' => $model->status
                        ]);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Failed to broadcast ChatSession updated', [
                    'session_id' => $model->session_id,
                    'error' => $e->getMessage()
                ]);
            }
        });
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_operator_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(ChatMessage::class)->latestOfMany();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting');
    }

    public function scopeByPriority($query, $priority = 'desc')
    {
        return $query->orderByRaw("CASE priority " .
            "WHEN 'urgent' THEN 4 " .
            "WHEN 'high' THEN 3 " .
            "WHEN 'normal' THEN 2 " .
            "WHEN 'low' THEN 1 END " . $priority);
    }

    // Helper methods
    public function getVisitorName(): string
    {
        if ($this->user) {
            return $this->user->name;
        }
        
        return $this->visitor_info['name'] ?? 'Anonymous Visitor';
    }

    public function getVisitorEmail(): ?string
    {
        if ($this->user) {
            return $this->user->email;
        }
        
        return $this->visitor_info['email'] ?? null;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function close(): void
    {
        $this->update([
            'status' => 'closed',
            'ended_at' => now(),
        ]);
    }

    public function updateActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }

    public function assignOperator(User $operator): void
    {
        $this->update([
            'assigned_operator_id' => $operator->id,
            'status' => 'active',
        ]);
    }

    public function getDuration(): ?int
    {
        if (!$this->ended_at) {
            return null;
        }
        
        return $this->started_at->diffInMinutes($this->ended_at);
    }

    // WebSocket channel name
    public function getChannelName(): string
    {
        return "chat-session.{$this->session_id}";
    }

    // Get admin channel name for this session
    public function getAdminChannelName(): string
    {
        return "admin-chat-session.{$this->session_id}";
    }

    // Broadcast to all relevant channels
    public function broadcastToAllChannels($event, $data = [])
    {
        try {
            $channels = [
                $this->getChannelName(), // Client channel
                $this->getAdminChannelName(), // Admin specific channel
                'admin-chat-notifications', // Global admin notifications
            ];

            foreach ($channels as $channel) {
                broadcast($event)->on($channel);
            }

            \Log::info('Broadcast sent to all channels', [
                'session_id' => $this->session_id,
                'event' => get_class($event),
                'channels' => $channels
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to broadcast to all channels', [
                'session_id' => $this->session_id,
                'error' => $e->getMessage()
            ]);
        }
    }
}