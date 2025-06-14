<?php

// File: app/Models/ChatSession.php - Fixed Version dengan Safe Broadcasting

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

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
        'close_reason',
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

        // PERBAIKAN: Safe broadcast when session is created
        static::created(function ($model) {
            try {
                if (class_exists('\App\Events\ChatSessionStarted')) {
                    broadcast(new \App\Events\ChatSessionStarted($model))->toOthers();
                    Log::info('ChatSession created broadcast sent', ['session_id' => $model->session_id]);
                } else {
                    Log::info('ChatSession created (no broadcast)', [
                        'session_id' => $model->session_id,
                        'user_id' => $model->user_id,
                        'status' => $model->status
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to broadcast ChatSession created', [
                    'session_id' => $model->session_id,
                    'error' => $e->getMessage()
                ]);
            }
        });

        // PERBAIKAN: Safe broadcast when session status changes
        static::updated(function ($model) {
            try {
                if ($model->isDirty('status')) {
                    if ($model->status === 'closed') {
                        if (class_exists('\App\Events\ChatSessionClosed')) {
                            broadcast(new \App\Events\ChatSessionClosed($model))->toOthers();
                            Log::info('ChatSession closed broadcast sent', ['session_id' => $model->session_id]);
                        } else {
                            Log::info('ChatSession closed (no broadcast)', ['session_id' => $model->session_id]);
                        }
                    } else {
                        if (class_exists('\App\Events\ChatSessionUpdated')) {
                            broadcast(new \App\Events\ChatSessionUpdated($model))->toOthers();
                            Log::info('ChatSession updated broadcast sent', [
                                'session_id' => $model->session_id,
                                'status' => $model->status
                            ]);
                        } else {
                            Log::info('ChatSession updated (no broadcast)', [
                                'session_id' => $model->session_id,
                                'status' => $model->status
                            ]);
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('Failed to broadcast ChatSession updated', [
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

    public function close(string $reason = null): void
    {
        $this->update([
            'status' => 'closed',
            'ended_at' => now(),
            'close_reason' => $reason,
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

    // PERBAIKAN: Safe broadcast to all relevant channels
    public function broadcastToAllChannels($event, $data = []): bool
    {
        try {
            // Check if we can broadcast
            if (!$event || !method_exists($event, 'broadcastOn')) {
                Log::warning('Invalid event for broadcasting', [
                    'session_id' => $this->session_id,
                    'event_class' => get_class($event)
                ]);
                return false;
            }

            broadcast($event)->toOthers();
            
            Log::info('Broadcast sent for session', [
                'session_id' => $this->session_id,
                'event' => get_class($event)
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to broadcast session event', [
                'session_id' => $this->session_id,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    // TAMBAHAN: Alternative notification method
    public function notifyStatusChange(): void
    {
        try {
            Log::info('Chat session status changed', [
                'session_id' => $this->session_id,
                'status' => $this->status,
                'user_id' => $this->user_id,
                'operator_id' => $this->assigned_operator_id,
                'timestamp' => now()->toISOString()
            ]);

            // Add any alternative notification logic here
            // e.g., database notifications, email notifications, etc.
            
        } catch (\Exception $e) {
            Log::error('Failed to notify status change', [
                'session_id' => $this->session_id,
                'error' => $e->getMessage()
            ]);
        }
    }
}